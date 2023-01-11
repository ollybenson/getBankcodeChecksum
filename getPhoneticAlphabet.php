<?php
$phoneticAlphabet = array (
	'A' => 'Alpha',
	'B' => 'Bravo',
	'C' => 'Charlie',
	'D' => 'Delta',
	'E' => 'Echo',
	'F' => 'Foxtrot',
	'G' => 'Golf',
	'H' => 'Hotel',
	'I' => 'India',
	'J' => 'Juliet',
	'K' => 'Kilo',
	'L' => 'Lima',
	'M' => 'Mike',
	'N' => 'November',
	'O' => 'Oscar',
	'P' => 'Papa',
	'Q' => 'Quebec',
	'R' => 'Romeo',
	'S' => 'Sierra',
	'T' => 'Tango',
	'U' => 'Uniform',
	'V' => 'Victor',
	'W' => 'Whiskey',
	'X' => 'X-ray',
	'Y' => 'Yankee',
	'Z' => 'Zulu',
	'0' => 'Zero',
	'1' => 'One',
	'2' => 'Two',
	'3' => 'Three',
	'4' => 'Four',
	'5' => 'Five',
	'6' => 'Six',
	'7' => 'Seven',
	'8' => 'Eight',
	'9' => 'Nine',
	' ' => 'Space',
	'.' => 'Full-stop',
	'-' => 'Dash',
	'_' => 'Underscore');

if (isset($_GET['text']) && !empty($_GET['text'])) $input = $_GET['text'];
	elseif (isset($_SERVER['QUERY_STRING'])  && !empty($_SERVER['QUERY_STRING'])) $input = $_SERVER['QUERY_STRING'];
	else {
		echo json_encode(array('error' => 'No input received'));
		exit;
		}
$temp = str_split(strtoupper($input));
$outputArray = array();
foreach($temp AS $key => $value) if (array_key_exists($value,$phoneticAlphabet)) $outputArray[$key] = $phoneticAlphabet[$value];	
array_filter($outputArray);
echo json_encode (array(
		'input' => $input, 
		'output' => (string) implode(" ",$outputArray)));
?>