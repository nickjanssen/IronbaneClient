<?php
/*
    This file is part of Ironbane MMO.

    Ironbane MMO is free software: you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation, either version 3 of the License, or
    (at your option) any later version.

    Ironbane MMO is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with Ironbane MMO.  If not, see <http://www.gnu.org/licenses/>.
*/




if ( !defined('BCS') ) {
	die("ERROR");
}

if ( !isset($c_title) ) {
	$c_title = $plugin_name;
}

if ( !$noTitlePostFix ) {
	$c_title = $c_title.' - Ironbane MMO';
}

if ( $use_jquery ) {
    //$c_head .= '<script src="config/jquery-1.6.2.js" type="text/javascript"></script>';
    $c_head .= '<script src="config/jquery-1.7.1.min.js" type="text/javascript"></script>';
    $c_head .= '<script src="config/jquery-ui-1.8.22.custom.min.js" type="text/javascript"></script>';
}

if ( $use_nicedit ) {
    $c_extra .= '
        <script src="config/nicEdit.js" type="text/javascript"></script>
        <script type="text/javascript">bkLib.onDomLoaded(nicEditors.allTextAreas);</script>
';
}


if ( $use_jscrollpane ) {
    $c_head .= '
<link type="text/css" href="config/jscrollpane/style/jquery.jscrollpane.css" rel="stylesheet" media="all" />
<script type="text/javascript" src="config/jscrollpane/script/jquery.mousewheel.js"></script>
<script type="text/javascript" src="config/jscrollpane/script/jquery.jscrollpane.min.js"></script>
';
}

$google = '
<script type="text/javascript">

  var _gaq = _gaq || [];
  _gaq.push([\'_setAccount\', \'UA-36851904-1\']);
  _gaq.push([\'_trackPageview\']);

  (function() {
    var ga = document.createElement(\'script\'); ga.type = \'text/javascript\'; ga.async = true;
    ga.src = (\'https:\' == document.location.protocol ? \'https://ssl\' : \'http://www\') + \'.google-analytics.com/ga.js\';
    var s = document.getElementsByTagName(\'script\')[0]; s.parentNode.insertBefore(ga, s);
  })();

</script>
';

$c_meta = '
<meta http-equiv="description" content="An action MMO played straight from your browser! Can you find and defeat Ironbane?">
<meta http-equiv="keywords" content="mmorpg, morpg, free, RPG, online, multiplayer, game, explore, fight, quest, battle, fun, monster, world, map, Ironbane">
<meta http-equiv="RATING" content="General">
';

// Include style theme
include("theme/theme.php");

$doctype = '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
   "http://www.w3.org/TR/html4/loose.dtd">';

if ( !empty($c_jquery) ) {
    $c_jquery = '<script type="text/javascript">'.$c_jquery.'</script>';
}

if( !$no_site_css ) {
    $c_css = '<link rel="stylesheet" href="theme/style.css" type="text/css">';
}

$template = ''.$doctype.'
<html dir=\'ltr\'>
<head>
'.$c_meta.'
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<meta http-equiv="Content-Style-Type" content="text/css">
<meta property="og:image" content="http://www.ironbane.com/images/logoBlock.png">
<title>'.$c_title.'</title>

'.$c_css.'
<link rel="shortcut icon" type="image/x-icon" href="/favicon.ico">
'.$c_head.'
'.$c_head_after.'

'.$google.'
</head>
<body>
'.$c_header.'
'.$c_main.'
'.$c_footer.'
'.$c_jquery.'
'.$c_extra.'

</body>
</html>';


echo $template;

?>
