<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of newPHPClass
 *
 * @author rybka
 */
interface MyProject_Model_Interface {
    //put your code here
    public function getStorage();
    public function insert(array $data);
    public function update(array $data, $id);
    public function delete($id);
    public function fetchEntries();
    public function fetchEntry($id);
}

?>
