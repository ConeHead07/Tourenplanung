<?php
echo '<pre>' . PHP_EOL;
$pl = <<<EOT
mysql> show processlist;
+------+------+-----------------+-------+---------+-------+---------------------------------+---------------------------------------------------------------------------------------------------+
| Id   | User | Host            | db    | Command | Time  | State                           | Info                                                                                              |
+------+------+-----------------+-------+---------+-------+---------------------------------+---------------------------------------------------------------------------------------------------+
| 2938 | root | localhost:56714 | mt_rm | Query   | 25447 | Sending data                    | SELECT t.Mandant, t.LaufendeNummer FROM mr_warenbewegungen_dispofilter t LEFT JOIN mr_wws_wb_key  |
| 2954 | root | localhost:56776 | mt_rm | Query   | 25444 | Sending data                    | SELECT t.Mandant, t.LaufendeNummer FROM mr_warenbewegungen_dispofilter t LEFT JOIN mr_wws_wb_key  |
| 2972 | root | localhost:56819 | mt_rm | Query   | 25490 | Sending data                    | SELECT t.Mandant, t.LaufendeNummer FROM mr_warenbewegungen_dispofilter t LEFT JOIN mr_wws_wb_key  |
| 2984 | root | localhost:56860 | mt_rm | Query   | 25492 | Sending data                    | SELECT t.Mandant, t.LaufendeNummer FROM mr_warenbewegungen_dispofilter t LEFT JOIN mr_wws_wb_key  | 
| 3000 | root | localhost:56924 | mt_rm | Query   | 25490 | Sending data                    | SELECT t.Mandant, t.LaufendeNummer FROM mr_warenbewegungen_dispofilter t LEFT JOIN mr_wws_wb_key  |
| 3012 | root | localhost:56962 | mt_rm | Query   | 25461 | Sending data                    | SELECT t.Mandant, t.LaufendeNummer FROM mr_warenbewegungen_dispofilter t LEFT JOIN mr_wws_wb_key  |
| 3030 | root | localhost:57021 | mt_rm | Query   | 25478 | Sending data                    | SELECT t.Mandant, t.LaufendeNummer FROM mr_warenbewegungen_dispofilter t LEFT JOIN mr_wws_wb_key  |
| 3047 | root | localhost:57079 | mt_rm | Query   | 25476 | Sending data                    | SELECT t.Mandant, t.LaufendeNummer FROM mr_warenbewegungen_dispofilter t LEFT JOIN mr_wws_wb_key  |
| 3064 | root | localhost:57139 | mt_rm | Query   | 25485 | Sending data                    | SELECT t.Mandant, t.LaufendeNummer FROM mr_warenbewegungen_dispofilter t LEFT JOIN mr_wws_wb_key  |
| 3080 | root | localhost:57181 | mt_rm | Query   | 25484 | Sending data                    | SELECT t.Mandant, t.LaufendeNummer FROM mr_warenbewegungen_dispofilter t LEFT JOIN mr_wws_wb_key  |
| 3097 | root | localhost:57250 | mt_rm | Query   | 25480 | Sending data                    | SELECT t.Mandant, t.LaufendeNummer FROM mr_warenbewegungen_dispofilter t LEFT JOIN mr_wws_wb_key  |
| 3113 | root | localhost:57310 | mt_rm | Query   | 25480 | Sending data                    | SELECT t.Mandant, t.LaufendeNummer FROM mr_warenbewegungen_dispofilter t LEFT JOIN mr_wws_wb_key  |
| 3129 | root | localhost:57355 | mt_rm | Query   | 25488 | Sending data                    | SELECT t.Mandant, t.LaufendeNummer FROM mr_warenbewegungen_dispofilter t LEFT JOIN mr_wws_wb_key  |
| 3145 | root | localhost:57412 | mt_rm | Query   | 25492 | Sending data                    | SELECT t.Mandant, t.LaufendeNummer FROM mr_warenbewegungen_dispofilter t LEFT JOIN mr_wws_wb_key  |
| 3203 | root | localhost:57617 | mt_rm | Query   | 23307 | Waiting for table metadata lock | TRUNCATE mr_wws_wb_keys                                                                           |
| 3219 | root | localhost:57670 | mt_rm | Query   | 22255 | Waiting for table metadata lock | TRUNCATE mr_wws_wb_keys                                                                           |
| 3235 | root | localhost:57716 | mt_rm | Query   | 21354 | Waiting for table metadata lock | TRUNCATE mr_wws_wb_keys                                                                           |
| 3246 | root | localhost:57764 | mt_rm | Query   | 20438 | Waiting for table metadata lock | TRUNCATE mr_wws_wb_keys                                                                           |
| 3263 | root | localhost:57828 | mt_rm | Query   | 19545 | Waiting for table metadata lock | TRUNCATE mr_wws_wb_keys                                                                           |
| 3279 | root | localhost:57873 | mt_rm | Query   | 18638 | Waiting for table metadata lock | TRUNCATE mr_wws_wb_keys                                                                           |
| 3295 | root | localhost:57927 | mt_rm | Query   | 17732 | Waiting for table metadata lock | TRUNCATE mr_wws_wb_keys                                                                           |
| 3309 | root | localhost:57969 | mt_rm | Query   | 16824 | Waiting for table metadata lock | TRUNCATE mr_wws_wb_keys                                                                           |
| 3324 | root | localhost:58015 | mt_rm | Query   | 15914 | Waiting for table metadata lock | TRUNCATE mr_wws_wb_keys                                                                           |
| 3338 | root | localhost:58070 | mt_rm | Query   | 15006 | Waiting for table metadata lock | TRUNCATE mr_wws_wb_keys                                                                           |
| 3353 | root | localhost:58111 | mt_rm | Query   | 14095 | Waiting for table metadata lock | TRUNCATE mr_wws_wb_keys                                                                           |
| 3369 | root | localhost:58165 | mt_rm | Query   | 13179 | Waiting for table metadata lock | TRUNCATE mr_wws_wb_keys                                                                           |
| 3385 | root | localhost:58222 | mt_rm | Query   | 12275 | Waiting for table metadata lock | TRUNCATE mr_wws_wb_keys                                                                           |
| 3399 | root | localhost:58266 | mt_rm | Query   | 11363 | Waiting for table metadata lock | TRUNCATE mr_wws_wb_keys                                                                           |
| 3429 | root | localhost:58364 | mt_rm | Query   |  9711 | Waiting for table metadata lock | TRUNCATE mr_wws_wb_keys                                                                           |
| 3445 | root | localhost:58415 | mt_rm | Query   |  8809 | Waiting for table metadata lock | TRUNCATE mr_wws_wb_keys                                                                           |
| 3461 | root | localhost:58457 | mt_rm | Query   |  7906 | Waiting for table metadata lock | TRUNCATE mr_wws_wb_keys                                                                           |
| 3474 | root | localhost:58492 | mt_rm | Query   |  6999 | Waiting for table metadata lock | TRUNCATE mr_wws_wb_keys                                                                           |
| 3487 | root | localhost:58531 | mt_rm | Query   |  6100 | Waiting for table metadata lock | TRUNCATE mr_wws_wb_keys                                                                           |
| 3502 | root | localhost:58585 | mt_rm | Query   |  5197 | Waiting for table metadata lock | TRUNCATE mr_wws_wb_keys                                                                           |
| 3519 | root | localhost:58632 | mt_rm | Query   |  4290 | Waiting for table metadata lock | TRUNCATE mr_wws_wb_keys                                                                           |
| 3532 | root | localhost:58669 | mt_rm | Query   |  3384 | Waiting for table metadata lock | TRUNCATE mr_wws_wb_keys                                                                           |
| 3551 | root | localhost:58717 | mt_rm | Query   |  2353 | Waiting for table metadata lock | TRUNCATE mr_wws_wb_keys                                                                           |
| 3567 | root | localhost:58767 | mt_rm | Query   |  1438 | Waiting for table metadata lock | TRUNCATE mr_wws_wb_keys                                                                           |
| 3582 | root | localhost:58805 | mt_rm | Query   |   535 | Waiting for table metadata lock | TRUNCATE mr_wws_wb_keys                                                                           |
| 3592 | root | localhost:58839 | NULL  | Query   |     0 | NULL                            | show processlist                                                                                  |
+------+------+-----------------+-------+---------+-------+---------------------------------+---------------------------------------------------------------------------------------------------+
EOT;

