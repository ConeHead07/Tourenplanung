<?php

require_once __DIR__ . '/../../library/MyProject/Time/Converter.php';

$times = array(
    'h:m' => '15:45',
    'h:m' => '01:09',
    'd:h:m' => '230:10:99',
    'h:m:s' => '30:20:15');

foreach($times as $format => $time){
    echo "t: " . $time . "($format) entspricht <br>" . PHP_EOL
        . ' -> ' . MyProject_Time_Converter::timeToSeconds($time, $format) . ' seconds ODER <br>' . PHP_EOL
        . ' -> ' . MyProject_Time_Converter::timeToMinutes($time, $format) . ' minutes ODER <br>' . PHP_EOL
        . ' -> ' . MyProject_Time_Converter::timeToHours($time,   $format) . ' hours <br>' . PHP_EOL
        . "<br>" . PHP_EOL;
}

