<?php

/**
 * Description of Auth
 *
 * @author rybka
 */
class Plugin_Auth extends Zend_Controller_Plugin_Abstract 
{
    protected $_debug = false;  
    
    public function dispatchLoopStartup(Zend_Controller_Request_Abstract $request) 
    {
        parent::routeStartup($request);
        
        /* @var $acl MyProject_Acl */    
        $acl = Zend_Registry::get('acl');
        
        // ? 'user' : 'guest';
        $identity = Zend_Auth::getInstance()->getIdentity();
        $role = (null == $identity) ? 'guest' : $identity->user_role;
        
        $module = $this->getRequest()->getModuleName();
        $prefixModule = ($module != 'default') ? $module.'_' : '';
        
        $IsAllowed = Zend_Registry::get('acl')->isAllowed(
            $role, 
            // Führte zu Fehler: $prefixModule.$role,
            $prefixModule . $request->getControllerName(),
            $request->getActionName()
        );
        
        if ($this->_debug && !$IsAllowed) {
            echo Zend_Debug::dump($identity, 'identity', true);
            echo '#' . __LINE__ . ' ' .__METHOD__ . '<br>' . "\n";         
            echo 'isAllowed( '.$role.', index, index): ' . (int)$acl->isAllowed( $role, 'index', 'index' ) . "<br>\n";
            echo 'isAllowed( '.$role.', touren_index,  index): ' . (int)$acl->isAllowed( $role, 'touren_index', 'index' )  . "<br>\n";
        
            echo 'Zend_Registry::get(acl)->isAllowed(' . implode(',<br>'.PHP_EOL, array(
                'role:'.$role,
                $prefixModule . $request->getControllerName(),
                $request->getActionName()
            )) . ') => ' . $IsAllowed . '<br>' . PHP_EOL;
            die('#' . __LINE__ . ' ' . __FILE__ . ' role: ' . $role );
        }
        
        if (!$IsAllowed) {
            if ($role == 'guest') {
                $request->setModuleName('default');
                $request->setControllerName( 'user');
                $request->setActionName( 'login');
            } else {
                $request->setModuleName( 'default');
                $request->setControllerName('index');
                $request->setActionName('index');
            }
        }
    }
}
