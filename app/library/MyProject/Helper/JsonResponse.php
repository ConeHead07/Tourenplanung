<?php
/**
 * Created by PhpStorm.
 * User: f.barthold
 * Date: 21.09.2018
 * Time: 16:39
 */

class MyProject_Helper_JsonResponse extends Zend_Controller_Action_Helper_Abstract
{
    /**
     * Constructor: initialize plugin loader     *
     * @return void
     */
    public function __construct()
    {
        $this->pluginLoader = new Zend_Loader_PluginLoader();
    }

    public function send(bool $success, string $message = '', string $error = '', array $data = [])
    {

        $r = $this->getResponse();
        $r->setHeader('Content-Type', 'application/json; charset=UTF-8', true);

        /** @var Zend_Controller_Action_Helper_Json $jsonResponse */
        $jsonResponse = $this->getActionController()->getHelper('json');
        $jsonResponse->direct([
            'type' => $success ? 'success' : 'error',
            'success' => $success,
            'msg' => $message,
            'error' => $error,
            'data' => $data
        ]);

    }

    public function ok(string  $message = '', array $data = [])
    {
        $this->send(true, $message, '', $data);
    }

    public function error(string  $error = '', array $data = [])
    {
        $this->send(true, '', $error, $data);
    }

    public function direct(bool $success, string $message = '', array $data = [])
    {
        if ($success) {
            $this->ok($message, $data);
        } else {
            $this->error($message, $data);
        }
    }

}