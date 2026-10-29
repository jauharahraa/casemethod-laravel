<?php

namespace App\Services;

/**
 * Penulis file .xlsx minimal (satu sheet) tanpa paket tambahan.
 * Hanya butuh ekstensi PHP "zip" (ZipArchive), yang aktif secara bawaan di XAMPP.
 * Teks ditulis sebagai string biasa (bukan rumus), sehingga isi sel yang diawali "=" tidak dieksekusi Excel.
 */
class XlsxWriter
{
    /** nama gaya => indeks <xf> pada styles.xml */
    private const STYLES = [
        'default' => 0, 'title' => 1, 'meta' => 2, 'header' => 3, 'text' => 4, 'center' => 5,
        'decimal' => 6, 'int' => 7, 'muted' => 8, 'sum_label' => 9, 'sum_decimal' => 10,
        'sum_int' => 11, 'section' => 12, 'sum_text' => 13,
    ];

    private array $cells = [];    // [baris][kolom] => [nilai, gaya]
    private array $heights = [];  // [baris] => tinggi
    private array $widths = [];   // [kolom] => lebar
    private array $merges = [];
    private ?array $freeze = null; // [jumlahBaris, jumlahKolom]

    public function __construct(private string $sheetName = 'Sheet1') {}

    public static function available(): bool
    {
        return class_exists(\ZipArchive::class);
    }

    /** Baris dan kolom berbasis 1. */
    public function set(int $row, int $col, mixed $value, string $style = 'default'): void
    {
        $this->cells[$row][$col] = [$value, self::STYLES[$style] ?? 0];
    }

    public function merge(int $r1, int $c1, int $r2, int $c2): void
    {
        $this->merges[] = self::ref($r1, $c1).':'.self::ref($r2, $c2);
    }

    public function width(int $col, float $w): void { $this->widths[$col] = $w; }
    public function rowHeight(int $row, float $h): void { $this->heights[$row] = $h; }
    public function freeze(int $rows, int $cols): void { $this->freeze = [$rows, $cols]; }

    public static function colName(int $n): string
    {
        $s = '';
        while ($n > 0) {
            $m = ($n - 1) % 26;
            $s = chr(65 + $m).$s;
            $n = intdiv($n - 1, 26);
        }
        return $s;
    }

    private static function ref(int $row, int $col): string
    {
        return self::colName($col).$row;
    }

    private static function esc(string $s): string
    {
        // buang karakter kontrol yang tidak valid di XML 1.0
        $s = preg_replace('/[^\x{9}\x{A}\x{D}\x{20}-\x{D7FF}\x{E000}-\x{FFFD}\x{10000}-\x{10FFFF}]/u', '', $s) ?? '';
        return htmlspecialchars($s, ENT_XML1 | ENT_QUOTES, 'UTF-8');
    }

