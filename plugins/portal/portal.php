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

$show_chat = true;

$query = "SELECT a.* from (SELECT * FROM forum_posts ORDER BY time ASC) as a, (SELECT * FROM forum_topics where board_id = 7) as b WHERE a.topic_id = b.id GROUP BY topic_id ORDER BY time DESC LIMIT 10";
$result = bcs_query($query) or bcs_error("<b>SQL ERROR</b> in <br>file ".__FILE__." on line ".__LINE__."<br><br><b>".$query."</b><br><br>".mysql_error());
for ($x = 0; $x < mysql_num_rows($result); $x++) {
    $row = mysql_fetch_array($result);

//    $fetchpost_row_content .= '
//		<table width="100%" cellpadding="2" cellspacing="1" border="0" class="forumline">
//		  <tr>
//			<td class="catHead" height="25"><span class="genmed"><b><a href="forum.php?action=topic&amp;topic='.$row[topic_id].'" class=gen>'.$row[title].'</a></b></span></td>
//		  </tr>
//		  <tr>
//			<td class="row1" align="left" height="24"><span class="gensmall">Posted by <b>'.  memberLink($row[user]).'</b> on '.createDate($row[time], $userdata[gmt]).'</span></td>
//		  </tr>
//		  <tr>
//			<td class="row2" align="left"><span class="gen" style="line-height:150%">'.post_parse($row[content]).'</span></td>
//		  </tr>
//		  <tr>
//			<td class="row3" align="left" height="24"><span class="gensmall"><a href="forum.php?action=topic&amp;topic='.$row[topic_id].'">'.  (getRowCount("forum_posts WHERE topic_id = '$row[topic_id]'")-1).' Comments</a> - <a href="forum.php?action=reply&amp;topic='.$row[topic_id].'">Add Comment</a></span></td>
//		  </tr>
//		</table>
//
//		<br>';
    $fetchpost_row_content .= '
<h2 style="display:inline"><a href="forum.php?action=topic&amp;topic='.$row[topic_id].'">'.$row[title].'</a></h2>
    <div class="gensmall">by <b>'.  memberLink($row[user]).'</b> on '.createDate($row[time], $userdata[gmt]).'</div>
        '.$spacer.'
        '.post_parse($row[content]).'
            <hr>
';
}
$fetchpost_row_content .= $spacer.'<div align="center" class="gen"><a href="index.php?plugin=forum&amp;action=board&amp;board=7">View older news topics</a></div>';

$query = "SELECT a.* FROM forum_posts as a, forum_topics as b WHERE a.topic_id = b.id AND b.private != 1 ORDER BY time DESC LIMIT 10";
$result = bcs_query($query) or bcs_error("<b>SQL ERROR</b> in <br>file ".__FILE__." on line ".__LINE__."<br><br><b>".$query."</b><br><br>".mysql_error());
for ($x = 0; $x < mysql_num_rows($result); $x++) {
    $row = mysql_fetch_array($result);

    $query2 = "SELECT id, title FROM forum_posts WHERE topic_id = '$row[topic_id]' ORDER BY time ASC LIMIT 1";
    $result2 = bcs_query($query2) or bcs_error("<b>SQL ERROR</b> in <br>file ".__FILE__." on line ".__LINE__."<br><br><b>".$query2."</b><br><br>".mysql_error());
    $row2 = mysql_fetch_array($result2);
    $title = $row2[title];
    if ( $row2[id]!=$row[id] ) {
        // We didnt make a new topic
        $title = "Reply to '".$title."'";
    }

	$recent_topic_row_content .= '&raquo; <a href="forum.php?action=topic&amp;topic='.$row[topic_id].'#'.$row[id].'" onMouseOver="document.all.recent_topics.stop()" onMouseOut="document.all.recent_topics.start()">'.$title.'</a><br>by '.  memberLink($row[user], ' onMouseOver="document.all.recent_topics.stop()" onMouseOut="document.all.recent_topics.start()"').' on '.  createDateSelf($row[time]).'<br><br>';
}

$noTitlePostFix = 1;
$c_title = "Ironbane MMO";


       // <br>Our players fought a total of <b>'. getRowCount("rpg_battles WHERE type = 1").'</b> battles during <b>'. getRowCount("rpg_wilderness_adventures").'</b> adventures.

$c_main = '
<h1>News</h1>

                '.$fetchpost_row_content.'
';


?>
