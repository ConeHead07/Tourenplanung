<?php
/**
 * Created by PhpStorm.
 * User: f.barthold
 * Date: 02.05.2019
 * Time: 11:58
 */

class MyProject_Helper_String
{

    public static function stripMargin(string $txt, string $intendMarker = '|') {

        return preg_replace(
            '#^\s*'. preg_quote($intendMarker) . '#m',
            '',
            ltrim($txt)
        );

    }
}