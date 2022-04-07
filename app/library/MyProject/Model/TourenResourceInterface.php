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
interface MyProject_Model_TourenResourceInterface {
    //put your code here
    public function drop($data, array $tourData = []);
    public function move($data);
}
