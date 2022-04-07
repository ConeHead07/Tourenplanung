<?php

class JqGridNavGridSearchFormOptions extends JqGridBaseObject {

    /**
     * 
     * @abstract This event fires (if defined) every time after the search dialog is shown
     * Default null
     * @param function $afterShowSearch
     * @return JqGridNavGridSearchFormOptions 
     */
    public function set_afterShowSearch($afterShowSearch) {
        $this->afterShowSearch = (is_string($afterShowSearch)) ? new JsFunction($afterShowSearch) : $afterShowSearch;
        return $this;
    }

    /**
     * 
     * @abstract This event fires (if defined) every time before the search dialog is shown
     * Default null
     * @param function $beforeShowSearch
     * @return JqGridNavGridSearchFormOptions 
     */
    public function set_beforeShowSearch($beforeShowSearch) {
        $this->beforeShowSearch = (is_string($beforeShowSearch)) ? new JsFunction($beforeShowSearch) : $beforeShowSearch;
        return $this;
    }

    /**
     * If set to true this closes the search dialog after the user apply a search - i.
     * @abstract  If set to true this closes the search dialog after the user apply a search - i.e.<br>
     * click on Find button
     * Default false
     * @param boolean $closeAfterSearch
     * @return JqGridNavGridSearchFormOptions 
     */
    public function set_closeAfterSearch($closeAfterSearch) {
        $this->closeAfterSearch = (bool) $closeAfterSearch;
        return $this;
    }

    /**
     * If set to true this closes the search dialog after the user apply a reset - i.e
     * @abstract  If set to true this closes the search dialog after the user apply a reset - i.e.<br>
     * click on Reset button
     * Default false
     * @param boolean $closeAfterReset
     * @return JqGridNavGridSearchFormOptions 
     */
    public function set_closeAfterReset($closeAfterReset) {
        $this->closeAfterReset = (bool) $closeAfterReset;
        return $this;
    }

    /**
     * 
     * @abstract Enables or disables draging of the modal
     * Default false
     * @param boolean $drag
     * @return JqGridNavGridSearchFormOptions 
     */
    public function set_drag($drag) {
        $this->drag = (bool) $drag;
        return $this;
    }

    /**
     * 
     * @abstract Enables or disables resizing of the modal
     * Default true
     * @param boolean $resize
     * @return JqGridNavGridSearchFormOptions 
     */
    public function set_resize($resize) {
        $this->resize = (bool) $resize;
        return $this;
    }

    /**
     * 
     * @abstract Is set to true the search dialog becomes modal
     * Default false
     * @param boolean $modal
     * @return JqGridNavGridSearchFormOptions 
     */
    public function set_modal($modal) {
        $this->modal = (bool) $modal;
        return $this;
    }

    /**
     * 
     * @abstract Defines the width os the search dialog
     * Default  450
     * @param  integer $width
     * @return JqGridNavGridSearchFormOptions 
     */
    public function set_width($width) {
        $this->width = (int) $width;
        return $this;
    }

    /**
     * 
     * @abstract  Defines the height of the search dialog
     * Default  auto
     * @param  mixed $height
     * @return JqGridNavGridSearchFormOptions 
     */
    public function set_height($height) {
        $this->height = (int) $height;
        return $this;
    }

    /**
     * 
     * @abstract The caption of the modal
     * Default see lang file
     * @param string $caption
     * @return JqGridNavGridSearchFormOptions 
     */
    public function set_caption($caption) {
        $this->caption = $caption;
        return $this;
    }

    /**
     * If set to true shows the query which is generated when the user defines the conditions for the search.
     * @abstract  If set to true shows the query which is generated when the user defines the conditions for the search.<br>
     * Valid only in advanced search.<br>
     * Again with this a button near search button appear which allows the user to show or hide the query string interactively  
     * Default  false
     * @param  boolean $showQuery
     * @return JqGridNavGridSearchFormOptions 
     */
    public function set_showQuery($showQuery) {
        $this->showQuery = (bool) $showQuery;
        return $this;
    }

    /**
     * 
     * @abstract The text in the find button
     * Default see lang file
     * @param string $Find
     * @return JqGridNavGridSearchFormOptions 
     */
    public function set_Find($Find) {
        $this->Find = $Find;
        return $this;
    }

