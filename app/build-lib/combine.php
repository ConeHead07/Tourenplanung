<?php

$opts = array();
$lastKey = '';
$argv = $_SERVER['argv'];
array_shift($argv);
foreach($argv as $_a) {
    if (preg_match('/^--([^ =]*)[ =]?(.*)$/', $_a, $m)) {
        $lastKey = $m[1];
        $opts[$lastKey] = $m[2];
    } elseif (preg_match('/^-([^-])[ =]?(.*)$/', $_a, $m)) {
        $lastKey = $m[1];
        $opts[$lastKey] = $m[2];
    } else {
        $opts[$lastKey].= ' ' . $_a;
    }
}

$pCssRelUrl = '#(url\s*\(\s*[\'"]?(?!https://|http://|/)[ ]*)([^/].*?)([\'"]?\))#i';

$inFiles = (isset($opts['i']) 
            ? explode(' ', trim($opts['i'])) : 
            (isset($opts['in']) ? explode(' ', trim($opts['in'])) : array() )
           );

$outFile = (isset($opts['o']) 
            ? trim($opts['o']) : 
            (isset($opts['out']) ? trim($opts['out']) : '' )
           );
$outMode = (isset($opts['m']) 
            ? trim($opts['m']) : 
            (isset($opts['writemode']) ? trim($opts['writemode']) : '' )
           );
$cssBaseUrl = (isset($opts['b']) 
            ? trim($opts['b']) : 
            (isset($opts['cssbaseurl']) ? trim($opts['cssbaseurl']) : '' )
           );

$fo = fopen($outFile, ('a'!==$outMode ? 'w+' : 'a+'));
if (!$fo) die('Cannot Create Output-File ' . $outFile);

foreach($inFiles as $_f) {
    if (!trim($_f)) continue;
    
    $fp = fopen($_f, 'r');
    $fs = filesize($_f);
    if ($fp) {
        if ($cssBaseUrl) {
            while( !feof($fp)) fputs($fo, preg_replace($pCssRelUrl, "$1$cssBaseUrl$2$3", fgets($fp, $fs))  );
        } else {
            while( !feof($fp)) fputs($fo, fgets($fp, $fs) );
        }
    }
    fclose($fp);
    fputs($fo, "\r\n");
}
fclose($fo);

//echo 'write file ' . $outFile . ' with content: ' . PHP_EOL . file_get_contents($outFile) . PHP_EOL;
return 0;
?>
