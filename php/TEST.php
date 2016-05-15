<?php

$var = "1,22,3,42";

$var = (preg_split("[,]", $var));

echo var_dump($var)." and the size is: ".sizeof($var);
?>