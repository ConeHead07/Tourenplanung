<?php

/** 
 * Column Chooser: With this method we can reorder columns and set visible and hidden columns in the grid.
 * @abstract
 * Used jQuery Widgets and Plugins:<br>
 * <li> jQuery UI core
 * <li> jQuery UI sortable widget.<br>
 * 
 * Calling convetions:<br>
 * <pre>
 * jQuery("#list").columnChooser(options);
 * or using the new API
 * jQuery("#list").jqGrid('columnChooser', options);
 * </pre>
 * where options is a object with properties listed in the methods of this class.<br>
 *
 * In order to use this method the jQuery multiselect plugin should be loaded before jqGrid<br>
 *
 * After calling this a modal dialog will appear where the user can reorder columns 
 * and set which of them can be visible and which of them can be hidden. 
 * 
 * @author rybka
 * @link http://www.trirand.com/jqgridwiki/lib/exe/fetch.php?media=wiki:columnchooser.jpg
 * @link http://www.trirand.com/jqgridwiki/doku.php?id=wiki:jquery_ui_methods
 */
class JqGridColumnChooserOptions extends JqGridBaseObject
{
        protected $_gridId = '';
        
	public function getGridId() {
            return $this->_gridId;
        }

        public function setGridId($gridId) {
            $this->_gridId = $gridId;
        }

        public function __construct($properties = NULL)
	{
		$this->set_done_default();
		parent::setProperties($properties);
	}
	
	/**
	 *
	 * @abstract Title of the modal dialog
	 * Default See $.jgrid.col.title in language file
	 * @param string $title
	 * @return JgGridColumnChooser
	 */
	public function set_title($title)
	{
		$this->title = $title;
		return $this;
	}

	/**
	 *
	 * @abstract Set the width of the dialog in pixels
	 * Default 420
	 * @param number $width
	 * @return JgGridColumnChooser
	 */
	public function set_width($width)
	{
		$this->width = $width;
		return $this;
	}

	/**
	 *
	 * @abstract Set the height of the dialog in pixels
	 * Default 240
	 * @param number $height
	 * @return JgGridColumnChooser
	 */
	public function set_height($height)
	{
		$this->height = $height;
		return $this;
	}

	/**
	 *
	 * @abstract Class which will be added to the selector where the selects are build
	 * Default null
	 * @param string $classname
	 * @return JgGridColumnChooser
	 */
	public function set_classname($classname)
	{
		$this->classname = $classname;
		return $this;
	}
	
	/**
	* Function done will be called when the user press Ok button, to process the changes
	* @abstract Wird intern vom constructor aufgerufen
	* In the current implementation we cal remapColumns method in order to reorder the columns
	* Default
	* @return JgGridColumnChooser
	*/
	public function set_done_default() {
		$this->done = new JsFunction("function (perm) {
			if (perm) {
				// \"OK\" button are clicked
				this.jqGrid(\"remapColumns\", perm, true);
				// the grid width is probably changed co we can get new width
				// and adjust the width of other elements on the page
				var gwdth = this.jqGrid(\"getGridParam\",\"width\");
				this.jqGrid(\"setGridWidth\",gwdth);
			} else {
				// we can do some action in case of \"Cancel\" button clicked
			}
		}");
		return $this;	
	}

	/**
	 * Function which will be called when the user press Ok button
	 * @abstract Function which will be called when the user press Ok button.
	 * In the current implementation we cal remapColumns method in order to reorder the columns
	 * Default
	 * @param JsFunction $done
	 * @return JgGridColumnChooser
	 */
	public function set_done($done)
	{
		$this->done = ($done instanceof JsFunction) ? $done : (new JsFunction($done));
		return $this;
	}

	/**
	 *
	 * @abstract msel is either the name of a ui widget class that extends a multiselect, or a function that supports creating a multiselect object (with no argument, or when passed an object), and destroying it (when passed the string "destroy")
	 * Default multiselect
	 * @param mixed $msel
	 * @return JgGridColumnChooser
	 */
	public function set_msel($msel)
	{
		$this->msel = $msel;
		return $this;
	}

	/**
	 *
	 * @abstract dlog is either the name of a ui widget class that behaves in a dialog-like way, or a function, that supports creating a dialog (when passed dlog_opts) or destroying a dialog (when passed the string "destroy")
	 * Default dialog
	 * @param mixed $dlog
	 * @return JgGridColumnChooser
	 */
	public function set_dlog($dlog)
	{
		$this->dlog = $dlog;
		return $this;
	}

	/**
	 * dlog_opts is either an option object to be passed to "dlog", or (more likely) a
	 * @abstract dlog_opts is either an option object to be passed to "dlog", or (more likely) a function that creates the options object.
	 * The default produces a suitable options object for ui.dialog
	 * Default
	 * @param mixed $dlog_opts
	 * @return JgGridColumnChooser
	 */
	public function set_dlog_opts($dlog_opts)
	{
		$this->dlog_opts = $dlog_opts;
		return $this;
	}

	/**
	 * Function to cleanup the dialog, and select
	 * @abstract Function to cleanup the dialog, and select.
	 * Also calls the done function with no permutation (to indicate that the columnChooser was aborted
	 * Default
	 * @param function $cleanup
	 * @return JgGridColumnChooser
	 */
	public function set_cleanup($cleanup)
	{
		$this->cleanup = $cleanup;
		return $this;
	}
        
        /**
         * Rückgabefunktion kann als Wert für die NavButton-Eigenschaft
         * onClickButton gesetzt werden, um einen Col-Chooser-Button
         * im Panel zu integrieren
         * @param string $GridId
         * @return string Json-Literal
         */
        public function renderMethod($GridId = '')
        {
            $gId = ($GridId ? $GridId : $this->_gridId);
            if (!$gId) {
                throw new JqGridException(
                         'Es wurde keine GridId für Methoden-Generierung angelegt! '
                        .'Nutzen Sie hierfür die Methode ' . __CLASS__ . '->setGridID() oder '
                        .'übergeben Sie die Id als Parameter der Methode ' . __METHOD__);
            }
            return 
                 'jQuery("' . $gId . '").jqGrid("columnChooser", '
                . JqGridHelper::json_encode($this)
                .')';
            
        }

}
