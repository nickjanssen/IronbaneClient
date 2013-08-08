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


if (!defined('BCS')) {
    die("ERROR");
}

$nav_extra = "";

if ( $userdata["pending_editor"] ) $s_editor = false;

if ( isset($_GET['action']) ) {
    $action = parseToDB($_GET['action']);
}
else {
    $action = null;
}

if ( isset($_GET['board']) ) {
    $board = parseToDB($_GET['board']);

    // View all topics from a board_id
    if (!is_numeric($board) && $board !== "pt" && $board !== "rt" && $board !== "ut" && $board !== "mt" && $board !== "up") {
        die();
    }
}

if ( isset($_GET['topic']) ) {
    $topic = (int)parseToDB($_GET['topic']);
}

if ( isset($_GET['post']) ) {
    $post = (int)parseToDB($_GET['post']);
}

if ( isset($_GET['quote_p'])) {
    $quote_p = (int)parseToDB($_GET['quote_p']);
}

if ( isset($_POST['submit'])) {
    $submit = true;
}
else {
    $submit = false;
}

$changemain = 0;
$nposts = 0;

$posts_per_page = 20;



// Make a rate list
$lista = "agree,artistic,badspelling,disagree,dumb,friendly,funny,goodidea,informative,thanks,unfriendly,useful";
$listb = "Agree,Artistic,Bad Spelling,Disagree,Dumb,Friendly,Funny,Good Idea,Informative,Thanks,Unfriendly,Useful";
$listc = explode(',', $lista);
$listd = explode(',', $listb);
$liste = "is usually Agreed with,is very Artistic,has Spelling Problems,is usually Disagreed with,is Dumb,is very Friendly,is a Comedian,is full of Good Ideas,is very Informative,is Liked by everyone,is very Useful";
$listf = explode(',', $liste);

$max_title_length = 30;



$smilies_explo1 = explode(" ", $smilies_list);
$smilies_explo2 = explode(" ", $smilies_repl);
$smilies_tot = count($smilies_explo1);




