<?php

class JqGridHeader extends JqGridBaseObject
{
    protected static $_jqGridBaseUrl = APPLICATION_BASE_URL;
    protected static $_enabled = false;
    
    public static function setJqGridBaseUrl($baseUrl)
    {
        self::$_jqGridBaseUrl = $baseUrl;
    }
    
    /**
     *
     * @param bool $mode true => aktiviert Ausgabe der jqgrid-Headerfiles
     */
    public static function enable($mode)
    {
        self::$_enabled = (bool) $mode;
    }
    public static function isEnabled()
    {
        return self::$_enabled;
    }
    
    public static function getLocal($opt = array())
    {
        if (!array_key_exists('baseUrl', $opt)) $opt['baseUrl'] = self::$_jqGridBaseUrl;
        if (!array_key_exists('without', $opt)) $opt['without']  = array();
        if (!array_key_exists('locale',  $opt)) $opt['locale']  = 'de';
        if (!array_key_exists('enable',  $opt)) $opt['enable']  = self::$_enabled;
        if (!$opt['enable']) return '';
        
        $not = $opt['without'];
        $withCssTheme  = (!$not || !in_array('cssTheme',$not));
        $withCssGrid   = (!$not || !in_array('cssGrid', $not));
        $withCssMulti  = (!$not || !in_array('cssMulti',$not));
        $withJQuery    = (!$not || !in_array('jQuery'  ,$not));
        $withJQueryUI  = (!$not || !in_array('jQueryUI',$not));
        $withJqLayout  = (!$not || !in_array('jQueryLayout', $not));        
        $withJqMulti   = (!$not || !in_array('jQueryMulti',  $not));
        $withJqTblDnd  = (!$not || !in_array('jQueryTblDnd', $not));
        $withJqContext = (!$not || !in_array('jQueryContext',$not));
        
        $bu = $opt['baseUrl'];
       
        if (1) return 
         (!$withCssTheme ? '' : '<link  href="' . $bu . '/jquery/themes/redmond/jquery-ui-1.8.16.custom.css" rel="stylesheet" type="text/css" />')
        .(!$withCssGrid  ? '' : '<link  href="' . $bu . '/jquery/jqgrid4.4.1/css/ui.jqgrid.css" rel="stylesheet" type="text/css" />')
        .(!$withJQuery   ? '' : '<script src="' . $bu . '/jquery/jqgrid4.4.1/jquery-1.7.2.min.js"></script>')
        .(!$withJQueryUI ? '' : '<script src="' . $bu . '/jquery/ui/minified/jquery-ui.min.js"></script>')
        .('<script src="' . $bu . '/jquery/jqgrid4.4.1/js/jquery.jqGrid.min.js"></script>')
        .('<script src="' . $bu . '/jquery/jqgrid4.4.1/js/i18n/grid.locale-de.js"></script>')
        ;
        
        return 
        ($withCssTheme  ? '<link  href="' . $bu . '/jquery/jqgrid4.0/themes/redmond/jquery-ui-1.8.2.custom.css" rel="stylesheet" type="text/css" />' . PHP_EOL : '')
       .($withCssGrid   ? '<link  href="' . $bu . '/jquery/jqgrid4.0/themes/ui.jqgrid.css" rel="stylesheet" type="text/css" />' . PHP_EOL : '')
       .($withCssMulti  ? '<link  href="' . $bu . '/jquery/jqgrid4.0/themes/ui.multiselect.css" rel="stylesheet" type="text/css" />' . PHP_EOL : '')
       .($withJQuery    ? '<script src="' . $bu . '/jquery/jqgrid4.0/js/jquery.min.js"></script>' . PHP_EOL : '')
       .($withJQueryUI  ? '<script src="' . $bu . '/jquery/jqgrid4.0/js/jquery-ui-1.8.2.custom.min.js"></script>' . PHP_EOL : '')
       .($withJqLayout  ? '<script src="' . $bu . '/jquery/jqgrid4.0/js/jquery.layout.js"></script>' . PHP_EOL : '')
       .($withJqMulti   ? '<script src="' . $bu . '/jquery/jqgrid4.0/js/ui.multiselect.js"></script>' . PHP_EOL : '')
       .($withJqContext ? '<script src="' . $bu . '/jquery/jqgrid4.0/js/jquery.contextmenu.js"></script>' . PHP_EOL : '')
       .($withJqTblDnd  ? '<script src="' . $bu . '/jquery/jqgrid4.0/js/jquery.tablednd.js"></script>' . PHP_EOL : '')
       .'<script src="' . $bu . '/jquery/jqgrid4.0/js/i18n/grid.locale-'.$opt['locale'].'.js"></script>' . PHP_EOL
       .'<script src="' . $bu . '/jquery/jqgrid4.0/js/jquery.jqGrid.min.js"></script>' . PHP_EOL
//       .'<script src="' . $bu . '/jquery/jqgrid4.0/src/grid.treegrid.js"></script>' . PHP_EOL
       ;
        
    }
    
    public static function getCdn()
    {
        if (!self::$_enabled) return '';
        
        return 
            '<link rel="stylesheet" href="http://trirand.com/blog/jqgrid/themes/redmond/jquery-ui-1.8.1.custom.css" type="text/css" />
            <link rel="stylesheet" href="http://trirand.com/blog/jqgrid/themes/ui.jqgrid.css" type="text/css" />
            <link rel="stylesheet" href="http://trirand.com/blog/jqgrid/themes/ui.multiselect.css" type="text/css" />
            <script src="http://trirand.com/blog/jqgrid/js/jquery.js"></script>
            <script src="http://trirand.com/blog/jqgrid/js/jquery-ui-1.8.1.custom.min.js"></script>
            <script src="http://trirand.com/blog/jqgrid/js/jquery.layout.js"></script>
            <script src="http://trirand.com/blog/jqgrid/js/i18n/grid.locale-en.js"></script>
            <script src="http://trirand.com/blog/jqgrid/js/ui.multiselect.js"></script>
            <script src="http://trirand.com/blog/jqgrid/js/jquery.jqGrid.min.js"></script>
            <script src="http://trirand.com/blog/jqgrid/js/jquery.tablednd.js"></script>
            <script src="http://trirand.com/blog/jqgrid/js/jquery.contextmenu.js"></script>';
    }
}
