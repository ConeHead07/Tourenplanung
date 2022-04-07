<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Acl
 *
 * @author rybka
 */
class MyProject_Acl extends Zend_Acl {

    //put your code here

    public function __construct() {
        $config = new Zend_Config_Ini(APPLICATION_PATH . '/configs/acl.ini');
//      Zend_Debug::dump($config->toArray());

        foreach ($config->roles as $role) {
            $this->addRole($role);
        }

        foreach ($config->resources as $resource) {
            $this->addResource($resource);
        }

        foreach ($config->rules as $function => $rule) {
            foreach ($rule as $role => $rule2) {
                if (is_object($rule2)) {
                    foreach ($rule2 as $resource => $rule3) {
                        if ('all' == $rule3) {
                            $this->$function($role, $resource);
                        } else {
                            $this->$function($role, $resource, $rule3->toArray());
                        }
                    }
                } else {
                    if ($rule2 == 'all') $this->$function($role);
                    else $this->$function($role, NULL, $rule2);
                }
            }
        }
    }

    protected function __clone() {
        ;
    }

}

?>
