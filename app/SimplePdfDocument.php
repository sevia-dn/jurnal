<?php

namespace App;

class SimplePdfDocument
{
    /**
     * @param  array<int, string>  $lines
     */
    public static function make(string $title, array $lines): string
    {
        $pageLines = array_chunk($lines, 46);
        $objects = [];
        $objects[] = '<< /Type /Catalog /Pages 2 0 R >>';
        $objects[] = '';
        $objects[] = '<< /Type /Font /Subtype /Type1 /BaseFont /Courier >>';

        $pageObjectNumbers = [];
        foreach ($pageLines as $pageLine) {
            $content = self::pageContent($title, $pageLine);
            $contentObjectNumber = count($objects) + 1;
            $objects[] = '<< /Length '.strlen($content)." >>\nstream\n{$content}\nendstream";

            $pageObjectNumbers[] = count($objects) + 1;
            $objects[] = "<< /Type /Page /Parent 2 0 R /MediaBox [0 0 842 595] /Resources << /Font << /F1 3 0 R >> >> /Contents {$contentObjectNumber} 0 R >>";
        }

        $pageReferences = implode(' ', array_map(
            static fn (int $number): string => "{$number} 0 R",
            $pageObjectNumbers
        ));
        $objects[1] = '<< /Type /Pages /Kids ['.$pageReferences.'] /Count '.count($pageObjectNumbers).' >>';

        $pdf = "%PDF-1.4\n";
        $offsets = [0];
        foreach ($objects as $number => $object) {
            $offsets[] = strlen($pdf);
            $pdf .= ($number + 1)." 0 obj\n{$object}\nendobj\n";
        }

        $xrefOffset = strlen($pdf);
        $pdf .= 'xref'."\n0 ".(count($objects) + 1)."\n0000000000 65535 f \n";
        foreach (array_slice($offsets, 1) as $offset) {
            $pdf .= sprintf('%010d 00000 n ', $offset)."\n";
        }

        return $pdf.'trailer'."\n<< /Size ".(count($objects) + 1).' /Root 1 0 R >>'."\nstartxref\n{$xrefOffset}\n%%EOF";
    }

    /**
     * @param  array<int, string>  $lines
     */
    private static function pageContent(string $title, array $lines): string
    {
        $content = "BT\n/F1 14 Tf\n40 555 Td\n(".self::escape($title).") Tj\n";
        $content .= "/F1 9 Tf\n0 -22 Td\n";

        foreach ($lines as $line) {
            $content .= '('.self::escape($line).") Tj\n0 -11 Td\n";
        }

        return $content.'ET';
    }

    private static function escape(string $value): string
    {
        $value = iconv('UTF-8', 'Windows-1252//TRANSLIT//IGNORE', $value) ?: '';
        $value = str_replace(['\\', '(', ')'], ['\\\\', '\\(', '\\)'], $value);

        return mb_strimwidth($value, 0, 135, '…', 'Windows-1252');
    }
}
