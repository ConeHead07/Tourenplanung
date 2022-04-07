<?php
/**
 * Created by PhpStorm.
 * User: f.barthold
 * Date: 06.08.2019
 * Time: 10:39
 */

namespace app\library\MyProject\Wwssync {

    class Bearbeitungsstatus
    {

        private $_appItemsCacheFile = '';
        private $_wwsItemsCacheFile = '';
        private $_appItemsUpdateFile = '';

        private $_iNumOpenAppItems = 0;
        private $_iNumOpenWwsItems = 0;
        private $_iNumWwsFound = 0;
        private $_iNumWwsNotFound = 0;
        private $_iNumWwsEqual = 0;
        private $_iNumWwsNotEqual = 0;
        private $_iNumUpdatedAppItems = 0;

        /** @var \Zend_Db_Adapter_Pdo_Mysql */
        private $_db = null;
        /** @var  \MyProject_Db_Sqlsrv */
        private $_wwsdb = null;

        private $_aEqualItems = [];
        private $_aUPD = [];

        public function __construct()
        {
            $this->_createCacheFiles();


            $this->setDbAdapter(\Zend_Registry::get('db'));

            $wwsdb = \Zend_Registry::get('wwsdb');
            $wwsdb->setFetchMode(\Sqlsrv::FETCH_ASSOC);
            $wwsdb->setScrollableCursor(\SQLSRV_CURSOR_STATIC);
            $this->setWwsAdapter($wwsdb);
        }

        public function setDbAdapter($db)
        {
            $this->_db = $db;
        }

        public function setWwsAdapter($wwsdb)
        {
            $this->_wwsdb = $wwsdb;
        }

        public function runDiff()
        {
            $this->_createCacheFiles();
            $this->_cacheOpenAppItems();
            $this->_cacheOpenWwsItems();
            $this->_cacheOpenDiffItems();

            return $this;
        }

        public function getProcessStatus()
        {

            return [
                'NumOpenAppItems' => $this->_iNumOpenAppItems,
                'NumOpenWwsItems' => $this->_iNumOpenWwsItems,
                'NumWwsFound' => $this->_iNumWwsFound,
                'NumWwsEqual' => $this->_iNumWwsEqual,
                'NumWwsNotEqual' => $this->_iNumWwsNotEqual,
                'NumWwsNotFound' => $this->_iNumWwsNotFound,
                'NumUpdatedAppItems' => $this->_iNumUpdatedAppItems,
            ];

        }


        private function _createCacheFiles()
        {
            $this->_appItemsCacheFile = tempnam(sys_get_temp_dir(), 'app_open_items_');
            $this->_appItemsUpdateFile = tempnam(sys_get_temp_dir(), 'app_update_items_');
            $this->_wwsItemsCacheFile = tempnam(sys_get_temp_dir(), 'wws_open_items_');

            return $this;
        }

        private function _cacheOpenAppItems()
        {
            $sql = <<<EOT
                SELECT Auftragsnummer, Bearbeitungsstatus 
                FROM mr_auftragskoepfe_dispofilter
                WHERE Mandant=10 AND Auftragsnummer > 0 AND LENGTH(Auftragsnummer) > 0 AND Auftragsnummer IS NOT NULL AND Bearbeitungsstatus BETWEEN 2 AND 5
                ORDER BY Auftragsnummer
EOT;

            $rslt = $this->_db->query($sql);
            $this->_iNumOpenAppItems = $rslt->rowCount();
            $iNumRecordsCheck = 0;

            $i = 0;
            while ($row = $rslt->fetch(\Zend_Db::FETCH_NUM)) {
                if (++$i % 10 === 0) {
                    echo '.';
                    if ($i % 800 === 0) {
                        echo "<br>\n";
                    }
                }
                $iNumRecordsCheck++;
                file_put_contents(
                    $this->_appItemsCacheFile,
                    ($iNumRecordsCheck > 1 ? "\n" : '') . implode(',', $row) . ',',
                    FILE_APPEND);
                flush();
            }
            echo "<br>\n";

            return $this;
        }

