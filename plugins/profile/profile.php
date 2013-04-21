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




$plugin_name = "Profile";
$plugin_version = "0.1.0";
$plugin_author = "Beather (admin@ironbane.com)";

if (!defined('BCS')) {
    die("ERROR");
}



if ( $s_editor ) {
  if ( isset($banuser) ) {
    if ( is_numeric($banuser) ) {

          AddTeamActionSelf("Ban user ".$banuser, "banuser", $banuser);

          $query = "UPDATE bcs_users set banned = 1 WHERE id = '$banuser' AND editor = 0 ";
          $result = mysql_query($query) or bcs_error("<b>SQL ERROR</b> in <br>file " . __FILE__ . " on line " . __LINE__ . "<br><br><b>" . $query . "</b><br><br>" . mysql_error());


          $user = $banuser;
    }
  }

  if ( isset($unbanuser) ) {
    if ( is_numeric($unbanuser) ) {

          AddTeamActionSelf("Unban user ".$unbanuser, "unbanuser", $unbanuser);

          $query = "UPDATE bcs_users set banned = 0 WHERE id = '$unbanuser' AND editor = 0";
          $result = mysql_query($query) or bcs_error("<b>SQL ERROR</b> in <br>file " . __FILE__ . " on line " . __LINE__ . "<br><br><b>" . $query . "</b><br><br>" . mysql_error());

          $user = $unbanuser;
    }
  }
}

if (isset($n)) {
    if (is_string($n)) {
        //$n = ucwords(strtolower($n));
        $query = "SELECT id FROM bcs_users WHERE name = '$n' ";
        $result = mysql_query($query) or bcs_error("<b>SQL ERROR</b> in <br>file " . __FILE__ . " on line " . __LINE__ . "<br><br><b>" . $query . "</b><br><br>" . mysql_error());
        $row = mysql_fetch_array($result);

        $user = $row[id];
    }
}

$bar_width = "200";


$user = parseToDB($user);

if (!$user || !is_numeric($user))
    $user = $userdata[id];

$query = "SELECT * FROM bcs_users WHERE id = '$user'";
$result = mysql_query($query) or bcs_error("<b>SQL ERROR</b> in <br>file " . __FILE__ . " on line " . __LINE__ . "<br><br><b>" . $query . "</b><br><br>" . mysql_error());
$memberdata = mysql_fetch_array($result);




    $totalbattles = $memberdata[pvp_wins] + $memberdata[pvp_losses] + $memberdata[pvp_escapes];

    $notgiven = '<i>Not given</i>';



    $c_main = '


  <table width="100%" cellspacing="2" cellpadding="2" border="0" align="center">
	<tr>
	  <td align="left" valign="bottom" colspan="2"><span class="maintitle">' . $memberdata[name] . '\'s Profile</span></td>
	  <td align="right" valign="bottom" nowrap="nowrap"></td>
	</tr>
	<tr>
	  <td align="left" valign="middle" class="nav" width="100%"><span class="nav"><a href="forum.php" class="nav">Ironbane Forum</a></td>
	  <td align="right" valign="bottom" class="nav" nowrap="nowrap"></td>
	</tr>
  </table>

<table cellspacing="0" cellpadding="5" border="0">
  <tr>
	<td valign="top" nowrap="nowrap">
	  <table width="100%" cellpadding="2" cellspacing="1" border="0" class="forumline">
	   <tr>
		<td class="catHead" height="25"><span class="genmed"><b>About ' . $memberdata[name] . '</b>' . ($memberdata[id] == $userdata[id] ? '<span class="gensmall"> [<a href="preferences.php">Edit my profile</a>]</span>' : '') . '' . ($s_auth ? '<span class="gensmall"> [<a href="admin.php">Administration panel</a>]</span>' : '') . '</span></td>
	   </tr>

		<td class="row1" align="center">
                <table width="100%" border="0" cellspacing="1" cellpadding="3">

    '.($memberdata[banned]?'
  					<tr>
                                          <td valign="middle" align="right" nowrap="nowrap"></td>
					  <td width="100%"><span class="genmed"><b>Banned</b></span></td>
					</tr> ':'
					<tr>
                                          <td valign="middle" align="right" nowrap="nowrap"></td>
					  <td width="100%">' . (empty($memberdata[forum_avatar]) ? '<img src="theme/images/noavatar.png">' : '<img src="' . $memberdata[forum_avatar] . '">') . '</td>
					</tr>
                      ').'
'.($s_editor?'
    '.(!$memberdata[banned]?'
  					<tr>
                                          <td valign="middle" align="right" nowrap="nowrap"></td>
					  <td width="100%"><span class="genmed"><a href="user.php?banuser=' . $user . '">Ban this user</a></span></td>
					</tr>
                                       ':'
  					<tr>
                                          <td valign="middle" align="right" nowrap="nowrap"></td>
					  <td width="100%"><span class="genmed"><a href="user.php?unbanuser=' . $user . '">Unban this user</a></span></td>
					</tr>
    ').'
':'').'
					<tr>
                                          <td valign="middle" align="right" nowrap="nowrap"></td>
					  <td width="100%"><span class="genmed"><a href="http://www.ironbane.com/forum.php?action=reply&board=pt&startp=' . $memberdata[name] . '">Start a private topic with ' . $memberdata[name] . '</a></span></td>
					</tr>
					<tr>
					  <td valign="middle" align="right" nowrap="nowrap"><span class="genmed"><strong>Joined</strong>&nbsp;</span></td>
					  <td width="100%"><span class="genmed">' . createDateSelf($memberdata[reg_date]) . '</span></td>
					</tr>
					<tr>
					  <td valign="middle" align="right" nowrap="nowrap"><span class="genmed"><strong>Last activity</strong>&nbsp;</span></td>
					  <td width="100%"><span class="genmed">' . timeAgo($memberdata[last_session]) . ' ago (' . ($memberdata[last_session] > $time - $onlinePeriod ? wrapcolor('Online!', $rpg_config[imprcolor]) : 'Offline') . ')</span></td>
					</tr>
