<?php

class Model_TourenDispoAttachments extends MyProject_Model_Database
{
    protected $_storageName = 'tourenDispoAttachments';
    
    /**
     * @var Zend_Config_Ini
     */
    protected $_conf = null;
    
    /**
     *
     * @var Zend_Db_Adapter_Mysqli
     */
    protected $_db = null;
    
    
    public function __construct() {
        $this->_conf = new Zend_Config_Ini(APPLICATION_PATH . '/configs/attachments.ini', 'attachments');
        $this->_db = $this->getStorage()->getAdapter();
        
    }
        
    function getOrderLink($chckfld, $ofld, $odir) {
        if ($ofld != $ofld || $odir == "DESC")
            return "/ofld/$chckfld/odir/ASC";
        return "/ofld/$chckfld/odir/DESC";
    }
    
    function format_file_size($bytes) {
        if ($bytes > (1024 * 1024 * 1024)) {
            return round($bytes / (1024 * 1024 * 1024), 1) . " GB";
        } elseif ($bytes > (1024 * 1024)) {
            return round($bytes / (1024 * 1024), 1) . " MB";
        } elseif ($bytes > (1024)) {
            return round($bytes / (1024), 1) . " KB";
        } else
            return $bytes . " Bytes";
    }

    function drop($tour_id, $dokid) {

        $row = $this->fetchEntry($dokid);

        if (!empty($row["dokid"])) {
            if ($row["tour_id"] != $tour_id) {
                throw new Exception("Ausgewähltes Attachement ist nicht dem Vorgang zugeordnet!<br>\n");
            }
            if (!file_exists($this->_conf->dir . $row["dok_datei"])) {
                throw new Exception("Datei " . $row["dok_datei"] . " existiert nicht!<br>\n");
            }
            @unlink($this->_conf->dir . $row["dok_datei"]);
            $this->delete($dokid);

            return true;
        } else {
            throw new Exception("Dateianhang mit der ID:" . $dokid . " wurde nicht gefunden!<br>\n");
        }
        return false;
    }

    function save_attachment($tour_id, $file, $size, $title) {

        $aFileInfo = pathinfo($file);

        $this->getStorage()->delete($this->_db->quoteInto('dok_datei LIKE ?', $file));

        $aData = array(
            'tour_id' => $tour_id,
            'oeffentlich' => 'Ja',
            'typ' => 'Datei',
            'dok_datei' => $file,
            'titel' => $title,
            'dok_groesse' => $size,
            'dok_type' => $aFileInfo["extension"],
            'created' => new Zend_Db_Expr('NOW()'),
            'createdby' => MyProject_Auth_Adapter::getUserId(),
        );

        $record = $this->getStorage()->createRow(
                $aData
        );

        print_r(['<pre>', $aData, $record->toArray(), '</pre>']);

        $record->save();
        return $record->dokid;
    }
    
    function getUploadForm($tour_id, $opts = array()) {
        $o = array_merge( array(
            'target' => 'file_upload_target',
            'hidden_frame' => true,
            'action' => './?ajaxcallback=top.Fb.uploadFinished',
            'form_name' => 'frmUpload',
            'form_id' => 'frmUpload',
            'on_submit' => '\'if ("XFb" in window && "showLoadingBar" in XFb && typeof(XFb.showLoadingBar) == "function") { XFb.showLoadingBar(1,"")}\'',
            'on_change' => '\'if ("XFb" in window && "submitUpload" in XFb && typeof(XFb.submitUpload) == "function")   { XFb.submitUpload()}\'',
            'max_upload_size' => $this->_conf->max_upload_size,
            'showSubmit' => false
            
        ), $opts);
        return 
        '<form id="' . $o['form_id'] . '" name="' . $o['form_name'] . '" onsubmit=' . $o['on_submit'] . ' target="' . $o['target'] . '"'
        .' action="' . $o['action'] . '" method="post" enctype="multipart/form-data">' . "\n"
        .'<input type="hidden" name="MAX_FILE_SIZE" value="' . $o['max_upload_size'] . '"><!-- Angabe in Bytes; -->' . "\n"
        .'<input type="File" name="uploadfile" onchange='. $o['on_change'] . '>' . "\n"
        .'<input type="hidden" name="tour_id" value="' . $tour_id . '">' . "\n"
        .($o['showSubmit'] ? '<input type="submit" value="Datei senden">' . "\n" : '')
        .'</form>' . "\n"
        .'<div id="attachments-status" style="min-height:20px;"><div style="float:left;width:20px;height:20px;"><span id="attachments-loading-bar"></span></div>'
        .'<div id="upload-message"></div><div id="attachments-message"></div></div>'
        .($o['hidden_frame'] ? '<iframe name="file_upload_target" style="display:none;height:1px;width:1px;"></iframe>' : '');
    }