        private function _cacheOpenWwsItems()
        {

            flush();
            ob_end_flush();

            $wsql = <<<EOT
                    SELECT AuftragsNummer, BearbeitungsStatus FROM scoffice7.dbo.AuftragsKoepfe
                        WHERE Mandant = 10 AND BearbeitungsStatus BETWEEN 2 AND 5
                    UNION SELECT AuftragsNummer, BearbeitungsStatus FROM scoffice7_Mig.dbo.AuftragsKoepfe 
                        WHERE Mandant = 10 AND BearbeitungsStatus BETWEEN 2 AND 5
                    ORDER BY AuftragsNummer
EOT;
            $wrslt = $this->_wwsdb->query($wsql);
            $this->_iNumOpenWwsItems = $wrslt->num_rows();

            $i=0;
            while ($wrow = $wrslt->fetch(SQLSRV_FETCH_NUMERIC)) {
                if (++$i % 10 === 0) {
                    echo '.';
                    if ($i % 800 === 0) {
                        echo "<br>\n";
                    }
                }

                file_put_contents(
                    $this->_wwsItemsCacheFile,
                    implode(',', $wrow) . ',' . "\n",
                    FILE_APPEND);
                flush();
            }
            echo "<br>\n";

            return $this;

        }

        private function _cacheOpenDiffItems()
        {
            $fh = fopen($this->_appItemsCacheFile, 'r');
            $fhUpdate = fopen($this->_appItemsUpdateFile, 'w');
            $wfh = fopen($this->_wwsItemsCacheFile, 'r');
            $i = 0;
            $wi = 0;

            $this->_iNumWwsFound = 0;
            $this->_iNumWwsNotFound = 0;
            $this->_iNumWwsEqual = 0;
            $this->_iNumWwsNotEqual = 0;

            if ($fh && $fhUpdate && $wfh) {
                $aAppItemsBlock = [];
                $aFoundItems = [];
                $this->_aEqualItems = [];
                $aNotFoundBlock = [];
                $aWwsItemsBlock = [];

                $isLastAppLine = false;
                while (!$isLastAppLine && $i < 5000) {
                    $i++;
                    $csv = fgetcsv($fh, 50);
                    $isLastAppLine = feof($fh);

                    $_anr = $csv[0];
                    if (count($csv) < 2) {
                        die("Error in App-Cache-File in Line $i: " . implode(",", $csv));
                    }
                    $_bst = $csv[1];
                    $aAppItemsBlock[$_anr] = $_bst;

                    if ($i % 100 === 0 || $isLastAppLine) {
                        // Start wws-abgleich blockweise

                        rewind($wfh);
                        $aWwsItemsBlock = [];
                        $wi = 0;
                        $isLastWwsLine = false;
                        while (!$isLastWwsLine) {
                            $wi++;
                            $wcsv = fgetcsv($wfh, 50);
                            $isLastWwsLine = feof($wfh);

                            $_wanr = $wcsv[0];
                            $_wbst = $wcsv[1];
                            $aWwsItemsBlock[$_wanr] = $_wbst;

                            if ($wi % 100 === 0 || $isLastWwsLine) {

                                foreach ($aAppItemsBlock as $_a => $_b) {

                                    if (isset($aFoundItems[$_a])) {
                                        continue;
                                    }

                                    if (isset($aWwsItemsBlock[$_a])) {
                                        $_wb = $aWwsItemsBlock[$_a];
                                        $this->_iNumWwsFound++;

                                        if ($_b != $aWwsItemsBlock[$_a]) {
                                            $this->_iNumWwsNotEqual++;

                                            $_sCsv = "$_a,$_wb,$_b,[UPD]";
                                            fwrite($fhUpdate, "$_sCsv\n");
                                        } else {
                                            $this->_iNumWwsEqual++;

                                            $this->_aEqualItems[$_a] = $_b;
                                        }
                                        $aFoundItems[$_a] = $aWwsItemsBlock[$_a];
                                    } elseif (!isset($aFoundItems[$_a])) {
                                        $aNotFoundBlock[$_a] = $_b;
                                    }
                                }

                                // Reset
                                $aWwsItemsBlock = [];
                            }
                        }

                        $aAppItemsBlock = [];
                    }
                }

                foreach ($aNotFoundBlock as $_na => $_nb) {
                    if (isset($aFoundItems[$_na])) {
                        continue;
                    }

                    $this->_iNumWwsNotFound++;
                    $_new = 100 + $_nb;
                    fwrite($fhUpdate, "$_na,$_new,$_nb,[NOK]\n");
                }

                fclose($fh);
                fclose($wfh);
                fclose($fhUpdate);
            }

            return $this;
        }

