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






$c_title = "Preferences";

if (!defined('BCS')) {
    die("ERROR");
}



if ($submit) {
    $safe_name = strip_tags($_POST['username']);
    $safe_pass = strip_tags($_POST['password']);
    $safe_email = strip_tags($_POST['email']);
    $safe_pass_old = strip_tags($_POST['password_old']);
    $safe_pass_confirm = strip_tags($_POST['password_confirm']);
    $safe_gmt = strip_tags($_POST['gmt']);
    $safe_avatar = strip_tags($_POST['avatar']);
    $safe_sig = strip_tags($_POST['sig']);

    $safe_bday = strip_tags($_POST['bday']);
    $safe_bmonth = strip_tags($_POST['bmonth']);
    $safe_byear = strip_tags($_POST['byear']);

    $avatarfile = parseToDB($avatarfile);

    // Lulz
    if ($safe_pass == "penis") {
        bcs_die("Your password is too short!", "back");
    }

    $list = "info_realname,info_country,info_location,info_gender,info_birthday,info_occupation,info_interests,info_website";
    $list_b = "Name,Country,Location,Gender,Age,Occupation,Interests,Website";
    $list_ex = explode(',', $list);
    $list_ex_b = explode(',', $list_b);
    for ($x = 0; $x < count($list_ex); $x++) {

        if (strlen(parseToDB($_POST[$list_ex[$x]])) > 50) {
            bcs_die('Sorry, but your ' . $list_ex_b[$x] . ' is a bit too long! Would you mind shortening it? (Max 50 chars.)', 'javascript:history.back()');
        }

        if ($list_ex[$x] == "info_birthday") {
            $safe_info .= $list_ex[$x] . " = '$safe_bday-$safe_bmonth-$safe_byear', ";
        } else {
            $safe_info .= $list_ex[$x] . " = '" . parseToDB($_POST[$list_ex[$x]]) . "', ";
        }
    }


    $query = "SELECT * FROM bcs_users WHERE email = '$safe_email' AND id NOT LIKE '$userdata[id]'";
    $result = bcs_query($query) or bcs_error("<b>SQL ERROR</b> in <br>file " . __FILE__ . " on line " . __LINE__ . "<br><br><b>" . $query . "</b><br><br>" . mysql_error());

    if (mysql_num_rows($result) > 0) {
        bcs_die('Sorry, that e-mail is already taken.', 'javascript:history.back()');
    }


    if ($safe_pass_old != "") {
        if ($safe_pass_old != $userdata[pass]) {
            bcs_die('Your old password does not match your current one.', 'javascript:history.back()');
        }
        if (strlen($safe_pass) < 4 || strlen($safe_pass) > 20) {
            bcs_die('Your new password must contain atleast 4, and maximum 20 characters.', 'javascript:history.back()');
        }
        if (strlen($safe_email) < 8 || strlen($safe_email) > 30) {
            bcs_die('Your new e-mail must contain atleast 8, and maximum 30 characters.', 'javascript:history.back()');
        }
        if ($safe_pass != $_POST['password']) {
            bcs_die('Your new password contains invalid characters. Please try another password.', 'javascript:history.back()');
        }
        if ($safe_pass != $safe_pass_confirm) {
            bcs_die('The new passwords you entered do not match. Please try again.', 'javascript:history.back()');
        }
        $pass_sql = " pass = '$safe_pass',";
    } else {
        $pass_sql = "";
    }

    if (!$safe_email == "" && (!strstr($safe_email, "@") || !strstr($safe_email, ".") )) {
        bcs_die('Your new e-mail is invalid. Please try again.', 'javascript:history.back()');
    }
    if (!is_numeric($safe_gmt) || $safe_gmt < -12 || $safe_gmt > 14) {
        die();
    }

    // if ( !preg_match("#^((ht|f)tp://)([^ \?&=\#\"\n\r\t<]*?(\.(jpg|jpeg|gif|png))$)#is", $safe_avatar) && strlen($safe_avatar) != 0 ) {
    // bcs_die('Sorry, the file format of your avatar is not allowed.','javascript:history.back()');
    // }

    if (strlen($safe_sig) > 255) {
        die();
    }

    $new_show_email = $_POST['show_email'] ? 1 : 0;
    $new_receive_email = $_POST['receive_email'] ? 1 : 0;

    //echo "test: ".$avatarfile."<br>";

    if (!empty($avatarfile)) {

        //echo "test<br>";

        if (!empty($userdata[forum_avatar])) {
            if ($userdata[forum_avatar] != "theme/images/noavatar.png") {
                @unlink("" . $userdata[forum_avatar]);
            }
        }

        $extensie_bestand = pathinfo($_FILES['avatarfile']['name']);
        $extensie_bestand = strtolower($extensie_bestand[extension]);



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
        //$_FILES["file"]["name"] = $name.".".$extensie_bestand;


        if (file_exists("uploads/avatars/" . $_FILES["avatarfile"]["name"])) {
            //echo $_FILES["file"]["name"] . " already exists. ";
            bcs_die("An error occured while uploading.<br><br>Message: " . $_FILES["avatarfile"]["name"] . " already exists.", "javascript:history.back(-1)");
        } else {
            move_uploaded_file($_FILES["avatarfile"]["tmp_name"], "uploads/avatars/" . $_FILES["avatarfile"]["name"]);
            //echo "Stored in: " . "upload/" . $_FILES["file"]["name"];
        }



        //echo "URL: http://www.nickmania.com/uploads/" . $_FILES["file"]["name"];

        $safe_avatar = "uploads/avatars/" . $_FILES["avatarfile"]["name"];
    } else {
        $safe_avatar = $userdata[forum_avatar];
    }


    // Update a row
    $query = "UPDATE bcs_users SET show_email = '$new_show_email', receive_email = '$new_receive_email', email = '$safe_email'," . $pass_sql . " gmt = '$safe_gmt', forum_avatar = '$safe_avatar', " . $safe_info . " forum_sig = '$safe_sig' WHERE id = '$userdata[id]'";
    $result = bcs_query($query) or bcs_error("<b>SQL ERROR</b> in <br>file " . __FILE__ . " on line " . __LINE__ . "<br><br><b>" . $query . "</b><br><br>" . mysql_error());

    bcs_die('Your preferences have been saved.', 'preferences.php');
} else {

    for ($x = 0; $x < 15; $x++) {
        if ($x == $userdata[gmt]) {
            $extra = " SELECTED";
        } else {
            $extra = "";
        }
        $gmtselect .= "<option value=\"" . $x . "\"" . $extra . ">GMT + " . $x . "</option>";
    }
    for ($x = -1; $x > -13; $x--) {
        if ($x == $userdata[gmt]) {
            $extra = " SELECTED";
        } else {
            $extra = "";
        }
        $gmtselect .= "<option value=\"" . $x . "\"" . $extra . ">GMT - " . abs($x) . "</option>";
    }


    if ($userdata[info_gender] == 1) {
        $checked_1 = "CHECKED";
    }
    if ($userdata[info_gender] == 2) {
        $checked_2 = "CHECKED";
    }


    $st_year = "1900"; //Starting Year
    $month_names = array("January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December");

    list($day, $month, $year) = explode("-", $userdata[info_birthday]);


    $day_select .= '<select name="bday" id="day">';

    for ($i = 1; $i <= 31; $i++) {
        $day_select .= '<option ';
        if ($i == $day) {
            $day_select .= 'selected="selected" ';
        }
        $day_select .= ' value="' . $i . '">' . $i . '</option>';
    }

    $day_select .= ' </select>';

    $month_select .= '<select name="bmonth" id="month">';

    for ($i = 1; $i <= 12; $i++) {
        $month_select .= '<option ';
        if ($i == $month) {
            $month_select .= 'selected="selected" ';
        }
        $month_select .= ' value="' . $i . '">' . $month_names[$i - 1] . '</option>';
    }

    $month_select .= ' </select>';
    $year_select .= '<select name="byear" id="year">';

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
                        <td align="left" width="50%" valign="top"><span class="gen"><input type="text" class="text" value="' . $userdata[email] . '" name="email" maxlength="30" size="30" /></span></td>
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
                        <td align="left" width="50%" valign="top"><input size="40" type="text" class="text" name="info_realname" maxlength="255" value="' . $userdata[info_realname] . '" /></td>
                        </tr>
                        <tr>
                        <td align="right" width="50%"><span class="gen">Country</span></td>
                        <td align="left" width="50%" valign="top"><input size="40" type="text" class="text" name="info_country" maxlength="255" value="' . $userdata[info_country] . '" /></td>
                        </tr>
                        <tr>
                        <td align="right" width="50%"><span class="gen">Location</span></td>
                        <td align="left" width="50%" valign="top"><input size="40" type="text" class="text" name="info_location" maxlength="255" value="' . $userdata[info_location] . '" /></td>
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
                        <td align="left" width="50%" valign="top"><input size="40" type="text" class="text" name="info_occupation" maxlength="255" value="' . $userdata[info_occupation] . '" /></td>
                        </tr>
                        <tr>
                        <td align="right" width="50%"><span class="gen">Interests</span></td>
                        <td align="left" width="50%" valign="top"><input size="40" type="text" class="text" name="info_interests" maxlength="255" value="' . $userdata[info_interests] . '" /></td>
                        </tr>
                        <tr>
                        <td align="right" width="50%"><span class="gen">Website</span></td>
                        <td align="left" width="50%" valign="top"><input size="40" type="text" class="text" name="info_website" maxlength="255" value="' . $userdata[info_website] . '" /></td>
                        </tr>
                        <tr>
                        <td align="right" width="50%"><span class=gen>Upload an avatar</span><br /><span class="gensmall">Max. 100 KB, Max. 100x100 pixels.</span></td>
                        <td align="left" width="50%" valign="top"><span class=gen><input type="file" name="avatarfile"></span></td>
                        </tr>
                        <tr>
                        <td align="right" width="50%"><span class="gensmall">Uploading a new avatar will replace your old one.</span></td>
                        <td align="left" width="50%" valign="top"><span class=gen>' . (empty($userdata[forum_avatar]) ? '<img src="theme/images/noavatar.png">' : '<img src="' . $userdata[forum_avatar] . '">') . '</span></td>
                        </tr>
                        <tr>
                        <td align="right" width="50%"><span class="gen">Signature</span><br /><span class="gensmall">Max. 255 characters.</td>
                        <td align="left" width="50%" valign="top"><textarea class="text" name="sig" maxlength="255" cols="40" rows="4">' . $userdata[forum_sig] . '</textarea></td>
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
                        <td align="left" width="50%" valign="top"><input type="checkbox" name="show_email" ' . ($userdata[show_email] == 1 ? "CHECKED" : "") . '></td>
                        </tr>
                        <tr>
                        <td align="right" width="50%"><span class="gen">Allow Ironbane to send me e-mail</span><br /><span class="gensmall">Allow Ironbane to send you notifications by e-mail (such as battle challenges, private topics, etc.)<br><b>Note:</b> Be sure to check your spam folder.</td>
                        <td align="left" width="50%" valign="top"><input type="checkbox" name="receive_email" ' . ($userdata[receive_email] == 1 ? "CHECKED" : "") . '></td>
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
?>