if ($action === "deletetopic") {
    if (!$s_editor) {
        die();
    }

    $confirm_del = isset($_POST["confirm_del"]) ? true : false;

    if ($confirm_del) {
        $query = "SELECT board_id FROM forum_topics WHERE id = '$topic'";
        $result = bcs_query($query) or bcs_error("<b>SQL ERROR</b> in <br>file " . __FILE__ . " on line " . __LINE__ . "<br><br><b>" . $query . "</b><br><br>" . mysql_error());
        $row = mysql_fetch_array($result);
        $board = $row["board_id"];

        $query = "DELETE FROM forum_topics WHERE id = '$topic'";
        $result = bcs_query($query) or bcs_error("<b>SQL ERROR</b> in <br>file " . __FILE__ . " on line " . __LINE__ . "<br><br><b>" . $query . "</b><br><br>" . mysql_error());
        $query = "DELETE FROM forum_posts WHERE topic_id = '$topic'";
        $result = bcs_query($query) or bcs_error("<b>SQL ERROR</b> in <br>file " . __FILE__ . " on line " . __LINE__ . "<br><br><b>" . $query . "</b><br><br>" . mysql_error());

        bcs_die("Topic was succesfully deleted.", "index.php?plugin=forum&amp;action=board&amp;board=" . $board);
    } else {
        bcs_die("Are you sure you wish to delete this topic ?<br /><br /><form action=\"index.php?plugin=forum&amp;action=deletetopic&amp;topic=" . $topic . "\" method=\"POST\"><input type=\"submit\" name=\"confirm_del\" value=\"Delete\" class=\"mainoption\"/></form>", "none");
    }
}elseif ($action === "stickytopic") {
    if (!$s_editor) {
        die();
    }
    $confirm_sticky = isset($_POST["confirm_sticky"]) ? true : false;
    if ($confirm_sticky) {
        $query = "SELECT board_id FROM forum_topics WHERE id = '$topic'";
        $result = bcs_query($query) or bcs_error("<b>SQL ERROR</b> in <br>file " . __FILE__ . " on line " . __LINE__ . "<br><br><b>" . $query . "</b><br><br>" . mysql_error());
        $row = mysql_fetch_array($result);
        $board = $row["board_id"];

        $query = "UPDATE forum_topics SET sticky = 1 WHERE id = '$topic'";
        $result = bcs_query($query) or bcs_error("<b>SQL ERROR</b> in <br>file " . __FILE__ . " on line " . __LINE__ . "<br><br><b>" . $query . "</b><br><br>" . mysql_error());

        bcs_die("Topic was succesfully stickied.", "index.php?plugin=forum&amp;action=board&amp;board=" . $board);
    } else {
        bcs_die("Are you sure you wish to sticky this topic ?<br /><br /><form action=\"index.php?plugin=forum&amp;action=stickytopic&amp;topic=" . $topic . "\" method=\"POST\"><input class=\"mainoption\" type=\"submit\" name=\"confirm_sticky\" value=\"Make sticky\" /></form>", "none");
    }
}elseif ($action === "unstickytopic") {
    if (!$s_editor) {
        die();
    }
    $confirm_unsticky = isset($_POST["confirm_unsticky"]) ? true : false;
    if ($confirm_unsticky) {
        $query = "SELECT board_id FROM forum_topics WHERE id = '$topic'";
        $result = bcs_query($query) or bcs_error("<b>SQL ERROR</b> in <br>file " . __FILE__ . " on line " . __LINE__ . "<br><br><b>" . $query . "</b><br><br>" . mysql_error());
        $row = mysql_fetch_array($result);
        $board = $row["board_id"];

        $query = "UPDATE forum_topics SET sticky = 0 WHERE id = '$topic'";
        $result = bcs_query($query) or bcs_error("<b>SQL ERROR</b> in <br>file " . __FILE__ . " on line " . __LINE__ . "<br><br><b>" . $query . "</b><br><br>" . mysql_error());

        bcs_die("Topic was succesfully unstickied.", "index.php?plugin=forum&amp;action=board&amp;board=" . $board);
    } else {
        bcs_die("Are you sure you wish to unsticky this topic ?<br /><br /><form action=\"index.php?plugin=forum&amp;action=unstickytopic&amp;topic=" . $topic . "\" method=\"POST\"><input class=\"mainoption\" type=\"submit\" name=\"confirm_unsticky\" value=\"Make unsticky\" /></form>", "none");
    }
} elseif ($action === "reply" || $action === "editpost") {





    requireLogin("forum");

    // Reply a post to a topic
    $is_first_post = false;
    $postdata = null;
    if ($action === "editpost") {

        // Get more info on the post
        if ( !isset($post) ) die("No post given!");

        $query = "SELECT * FROM forum_posts WHERE id = '$post'";
        $result = bcs_query($query) or bcs_error("<b>SQL ERROR</b> in <br>file " . __FILE__ . " on line " . __LINE__ . "<br><br><b>" . $query . "</b><br><br>" . mysql_error());
        $postdata = mysql_fetch_array($result);
        $topic = $postdata["topic_id"];


        $query2 = "SELECT private FROM forum_topics WHERE id = '$topic'";
        $result2 = bcs_query($query2) or bcs_error("<b>SQL ERROR</b> in <br>file " . __FILE__ . " on line " . __LINE__ . "<br><br><b>" . $query . "</b><br><br>" . mysql_error());
        $row2 = mysql_fetch_array($result2);

        if ((int)$row2['private'] === 1)
            $board = "pt";

        if ($postdata["user"] !== $userdata["id"] && !$s_editor)
            die('not authorised');

        $query = "SELECT id FROM forum_posts WHERE topic_id = '$topic' ORDER BY time ASC LIMIT 1";
        $result = bcs_query($query) or bcs_error("<b>SQL ERROR</b> in <br>file " . __FILE__ . " on line " . __LINE__ . "<br><br><b>" . $query . "</b><br><br>" . mysql_error());
        $row = mysql_fetch_array($result);

        if ($row["id"] === $postdata["id"]) {
            $is_first_post = true;
        }
    }

    if ( isset($board) && $board !== "pt") {
        $query3 = "SELECT * FROM forum_boards WHERE id = '$board'";
        $result3 = bcs_query($query3) or bcs_error("<b>SQL ERROR</b> in <br>file " . __FILE__ . " on line " . __LINE__ . "<br><br><b>" . $query . "</b><br><br>" . mysql_error());
        $row3 = mysql_fetch_array($result3);

        if ( $row3["modonly"] === 1 && $s_editor === 0 ) die("no access");


        $boardname = $row3["name"];
    } else {
        $boardname = "Private Topics";
    }

    // Are we posting a new topic ?
    if (empty($topic)) {
        // Yes
        $newtopic = 1;
        if (!is_numeric($board) && $board !== "pt") {
            die('error 1');
        }
        $is_first_post = true;
        $formtarget = "index.php?plugin=forum&amp;action=" . $action . "&board=" . $board;
    } else {

        $newtopic = 0;
        if (!is_numeric($topic)) {
            die('error 2');
        }

        $query = "SELECT * FROM forum_posts WHERE topic_id = '$topic'";
        $result = bcs_query($query) or bcs_error("<b>SQL ERROR</b> in <br>file " . __FILE__ . " on line " . __LINE__ . "<br><br><b>" . $query . "</b><br><br>" . mysql_error());


        $query4 = "SELECT title FROM forum_posts WHERE topic_id = '$topic' ORDER BY time ASC LIMIT 1";
        $result4 = bcs_query($query4) or bcs_error("<b>SQL ERROR</b> in <br>file " . __FILE__ . " on line " . __LINE__ . "<br><br><b>" . $query . "</b><br><br>" . mysql_error());
        $row4 = mysql_fetch_array($result4);
        $posttitle = $row4["title"];


        $formtarget = "index.php?plugin=forum&amp;action=" . $action . "&topic=" . $topic;

        if ($action === "editpost") {
            $formtarget .= "&post=" . $post;
        }

        $query2 = "SELECT * FROM forum_topics WHERE id = '$topic'";
        $result2 = bcs_query($query2) or bcs_error("<b>SQL ERROR</b> in <br>file " . __FILE__ . " on line " . __LINE__ . "<br><br><b>" . $query . "</b><br><br>" . mysql_error());
        $row2 = mysql_fetch_array($result2);
        $board = $row2["board_id"];
        $participants = $row2["private_chatters"];
        $query3 = "SELECT name FROM forum_boards WHERE id = '$board'";
        $result3 = bcs_query($query3) or bcs_error("<b>SQL ERROR</b> in <br>file " . __FILE__ . " on line " . __LINE__ . "<br><br><b>" . $query . "</b><br><br>" . mysql_error());
        $row3 = mysql_fetch_array($result3);
        $boardname = $row3["name"];

        if ((int)$row2['private'] === 1) {

            $board = "pt";
            $boardname = "Private Topics";

            // Check if we are allowed to view this topic
            $check_ok = 0;
            $par_list = explode(',', $row2["private_chatters"]);
            for ($x = 0; $x < count($par_list); $x++) {
                if ($userdata["name"] === $par_list[$x]) {
                    $check_ok = 1;
                }
            }
            // Check if we are the starter
            if ($row2["private_from"] === $userdata["id"]) {
                $check_ok = 1;
            }

            if (!$check_ok) {
                bcs_die('Sorry, you are not allowed to reply in this private topic.', 'back');
            }
        }

        $topicreview =     '
<table width="100%" border="0" cellpadding="0" cellspacing="0" align="center">
	<tr>
	  <td>

		<table border="0" cellpadding="5" cellspacing="0" width="100%" style="border-collapse:collapse;">
';

        $query = "SELECT * FROM forum_posts WHERE topic_id = '$topic' ORDER BY time DESC LIMIT 50";
        $tr_result = bcs_query($query) or bcs_error("<b>SQL ERROR</b> in <br>file " . __FILE__ . " on line " . __LINE__ . "<br><br><b>" . $query . "</b><br><br>" . mysql_error());
        for ($z = 0; $z < mysql_num_rows($tr_result); $z++) {
            $tr_row = mysql_fetch_array($tr_result);

            $topicreview .=     '

			<tr>
				<td class="row'.($z%2?1:2).'" width="200" valign="top"><span class="genmed">'.(timeAgo($tr_row["time"])).' ago by <b>'.memberLink($tr_row["user"]).'</b></span></td>
				<td class="row'.($z%2?1:2).'"><span class="genmed">'.post_parse($tr_row["content"]).'</span></td>
			</tr>
    ';
        }

        $topicreview .=     '
            </table>
         </td>
        </tr>
    </table>

';



        if ((int)$row2['private'] === 1) {
            //$indexlink = createLink("ironbane Forum", "index.php?plugin=forum") . " " . $ts . " " . createLink("Private Messages", "index.php?plugin=forum&amp;action=board&amp;board=pt") . " " . $ts . " " . createLink($row4[title], "index.php?plugin=forum&amp;action=topic&amp;topic=" . $topic) . " " . $ts . " " . $posttext;
        } else {
            //$indexlink = createLink("ironbane Forum", "index.php?plugin=forum") . " " . $ts . " " . createLink($row3[name], "index.php?plugin=forum&amp;action=board&amp;board=" . $row2[board_id]) . " " . $ts . " " . createLink($row4[title], "index.php?plugin=forum&amp;action=topic&amp;topic=" . $topic) . " " . $ts . " " . $posttext;
        }
    }

    if ($s_editor !== 1 && (int) $board === 7 && $newtopic === 1) {
        die('error 4');
    }

    if ($submit) {

        $safe_content = isset($_POST['message']) ? parseToDB($_POST['message']) : null;
        $safe_title = isset($_POST['subject']) ? parseToDB($_POST['subject']) : null;
        $safe_par = isset($_POST['participants']) ? parseToDB($_POST['participants']) : null;

        if ($board === "pt" && !$safe_par && $is_first_post) {
            bcs_die('Please enter participant names.', 'back');
        }

        if ($board === "pt") {
            // Check names
            $list = explode(',', $safe_par);
            if (count($list) > 10) {
                bcs_die('You can only invite max 10 participants.', 'back');
            }
            for ($x = 0; $x < count($list); $x++) {
                $query3 = "SELECT id FROM bcs_users WHERE name = '$list[$x]'";
                $result3 = bcs_query($query3) or bcs_error("<b>SQL ERROR</b> in <br>file " . __FILE__ . " on line " . __LINE__ . "<br><br><b>" . $query . "</b><br><br>" . mysql_error());
                if (mysql_num_rows($result3) === 0) {
                    bcs_die("The participant you entered named '" . $list[$x] . "' does not exist.", 'back');
                }
            }
        }

        if (!$safe_title && $newtopic) {
            bcs_die('Please make sure you enter a title.', 'back');
        }
        if (!$safe_content) {
            bcs_die('Please make sure you enter a message.', 'back');
        }


        if ($newtopic === 1) {

            // First insert a topic
            if ($board === "pt") {
                $query = "INSERT INTO forum_topics (board_id, private, private_chatters, private_from) VALUES('$board', 1, '$safe_par', '$userdata[id]')";
                $result = bcs_query($query) or bcs_error("<b>SQL ERROR</b> in <br>file " . __FILE__ . " on line " . __LINE__ . "<br><br><b>" . $query . "</b><br><br>" . mysql_error());
            } else {
                if ( !isset($board) || !is_numeric($board) ) die("bad board id");

                $query = "INSERT INTO forum_topics (board_id) VALUES('$board')";
                $result = bcs_query($query) or bcs_error("<b>SQL ERROR</b> in <br>file " . __FILE__ . " on line " . __LINE__ . "<br><br><b>" . $query . "</b><br><br>" . mysql_error());
            }

            $query3 = "SELECT id FROM forum_topics ORDER BY id DESC LIMIT 1";
            $result3 = bcs_query($query3) or bcs_error("<b>SQL ERROR</b> in <br>file " . __FILE__ . " on line " . __LINE__ . "<br><br><b>" . $query . "</b><br><br>" . mysql_error());
            $row3 = mysql_fetch_array($result3);
            $topic = $row3["id"];

        } else {
            // Get board id
            $query3 = "SELECT board_id FROM forum_topics WHERE id = '$topic'";
            $result3 = bcs_query($query3) or bcs_error("<b>SQL ERROR</b> in <br>file " . __FILE__ . " on line " . __LINE__ . "<br><br><b>" . $query . "</b><br><br>" . mysql_error());
            $row3 = mysql_fetch_array($result3);
            if (empty($board))
                $board = $row3["board_id"];
        }




        if ($action === "editpost") {

            // Get more info on the post
            if ( !isset($post) ) die("No post given!");

            $query = "UPDATE forum_posts SET title = '$safe_title', content = '$safe_content', lastedit_time = '$time', lastedit_count = lastedit_count + 1, lastedit_author = '$userdata[id]' WHERE id = '$post'";
            $result = bcs_query($query) or bcs_error("<b>SQL ERROR</b> in <br>file " . __FILE__ . " on line " . __LINE__ . "<br><br><b>" . $query . "</b><br><br>" . mysql_error());

            if ($board === "pt" && $newtopic === 1) {
                $query = "UPDATE forum_topics SET private_chatters = '$safe_par' WHERE id = '$topic'";
                $result = bcs_query($query) or bcs_error("<b>SQL ERROR</b> in <br>file " . __FILE__ . " on line " . __LINE__ . "<br><br><b>" . $query . "</b><br><br>" . mysql_error());
            }

        } else {
            $query = "INSERT INTO forum_posts (title, content, user, time, topic_id) VALUES('$safe_title', '$safe_content', '$userdata[id]', '$time', '$topic')";
            $result = bcs_query($query) or bcs_error("<b>SQL ERROR</b> in <br>file " . __FILE__ . " on line " . __LINE__ . "<br><br><b>" . $query . "</b><br><br>" . mysql_error());
        }



        // Don't update order for news comments
        if ($newtopic === 1 || $board !== 7) {
            $query = "UPDATE forum_topics SET time = '$time' WHERE id = '$topic'";
            $result = bcs_query($query) or bcs_error("<b>SQL ERROR</b> in <br>file " . __FILE__ . " on line " . __LINE__ . "<br><br><b>" . $query . "</b><br><br>" . mysql_error());
        }

        if ($board === "pt") {
            // Notify other person
            for ($x = 0; $x < count($list); $x++) {
                $query3 = "SELECT id, name, email, receive_email FROM bcs_users WHERE name = '$list[$x]'";
                $result3 = bcs_query($query3) or bcs_error("<b>SQL ERROR</b> in <br>file " . __FILE__ . " on line " . __LINE__ . "<br><br><b>" . $query . "</b><br><br>" . mysql_error());

                $row3 = mysql_fetch_array($result3);

                if ($row3["receive_email"]) {

                    mailto($row3["email"], $userdata["name"] . " has sent you a message", "
					Hey " . $row3["name"] . "!<br><br>

					" . $userdata["name"] . " has just sent you a message on Ironbane!<br>
					Please login and check your Private Topics to view the message.<br><br>

					You may also click on the following link to view your message:<br>
					http://www.ironbane.com/forum.php?action=topic&topic=" . $topic . "<br><br>

					See you soon!<br>
					IronBot");
                }
            }
        }

        bcs_die("Your post was " . ($action === "editpost" ? "edited" : "made") . " succesfully.", "forum.php?action=topic&topic=" . $topic);
    } else {
        $quote_c = "";
        if (isset($quote_p)) {
            $query = "SELECT user, content FROM forum_posts WHERE id = '$quote_p'";
            $result = bcs_query($query) or bcs_error("<b>SQL ERROR</b> in <br>file " . __FILE__ . " on line " . __LINE__ . "<br><br><b>" . $query . "</b><br><br>" . mysql_error());
            $row = mysql_fetch_array($result);

            $query2 = "SELECT name FROM bcs_users WHERE id = '$row[user]'";
            $result2 = bcs_query($query2) or bcs_error("<b>SQL ERROR</b> in <br>file " . __FILE__ . " on line " . __LINE__ . "<br><br><b>" . $query . "</b><br><br>" . mysql_error());
            $row2 = mysql_fetch_array($result2);
            $quote_c = "[quote=" . $row2["name"] . "]" . $row["content"] . "[/quote]\n\n";
        }

        $linktotopic = "";
        if (!$newtopic) {
            $linktotopic = '&nbsp;&raquo;&nbsp;<a href="forum.php?action=topic&topic=' . $topic . '" class="nav">' . $posttitle . '</a>';
        }

        $smilies_explo1 = explode(" ", $smilies_list);
        $smilies_explo2 = explode(" ", $smilies_repl);
        $smilies_tot = count($smilies_explo1);

        $b = 0;

        $smilie_columns = 4;

        $add = array();

        $smilies_col_content = "";

        for ($x = 0; $x < $smilies_tot; $x++) {

            if ( in_array($smilies_explo2[$x], $add) ) continue;

            $add[] = $smilies_explo2[$x];

            if ($b === 0) {
                $smilies_col_content .= '';
            }

            $smilies_col_content .= '<a href="javascript:emoticon(\'' . $smilies_explo1[$x] . '\')"><img src="plugins/forum/smilies/' . $smilies_explo2[$x] . '" border="0" alt="' . $smilies_explo1[$x] . '" title="' . $smilies_explo1[$x] . '" /></a>';

            if ($b === $smilie_columns - 1) {
                $smilies_col_content .= '';
                $b = -1;
            }
            $b++;
        }

        $startp = isset($_GET["startp"]) ? $_GET["startp"] : "";


        $c_head .= '
                <link href="config/editor.css" rel="Stylesheet" type="text/css" />
	<script src="config/editor.js" type="text/javascript"></script>';

        $c_main = '


<script language="JavaScript" type="text/javascript">
<!--
// bbCode control by
// subBlue design
// www.subBlue.com

// Startup variables
var imageTag = false;
var theSelection = false;

// Check for Browser & Platform for PC & IE specific bits
// More details from: http://www.mozilla.org/docs/web-developer/sniffer/browser_type.html
var clientPC = navigator.userAgent.toLowerCase(); // Get client info
var clientVer = parseInt(navigator.appVersion); // Get browser version

var is_ie = ((clientPC.indexOf("msie") !== -1) && (clientPC.indexOf("opera") === -1));
var is_nav = ((clientPC.indexOf(\'mozilla\')!==-1) && (clientPC.indexOf(\'spoofer\')===-1)
                && (clientPC.indexOf(\'compatible\') === -1) && (clientPC.indexOf(\'opera\')===-1)
                && (clientPC.indexOf(\'webtv\')===-1) && (clientPC.indexOf(\'hotjava\')===-1));
var is_moz = 0;

var is_win = ((clientPC.indexOf("win")!==-1) || (clientPC.indexOf("16bit") !== -1));
var is_mac = (clientPC.indexOf("mac")!==-1);

// Helpline messages
b_help = "Bold text: [b]text[/b]  (alt+b)";
i_help = "Italic text: [i]text[/i]  (alt+i)";
u_help = "Underline text: [u]text[/u]  (alt+u)";
q_help = "Quote text: [quote]text[/quote]  (alt+q)";
c_help = "Code display: [code]code[/code]  (alt+c)";
l_help = "List: [list]text[/list] (alt+l)";
o_help = "Ordered list: [list=]text[/list]  (alt+o)";
p_help = "Insert image: [img]http://image_url[/img]  (alt+p)";
w_help = "Insert URL: [url]http://url[/url] or [url=http://url]URL text[/url]  (alt+w)";
a_help = "Close all open bbCode tags";
s_help = "Font color: [color=red]text[/color]  Tip: you can also use color=#FF0000";
f_help = "Font size: [size=x-small]small text[/size]";

// Define the bbCode tags
bbcode = new Array();
bbtags = new Array(\'[b]\',\'[/b]\',\'[i]\',\'[/i]\',\'[u]\',\'[/u]\',\'[quote]\',\'[/quote]\',\'[code]\',\'[/code]\',\'[list]\',\'[/list]\',\'[list=]\',\'[/list]\',\'[img]\',\'[/img]\',\'[url]\',\'[/url]\');
imageTag = false;

// Shows the help messages in the helpline window
function helpline(help) {
	document.post.helpbox.value = eval(help + "_help");
}


// Replacement for arrayname.length property
function getarraysize(thearray) {
	for (i = 0; i < thearray.length; i++) {
		if ((thearray[i] === "undefined") || (thearray[i] === "") || (thearray[i] === null))
			return i;
		}
	return thearray.length;
}

// Replacement for arrayname.push(value) not implemented in IE until version 5.5
// Appends element to the array
function arraypush(thearray,value) {
	thearray[ getarraysize(thearray) ] = value;
}

// Replacement for arrayname.pop() not implemented in IE until version 5.5
// Removes and returns the last element of an array
function arraypop(thearray) {
	thearraysize = getarraysize(thearray);
	retval = thearray[thearraysize - 1];
	delete thearray[thearraysize - 1];
	return retval;
}


function checkForm() {

	formErrors = false;

	if (document.post.message.value.length < 2) {
		formErrors = "{L_EMPTY_MESSAGE}";
	}

	if (formErrors) {
		alert(formErrors);
		return false;
	} else {
		bbstyle(-1);
		//formObj.preview.disabled = true;
		//formObj.submit.disabled = true;
		return true;
	}
}

function emoticon(text) {
	var txtarea = document.post.message;
	text = \' \' + text + \' \';
	if (txtarea.createTextRange && txtarea.caretPos) {
		var caretPos = txtarea.caretPos;
		caretPos.text = caretPos.text.charAt(caretPos.text.length - 1) === \' \' ? text + \' \' : text;
		txtarea.focus();
	} else {
		txtarea.value  += text;
		txtarea.focus();
	}
}

function bbfontstyle(bbopen, bbclose) {
	var txtarea = document.post.message;

	if ((clientVer >= 4) && is_ie && is_win) {
		theSelection = document.selection.createRange().text;
		if (!theSelection) {
			txtarea.value += bbopen + bbclose;
			txtarea.focus();
			return;
		}
		document.selection.createRange().text = bbopen + theSelection + bbclose;
		txtarea.focus();
		return;
	}
	else if (txtarea.selectionEnd && (txtarea.selectionEnd - txtarea.selectionStart > 0))
	{
		mozWrap(txtarea, bbopen, bbclose);
		return;
	}
	else
	{
		txtarea.value += bbopen + bbclose;
		txtarea.focus();
	}
	storeCaret(txtarea);
}


function bbstyle(bbnumber) {
	var txtarea = document.post.message;

	donotinsert = false;
	theSelection = false;
	bblast = 0;

	if (bbnumber === -1) { // Close all open tags & default button names
		while (bbcode[0]) {
			butnumber = arraypop(bbcode) - 1;
			txtarea.value += bbtags[butnumber + 1];
			buttext = eval(\'document.post.addbbcode\' + butnumber + \'.value\');
			eval(\'document.post.addbbcode\' + butnumber + \'.value ="\' + buttext.substr(0,(buttext.length - 1)) + \'"\');
		}
		imageTag = false; // All tags are closed including image tags :D
		txtarea.focus();
		return;
	}

	if ((clientVer >= 4) && is_ie && is_win)
	{
		theSelection = document.selection.createRange().text; // Get text selection
		if (theSelection) {
			// Add tags around selection
			document.selection.createRange().text = bbtags[bbnumber] + theSelection + bbtags[bbnumber+1];
			txtarea.focus();
			theSelection = \'\';
			return;
		}
	}
	else if (txtarea.selectionEnd && (txtarea.selectionEnd - txtarea.selectionStart > 0))
	{
		mozWrap(txtarea, bbtags[bbnumber], bbtags[bbnumber+1]);
		return;
	}

	// Find last occurance of an open tag the same as the one just clicked
	for (i = 0; i < bbcode.length; i++) {
		if (bbcode[i] === bbnumber+1) {
			bblast = i;
			donotinsert = true;
		}
	}

	if (donotinsert) {		// Close all open tags up to the one just clicked & default button names
		while (bbcode[bblast]) {
				butnumber = arraypop(bbcode) - 1;
				txtarea.value += bbtags[butnumber + 1];
				buttext = eval(\'document.post.addbbcode\' + butnumber + \'.value\');
				eval(\'document.post.addbbcode\' + butnumber + \'.value ="\' + buttext.substr(0,(buttext.length - 1)) + \'"\');
				imageTag = false;
			}
			txtarea.focus();
			return;
	} else { // Open tags

		if (imageTag && (bbnumber !== 14)) {		// Close image tag before adding another
			txtarea.value += bbtags[15];
			lastValue = arraypop(bbcode) - 1;	// Remove the close image tag from the list
			document.post.addbbcode14.value = "Img";	// Return button back to normal state
			imageTag = false;
		}

		// Open tag
		txtarea.value += bbtags[bbnumber];
		if ((bbnumber === 14) && (imageTag === false)) imageTag = 1; // Check to stop additional tags after an unclosed image tag
		arraypush(bbcode,bbnumber+1);
		eval(\'document.post.addbbcode\'+bbnumber+\'.value += "*"\');
		txtarea.focus();
		return;
	}
	storeCaret(txtarea);
}

// From http://www.massless.org/mozedit/
function mozWrap(txtarea, open, close)
{
	var selLength = txtarea.textLength;
	var selStart = txtarea.selectionStart;
	var selEnd = txtarea.selectionEnd;
	if (selEnd === 1 || selEnd === 2)
		selEnd = selLength;

	var s1 = (txtarea.value).substring(0,selStart);
	var s2 = (txtarea.value).substring(selStart, selEnd)
	var s3 = (txtarea.value).substring(selEnd, selLength);
	txtarea.value = s1 + open + s2 + close + s3;
	return;
}

// Insert at Claret position. Code from
// http://www.faqts.com/knowledge_base/view.phtml/aid/1052/fid/130
function storeCaret(textEl) {
	if (textEl.createTextRange) textEl.caretPos = document.selection.createRange().duplicate();
}

//-->
</script>



<form action="' . $formtarget . '" method="post" name="post" onsubmit="doCheck();">



<table width="100%" cellspacing="2" cellpadding="2" border="0" align="center">
	<tr>
		<td align="left"><span  class="nav"><a href="forum.php" class="nav">Ironbane Forum</a>

		&raquo;&nbsp;<a href="forum.php?action=board&board=' . $board . '" class="nav">' . $boardname . '</a>' . $linktotopic . '</span></td>

	</tr>
</table>
<table width="100%" border="0" cellpadding="0" cellspacing="0" align="center">
	<tr>
	  <td class="tableborder">

		<table border="0" cellpadding="3" cellspacing="1" width="100%" style="border-collapse:collapse;">
			<tr>
				<th class="thHead" colspan="2" height="18"><span class="cattitle">' . ($action === 'editpost' ? 'Edit post' : ($newtopic ? 'Post a new topic' : 'Reply to topic')) . '</span></th>
			</tr>



' . ($board === "pt" && $is_first_post ? '
			<tr>
				<td class="row1" width="200"><span class="genmed"><b>Participants</b></span></td>
				<td class="row2"><span class="genmed"><input type="text" class="post" name="participants" maxlength="1000" style="width:350px" tabindex="1" value="' . $startp . $participants .'" />&nbsp;<input type="submit" style="width:95px" name="usersubmit" value="Find a user" class="liteoption" onClick="window.open(\'forum.php?action=finduser\', \'_finduser\', \'HEIGHT=250,resizable=yes,WIDTH=400\');return false;" /></span></td>
			</tr>
' : '') . '
' . ($newtopic === 1 || $is_first_post ? '
			<tr>
			  <td class="row1" width="100"><span class="genmed"><b>Subject</b></span></td>
			  <td class="row2"> <span class="genmed">
				<input type="text" name="subject" maxlength="60" style="width:99%" tabindex="2" class="post" value="' . $postdata["title"] . '" />
				</span> </td>
			</tr>
' : '') . '
			<tr>

			  <td class="row2" colspan="2" valign="top"><span class="genmed">

                            <table width="400" border="0" cellspacing="0" cellpadding="2">
                            <tr align="center" valign="middle">
                                    <td><span class="genmed">
                                    <input type="button" class="lightoption" accesskey="b" name="addbbcode0" value="B" style="font-weight:bold;" onClick="bbstyle(0)" onMouseOver="helpline(\'b\')" />
                                    </span></td>
                                    <td><span class="genmed">
                                    <input type="button" class="lightoption" accesskey="i" name="addbbcode2" value="i" style="font-style:italic;;" onClick="bbstyle(2)" onMouseOver="helpline(\'i\')" />
                                    </span></td>
                                    <td><span class="genmed">
                                    <input type="button" class="lightoption" accesskey="u" name="addbbcode4" value="u" style="text-decoration: underline;" onClick="bbstyle(4)" onMouseOver="helpline(\'u\')" />
                                    </span></td>
                                    <td><span class="genmed">
                                    <input type="button" class="lightoption" accesskey="q" name="addbbcode6" value="Quote" style="" onClick="bbstyle(6)" onMouseOver="helpline(\'q\')" />
                                    </span></td>
                                    <td><span class="genmed">
                                    <input type="button" class="lightoption" accesskey="l" name="addbbcode10" value="List" style="" onClick="bbstyle(10)" onMouseOver="helpline(\'l\')" />
                                    </span></td>
                                    <td><span class="genmed">
                                    <input type="button" class="lightoption" accesskey="o" name="addbbcode12" value="List=" style="" onClick="bbstyle(12)" onMouseOver="helpline(\'o\')" />
                                    </span></td>
                                    <td><span class="genmed">
                                    <input type="button" class="lightoption" accesskey="p" name="addbbcode14" value="Img" style=""  onClick="bbstyle(14)" onMouseOver="helpline(\'p\')" />
                                    </span></td>
                                    <td><span class="genmed">
                                    <input type="button" class="lightoption" accesskey="w" name="addbbcode16" value="URL" style="text-decoration: underline;" onClick="bbstyle(16)" onMouseOver="helpline(\'w\')" />
                                    </span></td>
                                    <td><span class="genmed">
                                            <select name="addbbcode18" onChange="bbfontstyle(\'[color=\' + this.form.addbbcode18.options[this.form.addbbcode18.selectedIndex].value + \']\', \'[/color]\');this.selectedIndex=0;" onMouseOver="helpline(\'s\')">
                                            <option style="color:black; background-color: #FAFAFA" value="#444444" class="genmed">Default</option>
                                            <option style="color:darkred; background-color: #FAFAFA" value="darkred" class="genmed">Dark Red</option>
                                            <option style="color:red; background-color: #FAFAFA" value="red" class="genmed">Red</option>
                                            <option style="color:orange; background-color: #FAFAFA" value="orange" class="genmed">Orange</option>
                                            <option style="color:brown; background-color: #FAFAFA" value="brown" class="genmed">Brown</option>
                                            <option style="color:yellow; background-color: #FAFAFA" value="yellow" class="genmed">Yellow</option>
                                            <option style="color:green; background-color: #FAFAFA" value="green" class="genmed">Green</option>
                                            <option style="color:olive; background-color: #FAFAFA" value="olive" class="genmed">Olive</option>
                                            <option style="color:cyan; background-color: #FAFAFA" value="cyan" class="genmed">Cyan</option>
                                            <option style="color:blue; background-color: #FAFAFA" value="blue" class="genmed">Blue</option>
                                            <option style="color:darkblue; background-color: #FAFAFA" value="darkblue" class="genmed">Dark Blue</option>
                                            <option style="color:indigo; background-color: #FAFAFA" value="indigo" class="genmed">Indigo</option>
                                            <option style="color:violet; background-color: #FAFAFA" value="violet" class="genmed">Violet</option>
                                            <option style="color:white; background-color: #FAFAFA" value="white" class="genmed">White</option>
                                            <option style="color:black; background-color: #FAFAFA" value="black" class="genmed">Black</option>
                                            </select>
                                            </span></td>
                                        <td><span class="genmed">
        <select name="addbbcode20" selected onChange="bbfontstyle(\'[size=\' + this.form.addbbcode20.options[this.form.addbbcode20.selectedIndex].value + \']\', \'[/size]\');this.selectedIndex=0;" onMouseOver="helpline(\'f\')">
					  <option value="0" class="genmed">Font size</option>
					  <option value="7" class="genmed">Tiny</option>
					  <option value="9" class="genmed">Small</option>
					  <option value="12" class="genmed">Normal</option>
					  <option value="18" class="genmed">Large</option>
					  <option  value="24" class="genmed">Huge</option>
					</select>
					</span></td>
                            </tr>
                            </table>
                            <table width="100%" border="0" cellspacing="0" cellpadding="2">
                            <tr>
                                    <td colspan="9"> ' . $smilies_col_content . '<span class="gensmall">
                                    <input type="text" name="helpbox" size="45" maxlength="100" style="width:450px; font-size:10px" class="helpline" value="Tip: Styles can be applied quickly to selected text." />
                                    </span>
                                    </td>
                            </tr>
                            </table>
                                <table width="100%" border="0" cellspacing="0" cellpadding="2">
                                <tr>
                                    <td>
                                        <textarea id="message" name="message" style="width:99%;height:200px" wrap="virtual" style="width:450px" tabindex="3" class="post" onselect="storeCaret(this);" onclick="storeCaret(this);" onkeyup="storeCaret(this);">' . $quote_c . $postdata["content"] . '</textarea>
                                        </td>
                                </tr>
                            </table>


                    </span></td>
            </tr>

            <tr>
                <td class="row2" colspan="2" align="center"><input type="submit" accesskey="s" tabindex="6" name="submit" class="mainoption" value="Submit" onclick="doCheck();" /></td>
            </tr>
        </table>

' . ($newtopic !== 1 && !$is_first_post ? '
    </td>
    </tr>
</table>
    '.$spacer.'

<table width="100%" border="0" cellpadding="0" cellspacing="0" align="center">
	<tr>
	  <td class="tableborder">

		<table border="0" cellpadding="0" cellspacing="0" width="100%" style="border-collapse:collapse;">
			<tr>
				<th class="thHead" colspan="2" height="19"><span class="cattitle">Topic review</span></th>
			</tr>
			<tr>
                              <td colspan="2"><div class="forumpage">
                              '.$topicreview.'</div>
                             </td>
			</tr>
                </table>

':'').'
		</td>
	</tr>
</table>

  <table width="100%" cellspacing="2" border="0" align="center" cellpadding="2">
	<tr>
	  <td align="right" valign="top"><span class="gensmall">All times are GMT ' . ((intval($userdata["gmt"]) >= 0 ? '+' : '') . $userdata["gmt"]) . '</span></td>
	</tr>
  </table>
</form>






';
        //TODO CHECK participants on edit able to add/remove members?
    }
} elseif ($action === "finduser") {
    $simple = 1;


    if (isset($_GET["user"])) {
        $username_search = preg_replace('/\*/', '%', parseToDB($_GET["user"]));

        $sql = "SELECT name
			FROM bcs_users
			WHERE name LIKE '$username_search' AND id > 0
			ORDER BY name";
        $result = bcs_query($sql) or bcs_error("<b>SQL ERROR</b> in <br>file " . __FILE__ . " on line " . __LINE__ . "<br><br><b>" . $query . "</b><br><br>" . mysql_error());
        for ($x = 0; $x < mysql_num_rows($result); $x++) {
            $memberdata = mysql_fetch_array($result);


            $username_list .= '<option value="' . $memberdata['name'] . '">' . $memberdata['name'] . '</option>';
        }
        if (mysql_num_rows($result) === 0) {
            $username_list = '<option>No matches found.</option>';
        }
    }

    $c_main = '

<script language="javascript" type="text/javascript">
<!--
function refresh_username(selected_username)
{
	if ( opener.document.forms[\'post\'].participants.value !== \'\' )
		opener.document.forms[\'post\'].participants.value += \',\';

	opener.document.forms[\'post\'].participants.value += selected_username;
	opener.focus();
}
//-->
</script>

<form method="post" name="search" action="forum.php?action=finduser">

	  <br />

<table width="90%" border="0" cellpadding="0" cellspacing="0" align="center">
	<tr>
	  <td class="tableborder">


		<table width="100%" border="0" cellspacing="0" cellpadding="0">
			<tr>
				<td>

					<table width="100%" cellpadding="4" cellspacing="1" border="0">
						<tr>
							<th class="thHead"><span class="cattitle">Find a user</span></th>
						</tr>
						<tr>
							<td valign="top" class="row1"><span class="genmed"><input type="text" name="user" value="' . $user . '" class="post" />&nbsp; <input type="submit" name="search" value="Search" class="liteoption" /></span><br /><span class="gensmall">Use * as a wildcard for partial matches</span><br />
							' . (!empty($user) ? '
							<span class="genmed">Select user:<br /><select name="username_list">' . $username_list . '</select>&nbsp; <input type="submit" class="liteoption" onClick="refresh_username(this.form.username_list.options[this.form.username_list.selectedIndex].value);return false;" name="use" value="Select" /></span><br />
							' : '') . '
						</tr>
						<tr>
							<td align="center" class="row1"><span class="genmed"><a href="javascript:window.close();" class="genmed">Close window</a></span></td>
						</tr>
					</table>

				</td>
			</tr>
		</table>

  		</td>
	</tr>
</table>

</form>

	';
} elseif ($action === "delpost") {

    if (!is_numeric($post)) {
        die();
    }



    $query = "SELECT * FROM forum_posts WHERE id = '$post'";
    $result = bcs_query($query) or bcs_error("<b>SQL ERROR</b> in <br>file " . __FILE__ . " on line " . __LINE__ . "<br><br><b>" . $query . "</b><br><br>" . mysql_error());
    $row = mysql_fetch_array($result);

    if ($s_editor !== 1) {
        if ($row["user"] !== $userdata["id"]) {
            die();
        }
    }

    if ($confirm_del) {
        $query = "DELETE FROM forum_posts WHERE id = '$post'";
        $result = bcs_query($query) or bcs_error("<b>SQL ERROR</b> in <br>file " . __FILE__ . " on line " . __LINE__ . "<br><br><b>" . $query . "</b><br><br>" . mysql_error());
        bcs_die("Your post was deleted succesfully.", "index.php?plugin=forum&amp;action=topic&amp;topic=" . $row["topic_id"]);
    } else {
        bcs_die("Are you sure you wish to delete this post?<br /><br /><form action=\"index.php?plugin=forum&amp;action=delpost&amp;post=" . $post . "\" method=POST><input type=submit name=confirm_del value=Delete></form>", "none");
    }
} elseif ($action === "board") {


    $validation_ok = 1;



    if ($board === "pt") {
        if ($_SESSION['logged_in'] === FALSE) {
            bcs_die('Please log in first.');
        }
        // Private Topics
        // WARNING: Private topics may appear wrong
        // Fix me kommas en extra like check erachter

        $_SESSION["showedpmwarning"] = true;

        if ( $s_admin ) {
            $query = "SELECT * FROM forum_topics WHERE private = 1 ORDER BY time DESC";
        }
        else {
            $query = "SELECT * FROM forum_topics WHERE (private_from = '$userdata[id]' OR private_chatters LIKE '%" . $userdata["name"] . "%') AND private = 1 ORDER BY time DESC";
        }
        $result = bcs_query($query) or bcs_error("<b>SQL ERROR</b> in <br>file " . __FILE__ . " on line " . __LINE__ . "<br><br><b>" . $query . "</b><br><br>" . mysql_error());


        $boardtitle = "Private Topics";
    } elseif ($board === "rt") {
        if (!$s_auth) {
            bcs_die('Please log in first.');
        }

        $query = "SELECT * FROM forum_topics WHERE (time > '$userdata[previous_session]' AND private = 0) OR ((private_from = '$userdata[id]' OR private_chatters LIKE '%" . $userdata["name"] . "%') AND private = 1 AND time > '$userdata[previous_session]') ORDER BY time DESC";
        $result = bcs_query($query) or bcs_error("<b>SQL ERROR</b> in <br>file " . __FILE__ . " on line " . __LINE__ . "<br><br><b>" . $query . "</b><br><br>" . mysql_error());

        $boardtitle = "Recent Topics";
    } elseif ($board === "ut") {


        $query = "SELECT a.* from forum_topics as a, (SELECT topic_id, count(*) as count FROM forum_posts GROUP BY topic_id) as b WHERE b.count = 1 AND a.id = b.topic_id";
        $result = bcs_query($query) or bcs_error("<b>SQL ERROR</b> in <br>file " . __FILE__ . " on line " . __LINE__ . "<br><br><b>" . $query . "</b><br><br>" . mysql_error());

        $boardtitle = "Unanswered Posts";
    } elseif ($board === "mt") {


        $query = "SELECT a.* FROM forum_topics as a, (SELECT topic_id FROM forum_posts WHERE user = '$userdata[id]' GROUP BY topic_id) as b WHERE b.topic_id = a.id";
        $result = bcs_query($query) or bcs_error("<b>SQL ERROR</b> in <br>file " . __FILE__ . " on line " . __LINE__ . "<br><br><b>" . $query . "</b><br><br>" . mysql_error());

        $boardtitle = "My Posts";
    } elseif ($board === "up") {
        if ( !isset($_GET["user"]) ) die("No user given!");

        $user = (int)parseToDB($_GET["user"]);

        $query = "SELECT a.* FROM forum_topics as a, (SELECT topic_id FROM forum_posts WHERE user = '$user' GROUP BY topic_id) as b WHERE b.topic_id = a.id";
        $result = bcs_query($query) or bcs_error("<b>SQL ERROR</b> in <br>file " . __FILE__ . " on line " . __LINE__ . "<br><br><b>" . $query . "</b><br><br>" . mysql_error());

        $boardtitle = memberName($user) . '\'s Posts';
    } else {
        $query = "SELECT * FROM forum_topics WHERE board_id = '$board' ORDER BY sticky DESC, time DESC";
        $result = bcs_query($query) or bcs_error("<b>SQL ERROR</b> in <br>file " . __FILE__ . " on line " . __LINE__ . "<br><br><b>" . $query . "</b><br><br>" . mysql_error());


        $query2 = "SELECT * FROM forum_boards WHERE id = '$board'";
        $result2 = bcs_query($query2) or bcs_error("<b>SQL ERROR</b> in <br>file " . __FILE__ . " on line " . __LINE__ . "<br><br><b>" . $query . "</b><br><br>" . mysql_error());
        $row2 = mysql_fetch_array($result2);

        if ( $row2["modonly"] === 1 && $s_editor === 0 ) die("no access");

        $boardtitle = $row2["name"];
    }


    $c_title = $boardtitle;
    $topicrow_content = "";
    $temp = "";

    for ($x = 0; $x < mysql_num_rows($result); $x++) {


        $row = mysql_fetch_array($result);

        $par_names = $row["private_chatters"];

        $nviews = $row["views"];

        $topic_read = true;

        if ($row["time"] > $userdata["last_session"]) {
            $topic_read = false;
        }

        $query2 = "SELECT * FROM forum_posts WHERE topic_id = '$row[id]' ORDER BY time ASC LIMIT 1";
        $result2 = bcs_query($query2) or bcs_error("<b>SQL ERROR</b> in <br>file " . __FILE__ . " on line " . __LINE__ . "<br><br><b>" . $query . "</b><br><br>" . mysql_error());
        $row2 = mysql_fetch_array($result2);

        $query3 = "SELECT id FROM forum_posts WHERE topic_id = '$row[id]'";
        $result3 = bcs_query($query3) or bcs_error("<b>SQL ERROR</b> in <br>file " . __FILE__ . " on line " . __LINE__ . "<br><br><b>" . $query . "</b><br><br>" . mysql_error());
        $nreplies = mysql_num_rows($result3) - 1;




        $query4 = "SELECT id, title, user, time, topic_id FROM forum_posts WHERE topic_id = '$row[id]' ORDER BY time DESC LIMIT 1";
        $result4 = bcs_query($query4) or bcs_error("<b>SQL ERROR</b> in <br>file " . __FILE__ . " on line " . __LINE__ . "<br><br><b>" . $query . "</b><br><br>" . mysql_error());
        $row4 = mysql_fetch_array($result4);
        if (strlen($row4["title"]) > $max_title_length) {
            $limitname = substr($row4["title"], 0, $max_title_length) . "...";
        } else {
            $limitname = $row4["title"];
        }



        if ($nreplies > $posts_per_page) {
            $num_pages = ceil($nreplies / $posts_per_page);
            for ($a = 1; $a <= $num_pages; $a++) {
                $temp .= "[" . createLink($a, "index.php?plugin=forum&amp;action=topic&amp;topic=" . $row4["topic_id"] . "&amp;page=" . $a) . "] ";
            }
            $nav_extra = "<br /><span class=\"gen\">Go to page: " . $temp . "</span>";
        } else {
            $nav_extra = "";
            $temp = "";
        }

        $lastpost = createLink($limitname, "index.php?plugin=forum&amp;action=topic&amp;topic=" . $row4["topic_id"] . "#" . $row4["id"]) . "<br />by " . memberLink($row4["user"]) . " on " . createDate($row4["time"], $userdata["gmt"]);
        if ($board === "pt") {
            $par_list = explode(',', $par_names);
            $par_names = "";
            for ($z = 0; $z < count($par_list); $z++) {
                $par_names .= $par_list[$z];
                if ($z !== count($par_list) - 1) {
                    $par_names .= "<br />";
                }
            }

            $pt_extra = "<td class=row" . ($x % 2 ? 1 : 2) . " width=\"28%\"><span class=\"gen\"><b>" . createLink($row2["title"], "index.php?plugin=forum&amp;action=topic&amp;topic=" . $row["id"]) . "</b></span>" . $nav_extra . "</td>
    <td class=row" . ($x % 2 ? 1 : 2) . " width=\"10%\"><span class=\"gensmall\">" . $par_names . "</span></td>";
        } else {

            $pt_extra = "<td class=row" . ($x % 2 ? 1 : 2) . " width=\"38%\"><span class=\"gen\"><b>" . createLink($row2["title"], "index.php?plugin=forum&amp;action=topic&amp;topic=" . $row["id"]) . "</b></span>" . $nav_extra . "</td>";
        }


        if ($topic_read) {
            $rt_image = "<img src=themes/images/topic_read.gif alt=\"No new replies since your last visit\">";
        } else {
            $rt_image = "<img src=themes/images/topic_unread.gif>";
        }


        //	$thelist .= "
        //
    //<tr>
        //<td class=row".($x%2?1:2)." width=\"2%\"><span class=\"gen\">".$rt_image."</span></td>
        //".$pt_extra."
        //<td class=row".($x%2?1:2)." width=\"20%\"><span class=\"gen\">".memberLink($row2[user])."</span></td>
        //<td class=row".($x%2?1:2)." width=\"10%\"><span class=\"gen\">".$nreplies."</span></td>
        //<td class=row".($x%2?1:2)." width=\"30%\"><span class=\"gen\">".$lastpost."</span></td>
        //</tr>
        //";
        $rowclass = $x % 2 ? "row2" : "row1";
        if ( (int)$row["sticky"] === 1 ) {
            $row2["title"] = "<b>Sticky: $row2[title]</b>";
            $rowclass = "row3";
        }

        $topicrow_content .= '
  			<tr>
			  <td class="'.$rowclass.'" align="center" valign="middle" width="20"><img src="theme/images/folder.gif" /></td>
			  <td class="'.$rowclass.'" width="100%"><span class="topictitle"><a href="forum.php?action=topic&topic=' . $row["id"] . '" class="topictitle">' . $row2["title"] . ''.($s_admin?" ($row[id])":"").'</a></span></td>
			  <td class="'.$rowclass.'" align="center" valign="middle"><span class="postdetails">' . $nreplies . '</span></td>
			  <td class="'.$rowclass.'" align="center" valign="middle"><span class="name">' . memberLink($row2["user"]) . '</span></td>
			  <td class="'.$rowclass.'" align="center" valign="middle"><span class="postdetails">' . $nviews . '</span></td>
			  <td class="'.$rowclass.'" align="center" valign="middle" nowrap="nowrap"><span class="postdetails">' . timeAgo($row4["time"]) . ' ago<br />' . memberLink($row4["user"]) . ' <a href="index.php?plugin=forum&amp;action=topic&amp;topic=' . $row4["topic_id"] . '#' . $row4["id"] . '"><img src="theme/images/icon_latest_reply.gif"></a></span></td>
			</tr>
';
    }

    //if (($board !== 7 && $board !== "rt" ) || $s_admin === 1) {
    if (mysql_num_rows($result) === 0) {
        $topicrow_content .= '

			<tr>
			  <td class="row3" colspan="6" height="30" align="center" valign="middle"><span class="genmed">' . (is_numeric($board) ? 'No topics have been posted yet. <a href="index.php?plugin=forum&action=reply&board=' . $board . '">Start a new topic</a>' : 'No topics found.') . '</span></td>
			</tr>
';
//			$thelist .= "
//
//		<tr>
//		<td class=row2 width=\"100%\" colspan=".($board==="pt"?6:5)."><span class=\"gen\">
//		There are no topics posted here yet. <a href=index.php?plugin=forum&action=reply&board=".$board.">Start a new topic</a>!
//		</td>
//		</tr>
//		";
    }
    //}

    $c_main = '





  <table width="100%" cellspacing="2" cellpadding="2" border="0" align="center">
	<tr>
	  <td align="left" valign="bottom" colspan="2"><span class="maintitle">' . $boardtitle . '</span><br /><span class="gensmall"><b>Users browsing this forum: ' . getListOfOnlineMembers(",", "action=board&board=".$board) . '</b></span></td>
	  <td align="right" valign="bottom" nowrap="nowrap"></td>
	</tr>
	<tr>
	  <td align="left" valign="middle" width="50" colspan="2">'.(($s_editor != 1 && (int)$board === 7)?'':'<a href="forum.php?action=reply&board=' . $board . '"><img src="theme/images/lang_english/new_topic.gif" border="0" alt="Post new topic" /></a>').'</td>
	</tr>
	<tr>
	  <td align="left" valign="middle" class="nav" width="100%"><span class="nav"><a href="forum.php" class="nav">Ironbane Forum</a>&nbsp;&raquo;&nbsp;<span class="nav">' . $boardtitle . '</span></span></td>
	  <td align="right" valign="bottom" class="nav" nowrap="nowrap"></td>
	</tr>
  </table>

<table width="100%" border="0" cellpadding="0" cellspacing="0">
	<tr>
  		<td class="tableborder">

		  <table border="0" cellpadding="4" cellspacing="1" width="100%" style="border-collapse:collapse;">
			<tr>
			  <th colspan="2" align="center" class="thCornerL" nowrap="nowrap">&nbsp;Topics&nbsp;</th>
			  <th width="50" align="center" class="thTop" nowrap="nowrap">&nbsp;Replies&nbsp;</th>
			  <th width="100" align="center" class="thTop" nowrap="nowrap">&nbsp;Author&nbsp;</th>
			  <th width="50" align="center" class="thTop" nowrap="nowrap">&nbsp;Views&nbsp;</th>
			  <th align="center" class="thCornerR" nowrap="nowrap">&nbsp;Last Post&nbsp;</th>
			</tr>
			<!-- BEGIN topicrow -->
                        ' . $topicrow_content . '

			<!-- END topicrow -->
			<!-- BEGIN switch_no_topics -->

			<!-- END switch_no_topics -->
			<tr>
			  <td class="catBottom" align="center" valign="middle" colspan="6" height="28"></td>
			</tr>
		  </table>

  		</td>
	</tr>
</table>

  <table width="100%" cellspacing="2" border="0" align="center" cellpadding="2">
	<tr>
	  <td align="left" valign="middle" class="nav" width="100%"><span class="nav"><a href="forum.php" class="nav">Ironbane Forum</a>&nbsp;&raquo;&nbsp;<span class="nav">' . $boardtitle . '</span></span></td>
	  <td align="right" valign="middle" nowrap="nowrap"><span class="gensmall">All times are GMT ' . ((intval($userdata["gmt"]) >= 0 ? '+' : '') . $userdata["gmt"]) . '</span><br /><span class="nav"></span></td>
	</tr>
	<tr>
	  <td align="left" valign="middle" width="50" colspan="2">'.(($s_editor != 1 && (int)$board === 7)?'':'<a href="forum.php?action=reply&board=' . $board . '"><img src="theme/images/lang_english/new_topic.gif" border="0" alt="Post new topic" /></a>').'</td>
	</tr>
  </table>



<table width="100%" cellspacing="0" border="0" align="center" cellpadding="0">
	<tr>
		<td align="center" valign="top">

			<table cellspacing="3" cellpadding="0" border="0">

				<tr>
					<td width="20" align="left"><img src="theme/images/folder_new.gif" alt="New posts" /></td>
					<td class="gensmall">New posts</td>
					<td>&nbsp;&nbsp;</td>
					<td width="20" align="center"><img src="theme/images/folder.gif" alt="No new posts" /></td>
					<td class="gensmall">No new posts</td>
				</tr>
				<tr>

					<td width="20" align="center"><img src="theme/images/folder_announce.gif" alt="Announcement" /></td>
					<td class="gensmall">Announcement</td>
					<td>&nbsp;&nbsp;</td>
					<td width="20" align="center"><img src="theme/images/folder_sticky.gif" alt="Sticky" /></td>
					<td class="gensmall">Sticky</td>
				</tr>
				<tr>
					<td class="gensmall"><img src="theme/images/folder_lock_new.gif" alt="" /></td>

					<td class="gensmall">New posts [ Locked ]</td>
					<td>&nbsp;&nbsp;</td>
					<td class="gensmall"><img src="theme/images/folder_lock.gif" alt="" /></td>
					<td class="gensmall">No new posts [ Locked ]</td>
				</tr>
			</table>

		</td>

	</tr>
</table>



';
} elseif ($action === "byuser") {
    if (!is_numeric($user)) {
        die();
    }
    $query = "SELECT * FROM forum_posts WHERE user = '$user' ORDER BY time ASC";
    $result = bcs_query($query) or bcs_error("<b>SQL ERROR</b> in <br>file " . __FILE__ . " on line " . __LINE__ . "<br><br><b>" . $query . "</b><br><br>" . mysql_error());

    for ($x = 0; $x < mysql_num_rows($result); $x++) {
        $row = mysql_fetch_array($result);
    }
} elseif ($action === "topic") {
    $validation_ok = 1;

    // View all posts from a topic_id
    if (!is_numeric($topic)) {
        die();
    }
    if (isset($page)) {
        if (!is_numeric($page)) {
            die();
        }
    } else {
        $page = 1;
    }

    $query2 = "UPDATE forum_topics SET views = views + 1 WHERE id = '$topic'";
    $result2 = bcs_query($query2) or bcs_error("<b>SQL ERROR</b> in <br>file " . __FILE__ . " on line " . __LINE__ . "<br><br><b>" . $query . "</b><br><br>" . mysql_error());


    $finish = $page * $posts_per_page;
    $start = $finish - $posts_per_page;

    //$moresql = " LIMIT " . $start . "," . $posts_per_page;
//    $moresql = "";

    $query4 = "SELECT title FROM forum_posts WHERE topic_id = '$topic' ORDER BY time ASC LIMIT 1";
    $result4 = bcs_query($query4) or bcs_error("<b>SQL ERROR</b> in <br>file " . __FILE__ . " on line " . __LINE__ . "<br><br><b>" . $query . "</b><br><br>" . mysql_error());
    $row4 = mysql_fetch_array($result4);

    $query = "SELECT * FROM forum_posts WHERE topic_id = '$topic' ORDER BY time ASC";
    $result = bcs_query($query) or bcs_error("<b>SQL ERROR</b> in <br>file " . __FILE__ . " on line " . __LINE__ . "<br><br><b>" . $query . "</b><br><br>" . mysql_error());

    $c_title = $row4["title"];

    $nav_pages = "";

    $query5 = "SELECT id FROM forum_posts WHERE topic_id = '$topic'";
    $result5 = bcs_query($query5) or bcs_error("<b>SQL ERROR</b> in <br>file " . __FILE__ . " on line " . __LINE__ . "<br><br><b>" . $query . "</b><br><br>" . mysql_error());
    $nreplies = mysql_num_rows($result5) - 1;
    if ($nreplies > $posts_per_page) {
        $num_pages = ceil($nreplies / $posts_per_page);
        for ($a = 1; $a <= $num_pages; $a++) {
            if ($a === $page) {
                $temp .= "<b>" . $a . "</b> ";
            } else {
                $temp .= "" . createLink($a, "index.php?plugin=forum&amp;action=topic&amp;topic=" . $topic . "&amp;page=" . $a) . " ";
            }
        }
        $nav_pages = "Go to page: " . $temp . "";
    }


    $query2 = "SELECT * FROM forum_topics WHERE id = '$topic'";
    $result2 = bcs_query($query2) or bcs_error("<b>SQL ERROR</b> in <br>file " . __FILE__ . " on line " . __LINE__ . "<br><br><b>" . $query . "</b><br><br>" . mysql_error());
    $row2 = mysql_fetch_array($result2);

    $query3 = "SELECT * FROM forum_boards WHERE id = '$row2[board_id]'";
    $result3 = bcs_query($query3) or bcs_error("<b>SQL ERROR</b> in <br>file " . __FILE__ . " on line " . __LINE__ . "<br><br><b>" . $query . "</b><br><br>" . mysql_error());
    $row3 = mysql_fetch_array($result3);

    $query8 = "SELECT * FROM forum_cats WHERE id = '$row3[forumcat]'";
    $result8 = bcs_query($query8) or bcs_error("<b>SQL ERROR</b> in <br>file " . __FILE__ . " on line " . __LINE__ . "<br><br><b>" . $query . "</b><br><br>" . mysql_error());
    $row8 = mysql_fetch_array($result8);

    if ( $row8["modonly"] === 1 && $s_editor === 0 && $userdata["pending_editor"] === 0 ) bcs_die("I'm sorry, but that topic is restricted to the development team.");


    //$indexlink = createLink("ironbane Forum","index.php?plugin=forum");

    $userlist = "";
    if ((int)$row2['private'] === 1) {
        // Check if we are allowed to view this topic
        $check_ok = 0;
        $par_list = explode(',', $row2["private_chatters"]);

        // Add ourselves always
        $userlist .= memberLink($userdata["id"]);

        // Only comma if the list
        //$userlist .= ',';

        for ($x = 0; $x < count($par_list); $x++) {
            if ($userdata["name"] === $par_list[$x]) {
                $check_ok = 1;
            } else {
                $userlist .= ', ' . memberLink($par_list[$x]);
            }
        }
        // Check if we are the starter
        if ($row2["private_from"] === $userdata["id"]) {
            $check_ok = 1;
        } else {
            $userlist .= ', ' . memberLink($row2["private_from"]);
        }

        if (!$check_ok && !$s_admin) {
            bcs_die('Sorry, you are not allowed to view this private topic.', 'back');
        }

        if ($row2["time"] > $userdata["previous_session"]) $_SESSION["showedpmwarning"] = true;

        $board = "pt";
        $boardname = "Private Topics";
    } else {

        $board = $row2["board_id"];
        $boardname = $row3["name"];
    }
    //if ( $row2[board_id] !== 7 || $s_admin === 1 ) {
    $link1 = createLink("<img src=\"themes/images/buttons/reply.gif\" border=0>", "index.php?plugin=forum&amp;action=reply&amp;topic=" . $topic);
    //}

    if ($s_editor === 1) {
        $admin_topic = "<span class=\"gensmall\">";
        $admin_topic .= createLink("<img src=\"themes/images/buttons/delete.gif\" border=0>", "index.php?plugin=forum&amp;action=deletetopic&amp;topic=" . $topic);
        $admin_topic .= " ";
        $admin_topic .= createLink("<img src=\"themes/images/buttons/lock.gif\" border=0>", "index.php?plugin=forum&amp;action=locktopic&amp;topic=" . $topic);
        $admin_topic .= "</span>";
    }

    $postrow_content = "";

    for ($x = 0; $x < mysql_num_rows($result); $x++) {
        $row = mysql_fetch_array($result);


        $moreinfo = "";

        $query7 = "SELECT name, reg_date, forum_avatar, forum_sig, info_location, info_realname, last_session FROM bcs_users WHERE id = '$row[user]'";
        $result7 = bcs_query($query7) or bcs_error("<b>SQL ERROR</b> in <br>file " . __FILE__ . " on line " . __LINE__ . "<br><br><b>" . $query . "</b><br><br>" . mysql_error());
        $row7 = mysql_fetch_array($result7);

        //$moreinfo .= "Level " . $row7[rpg_level]." ".classname($row7[rpg_class])."<br>Ranking: #".getRPGrank($row[user])."<br>";

//        $guild = hasGuild($row[user]);
//        if ($guild !== false) {
//            $moreinfo .= 'Guild: ' . guildlink($guild[id])."<br>";
//        }

        //$moreinfo .= '<br>';

        if ($row["user"] !== -1) {
            $moreinfo .= 'Joined: ' . createDate($row7["reg_date"], $userdata["gmt"], 5) . '<br />Posts: ' . getTotalUserPosts($row["user"]);
        }

//	if ( $row7[info_country] ) {
//		$moreinfo .= "<br />Country: ".$row7[info_country];
//	}
        if ($row7["info_location"]) {
            $moreinfo .= "<br />Location: " . $row7["info_location"];
        }
//	if ( $row7[info_age] ) {
//		$moreinfo .= "<br />Age: ".$row7[info_age];
//	}
//	if ( $row7[info_gender] ) {
//		switch($info_gender) {
//			case 1:
//				$moreinfo .= "<br />Gender: Male";
//				break;
//			case 2:
//				$moreinfo .= "<br />Gender: Female";
//				break;
//		}
//	}
//	if ( $row7[info_occupation] ) {
//		$moreinfo .= "<br />Occupation: ".$row7[info_occupation];
//	}
//	if ( $row7[info_interests] ) {
//		$moreinfo .= "<br />Interests: ".$row7[info_interests];
//	}


        $is_online = false;

        if ($row7["last_session"] + 300 >= time()) {
            $is_online = true;
        }

        if ($is_online) {
            $io_msg = "I am currently <b>online</b>!";
            $io_image = "<img src=themes/images/online.gif onmouseover=\"Tip('" . $io_msg . "')\" onmouseout=\"UnTip()\">";
        } else {
            $io_msg = "I am currently <i>offline</i>.";
            $io_image = "<img src=themes/images/offline.gif onmouseover=\"Tip('" . $io_msg . "')\" onmouseout=\"UnTip()\">";
        }


        if ($row7["forum_avatar"]) {
            $u_avatar = "<img src=\"" . $row7["forum_avatar"] . "\" alt=\"" . $row7["name"] . "'s Avatar\">";
        } else {
            $u_avatar = "";
        }

        if ($row7["forum_sig"]) {
            $u_sig = "<span class=\"gen\"><br /><br />_________________<br />" . $row7["forum_sig"] . "<br /></span>";
        } else {
            $u_sig = "";
        }
        //

        $button_edit = '';
        $button_quote = '<a href="forum.php?action=reply&topic=' . $topic . '&quote_p=' . $row["id"] . '"><img src="theme/images/lang_english/icon_quote.gif" border="0"></a>';
        $button_delete = '';


        if ($s_editor === 1 || ( $row["user"] === $userdata["id"] && $s_auth )) {
            $button_edit = '<a href="index.php?plugin=forum&action=editpost&post=' . $row["id"] . '"><img src="theme/images/lang_english/icon_edit.gif" border="0"></a>';
        }

        if ($s_editor === 1) {
            $button_delete = '<a href="index.php?plugin=forum&action=delpost&post=' . $row["id"] . '"><img src="theme/images/icon_delete.gif" border="0"></a>';
        }

        $rated_list = "";
        $rate_list = "<div id=\"rinfo" . $row["id"] . "\"><br /></div>";

        //$rate_list  .= "<table border=0 bgcolor=white><tr><td>";

        /*
          for($y=0;$y<count($listc);$y++){
          $querys = "SELECT id FROM forum_ratings WHERE to_post = '$row[id]' AND rating = '$listc[$y]'";
          $results = bcs_query($querys) or bcs_error("<b>SQL ERROR</b> in <br>file ".__FILE__." on line ".__LINE__."<br><br><b>".$query."</b><br><br>".mysql_error());
          if ( mysql_num_rows($results) > 0 ) {
          $rated_list .= "<img src=\"plugins/forum/smiles/rating/".$listc[$y].".gif\" border=\"0\" alt=\"".$listd[$y]."\"> (".mysql_num_rows($results)."x ".$listd[$y].")  ";
          }
          }

          for($y=0;$y<count($listc);$y++){
          if ( $y === 6 ) {
          $rate_list .= "<br />";
          }
          $rate_list .= " <a onmouseover=\"document.getElementById('rinfo".$row[id]."').innerHTML='".$listd[$y]."'\" onmouseout=\"document.getElementById('rinfo".$row[id]."').innerHTML='<br />'\" href=\"index.php?plugin=forum&amp;action=ratepost&amp;post=".$row[id]."&amp;rating=".$listc[$y]."\"><img src=\"plugins/forum/smiles/rating/".$listc[$y].".gif\" border=\"0\" alt=\"".$listd[$y]."\"></a>";
          }
         */

        //$rate_list .= "</td></tr></table>";
        // Make a rating of the user aswell 02/01/07 - Happy new year!
        // Count all ratings, and put a icon of the highest rating here
//	$max = 0;
//	$rating_user = "";
//	$z = 0;
//
//	for($y=0;$y<count($listc);$y++){
//		$querys = "SELECT a.id, b.id FROM forum_ratings AS a, forum_posts AS b WHERE b.user = '$row[user]' AND a.to_post = b.id AND a.rating = '$listc[$y]'";
//		$results = bcs_query($querys) or bcs_error("<b>SQL ERROR</b> in <br>file ".__FILE__." on line ".__LINE__."<br><br><b>".$query."</b><br><br>".mysql_error());
//		$count = mysql_num_rows($results);
//		if ( $count > $max ) {
//			$max = $count;
//			$z = $y;
//		}
//	}
//
//	// Add the best related rating
//	if ( $max > 2 ) {
//		$rating_user = "<img src=\"plugins/forum/smiles/rating/".$listc[$z].".gif\" border=\"0\" alt=\"".$row7[name]." ".$listf[$z]."\">";
//	}


        $when = "<span class=gensmall>Posted on " . createDate($row["time"], $userdata["gmt"]) . "</span>";

        //TODO: rank image, page count, pagination, view posts since last visit etc


        $image_profile = '<a href="user.php?n=' . $row7["name"] . '"><img src="theme/images/lang_english/icon_profile.gif"></a>';
        $image_pm = '<a href="forum.php?action=reply&board=pt&startp=' . $row7["name"] . '"><img src="theme/images/lang_english/icon_pm.gif"></a>';
        $image_www = '';
        if (!empty($row7["info_website"])) {
            $image_www = '<a href="' . $row7["info_website"] . '" rel="nofollow"><img src="theme/images/lang_english/icon_www.gif"></a>';
        }

        $rowclass = $x % 2 ? "row2" : "row1";

        $postrow_content .= '

				<tr>
					<td width="150" align="left" valign="top" class="' . $rowclass . '"><span class="name"><a name="' . $row["id"] . '"></a><b>' . getPosterLink($row, $row["user"]) . '</b>'.($row7["info_realname"]!==""&&$row8["modonly"]===1?' ('.$row7["info_realname"].')':'').'</span><br /><span class="postdetails">' . getRank($row["user"]) . '<br />' . $u_avatar . '<br /><br />' . $moreinfo . '</span><br /></td>
					<td class="' . $rowclass . '" height="28" valign="top"><table width="100%" border="0" cellspacing="0" cellpadding="0">
						<tr>
							<td width="100%">&nbsp;<img src="theme/images/icon_minipost.gif" border="0" />&nbsp;<span class="postdetails">Posted: ' . createDate($row["time"], $userdata["gmt"]) . ' ('.(timeAgo($row["time"])).' ago)</span></td>
							<td valign="top" nowrap="nowrap"><table border="0" cellpadding="0" cellspacing="2" width="100%"><tr><td width="100%" nowrap="nowrap">' . $button_quote . ' ' . $button_edit . ' ' . $button_delete . '</td></tr></table></td>
						</tr>
						<tr>
							<td colspan="2"><hr /></td>
						</tr>
						<tr>
							<td colspan="2"><span class="gen">' . post_parse($row["content"]) . post_parse($u_sig) . '</span><span class="gensmall">' . (intval($row["lastedit_count"]) > 0 ? $spacer.'Last edited by ' . memberLink($row["lastedit_author"]) . ' on ' . createDate($row["lastedit_time"], $userdata["gmt"]) . ' ('.timeAgo($row["lastedit_time"]).' ago). Edited ' . $row["lastedit_count"] . ' time' . (intval($row["lastedit_count"]) !== 1 ? 's' : '') . ' in total' : '') . '</span></td>
						</tr>
					</table></td>
				</tr>
				<tr>
					<td class="' . $rowclass . '" width="150" align="left" valign="middle"><span class="gensmall">&nbsp;<a href="#" class="gensmall">Back to top&nbsp;&raquo;</a></span></td>
					<td class="' . $rowclass . '" height="28" valign="bottom" nowrap="nowrap">

						<table cellspacing="0" cellpadding="0" border="0" height="18" width="18">
							<tr>
								<td valign="middle" nowrap="nowrap"><table border="0" cellpadding="0" cellspacing="2" width="100%"><tr><td width="100%" nowrap="nowrap">' . ($row["user"] !== -1 ? ' ' . $image_profile . ' ' . $image_pm . ' ' . $image_www . ' ' : '') . '</td></tr></table></td>
							</tr>
						</table>

					</td>
				</tr>
				<tr>
					<td class="row3" colspan="2" height="1"><img src="theme/images/spacer.gif" alt="" width="1" height="1" /></td>
				</tr>


';


    }

    if ( $row2["sticky"] ) $row4["title"] = "Sticky topic: ".$row4["title"];

    $c_main = '


<table width="100%" cellspacing="2" cellpadding="2" border="0">
  <tr>
	<td align="left" valign="bottom" colspan="2"><span class="maintitle">' . $row4["title"] . '</a></span></td>
  </tr>
</table>

<table width="100%" cellspacing="2" cellpadding="2" border="0">
  <tr>
	<td align="left" colspan="3"><span class="genbig">' . $nav_pages . '</span></td>
  </tr>
  <tr>
	<td align="left" valign="bottom" nowrap="nowrap"><span class="nav">'.(($s_editor !== 1 && (int) $board === 7)?'':'<a href="forum.php?action=reply&board=' . $row2["board_id"] . '"><img src="theme/images/lang_english/new_topic.gif" border="0" alt="Post new topic" align="middle" /></a>').'&nbsp;&nbsp;&nbsp;<a href="index.php?plugin=forum&action=reply&topic=' . $topic . '"><img src="theme/images/lang_english/post_reply.gif" border="0" alt="Reply to topic" align="middle" /></a></span></td>
  </tr>
  <tr>
	<td align="left" valign="middle" width="100%"><span class="nav"><a href="forum.php" class="nav">Ironbane Forum</a>&nbsp;&raquo;&nbsp;<a href="forum.php?action=board&board=' . $board . '" class="nav">' . $boardname . '</a>&nbsp;&raquo;&nbsp;<span class="nav">' . $row4["title"] . '</span></span></td>
  </tr>
</table>

<div class="forumpage">
<table width="100%" border="0" cellpadding="0" cellspacing="0">
	<tr>
  		<td class="tableborder">

			<table width="100%" cellspacing="1" cellpadding="3" border="0" style="border-collapse:collapse;">
			' . ($board === 'pt' ? '
							<tr>
					<th class="catHead"colspan="2" nowrap="nowrap" align>Private</th>
				</tr>
	   <tr>
		<td class="row1" colspan="2" align="center"><span class="gen">This topic is <b>Private</b>. Only the following users have access to this topic: ' . $userlist . '</span><br><br></td>
	   </tr>
			' : '') . '
				<tr>
					<th class="thLeft" width="150" nowrap="nowrap">Author</th>
					<th class="thRight" nowrap="nowrap">Message</th>
				</tr>
				<!-- BEGIN postrow -->
                                ' . $postrow_content . '

				<!-- END postrow -->
				<tr align="center">
					<td class="catBottom" colspan="2" height="28"></td>
				</tr>
			</table>

  		</td>
	</tr>
</table>
</div>
<table width="100%" cellspacing="2" cellpadding="2" border="0" align="center">
  <tr>
	<td align="left" valign="middle" width="100%"><span class="nav"><a href="forum.php" class="nav">Ironbane Forum</a>&nbsp;&raquo;&nbsp;<a href="forum.php?action=board&board=' . $board . '" class="nav">' . $boardname . '</a>&nbsp;&raquo;&nbsp;<span class="nav">' . $row4["title"] . '</span></span></td>
  </tr>
  <tr>
	<td align="left" valign="bottom" nowrap="nowrap"><span class="nav">'.(($s_editor !== 1 && (int)$board === 7)?'':'<a href="forum.php?action=reply&board=' . $row2["board_id"] . '"><img src="theme/images/lang_english/new_topic.gif" border="0" alt="Post new topic" align="middle" /></a>').'&nbsp;&nbsp;&nbsp;<a href="index.php?plugin=forum&action=reply&topic=' . $topic . '"><img src="theme/images/lang_english/post_reply.gif" border="0" alt="Reply to topic" align="middle" /></a></span></td>
  </tr>
  <tr>
	<td align="left" colspan="3"><span class="genbig">' . $nav_pages . '</span></td>
  </tr>
</table>

<table width="100%" cellspacing="2" border="0" align="center">
  <tr>

	<td width="40%" valign="top" nowrap="nowrap" align="left">
        '.($s_editor?'
	  <a href="forum.php?action=deletetopic&topic=' . $topic . '"><img src="theme/images/topic_delete.gif" alt="Delete this topic" title="Delete this topic" border="0" /></a>&nbsp;

           '.($row2["sticky"]?'<a href="forum.php?action=unstickytopic&topic=' . $topic . '"><img src="theme/images/topic_sticky.gif" alt="Sticky this topic" title="Sticky this topic" border="0" /></a>':'<a href="forum.php?action=stickytopic&topic=' . $topic . '"><img src="theme/images/topic_sticky.gif" alt="Sticky this topic" title="Sticky this topic" border="0" /></a>').'&nbsp;
               ':'').'
          </td>
	<td align="right" valign="top" nowrap="nowrap">
            </td>
</tr>
</table>



';

//              <a href="forum.php?action=movetopic&topic=' . $topic . '"><img src="theme/images/topic_move.gif" alt="Move this topic" title="Move this topic" border="0" /></a>&nbsp;
//          <a href="forum.php?action=locktopic&topic=' . $topic . '"><img src="theme/images/topic_lock.gif" alt="Lock this topic" title="Lock this topic" border="0" /></a>&nbsp;
//
} else {
    $validation_ok = 1;





    if ($s_auth) {
        // Add private topics

        $catrow_content = '
                              <tr>
                                    <td colspan="4" class="categorybar">

                                              <table border="0">
                                                    <tr>
                                                      <td><img src="theme/images/category_icon.gif" /></td>
                                                      <td><span class="cattitle">Private Topics</span></td>
                                                    </tr>
                                              </table>

                                     </td>
                              </tr>
                              ';

        // Private Topics extra loop (pre)

        $query2 = "SELECT * FROM forum_topics WHERE (private_from = '$userdata[id]' OR private_chatters LIKE '%" . $userdata["name"] . "%') AND private = 1";
        $result2 = bcs_query($query2) or bcs_error("<b>SQL ERROR</b> in <br>file " . __FILE__ . " on line " . __LINE__ . "<br><br><b>" . $query . "</b><br><br>" . mysql_error());
        $ntopics = mysql_num_rows($result2);
        for ($y = 0; $y < $ntopics; $y++) {
            $row2 = mysql_fetch_array($result2);
            $query3 = "SELECT * FROM forum_posts WHERE topic_id = '$row2[id]'";
            $result3 = bcs_query($query3) or bcs_error("<b>SQL ERROR</b> in <br>file " . __FILE__ . " on line " . __LINE__ . "<br><br><b>" . $query . "</b><br><br>" . mysql_error());
            $nposts += mysql_num_rows($result3);
        }
        // Calculate topics & posts


        $query2 = "SELECT id, title, user, time, topic_id FROM forum_posts ORDER BY time DESC ";
        $result2 = bcs_query($query2) or bcs_error("<b>SQL ERROR</b> in <br>file " . __FILE__ . " on line " . __LINE__ . "<br><br><b>" . $query . "</b><br><br>" . mysql_error());
        for ($y = 0; $y < mysql_num_rows($result2); $y++) {
            $row2 = mysql_fetch_array($result2);
            $query3 = "SELECT * FROM forum_topics WHERE (private_from = '$userdata[id]' OR private_chatters LIKE '%" . $userdata["name"] . "%') AND private = 1 AND id = '$row2[topic_id]'";
            $result3 = bcs_query($query3) or bcs_error("<b>SQL ERROR</b> in <br>file " . __FILE__ . " on line " . __LINE__ . "<br><br><b>" . $query . "</b><br><br>" . mysql_error());
            $row3 = mysql_fetch_array($result3);
            if ((int)$row3['private'] === 1) {
                // We found one !
                if (strlen($row2["title"]) > $max_title_length) {
                    $limitname = substr($row2["title"], 0, $max_title_length) . "...";
                } else {
                    $limitname = $row2["title"];
                }
                $lastpost = timeAgo($row2["time"]) . ' ago<br>' . memberLink($row2["user"]) . ' <a href="index.php?plugin=forum&amp;action=topic&amp;topic=' . $row2["topic_id"] . '#' . $row2["id"] . '"><img src="theme/images/icon_latest_reply.gif"></a>';
                break;
            } else {
                $lastpost = "";
            }
        }


        $catrow_content .= '
			  <tr>
				<td class="row1">

					<table border="0">
						<tr>
						  <td valign="top"><img src="theme/images/folder.gif" /></td>
						  <td valign="top"><a href="index.php?plugin=forum&amp;action=board&amp;board=pt" class="forumlink">Private Topics</a><br />
							  <span class="gensmall">View private topics between you and other users.</span></td>
						</tr>
					  </table>
				  </td>
					<td class="row1" align="center"><span class="gensmall">' . $ntopics . '</span></td>
					<td class="row1" align="center"><span class="gensmall">' . $nposts . '</span></td>
					<td class="row1" align="center"><span class="gensmall">' . $lastpost . '</span></td>
				  </tr>	';

        // stop PT loop (pre)
    }

    // View all boards available
    $querycats = "SELECT id, name FROM forum_cats WHERE modonly <= '$userdata[editor]' ORDER BY `order` ASC";
    $resultcats = bcs_query($querycats) or bcs_error("<b>SQL ERROR</b> in <br>file " . __FILE__ . " on line " . __LINE__ . "<br><br><b>" . $query . "</b><br><br>" . mysql_error());



    while($rowcats = mysql_fetch_array($resultcats)) {


        // View all boards available
        $query = "SELECT id, name, description FROM forum_boards WHERE forumcat = '$rowcats[id]' ORDER BY `order` ASC";
        $result = bcs_query($query) or bcs_error("<b>SQL ERROR</b> in <br>file " . __FILE__ . " on line " . __LINE__ . "<br><br><b>" . $query . "</b><br><br>" . mysql_error());

        $catrow_content .= '
			  <tr>
				<td colspan="4" class="categorybar">

					  <table border="0">
						<tr>
						  <td><img src="theme/images/category_icon.gif" /></td>
						  <td><span class="cattitle">' . $rowcats["name"] . '</span></td>
						</tr>
					  </table>

			 	 </td>
			  </tr>
			  ';

        while($row = mysql_fetch_array($result)) {



            $ntopics = 0;
            $nposts = 0;


            $topic_read = true;

            $query2 = "SELECT id, time FROM forum_topics WHERE board_id = $row[id]";
            $result2 = bcs_query($query2) or bcs_error("<b>SQL ERROR</b> in <br>file " . __FILE__ . " on line " . __LINE__ . "<br><br><b>" . $query . "</b><br><br>" . mysql_error());
            $ntopics = mysql_num_rows($result2);
            while($row2 = mysql_fetch_array($result2)) {
                $query3 = "SELECT COUNT(id) as c FROM forum_posts WHERE topic_id = $row2[id]";
                $result3 = bcs_query($query3) or bcs_error("<b>SQL ERROR</b> in <br>file " . __FILE__ . " on line " . __LINE__ . "<br><br><b>" . $query . "</b><br><br>" . mysql_error());
               
                $nposts += mysql_fetch_array($result3)['c'];

                if ($row2["time"] > $userdata["last_session"]) {
                    $topic_read = false;
                }
            }
            // Calculate topics & posts


            $query2 = "SELECT id, title, user, time, topic_id FROM forum_posts ORDER BY time DESC ";
            $result2 = bcs_query($query2) or bcs_error("<b>SQL ERROR</b> in <br>file " . __FILE__ . " on line " . __LINE__ . "<br><br><b>" . $query . "</b><br><br>" . mysql_error());
            while($row2 = mysql_fetch_array($result2)){
                $query3 = "SELECT board_id FROM forum_topics WHERE id = '$row2[topic_id]'";
                $result3 = bcs_query($query3) or bcs_error("<b>SQL ERROR</b> in <br>file " . __FILE__ . " on line " . __LINE__ . "<br><br><b>" . $query . "</b><br><br>" . mysql_error());
                $row3 = mysql_fetch_array($result3);
                if ($row3["board_id"] === $row["id"]) {
                    // We found one !
                    if (strlen($row2["title"]) > $max_title_length) {
                        $limitname = substr($row2["title"], 0, $max_title_length) . "...";
                    } else {
                        $limitname = $row2["title"];
                    }
                    $lastpost = timeAgo($row2["time"]) . ' ago<br>by ' . memberLink($row2["user"]) . ' <a href="index.php?plugin=forum&amp;action=topic&amp;topic=' . $row2["topic_id"] . '#' . $row2["id"] . '"><img src="theme/images/icon_latest_reply.gif"></a>';
                    break;
                } else {
                    $lastpost = "";
                }
            }



            $catrow_content .= '
			  <tr>
				<td class="row1">

					<table border="0">
						<tr>
						  <td valign="top"><img src="theme/images/folder.gif" /></td>
						  <td valign="top"><a href="index.php?plugin=forum&amp;action=board&amp;board=' . $row["id"] . '" class="forumlink">' . $row["name"] . '</a><br />
							  <span class="gensmall">' . $row["description"] . '</span></td>
						</tr>
					  </table>
				  </td>
					<td class="row1" align="center"><span class="gensmall">' . $ntopics . '</span></td>
					<td class="row1" align="center"><span class="gensmall">' . $nposts . '</span></td>
					<td class="row1" align="center"><span class="gensmall">' . $lastpost . '</span></td>
				  </tr>	';

            unset($nposts);
        }
    }

//		' . ($s_auth ? '
//		<a href="forum.php?action=board&board=rt" class="gensmall">View posts since last visit</a><br />
//                <a href="forum.php?action=board&board=mt" class="gensmall">View your posts</a><br />
//		' : '') . '
//		<a href="forum.php?action=board&board=ut" class="gensmall">View unanswered posts</a></td>

    $nonline = getNumberOfOnlineMembers();


    $c_main = '

<table width="100%" border="0" cellpadding="0" cellspacing="0" style="border-collapse:collapse">
	<tr>
	  <td class="tableborder">


		  <table width="100%" border="1" cellspacing="0" class="forumcontainer">
			  <tr>
				<td width="73%" class="forumcolumns">&nbsp;Board&nbsp;</td>
				<td width="6%" class="forumcolumns">&nbsp;Topics&nbsp;</td>
				<td width="6%" class="forumcolumns">&nbsp;Posts&nbsp;</td>
				<td width="15%" class="forumcolumns">&nbsp;Last Post&nbsp;</td>
			  </tr>

<!-- BEGIN catrow -->

' . $catrow_content . '


<!-- BEGIN forumrow -->


<!-- END forumrow -->
<!-- END catrow -->

			</table>


		</td>
	</tr>
  </table>


<br />

<table width="100%" border="0" cellpadding="0" cellspacing="0">
	<tr>
		<td class="tableborder">

	  		<table width="100%" border="0" cellspacing="1" style="border-collapse: collapse;">
				<tr>
					<td colspan="2" bgcolor="#0C0C0C" class="categorybar">

						<table border="0">
							<tr>
								<td><img src="theme/images/category_icon.gif" /></td>
								<td><span class="cattitle"><span class="cattitle">Statistics</a></span></td>
							</tr>
						</table>

					</td>
				</tr>
				<tr>
					<td class="row1" align="center" valign="middle" rowspan="2" width="6%"><img src="theme/images/whosonline.gif" alt="Who is Online" /></td>
					<td class="row1" align="left" width="94%"><span class="gensmall">In total, our players have made about <b>' . getRowCount("forum_posts") . '</b> forum posts.<br />We have <b>' . getRowCount("bcs_users WHERE id > 0") . '</b> registered players!<br />The newest registered player is <b>' . getNewestMember() . '</b>.</span></td>
				</tr>
			</table>
		</td>
	</tr>
</table>


<br />

<table cellspacing="3" border="0" align="center" cellpadding="0">
  <tr>
	<td width="20" align="center"><img src="theme/images/folder_new.gif" alt="New posts"/></td>
	<td><span class="gensmall">New posts</span></td>
	<td>&nbsp;&nbsp;</td>
	<td width="20" align="center"><img src="theme/images/folder.gif" alt="No new posts" /></td>
	<td><span class="gensmall">No new posts</span></td>
	<td>&nbsp;&nbsp;</td>
	<td width="20" align="center"><img src="theme/images/folder_lock.gif" alt="Forum is locked" /></td>
	<td><span class="gensmall">Forum is locked</span></td>
  </tr>
</table>

	';
}

$c_main = '
<table width="100%" cellspacing="0" cellpadding="5" border="0" align="center">
	<tr><td valign="top">  ' . $c_main . '
</td>
  </tr></table>

';

//if ( $s_editor ) $s_admin = false;

if ( $userdata["pending_editor"] ) $s_editor = true;

?>
