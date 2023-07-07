<?php

$command = "service apache2 status";
exec($command." 2>&1", $output);
$array = explode(" ", $output[4]);
print_r($array[4]);
//return $array;
?>