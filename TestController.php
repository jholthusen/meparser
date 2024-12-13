<?php

class TestController
{
    /**
     * Returns a JSON string object to the browser when hitting the root of the domain
     *
     * @url GET /meparser/
     * @url GET /
     */
    public function test()
    {
        return "Hello World";
    }
    /**
     * Returns a JSON string object to the browser when hitting the root of the domain
     *
     * @url POST /meparser/index.php
     * @url POST /meparser/
     * @url POST /
     * @url POST /index.php
     * 
     */
    public function parse_me()
    {
        // Parse PDF file and build necessary objects.
    $parser = new \Smalot\PdfParser\Parser();
    
    function to_utf8($item)
    {
        return mb_convert_encoding($item, "UTF-8", mb_detect_encoding($item));
    }
    if(is_null($_FILES["filename"]["tmp_name"])) return "Please include a file in your request"; // catching empty POST calls

$pdf = $parser->parseFile($_FILES["filename"]["tmp_name"]);
$post_swimmer_name=$_POST["swimmer_name"];
$section="";
$competition="";
$run="";
$result_array=array();

$text = $pdf->getText();
//echo $text;
$lines = explode("\n",$text);

$competition_name="";
$competition_number=0;
$event_info_read=false;
$event_name="";
$event_generator="";
$event_date_from="";
$event_date_to="";

foreach($lines as $line_number => $line)
    if($line_number == 1) //Nur für EasyWk, noch nicht mit anderen Tools geprüft.
    {
        $event_name=$line;

    }
    else if(str_starts_with($line,"Stand"))
    {
        $event_generator=substr($line,42);
        if($event_generator!="EasyWk")
        {
            echo "$event_generator is not EasyWk"; //
        }
    }
    else if(str_starts_with($line,"vom")) //Wettkampfdatum , nur mit mehrtäigigen getestet bisher
    {
        $event_date_from=substr($line,5,10);
        $event_date_to=substr($line,20);
        continue;
    }
    else if(str_starts_with($line,"Abschnitt "))
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
            $competition_number=substr($line,10,2-$offset);
            $competition_name=substr($line,15-$offset,6);
    		}


        //$competition=$line;
    }
    else if(str_starts_with($line,"Lauf"))
    {
        $run=$line;
    }
    else if(str_contains($line,"Bahn "))
    {
        $swimmer_name="";
        if($post_swimmer_name!="") // Initialer Use case: nur starts für spezifischen Schwimmer
        {
            if(!str_contains($line,$post_swimmer_name)) continue;
            $swimmer_name=$post_swimmer_name;
        }
        if(in_array(substr($line,0,6),array("Bahn S","Zeitne","Wender"))) continue; //Artefakte rausfiltern;

        //Example line to be processed here:
        // Bahn 6 Arthur Holthusen  2011 Duisburger ST 	00:35,91
        // first two elements : Bahn (Bahn number), last element Meldezeit
        // N elements in between, but certainly a number for the birth year 

        $swimmer_array=explode(" ",$line);
        $swimmer_array_length=count($swimmer_array);
        if($swimmer_array_length < 5) continue;
        $swimmer_club="";
        foreach($swimmer_array as $key => $value)
        {
            if(preg_match("/\d{4}/",$value)) // birth year is 4 digits
            {
                $year_pos=$key;
                //echo "Geburtsjahr ".$value."\n<br/>";
                $swimmer_birthyear=$value;
                break;
            }
        }

        if($swimmer_name=="")
        {
            for($i=2;$i < $year_pos; $i++)
            {
                $swimmer_name = $swimmer_name.$swimmer_array[$i]." ";
            }    
        }
        $swimmer_name=trim($swimmer_name);

        for($i=$year_pos+1;$i < $swimmer_array_length-1; $i++)
        {
            $swimmer_club = $swimmer_club.$swimmer_array[$i]." ";
        }
        $swimmer_club=trim($swimmer_club);

        $swimmer_meldezeit=trim($swimmer_array[$swimmer_array_length-1]);

        /*
        print_r($swimmer_array);
        echo "name: ".$swimmer_name;
        echo "Club: ".$swimmer_club;
        */
        //echo to_utf8($competition_name);

        $result_array["event"]["name"]=$event_name;
        $result_array["event"]["date_from"]=$event_date_from;
        $result_array["event"]["date_to"]=$event_date_to;
        $result_array["event"]["generator"]=$event_generator;

        $result_array["section"][to_utf8($section)]["competition"][to_utf8($competition)]["name"]=to_utf8($competition_name);
        $result_array["section"][to_utf8($section)]["competition"][to_utf8($competition)]["number"]=$competition_number;
        $result_array["section"][to_utf8($section)]["competition"][to_utf8($competition)]["run"][to_utf8($run)][substr($line,0,6)]["swimmer"]=$swimmer_name;
        $result_array["section"][to_utf8($section)]["competition"][to_utf8($competition)]["run"][to_utf8($run)][substr($line,0,6)]["meldezeit"]=$swimmer_meldezeit;
        
        $result_array["swimmer"][to_utf8($swimmer_name)]["section"][to_utf8($section)]["competition"][to_utf8($competition)]["run"][to_utf8($run)]["bahn"]=substr($line,0,6);
        $result_array["swimmer"][to_utf8($swimmer_name)]["section"][to_utf8($section)]["competition"][to_utf8($competition)]["run"][to_utf8($run)]["meldezeit"]=$swimmer_meldezeit;
        $result_array["swimmer"][to_utf8($swimmer_name)]["club"]=$swimmer_club;
        $result_array["swimmer"][to_utf8($swimmer_name)]["birthyear"]=$swimmer_birthyear;



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