    public function getList($tour_id, $opts = array())
    {
        if (!$tour_id) {
            return [];
        }

        $aOrderFields = array(
            "dok_datei" => array("field" => "dok_datei", "defOrder" => "ASC"),
            "dok_groesse" => array("field" => "dok_groesse", "defOrder" => "ASC"),
            "created" => array("field" => "created", "defOrder" => "DESC"),
        );

        $ofld = (isset($opts['ofld']) ? $opts['ofld'] : '');
        $odir = (isset($opts['odir']) ? $opts['odir'] : '');

        if (isset($aOrderFields[$ofld])) {
            if (!in_array(strtoupper($odir), array("ASC", "DESC")))
                $odir = "";
            $OrderBy = $aOrderFields[$ofld]["field"] . " " . (empty($odir) ? $aOrderFields[$ofld]["defOrder"] : $odir);
        } else {
            $ofld = "created";
            $odir = "DESC";
            $OrderBy = "$ofld $odir";
        }

        return $this->fetchEntries(array(
            'where' => $this->_db->quoteInto('tour_id = ?', $tour_id, 'int'),
            'order' => $OrderBy
        ));

    }
    function getTableList($tour_id, $opts = array()) 
    {

        $aOrderFields = array(
            "dok_datei" => array("field" => "dok_datei", "defOrder" => "ASC"),
            "dok_groesse" => array("field" => "dok_groesse", "defOrder" => "ASC"),
            "created" => array("field" => "created", "defOrder" => "DESC"),
        );
        
        $ofld = (isset($opts['ofld']) ? $opts['ofld'] : '');
        $odir = (isset($opts['odir']) ? $opts['odir'] : '');
        $filepath = (isset($opts['filepath']) ? $opts['filepath'] : '');
        $droppath = (isset($opts['droppath']) ? $opts['droppath'] : '');
        $listpath = (isset($opts['listpath']) ? $opts['listpath'] : '');
        $removable = (isset($opts['removable']) ? $opts['removable'] : true);
        $sortable = (isset($opts['sortable']) ? $opts['sortable'] : true);
        
        
        if (!$tour_id)
            return "";



        if (isset($aOrderFields[$ofld])) {
            if (!in_array(strtoupper($odir), array("ASC", "DESC")))
                $odir = "";
            $OrderBy = $aOrderFields[$ofld]["field"] . " " . (empty($odir) ? $aOrderFields[$ofld]["defOrder"] : $odir);
        } else {
            $ofld = "created";
            $odir = "DESC";
            $OrderBy = "$ofld $odir";
        }

        $rows = $this->fetchEntries(array(
            'where' => $this->_db->quoteInto('tour_id = ?', $tour_id, 'int'),
            'order' => $OrderBy
                ));

        if ($removable) {
            $dropLink = '<a class="list-action" href="' . $droppath . "/drop/{dokid}/ofld/$ofld/odir/$odir\" style=\"text-decoration:none;border:0;\">";
            $dropLink .= '<span title="Loeschen" class="ui-state-default ui-corner-all" style="display:inline-block;"><span class="ui-icon ui-icon-trash"></span></span>';
            //$dropLink.= '<img src="images/status_storniert.png" style="text-decoration:none;border:0;" border=0 align="absmiddle" width="16" height="16">';
            $dropLink .= ' L&ouml;schen';
            $dropLink .= "</a>";
        } else {
            $dropLink = '';
        }

        $fileList = "";

        if (is_array($rows) && count($rows))
            for ($i = 0; $i < count($rows); $i++) {
                $row = $rows[$i];
                $fileList.= "<tr>";
                $fileList.= "<td align=right>" . ($i + 1) . "</td>\n";
                $fileList.= "<td><a href=\"" . $filepath . '/dokid/' . $row["dokid"] . '/' . $row["dok_datei"] . "\">" . $row["dok_datei"] . "</a></td>\n";
                $fileList.= "<td>" . $this->format_file_size($row["dok_groesse"]) . "</td>\n";
                $fileList.= "<td>" . $row["created"] . "</td>\n";
                if ($removable) {
                    $fileList .= "<td>" . str_replace("{dokid}", $row["dokid"], $dropLink) . " </td>\n";
                }
                $fileList.= "</tr>\n";
            }

        if ($fileList) {
            $fileListHd = "<thead>";
            $fileListHd.= "<tr>";
            $fileListHd.= "<td align=right>#</td>\n";
            if ($sortable) {
                $fileListHd .= "<td><a href=\"" . $listpath . $this->getOrderLink("dok_datei", $ofld, $odir) . "\" data-query=\"" . $this->getOrderLink("dok_datei", $ofld, $odir) . "\">Datei</a></td>\n";
                $fileListHd .= "<td><a href=\"" . $listpath . $this->getOrderLink("dok_groesse", $ofld, $odir) . "\" data-query=\"" . $this->getOrderLink("dok_groesse", $ofld, $odir) . "\">Gr&ouml;&szlig;e</a></td>\n";
                $fileListHd .= "<td><a href=\"" . $listpath . $this->getOrderLink("created", $ofld, $odir) . "\" data-query=\"" . $this->getOrderLink("created", $ofld, $odir) . "\">Upload vom</a></td>\n";
            } else {
                $fileListHd .= "<td>Dateianhänge</td>\n";
                $fileListHd .= "<td>Gr&ouml;&szlig;e</td>\n";
                $fileListHd .= "<td>Datum</td>\n";
            }
            if ($removable) {
                $fileListHd.= "<td>L&ouml;schen</td>\n";
            }
            $fileListHd.= "</tr>\n";
            $fileListHd.= "</thead>\n";
            
            return '<table class="tblList">' . "\n"
                 . $fileListHd
                 . '<tbody>' . $fileList . '</tbody>' . "\n" . '</table>' . "\n";
        }
        return "";
    }

