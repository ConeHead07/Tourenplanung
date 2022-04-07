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

        $hdAccept = $request->getHeader('accept');
        
        // ? 'user' : 'guest';
//        Zend_Auth::getInstance()->clearIdentity();
        $identity = Zend_Auth::getInstance()->getIdentity();
//        $identity = null;
//        print_r(['<pre>', '#'=>__LINE__, __FILE__, __METHOD__, 'identity'=>$identity, '</pre>']);
//        exit;
        $role = (null == $identity) ? 'guest' : $identity->user_role;
        
        $module = $this->getRequest()->getModuleName();
        $prefixModule = ($module != 'default') ? $module.'_' : '';

        $controllerName = $prefixModule . $request->getControllerName();
        $actionName = $request->getActionName();
        
        $IsAllowed =
            ($controllerName == 'user' && $actionName == 'logout')
            || Zend_Registry::get('acl')->isAllowed(
            $role, 
            // Fuehrte zu Fehler: $prefixModule.$role,
            $controllerName,
            $actionName
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

            if (stripos($hdAccept, 'json') !== false) {
                header("Content-Type: text/json");
                echo json_encode([
                    'type' => 'error',
                    'success' => false,
                    'error' => 'Zugriff auf angeforderte Ressource wurde abgelehnt!',
                    'data' => [],
                ]);
                exit;
            }

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
