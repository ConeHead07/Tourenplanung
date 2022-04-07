<?php

class MyProject_Time_Converter {
    
    static public function timeToSeconds($time, $format = '') {
        $p = explode(':', $time);
        $f = explode(':', $format);
        if ($format && count($f) !== count($p)) {
            throw new InvalidArgumentException('Format ist unkompatibel zum time-Wert. Ungleiche Anzahl Elemente!');
        }
        
        foreach($p as &$_part) $_part = (int)ltrim($_part, '0');
        $d = $h = $m = $s = 0;
        
        if (!$format) {
            switch( count($p) ) {
                case 1: $format = 'm';
                case 2: $format = 'h:m'; break;
                case 3: $format = 'h:m:s'; break;
                
                case 4:
                default: $format = 'd:h:m:s'; break;

            }
            $f = explode(':', $format);
        }
        
        for($i = 0; $i < count($f); ++$i) {
            switch($f[$i]) {
                case 's': $s = $p[$i]; break;
                case 'm': $m = $p[$i]; break;
                case 'h': $h = $p[$i]; break;
                case 'd': $d = $p[$i]; break;
                
                default:
                    throw new InvalidArgumentException('format ' . $format . ' enthält ungültige Zeichen wie f['.$i.'] ' . $f[$i] . '. Zulässig sind nur d:h:m:s!');
            }
        }
        
        return ($d * 24*3600) + ($h * 3600) + ($m * 60) + $s;
    }
            
    static public function timeToMinutes($time, $format = '') {
        
        $s = self::timeToSeconds($time, $format);
        $m = (float)floor($s / 60);
        if ($s % 60) $m+= ($s % 60) / 60;
        
        return $m;
    }        
    static public function timeToHours($time, $format = '') {
        
        $s = self::timeToSeconds($time, $format);
        $h = (float)floor($s / 3600);
        $h+= ($s % 3600) / 3600;
        
        return $h;
    }
}
