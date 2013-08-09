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


require 'PHPMailer/class.phpmailer.php';

// Smilies

$smilies_list = ":D :) :( :O :shock: :? 8) :lol: :x :P :oops: :cry: :evil: :twisted: :roll: ;) :!: :?: :idea: :arrow:";
$smilies_repl = "biggrin.gif smile.gif sad.gif surprised.gif eek.gif confused.gif cool.gif lol.gif mad.gif razz.gif redface.gif cry.gif evil.gif twisted.gif rolleyes.gif wink.gif exclaim.gif question.gif idea.gif arrow.gif";


function createLink($title, $address, $extra="") {
    return "<a href=\"" . $address . "\"" . $extra . ">" . $title . "</a>";
}


// The following function was created thanks to the help
// of a contributed note at the gmdate() function. www.php.net
function createDate($time, $timezone, $special=0) {
    if ($special == 1) {
        $dformat = "G";
    } elseif ($special == 2) {
        $dformat = "l H:i";
    } elseif ($special == 3) {
        $dformat = "H:i";
    } elseif ($special == 4) {
        $dformat = "d.m.y";
    } elseif ($special == 5) {
        $dformat = "d M Y";
    } else {
        $dformat = "D M d, Y g:i a";
    }
    $created_date = gmdate($dformat, $time + 3600 * ($timezone + date("I")));
    return $created_date;
}


function createDateSelf($time, $special=0) {
    global $userdata;
    return createDate($time, $userdata["gmt"], $special);
}

function parseToSurface($text) {
    $text = stripslashes($text);
    return $text;
}

function parseToDB($text, $allow_html=false) {

    $text = mysql_real_escape_string($text);

    if ( !$allow_html ) {
        $text = strip_tags($text);
    }

    return $text;
}



function getPosterLink($row, $user) {
    if (!empty($row["guestname"])) {
        if (!strstr($row["guestcontact"], '@')) {
            if (substr($row["guestcontact"], 0, 3) == "www") {
                $row["guestcontact"] = "http://" . $row["guestcontact"];
            }

            $who = empty($row["guestcontact"]) ? $row["guestname"] : "<a href='" . $row["guestcontact"] . "'>" . $row["guestname"] . "</a>";
        } else {
            $who = empty($row["guestcontact"]) ? $row["guestname"] : "<a href=mailto:'" . $row["guestcontact"] . "'>" . $row["guestname"] . "</a>";
        }
    } else {
        $who = memberLink($user);
    }

    return $who;
}



function bcs_die($msg, $url="index.php") {
    global $language, $o_css, $o_body, $c_theme, $userdata, $s_auth, $version,
        $use_simple_rendering, $spacer, $hspacer, $c_jquery, $use_jquery,
        $c_extra, $plugin, $noTitlePostFix, $use_nicedit, $use_jscrollpane,
        $s_editor, $use_niftyplayer, $s_editor, $c_footer, $c_jquery_manual,
        $use_niftyplayer, $no_site_css, $c_head_after, $c_head;


    $bcs_died = true;
    $c_title = "Information";

    if ($url == "back") {
        $url = "javascript:history.back(-1)";
    }


    if ($url != "none") {
        $msg = $msg . "<br><br>" . makeurlbutton("Continue", $url, "mainoption") . "<br><br></span><span class=\"gensmall\"><div id=\"redirect_cd\"></div></span><span class=\"gen\">";

        $c_jquery .= '
          $(document).ready(function(){countdown();});
        ';
        $c_head = "<script language=\"javascript\">

var seconds = 5;
var redirect_url = '" . $url . "';

function countdown() {

	document.getElementById('redirect_cd').innerHTML = 'Redirecting in '+seconds+' seconds.';

	if ( seconds == 0 ) {
		location.href = redirect_url;
	}

	else {
		seconds--;
		setTimeout(function(){countdown()}, 1000);
	}

}

</script>";

    }

    $c_main = '
<table width="'.($use_simple_rendering?'100%':'600').'" cellspacing="0" cellpadding="5" border="0" align="center">
  <tr>
	<td valign="top" align="center" width="100%">
	  <table width="100%" cellpadding="2" cellspacing="1" border="0" class="forumline">
	   <tr>
		<td class="row1" align="center" valign="top"><div style="height:20px"></div><span class="genbig">' . $msg . '<div style="height:20px"></div></span>
            </td>
        </tr>
       </table>
    </td>
</tr>
</table>
        ';

    if ($use_simple_rendering) {
        $c_header = "";
        $c_footer = "";
    }

    include("template.php");

    exit;

}


