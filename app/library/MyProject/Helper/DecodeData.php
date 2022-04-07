<?php

/**
 * Description of Test
 *
 * @author rybka
 */
class MyProject_Helper_DecodeData extends Zend_Controller_Action_Helper_Abstract 
{
 
    /**
     * Constructor: initialize plugin loader     *
     * @return void
     */
    public function __construct()
    {
        $this->pluginLoader = new Zend_Loader_PluginLoader();
    }
    
    public static function getUtf8DecodedData($data, Zend_Controller_Request_Http $request = null)
    {
        $IsUtf8 = false;
        if ($request) {
            $encType = $request->getHeader('Content-Type');
            if ($encType) {
                $IsUtf8 = preg_match( '/utf-?8/i', $encType);
            } 
        }
        // Check and Try to Fit Charset-Encoding
        mb_detect_order( array(
            'UTF-8',
            'ISO-8859-1',
            'ASCII',
        ));

        foreach($data as $k => $v) {
            if (is_array($v)) $data[$k] = self::getUtf8DecodedData($v, $request);
            elseif (is_string($v)) {
                if (!$IsUtf8) {
                    $current_encoding = mb_detect_encoding($v, mb_detect_order() );
                    $data[$k] = @iconv($current_encoding, 'ISO-8859-1', $v);
                } else {
                    $data[$k] = utf8_decode($v);
                }
            }                
        }
        return $data;
    }
    
    /**
     * Automatische Dekodierung utf8-kodierter Daten in multidim-Arrays
     * @param  array $data
     * @param Zend_Controller_Request_Http $request
     * @return array
     */
    public function direct($data, Zend_Controller_Request_Http $request = null)
    {
        return self::getUtf8DecodedData($data, $request);
    }
}

?>
