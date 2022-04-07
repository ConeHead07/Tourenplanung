<?php
/**
 * Created by PhpStorm.
 * User: f.barthold
 * Date: 14.05.2019
 * Time: 09:11
 */

class MyProject_Controller_RestAbstract extends Zend_Controller_Action
{

    protected function sendJson(bool $success, string $error, array $aData = [], string $message = '')
    {

        $responseData = [
            'type' => $success ? 'success' : 'error',
            'success' => $success,
            'error' => $error,
            'data'=> $aData,
        ];

        if ($message) {
            $responseData['msg'] = $message;
        }

        $json = json_encode( $responseData, JSON_PRETTY_PRINT);

        header('Content-Type: application/json; charset=UTF-8');
        header("Content-Length: ". strlen($json));
        echo $json;

        exit;
    }

    protected function sendRawJson($anyData)
    {
        $json = json_encode( $anyData, JSON_PRETTY_PRINT);

        header('Content-Type: application/json; charset=UTF-8');
        header("Content-Length: ". strlen($json));
        echo $json;

        exit;
    }

    protected function sendJsonData(array $data, string $msg = '') {
        $this->sendJson(true, '', $data, $msg);
    }

    protected function sendJsonSuccess(string $msg, array $data = []) {
        $this->sendJson(true, '', $data, $msg);
    }

    protected function sendJsonSuccessID($id, string $msg = '', array $data = [])
    {
        $this->sendRawJson([
            'type' => 'success',
            'success' => true,
            'msg' => $msg,
            'id' => $id,
            'data' => $data,
        ]);
    }

    protected function sendJsonError(string $error, array $data = []) {
        $this->sendJson(false, $error, $data, '');
    }

    protected function _require( $mixedExpression, string $messageOnFalse, string $response = 'Exception') {
        $isCallable = is_callable($mixedExpression);
        $bResultOk = (bool)($isCallable ? $mixedExpression() : !empty($mixedExpression));

        if (!$bResultOk) {
            // No Break required
            switch ($response) {
                case 'Exception':
                    throw new Exception($messageOnFalse);

                case 'json':
                    ob_end_clean();
                    $this->sendJsonError($messageOnFalse);

                default:
                    ob_end_clean();
                    echo $messageOnFalse;
                    flush();
                    exit;
            }
        }
    }

}