function requireLogin($this="") {

    global $s_auth;

    if ($s_auth == FALSE) {

        //$redirect = $this;

        $_SESSION["nm_redirect"] = $this;



        //echo "redirect is now: ".$_SESSION["nm_redirect"]."<br>";

        header("Location: index.php?plugin=login");

        exit;

    }

}




function mailToAll($subject, $content, $shownote) {

    set_time_limit(1000);

    $sql = "SELECT email FROM bcs_users WHERE receive_email = 1";
    $result = bcs_query($sql) or bcs_error("<b>SQL ERROR</b> in <br>file " . __FILE__ . " on line " . __LINE__ . "<br><br><b>" . $query . "</b><br><br>" . mysql_error());
    for ($x = 0; $x < mysql_num_rows($result); $x++) {
        $row = mysql_fetch_array($result);
        mailto($row["email"], $subject, $content, $shownote);
        echo "Mail sent to: ".$row["email"]."<br>";
    }
}

function mailto($to, $subject, $content, $shownote=0) {
    global $mandrill_api_key;

    if ( empty($to) ) return;


    $content = '<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<meta http-equiv="Content-Style-Type" content="text/css">
<title>Ironbane - ' . $subject . '</title>
<link rel="stylesheet" href="http://www.ironbane.com/plugins/game/style.css" type="text/css">
</head>
<body>
<a href="http://www.ironbane.com/"><img src="http://www.ironbane.com/theme/images/logo_isolated.png"></a>
<div class="bigoutline">' . $content . '</div>
</body>
</html>';

    $mail = new PHPMailer;

    $mail->IsSMTP();                                      // Set mailer to use SMTP
    $mail->Host = 'smtp.mandrillapp.com';  // Specify main and backup server
    $mail->SMTPAuth = true;                               // Enable SMTP authentication
    $mail->Username = 'nikke@ironbane.com';                            // SMTP username
    $mail->Password = $mandrill_api_key;                           // SMTP password
    //$mail->SMTPSecure = 'ssl';                            // Enable encryption, 'ssl' also accepted

    $mail->From = 'ironbot@ironbane.com';
    $mail->FromName = 'Ironbane';
    //$mail->AddAddress('jinfo', 'Josh Adams');  // Add a recipient
    $mail->AddAddress($to);               // Name is optional
    //$mail->AddReplyTo('info@example.com', 'Information');
    // $mail->AddCC('cc@example.com');
    // $mail->AddBCC('bcc@example.com');

    $mail->WordWrap = 50;                                 // Set word wrap to 50 characters
    // $mail->AddAttachment('/var/tmp/file.tar.gz');         // Add attachments
    // $mail->AddAttachment('/tmp/image.jpg', 'new.jpg');    // Optional name
    $mail->IsHTML(true);                                  // Set email format to HTML

    $mail->Subject = $subject;
    $mail->Body    = $content;
    $mail->AltBody = $content;

    if(!$mail->Send()) {
       // echo 'Message could not be sent.';
       echo 'Mailer Error: ' . $mail->ErrorInfo;
       // exit;
    }

    // echo 'Message has been sent';
}



function getRank($user) {

    if ($user == 0)
        return 0;

    $user = parseToDB($user);

    $sql = "SELECT editor FROM bcs_users WHERE id = '$user'";
    $result = bcs_query($sql) or bcs_error("<b>SQL ERROR</b> in <br>file " . __FILE__ . " on line " . __LINE__ . "<br><br><b>" . $query . "</b><br><br>" . mysql_error());
    $row = mysql_fetch_array($result);


    return $row["editor"] == 0 ? "Player" : "Ironbane Team";


}

function memberLink($user, $special="") {
    global $language;

    $user = parseToDB($user);

    if (is_numeric($user)) {
        $sql = "SELECT name, editor FROM bcs_users WHERE id = '$user'";
    } else {
        $sql = "SELECT name, editor FROM bcs_users WHERE name = '$user'";
    }

    $result = bcs_query($sql) or bcs_error("<b>SQL ERROR</b> in <br>file " . __FILE__ . " on line " . __LINE__ . "<br><br><b>" . $query . "</b><br><br>" . mysql_error());
    $row = mysql_fetch_array($result);

    if ($user <= 0) {
        return "Guest";
    } else {
        $color = !$row["editor"] ? "#1beee7" : "#BF96D8";
        $special .= ' style="color:'.$color.'"';
        return '<a href="user.php?n=' . $row["name"] . '"' . $special . '>' . $row["name"] . '</a>';
    }
}

