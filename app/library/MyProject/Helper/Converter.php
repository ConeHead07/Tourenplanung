<?php
/**
 * Created by PhpStorm.
 * User: f.barthold
 * Date: 19.09.2018
 * Time: 14:10
 */

class MyProject_Helper_Converter extends Zend_Controller_Action_Helper_Abstract
{
    /**
     * Constructor: initialize plugin loader     *
     * @return void
     */
    public function __construct()
    {
        $this->pluginLoader = new Zend_Loader_PluginLoader();
    }

    static public function zerofill($value, int $width = 2): string {
        return str_pad((string)$value, $width, '0', STR_PAD_LEFT);
    }

    static public function secondsToTime(int $seconds): string
    {
        $h = self::zerofill(floor($seconds / 3600), 2);
        $m = self::zerofill(floor( $seconds / 60), 2);
        $s = self::zerofill($seconds % 60, 2);

        return "$h:$m:$s";
    }
}