        public function saveChanges()
        {

            $fh = fopen($this->_appItemsUpdateFile, 'r');

            if ($fh) {

                while ($row = fgetcsv($fh)) {

                    if (count($row) < 2) {
                        continue;
                    }
                    $_a = (int)$row[0];
                    $_b = (int)$row[1];

                    $sql = 'UPDATE mr_auftragskoepfe_dispofilter '
                        . ' SET Bearbeitungsstatus = ' . $_b
                        . ' WHERE Mandant=10 AND Auftragsnummer = ' . $_a;

                    $stmt = $this->_db->query($sql);

                    $this->_iNumUpdatedAppItems += $stmt->rowCount();

                }

                fclose($fh);
            }

            return $this;
        }

        public function printDebugItemList()
        {

            $aCSV = array_map(function ($v) {
                return explode(',', trim($v));
            }, file($this->_appItemsUpdateFile));

            usort($aCSV, function ($a, $b) {
                return (int)$a[0] - (int)$b[0];
            });

            $last = 0;
            $iUPD = 0;
            $iNOK = 0;
            $this->_aUPD = [];
            $iNumCSV = count($aCSV);


            echo "<h3>Changed-Items</h3>\n";
            for ($i = 0, $j = 1; $i < $iNumCSV; $i++, $j++) {
                $n = '';
                if ($aCSV[$i][3] == '[NOK]') {
                    $iNOK++;
                    $n = $iNOK;
                }
                if ($aCSV[$i][3] == '[UPD]') {
                    $iUPD++;
                    $n = $iUPD;
                }

                $_a = (int)$aCSV[$i][0];
                $_b = (int)$aCSV[$i][1];
                $this->_aUPD[] = $_a;
                $curr = $aCSV[$i][0];
                $col = ($last === $curr) ? "red" : "green";
                echo "<span style='color:$col'>#$j  " . implode(',', $aCSV[$i]) . "....$n</span><br>\n";
                $last = $curr;
            }

            uksort($this->_aEqualItems, function ($keyA, $keyB) {
                return (int)$keyA - (int)$keyB;
            });

            $iEQI = 0;
            $last = '';
            echo "<h3>Equal-Items</h3>\n";
            foreach ($this->_aEqualItems as $_a => $_b) {
                $iEQI++;
                $col = ($_a == $last) ? 'red' : 'green';
                echo "<span style='color:$col;'>#$iEQI $_a => $_b</span><br>\n";
                $last = $_a;
                if (in_array($_a, $this->_aUPD)) {
                    echo "$_a is in EqualItems and UpdateItems<br>\n";
                }

            }

            return $this;

        }

        public function __destruct()
        {
            $this->_removeCacheFiles();
        }

        public function _removeCacheFiles()
        {
            // TODO: Implement __destruct() method.
            @unlink($this->_appItemsCacheFile);
            @unlink($this->_wwsItemsCacheFile);
            @unlink($this->_appItemsUpdateFile);

            return $this;
        }

    }
}