function memberName($user) {
    $user = parseToDB($user);
    $sql = "SELECT name FROM bcs_users WHERE id = '$user'";
    $result = bcs_query($sql) or bcs_error("<b>SQL ERROR</b> in <br>file " . __FILE__ . " on line " . __LINE__ . "<br><br><b>" . $query . "</b><br><br>" . mysql_error());
    $row = mysql_fetch_array($result);
    return $row["name"];
}

function getRow($query) {
    $result = bcs_query($query) or bcs_error("<b>SQL ERROR</b> in <br>file " . __FILE__ . " on line " . __LINE__ . "<br><br><b>" . $query . "</b><br><br>" . mysql_error());
    return mysql_fetch_array($result);
}


function post_parse($text, $parse_smilies=1) {

    global $smilies_list, $smilies_repl, $external;

    // E-mail
    $text = preg_replace("#\[email\]([a-z0-9&\-_.]+?@[\w\-]+\.([\w\-\.]+\.)?[\w]+)\[/email\]#si", "<a href=\"mailto:$1\">$1</a>", $text);

    // Image

    $text = preg_replace("/\[img\](.+?)\[\/img\]/", "<img src=\"$1\" alt=\"User posted image\" >", $text);

    // Links

//    $text = preg_replace("#\[url\]([\w]+?://[^ \"\n\r\t<]*?)\[/url\]#si", "<a href=\"$1\">$1</a>", $text);
//
//    $text = preg_replace("#\[url\]((www|ftp)\.[^ \"\n\r\t<]*?)\[/url\]#si", "<a href=\"$1\">$1</a>", $text);
//
//    $text = preg_replace("#\[url=([\w]+?://[^ \"\n\r\t<]*?)\](.*?)\[/url\]#si", "<a href=\"$1\">$2</a>", $text);
//
//    $text = preg_replace("#\[url=((www|ftp)\.[^ \"\n\r\t<]*?)\](.*?)\[/url\]#si", "<a href=\"$1\">$2</a>", $text);

    $text = preg_replace("#\[url=(.*?)\](.*?)\[/url\]#si", "<a href=\"$1\" target=\"_blank\">$2</a>", $text);

    //$text = preg_replace("#\[time=(.*?)\]#sie", "'.createDateSelf($1, 3).'", $text);

    $text = preg_replace("(\[time=(.+?)\])e", "'('.createDateSelf('$1', 3).')'", $text);

    //$text = preg_replace("(\[item=(.+?)\])e", "'['.createitemlinkID('$1').']'", $text);

    if ( function_exists('createitemlinkID') ) {

        $text = preg_replace("(\[(.+?)\])e", "''.createitemlinkID('$1').''", $text);
    }

//    $text = preg_replace_callback(
//                "#\[time=(.*?)\]#sie",
//                "createDateSelf",
//                $text);

//    $text = preg_replace("/([time=(.*?))/e",
//             "'\\1'.strtoupper('\\2').'\\3'",
//             $text);

    // Quote

    //$text = preg_replace("#\[quote=(.*?)\](.*?)\[\/quote\]#si", "<fieldset><legend><span class=\"gensmall\"><b>&nbsp;Quote: $1 &nbsp;</b></span></legend><span class=\"gensmall\"><br>$2</span></fieldset>",$text);

//	$text = preg_replace("#\[quote=(.*?)\](.*?)\[\/quote\]#si", "<table width=\"100%\" border=\"0\"><tr><td class=\"row4\"><span class=\"gensmall\"><b>&nbsp;Quote: $1 &nbsp;</b></span></td></tr><tr><td class=\"row4\"><span class=\"gensmall\"><br>$2</span></td></table>",$text);

    //$text = preg_replace("#\[quote=(.*?)\](.*?)\[\/quote\]#si", '<table width="90%" cellspacing="1" cellpadding="3" border="0" align="center"><tr><td><span class="genmed"><b>$1 wrote:</b></span></td></tr><tr><td class="quote">$2</td></tr></table>',$text);

    $quote_open = '</span><table width="90%" cellspacing="1" cellpadding="3" border="0" align="center"><tr> <td><span class="genmed"><b>Quote:</b></span></td></tr><tr><td class="quote">';

    $quote_close = '</td></tr></table><span class="postbody">';

    $quote_open_username = '</span><table width="90%" cellspacing="1" cellpadding="3" border="0" align="center"><tr><td><span class="genmed"><b>$1 wrote:</b></span></td></tr><tr><td class="quote">';



    // [QUOTE] and [/QUOTE] for posting replies with quote, or just for quoting stuff.

    $text = str_replace("[quote]", $quote_open, $text);

    $text = str_replace("[/quote]", $quote_close, $text);



    // New one liner to deal with opening quotes with usernames...

    // replaces the two line version that I had here before..

    $text = preg_replace("#\[quote=(.*?)\]#si", $quote_open_username, $text);



    // Quote Anonymous

    //$text = preg_replace("#\[quote\](.*?)\[\/quote\]#si", "<table width=\"100%\" border=\"1\"><tr><td class=\"row1\"><span class=\"gensmall\"><b>&nbsp;Quote&nbsp;</b></span></td></tr><tr><td class=\"row2\"><span class=\"gensmall\"><br>$1</span></td></table>",$text);

    // Code

    $text = preg_replace("/\[code\](.+?)\[\/code\]/", "<fieldset><legend><span class=\"gensmall\"><b>&nbsp;Code: &nbsp;</b></span></legend><br><font face=\"fixedsys\" color=\"green\">$1</font></fieldset>", $text);



    // Bold, Underline, Italic

    $text = preg_replace("#\[b\](.+?)\[\/b\]#si", "<b>$1</b>", $text);
    $text = preg_replace("#\[i\](.+?)\[\/i\]#si", "<i>$1</i>", $text);
    $text = preg_replace("#\[u\](.+?)\[\/u\]#si", "<u>$1</u>", $text);
    $text = preg_replace("#\[B\](.+?)\[\/B\]#si", "<b>$1</b>", $text);
    $text = preg_replace("#\[I\](.+?)\[\/I\]#si", "<i>$1</i>", $text);
    $text = preg_replace("#\[U\](.+?)\[\/U\]#si", "<u>$1</u>", $text);
    $text = preg_replace("#\[s\](.+?)\[\/s\]#si", "<s>$1</s>", $text);
    $text = preg_replace("#\[S\](.+?)\[\/S\]#si", "<S>$1</S>", $text);

    $text = preg_replace("#\[h(.+?)\]#si", "<h$1>", $text);
	$text = preg_replace("#\[\/h(.+?)\]#si", "</h$1>", $text);
    // Font size & color

    $text = preg_replace("#\[color=(.+?)\](.+?)\[\/color\]#si", "<font color=\"$1\">$2</font>", $text);

    $text = preg_replace("#\[size=(.+?)\](.+?)\[\/size\]#si", "<div style=\"display:inline;font-size:$1px\">$2</div>", $text);



    // Automatic links

    // $text = eregi_replace("(^|[\n\r\t])((http(s?)://)(www\.)?([a-z0-9_-]+(\.[a-z0-9_-]+)+)(/[^/ \n\r]*)*)", "<a href=\"\\2\" target=\"_blank\">\\2</a>", $text);

    // $text = eregi_replace("(^|[\n\r\t])((http(s?)://)(www\.)?([a-z0-9_-]+([a-z0-9_-]+)+)(/[^/ \n\r]*)*)", "<a href=\"\\2\" target=\"_blank\">\\2</a>", $text);

    // $text = eregi_replace("(^|[\n\r\t])([a-z_-][a-z0-9\._-]*@[a-z0-9_-]+(\.[a-z0-9_-]+)+)", "<a href=\"mailto:\\2\">\\2</a>", $text);

    // $text = eregi_replace("(^|[\n\r\t])(www\.([a-z0-9_-]+(\.[a-z0-9_-]+)+)(/[^/ \n\r]*)*)", "<a href=\"http://\\2\" target=\"_blank\">\\2</a>", $text);

    // $text = eregi_replace("(^|[\n\r\t])(www\.([a-z0-9_-]+([a-z0-9_-]+)+)(/[^/ \n\r]*)*)", "<a href=\"http://\\2\" target=\"_blank\">\\2</a>", $text);

    // $text = eregi_replace("(^|[\n\r\t])(ftp://([a-z0-9_-]+(\.[a-z0-9_-]+)+)(/[^/ \n\r]*)*)", "<a href=\"\\2\" target=\"_blank\">\\2</a>", $text);

    // $text = eregi_replace("(^|[\n\r\t])(ftp://([a-z0-9_-]+([a-z0-9_-]+)+)(/[^/ \n\r]*)*)", "<a href=\"\\2\" target=\"_blank\">\\2</a>", $text);

    // $text = eregi_replace("(^|[\n\r\t])(ftp\.([a-z0-9_-]+(\.[a-z0-9_-]+)+)(/[^/ \n\r]*)*)", "<a href=\"\\2\" target=\"_blank\">\\2</a>", $text);



    // Maakt lijst met rondje
    $text = preg_replace("#\[list\](.*?)\[/list\]#si", "<ul>\\1</ul>", $text);
    $text = preg_replace("#\[ul\](.*?)\[/ul\]#si", "<ul>\\1</ul>", $text);
    $text = preg_replace("#\[li\](.*?)\[/li\]#si", "<li>\\1</li>", $text);
    // Maakt lijst met getallen

    $text = preg_replace("#\[list=1\](.*?)\[/list\]#si", "<ol type=\"1\">\\1</ol>", $text);

    // Maakt lijst met letters

    $text = preg_replace("#\[list=a\](.*?)\[/list\]#si", "<ol type=\"a\">\\1</ol>", $text);

    // Dit zorgt ervoor dat er telkens een optie bij komt

    $text = str_replace("[*]", "<li>", $text); // <li> maken met [li]

//	$text = str_replace("[li]", "<li>", $text);
//	$text = str_replace("[/li]", "</li>", $text);
//	$text = str_replace("[ul]", "<ul>", $text);
//	$text = str_replace("[/ul]", "</ul>", $text);
//	$text = str_replace("[ol]", "<ol>", $text);
//	$text = str_replace("[/ol]", "</ol>", $text);
    // Text alignment

    $text = preg_replace("/\[right\](.+?)\[\/right\]/", "<div align=\"right\">$1</div>", $text);

    if ($parse_smilies == 1) {

        $text = parse_smilies($text);

    }

    $text = make_clickable($text);

    // Lines

    $text = nl2br($text, false);
    //$text = strtr($text, array("\r\n" => '<br>', "\r" => '<br>', "\n" => '<br>')); ;

    return $text;

}