' . ($s_admin ? '
					<tr>
					  <td valign="top" align="right" nowrap="nowrap"><span class="genmed"><strong>Last time online</strong>&nbsp;</span></td>
					  <td><span class="genmed">' . timeSince($memberdata[last_session]-$memberdata[previous_session]) . '</span></td>
					</tr>
					<tr>
					  <td valign="top" align="right" nowrap="nowrap"><span class="genmed"><strong>User ID</strong>&nbsp;</span></td>
					  <td><span class="genmed">' . $memberdata[id] . '</span></td>
					</tr>
					<tr>
					  <td valign="top" align="right" nowrap="nowrap"><span class="genmed"><strong>Pos</strong>&nbsp;</span></td>
					  <td><span class="genmed">' . $memberdata[rpg_x] . ' ' . $memberdata[rpg_y] . ' in zone ' . $memberdata[rpg_zone] . '</span></td>
					</tr>
' : '') . '
					<tr>
					  <td valign="top" align="right" nowrap="nowrap"><span class="genmed"><strong>Total posts</strong>&nbsp;</span></td>

					  <td valign="top"><span class="genmed">' . getTotalUserPosts($memberdata[id]) . '</span><br /><span class="gensmall">[' . round((((getTotalUserPosts($memberdata[id])) / (getRowCount("forum_posts"))) * 100), 2) . '% of total / ' . round(getTotalUserPosts($memberdata[id]) / (($time - $memberdata[reg_date]) / 86400), 2) . ' posts per day]</span> <br /><span class="gensmall"><a href="forum.php?action=board&board=up&user=' . $memberdata[id] . '" class="gensmall">Find all posts by ' . $memberdata[name] . '</a></span></td>
					</tr>
					<tr>
					  <td valign="middle" align="right" nowrap="nowrap"><span class="genmed"><strong>Status</strong>&nbsp;</span></td>
					  <td width="100%"><span class="genmed">' . getRank($memberdata[id]) . '</span></td>
					</tr>
					<tr>
					  <td valign="middle" align="right" nowrap="nowrap"><span class="genmed"><strong>Country</strong>&nbsp;</span></td>
					  <td><span class="genmed">' . (empty($memberdata[info_country]) ? $notgiven : $memberdata[info_country]) . '</span></td>
					</tr>
					<tr>
					  <td valign="middle" align="right" nowrap="nowrap"><span class="genmed"><strong>Location</strong>&nbsp;</span></td>
					  <td><span class="genmed">' . (empty($memberdata[info_location]) ? $notgiven : $memberdata[info_location]) . '</span></td>
					</tr>
					<tr>
					  <td valign="middle" align="right" nowrap="nowrap"><span class="genmed"><strong>Website</strong>&nbsp;</span></td>
					  <td><span class="genmed">' . (empty($memberdata[info_website]) ? $notgiven : '<a href="' . $memberdata[info_website] . '" rel="nofollow">' . $memberdata[info_website] . '</a>') . '</span></td>
					</tr>
					<tr>
					  <td valign="middle" align="right" nowrap="nowrap"><span class="genmed"><strong>Occupation</strong>&nbsp;</span></td>
					  <td><span class="genmed">' . (empty($memberdata[info_occupation]) ? $notgiven : $memberdata[info_occupation]) . '</span></td>
					</tr>
					<tr>
					  <td valign="top" align="right" nowrap="nowrap"><span class="genmed"><strong>Interests</strong>&nbsp;</span></td>
					  <td><span class="genmed">' . (empty($memberdata[info_interests]) ? $notgiven : $memberdata[info_interests]) . '</span></td>
					</tr>
					<tr>
					  <td valign="top" align="right" nowrap="nowrap"><span class="genmed"><strong>Real name</strong>&nbsp;</span></td>
					  <td><span class="genmed">' . (empty($memberdata[info_realname]) ? $notgiven : $memberdata[info_realname]) . '</span></td>
					</tr>
					<tr>
					  <td valign="top" align="right" nowrap="nowrap"><span class="genmed"><strong>Age</strong>&nbsp;</span></td>
					  <td><span class="genmed">' . (empty($memberdata[info_birthday]) ? $notgiven : getAgeFromBirthday($memberdata[info_birthday])) . '</span></td>
					</tr>
					<tr>
					  <td valign="top" align="right" nowrap="nowrap"><span class="genmed"><strong>Gender</strong>&nbsp;</span></td>
					  <td><span class="genmed">' . (empty($memberdata[info_gender]) ? $notgiven : ($memberdata[info_gender] == 1 ? 'Male' : 'Female')) . '</span></td>
					</tr>

' . ($s_admin ? '
					<tr>
					  <td valign="top" align="right" nowrap="nowrap"><span class="genmed"><strong>Last page</strong>&nbsp;</span></td>
					  <td><span class="genmed">' . $memberdata[last_page] . '</span></td>
					</tr>
					<tr>
					  <td valign="top" align="right" nowrap="nowrap"><span class="genmed"><strong>Current tutorial</strong>&nbsp;</span></td>
					  <td><span class="genmed">' . $memberdata[rpg_tut] . '</span></td>
					</tr>
' : '') . '
				  </table>

                    </td>
	   </tr>
	  </table>
	</td>

         </tr>

</table>
';

?>