    private function sheetXml(): string
    {
        $maxRow = $this->cells ? max(array_keys($this->cells)) : 1;
        $maxCol = 1;
        foreach ($this->cells as $cols) { $maxCol = max($maxCol, max(array_keys($cols))); }

        $x = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
            .'<worksheet xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main">'
            .'<sheetPr><pageSetUpPr fitToPage="1"/></sheetPr>'
            .'<dimension ref="A1:'.self::ref($maxRow, $maxCol).'"/>'
            .'<sheetViews><sheetView workbookViewId="0" showGridLines="0" tabSelected="1">';
        if ($this->freeze) {
            [$fr, $fc] = $this->freeze;
            $tl = self::ref($fr + 1, $fc + 1);
            $x .= '<pane'.($fc ? ' xSplit="'.$fc.'"' : '').($fr ? ' ySplit="'.$fr.'"' : '')
                .' topLeftCell="'.$tl.'" activePane="'.($fr && $fc ? 'bottomRight' : ($fr ? 'bottomLeft' : 'topRight')).'" state="frozen"/>';
        }
        $x .= '</sheetView></sheetViews><sheetFormatPr defaultRowHeight="18" customHeight="1"/>';

        if ($this->widths) {
            ksort($this->widths);
            $x .= '<cols>';
            foreach ($this->widths as $c => $w) {
                $x .= '<col min="'.$c.'" max="'.$c.'" width="'.$w.'" customWidth="1"/>';
            }
            $x .= '</cols>';
        }

        $x .= '<sheetData>';
        ksort($this->cells);
        foreach ($this->cells as $r => $cols) {
            ksort($cols);
            $ht = isset($this->heights[$r]) ? ' ht="'.$this->heights[$r].'" customHeight="1"' : '';
            $x .= '<row r="'.$r.'"'.$ht.'>';
            foreach ($cols as $c => [$v, $s]) {
                $ref = self::ref($r, $c);
                if ($v === null || $v === '') {
                    $x .= '<c r="'.$ref.'" s="'.$s.'"/>';
                } elseif (is_int($v) || is_float($v)) {
                    $x .= '<c r="'.$ref.'" s="'.$s.'"><v>'.rtrim(rtrim(number_format((float) $v, 6, '.', ''), '0'), '.').'</v></c>';
                } else {
                    $x .= '<c r="'.$ref.'" s="'.$s.'" t="inlineStr"><is><t xml:space="preserve">'.self::esc((string) $v).'</t></is></c>';
                }
            }
            $x .= '</row>';
        }
        $x .= '</sheetData>';

        if ($this->merges) {
            $x .= '<mergeCells count="'.count($this->merges).'">';
            foreach ($this->merges as $m) { $x .= '<mergeCell ref="'.$m.'"/>'; }
            $x .= '</mergeCells>';
        }
        return $x.'<pageMargins left="0.5" right="0.5" top="0.75" bottom="0.75" header="0.3" footer="0.3"/>'
            .'<pageSetup paperSize="9" orientation="landscape" fitToWidth="1" fitToHeight="0"/></worksheet>';
    }

    private function stylesXml(): string
    {
        $b = '<border><left style="thin"><color rgb="FFD9C9CC"/></left><right style="thin"><color rgb="FFD9C9CC"/></right>'
            .'<top style="thin"><color rgb="FFD9C9CC"/></top><bottom style="thin"><color rgb="FFD9C9CC"/></bottom><diagonal/></border>';
        $xf = fn (int $font, int $fill, int $border, int $numFmt, string $align = '') =>
            '<xf numFmtId="'.$numFmt.'" fontId="'.$font.'" fillId="'.$fill.'" borderId="'.$border.'" xfId="0"'
            .($numFmt ? ' applyNumberFormat="1"' : '').($font ? ' applyFont="1"' : '').($fill ? ' applyFill="1"' : '')
            .($border ? ' applyBorder="1"' : '').($align ? ' applyAlignment="1">'.$align.'</xf>' : '/>');
        $left = '<alignment horizontal="left" vertical="center" wrapText="1"/>';
        $center = '<alignment horizontal="center" vertical="center" wrapText="1"/>';

        return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
            .'<styleSheet xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main">'
            .'<numFmts count="1"><numFmt numFmtId="164" formatCode="0.00"/></numFmts>'
            .'<fonts count="6">'
            .'<font><sz val="11"/><color rgb="FF27153D"/><name val="Calibri"/><family val="2"/></font>'
            .'<font><b/><sz val="11"/><color rgb="FF27153D"/><name val="Calibri"/><family val="2"/></font>'
            .'<font><b/><sz val="11"/><color rgb="FFFFFFFF"/><name val="Calibri"/><family val="2"/></font>'
            .'<font><b/><sz val="15"/><color rgb="FF27153D"/><name val="Calibri"/><family val="2"/></font>'
            .'<font><i/><sz val="11"/><color rgb="FF6B7280"/><name val="Calibri"/><family val="2"/></font>'
            .'<font><b/><sz val="12"/><color rgb="FF3E828E"/><name val="Calibri"/><family val="2"/></font>'
            .'</fonts>'
            .'<fills count="4"><fill><patternFill patternType="none"/></fill><fill><patternFill patternType="gray125"/></fill>'
            .'<fill><patternFill patternType="solid"><fgColor rgb="FF27153D"/><bgColor indexed="64"/></patternFill></fill>'
            .'<fill><patternFill patternType="solid"><fgColor rgb="FFE4F1EA"/><bgColor indexed="64"/></patternFill></fill></fills>'
            .'<borders count="2"><border><left/><right/><top/><bottom/><diagonal/></border>'.$b.'</borders>'
            .'<cellStyleXfs count="1"><xf numFmtId="0" fontId="0" fillId="0" borderId="0"/></cellStyleXfs>'
            .'<cellXfs count="14">'
            .$xf(0, 0, 0, 0)                 // 0 default
            .$xf(3, 0, 0, 0)                 // 1 title
            .$xf(4, 0, 0, 0)                 // 2 meta
            .$xf(2, 2, 1, 0, $center)        // 3 header
            .$xf(0, 0, 1, 0, $left)          // 4 text
            .$xf(0, 0, 1, 0, $center)        // 5 center
            .$xf(0, 0, 1, 164, $center)      // 6 decimal
            .$xf(0, 0, 1, 1, $center)        // 7 integer
            .$xf(4, 0, 1, 0, $center)        // 8 muted ("Belum dinilai")
            .$xf(1, 3, 1, 0, $left)          // 9 label ringkasan
            .$xf(1, 3, 1, 164, $center)      // 10 ringkasan desimal
            .$xf(1, 3, 1, 1, $center)        // 11 ringkasan bilangan bulat
            .$xf(5, 0, 0, 0)                 // 12 judul bagian
            .$xf(1, 3, 1, 0, $center)        // 13 ringkasan teks
            .'</cellXfs>'
            .'<cellStyles count="1"><cellStyle name="Normal" xfId="0" builtinId="0"/></cellStyles>'
            .'</styleSheet>';
    }