function _make_url_clickable_cb($matches) {
	$ret = '';
	$url = $matches[2];

	if ( empty($url) )
		return $matches[0];
	// removed trailing [.,;:] from URL
	if ( in_array(substr($url, -1), array('.', ',', ';', ':')) === true ) {
		$ret = substr($url, -1);
		$url = substr($url, 0, strlen($url)-1);
	}
	return $matches[1] . "<a href=\"$url\" rel=\"nofollow\" target=\"_new\">$url</a>" . $ret;
}

function make_clickable($ret) {
	$ret = ' ' . $ret;
	// in testing, using arrays here was found to be faster
	$ret = preg_replace_callback('#([\s>])([\w]+?://[\w\\x80-\\xff\#$%&~/.\-;:=,?@\[\]+]*)#is', '_make_url_clickable_cb', $ret);

	// this one is not in an array because we need it to run last, for cleanup of accidental links within links
	$ret = preg_replace("#(<a( [^>]+?>|>))<a [^>]+?>([^>]+?)</a></a>#i", "$1$3</a>", $ret);
	$ret = trim($ret);
	return $ret;
}

function parse_smilies($text) {
    global $smilies_list, $smilies_repl, $external;
        // Smilies

        $smilies_explo1 = explode(" ", $smilies_list);

        $smilies_explo2 = explode(" ", $smilies_repl);

        $smilies_tot = count($smilies_explo1);

        for ($x = 0; $x < $smilies_tot; $x++) {

            $text = str_replace($smilies_explo1[$x], "<img src=\"" . ($external ? "http://www.ironbane.com/" : "") . "plugins/forum/smilies/" . $smilies_explo2[$x] . "\" alt=\"" . $smilies_explo1[$x] . "\">", $text);

        }
        return $text;
}


