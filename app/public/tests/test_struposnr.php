<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

$nr1 = '001.002.0030.400.005';

echo $nr1. '<br/>' . PHP_EOL;
$nr2 = preg_replace('/^0+/', '', $nr1);
echo $nr2. '<br/>' . PHP_EOL;
$nr2 = preg_replace('/\.0+/', '.', $nr2);
echo $nr2. '<br/>' . PHP_EOL;
?>
