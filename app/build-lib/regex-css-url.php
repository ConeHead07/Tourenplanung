<?php


$lines = <<<EOT
.ui-icon { width: 16px; height: 16px; background-image: url(images/ui-icons_469bdd_256x240.png); }
.ui-widget-content .ui-icon {background-image: url(images/ui-icons_469bdd_256x240.png); }
.ui-widget-header .ui-icon {background-image: url(images/ui-icons_d8e7f3_256x240.png); }
.ui-state-default .ui-icon { background-image: url("images/ui-icons_6da8d5_256x240.png"); }
.ui-state-hover .ui-icon, .ui-state-focus .ui-icon {background-image: url('images/ui-icons_217bc0_256x240.png'); }
.ui-state-active .ui-icon {background-image: url(/images/ui-icons_f9bd01_256x240.png); }
.ui-state-highlight .ui-icon {background-image: url(http://images/ui-icons_2e83ff_256x240.png); }
.ui-state-error .ui-icon, .ui-state-error-text .ui-icon {background-image: url(images/ui-icons_cd0a0a_256x240.png); }
EOT;

$pattern = '#url\([\'"]?[ ]*([^/].*?)[\'"]?\)#';
$lines2 = preg_replace($pattern, "../jquery/themes/redmond/$1", $lines);

echo '<pre>' . $lines2 . '</pre>' . PHP_EOL;

if (preg_match_all($pattern, $lines, $m)) {
    echo '<pre>'.print_r($m,1).'</pre>';
}
?>