function getRowCount($table) {
    $query = "SELECT count(id) FROM " . $table . "";
    $result = bcs_query($query) or bcs_error("<b>SQL ERROR</b> in <br>file " . __FILE__ . " on line " . __LINE__ . "<br><br><b>" . $query . "</b><br><br>" . mysql_error());
    $total = mysql_fetch_array($result);
    return $total[0];

}

function getNewestMember() {
    $query2 = "SELECT id FROM bcs_users ORDER BY reg_date DESC LIMIT 1";
    $result2 = bcs_query($query2) or bcs_error("<b>SQL ERROR</b> in <br>file " . __FILE__ . " on line " . __LINE__ . "<br><br><b>" . $query . "</b><br><br>" . mysql_error());
    $row3 = mysql_fetch_array($result2);

    $new_member = memberLink($row3["id"]);

    return $new_member;

}


function getNumberOfOnlineMembers() {


    $query2 = "SELECT count(id) FROM bcs_users WHERE online = 1";

    $result2 = bcs_query($query2) or bcs_error("<b>SQL ERROR</b> in <br>file " . __FILE__ . " on line " . __LINE__ . "<br><br><b>" . $query . "</b><br><br>" . mysql_error());

    $total = mysql_fetch_array($result2);

    return $total[0];

}

function getListOfLastDayVisitors() {

    global $time;


    $query = "SELECT id from bcs_users WHERE last_session > " . ($time - 86400) . " ORDER BY last_session";
    //echo $query;
    $result = bcs_query($query) or bcs_error("<b>SQL ERROR</b> in <br>file " . __FILE__ . " on line " . __LINE__ . "<br><br><b>" . $query . "</b><br><br>" . mysql_error());
    $visitors = array();
    $totalvisitors = mysql_num_rows($result);
    $visitors_to_show = 10;
    $number = min($totalvisitors, $visitors_to_show);
    for($i = 0; $i < $number ; $i++) {
        $row = mysql_fetch_array($result);
        $visitors[] = memberLink($row["id"]); 
    }

    $visitors = implode(', ', $visitors);
    if($totalvisitors > $visitors_to_show) {
        $visitors .= " and ". ($totalvisitors - $visitors_to_show) . " more";
    }


    return empty($visitors) ? "None" : $visitors;
}

