<?php
require __DIR__ . '/vendor/autoload.php';
//standalone script to extract the plain text from pdf

$parser = new \Smalot\PdfParser\Parser();
$pdf = $parser->parseFile($_FILES["filename"]["tmp_name"]);

$text = $pdf->getText();
echo $text;


?>