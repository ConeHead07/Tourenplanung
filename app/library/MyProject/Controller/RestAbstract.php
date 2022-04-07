<?php
/**
 * Created by PhpStorm.
 * User: f.barthold
 * Date: 14.05.2019
 * Time: 09:11
 */

class MyProject_Controller_RestAbstract extends Zend_Controller_Action
{


    protected function sendJSON(bool $success, string $error, array $aData = [], string $message = '')
    {

        $responseData = [
            'success' => $success,
            'error' => $error,
            'data'=> $aData,
        ];

        if ($message) {
            $responseData['message'] = $message;
        }

        $json = json_encode( $responseData, JSON_PRETTY_PRINT);

        header('Content-Type: application/json; charset=UTF-8');
        header("Content-Length: ". strlen($json));
        echo $json;

        exit;
    }

    protected function sendJSONSuccess(array $aData = [], string $message = '')
    {
        $this->sendJSON(true, '', $aData);
    }

    protected function sendJSONError(string $error, array $aData = [])
    {
        $this->sendJSON(false, $error, $aData);
    }

}