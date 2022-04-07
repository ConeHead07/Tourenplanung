<?php

/**
 * colModel Options

  As of 3.5 release jqGrid uses a common search options that can be used on every search method. Below is a list of these options that should be set in colModel. Note that some options are not applicable for particular method.
  Option	Type	Description	Default
  search	boolean	Determines if the field can be searched.	true
  stype	string	Determines the search type of the field. Can be text - also a input element with type text is created and select - a select element is created	text
  searchoptions	object	Object which contain definition, events and other properties for the searched field. See below
  searchrules	object	Object which contain additional conditions for validating user input
 */
class JqGridColModelSearchOptions extends JqGridBaseObject {

    /**
     * This option is valid only for the elements of type select - i.e stype:'select'
     * @abstract This option is valid only for the elements of type select - i.e stype:'select'.<br>
     * The option represent the url from where we load the select element.<br>
     * When this option is set the element will be filled with values from the ajax request.<br>
     * The data should be a valid html select element with the desired options.<br>
     * By example the request should contain &lt;select&gt;&lt;option value=?1?&gt;One&lt;/option&gt; &lt;option value=?2?&gt;Two&lt;/option&gt;&lt;/select&gt;.<br>
     * This is called only once.
     * @param string $dataUrl
     * @return JqGridColModelSearchOptions 
     */
    public function set_dataUrl($dataUrl) {
        $this->dataUrl = $dataUrl;
        return $this;
    }

    /**
     * This option have sense only if the dataUrl parameter is set
     * @abstract This option have sense only if the dataUrl parameter is set.<br>
     * In case where the server response can not build the select element you can use your on function to build the select.<br>
     * The function should return a string containing the select and options value(s) as described in dataUrl option.<br>
     * Parameter passed to this function is the server response
     * @param function $buildSelect
     * @return JqGridColModelSearchOptions 
     */
    public function set_buildSelect($buildSelect) {
        $this->buildSelect = (is_string($buildSelect) ? new JsFunction($buildSelect) : $buildSelect);
        return $this;
    }

    /**
     * If set this function is called only once when the element is created
     * @abstract If set this function is called only once when the element is created.<br>
     * To this function we pass the element object.<br/>
     * 
     *  dataInit: function(elem) { <br/>
     *  do something <br/>
     *  } <br/>
     *  Also use this function to attach datepicker, time picker and etc.<br>
     * Example: <br/>
     *  dataInit : function (elem) {<br/>
     *  $(elem).datepicker();<br/>
     * 
     *  }
     * @param function $dataInit
     * @return JqGridColModelSearchOptions 
     */
    public function set_dataInit($dataInit) {
        $this->dataInit = (is_string($dataInit) ? new JsFunction($dataInit) : $dataInit);
        return $this;
    }

    /**
     * List of events to apply to the data element; uses $(?#id?).bind(type, [data], fn
     * @abstract List of events to apply to the data element; uses $(?#id?).bind(type, [data], fn) to bind events to data element.<br>
     * Should be described like this:  <br/>
     *  dataEvents: [ <br/>
     *  { type: 'click', data: { i: 7 }, fn: function(e) { console.log(e.data.i); }},<br/>
     * 
     *  { type: 'keypress', fn: function(e) { console.log('keypress'); } } <br/>
     *  ]
     * @param array $dataEvents
     * @return JqGridColModelSearchOptions 
     */
    public function set_dataEvents($dataEvents) {
        $this->dataEvents = $dataEvents;
        return $this;
    }

    /**
     * attr is object where we can set valid attributes to the created element
     * @abstract attr is object where we can set valid attributes to the created element.<br>
     * By example: <br/>
     *  attr : { title: ?Some title? } <br/>
     * 
     *  Will set a title of the searched element
     * @param object $attr
     * @return JqGridColModelSearchOptions 
     */
    public function set_attr($attr) {
        $this->attr = $attr;
        return $this;
    }

    /**
     * By default hidden elements in the grid are not searchable
     * @abstract  By default hidden elements in the grid are not searchable .<br>
     * In order to enable searching when the field is hidden set this option to true
     * @param boolean $searchhidden
     * @return JqGridColModelSearchOptions 
     */
    public function set_searchhidden($searchhidden) {
        $this->searchhidden = $searchhidden;
        return $this;
    }

    /**
     * This option is used only in advanced single field searching and determines the o
     * @abstract This option is used only in advanced single field searching and determines the operation that is applied to the element.<br>
     * If not set all the available options will be used.<br>
     * All available option are: <br/>
     *  ['eq','ne','lt','le','gt','ge','bw','bn','in','ni','ew','en','cn','nc'] <br/>
     *  The corresponding texts are in language file and mean the following: <br/>
     *  ['equal','not equal', 'less', 'less or equal','greater','greater or equal', 'begins with','does not begin with','is in','is not in','ends with','does not end with','contains','does not contain'] <br/>
     *  Note that the elements in sopt array can be mixed in any order.
     * @param array $sopt
     * @return JqGridColModelSearchOptions 
     */
    public function set_sopt($sopt) {
        $this->sopt = $sopt;
        return $this;
    }

    /**
     * 
     * @abstract If not empty set a default value in the search input element.
     * @param string $defaultValue
     * @return JqGridColModelSearchOptions 
     */
    public function set_defaultValue($defaultValue) {
        $this->defaultValue = $defaultValue;
        return $this;
    }

    /**
     * The option is used only for stype select and defines the select options in the
     * @abstract  The option is used only for stype select and defines the select options in the search dialogs.<br>
     * When set for stype select and dataUrl option is not set, the value can be a string or object.<br>
     * <br/>
     * 
     *  If the option is a string it must contain a set of value:label pairs with the value separated from the label with a colon (:) and ended with(;).<br>
     * The string should not end with a (;)- editoptions:{value:?1:One;2:Two?}.If set as object it should be defined as pair name:value - editoptions:{value:{1:'One',2:'Two'}} 
     * @param mixed $value
     * @return JqGridColModelSearchOptions 
     */
    public function set_value($value) {
        $this->value = $value;
        return $this;
    }

}
