<?php
if (basename(__FILE__) == basename($_SERVER['PHP_SELF'])) {
    echo '#' . __LINE__ . ' ' . basename(__FILE__) . '<br/>' . PHP_EOL;
    echo '#' . __LINE__ . ' ' . basename($_SERVER['PHP_SELF']) . '<br/>' . PHP_EOL;
    
    $options = getopt('Y::');
    $year = (isset($options['Y']) ? $options['Y'] : date('Y'));
    if (isset($_REQUEST['Y'])) $year = $_REQUEST['Y'];

    // http://www.feiertage.net/frei-tage.php
    $C = new MyProject_Date_Holidays;

    $aBL  = $C->getBLAlias();
    $FTBL = $C->getGFB();
    $H = $C->getHolidays($year);
    if (0) echo '<pre>FTBL: ' . print_r($FTBL,1) . '</pre>' . PHP_EOL;
    if (1) echo '<pre>Holidays ' . $year . ' : ' . print_r($H,1) . '</pre>' . PHP_EOL;
    
    $neujahr = strtotime("$year-01-01");
    $currDate= date('Y-m-d', $neujahr);
    $n = 0;
    do {
        echo $currDate;
        $chck = MyProject_Date_Holidays::getHolidayByDate($currDate);
        if (null !== $chck) echo ' '. $chck['name'];
        echo '<br/>' . PHP_EOL;
        ++$n;
        $currDate = date('Y-m-d', strtotime("+$n days", $neujahr));
        if ($n > 370) exit;
    } while($currDate <= "$year-12-31");
    exit;
}



class MyProject_Date_Holidays {
    protected $year = 0;
    
    protected $GFT = '';
    
    protected $BL = array();
    protected static $holidayCache = array();
    
    public static function getBLAlias() {
        return array(
         'BW' =>  'Baden-Württemberg',
         'NI' =>  'Niedersachsen',
         'BY' =>  'Bayern',
         'NW' =>  'Nordrhein-Westfalen',
         'BE' =>  'Berlin',
         'RP' =>  'Rheinland-Pfalz',
         'BB' =>  'Brandenburg',
         'SL' =>  'Saarland',
         'HB' =>  'Bremen',
         'SN' =>  'Sachsen',
         'HH' =>  'Hamburg',
         'ST' =>  'Sachsen-Anhalt',
         'HE' =>  'Hessen',
         'SH' =>  'Schleswig-Holstein',
         'MV' =>  'Mecklenburg-Vorpommern',
         'TH' =>  'Thüringen',
    );
    }
    
