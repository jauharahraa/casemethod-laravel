<?php

namespace App\Services;

use App\Models\CaseStudy;
use App\Models\Classroom;
use App\Models\Grade;

/**
 * Menyusun rekap NILAI GURU per kelas untuk diekspor.
 * Sumber nilai: tabel grades kolom final_score (nilai akhir yang diberikan guru).
 * Skor/feedback AI (tabel ai_feedbacks) sengaja TIDAK dibaca di sini.
 */
class GradeExportService
{
    public const NOT_GRADED = 'Belum dinilai';

    /** Mengambil data aktual dari database untuk satu kelas milik guru. */
    public function forClassroom(Classroom $classroom, bool $onlyPublished = false): array
    {
        $students = $classroom->students()->get()
            ->map(fn ($s) => ['id' => $s->id, 'name' => $s->full_name, 'number' => $s->participant_number])
            ->all();

        // Kasus kelas ini yang sudah dipublikasikan, ditambah kasus yang sudah punya nilai
        // (mis. kasus yang kemudian ditarik kembali ke draft). Kasus draft murni tidak dihitung.
        $cases = CaseStudy::where('classroom_id', $classroom->id)
            ->where('user_id', $classroom->user_id)
            ->where(fn ($q) => $q->where('status', 'published')->orWhereHas('submissions.grade'))
            ->orderBy('created_at')->orderBy('id')
            ->get(['id', 'title'])
            ->map(fn ($c) => ['id' => $c->id, 'title' => $c->title])
            ->all();

        $grades = [];
        $rows = Grade::query()
            ->join('submissions', 'submissions.id', '=', 'grades.submission_id')
            ->whereIn('submissions.case_id', array_column($cases, 'id'))
            ->when($onlyPublished, fn ($q) => $q->where('grades.published', true))
            ->get(['submissions.student_id', 'submissions.case_id', 'grades.final_score']);
        foreach ($rows as $g) {
            $grades[$g->student_id][$g->case_id] = (float) $g->final_score;
        }

        return self::compute($students, $cases, $grades);
    }

    /**
     * Perhitungan murni (tanpa database).
     * @param array $students [['id','name','number'], ...] sudah terurut
     * @param array $cases    [['id','title'], ...] sudah terurut
     * @param array $grades   [idSiswa][idKasus] => nilai guru (hanya yang SUDAH dinilai)
     */
    public static function compute(array $students, array $cases, array $grades): array
    {
        $totalCases = count($cases);
        $rows = [];
        $studentAverages = [];
        $perCaseValues = array_fill_keys(array_column($cases, 'id'), []);

        foreach ($students as $i => $s) {
            $scores = [];
            $given = [];
            foreach ($cases as $c) {
                $v = $grades[$s['id']][$c['id']] ?? null;
                $scores[$c['id']] = $v;
                if ($v !== null) {
                    $given[] = $v;
                    $perCaseValues[$c['id']][] = $v;
                }
            }
            // Rata-rata HANYA dari kasus yang sudah dinilai; kasus belum dinilai tidak dihitung 0.
            $avg = $given ? round(array_sum($given) / count($given), 2) : null;
            if ($avg !== null) $studentAverages[] = $avg;

            $rows[] = [
                'no' => $i + 1, 'name' => $s['name'], 'number' => $s['number'],
                'scores' => $scores, 'average' => $avg,
                'graded' => count($given), 'ungraded' => $totalCases - count($given),
            ];
        }

        $stat = fn (array $v) => $v ? [
            'average' => round(array_sum($v) / count($v), 2), 'highest' => max($v), 'lowest' => min($v), 'count' => count($v),
        ] : ['average' => null, 'highest' => null, 'lowest' => null, 'count' => 0];

        return [
            'cases' => $cases,
            'rows' => $rows,
            'summary' => [
                'students_total' => count($students),
                // rata-rata kelas = rata-rata dari rata-rata nilai tiap siswa yang sudah punya nilai
                'class' => $stat($studentAverages),
                'per_case' => array_map($stat, $perCaseValues),
            ],
        ];
    }

