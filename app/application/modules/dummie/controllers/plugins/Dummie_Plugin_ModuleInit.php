<?php 

class Dummie_Plugin_ModuleInit extends Zend_Controller_Plugin_Abstract
{
	public function dispatchLoopStartup(Zend_Controller_Request_Abstract $request)
	{
		$module = $this->getRequest()->getModuleName();
		$moduleDir = APPLICATION_PATH . '/application/modules/' . $module;
		
		$config = new Zend_Config_Ini($moduleDir . '/config/config.ini');
		Zend_Registry::set('config_' . $modue, $config);
		// BEISPIELE: WAS HIER INITIALISERT WERDEN K�NNTE??
		// Lade Konfig f�r das Modul
		// Lade ACL f�r das Modul
		// Lade �bersetzungen
		// Lege neuen Pfad f�r modulspezifische Layouts fest
	}
}
