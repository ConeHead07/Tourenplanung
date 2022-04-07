<?php

class Dummie_Bootstrap extends Zend_Application_Module_Bootstrap
{
    protected function _initLayout()
    {
        $p = explode('/', $_SERVER['SERVER_PROTOCOL']);
        $p = strtolower(array_shift($p));
        $h = $_SERVER["HTTP_HOST"];
        $u = $_SERVER['REQUEST_URI'];
        $url = $p .'://' . $h . $u;
//        echo ' url: ' . $url . "<br>\n";
        $r = new Zend_Controller_Request_Http();
        $r->setBaseUrl( APPLICATION_BASE_URL );
        $r->setRequestUri($u);
//        die( '#' . $r->getRequestUri());
        $p = explode('_', __CLASS__);
        $thisModul = array_shift($p);
        $requestedModul = $r->getModuleName();
        if ($thisModul != $this->getModuleName()) return;
    }
}