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




//if ( $_SERVER["HTTP_HOST"] == "localhost" ) {
//    setcookie("XDEBUG_SESSION", "netbeans-xdebug", time()+3600);
//    $XDEBUG_SESSION_START="netbeans-xdebug";
//}

if ( !file_exists("config.php") ) die("Config file not found!");
include("config.php");


mysql_connect($mysql_hostname, $mysql_user, $mysql_password) or bcs_error("Could not connect to MySQL: ".mysql_error());
mysql_select_db($mysql_db) or bcs_error("Could not select database: ".mysql_error());


session_start();

// Define BCS
define('BCS', true);


$query_count = 0;
function bcs_query($query) {
    global $query_count;
    $query_count++;
    static $query_count;
    return mysql_query($query);
}
function bcs_error($string) {

    $string = "".date('l jS \of F Y h:i:s A').":\n$string\n\n";

    // $filename = 'dbErrors.txt';

    // if (!$handle = fopen($filename, 'a+')) {
    //     print "Cannot open file ($filename)";
    //     exit;
    // }

    // // Write $somecontent to our opened file.
    // if (fwrite($handle, $string) === FALSE) {
    //     print "Cannot write to file ($filename)";
    //     exit;
    // }



    // fclose($handle);
    die($string);
}


include("config/functions.php");
include("config/init.php");

$time = time();


// Check for cookies
if ( !empty($_COOKIE['bcs_username']) && !empty($_COOKIE['bcs_password']) && (empty($_SESSION['logged_in']) && $_SESSION['logged_in'] == FALSE) ) {

	$c_user = htmlspecialchars(strip_tags($_COOKIE['bcs_username']));
	$c_pass = htmlspecialchars(strip_tags($_COOKIE['bcs_password']));

	$query = "SELECT id, pass FROM bcs_users WHERE name = '$c_user'";
	$result = bcs_query($query) or bcs_error("Error retrieving user: ".mysql_error());
	if ( mysql_num_rows($result) > 0 ) {
		$row = mysql_fetch_array($result);
		if ( $row[pass] == $c_pass ) {
			// Auth successful
			//echo "Cookie check OK";
			$_SESSION['logged_in'] = TRUE;
			$_SESSION['user_id'] = $row[id];
		}
		else {
			//echo "Cookie password does not match";
		}
	}
	else {
		//echo "Cookie user not found";
	}
}

if ( !empty($_SESSION['logged_in']) && $_SESSION['logged_in'] == TRUE ) {

	$s_auth = TRUE;

	// Get information about given user
	$query = "SELECT * FROM bcs_users WHERE id = '$_SESSION[user_id]'";
	$result = bcs_query($query) or bcs_error("Error retrieving user: ".mysql_error());

        if ( mysql_num_rows($result) == 0 ) {
            unset($_SESSION['logged_in']);
            unset($_SESSION['user_id']);
            setcookie("bcs_username", "", time() - 3600);
            setcookie("bcs_password", "", time() - 3600);
            header("Location: index.php");
        }

	$userdata = mysql_fetch_array($result);

	$s_admin = $userdata['admin'];
    $s_editor = $userdata['editor'];
    if ( $userdata[pending_editor] ) $s_editor = 1;

    $s_moderator = $userdata['moderator'];
	$s_name = $userdata['name'];


    $sqlextra = "";
    // Update previous session
    $justloggedin = false;
    if ( $userdata[last_session] < $time - $onlinePeriod ) {
        $justloggedin = true;
        $sqlextra = "previous_session = last_session, ";
        $userdata[previous_session] = $userdata[last_session];
    }

    $sqlextra .= "last_page = '".  parseToDB($_SERVER[REQUEST_URI])."', ";

	// Update session
	$sql = "UPDATE bcs_users SET ".$sqlextra."last_session = '$time' WHERE id = '$_SESSION[user_id]'";
	$result = bcs_query($sql) or bcs_error("<b>SQL ERROR</b> in <br>file ".__FILE__." on line ".__LINE__."<br><br><b>".$sql."</b><br><br>".mysql_error());

    // Update locally, as well!
    $userdata["last_session"] = $time;
    $userdata["last_page"] = $_SERVER[REQUEST_URI];

} else {
	// Get information about guest
	$query = "SELECT * FROM bcs_users WHERE id = '0'";
	$result = bcs_query($query) or bcs_error("Error retrieving guest: ".mysql_error());
	$userdata = mysql_fetch_array($result);

	// Make a temporarily guest
	$_SESSION['user_id'] = 0;
	$userdata["name"] = "Guest";
	$s_auth = FALSE;
}

if ( isset($userdata['banned']) && $userdata['banned'] ) {
  bcs_die("You have been banned from this website.", "none");
}

//if ( $plugin != "login" && !$s_admin ) {
//    die("<h1>Fixing some stuff please check back later!</h1>");
//}



if ( isset($plugin) ) {
	$temp_plugin = parseToDB($plugin);
}
else {
	$temp_plugin = "portal";
}

include("plugins/".$temp_plugin."/".$temp_plugin.".php");
include("config/template.php");

mysql_close();

?>
