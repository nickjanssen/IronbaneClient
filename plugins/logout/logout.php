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



//if ( $confirm ) {
        $query = "UPDATE bcs_users SET last_session = last_session - '$onlinePeriod' WHERE id = '$userdata[id]'";
        $result = bcs_query($query) or bcs_error("<b>SQL ERROR</b> in <br>file " . __FILE__ . " on line " . __LINE__ . "<br><br><b>" . $query . "</b><br><br>" . mysql_error());

	unset($_SESSION['logged_in']);
	unset($_SESSION['user_id']);
	setcookie("bcs_username", "", time() - 3600);
	setcookie("bcs_password_hash", "", time() - 3600);

        //writeChatMessage("<i>".  memberLink($userdata[id])." logged out.</i>");

	header("Location: index.php");
	//bcs_die("You are now logged out!");
//}
//else {
//	if ( $_COOKIE['bcs_username'] ) {
//		$cnote = "<br /><br /><span class=\"gensmall\"><b>Note:</b> This will disable your automatic log in.</span>";
//	}
//	bcs_die("Are you sure you wish to log out?".$cnote."<br /><br /><form action=\"index.php?plugin=logout\" method=\"POST\"><input type=\"submit\" name=\"confirm\" value=\"Log me out\" class=\"mainoption\" /></form>", "none");
//}

?>
