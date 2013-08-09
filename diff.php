
<?php
function my_serialize(&$arr,$pos){
  $arr = serialize($arr);
}

function my_unserialize(&$arr,$pos){
  $arr = unserialize($arr);
}
$first_array[] = array('0'=>'ala'); 
$second_array[] = array('0'=>'aka'); 
for ($i = 1; $i < 25000; $i++) {
	$first_array[] = array($i=>$i.'ala');
}
for ($i = 1; $i < 25000; $i++) {
	$second_array[] = array($i=>$i.'ala');
}
//$first_array = array(array('0'=>'ala'),array('1'=>'ada'),array('2'=>'ama'));
// $second_array = array(array('0'=>'ala'),array('1'=>'aka'),array('2'=>'ama'));
 //make a copy
$t1 = microtime (true);
$first_array_s = $first_array;
$second_array_s = $second_array;

// serialize all sub-arrays
array_walk($first_array_s,'my_serialize');
array_walk($second_array_s,'my_serialize');

// array_diff the serialized versions
$diff = array_diff($first_array_s,$second_array_s);

// unserialize the result
array_walk($diff,'my_unserialize');
$t2 = microtime (true);

$final = mktime(0, 0, $t2 - $t1);

print_r($diff);
echo $t2 - $t1;
?>