    public static function getGFB() {
        $GFB = array();
        foreach( self::mkArrayByCsv( 
           'Feiertag;           ALL;HALB;BW;BY;BE;BB;HB;HH;HE;MV;NI;NW;RP;SL;SN;ST;SH;TH'. PHP_EOL
          .'Neujahrstag;        1;  0;   1; 1; 1; 1; 1; 1; 1; 1; 1; 1; 1; 1; 1; 1; 1; 1' . PHP_EOL
          .'Heilige Drei Könige;0;  0;   1; 1; 0; 0; 0; 0; 0; 0; 0; 0; 0; 0; 0; 1; 0; 0' . PHP_EOL
          .'Karfreitag;         1;  0;   1; 1; 1; 1; 1; 1; 1; 1; 1; 1; 1; 1; 1; 1; 1; 1' . PHP_EOL
          .'Ostermontag;        1;  0;   1; 1; 1; 1; 1; 1; 1; 1; 1; 1; 1; 1; 1; 1; 1; 1' . PHP_EOL
          .'1. Mai;             1;  0;   1; 1; 1; 1; 1; 1; 1; 1; 1; 1; 1; 1; 1; 1; 1; 1' . PHP_EOL
          .'Christi Himmelfahrt;1;  0;   1; 1; 1; 1; 1; 1; 1; 1; 1; 1; 1; 1; 1; 1; 1; 1' . PHP_EOL
          .'Pfingstmontag;      1;  0;   1; 1; 1; 1; 1; 1; 1; 1; 1; 1; 1; 1; 1; 1; 1; 1' . PHP_EOL
          .'Fronleichnam;       0;  0;   1; 1; 0; 0; 0; 0; 1; 0; 0; 1; 1; 1; 0; 0; 0; 0' . PHP_EOL
          .'Mariä Himmelfahrt;  0;  0;   0; k; 0; 0; 0; 0; 0; 0; 0; 0; 0; 1; 0; 0; 0; 0' . PHP_EOL
          .'Tag der dt. Einheit;1;  0;   1; 1; 1; 1; 1; 1; 1; 1; 1; 1; 1; 1; 1; 1; 1; 1' . PHP_EOL
          .'Reformationstag;    0;  0;   0; 0; 0; 1; 0; 0; 0; 1; 0; 0; 0; 0; 1; 1; 0; 1' . PHP_EOL
          .'Allerheiligen;      0;  0;   1; 1; 0; 0; 0; 0; 0; 0; 0; 1; 1; 1; 0; 0; 0; 0' . PHP_EOL
          .'Buß- u. Bettag;     0;  0;   0; 0; 0; 0; 0; 0; 0; 0; 0; 0; 0; 0; 1; 0; 0; 0' . PHP_EOL
          .'Heiligabend;        1;  1;   1; 1; 1; 1; 1; 1; 1; 1; 1; 1; 1; 1; 1; 1; 1; 1' . PHP_EOL
          .'1. Weihnachtstag;   1;  0;   1; 1; 1; 1; 1; 1; 1; 1; 1; 1; 1; 1; 1; 1; 1; 1' . PHP_EOL
          .'2. Weihnachtstag;   1;  0;   1; 1; 1; 1; 1; 1; 1; 1; 1; 1; 1; 1; 1; 1; 1; 1' . PHP_EOL
          .'Sylvester;          1;  1;   1; 1; 1; 1; 1; 1; 1; 1; 1; 1; 1; 1; 1; 1; 1; 1'
          ) as $row) {
            $ft = $row['Feiertag'];
            unset($row['Feiertag']);
            $GFB[$ft] = $row;
        }
        
        return $GFB;
    }
    
    public function setYear($year) {
        $this->year = $year;
    }
    
    public function getYear() {
        return ($this->year)?:date("Y");
    }
    
    public function getGermanPublicHolidays($year = null) {
        if(is_null($year)) $year = $this->getYear();
        return self::getGermanPublicHolidaysByYear($year);
    }
    
    public static function getGermanPublicHolidaysByYear($year) {
        
        if(!$easter = easter_date($year)) {
            return false;
        } else {
            $holidays['Neujahrstag']         = mktime(0,0,0,1,1,     $year);
            $holidays['Heilige Drei Könige'] = mktime(0,0,0,1,6,     $year);
            $holidays['Weiberfastnacht']     = strtotime("-52 days", $easter);
            $holidays['Rosenmontag']         = strtotime("-48 days", $easter);
            $holidays['Fastnacht']           = strtotime("-47 days", $easter);
            $holidays['Achermittwoch']       = strtotime("-46 days", $easter);
            $holidays['Valentinstag']        = mktime(0,0,0,2,14,    $year);
            $holidays['1. Mai']              = mktime(0,0,0,5,1,     $year);
            $holidays['Karfreitag']          = strtotime("-2 days",  $easter);
            $holidays['Ostern']              = $easter;
            $holidays['Ostersonntag']        = $easter;
            $holidays['Ostermontag']         = strtotime("+1 day",   $easter);
            $holidays['Christi Himmelfahrt'] = strtotime("+39 days", $easter);
            $holidays['Pfingsten']           = strtotime("+49 days", $easter);
            $holidays['Pfingstsonntag']      = strtotime("+49 days", $easter);
            $holidays['Pfingstmontag']       = strtotime("+50 days", $easter);
            $holidays['Fronleichnam']        = strtotime("+60 days", $easter);
            $holidays['Mariä Himmelfahrt']   = mktime(0,0,0,8,15,    $year);
            $holidays['Reformationstag']     = mktime(0,0,0,10,31,   $year);
            $holidays['Allerheiligen']       = mktime(0,0,0,11,1,    $year);
            $holidays['Tag der dt. Einheit'] = mktime(0,0,0,10,3,    $year);
            $holidays['Heiligabend']         = mktime(0,0,0,12,24,   $year);
            $holidays['1. Weihnachtstag']    = mktime(0,0,0,12,25,   $year);
            $holidays['2. Weihnachtstag']    = mktime(0,0,0,12,26,   $year);
            $holidays['Sylvester']           = mktime(0,0,0,12,31,   $year);
            $holidays['Buß- u. Bettag']      = strtotime("-11 days", strtotime("1 sunday", mktime(0,0,0,11,26,$year)));
            $holidays['1. Advent']           = strtotime("1 sunday", mktime(0,0,0,11,26,$year));
            $holidays['2. Advent']           = strtotime("2 sunday", mktime(0,0,0,11,26,$year));
            $holidays['3. Advent']           = strtotime("3 sunday", mktime(0,0,0,11,26,$year));
            $holidays['4. Advent']           = strtotime("4 sunday", mktime(0,0,0,11,26,$year));
            return $holidays;
        }
    }
    
