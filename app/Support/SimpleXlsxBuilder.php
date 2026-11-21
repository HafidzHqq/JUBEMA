<?php

namespace App\Support;

use RuntimeException;
use ZipArchive;

/**
 * Generator file Excel (.xlsx) murni berbasis ZipArchive (OOXML)
 * tanpa ketergantungan paket tambahan, lengkap dengan beberapa sheet,
 * judul laporan, lebar kolom rapi, header berwarna, dan pembungkus teks.
 */
class SimpleXlsxBuilder
{
    /**
     * @var list<array{
     *     name: string,
     *     title: string,
     *     subtitle: string,
     *     headers: list<string>,
     *     widths: list<int|float>,
     *     rows: list<list<string|int|float|null>>
     * }>
     */
    private array $sheets = [];

    /**
     * @param  list<string>  $headers
     * @param  list<int|float>  $widths
     * @param  list<list<string|int|float|null>>  $rows
     */
    public function addSheet(
        string $name,
        string $title,
        string $subtitle,
        array $headers,
        array $widths,
        array $rows
    ): self {
        // Nama sheet Excel maksimal 31 karakter dan tidak boleh memuat : \ / ? * [ ]
        $cleanName = preg_replace('/[:\\\\\/?*\[\]]/', '', trim($name)) ?: 'Sheet';
        $cleanName = mb_substr($cleanName, 0, 31);

        $this->sheets[] = [
            'name' => $cleanName,
            'title' => $title,
            'subtitle' => $subtitle,
            'headers' => $headers,
            'widths' => $widths,
            'rows' => $rows,
        ];

        return $this;
    }

