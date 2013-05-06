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


if ( !isset($special_message) && $s_editor ) {
    //$special_message = "The very first version is online :D";
}

if (!defined('BCS')) {
    die("ERROR");
}

if (!isset($_SESSION[showedpmwarning]) && $newmessages > 0) {
    //$special_message = 'You have new private messages. Go to your <a href="index.php?plugin=forum&action=board&board=pt">Private Topics</a>';
}

if ( $s_editor ) {
  //$special_message = 'Check out the experimental <a href="todos.php">todo list</a>!';
    // $special_message = 'We are moving over to use Trello as our <a href="https://trello.com/ironbane">new todo list</a>!';
}

//
// Build page
//

$c_head .= '<script type="text/javascript" language="javascript" src="plugins/game/shared.js"></script>
';

$c_jquery .= '

    $("body").append(\'<div id="tooltip" class="gen"></div>\');
    //$("#tooltip").css("width", "200px");
    //$("#tooltip").css("height", "50px");
    //$("#tooltip").css("padding", "5px");
    $("#tooltip").css("position", "absolute");
    $("#tooltip").css("border-color", "black");
    $("#tooltip").css("border-width", "2px");
    $("#tooltip").css("border-style", "solid");
    $("#tooltip").css("background-color", brown3);
    //$("#tooltip").css("background-image", "url(plugins/world/images/misc/bg_brown.png)");
    $("#tooltip").css("z-index", "100018");
    $("#tooltip").hide();

';

if ($use_niftyplayer) {
    $c_head .= '<script type="text/javascript" language="javascript" src="config/niftyplayer.js"></script>
';
}



if ($use_simple_rendering) {
    $c_header .= '

    ';

    $c_footer .= '

    ';

} else {


//$gameserver_online = true;
//$url = 'http://www.ironbane.com:8080/';
//if ( !@get_headers($url, 1) ) $gameserver_online = false;
//
//
//
//            <div style="float:right;width:165px">
//                <div class="genbig" style="position:absolute;top:50px;">Server <div style="display:inline;color:'.($gameserver_online?'#9FF781':'#FF0000').'">'.($gameserver_online?'Online':'Offline').'</div></div>
//                <div id="playbuttoncontainer"><a href="game.php"><img id="playbutton" src="theme/images/buttons/play.png"></a></div>
//            </div>

   $nposts = getRowCount("forum_topics WHERE (time > '$userdata[previous_session]' AND private = 0) OR ((private_from = '$userdata[id]' OR private_chatters LIKE '%" . $userdata[name] . "%') AND private = 1 AND time > '$userdata[previous_session]') ORDER BY time DESC");

    $c_header = '
<div id="topgradient"></div>

<div id="pagecontainer">
    <div id="topcontainer">
        <div id="topleftpillar"></div>

        <div id="topmiddle">
            <div><a href="index.php"><img src=theme/images/logo_'.($plugin=="forum"?"forum":"isolated").'.png></a></div>
        </div>

        <div id="toprightpillar"></div>
    </div>
    <div id="middlecontainer">
        <div id="middleleftpillar"></div>

        <div style="float:left">
            <div id="topcontent"></div>
            <div id="content">
' . (!empty($special_message) ? '
                <div style="width:100%;min-height:50px;">
                    <div style="float:left"><img src=theme/images/attention.png></div>
                    <div style="float:left;padding:15px">
                    ' . $special_message . '
                    </div>
                </div>
                <hr>
    ' : '') . '
      <div align="center">
        <a href="index.php">About</a>
        | <a href="forum.php">Forum</a>
        | <a href="game.php">Play Ironbane</a>
        | <a href="get-involved.php">Get Involved</a>
        | <a href="https://github.com/ironbane" target="_new">GitHub</a>
        | <a href="https://twitter.com/IronbaneMMO" target="_new">Twitter</a>
        | '.(!$s_auth?'<a href="login.php">Log In</a>':'<a href="preferences.php">Preferences</a>
            | <a href="logout.php">Log Out</a>').''.($s_editor?' | <a href="editor.php">Editor</a>
            | <a href="uploads.php?action=viewuploads">Uploads</a>
            | <a href="https://trello.com/ironbane" target="_new">Todo List</a>':'').'
      </div>
' . ($s_auth ? '
<hr>
                Hey, <b>'.  memberLink($userdata[id]).'!</b> You last visited '.timeAgo($userdata[previous_session]).' ago.
                    '.($nposts>0?'There '.($nposts==1?'is':'are').' <a href="forum.php?action=board&amp;board=rt"><b>'.$nposts.'</b> new/updated topic'.SorNot($nposts).'</a> since your last visit':'No new posts since your last visit').'. Currently online: '.getListOfOnlineMembers().'<br>Players that visited in the last 24 hours: '.  getListOfLastDayVisitors().'

' : '') . '       <hr>
';
//    <div id="bottomcontainer">
//        <div id="bottomleftpillar"></div>
//
//        <div id="bottommiddle"></div>
//
//        <div id="bottomrightpillar"></div>
//    </div>
    $c_footer .= '
             </div>
        </div>

        <div id="middlerightpillar"></div>
    </div>

    <div id="bottombg"></div>

    <div id="bottomcontainer">
        <div id="bottomleftpillar"></div>

        <div id="bottommiddle">
            <div id="footer">&copy; Ironbane 2013<br><a href="terms.php" target="_blank">Terms and Conditions</a> | <a href="pp.php" target="_blank">Privacy Policy</a></div>
        </div>

        <div id="bottomrightpillar"></div>
    </div>


</div>

';
    $c_jquery_manual .= '

            function FixBorder() {
            var height = $("#content").height();
            // Must be dividable by 128
            height = Math.ceil(height/128)*128;
            $("#middleleftpillar").height(height);
            $("#middlerightpillar").height(height);
            $("#middlecontainer").height(height);
            //$("#content").height(height-20);
            $("#content").css("min-height", (height-20)+"px");
        };
';



 $c_jquery .= '

        FixBorder();
        for(var i=1;i<5;i++){
            setTimeout(function(){FixBorder();}, i*1000);
        }


    ';
}




$c_header .= '



';



if ($use_niftyplayer) {
    $c_footer .= '<object classid="clsid:D27CDB6E-AE6D-11cf-96B8-444553540000" codebase="http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=6,0,0,0" width="165" height="38" id="niftyPlayer1" align="">
<param name=movie value="config/niftyplayer.swf?file=config/chat.mp3">
<param name=quality value=high>
<param name=bgcolor value=#FFFFFF>
<embed src="config/niftyplayer.swf?file=config/chat.mp3" quality=high bgcolor=#FFFFFF width="0" height="0" name="niftyPlayer1" align="" type="application/x-shockwave-flash" pluginspage="http://www.macromedia.com/go/getflashplayer">
</embed>
</object>
';
}

// Wait for document load
$c_jquery = '
'.$c_jquery_manual.'
$(document).ready(function(){
' . $c_jquery . '
})
';

?>
