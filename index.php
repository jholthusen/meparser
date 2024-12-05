<?php
require __DIR__ . '/vendor/autoload.php';
include './TestController.php';

spl_autoload_register(); // don't load our classes unless we use them

$mode = 'debug'; // 'debug' or 'production'
$server = new \Jacwright\RestServer\RestServer($mode);
// $server->refreshCache(); // uncomment momentarily to clear the cache if classes change in production mode

$server->addClass('TestController');

$server->handle();

/*
// Parse PDF file and build necessary objects.
$parser = new \Smalot\PdfParser\Parser();

$pdf = $parser->parseFile($_FILES["filename"]["tmp_name"]);
$swimmer_name=$_POST["swimmer_name"];

$text = $pdf->getText();
//echo $text;
$lines = explode("\n",$text);

foreach($lines as $line)
    if(str_starts_with($line,"Abschnitt "))
    {
        $section_info=$line;
        echo $section_info."<br/>";
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
        echo $section." ";
        echo $competition." ";
        echo $run." ";
        echo $line."<br/>";
    }

/*
//POwershell Logic 
Foreach($line in $lines)
{
	if($line.StartsWith("Abschnitt "))
	{
		$section=$line.Substring(0,11)
	}
	if($line.StartsWith("Wettkampf "))
	{
		if($line.Substring(11,1) -eq " ") #einstellige Wettkämpfe
		{
			$offset=1
		}
		else 
		{
			$offset=0
		}
		if($line.Length -gt 10)
		{
			$competition="Wk "+$line.Substring(10,2-$offset)+"-"+$line.Substring(15-$offset,6)
		}
		else 
		{
			
		}
	}
	if($line.StartsWith("Lauf"))
	{
		$lauf=$line
	}
	if($line.Contains($Name))
	{
		$Start = New-Object PSObject
		$Start | Add-Member -type NoteProperty -name "Section" -Value $section
		$Start | Add-Member -type NoteProperty -name "competition" -Value $competition
		$Start | Add-Member -type NoteProperty -name "lauf" -Value $lauf
		$Start | Add-Member -type NoteProperty -name "lane" -Value $line.Substring(0,7)
		
		$section
		$competition
		$lauf
		$line
		$listOfStarts.Add($Start)
	}
	
}

*/

?>