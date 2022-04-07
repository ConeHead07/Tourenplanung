<?php

class Touren_AttachmentsController extends Zend_Controller_Action {

    protected $_tour_id = 0;
    protected $_title = '';
    protected $_TABLE;
    protected $db;
    protected $user;
    protected $MConf;
    protected $dropError;
    protected $_confAttachments = array();

    /**
     * 
     * @var Model_TourenDispoAttachments 
     */
    protected $_modelAttachments = null;

    /**
     *
     * @var Model_TourenDispoVorgaenge
     */
    protected $_modelVorgaenge = null;

    /**
     * 
     * @var Model_Db_TourenDispoAttachments 
     */
    protected $_storageAttachments = null;
    protected $_tblAttachments = '';
    protected $_tblidAttachments = '';

    /**
     *
     * @var $this->_modelAttachments->delete($dokid);
     */
    protected $_db = null;
    protected $_mime_types = array();

    function init() {
        $r = $this->getRequest();
        $this->_db = Zend_Registry::get('db');
        $this->_tour_id = $r->getParam('tour_id', 0);
        
        if (!$this->_tour_id)
            die("<strong>Es wurde keine AntragsID &uuml;bergeben!</strong> <br>
<br><strong>Wie erhalte ich eine AntragsID?</strong><br>
Eine ID erhält Ihr Antrag mit dem ersten erfolgreichen (Zwischen-)Speichern
mit mind. einem vollständigen Mitarbeitereintrag in der Umzugsliste.<br>
<br>
Anschlie&szlig;end k&ouml;nnen Sie Dateien hinzuf&uuml;gen!<br>\n");
        
        $this->_title = $r->getParam('titel', '');
        $this->_modelAttachments = new Model_TourenDispoAttachments();
        $this->_modelVorgaenge = new Model_TourenDispoVorgaenge();
        $this->_storageAttachments = $this->_modelAttachments->getStorage();
        $this->_tblAttachments = $this->_storageAttachments->info(Zend_Db_Table::NAME);
        $this->_tblidAttachments = $this->_storageAttachments->info(Zend_Db_Table::PRIMARY);
        
        $this->_confAttachments = $this->getInvokeArg('bootstrap')->getOption('attachments');
		$this->_confAttachments = array(
			"dir" => APPLICATION_PATH . "/data/attachments/",
			"max_upload_size" => 52428800, //; 50MB
		);
//        Zend_Debug::dump($this->_confAttachments, '$this->_confAttachments');

        $this->_mime_types = $mime_types_map = array(
            'jpg' => 'image/jpeg',
            'gif' => 'image/gif',
            'png' => 'image/png',
            'pdf' => 'application/pdf'
        );
    }
    
    function listAction() {
        $r = $this->getRequest();
        $ofld = $r->getParam('ofld', '');
        $odir = $r->getParam('odir', '');

        
        die( $this->_modelAttachments->getTableList($this->_tour_id, array(
            'ofld' => $ofld, 
            'odir' => $odir, 
            'filepath' => $this->getFrontController()->getBaseUrl() . '/touren/attachments/file/tour_id/' . $this->_tour_id . '', 
            'droppath' => $this->getFrontController()->getBaseUrl() . '/touren/attachments/drop/tour_id/' . $this->_tour_id . '', 
            'listpath' => $this->getFrontController()->getBaseUrl() . '/touren/attachments/list/tour_id/' . $this->_tour_id . ''))
        );
    }
    
    function dropAction() {
        $r = $this->getRequest();
        $drop = $r->getParam('drop', ''); // dokid
        try {
            $this->_modelAttachments->drop($this->_tour_id, $drop);
            $this->_helper->json( array('success'=>1) );
        } catch(Exception $e) {
            $uploadError = ($uploadError ? "<br>\n" : "") . $e->getMessage();
            $this->_helper->json( array('success' => 0, 'error' => $e->getMessage() ) );
        }
    }

    function indexAction() {
        $r = $this->getRequest();

        /*         * ***************************** START INDEX ACTION ************************ */
        set_time_limit(600);
        error_reporting(E_ALL);
//echo ini_get("error_reporting");

        $isAdmin = (strpos(MyProject_Auth_Adapter::getUserRole(), "admin") !== false);
        $tour_id = $r->getParam('tour_id', '');
        $drop = $r->getParam('drop', '');
        $titel = $r->getParam('titel', '');
        $ofld = $r->getParam('ofld', '');
        $odir = $r->getParam('odir', '');
        $ajaxCallBack = $r->getParam('ajaxcallback', '');
        $this->view->out = '';

        $uploadError = "";
        $uploadMsg = "";
        
        if (isset($_FILES) && isset($_FILES["uploadfile"])) {
            try {
                $dokid = $this->_modelAttachments->save_upload($tour_id, $isAdmin);
                if ($dokid) {
                    $uploadMsg = "Hochgeladene Datei '" . $_FILES['uploadfile']['name'] . "' wurde gespeichert!";
                }
            } catch(Exception $e) {
                $uploadError = ($uploadError ? "<br>\n" : "") . $e->getMessage();
            }
        }
        if (!empty($drop)) {
            try {
                $this->_modelAttachments->drop($tour_id, $drop);
            } catch(Exception $e) {
                $uploadError = ($uploadError ? "<br>\n" : "") . $e->getMessage();
            }
        }
        

        if ($ajaxCallBack) {
            die( 
                '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">' . "\n"
                .'<html>' . "\n"
                .'<head>' . "\n"
                .'<title>Upload</title>' . "\n"
                .'<script>'
                . $ajaxCallBack . '(' . json_encode( array(
                    'msg'=> utf8_encode($uploadMsg), 
                    'error' => utf8_encode($uploadError),
                  )) 
                . ')' 
                . "\n"
                . '</script>' . "\n"
                . '</head>'   . "\n"
                . '<body>'    . "\n"
                . '</body>'   . "\n"
                . '</html>'   . "\n");
                    
        } else {

            $this->view->out.=  
                '<style>' . "\n"
                .'.upMsg { font-size:11px; color:#228b22; font-family:Arial,Helvetica,sans-serif;}' . "\n"
                .'.upErr { font-size:11px; color:#f00; font-family:Arial,Helvetica,sans-serif;}' . "\n"
                .'</style><link rel="STYLESHEET" type="text/css" href="css/tablelisting.css">' . "\n"
                .'<script>' . "\n"
                .'var XFb = {' . "\n" 
                .' listbox: "#attachments_list",' . "\n"
                
                .' uploadFinished: function(o){ ' . "\n"
                .'  XFb.showLoadingBar(0);' . "\n"
                .'  if (arguments.length && typeof(arguments[0])=="object") {' . "\n"
                .'   var m = "";' . "\n"
                .'   if ("msg" in o && o.msg) m= o.msg + "\n";' . "\n"
                .'   if ("error" in o && o.error) m+= o.error;' . "\n"
                .'   if ( $("#upload-message").length) {' . "\n"
                .'     $("#upload-message").text(m);' . "\n"
                .'   }' . "\n"
                .'   else if ("error" in o && o.error) alert(o.error);' . "\n"
                .'  }' . "\n"
                .'  XFb.reloadAttachments();' . "\n"
                .' },' . "\n"
                
                .' reloadAttachments: function() {' . "\n" 
                .'  var $a = $(XFb.listbox);' . "\n"                   
                .'  XFb.queryAttachments($a.attr("data-query") );' . "\n"
                .' },'
                    
                .' queryAttachments: function(query) {' . "\n"
                .'  var $a = $(XFb.listbox);' . "\n"
                .'  XFb.showLoadingBar(1);' . "\n"
                .'  $a.load( $a.attr("data-baseurl") + query, function() {' . "\n"
                .'   XFb.bindListEvents();' . "\n"
                .'   XFb.showLoadingBar(0);' . "\n"
                .'  });' . "\n"
                .' },' . "\n"
                    
                .' bindListEvents: function() {' . "\n"
                .'  var $a = $(XFb.listbox);' . "\n"
                .'  $a.find("thead a").click(function(e) {' . "\n"
                .'   e.preventDefault();' . "\n"
                .'   $a.attr("data-query", $(this).attr("data-query") );' . "\n"
                .'   XFb.queryAttachments( $(this).attr("data-query") );' . "\n"
                .'  });' . "\n"
                
                .'  $a.find("tbody a.list-action").click(function(e) {' . "\n"
                .'   e.preventDefault();' . "\n"
                .'   $.getJSON( $(this).attr("href"), function(data) {' . "\n"
                .'    if (arguments.length && typeof(arguments[0])=="object") {' . "\n"
                .'     var m = "";' . "\n"
                .'     if ( "msg" in data && data.msg ) m+= data.msg + "\n";' . "\n"
                .'     if ( "error" in data && data.error) m+= data.error + "\n";' . "\n"
                .'     if ( m ) {' . "\n"
                .'         if ( $("#attachments-message").length) {' . "\n"
                .'         $("#attachments-message").text(m);' . "\n"
                .'     }' . "\n"
                .'     else alert(m);' . "\n"
                .'     }' . "\n"
                .'    }' . "\n"
                .'    XFb.reloadAttachments();' . "\n"
                .'   });' . "\n"
                .'  });' . "\n"
                .' },' . "\n"
                    
                .' showLoadingBar: function(on) {' . "\n"
                .'  if (!$("#attachments-loading-bar img").length) {' . "\n"
                .'    $("#attachments-loading-bar").append( $("<img/>").attr("src", "' .$this->getFrontController()->getBaseUrl() .'/img/wheel01.gif") );' . "\n"
                .'  }' . "\n"
                .'  if (on) $("#attachments-loading-bar").show();' . "\n"
                .'  else $("#attachments-loading-bar").hide();' . "\n"
                .' },' . "\n"
                    
                .' submitUpload: function(on) {' . "\n"
                .'  $("#frmUpload")[0].submit();' . "\n"
                .'  $("#frmUpload")[0].reset();' . "\n"
                .'  XFb.showLoadingBar(1);' . "\n"
                .' }' . "\n"
                    
                .'};' . "\n"
                .'</script>' . "\n"
                .'</head>' . "\n"
                .'<body>' . "\n"
                . $this->_modelAttachments->getUploadForm($tour_id, array(
                    'action' => $this->getFrontController()->getBaseUrl() . '/touren/attachments/index/?ajaxcallback=parent.XFb.uploadFinished'
                ));

            if ($uploadError)
                $this->view->out.= "<div class=\"upErr\">" . $uploadError . "</div>\n";
            if ($uploadMsg)
                $this->view->out.= "<div class=\"upMsg\">" . $uploadMsg . "</div>\n";

            $baseUrl = $this->getFrontController()->getBaseUrl();
            $this->view->out.= 
                '<div id="attachments_list" data-baseurl="' . $baseUrl . '/touren/attachments/list/tour_id/' . $tour_id . '' . '" data-query="">'
                . $this->_modelAttachments->getTableList($tour_id, array(
                    'ofld' => $ofld, 
                    'odir' => $odir,
                    'filepath' => $baseUrl . '/touren/attachments/file/tour_id/' . $tour_id,
                    'droppath' => $baseUrl . '/touren/attachments/drop/tour_id/' . $tour_id,
                    'listpath' => $baseUrl . '/touren/attachments/list/tour_id/' . $tour_id )
                   )
                . '</div>'
                . '<script>XFb.bindListEvents()</script>';
            
            //$this->view->out.= '<iframe name="attachments_list" src="' . $this->getFrontController()->getBaseUrl() . '/touren/attachments/list/tour_id/' . $tour_id . '/" style="width:100%;heigth:200px;"></iframe>';
        }
        /*         * ***************************** ENDE  INDEX ACTION ************************ */

    }
    
    function fileAction()
    {
        $this->_helper->layout->disableLayout();
        $r = $this->getRequest();
        $dokid = $r->getParam('dokid', 0);
        
        
        $f = $this->_modelAttachments->fetchEntry($dokid);
        
        $path = $this->_confAttachments['dir'] . '/' . $f['dok_datei'];
        $type = strtolower($f['dok_type']);
        $contentType = (isset($this->_mime_types[$type])) ? $this->_mime_types[$type] : 'application/octet-stream';
        
		if (0) die(
			"<pre>"
		   ."dokid: $dokid\n"
		   ."path: $path\n"
		   ."type: $type\n"
		   ."contentType: $contentType\n"
		   ."filesize($path): " . filesize($path) . "\n"
		   ."</pre>"
		);
        header('Content-Description: File Transfer');
        header('Content-Type: ' . $contentType);
        header('Content-Disposition: attachment; filename='.basename($f['dok_datei']));
        header('Content-Transfer-Encoding: binary');
        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');
        header('Content-Length: ' . filesize($path));
        
        readfile($this->_confAttachments['dir'] . '/' . $f['dok_datei']);
        die();
    }

}
