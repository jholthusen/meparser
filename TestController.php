<?php

class TestController
{
    /**
     * Returns a JSON string object to the browser when hitting the root of the domain
     *
     * @url GET /meparser/
     */
    public function test()
    {
        return "Hello World";
    }
    /**
     * Returns a JSON string object to the browser when hitting the root of the domain
     *
     * @url POST meparser/index.php
     * @url POST meparser/
     * @url POST /
     */
    public function parse_me()
    {
        // Parse PDF file and build necessary objects.
    $parser = new \Smalot\PdfParser\Parser();
    
    function to_utf8($item)
    {
        return mb_convert_encoding($item, "UTF-8", mb_detect_encoding($item));
    }

$pdf = $parser->parseFile($_FILES["filename"]["tmp_name"]);
$swimmer_name=$_POST["swimmer_name"];
$section="";
$competition="";
$run="";
$result_array=array();

$text = $pdf->getText();
//echo $text;
$lines = explode("\n",$text);

foreach($lines as $line)
    if(str_starts_with($line,"Abschnitt "))
    {
        $section_info=$line;
        //echo $section_info."<br/>";
        $section=substr($line,0,11);
    }
    else if(str_starts_with($line,"Wettkampf "))
    {
        $offset=0;
        if(substr($line,11,1)==" ") //single digit competitions
        {
            $offset=1;
        }
        if(strlen($line)>10)
		{
			$competition="Wk ".substr($line,10,2-$offset)."-".substr($line,15-$offset,6);
		}


        //$competition=$line;
    }
    else if(str_starts_with($line,"Lauf"))
    {
        $run=$line;
    }
    else if(str_contains($line,$swimmer_name))
    {
        $result_array[to_utf8($section)][to_utf8($competition)][to_utf8($run)]=to_utf8($line);
        /*echo $section." ";
        echo $competition." ";
        echo $run." ";
        echo $line."<br/>";
        */
    }
        //print_r($result_array);
        return $result_array; // JSON serialization happens outside
    }
}