function getArrayOfOnlineMembers($criteria="") {
    global $time, $onlinePeriod;
    $list = array();
    $query = "SELECT id, last_page from bcs_users WHERE last_session > " . ($time - $onlinePeriod) . "";
    $result = bcs_query($query) or bcs_error("<b>SQL ERROR</b> in <br>file " . __FILE__ . " on line " . __LINE__ . "<br><br><b>" . $query . "</b><br><br>" . mysql_error());
    for ($y = 0; $y < mysql_num_rows($result); $y++) {
        $row = mysql_fetch_array($result);

        if ($criteria != "") {
            if (!strpos($row["last_page"], $criteria))
                continue;
        }
        array_push($list, $row["id"]);
    }
    return $list;
}

function getRawListOfOnlineMembers($criteria="") {

    $list = getArrayOfOnlineMembers($criteria);

    $newlist = array();

    foreach ($list as $key) {
        array_push($newlist,
            array('id' => $key,
            'name' => membername($key))
        );
    }

    return $newlist;

}

function getListOfOnlineMembers($glue, $criteria="") {

    $list = getArrayOfOnlineMembers($criteria);

    $newlist = array();

    foreach ($list as $key) {
        array_push($newlist, memberlink($key));
    }

    return empty($list) ? "None" : implode($glue, $newlist);

}


function getTotalUserPosts($user) {
    $user = parseToDB($user);
    $query2 = "SELECT count(id) FROM forum_posts WHERE user = '$user'";
    $result2 = bcs_query($query2) or bcs_error("<b>SQL ERROR</b> in <br>file " . __FILE__ . " on line " . __LINE__ . "<br><br><b>" . $query . "</b><br><br>" . mysql_error());
    $total = mysql_fetch_array($result2);

    return $total[0];

}



