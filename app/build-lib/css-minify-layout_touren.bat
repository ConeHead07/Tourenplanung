php ./combine.php --out= ../public/css/all-layout-touren.max.css --writemode=w --cssbaseurl=../jquery/themes/redmond/ --in=../public/jquery/themes/redmond/jquery-ui-1.8.16.custom.css
php ./combine.php --out= ../public/css/all-layout-touren.max.css --writemode=a --in=../public/css/default.css ../public/css/jq_themes.extensions.css ../public/css/jquery.fbMultiSearchBox.css  ../public/css/droplinebar.css
php ./combine.php --out= ../public/css/all-layout-touren.max.css --writemode=a --cssbaseurl=../jquery/jquery.treeview/ --in=../public/jquery/jquery.treeview/jquery.treeview.css ../public/jquery/jquery.treeview/screen.css 
php ./combine.php --out= ../public/css/all-layout-touren.max.css --writemode=a --cssbaseurl=../jquery/ui/layout/css/ --in=../public/jquery/ui/layout/css/documentation.css 
php ./combine.php --out= ../public/css/all-layout-touren.max.css --writemode=a --cssbaseurl=../jquery/atooltip/ --in=../public/jquery/atooltip/atooltip.css 
php ./combine.php --out= ../public/css/all-layout-touren.max.css --writemode=a --cssbaseurl=../jquery/autocomplete_chooser/ --in=../public/jquery/autocomplete_chooser/chooser.css 
php ./combine.php --out= ../public/css/all-layout-touren.max.css --writemode=a --cssbaseurl=../jquery/combobox/ --in=../public/jquery/combobox/jquery.ui.combobox.css 
php ./combine.php --out= ../public/css/all-layout-touren.max.css --writemode=a --cssbaseurl=../touren/jquery.crm.css/ --in=../public/touren/jquery.crm.css/DragRoutes.css ../public/touren/jquery.crm.css/fbPortlet.css
php ./combine.php --out= ../public/css/all-layout-touren.max.css --writemode=a --in=../public/jquery/util/jquerytoast/jquery.toast.css

java -jar ./yuicompressor-2.4.7.jar -o ..\public\css\all-layout-touren.min.css ..\public\css\all-layout-touren.max.css