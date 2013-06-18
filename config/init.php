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

$no_site_css = false;


$using_chromeframe = stristr($_SERVER['HTTP_USER_AGENT'], 'chromeframe');

$use_jquery = true;
$use_jscrollpane = true;
$use_nicedit = false;

$bcs_died = false;

$onlinePeriod = 300;

$noTitlePostFix = false;

unset($c_jquery, $c_head_after, $c_jquery_manual);

$c_extra = "";

$spacer = '<div class="spacer"></div>';

$hspacer = '&nbsp;&nbsp;&nbsp;';

$skinIdMaleStart = 1000;
$skinIdMaleEnd = 1004;

$skinIdFemaleStart = 1010;
$skinIdFemaleEnd = 1014;

$hairIdMaleStart = 1000;
$hairIdMaleEnd = 1009;

$hairIdFemaleStart = 1010;
$hairIdFemaleEnd = 1019;

$eyesIdMaleStart = 1000;
$eyesIdMaleEnd = 1009;

$eyesIdFemaleStart = 1010;
$eyesIdFemaleEnd = 1019;


class ChatTypes
{
    const Chat = 0;
    const Announcement = 1;
    // etc.
}

$c_css = "";
$c_header = "";
$c_main = "";
$c_footer = "";
$c_head = "";
$c_head_after = "";
$c_jquery = "";
$c_jquery_manual = "";
$use_niftyplayer = false;
$use_simple_rendering = false;


?>