function getAgeFromBirthday($birthday) {

//    You can activate it with:

//    echo birthday("DD-MM-YYYY");



    list($day, $month, $year) = explode("-", $birthday);

    $year_diff = date("Y") - $year;

    $month_diff = date("m") - $month;

    $day_diff = date("d") - $day;

    if ($month_diff < 0)

        $year_diff--;

    elseif (($month_diff == 0) && ($day_diff < 0))

        $year_diff--;

    return $year_diff;

}


function getNumberOfNewMessages() {

    global $userdata;

    // TODO bad selection, will cause bad results sooner or later

    $count = 0;

    $query = 'SELECT * FROM forum_topics WHERE (private_from = ' . $userdata["id"] . ' OR private_chatters LIKE \'' . $userdata[name] . '\') AND private = 1';

    $result = bcs_query($query) or bcs_error("<b>SQL ERROR</b> in <br>file " . __FILE__ . " on line " . __LINE__ . "<br><br><b>" . $query . "</b><br><br>" . mysql_error());

    for ($y = 0; $y < mysql_num_rows($result); $y++) {

        $row = mysql_fetch_array($result);



        $query2 = 'SELECT * FROM forum_posts WHERE time > ' . $userdata["previous_session"] . ' AND user != ' . $userdata[id] . ' AND topic_id = ' . $row[id] . '';

        $result2 = bcs_query($query2) or bcs_error("<b>SQL ERROR</b> in <br>file " . __FILE__ . " on line " . __LINE__ . "<br><br><b>" . $query . "</b><br><br>" . mysql_error());

        $count += mysql_num_rows($result2);

    }

    return $count;

}



function timeAgo($past) {

    global $time;

    $since = $time - $past;

    return timeSince($since);

}



function timeSince($since) {

    $chunks = array(

        array(60 * 60 * 24 * 365, 'year'),

        array(60 * 60 * 24 * 30, 'month'),

        array(60 * 60 * 24 * 7, 'week'),

        array(60 * 60 * 24, 'day'),

        array(60 * 60, 'hour'),

        array(60, 'minute'),

        array(1, 'second')

    );



    for ($i = 0, $j = count($chunks); $i < $j; $i++) {

        $seconds = $chunks[$i][0];

        $name = $chunks[$i][1];

        if (($count = floor($since / $seconds)) != 0) {

            break;

        }

    }



    $print = ($count == 1) ? '1 ' . $name : "$count {$name}s";

    return $print;

}



function random_float($min, $max) {

    return ($min + lcg_value() * (abs($max - $min)));

}



function ChooseRandom($list, $del="\n") {
    //global $s_admin;

    $explo = explode($del, $list);

//    if ( $s_admin ) {
//        echo "alert('rand(0, ".(count($explo) - 1).")');";
//    }

    $rand_name = $explo[mt_rand(0, (count($explo) - 1))];

    return trim($rand_name);

}



function ChooseRandomOnce($list, $del="\n") {

    $id = md5($list);



    if (!isset($_SESSION[$id])) {

        $explo = explode($del, $list);

        $_SESSION[$id] = $explo[rand(0, (count($explo) - 1))];

    }



    return $_SESSION[$id];

}



function WasLucky($maxchance) {

    return rand(1, $maxchance) == 1;

}



function WasLucky100($chance) {

    return $chance >= mt_rand(1, 100);

}



function ShowIfLucky($text, $maxchance) {

    return WasLucky($maxchance) ? $text : '';

}



function ShowOnce($text) {

    $id = md5($text);

    if (!isset($_SESSION[$id])) {

        $_SESSION[$id] = 1;

        return $text;

    }

    return "";

}




function AorAn($thing) {
    return ((preg_match('/^[aeiou]|s\z/i', strtolower($thing))) ? "an" : "a") . " ".$thing;
}



function SorNot($count) {

    return $count != 1 ? "s" : "";

}



function makeurlbutton($text, $url, $class="lightoption") {
    return '<input type="button" onclick="location.href=\'' . $url . '\'" value="' . $text . '" class="' . $class . '">';
}



function wrapColor($text, $color) {
    return '<font color=' . $color . '>' . $text . '</font>';
}




function debugCompare($a, $b) {

    if ($a == $b) {

        echo "Compare: '" . $a . "' == '" . $b . "'<br>";

    } else {

        echo "Compare: '" . $a . "' != '" . $b . "'<br>";

    }

}



function codeslashes($text) {
    $text = str_replace("'", "&#39;", $text);
    return $text;
}

