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

$link = mysqli_connect($mysql_hostname, $mysql_user, $mysql_password, $mysql_db) or bcs_error("Could not connect to MySQL.");

$c_title = "Preferences";

if (isset($submit)) {
    
    $pass_new = phash($_POST['password']);
    $email = strip_tags($_POST['email']);
    $pass_old = phash($_POST['password_old']);
    $pass_confirm = phash($_POST['password_confirm']);
    $gmt = isset($_POST['gmt']) ? strip_tags($_POST['gmt']) : $userdata['gmt'];
    $sig = strip_tags($_POST['sig']);

    $bday = is_numeric($_POST['bday']) ? $_POST['bday'] : 1;
    $bmonth = is_numeric($_POST['bmonth']) ? $_POST['bmonth'] : 1;
    $byear = is_numeric($_POST['byear']) ? $_POST['byear'] : 2013;
    
    $show_email = isset($_POST['show_email']) ? ($_POST['show_email']? 1 : 0) : 0;  
    $receive_email = isset($_POST['receive_email']) ? ($_POST['receive_email'] ? 1 : 0) : 0;  

    // Lulz c===3
    if ($pass_new == "penis") {
      bcs_die("Your password is too short!", "javascript:history.back()");
    }

    $info_item_list = array('info_realname','info_country','info_location','info_gender','info_occupation','info_interests','info_website');
    $info_item_title_list = array('Name','Country','Location','Gender','Age','Occupation','Interests','Website');
    
    $info = Array();
    for ($i = 0; $i < count($info_item_list); $i++) {
      
      if (strlen($_POST[$info_item_list[$i]]) > 50) {
        bcs_die('Sorry, but your ' . $info_item_title_list[$i] . ' is a bit too long! Would you mind shortening it? (Max 50 chars.)', 'javascript:history.back()');
      }
      
      $info[$info_item_list[$i]] = !empty($_POST[$info_item_list[$i]]) ? $_POST[$info_item_list[$i]] : $userdata[$info_item_list[$i]];
      
    }
    $info["info_birthday"] = "$bday-$bmonth-$byear";
    
    


    $prepared_query = "SELECT id FROM bcs_users WHERE email=? AND id NOT LIKE ?";
    
    if($stmt = mysqli_prepare($link, $prepared_query)) {
    
      mysqli_stmt_bind_param($stmt, "si", $email, $userdata['id']);
        
      mysqli_stmt_execute($stmt) or bcs_error("<b>SQL ERROR</b> in <br>file ". __FILE__ ." on line ". __LINE__ ."<br><br><b>" . $prepared_query . "</b><br><br>" . mysqli_stmt_error());
      
      if (mysqli_stmt_num_rows($stmt) > 0) {
        bcs_die('Sorry, that e-mail is already taken.', 'javascript:history.back()');
      }
      
      mysqli_stmt_close($stmt);
      
    }


    if ($pass_old != "") {
    
      if ($pass_old != $userdata['pass']) {
        bcs_die('Your old password does not match your current one.', 'javascript:history.back()');
      }
      if (strlen($pass_new) < 4 || strlen($pass_new) > 20) {
          bcs_die('Your new password must contain at least 4, and maximum 20 characters.', 'javascript:history.back()');
      }
      if (strlen($email) < 8 || strlen($email) > 30) {
          bcs_die('Your new e-mail must contain at least 8, and maximum 30 characters.', 'javascript:history.back()');
      }
      if ($pass_new != $pass_confirm) {
          bcs_die('The new passwords you entered do not match. Please try again.', 'javascript:history.back()');
      }
      
    }
    if ($email == "" || !filter_var($email, FILTER_VALIDATE_EMAIL)) { 
      bcs_die('Your new e-mail is invalid. Please try again.', 'javascript:history.back()');
    }
    if (!is_numeric($gmt) || $gmt < -12 || $gmt > 14) {
        bcs_die('Your current local time and date (gmt) are invalid.', 'javascript:history.back()');
    }


    if (strlen($sig) > 255) {
        bcs_die('Your signature exceeds the manximum character limit. (Max 255 chars.) ', 'javascript:history.back()');
    }
    
    $avatar = isset($userdata['forum_avatar']) ? $userdata['forum_avatar'] : "";
    if (isset($_FILES['avatarfile'])) {
      if(!empty($_FILES['avatarfile']['name'])) {

        $extensie_bestand = pathinfo($_FILES['avatarfile']['name']);
        $extensie_bestand = strtolower($extensie_bestand['extension']);



        if ($extensie_bestand != "jpg" && $extensie_bestand != "png" && $extensie_bestand != "gif") {
            bcs_die("The file you are uploading must be either .JPG, .PNG or .GIF (" . $extensie_bestand . ").", "javascript:history.back(-1)");
        }

        if ($_FILES["avatarfile"]["size"] > 500000) {
            bcs_die("The file you are uploading is over 500 KB.", "javascript:history.back(-1)");
        }

        list($width, $height, $type, $attr) = getimagesize($_FILES["avatarfile"]["tmp_name"]);

        if ( $width > 100 || $height > 100 ) {
            bcs_die("The image you are uploading is too wide or too high! Please make sure it does not exceed 100x100 pixels!", "javascript:history.back(-1)");
        }

        $_FILES["avatarfile"]["name"] = md5((time() + rand(1, 5000)) . $_FILES['avatarfile']['name']) . "." . $extensie_bestand;
        
        if (!empty($avatar)) {
            if ($userdata[forum_avatar] != "theme/images/noavatar.png") {
                @unlink("" . $avatar);
            }
        }
        
        
        if (file_exists("uploads/avatars/" . $_FILES["avatarfile"]["name"])) {
            //echo $_FILES["file"]["name"] . " already exists. ";
            bcs_die("An error occured while uploading.<br><br>Message: " . $_FILES["avatarfile"]["name"] . " already exists.", "javascript:history.back(-1)");
        } else {
            move_uploaded_file($_FILES["avatarfile"]["tmp_name"], "uploads/avatars/" . $_FILES["avatarfile"]["name"]);
            //echo "Stored in: " . "upload/" . $_FILES["file"]["name"];
        }



        //echo "URL: http://www.nickmania.com/uploads/" . $_FILES["file"]["name"];

        $avatar = "uploads/avatars/" . $_FILES["avatarfile"]["name"];
      }
    }


    // Update a row
    $prepared_query = "UPDATE bcs_users SET show_email=?, receive_email=?, email=?, pass=?, gmt=?, forum_avatar = ?, ".
      "info_realname=?, info_country=?, info_location=?, info_gender=?, info_birthday=?, info_occupation=?, info_interests=?, info_website=?, ".
      "forum_sig=? WHERE id=?";
    if($stmt = mysqli_prepare($link, $prepared_query)) {
    
      $pass = (($pass_old != "") ? $pass_new : $userdata['pass']);
      mysqli_stmt_bind_param($stmt, "iisssssssisssssi", $show_email, $receive_email, $email, $pass, $gmt, $avatar,
        $info['info_realname'], $info['info_country'], $info['info_location'], $info['info_gender'],
        $info['info_birthday'], $info['info_occupation'], $info['info_interests'], $info['info_website'],
        $sig, $userdata['id']);

      
      mysqli_stmt_execute($stmt) or bcs_error(mysqli_stmt_error($stmt));
      
      mysqli_stmt_close($stmt);
      
    } else {
       bcs_error(mysqli_error($link));
    }
    
    bcs_die('Your preferences have been saved.', 'preferences.php');
} else {
    $gmtselect = "";
    for ($x = 0; $x < 15; $x++) {
        if ($x == $userdata['gmt']) {
            $extra = " SELECTED";
        } else {
            $extra = "";
        }
        $gmtselect .= "<option value=\"" . $x . "\"" . $extra . ">GMT + " . $x . "</option>";
    }
    for ($x = -1; $x > -13; $x--) {
        if ($x == $userdata['gmt']) {
            $extra = " SELECTED";
        } else {
            $extra = "";
        }
        $gmtselect .= "<option value=\"" . $x . "\"" . $extra . ">GMT - " . abs($x) . "</option>";
    }


    $checked_1 = ($userdata['info_gender'] == 1) ? "CHECKED" : "";
    $checked_2 = ($userdata['info_gender'] == 2) ? "CHECKED" : "";


    $st_year = "1900"; //Starting Year
    $month_names = array("January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December");

    list($day, $month, $year) = explode("-", $userdata['info_birthday']);


    $day_select = '<select name="bday" id="day">';

    for ($i = 1; $i <= 31; $i++) {
        $day_select .= '<option ';
        if ($i == $day) {
            $day_select .= 'selected="selected" ';
        }
        $day_select .= ' value="' . $i . '">' . $i . '</option>';
    }

    $day_select .= ' </select>';

    $month_select = '<select name="bmonth" id="month">';

    for ($i = 1; $i <= 12; $i++) {
        $month_select .= '<option ';
        if ($i == $month) {
            $month_select .= 'selected="selected" ';
        }
        $month_select .= ' value="' . $i . '">' . $month_names[$i - 1] . '</option>';
    }

    $month_select .= ' </select>';
    $year_select = '<select name="byear" id="year">';

    for ($i = 2011; $i >= $st_year; $i--) {
        $year_select .= '<option ';
        if ($i == $year) {
            $year_select .= ' selected="selected" ';
        }
        $year_select .= ' value="' . $i . '">' . $i . '</option>';
    }

    $year_select .= '</select>';


    $c_main = '

<form action="preferences.php" method="POST" enctype="multipart/form-data">
<table width="100%" cellspacing="0" cellpadding="5" border="0" align="center">
  <tr>
	<td valign="top">
        <h1>User Preferences</h1>
	  <table width="100%" cellpadding="2" cellspacing="1" border="0" class="forumline">
	   <tr>
		<td class="catHead" height="25"><span class="genmed"><b>Important stuff</b></span></td>
	   </tr>
	   <tr>
		<td class="row1" align="center">
                    <table width="100%" border="0" cellspacing="1" cellpadding="3">
                        <tr>
                        <td align="right" width="50%"><span class="gen">Old Password:</span><br /><span class="gensmall">If you would like to keep your password, leave this blank.</span></td>
                        <td align="left" width="50%" valign="top"><span class="gen"><input type="password" class="text" value="" name="password_old" maxlength="20" size="40" /></span></td>
                        </tr>
                        <tr>
                        <td align="right" width="50%"><span class="gen">New Password:</span><br /><span class="gensmall">Case-sensitive, min. 4 characters, max. 20 characters.</span></td>
                        <td align="left" width="50%" valign="top"><span class="gen"><input type="password" class="text" value="" name="password" maxlength="20" size="40" /></span></td>
                        </tr>
                        <tr>
                        <td align="right" width="50%"><span class="gen">Confirm New Password:</span></td>
                        <td align="left" width="50%" valign="top"><span class="gen"><input type="password" class="text" value="" name="password_confirm" maxlength="20" size="40" /></span></td>
                        </tr>
                        <tr>
                        <td align="right" width="50%"><span class="gen">E-mail Address:</span><br /><span class="gensmall">Your e-mail will NOT be shared with anyone, it is only used to send back your forgotten password.</span></td>
                        <td align="left" width="50%" valign="top"><span class="gen"><input type="text" class="text" value="' . $userdata['email'] . '" name="email" maxlength="30" size="30" /></span></td>
                        </tr>
                        <tr>
                        <td align="right" width="50%"><span class="gen">GMT Timezone: [<a href="http://wwp.greenwichmeantime.com/gmt-converter2.htm" target="_blank">?</a>]</span></td>
                        <td align="left" width="50%" valign="top">
                        <select name="gmt">
                        ' . $gmtselect . '
                        </select></td>
                        </tr>
                        <tr>
                        <td align="center" colspan="2"><input type="submit" name="submit" value="Update preferences" class="mainoption" /></td>
                        </tr>
                    </table>
               </td>
	   </tr>
	  </table>
	</td>
        </tr><tr>
	<td valign="top">
	  <table width="100%" cellpadding="2" cellspacing="1" border="0" class="forumline">
	   <tr>
		<td class="catHead" height="25"><span class="genmed"><b>Optional stuff</b></span></td>
	   </tr>
	   <tr>
		<td class="row1">
                <span class="gen">It would be awesome if you\'d tell us a bit more who you are and what you do!<br>This information will be put on your profile page.</span>
                    <table width="100%" border="0" cellspacing="1" cellpadding="3">

                        <tr>
                        <td align="right" width="50%"><span class="gen">Name</span></td>
                        <td align="left" width="50%" valign="top"><input size="40" type="text" class="text" name="info_realname" maxlength="255" value="' . $userdata['info_realname'] . '" /></td>
                        </tr>
                        <tr>
                        <td align="right" width="50%"><span class="gen">Country</span></td>
                        <td align="left" width="50%" valign="top"><input size="40" type="text" class="text" name="info_country" maxlength="255" value="' . $userdata['info_country'] . '" /></td>
                        </tr>
                        <tr>
                        <td align="right" width="50%"><span class="gen">Location</span></td>
                        <td align="left" width="50%" valign="top"><input size="40" type="text" class="text" name="info_location" maxlength="255" value="' . $userdata['info_location'] . '" /></td>
                        </tr>
                        <tr>
                        <td align="right" width="50%"><span class="gen">Gender</span></td>
                        <td align="left" width="50%" valign="top"><span class="gen"><table width="200" border="0" cellpadding="0" cellspacing="0">
                        <tr><td width="50%"><span class=gen><input type="radio" class="text" value="1" name="info_gender" ' . $checked_1 . '/> Male</span></td><td width="50%"><span class=gen><input type="radio" class="text" value="2" name="info_gender" ' . $checked_2 . '/> Female</span></td></tr></table></td>
                        </tr>
                        <tr>
                        <td align="right" width="50%"><span class="gen">Birthday</span></td>
                        <td align="left" width="50%" valign="top">' . $day_select . ' ' . $month_select . ' ' . $year_select . '</td>
                        </tr>
                        <tr>
                        <td align="right" width="50%"><span class="gen">Occupation</span></td>
                        <td align="left" width="50%" valign="top"><input size="40" type="text" class="text" name="info_occupation" maxlength="255" value="' . $userdata['info_occupation'] . '" /></td>
                        </tr>
                        <tr>
                        <td align="right" width="50%"><span class="gen">Interests</span></td>
                        <td align="left" width="50%" valign="top"><input size="40" type="text" class="text" name="info_interests" maxlength="255" value="' . $userdata['info_interests'] . '" /></td>
                        </tr>
                        <tr>
                        <td align="right" width="50%"><span class="gen">Website</span></td>
                        <td align="left" width="50%" valign="top"><input size="40" type="text" class="text" name="info_website" maxlength="255" value="' . $userdata['info_website'] . '" /></td>
                        </tr>
                        <tr>
                        <td align="right" width="50%"><span class=gen>Upload an avatar</span><br /><span class="gensmall">Max. 100 KB, Max. 100x100 pixels.</span></td>
                        <td align="left" width="50%" valign="top"><span class=gen><input type="file" name="avatarfile"></span></td>
                        </tr>
                        <tr>
                        <td align="right" width="50%"><span class="gensmall">Uploading a new avatar will replace your old one.</span></td>
                        <td align="left" width="50%" valign="top"><span class=gen>' . (empty($userdata['forum_avatar']) ? '<img src="theme/images/noavatar.png">' : '<img src="' . $userdata['forum_avatar'] . '">') . '</span></td>
                        </tr>
                        <tr>
                        <td align="right" width="50%"><span class="gen">Signature</span><br /><span class="gensmall">Max. 255 characters.</td>
                        <td align="left" width="50%" valign="top"><textarea class="text" name="sig" maxlength="255" cols="40" rows="4">' . $userdata['forum_sig'] . '</textarea></td>
                        </tr>
                        <tr>
                        <td align="center" colspan="2"><input type="submit" name="submit" value="Update preferences" class="mainoption" /></td>
                        </tr>
                    </table>
               </td>
	   </tr>
	  </table>
	</td>
        </tr><tr>
	<td valign="top">
	  <table width="100%" cellpadding="2" cellspacing="1" border="0" class="forumline">
	   <tr>
		<td class="catHead" height="25"><span class="genmed"><b>Other stuff</b></span></td>
	   </tr>
	   <tr>
		<td class="row1" align="center">
                    <table width="100%" border="0" cellspacing="1" cellpadding="3">
                        <tr>
                        <td align="right" width="50%"><span class="gen">Show my e-mail address</span></td>
                        <td align="left" width="50%" valign="top"><input type="checkbox" name="show_email" ' . ($userdata['show_email'] == 1 ? "CHECKED" : "") . '></td>
                        </tr>
                        <tr>
                        <td align="right" width="50%"><span class="gen">Allow Ironbane to send me e-mail</span><br /><span class="gensmall">Allow Ironbane to send you notifications by e-mail (such as battle challenges, private topics, etc.)<br><b>Note:</b> Be sure to check your spam folder.</td>
                        <td align="left" width="50%" valign="top"><input type="checkbox" name="receive_email" ' . ($userdata['receive_email'] == 1 ? "CHECKED" : "") . '></td>
                        </tr>

                        <tr>
                        <td align="center" colspan="2"><input type="submit" name="submit" value="Update preferences" class="mainoption" /></td>
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
}

mysqli_close($link);
?>