    function save_upload($tour_id, $allowOverwrite) {
        $max_size = $this->_conf->max_upload_size;
        $dst_dir  = $this->_conf->dir;
        $title = '';

        $aUploadErrCodes[0] = "";
        $aUploadErrCodes[1] = 'Dateigroesse überschreitet Servervorgaben!';
        $aUploadErrCodes[2] = 'Datei ist zu groß. MAX_FILE_SIZE (' . $this->format_file_size($_POST["MAX_FILE_SIZE"]) . ') wurde überschritten!';
        $aUploadErrCodes[3] = 'Datei wurde unvollständig übertragen!';
        $aUploadErrCodes[4] = 'Es wurde keine Datei hochgeladen!';

        if ($tour_id) {
            if (isset($_FILES["uploadfile"]) && !$_FILES["uploadfile"]["error"]) {
                $Im = &$_FILES["uploadfile"];
                if ($Im["size"] <= $max_size) {
                    $saveas = $dst_dir . "tour_id_" . $tour_id . "_" . $_FILES['uploadfile']['name'];
                    if (file_exists($saveas) && !$allowOverwrite) {
                        throw new Exception("Fehler: Eine gleichnamige Datei existiert bereits. Nur Administratoren d&uuml;rfen Dateien &uuml;berschreiben!");
                        return false;
                    }
                    @unlink($saveas);
                    move_uploaded_file($_FILES['uploadfile']['tmp_name'], $saveas);

                    $dokid = $this->save_attachment($tour_id, basename($saveas), filesize($saveas), $title);
                    if (!$dokid) {
                        throw new Exception("DB-Fehler: Hochgeladene Datei konnte nicht gespeichert werden!");
                    } else {
                        return $dokid;
                    }
                } else {
                    throw new Exception("Hochgeladene Datei ist zu groß!\\nBitte nicht groesser als " . $this->format_file_size($max_size) . ".");
                }
            } else {
                if ($_FILES["uploadfile"]["error"])
                    throw new Exception($aUploadErrCodes[$_FILES["uploadfile"]["error"]]);
            }
        } else {
            throw new Exception("Unauthorisierter Upload!");
        }
        return false;
    }
}