    /**
     * Menulis berkas .xlsx.
     * @param array $meta ['subject','classroom','teacher','date','only_published']
     */
    public function buildWorkbook(array $result, array $meta, string $path): void
    {
        $cases = $result['cases'];
        $n = count($cases);
        $firstCase = 4;
        $colAvg = $firstCase + $n;
        $colGraded = $colAvg + 1;
        $colUngraded = $colAvg + 2;
        $last = $colUngraded;
        $headerRow = 7;

        $w = new XlsxWriter('Rekap Nilai');
        $w->set(1, 1, 'Rekap Nilai Case Method', 'title');
        $w->set(2, 1, 'Materi: '.$meta['subject'], 'meta');
        $w->set(3, 1, 'Kelas: '.$meta['classroom'], 'meta');
        $w->set(4, 1, 'Diekspor: '.$meta['date'].' · Guru: '.$meta['teacher'], 'meta');
        $w->set(5, 1, 'Nilai yang dipakai adalah nilai akhir dari guru. Kasus yang belum dinilai tidak dihitung sebagai 0. '
            .($meta['only_published'] ? 'Hanya nilai yang sudah dipublikasikan.' : 'Termasuk nilai yang belum dipublikasikan ke siswa.'), 'meta');
        foreach ([1, 2, 3, 4, 5] as $r) { $w->merge($r, 1, $r, $last); }
        $w->rowHeight(1, 26);

        // Header
        $w->set($headerRow, 1, 'No', 'header');
        $w->set($headerRow, 2, 'Nama Siswa', 'header');
        $w->set($headerRow, 3, 'ID / Nomor Peserta', 'header');
        $lines = 2;
        foreach ($cases as $i => $c) {
            $w->set($headerRow, $firstCase + $i, $c['title'], 'header');
            $lines = max($lines, (int) ceil(mb_strlen($c['title']) / 16));
        }
        $w->set($headerRow, $colAvg, 'Rata-rata Nilai Guru', 'header');
        $w->set($headerRow, $colGraded, 'Kasus Sudah Dinilai', 'header');
        $w->set($headerRow, $colUngraded, 'Kasus Belum Dinilai', 'header');
        $w->rowHeight($headerRow, min(150, max(34, $lines * 16 + 8)));

        // Kolom
        $w->width(1, 6);
        $w->width(2, 30);
        $w->width(3, 20);
        for ($c = $firstCase; $c < $colAvg; $c++) { $w->width($c, 18); }
        $w->width($colAvg, 17);
        $w->width($colGraded, 15);
        $w->width($colUngraded, 15);
        $w->freeze($headerRow, 3);

        // Isi
        $r = $headerRow;
        foreach ($result['rows'] as $row) {
            $r++;
            $w->set($r, 1, $row['no'], 'int');
            $w->set($r, 2, $row['name'], 'text');
            $w->set($r, 3, $row['number'], 'center');
            foreach ($cases as $i => $c) {
                $v = $row['scores'][$c['id']];
                $v === null ? $w->set($r, $firstCase + $i, self::NOT_GRADED, 'muted')
                            : $w->set($r, $firstCase + $i, $v, 'decimal');
            }
            $row['average'] === null ? $w->set($r, $colAvg, self::NOT_GRADED, 'muted')
                                     : $w->set($r, $colAvg, $row['average'], 'decimal');
            $w->set($r, $colGraded, $row['graded'], 'int');
            $w->set($r, $colUngraded, $row['ungraded'], 'int');
        }

        // Ringkasan kelas
        $sum = $result['summary'];
        $r += 2;
        $w->set($r, 1, 'Ringkasan Kelas', 'section');
        $w->merge($r, 1, $r, 3);

        $items = [
            ['Rata-rata kelas', 'average', 'sum_decimal'],
            ['Nilai tertinggi', 'highest', 'sum_decimal'],
            ['Nilai terendah', 'lowest', 'sum_decimal'],
            ['Jumlah siswa yang sudah memiliki nilai (dari '.$sum['students_total'].' siswa)', 'count', 'sum_int'],
        ];
        foreach ($items as [$label, $key, $style]) {
            $r++;
            $w->set($r, 1, $label, 'sum_label');
            $w->set($r, 2, null, 'sum_label');
            $w->set($r, 3, null, 'sum_label');
            $w->merge($r, 1, $r, 3);
            $w->rowHeight($r, 20);
            foreach ($cases as $i => $c) {
                $v = $sum['per_case'][$c['id']][$key];
                $v === null ? $w->set($r, $firstCase + $i, '-', 'sum_text') : $w->set($r, $firstCase + $i, $v, $style);
            }
            $v = $sum['class'][$key];
            $v === null ? $w->set($r, $colAvg, '-', 'sum_text') : $w->set($r, $colAvg, $v, $style);
            $w->set($r, $colGraded, null, 'sum_text');
            $w->set($r, $colUngraded, null, 'sum_text');
        }
        $r += 2;
        $w->set($r, 1, 'Kolom kasus: ringkasan per kasus. Kolom rata-rata: ringkasan kelas, dihitung dari rata-rata nilai tiap siswa yang sudah memiliki nilai.', 'meta');
        $w->merge($r, 1, $r, $last);

        $w->save($path);
    }
}