    /**
     * 
     * @abstract If set to true this activates the advanced searching
     * Default false
     * @param boolean $multipleSearch
     * @return JqGridNavGridSearchFormOptions 
     */
    public function set_multipleSearch($multipleSearch) {
        $this->multipleSearch = $multipleSearch;
        return $this;
    }

    /**
     * 
     * @abstract If set to true this activates the advanced searching with a possibilities to define a complex condfitions
     * Default false
     * @param boolean $multipleGroup
     * @return JqGridNavGridSearchFormOptions 
     */
    public function set_multipleGroup($multipleGroup) {
        $this->multipleGroup = $multipleGroup;
        return $this;
    }

    /**
     * 
     * @abstract Translation strings that corresponds to the sopt options
     * Default see lang file
     * @param array $odata
     * @return JqGridNavGridSearchFormOptions 
     */
    public function set_odata($odata) {
        $this->odata = $odata;
        return $this;
    }

    /**
     * If defined this event fires when the dialog is closed
     * @abstract If defined this event fires when the dialog is closed.<br>
     * Can return true or false.<br>
     * If the event return false the dialog will not be closed
     * Default null
     * @param function $onClose
     * @return JqGridNavGridSearchFormOptions 
     */
    public function set_onClose($onClose) {
        $this->onClose = (is_string($onClose)) ? new JsFunction($onClose) : $onClose;
        return $this;
    }

    /**
     * 
     * @abstract  This function if defined is lunched every time the filter is redrawed - the filter is redrawed evey time when we add or delet rules or fields Tio this function we pass the search parameters as parameter 
     * Default  null
     * @param  function $afterRedraw
     * @return JqGridNavGridSearchFormOptions 
     */
    public function set_afterRedraw($afterRedraw) {
        $this->afterRedraw = (is_string($afterRedraw)) ? new JsFunction($afterRedraw) : $afterRedraw;
        return $this;
    }

    /**
     * 
     * @abstract If defined this event fires when the search Button is clicked.
     * Default null
     * @param function $onSearch
     * @return JqGridNavGridSearchFormOptions 
     */
    public function set_onSearch($onSearch) {
        $this->onSearch = (is_string($onSearch)) ? new JsFunction($onSearch) : $onSearch;
        return $this;
    }

    /**
     * 
     * @abstract  If defined this function fire if reset button is activated 
     * Default null
     * @param function $onReset
     * @return JqGridNavGridSearchFormOptions 
     */
    public function set_onReset($onReset) {
        $this->onReset = (is_string($onReset)) ? new JsFunction($onReset) : $onReset;
        return $this;
    }
    
    /**
     * @abstract This event occurs only once when the modal is created	Default null
     * @param function $onInitializeSearch
     * @return JqGridNavGridSearchFormOptions 
     */
    public function set_onInitializeSearch($onInitializeSearch) {
        $this->onInitializeSearch = (is_string($onInitializeSearch) ? new JsFunction($onInitializeSearch) : $onInitializeSearch);
        return $this;
    }
    /**
     * 
     * @abstract  Is this option is set to true the search dialogue is closed if the user press ESC key
     * @param  boolean  $closeOnEscape
     * @return JqGridNavGridSearchFormOptions 
     */
    public function set_closeOnEscape($closeOnEscape) {
        $this->closeOnEscape = $closeOnEscape;
        return $this;
    }

    /**
     * This option is valid only in navigator options
     * @abstract  This option is valid only in navigator options.<br>
     * If set to true the dialog appear automatically when the grid is constructed for first time
     * Default  false
     * @param boolean $showOnLoad
     * @return JqGridNavGridSearchFormOptions 
     */
    public function set_showOnLoad($showOnLoad) {
        $this->showOnLoad = $showOnLoad;
        return $this;
    }

    /**
     * if searchrules are defined this turn on of error checking
     * @abstract  if searchrules are defined this turn on of error checking.<br>
     * If there is a error in the input the filter is not posted to the server and a error message appear.
     * Default true
     * @param  boolean  $errorcheck
     * @return JqGridNavGridSearchFormOptions 
     */
    public function set_errorcheck($errorcheck) {
        $this->errorcheck = $errorcheck;
        return $this;
    }