    /** Menulis file .xlsx ke $path. */
    public function save(string $path): void
    {
        if (! self::available()) {
            throw new \RuntimeException('Ekstensi PHP "zip" belum aktif.');
        }
        $name = self::esc(mb_substr(preg_replace('/[\[\]\*\?\/\\\\:]/u', ' ', $this->sheetName), 0, 31));

        $zip = new \ZipArchive();
        if ($zip->open($path, \ZipArchive::CREATE | \ZipArchive::OVERWRITE) !== true) {
            throw new \RuntimeException('Gagal membuat berkas Excel sementara.');
        }
        $zip->addFromString('[Content_Types].xml', '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
            .'<Types xmlns="http://schemas.openxmlformats.org/package/2006/content-types">'
            .'<Default Extension="rels" ContentType="application/vnd.openxmlformats-package.relationships+xml"/>'
            .'<Default Extension="xml" ContentType="application/xml"/>'
            .'<Override PartName="/xl/workbook.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet.main+xml"/>'
            .'<Override PartName="/xl/worksheets/sheet1.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.worksheet+xml"/>'
            .'<Override PartName="/xl/styles.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.styles+xml"/>'
            .'</Types>');
        $zip->addFromString('_rels/.rels', '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
            .'<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">'
            .'<Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/officeDocument" Target="xl/workbook.xml"/>'
            .'</Relationships>');
        $zip->addFromString('xl/workbook.xml', '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
            .'<workbook xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main" xmlns:r="http://schemas.openxmlformats.org/officeDocument/2006/relationships">'
            .'<bookViews><workbookView/></bookViews><sheets><sheet name="'.$name.'" sheetId="1" r:id="rId1"/></sheets></workbook>');
        $zip->addFromString('xl/_rels/workbook.xml.rels', '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
            .'<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">'
            .'<Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/worksheet" Target="worksheets/sheet1.xml"/>'
            .'<Relationship Id="rId2" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/styles" Target="styles.xml"/>'
            .'</Relationships>');
        $zip->addFromString('xl/styles.xml', $this->stylesXml());
        $zip->addFromString('xl/worksheets/sheet1.xml', $this->sheetXml());
        $zip->close();
    }
}