    /**
     * Bangun file .xlsx sementara dan kembalikan path absolutnya.
     */
    public function buildToTempFile(): string
    {
        if (count($this->sheets) === 0) {
            throw new RuntimeException('Minimal harus ada satu sheet sebelum membangun file XLSX.');
        }

        $tmpPath = tempnam(sys_get_temp_dir(), 'jubema_xlsx_');
        if ($tmpPath === false) {
            throw new RuntimeException('Gagal membuat berkas sementara untuk ekspor Excel.');
        }

        $zip = new ZipArchive;
        if ($zip->open($tmpPath, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
            @unlink($tmpPath);
            throw new RuntimeException('Gagal membuka arsip ZIP untuk file XLSX.');
        }

        $zip->addFromString('[Content_Types].xml', $this->buildContentTypes());
        $zip->addFromString('_rels/.rels', $this->buildRootRels());
        $zip->addFromString('xl/workbook.xml', $this->buildWorkbook());
        $zip->addFromString('xl/_rels/workbook.xml.rels', $this->buildWorkbookRels());
        $zip->addFromString('xl/styles.xml', $this->buildStyles());

        foreach ($this->sheets as $idx => $sheet) {
            $sheetNumber = $idx + 1;
            $zip->addFromString("xl/worksheets/sheet{$sheetNumber}.xml", $this->buildSheetXml($sheet));
        }

        $zip->close();

        return $tmpPath;
    }

    private function buildContentTypes(): string
    {
        $overrides = '';
        foreach ($this->sheets as $idx => $_) {
            $n = $idx + 1;
            $overrides .= '<Override PartName="/xl/worksheets/sheet'.$n.'.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.worksheet+xml"/>';
        }

        return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
            .'<Types xmlns="http://schemas.openxmlformats.org/package/2006/content-types">'
            .'<Default Extension="rels" ContentType="application/vnd.openxmlformats-package.relationships+xml"/>'
            .'<Default Extension="xml" ContentType="application/xml"/>'
            .'<Override PartName="/xl/workbook.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet.main+xml"/>'
            .'<Override PartName="/xl/styles.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.styles+xml"/>'
            .$overrides
            .'</Types>';
    }

    private function buildRootRels(): string
    {
        return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
            .'<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">'
            .'<Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/officeDocument" Target="xl/workbook.xml"/>'
            .'</Relationships>';
    }

    private function buildWorkbook(): string
    {
        $sheetsXml = '';
        foreach ($this->sheets as $idx => $sheet) {
            $n = $idx + 1;
            $name = $this->esc($sheet['name']);
            $sheetsXml .= '<sheet name="'.$name.'" sheetId="'.$n.'" r:id="rId'.$n.'"/>';
        }

        return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
            .'<workbook xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main" '
            .'xmlns:r="http://schemas.openxmlformats.org/officeDocument/2006/relationships">'
            .'<sheets>'.$sheetsXml.'</sheets>'
            .'</workbook>';
    }

    private function buildWorkbookRels(): string
    {
        $rels = '';
        foreach ($this->sheets as $idx => $_) {
            $n = $idx + 1;
            $rels .= '<Relationship Id="rId'.$n.'" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/worksheet" Target="worksheets/sheet'.$n.'.xml"/>';
        }
        $styleRelId = count($this->sheets) + 1;
        $rels .= '<Relationship Id="rId'.$styleRelId.'" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/styles" Target="styles.xml"/>';

        return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
            .'<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">'
            .$rels
            .'</Relationships>';
    }

    private function buildStyles(): string
    {
        return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
            .'<styleSheet xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main">'
            .'<fonts count="5">'
            .'<font><sz val="10"/><color rgb="FF18283B"/><name val="Calibri"/></font>'
            .'<font><b/><sz val="13"/><color rgb="FF18283B"/><name val="Calibri"/></font>'
            .'<font><i/><sz val="9.5"/><color rgb="FF4A5868"/><name val="Calibri"/></font>'
            .'<font><b/><sz val="10"/><color rgb="FFFFFFFF"/><name val="Calibri"/></font>'
            .'<font><b/><sz val="10"/><color rgb="FF0F766E"/><name val="Calibri"/></font>'
            .'</fonts>'
            .'<fills count="5">'
            .'<fill><patternFill patternType="none"/></fill>'
            .'<fill><patternFill patternType="gray125"/></fill>'
            .'<fill><patternFill patternType="solid"><fgColor rgb="FF18283B"/><bgColor indexed="64"/></patternFill></fill>'
            .'<fill><patternFill patternType="solid"><fgColor rgb="FFF8F6F1"/><bgColor indexed="64"/></patternFill></fill>'
            .'<fill><patternFill patternType="solid"><fgColor rgb="FFE6F4F1"/><bgColor indexed="64"/></patternFill></fill>'
            .'</fills>'
            .'<borders count="2">'
            .'<border><left/><right/><top/><bottom/><diagonal/></border>'
            .'<border>'
            .'<left style="thin"><color rgb="FFD5CFC2"/></left>'
            .'<right style="thin"><color rgb="FFD5CFC2"/></right>'
            .'<top style="thin"><color rgb="FFD5CFC2"/></top>'
            .'<bottom style="thin"><color rgb="FFD5CFC2"/></bottom>'
            .'<diagonal/>'
            .'</border>'
            .'</borders>'
            .'<cellStyleXfs count="1"><xf numFmtId="0" fontId="0" fillId="0" borderId="0"/></cellStyleXfs>'
            .'<cellXfs count="6">'
            // 0: Normal cell (wrap + border)
            .'<xf numFmtId="0" fontId="0" fillId="0" borderId="1" applyFont="1" applyBorder="1" applyAlignment="1"><alignment vertical="top" wrapText="1"/></xf>'
            // 1: Title banner
            .'<xf numFmtId="0" fontId="1" fillId="0" borderId="0" applyFont="1" applyAlignment="1"><alignment vertical="center"/></xf>'
            // 2: Subtitle
            .'<xf numFmtId="0" fontId="2" fillId="0" borderId="0" applyFont="1" applyAlignment="1"><alignment vertical="center"/></xf>'
            // 3: Header cell
            .'<xf numFmtId="0" fontId="3" fillId="2" borderId="1" applyFont="1" applyFill="1" applyBorder="1" applyAlignment="1"><alignment vertical="center" wrapText="1"/></xf>'
            // 4: Zebra row cell
            .'<xf numFmtId="0" fontId="0" fillId="3" borderId="1" applyFont="1" applyFill="1" applyBorder="1" applyAlignment="1"><alignment vertical="top" wrapText="1"/></xf>'
            // 5: Positive status cell (Selesai / Sudah Dijadwalkan)
            .'<xf numFmtId="0" fontId="4" fillId="4" borderId="1" applyFont="1" applyFill="1" applyBorder="1" applyAlignment="1"><alignment horizontal="center" vertical="top" wrapText="1"/></xf>'
            .'</cellXfs>'
            .'</styleSheet>';
    }

    /**
     * @param  array{
     *     name: string,
     *     title: string,
     *     subtitle: string,
     *     headers: list<string>,
     *     widths: list<int|float>,
     *     rows: list<list<string|int|float|null>>
     * }  $sheet
     */
    private function buildSheetXml(array $sheet): string
    {
        $colCount = max(1, count($sheet['headers']));
        $lastCol = $this->colLetter($colCount);

        $colsXml = '<cols>';
        foreach ($sheet['widths'] as $idx => $width) {
            $colNum = $idx + 1;
            $colsXml .= '<col min="'.$colNum.'" max="'.$colNum.'" width="'.$width.'" customWidth="1"/>';
        }
        $colsXml .= '</cols>';

        $rowsXml = '';

        // Baris 1: Judul
        $rowsXml .= '<row r="1" ht="24" customHeight="1">'
            .$this->inlineCell('A1', $sheet['title'], 1)
            .'</row>';

        // Baris 2: Subjudul / tanggal cetak
        $rowsXml .= '<row r="2" ht="18" customHeight="1">'
            .$this->inlineCell('A2', $sheet['subtitle'], 2)
            .'</row>';

        // Baris 3: Spasi kosong
        $rowsXml .= '<row r="3" ht="8" customHeight="1"/>';

        // Baris 4: Header tabel
        $rowsXml .= '<row r="4" ht="26" customHeight="1">';
        foreach ($sheet['headers'] as $cIdx => $header) {
            $ref = $this->colLetter($cIdx + 1).'4';
            $rowsXml .= $this->inlineCell($ref, $header, 3);
        }
        $rowsXml .= '</row>';

        // Baris 5+: Data
        $rowNum = 5;
        if (count($sheet['rows']) === 0) {
            $rowsXml .= '<row r="5" ht="22" customHeight="1">'
                .$this->inlineCell('A5', 'Belum ada data pada bagian ini.', 2)
                .'</row>';
        } else {
            foreach ($sheet['rows'] as $rIdx => $row) {
                $isZebra = ($rIdx % 2) === 1;
                $defaultStyle = $isZebra ? 4 : 0;

                $rowsXml .= '<row r="'.$rowNum.'">';
                foreach ($sheet['headers'] as $cIdx => $_) {
                    $val = $row[$cIdx] ?? '';
                    $ref = $this->colLetter($cIdx + 1).$rowNum;
                    $strVal = is_scalar($val) ? (string) $val : '';
                    $style = in_array($strVal, ['Selesai', 'Sudah Dijadwalkan', 'Dibagikan ke BK'], true)
                        ? 5
                        : $defaultStyle;

                    if (is_int($val) || is_float($val)) {
                        $rowsXml .= '<c r="'.$ref.'" s="'.$style.'"><v>'.$val.'</v></c>';
                    } else {
                        $rowsXml .= $this->inlineCell($ref, $strVal, $style);
                    }
                }
                $rowsXml .= '</row>';
                $rowNum++;
            }
        }

        $mergeXml = '';
        if ($colCount > 1) {
            $mergeXml = '<mergeCells count="2">'
                .'<mergeCell ref="A1:'.$lastCol.'1"/>'
                .'<mergeCell ref="A2:'.$lastCol.'2"/>'
                .'</mergeCells>';
        }

        return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
            .'<worksheet xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main">'
            .'<sheetViews><sheetView workbookViewId="0">'
            .'<pane ySplit="4" topLeftCell="A5" activePane="bottomLeft" state="frozen"/>'
            .'</sheetView></sheetViews>'
            .'<sheetFormatPr defaultRowHeight="20"/>'
            .$colsXml
            .'<sheetData>'.$rowsXml.'</sheetData>'
            .$mergeXml
            .'</worksheet>';
    }

    private function inlineCell(string $ref, string $value, int $styleIndex): string
    {
        return '<c r="'.$ref.'" t="inlineStr" s="'.$styleIndex.'">'
            .'<is><t xml:space="preserve">'.$this->esc($value).'</t></is>'
            .'</c>';
    }

    private function colLetter(int $index): string
    {
        $letter = '';
        while ($index > 0) {
            $mod = ($index - 1) % 26;
            $letter = chr(65 + $mod).$letter;
            $index = intdiv($index - 1, 26);
        }

        return $letter;
    }

    private function esc(string $text): string
    {
        // Bersihkan karakter kontrol XML yang tidak valid
        $clean = preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F]/u', '', $text) ?? $text;

        return htmlspecialchars($clean, ENT_XML1 | ENT_QUOTES, 'UTF-8');
    }
}