    /**
     * 
     * @abstract The text for the clear (reset) button
     * Default see lang file
     * @param string $Reset
     * @return JqGridNavGridSearchFormOptions 
     */
    public function set_Reset($Reset) {
        $this->Reset = $Reset;
        return $this;
    }

    /**
     * 
     * @abstract See <em>sopt</em> description
     * Default searchField
     * @param string $sField
     * @return JqGridNavGridSearchFormOptions 
     */
    public function set_sField($sField) {
        $this->sField = $sField;
        return $this;
    }

    /**
     * Aplicable to advanced searching
     * @abstract Aplicable to advanced searching.<br>
     * See advanced <a title="wiki:advanced_searching" class="wikilink1" href="/http://http://www.trirand.com/jqgridwiki/doku.php?id=wiki:advanced_searching"> searching</a> 
     * Default filters
     * @param string $sFilter
     * @return JqGridNavGridSearchFormOptions 
     */
    public function set_sFilter($sFilter) {
        $this->sFilter = $sFilter;
        return $this;
    }

    /**
     * 
     * @abstract See <em>sopt</em> description
     * Default searchOper
     * @param string $sOper
     * @return JqGridNavGridSearchFormOptions 
     */
    public function set_sOper($sOper) {
        $this->sOper = $sOper;
        return $this;
    }

    /**
     * Use this option to set common search rules
     * @abstract Use this option to set common search rules.<br>
     * If not set all the available options will be used.<br>
     * All available option are: ['eq','ne','lt','le','gt','ge','bw','bn','in','ni','ew','en','cn','nc'] 
     * The corresponding texts are in language file and mean the following: 
     * ['equal','not equal', 'less', 'less or equal','greater','greater or equal', 
     * 'begins with','does not begin with','is in','is not in','ends with','does not end with',
     * 'contains','does not contain'] 
     * Note that the elements in sopt array can be mixed in any order.
     * Default  
     * @param array $sopt
     * @return JqGridNavGridSearchFormOptions 
     */
    public function set_sopt($sopt) {
        $this->sopt = $sopt;
        return $this;
    }

    /**
     * 
     * @abstract See <em>sopt</em> description
     * Default searchString
     * @param string $sValue
     * @return JqGridNavGridSearchFormOptions 
     */
    public function set_sValue($sValue) {
        $this->sValue = $sValue;
        return $this;
    }

    /**
     * 
     * @abstract If this option is set to 0 the overlay in grid is disabled and the user can interact with the grid while search dialog is active 
     * Default  10 
     * @param integer $overlay
     * @return JqGridNavGridSearchFormOptions 
     */
    public function set_overlay($overlay) {
        $this->overlay = $overlay;
        return $this;
    }

    /**
     * If defined this should be a valid id in the <acronym title="Document Object Mod
     * @abstract  If defined this should be a valid id in the <acronym title="Document Object Model">DOM</acronym>.<br>
     * Also if this option is set the filter is inserted as child of this element
     * Default  null
     * @param  string $layer
     * @return JqGridNavGridSearchFormOptions 
     */
    public function set_layer($layer) {
        $this->layer = $layer;
        return $this;
    }

    /**
     * Defines the name of the templates used for easy user input - by example like th
     * @abstract  Defines the name of the templates used for easy user input - by example like this : ['Template1', 'Template2',?].<br>
     * See grid demo how to define templates.
     * Default  null
     * @param  array $tmplNames
     * @return JqGridNavGridSearchFormOptions 
     */
    public function set_tmplNames($tmplNames) {
        $this->tmplNames = $tmplNames;
        return $this;
    }

    /**
     * if defined this should correspond to the tmplNames
     * @abstract  if defined this should correspond to the tmplNames.<br>
     * See demo how to define template
     * Default  null
     * @param array of objects  $tmplFilters
     * @return JqGridNavGridSearchFormOptions 
     */
    public function set_tmplFilters($tmplFilters) {
        $this->tmplFilters = $tmplFilters;
        return $this;
    }

    /**
     * If a template is defined a select element appear between the Reset and Find but
     * @abstract  If a template is defined a select element appear between the Reset and Find buttons.<br>
     * This is a the text describing the select 
     * Default Template:
     * @param  string $tmplLabel
     * @return JqGridNavGridSearchFormOptions 
     */
    public function set_tmplLabel($tmplLabel) {
        $this->tmplLabel = $tmplLabel;
        return $this;
    }

}
