<?php

namespace Tests\Feature;

use App\Models\CaseStudy;
use App\Models\Classroom;
use App\Models\Student;
use App\Models\Subject;
use App\Models\Submission;
use App\Models\User;
use App\Services\RubricService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class CaseMethodTest extends TestCase
{
    use RefreshDatabase;

    private function setupData(string $username = 'guru1'): array
    {
        $guru = User::create(['name' => 'Guru', 'username' => $username, 'password' => 'password123']);
        $subject = Subject::create(['user_id' => $guru->id, 'name' => 'Ekonomi']);
        $class = Classroom::create(['user_id' => $guru->id, 'subject_id' => $subject->id, 'name' => 'XI A', 'code' => strtoupper(substr(md5($username), 0, 6))]);
        $student = Student::create(['user_id' => $guru->id, 'full_name' => 'Budi Santoso', 'participant_number' => 'N-'.$username]);
        $class->students()->attach($student->id);
        $case = CaseStudy::create(['user_id' => $guru->id, 'classroom_id' => $class->id, 'title' => 'Kasus A', 'description' => 'd', 'instructions' => 'i', 'objectives' => 'o', 'status' => 'published']);
        $case->rubricVersions()->create(['version' => 1, 'data' => RubricService::default()]);
        return compact('guru', 'class', 'student', 'case');
    }

    private function asStudent(array $d)
    {
        return $this->withSession(['student_id' => $d['student']->id, 'classroom_id' => $d['class']->id]);
    }

    private function answers(): array
    {
        $t = str_repeat('Jawaban yang cukup panjang untuk lolos validasi. ', 2);
        return ['identification' => $t, 'analysis' => $t, 'solution' => $t];
    }

    public function test_teacher_register_and_login(): void
    {
        $this->post('/guru/daftar', ['name' => 'Bu Guru', 'username' => 'buguru', 'password' => 'rahasia123', 'password_confirmation' => 'rahasia123'])->assertRedirect('/guru');
        $this->post('/guru/keluar');
        $this->post('/guru/masuk', ['username' => 'buguru', 'password' => 'rahasia123'])->assertRedirect('/guru');
        $this->assertDatabaseMissing('users', ['password' => 'rahasia123']);
    }

    public function test_student_login_requires_matching_data(): void
    {
        $d = $this->setupData();
        $ok = ['full_name' => 'budi santoso', 'class_name' => 'xi a', 'participant_number' => $d['student']->participant_number, 'class_code' => $d['class']->code];
        $this->post('/siswa/masuk', array_merge($ok, ['class_code' => 'SALAH1']))->assertSessionHasErrors('login');
        $this->post('/siswa/masuk', $ok)->assertRedirect('/siswa');
    }

    public function test_student_cannot_see_draft_or_other_class_case(): void
    {
        $d = $this->setupData();
        $d['case']->update(['status' => 'draft']);
        $this->asStudent($d)->get('/siswa/kasus/'.$d['case']->id)->assertNotFound();

        $d['case']->update(['status' => 'published']);
        $other = $this->setupData('guru2');
        $this->asStudent($d)->get('/siswa/kasus/'.$other['case']->id)->assertNotFound();
    }

    public function test_full_flow_with_single_revision_enforced_by_backend(): void
    {
        config(['services.gemini.key' => null]);
        $d = $this->setupData();
        $url = '/siswa/kasus/'.$d['case']->id;

        $this->asStudent($d)->post("$url/kirim", $this->answers())->assertRedirect();
        $this->assertSame('feedback', Submission::first()->status);
        $this->assertDatabaseHas('ai_feedbacks', ['mode' => 'simulasi', 'status' => 'ok']);

        $this->asStudent($d)->post("$url/kirim", $this->answers())->assertStatus(409); // tidak bisa kirim ulang

        $this->asStudent($d)->post("$url/revisi")->assertRedirect();
        $this->asStudent($d)->post("$url/revisi")->assertStatus(409); // revisi kedua ditolak
        $this->asStudent($d)->post("$url/kirim-revisi", $this->answers())->assertRedirect();

        $s = Submission::first();
        $this->assertSame('locked', $s->status);
        $this->assertSame(2, $s->versions()->count());
        $this->asStudent($d)->post("$url/revisi")->assertStatus(409);
        $this->asStudent($d)->post("$url/draft", $this->answers())->assertStatus(302); // dikunci: ditolak dengan error
        $this->assertSame(1, Submission::count());
    }

    private function aiJson(int $i, int $a, int $so, $final = 99): array
    {
        $asp = fn ($n) => ['score' => $n, 'reason' => "alasan $n", 'feedback' => "saran $n"];
        return ['candidates' => [['content' => ['parts' => [['text' => json_encode([
            'aspects' => ['identification' => $asp($i), 'analysis' => $asp($a), 'solution' => $asp($so)],
            'final_score' => $final, // sengaja salah: program harus mengabaikannya
            'conclusion' => 'kesimpulan uji', 'suggestions' => ['s1'], 'guiding_questions' => ['q1'],
        ])]]]]]];
    }

    public function test_ai_failure_can_be_retried_without_consuming_revision(): void
    {
        config(['services.gemini.key' => 'dummy']);
        $fail = true;
        Http::fake(fn () => $fail ? Http::response(['error' => ['message' => 'boom']], 500) : Http::response($this->aiJson(4, 2, 0)));
        $d = $this->setupData();
        $url = '/siswa/kasus/'.$d['case']->id;

        $this->asStudent($d)->post("$url/kirim", $this->answers());
        $this->assertDatabaseHas('ai_feedbacks', ['status' => 'failed']);
        $this->asStudent($d)->post("$url/revisi")->assertStatus(409); // belum ada feedback -> revisi tertahan

        $fail = false;
        $this->asStudent($d)->post("$url/coba-lagi");

        $this->assertSame(1, Submission::count());
        $this->assertSame(1, \App\Models\AiFeedback::count());
        $this->assertDatabaseHas('ai_feedbacks', ['status' => 'ok', 'mode' => 'gemini']);
        $this->asStudent($d)->post("$url/revisi")->assertRedirect();
    }

    public function test_ai_feedback_shows_three_aspects_and_program_computes_final_score(): void
    {
        config(['services.gemini.key' => 'dummy']);
        Http::fake(fn () => Http::response($this->aiJson(4, 2, 0, 99)));
        $d = $this->setupData();
        $url = '/siswa/kasus/'.$d['case']->id;

        $this->asStudent($d)->post("$url/kirim", $this->answers());
        $content = \App\Models\AiFeedback::first()->content;
        $this->assertEquals(42.5, $content['final_score']); // bukan 99 dari AI
        $this->assertSame(['identification', 'analysis', 'solution'], array_keys($content['aspects']));

        $this->asStudent($d)->get($url)
            ->assertSee('Identifikasi Masalah')->assertSee('Analisis Masalah')->assertSee('Solusi')
            ->assertSee('Skor 4/4')->assertSee('Skor 2/4')->assertSee('Skor 0/4')
            ->assertSee('alasan 4')->assertSee('saran 2')->assertSee('kesimpulan uji')->assertSee('42.5');
    }

    public function test_invalid_ai_scores_mark_feedback_failed(): void
    {
        config(['services.gemini.key' => 'dummy']);
        $bad = $this->aiJson(4, 2, 0);
        $payload = json_decode($bad['candidates'][0]['content']['parts'][0]['text'], true);
        unset($payload['aspects']['solution']['score']);
        $bad['candidates'][0]['content']['parts'][0]['text'] = json_encode($payload);
        Http::fake(fn () => Http::response($bad));
        $d = $this->setupData();

        $this->asStudent($d)->post('/siswa/kasus/'.$d['case']->id.'/kirim', $this->answers());
        $this->assertDatabaseHas('ai_feedbacks', ['status' => 'failed']);
        $this->assertSame('feedback', Submission::first()->status); // jawaban aman, bisa coba lagi
    }

    public function test_rubric_formula_boundaries_and_combinations(): void
    {
        $r = RubricService::default();
        $this->assertEquals(100, array_sum(RubricService::weights($r)));
        $f = fn ($i, $a, $s) => RubricService::finalFromAspectScores($r, ['identification' => $i, 'analysis' => $a, 'solution' => $s])['final'];
        $this->assertEquals(0, $f(0, 0, 0));
        $this->assertEquals(100, $f(4, 4, 4));
        $this->assertEquals(42.5, $f(4, 2, 0));
        $this->assertEquals(57.5, $f(2, 4, 1));
        $this->assertEquals(53.75, $f(1, 2, 3));
    }

    public function test_teacher_saves_simplified_rubric_and_legacy_rubric_opens_in_new_format(): void
    {
        $d = $this->setupData();
        $this->actingAs($d['guru'])->post('/guru/kasus', [
            'classroom_id' => $d['class']->id, 'title' => 'Baru', 'description' => 'd', 'instructions' => 'i', 'objectives' => 'o',
            'action' => 'draft', 'rubric' => RubricService::default()['aspects'],
        ])->assertSessionHasNoErrors();
        $new = CaseStudy::where('title', 'Baru')->first()->currentRubric->data;
        $this->assertTrue(RubricService::isSimplified($new));
        $this->assertSame(['identification', 'analysis', 'solution'], array_keys($new['aspects']));

        // Rubrik versi lama (banyak kriteria) pada kasus yang sudah ada
        $legacy = ['aspects' => collect(RubricService::ASPECTS)->map(fn ($l) => ['label' => $l, 'weight' => 33.34, 'criteria' => [['name' => 'x', 'description' => '', 'max' => 10], ['name' => 'y', 'description' => '', 'max' => 10]]])->all()];
        $d['case']->rubricVersions()->first()->update(['data' => $legacy]);
        $this->actingAs($d['guru'])->get('/guru/kasus/'.$d['case']->id.'/ubah')->assertOk()->assertSee('Ketepatan identifikasi masalah');
    }

    public function test_grade_calculation_and_publication_visibility(): void
    {
        config(['services.gemini.key' => null]);
        $d = $this->setupData();
        $url = '/siswa/kasus/'.$d['case']->id;
        $this->asStudent($d)->post("$url/kirim", $this->answers());
        $this->asStudent($d)->post("$url/selesai");
        $sub = Submission::first();

        // skor 4/2/0 -> (4/4 x 25) + (2/4 x 35) + (0/4 x 40) = 25 + 17.5 + 0 = 42.5
        $scores = ['identification' => [4], 'analysis' => [2], 'solution' => [0]];
        $this->actingAs($d['guru'])->post('/guru/penilaian/'.$sub->id, ['scores' => $scores, 'comment' => 'Bagus', 'publish' => '0'])->assertRedirect();
        $this->assertEquals(42.5, $sub->fresh()->grade->final_score);

        $this->actingAs($d['guru']); // guru & siswa berbeda sesi; cek tampilan siswa
        $this->asStudent($d)->get($url)->assertDontSee('Nilai dari guru');
        $this->actingAs($d['guru'])->post('/guru/penilaian/'.$sub->id.'/publikasi');
        $this->asStudent($d)->get($url)->assertSee('Nilai dari guru')->assertSee('42.5');
    }

    public function test_teacher_cannot_access_other_teachers_data(): void
    {
        $a = $this->setupData('guruA');
        $b = $this->setupData('guruB');
        $this->actingAs($a['guru'])->get('/guru/kasus/'.$b['case']->id.'/ubah')->assertNotFound();
        $this->actingAs($a['guru'])->get('/guru/kelas/'.$b['class']->id)->assertNotFound();
    }

    public function test_rubric_weights_must_total_100(): void
    {
        $d = $this->setupData();
        $rubric = RubricService::default()['aspects'];
        $rubric['solution']['weight'] = 50;
        $this->actingAs($d['guru'])->post('/guru/kasus', [
            'classroom_id' => $d['class']->id, 'title' => 'X', 'description' => 'd', 'instructions' => 'i', 'objectives' => 'o',
            'action' => 'draft', 'rubric' => $rubric,
        ])->assertSessionHasErrors('rubric');
    }
}