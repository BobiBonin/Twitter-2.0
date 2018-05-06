<?php

$str = "ko staa @armando @kibik @ohliuv";

preg_match_all('/(?!\b)(@\w+\b)/',$str,$matches);
var_dump($matches[0]);
$arr = array_unique($matches[0]);

foreach ($arr as $match) {
    $new[] = substr($match,1);
}
foreach ($new as $item) {
    echo $item . PHP_EOL;
}