$lines = explode("\r\n", $pl);

$fields = explode('|', trim($lines[2], "| \r\n"));
foreach($fields as &$field) $field = trim($field);

$kills = '';
$nokills = '';

for($i = 3; $i < count($lines); ++$i) {
    if (substr($lines[$i][0], 0, 1) !== '|') continue;
    $line = trim($lines[$i], "| \r\n");
    
    $vals = explode('|', $line);
    foreach($vals as &$val) $val = trim($val);
    if (count($vals) == count($fields)) {
        $row = array_combine($fields, $vals);
        if ( (int)$row['Time'] > 1000) {
            $kills.= 'kill ' . trim($row['Id']) . '; /* ' . $row['Time'] . ' */' . PHP_EOL;
        } else {
            $nokills.= 'kill ' . trim($row['Id']) . '; /* ' . $row['Time'] . ' */' . PHP_EOL;
        }
    } else {
        echo '!!!Zeile kann nicht gelesen werden (Anzahl Werte: ' . count($vals) . ', Fields: ' . count($fields) . '):' . PHP_EOL . $line . PHP_EOL . print_r($vals,1) . PHP_EOL . PHP_EOL; 
    }
}

echo 'KILLS: ' . PHP_EOL . $kills . PHP_EOL . PHP_EOL . 'NO-KILLS: ' . PHP_EOL . $nokills;

?>
