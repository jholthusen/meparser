<?php

// Parse PDF file and build necessary objects.
$parser = new \Smalot\PdfParser\Parser();
$pdf = $parser->parseFile('/path/to/document.pdf');

$text = $pdf->getText();
echo $text;

?>