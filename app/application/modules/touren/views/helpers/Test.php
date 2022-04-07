<?php

/*
 * Helper kann bei Einhaltung der Namenskonvention ohne Registrierung
 * im View direkt mit $this->Test() aufgerufen werden!
 */

/**
 * Description of Test
 * @example
 * Aufruf aus einem View (index.phtml)
 * <li><a href="#tabs-monat" name="tabs-monat" rel="<?php echo $this->baseUrl(); ?>
 * /touren/ajax/calendarmonthdata" onclick="pageLayout.close('west')">
 * <?php echo $this->Test(); ?>
 * </a></li>
 * @author rybka
 */
class Touren_View_Helper_Test {
    //put your code here
    
    public function Test() 
    {
        return __METHOD__ . ' :-) ';
    }
}

?>
