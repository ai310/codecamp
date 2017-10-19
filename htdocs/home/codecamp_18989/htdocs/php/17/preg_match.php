<?php
$subject = "php,perl,apache,Linux";
$pattern = '/apache/';
$match_num = preg_match($pattern, $subject, $matches);
var_dump($match_num);
print "<br>";
var_dump($matches);
?>