    public static function getHolidayByDate($date) {
        $time = strtotime($date);
        
        if (!$time) return false;        
        
        $year = date('Y', $time);
        $test = date('Y-m-d', $time);
        
        foreach(self::getHolidaysByYear($year) as $_date => $_props) {
            if ($_date == $test && $_props['frei']) return $_props;
        }        
        return null;
    }
    
    public function getHolidays($year = null) {
        if(is_null($year)) $year = $this->getYear();
        
        return self::getHolidaysByYear($year);
    }
    
    public static function getHolidaysByYear($year) {
        if (!isset(self::$holidayCache[$year])) {
            $h = self::getGermanPublicHolidaysByYear( $year );
            #echo '#' . __LINE__ . ' h: ' . print_r($h, 1) . PHP_EOL;

            $FTBL = self::getGFB();
            $holidays = array();
            foreach($h as $_name => $_time) {
                $_d = date('Y-m-d', $_time);
                $k = $_name;
                $only = array();
                $frei = 0;
                $halb = 0;

                if (isset($FTBL[$k])) {
                   $frei = 1;
                   $halb = $FTBL[$k]['HALB'];
                   if (!$FTBL[$k]['ALL']) {
                       foreach($FTBL[$k] as $b => $x) if ($b!='ALL' && $x) $only[] = $b;
                   }
                }

                $holidays[$_d] = array(
                    'name' => $_name,
                    'time' => $_time,
                    'frei' => $frei,
                    'halb' => $halb,
                    'only' => $only,
                );
            }
            ksort($holidays);
            self::$holidayCache[$year] = $holidays;
        }
        return self::$holidayCache[$year];
    }
    
    public static function mkArrayByCsv($csv, $separator = ';', $assoc = true) {
        if (null == $separator) $separator = ';';
        $keys = array();
        $arr = array();
        foreach(explode("\n", $csv) as $line) {
            if (strpos($line, $separator) === false) continue;
            $line = preg_replace('/'.$separator.'\s*/', $separator, trim($line));
            
            if ($assoc) {
                if (!count($keys)) $keys = explode($separator, trim($line));
                else $arr[] = array_combine($keys, explode($separator, $line));
            } else {
                $arr[] = explode($separator, $line);
            }
        } 
        if (0) echo '<pre>arr: ' . print_r($arr,1) . '</pre>' . PHP_EOL;
        return $arr;
    }
    
    public static function mkArrayByCsvPairs($csv, $separator = ';') {
        if (null == $separator) $separator = ';';
        $arr = array();
        foreach(explode("\n", $csv) as $line) {
            if (strpos($line, $separator) === false) continue;
            $line = preg_replace('/'.$separator.'\s*/', $separator, trim($line));

            $vals = explode($separator, trim($line));
            $arr[$vals[0]] = implode($separator, array_slice($vals, 1));
        } 
        if (0) echo '<pre>arr: ' . print_r($arr,1) . '</pre>' . PHP_EOL;
        return $arr;
    }    
}




?>

