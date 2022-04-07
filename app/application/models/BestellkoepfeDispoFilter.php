<?php


class Model_BestellkoepfeDispoFilter extends MyProject_Model_Database
{
    //put your code here
    protected $_storageName = 'bestellkoepfeDispoFilter';
    
    public function reloadData()
    {
        $db = $this->getStorage()->getAdapter();
        
        $tblBDF = $this->getStorage()->info(Zend_Db_Table::NAME);
        $tblB = MyProject_Model_Database::loadStorage('bestellkoepfe')->info(Zend_Db_Table::NAME);
        $tblVDF = MyProject_Model_Database::loadStorage('vorgaengeDispoFilter')->info(Zend_Db_Table::NAME);
        
        $this->getStorage()->delete("1 > 0");
        
        $sql = 'Insert into ' . $tblBDF . '(Mandant, Auftragsnummer, Bestellnummer, BestellName, ErwarteterEingangWoche, ErwarteterEingangJahr, ErwarteterEingang, ErwarteterEingangterminFix) '
              .' Select bk.Mandant, bk.Auftragsnummer, bk.Bestellnummer, bk.BestellName, bk.ErwarteterEingangWoche, bk.ErwarteterEingangJahr, bk.ErwarteterEingang, bk.ErwarteterEingangterminFix '
              .' FROM ' . $tblVDF . ' akf '
              .' left join ' . $tblB . ' bk '
              .' ON (akf.Mandant =bk.Mandant and akf.Auftragsnummer = bk.Auftragsnummer)';
        // Noch ungetestet      
        $sqlNeu.= 'Insert into ' 
              .$tblBDF.'(Mandant, Auftragsnummer, Bestellnummer, BestellName, ErwarteterEingangWoche, ErwarteterEingangJahr, ErwarteterEingang, ErwarteterEingangterminFix) '
              .' Select  Mandant, Auftragsnummer, Bestellnummer, BestellName, ErwarteterEingangWoche, ErwarteterEingangJahr, ErwarteterEingang, ErwarteterEingangterminFix '
              .'From ' . $tblB . ' bk WHERE CONCAT(Mandant,":", Auftragsnummer) IN ('
              .' SELECT CONCAT(Mandant,":", Auftragsnummer) FROM ' . $tblVDF
              .')';
                
        $db->query($sql);
/*        
        CASE
 WHEN bk.ErwarteterEingangWoche = 0 
 THEN 
  CASE 
  WHEN bk.ErwarteterEingang IS NOT NULL
  THEN MID(YearWeek(bk.ErwarteterEingang), 5, 2)
  WHEN bk.Liefertermin IS NOT NULL
  THEN MID(YearWeek(bk.Liefertermin), 5,2)
  ELSE 0
  END
 ELSE bk.ErwarteterEingangWoche
 END AS ErwarteterEingangWoche,
 CASE
 WHEN bk.ErwarteterEingangWoche = 0 
 THEN 
  CASE 
  WHEN bk.ErwarteterEingang IS NOT NULL
  THEN MID(YearWeek(bk.ErwarteterEingang), 1, 4)
  WHEN bk.Liefertermin IS NOT NULL
  THEN MID(YearWeek(bk.Liefertermin), 1, 4)
  ELSE 0
  END
 ELSE bk.ErwarteterEingangJahr
 END AS ErwarteterEingangJahr,
 CASE
 WHEN bk.ErwarteterEingang IS NULL AND bk.Liefertermin IS NOT NULL
 THEN bk.Liefertermin
 ELSE bk.ErwarteterEingang
 END AS ErwarteterEingang,
*/
    }
    
}