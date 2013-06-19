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

include("config/functions_game.php");

$action = $_GET['action'];

if ( $action == "clearguest" ) {
	setcookie("guestCharacterId", "", time()-100);
	die('ok');
}

function errmsg($msg) {
	die('{"errmsg":"'.$msg.'"}');
}


if ( $s_admin ) {
    if ( $action === "hashem" ) {
        //$hash =
        // Generate random passwords and hash them

        $sql = "SELECT id, pass FROM bcs_users";
        $result = bcs_query($sql) or bcs_error("<b>SQL ERROR</b> in <br>file " . __FILE__ . " on line " . __LINE__ . "<br><br><b>" . $sql . "</b><br><br>" . mysql_error());
        for ($x = 0; $x < mysql_num_rows($result); $x++) {
            $row = mysql_fetch_array($result);

            $newhash = passwordHash($row["pass"]);

            $sql2 = "UPDATE bcs_users SET pass = '$newhash' WHERE id = '$row[id]'";
            $result2 = bcs_query($sql2) or bcs_error("<b>SQL ERROR</b> in <br>file " . __FILE__ . " on line " . __LINE__ . "<br><br><b>" . $sql2 . "</b><br><br>" . mysql_error());
        }

    }
}

if ( $action == "delchar" ) {

	if ( !$s_auth ) errmsg("Please login first!");

    if ( !isset($_POST['pass']) ) errmsg("No password given!");

    if ( !isset($_POST['id']) ) die('No id given!');

	$id = (int)parseToDB($_POST['id']);

	if ( $userdata["pass"] != passwordHash($_POST['pass']) ) errmsg("The password you entered was incorrect!");

    if ( getRowCount("ib_characters WHERE id = '$id' AND user = '$userdata[id]'") == 0 ) errmsg("No character found!");

	$query = "DELETE FROM ib_characters WHERE id = '$id' AND user = '$userdata[id]'";
	$result = bcs_query($query) or bcs_error("<b>SQL ERROR</b> in <br>file " . __FILE__ . " on line " . __LINE__ . "<br><br><b>" . $query . "</b><br><br>" . mysql_error());

	$query = "DELETE FROM ib_items WHERE owner = '$id'";
	$result = bcs_query($query) or bcs_error("<b>SQL ERROR</b> in <br>file " . __FILE__ . " on line " . __LINE__ . "<br><br><b>" . $query . "</b><br><br>" . mysql_error());

	$query = "UPDATE bcs_users SET characterused = 0 WHERE id = '$userdata[id]'";
	$result = bcs_query($query) or bcs_error("<b>SQL ERROR</b> in <br>file " . __FILE__ . " on line " . __LINE__ . "<br><br><b>" . $query . "</b><br><br>" . mysql_error());

    $dir = "plugins/game/images/characters/$id/";

        // Delete folder
    if (is_dir($dir)) {
        unlink($dir."full.png");
        unlink($dir."big.png");
        rmdir($dir);
    }

	die('{ "id": "'.$id.'"}');
}
else if ( $action == "clothes" ) {

    if ( !isset($_GET['c']) ) die("No cloth params given!");

    $p = explode(" ", $_GET['c']);

    // $character, $skin, $hair, $head, $body, $feet, $big=false

    foreach ($p as $value) {
        $value = intval($value);
    }

    CreateFullCharacterImage($p[0], $p[1], $p[2], $p[3], $p[4], $p[5], $p[6]);

    die();
}
else if ( $action == "makechar" ) {

	if ( $s_auth ) {


        if ( !isset($_POST['name']) ) die('No name given!');

        if ( !isset($_POST['skin']) ) die('No skin given!');

        if ( !isset($_POST['eyes']) ) die('No eyes given!');

        if ( !isset($_POST['hair']) ) die('No hair given!');

		$name = parseToDB($_POST['name']);

        if ( !ctype_alnum($name) ) {
          errmsg('Your character name can only use letters and numbers!');
        }

		if ( strlen($name) > 12 ) errmsg('Character name is too long! (Maximum 12 characters)');
		if ( strlen($name) < 2 ) errmsg('Character name is too short! (Minimum 2 characters)');

        $skin = intval(parseToDB($_POST["skin"]));
        $eyes = intval(parseToDB($_POST["eyes"]));
        $hair = intval(parseToDB($_POST["hair"]));

		if ( getRowCount("ib_characters WHERE name = '$name'") > 0 ) errmsg('Character name already exists!');
		if ( getRowCount("ib_characters WHERE user = '$userdata[id]'") > 9 ) errmsg("You already have too many characters!");

        if ( !is_int($skin)
            ||
            !(($skin >= $skinIdMaleStart && $skin <= $skinIdMaleEnd)
            || ($skin >= $skinIdFemaleStart && $skin <= $skinIdFemaleEnd)) ) {
            errmsg('Skin ID does not exist! ('.$skin.')');
        }

        if ( !is_int($hair)
            ||
            !(($hair >= $hairIdMaleStart && $hair <= $hairIdMaleEnd)
            || ($hair >= $hairIdFemaleStart && $hair <= $hairIdFemaleEnd)) ) {
            errmsg('Hair ID does not exist! ('.$hair.')');
        }

        if ( !is_int($eyes)
            ||
            !(($eyes >= $eyesIdMaleStart && $eyes <= $eyesIdMaleEnd)
            || ($eyes >= $eyesIdFemaleStart && $eyes <= $eyesIdFemaleEnd)) ) {
            errmsg('Eyes ID does not exist! ('.$eyes.')');
        }



		$query = "INSERT INTO ib_characters (name, user, skin, eyes, hair, creationtime) VALUES('$name', '$userdata[id]', $skin, $eyes, $hair, $time)";
		$result = bcs_query($query) or bcs_error("<b>SQL ERROR</b> in <br>file " . __FILE__ . " on line " . __LINE__ . "<br><br><b>" . $query . "</b><br><br>" . mysql_error());

        $id = mysql_insert_id();

        // Add a sword
		// $query = "INSERT INTO ib_items (template, attr1, owner, equipped, slot) VALUES(1, 2, '$id', 1, 0)";
		// $result = bcs_query($query) or bcs_error("<b>SQL ERROR</b> in <br>file " . __FILE__ . " on line " . __LINE__ . "<br><br><b>" . $query . "</b><br><br>" . mysql_error());

		//CreateFullCharacterImage($id, $skin, $hair);
	}
	else {
		// Check if we already have a cookie assigned
				if ( !isset($_COOKIE['guestCharacterId']) ) {
			do {
				$name = getName(4, 8, "", "");
			} while (getRowCount("ib_characters WHERE name = '$name'") > 0);

            // Male or female?
            if ( rand(1,2) == 1 ) {
                $skin = rand($skinIdMaleStart, $skinIdMaleEnd);
                $eyes = rand($eyesIdMaleStart, $eyesIdMaleEnd);
                $hair = rand($hairIdMaleStart, $hairIdMaleEnd);
            }
            else {
                $skin = rand($skinIdFemaleStart, $skinIdFemaleEnd);
                $eyes = rand($eyesIdFemaleStart, $eyesIdFemaleEnd);
                $hair = rand($hairIdFemaleStart, $hairIdFemaleEnd);
            }



			$query = "INSERT INTO ib_characters (name, user, skin, eyes, hair, creationtime) VALUES('$name', 0, $skin, $eyes, $hair, $time)";
			$result = bcs_query($query) or bcs_error("<b>SQL ERROR</b> in <br>file " . __FILE__ . " on line " . __LINE__ . "<br><br><b>" . $query . "</b><br><br>" . mysql_error());

			$id = mysql_insert_id();

			// Set cookie with one month length
			setcookie("guestCharacterId", $id, time()+2419200);

                        // // Add a sword
                        // $query = "INSERT INTO ib_items (template, attr1, owner, equipped, slot) VALUES(1, 2, '$id', 1, 0)";
                        // $result = bcs_query($query) or bcs_error("<b>SQL ERROR</b> in <br>file " . __FILE__ . " on line " . __LINE__ . "<br><br><b>" . $query . "</b><br><br>" . mysql_error());


			//$id = 1;

			//CreateFullCharacterImage($id, rand(0, 2), rand(0, 2));
		}
        else {
            die('{ "id": '.$_COOKIE['guestCharacterId'].', "equipment": ""}');
        }
	}


	die('{ "id": '.$id.', "name":"'.$name.'", "skin": '.$skin.', "eyes": '.$eyes.', "hair": '.$hair.', "equipment": ""}');

}
else if ( $action == "logout" ) {

    if ( !$s_auth ) errmsg("Already logged out!");

    $query = "UPDATE bcs_users SET last_session = last_session - '$onlinePeriod' WHERE id = '$userdata[id]'";
    $result = bcs_query($query) or bcs_error("<b>SQL ERROR</b> in <br>file " . __FILE__ . " on line " . __LINE__ . "<br><br><b>" . $query . "</b><br><br>" . mysql_error());

    unset($_SESSION['logged_in']);
    unset($_SESSION['user_id']);
    setcookie("bcs_username", "", time() - 3600);
    setcookie("bcs_password", "", time() - 3600);

    die("OK");
}
else if ( $action == "book" ) {

    if ( !isset($_GET["book"]) || !is_numeric($_GET["book"]) ) errmsg("No book given!");

    $book = parseToDB($_GET["book"]);

    $sql3 = "SELECT text FROM ib_books WHERE id = '$book'";
    $result3 = bcs_query($sql3) or bcs_error("<b>SQL ERROR</b> in <br>file " . __FILE__ . " on line " . __LINE__ . "<br><br><b>" . $query . "</b><br><br>" . mysql_error());
    $row3 = mysql_fetch_array($result3);

    die(json_encode(array('text' => $row3[text])));
}
else if ( $action == "register" ) {

    if ( $s_auth ) errmsg("Already registered!");

    if ( !isset($_POST['Ux466hj8']) ) die("No username given!");
    if ( !isset($_POST['Ed2h18Ks']) ) die("No pass given!");
    if ( !isset($_POST['s8HO5oYe']) ) die("No email given!");

    if ( !isset($_POST['url']) ) die("No url given!");

    $safe_name = parseToDB($_POST['Ux466hj8']);
    $safe_pass = parseToDB($_POST['Ed2h18Ks']);
    $safe_email = parseToDB($_POST['s8HO5oYe']);

    $honeypot = $_POST['url'];

    if ( !empty($honeypot) ) {
        die("Sorry, I have detected you may be a computer bot! That's not cool.<br><br>If you believe this error to be my fault, please contact my administrator!");
    }

    if (strlen($safe_name) < 4 || strlen($safe_name) > 20) {
        die('Your username must contain atleast 4, and maximum 20 characters.');
    }

    $aValid = array('-', '_', ' ');
    if ( !ctype_alnum(str_replace($aValid, '', $safe_name)) ) {
        die('Your username can only contain letters and numbers! Please try again.');
    }

    $query = "SELECT * FROM bcs_users WHERE name = '$safe_name'";
    $result = bcs_query($query) or bcs_error("<b>SQL ERROR</b> in <br>file " . __FILE__ . " on line " . __LINE__ . "<br><br><b>" . $query . "</b><br><br>" . mysql_error());

    if (mysql_num_rows($result) > 0) {
        die('Sorry, that username is already taken.');
    }

    if (strlen($safe_pass) < 4 || strlen($safe_pass) > 20) {
        die('Your password must contain atleast 4, and maximum 20 characters.');
    }

    if ($safe_pass != $_POST['Ed2h18Ks']) {
        die('Your password contains invalid characters. Please try another password.');
    }
//    if ($safe_pass != $safe_pass_confirm) {
//        bcs_die('The passwords you entered do not match. Please try again.');
//    }
    if (!$safe_email == "" && (!strstr($safe_email, "@") || !strstr($safe_email, ".") )) {
        die('The e-mail you entered is invalid. Please try again.');
    }

    if (strlen($safe_email) < 8 || strlen($safe_email) > 50) {
        die('Your e-mail must contain atleast 8, and maximum 50 characters.');
    }
    $query = "SELECT * FROM bcs_users WHERE email = '$safe_email'";
    $result = bcs_query($query) or bcs_error("<b>SQL ERROR</b> in <br>file " . __FILE__ . " on line " . __LINE__ . "<br><br><b>" . $query . "</b><br><br>" . mysql_error());

    if (mysql_num_rows($result) > 0) {
        die('Sorry, that e-mail is already taken. Did you forget your password?');
    }

    // Last session = now
    $time = time();

    // Create a new ID
    $sql3 = "SELECT id FROM bcs_users ORDER BY id DESC LIMIT 1";
    $result3 = bcs_query($sql3) or bcs_error("<b>SQL ERROR</b> in <br>file " . __FILE__ . " on line " . __LINE__ . "<br><br><b>" . $query . "</b><br><br>" . mysql_error());
    $row3 = mysql_fetch_array($result3);
    $newid = $row3['id'] + 1; // Simulates auto-increment

    $activationkey = mt_rand();

    // Hash the password
    $safe_pass = passwordHash($safe_pass);

    // Insert a row
    $query = "INSERT INTO bcs_users (id, name, email, show_email, pass, reg_date, last_session, previous_session, activationkey)
	VALUES('$newid', '$safe_name', '$safe_email', 0, '$safe_pass', '$time', '$time', '$time', '$activationkey')";
    $result = bcs_query($query) or bcs_error("<b>SQL ERROR</b> in <br>file " . __FILE__ . " on line " . __LINE__ . "<br><br><b>" . $query . "</b><br><br>" . mysql_error());

    $query = "SELECT * FROM bcs_users WHERE name = '$safe_name'";
    $result = bcs_query($query) or bcs_error("<b>SQL ERROR</b> in <br>file " . __FILE__ . " on line " . __LINE__ . "<br><br><b>" . $query . "</b><br><br>" . mysql_error());
    $userdata = mysql_fetch_array($result);

    // Send a mail
        mailto($safe_email, "Welcome to Ironbane!", '

<div id="mailbox">Hi ' . $safe_name . ', thanks for registering!<br><br>To really enjoy our game and use all features, please verify your e-mail by clicking on the following link:<br><br><a href="http://www.ironbane.com/login.php?action=activate&uid='.$newid.'&key='.$activationkey.'">http://www.ironbane.com/login.php?action=activate&uid='.$newid.'&key='.$activationkey.'</a><br><br>This way, we know you are a real player and we can treat you like one!<br><br>Have fun!<br><br>IronBot<br><br>') ? "true" : "false" . "</div>
";



    // Log the player in
    $s_auth = TRUE;
    $_SESSION['logged_in'] = TRUE;
    $_SESSION['user_id'] = $newid;


    $cookietime = 31536000;
    // Set cookies
    setcookie("bcs_username", $safe_name, time()+$cookietime);
    setcookie("bcs_password", $safe_pass, time()+$cookietime);

    die("OK;Registration successful! Please check your e-mail and click the activation link inside so we know you are a real human!");

}
else if ( $action == "login" ) {

    if ( $s_auth ) errmsg("Already logged in!");

    if ( !isset($_POST['user']) ) die("No user given!");
    if ( !isset($_POST['pass']) ) die("No pass given!");

	$user = parseToDB($_POST['user']);
	$pass = parseToDB($_POST['pass']);

	$s_auth = FALSE;

	if ( empty($user) ) {
		die("Please enter a username.");
	}
	if ( empty($pass) ) {
		die("Please enter a password.");
	}

	$safeuser = parseToDB($user);
	$safepass = parseToDB($pass);

	// Get information about given user
	$query = "SELECT * FROM bcs_users WHERE name = '$safeuser'";
	$result = mysql_query($query) or bcs_error("Error retrieving user: ".mysql_error()."");
	$row = mysql_fetch_array($result);

	if ( mysql_num_rows($result) == 1 && passwordHash($safepass) === $row["pass"] ) {

        // Check for the activation key
        if ( !empty($row["activationkey"]) ) {
            if ( $row["activationkey"] === "NEWPASS" ) {
                // Send an e-mail with a new activation key
                $newpass = randomPassword();

                $newpasshashed = passwordHash($newpass);

                mailto($row["email"], "Password reset", '

                <div id="mailbox">Hi ' . $row["name"] . ',<br><br>
                Due to a recent security breach, a new password was generated for your account.<br>From now on, all passwords are saved encrypted in the database and cannot be restored.<br><br>
                Your new password is: '.$newpass.'<br><br>
                Please login with your newly generated password, and then change it on the Preferences page.<br><br>
                <a href="http://www.ironbane.com/login.php">http://www.ironbane.com/login.php</a><br><br>I\'m very sorry for the inconvience this has caused. <br><br>Sincerely,<br>IronBot<br><br>') ? "true" : "false" . "</div>
                ";

                $query = "UPDATE bcs_users SET pass = '$newpasshashed', activationkey = '' WHERE id = '$row[id]'";
                $result = mysql_query($query) or bcs_error("Error retrieving user: ".mysql_error()."");

                die("Due to a recent security breach, a new password was generated for your account. Please check your e-mail for further information.");
            }
        }

		$s_auth = TRUE;
		$_SESSION['logged_in'] = TRUE;
		$_SESSION['user_id'] = $row['id'];


		$cookietime = 31536000;
		// Set cookies
		setcookie("bcs_username", $safeuser, time()+$cookietime);
		setcookie("bcs_password", $safepass, time()+$cookietime);

		die("OK");
	}
	else {
		die("Sorry, you entered a wrong username or password!");
	}

}
else if ( $action == "getchars" ) {
	if ( $s_auth ) {

		// Make a list of our characters
		$query = "SELECT * FROM ib_characters WHERE user = '$userdata[id]'";
		$result = mysql_query($query) or bcs_error("<b>SQL ERROR</b> in <br>file " . __FILE__ . " on line " . __LINE__ . "<br><br><b>" . $query . "</b><br><br>" . mysql_error());

		$chars = '';

		for ($index = 0; $index < mysql_num_rows($result); $index++) {
			$row = mysql_fetch_array($result);


                        // TODO, get the armor we're wearing and put it here as well
                        $itemlist = array();
                        $query2 = "SELECT template FROM ib_items WHERE owner = '$row[id]' AND equipped = 1";
                        $result2 = mysql_query($query2) or bcs_error("<b>SQL ERROR</b> in <br>file " . __FILE__ . " on line " . __LINE__ . "<br><br><b>" . $query . "</b><br><br>" . mysql_error());
                        for ($index2 = 0; $index2 < mysql_num_rows($result2); $index2++) {
                            $row2 = mysql_fetch_array($result2);
                            array_push($itemlist, $row2["template"]);
                        }

			$inv = implode(",", $itemlist);

			$chars .= '
			{
				id: '.$row["id"].',
				name: "'.$row["name"].'",
                                skin: "'.$row["skin"].'",
                                eyes: "'.$row["eyes"].'",
                                hair: "'.$row["hair"].'",
                                equipment: "'.$inv.'"
			}'.($index<(mysql_num_rows($result)-1)?',':'').'
			';


		}

		$chars = 'chars = ['.$chars.'];
				startdata.user = '.$userdata["id"].';
                startdata.name = "'.$userdata["name"].'";
                startdata.pass = "'.$userdata["pass"].'";
                startdata.characterUsed = "'.$userdata["characterused"].'";
				charCount = chars.length;

				if ( charCount > 0 && startdata.characterUsed == 0 ) startdata.characterUsed = chars[0].id;

                                isEditor = '.$userdata["editor"].';

                                hudHandler.MakeNewsPage();
		';
		die($chars);
	}
	else {

		if ( isset($_COOKIE['guestCharacterId']) ) {

			$guestCharacterId = parseToDB($_COOKIE['guestCharacterId']);



			// Make a list of our characters
			$query = "SELECT * FROM ib_characters WHERE id = '$guestCharacterId' and user = 0";
			$result = mysql_query($query) or bcs_error("<b>SQL ERROR</b> in <br>file " . __FILE__ . " on line " . __LINE__ . "<br><br><b>" . $query . "</b><br><br>" . mysql_error());

			if ( mysql_num_rows($result) == 0 ) {

                            // Somethings wrong, unset the cookie
                            setcookie("guestCharacterId", "", time()-100);

                            // die('hudHandler.MessageAlert("Guest character with cookie ID does not exist!");');

            }
            else {

    			$chars = '';


    			$row = mysql_fetch_array($result);

    			$chars .= '
    			{
    				id: '.$row["id"].',
    				name: "'.$row["name"].'"
    			}
    			';



    			$chars = 'chars = ['.$chars.'];
    					startdata.characterUsed = "'.$guestCharacterId.'";
    					charCount = 1;
    			';

    			die($chars);

            }
		}


	}
	die();
}

die("no action given!");

?>
