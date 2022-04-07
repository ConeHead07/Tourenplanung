<?php
require_once 'JqGrid/JsFunction.php';
/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
class JqGrid_Zend_GridModelCreator {
    
    public function __construct() {
        
    }
    
    public static function getColModelByZendTbl(Zend_Db_Table $tbl) {
        return $tbl->info(); //Zend_Db_Table::METADATA);
        die(print_r($cols, 1));
    }
    
    public static function getColModelByModelName($name) {
        $s = MyProject_Model_Database::loadStorage($name);
//        Zend_Debug::dump($s, 'storage', true);
        if ($s) return $s->info(); //Zend_Db_Table::METADATA);
        return NULL;
        die(print_r($cols, 1));
    }
    
    public static function createGridModel($tbl) {
        if ($tbl instanceof Zend_Db_Table) $info = self::getColModelByZendTbl($tbl);
        elseif (is_string($tbl)) $info = self::getColModelByModelName ($tbl);
        else return '';
        
        $meta = $info['metadata'];
        //return $info;
        $cols = array();
        foreach($meta as $_f => $_p) {
            $cols[$_f] = array("name"=>$_f,"index"=>$_f,"editable"=>false);
            if ($_p['PRIMARY']) $cols[$_f]["key"] = true;
        }
        $grid_id = 'grid' . $info['name'];
        $grid = array(
            'colNames'      => array_keys( $cols ),
            'colModel'      => array_values( $cols ),
            'caption'       => $info['name'],
            'datatype'      => 'json',
            'pager'         => '#' . $grid_id . 'Lst_pager',
            'height'        => 'auto',
            'jsonReader'    => array('repeatitems'=> false,'id' => 0),
            'autowidth'     => true,
            'rowList'       => array(10,20,30,40,50,100),
            'rowNum'        => 10,
            'rownumbers'    => false,
            'altRows'       => true,
            'altclass'      => 'ui-jqgrid-altrow',
            'resizable'     => true,
            'sortable'      => true,
            'url'           => APPLICATION_BASE_URL . '/vorgaenge/gridresponsedata',
            "onSelectRow"   => new JsFunction('function(id, status) { }'), // +
            'shrinkToFit'   => true,
            'loadError'     =>  new JsFunction('function(xhr,status,error){alert(status+\'\n\'+error);}'),
        );
        return $grid;
        
    }
    
    public static function createGridModelByColNames($colNames, $grid_id = '', $opts = array()) {
        if (!$opts) $opts = array();
        $cols = array();
        foreach($colNames as $_f) {
            $cols[$_f] = array("name"=>$_f,"index"=>$_f,"editable"=>false);
        }
        if (!isset($grid_id)) $grid_id = 'grid' . substr(md5( time() . rand()), 0, 10);
        $grid = array(
            'colNames'      => array_keys( $cols ),
            'colModel'      => array_values( $cols ),
            'caption'       => '',
            'datatype'      => 'json',
            'pager'         => '#' . $grid_id . 'Lst_pager',
            'height'        => 'auto',
            'jsonReader'    => array('repeatitems'=> false,'id' => 0),
            'autowidth'     => true,
            'rowList'       => array(10,20,30,40,50,100),
            'rowNum'        => 10,
            'rownumbers'    => false,
            'altRows'       => true,
            'altclass'      => 'ui-jqgrid-altrow',
            'resizable'     => true,
            'sortable'      => true,
            'url'           => '',
            "onSelectRow"   => new JsFunction('function(id, status) { }'), // +
            'shrinkToFit'   => true,
            'loadError'     =>  new JsFunction('function(xhr,status,error){alert(status+\'\n\'+error);}'),
        );
        //die( '<pre>#'.__LINE__ . ' ' . __METHOD__ . "\n" . print_r($grid,1));
        //if ($opts) $grid = array_merge($grid, $opts);
        return $grid;
        
    }
}
?>
