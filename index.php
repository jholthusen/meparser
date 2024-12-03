<?php
require __DIR__ . '/vendor/autoload.php';

// Parse PDF file and build necessary objects.
$parser = new \Smalot\PdfParser\Parser();
$pdf = $parser->parseFile('C:\Users\holthusen\OneDrive - adesso Group\Dokumente\dev\meparser\meparser\241207-Duisburg-Duisburger_ST.pdf');

$text = $pdf->getText();
echo $text;

?>