function shuffle_assoc( $array )
{
   $keys = array_keys( $array );
   shuffle( $keys );
   return array_merge( array_flip( $keys ) , $array );
}

function getBrowser()
{
    $u_agent = $_SERVER['HTTP_USER_AGENT'];
    $bname = 'Unknown';
    $platform = 'Unknown';
    $version= "";

    //First get the platform?
    if (preg_match('/linux/i', $u_agent)) {
        $platform = 'linux';
    }
    elseif (preg_match('/macintosh|mac os x/i', $u_agent)) {
        $platform = 'mac';
    }
    elseif (preg_match('/windows|win32/i', $u_agent)) {
        $platform = 'windows';
    }

    // Next get the name of the useragent yes seperately and for good reason
    if(preg_match('/MSIE/i',$u_agent) && !preg_match('/Opera/i',$u_agent))
    {
        $bname = 'Internet Explorer';
        $ub = "MSIE";
    }
    elseif(preg_match('/Firefox/i',$u_agent))
    {
        $bname = 'Mozilla Firefox';
        $ub = "Firefox";
    }
    elseif(preg_match('/Chrome/i',$u_agent))
    {
        $bname = 'Google Chrome';
        $ub = "Chrome";
    }
    elseif(preg_match('/Safari/i',$u_agent))
    {
        $bname = 'Apple Safari';
        $ub = "Safari";
    }
    elseif(preg_match('/Opera/i',$u_agent))
    {
        $bname = 'Opera';
        $ub = "Opera";
    }
    elseif(preg_match('/Netscape/i',$u_agent))
    {
        $bname = 'Netscape';
        $ub = "Netscape";
    }

    // finally get the correct version number
    $known = array('Version', $ub, 'other');
    $pattern = '#(?<browser>' . join('|', $known) .
    ')[/ ]+(?<version>[0-9.|a-zA-Z.]*)#';
//    if (!preg_match_all($pattern, $u_agent, $matches)) {
//        // we have no matching number just continue
//    }


    return array(
        'userAgent' => $u_agent,
        'name_long'      => $bname,
        'name'      => $ub,
        'platform'  => $platform,
        'pattern'    => $pattern
    );
}

function AddTeamActionSelf($action, $link, $previous_data) {
    global $userdata;
    AddTeamAction($userdata["id"], $action, $link, $previous_data);
}

function AddTeamAction($user, $action, $link, $previous_data) {
    global $time;
    $action = parseToDB($action, true);
    $link = parseToDB($link);
    $previous_data = parseToDB($previous_data, true);
    $query = "INSERT INTO ib_team_actions (user, action, link, previous_data, time) VALUES('$user', '$action', '$link', '$previous_data', '$time')";
    $result = bcs_query($query) or bcs_error("<b>SQL ERROR</b> in <br>file " . __FILE__ . " on line " . __LINE__ . "<br><br><b>" . $query . "</b><br><br>" . mysql_error());
}


function create_blank($width, $height){
    //create image with specified sizes
    $image = imagecreatetruecolor($width, $height);
    //saving all full alpha channel information
    imagesavealpha($image, true);
    //setting completely transparent color
    $transparent = imagecolorallocatealpha($image, 0, 0, 0, 127);
    //filling created image with transparent color
    imagefill($image, 0, 0, $transparent);
    return $image;
}

function writeChatMessage($author, $text, $type) {
    global $time;
    $text = parseToDB($text);
    $query = "INSERT INTO bcs_chat (author, line, type, time) VALUES($author, '$text', $type, $time)";
    $result = bcs_query($query) or bcs_error("<b>SQL ERROR</b> in <br>file " . __FILE__ . " on line " . __LINE__ . "<br><br><b>" . $query . "</b><br><br>" . mysql_error());
}

function randomPassword() {
    $alphabet = "abcdefghijklmnopqrstuwxyzABCDEFGHIJKLMNOPQRSTUWXYZ0123456789";
    $pass = array(); //remember to declare $pass as an array
    $alphaLength = strlen($alphabet) - 1; //put the length -1 in cache
    for ($i = 0; $i < 8; $i++) {
        $n = rand(0, $alphaLength);
        $pass[] = $alphabet[$n];
    }
    return implode($pass); //turn the array into a string
}

function passwordHash($password) {
    global $crypt_salt;
    return md5($crypt_salt.$password);
}

?>
