<?php 

class Dummie_Plugin_ModuleInit extends Zend_Controller_Plugin_Abstract
{
	public function dispatchLoopStartup(Zend_Controller_Request_Abstract $request)
	{
		$module = $this->getRequest()->getModuleName();
		$moduleDir = APPLICATION_PATH . '/application/modules/' . $module;
		
		$config = new Zend_Config_Ini($moduleDir . '/config/config.ini');
		Zend_Registry::set('config_' . $modue, $config);
		// BEISPIELE: WAS HIER INITIALISERT WERDEN KÖNNTE??
		// Lade Konfig für das Modul
		// Lade ACL für das Modul
		// Lade Übersetzungen
		// Lege neuen Pfad für modulspezifische Layouts fest
	}
}
