<!--
To change this template, choose Tools | Templates
and open the template in the editor.
-->
<!DOCTYPE html>
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <title></title>
        <style>
/* states and images */
.v-stat-icon { display:block; float:left; width: 30px; height: 30px; background-image: url(../touren/img/round-icons-small.png); }
.v-stat-gruen { background-position: -29px 0; }
.v-stat-beauftragt, .v-stat-rot   { background-position: 0 0; }
.v-stat-fertig,     .v-stat-blau  { background-position: -58px 0; }
.v-stat-erneut,     .v-stat-braun { background-position: 0 -29px; }
.v-stat-neu,        .v-stat-grau  { background-position: -29px -29px; }
.v-stat-teil,       .v-stat-gelb  { background-position: -58px -29px; }

/* states and mini-images */
.v-stat-mini { display:block; float:left; width: 16px; height: 16px; background-image: url(../touren/img/round-icons-mini.png); }
.v-mini-gruen { background-position: -16px 0; }
.v-mini-beauftragt, .v-mini-rot   { background-position: 0 0; }
.v-mini-fertig,     .v-mini-blau  { background-position: -32px 0; }
.v-mini-erneut,     .v-mini-braun { background-position: 0 -16px; }
.v-mini-neu,        .v-mini-grau  { background-position: -16px -16px; }
.v-mini-teil,       .v-mini-gelb  { background-position: -32px -16px; }
        </style>
    </head>
    <body>
        <div>
        <span class="v-stat-icon v-stat-beauftragt v-stat-rot" title="rot"></span>
        <span class="v-stat-icon v-stat-gruen" title="gruen"></span>
        <span class="v-stat-icon v-stat-fertig v-stat-blau" title="blau"></span>
        <span class="v-stat-icon v-stat-erneut v-stat-braun" title="braun"></span>
        <span class="v-stat-icon v-stat-neu v-stat-grau" title="grau"></span>
        <span class="v-stat-icon v-stat-teil v-stat-gelb" title="gelb"></span>
        </div>
        <br clear="all" style="clear:both;"/>
        
        <div>
        <span class="v-stat-mini v-mini-beauftragt v-mini-rot" title="rot"></span>
        <span class="v-stat-mini v-mini-gruen" title="gruen"></span>
        <span class="v-stat-mini v-mini-fertig v-mini-blau" title="blau"></span>
        <span class="v-stat-mini v-mini-erneut v-mini-braun" title="braun"></span>
        <span class="v-stat-mini v-mini-neu v-mini-grau" title="grau"></span>
        <span class="v-stat-mini v-mini-teil v-mini-gelb" title="gelb"></span>
        </div>
        <br clear="all" style="clear:both;"/>
        <img src="../touren/img/round-icons-small.png" />
        <img src="../touren/img/round-icons-mini.png" />
        <?php
        // put your code here
        ?>
    </body>
</html>
