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

if ( $action == "getmap" ) {

    ini_set("memory_limit","100M");

    set_time_limit(300);

    $cellsize = 768;

    if ( !is_numeric($zone) ) {
        die("no zone given!");
    }

    // 1. get to know the min/max cell values by looping over terrains
    // 2. Calculate the amount of cells horizontally & vertically


    // ...but check if the mapfile exists
    $mapFile = "plugins/game/data/$zone/map.png";

    if ( $s_editor && $rerender ) {

            if ( !is_numeric($waterlevel) ) {
                die("no waterlevel given!");
            }


        $mapSize = 500;

        if ( $size ) $mapSize = $size;


        // $im = create_blank($mapSize, $mapSize);
        $im = imagecreatetruecolor($mapSize, $mapSize);



        $minx = 0;
        $minz = 0;
        $maxx = 0;
        $maxz = 0;

        for ($x=-50; $x < 50; $x++) {
            for ($z=-50; $z < 50; $z++) {
                $dir = "plugins/game/data/$zone/$x/$z/";

                if ( !is_dir($dir) ) {
                    continue;
                }

                if ( $x < $minx ) $minx = $x;
                if ( $z < $minz ) $minz = $z;
                if ( $x > $maxx ) $maxx = $x;
                if ( $z > $maxz ) $maxz = $z;

                // $filename = "plugins/game/data/$zone/$x/$z/terrain.dat";

                // $file = file_get_contents($filename);

                // die($file);
            }

        }

        $cellsx = $maxx - $minx + 1;
        $cellsz = $maxz - $minz + 1;

        $highestXorZCells = max($cellx, $cellsz);

        $cellPixels = ($mapSize/$highestXorZCells);

        $cellSize = 56;

        $mapOffsetX = 0;
        $mapOffsetY = 0;


        if ( $cellsz > $cellsx ) {
            $mapOffsetX = (($cellsz - $cellsx)/2)*$cellPixels;
        }
        else if ( $cellsx > $cellsz ) {
            $mapOffsetY = (($cellsx - $cellsz)/2)*$cellPixels;
        }
        // echo "cellsx: $cellsx, cellsz: $cellsz<br>";

        // echo "minx: $minx, minz: $minz, maxx: $maxx, maxz: $maxz";


        for ($x=0; $x < $cellsx; $x++) {
            for ($z=0; $z < $cellsz; $z++) {

                $offsetx = $x + $minx;
                $offsetz = $z + $minz;



                $filename = "plugins/game/data/$zone/$offsetx/$offsetz/terrain.dat";

                if ( file_exists($filename) ) {
                    $file = file_get_contents($filename);

                    $terrain = explode(";", $file);

            //                 var ar = terrain.split(';');
                    $count = 0;
                    for ($tx=0; $tx < $cellSize; $tx++) {
                        for ($tz=0; $tz < $cellSize; $tz++) {
                            $info = explode(",", $terrain[$count]);

                            $t = $info[0];

                            $height = round(floatval($info[1]));

                            if ( $heightmap ) {
                                $paint_tile = imagecreatefrompng("plugins/game/images/tiles/20.png");


                                imagefilter($paint_tile, IMG_FILTER_BRIGHTNESS, $height*3);
                            }
                            else {
                                if ( $height <= $waterlevel ) {
                                    $paint_tile = imagecreatefrompng("plugins/game/images/tiles/1651.png");
                                }
                                else {
                                    $paint_tile = imagecreatefrompng("plugins/game/images/tiles/$t.png");
                                }

                                imagefilter($paint_tile, IMG_FILTER_BRIGHTNESS, -$height*2);
                            }

                            // imagepng($paint_tile);
                            // die();

                            // bool imagecopyresampled ( resource $dst_image , resource $src_image , int $dst_x , int $dst_y , int $src_x , int $src_y , int $dst_w , int $dst_h , int $src_w , int $src_h )

                            $rx = ($x * $cellPixels) + (($tx / $cellSize)*$cellPixels);
                            $rz = ($z * $cellPixels) + (($tz / $cellSize)*$cellPixels);

                            $rx += $mapOffsetX;
                            $rz += $mapOffsetY;

                            $tileSize = ceil($mapSize / ($highestXorZCells*$cellSize));

                            // die("number: $tileSize");

                            imagecopyresampled ($im, $paint_tile, $rx, $rz, 0, 0,
                                $tileSize, $tileSize,
                                imagesx($paint_tile), imagesy($paint_tile));

                            // imagepng ($terrain, $file);

                            $count++;
                        }
                    }

            // var count = 0;

            // terrainHandler.world[cellX][cellZ]['terrain'] = {};
            // for(var x = offset_x-cellSizeHalf;x<offset_x+cellSizeHalf;x+=worldScale){
            //   if ( !ISDEF(terrainHandler.world[cellX][cellZ]['terrain'][x]) ) terrainHandler.world[cellX][cellZ]['terrain'][x] = {};
            //   for(var z = offset_z-cellSizeHalf;z<offset_z+cellSizeHalf;z+=worldScale){
            //     var info = ar[count].split(',');
            //     terrainHandler.world[cellX][cellZ]['terrain'][x][z] = {
            //       t:parseInt(info[0]),
            //       y:parseFloat(info[1])
            //     };
            //     count++;
            //   }
            // }
                }

                // $file = file_get_contents($filename);
            }
        }




        // header ('Content-Type: image/png');

        // imagesavealpha($im, true);
        // imagealphablending($im, false);


        if ( $heightmap ) {
            $mapFile = "plugins/game/data/$zone/map_height.png";
        }

        if ( $size ) {
            $mapFile = "plugins/game/data/$zone/map_$size.png";
        }


        imagepng($im, $mapFile);



        die("OK");
    }
    else {
        header("Location: ".$mapFile);
    }

}
else if ( $action == "getchunkimage" ) {

    $chunksize = 10;
    $cellsize = 90;


    // Check if there is an image for this chunk
    // If there is one, echo it
    // If not, make a new one and echo it
    if ( !is_numeric($zone) || !is_numeric($x) || !is_numeric($z) ) {
        die("no zone/x/z given!");
    }

    $cellpos = WorldToCellCoordinates($x, $z, $cellsize);
    $chunkpos = WorldToCellCoordinates($x, $z, $chunksize);

    $dir = "plugins/game/data/$zone/$cellpos[x]/$cellpos[z]/";

    if ( !is_dir($dir) ) {
        die("cell does not exist");
    }

    $dir = "plugins/game/data/$zone/$cellpos[x]/$cellpos[z]/$chunkpos[x]/";
    if ( !is_dir($dir) ) {
        mkdir($dir);
    }

    $dir = "plugins/game/data/$zone/$cellpos[x]/$cellpos[z]/$chunkpos[x]/$chunkpos[z]/";
    if ( !is_dir($dir) ) {
        mkdir($dir);
    }

    $file = "plugins/game/data/$zone/$cellpos[x]/$cellpos[z]/$chunkpos[x]/$chunkpos[z]/terrain.png";

    if ( file_exists($file) ) {

		if ( isset($t) && $userdata[editor] && is_numeric($t) ) {

			$terrain = imagecreatefrompng($file);

			// If we have a $t set (and we're an editor), draw it on the map

			$paint_tile = imagecreatefrompng("plugins/world/images/tiles/$t.png");

			$rx = $x % $chunksize;
			$rz = $z % $chunksize;

			// bool imagecopyresampled ( resource $dst_image , resource $src_image , int $dst_x , int $dst_y , int $src_x , int $src_y , int $dst_w , int $dst_h , int $src_w , int $src_h )

			imagecopyresampled ($terrain, $paint_tile, $rx, $rz, 0, 0, imagesx($paint_tile), imagesy($paint_tile), imagesx($paint_tile), imagesy($paint_tile));

			imagepng ($terrain, $file);

			header('Content-Type: image/png');

			imagepng($terrain);
			imagedestroy($terrain);

		}
		else {
			header('Content-Type: image/png');
			header("Location: $file\r\n");
		}

        die();
    }
    else {
        // Make it, and output it!

        $width = $chunksize * 16;

        $terrain = imagecreatetruecolor($width, $width);




        // Repeat a base tile
        $tile = imagecreatefrompng("plugins/world/images/tiles/11.png");
        imagesettile($terrain, $tile);
        imagefilledrectangle($terrain, 0, 0, $width, $width, IMG_COLOR_TILED);




        imagepng ($terrain, $file);

        header('Content-Type: image/png');

        imagepng($terrain);
        imagedestroy($terrain);

        die();
    }

}
else if ( $action == "delchar" ) {

	if ( !$s_auth ) errmsg("Please login first!");

	$id = parseToDB($id);

	if ( $userdata[pass] != $_POST['pass'] ) errmsg("The password you entered was incorrect!");

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

    $p = explode(" ", $c);

    // $character, $skin, $hair, $head, $body, $feet, $big=false

    foreach ($p as $value) {
        $value = intval($value);
    }

    CreateFullCharacterImage($p[0], $p[1], $p[2], $p[3], $p[4], $p[5], $p[6]);

    die();
}
else if ( $action == "makechar" ) {

	if ( $s_auth ) {
		$name = parseToDB($name);



        if ( !ctype_alnum($name) ) {
          errmsg('Your character name can only use letters and numbers!');
        }


		if ( strlen($name) > 12 ) errmsg('Character name is too long! (Maximum 12 characters)');
		if ( strlen($name) < 2 ) errmsg('Character name is too short! (Minimum 2 characters)');

		if ( getRowCount("ib_characters WHERE name = '$name'") > 0 ) errmsg('Character name already exists!');
		if ( getRowCount("ib_characters WHERE user = '$userdata[id]'") > 9 ) errmsg("You already have too many characters!");

		//$id = mysql_insert_id();
		//$id = 1;

        $skin = intval(parseToDB($_POST["skin"]));
        $eyes = intval(parseToDB($_POST["eyes"]));
        $hair = intval(parseToDB($_POST["hair"]));


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
  if ( !is_numeric($book) ) errmsg("No book given!");

    $sql3 = "SELECT text FROM ib_books WHERE id = '$book'";
    $result3 = bcs_query($sql3) or bcs_error("<b>SQL ERROR</b> in <br>file " . __FILE__ . " on line " . __LINE__ . "<br><br><b>" . $query . "</b><br><br>" . mysql_error());
    $row3 = mysql_fetch_array($result3);

    die(json_encode(array('text' => $row3[text])));
}
else if ( $action == "register" ) {

    if ( $s_auth ) errmsg("Already registered!");

    $safe_name = parseToDB($_POST['Ux466hj8']);
    $safe_pass = parseToDB($_POST['Ed2h18Ks']);
    $safe_email = parseToDB($_POST['s8HO5oYe']);

    $honeypot = $_POST['url'];


    if ($read_tac) {
        $tac_ok = 1;
    } else {
        $tac_ok = 0;
    }

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

    // Insert a row
    $query = "INSERT INTO bcs_users (id, name, email, show_email, pass, reg_date, last_session, previous_session, activationkey)
	VALUES('$newid', '$safe_name', '$safe_email', 0, '$safe_pass', '$time', '$time', '$time', '$activationkey')";
    $result = bcs_query($query) or bcs_error("<b>SQL ERROR</b> in <br>file " . __FILE__ . " on line " . __LINE__ . "<br><br><b>" . $query . "</b><br><br>" . mysql_error());

    $query = "SELECT * FROM bcs_users WHERE name = '$safe_name'";
    $result = bcs_query($query) or bcs_error("<b>SQL ERROR</b> in <br>file " . __FILE__ . " on line " . __LINE__ . "<br><br><b>" . $query . "</b><br><br>" . mysql_error());
    $userdata = mysql_fetch_array($result);


    // Send a mail


        mailto($safe_email, "Welcome to Ironbane!", '

<div id="mailbox">Hi ' . $safe_name . ', thanks for registering!<br><br>To really enjoy our game and use all features, please verify your e-mail by clicking on the following link:<br><br><a href="http://www.ironbane.com/login.php?action=activate&uid='.$newid.'&key='.$activationkey.'">http://www.ironbane.com/login.php?action=activate&uid='.$newid.'&key='.$activationkey.'</a><br><br>This way, we know you are a real player and we can treat you like one!<br><br>Here are your account details, it\'s probably handy should you ever forget.<br><br><b>Username: ' . $safe_name . '<br>Password: ' . $safe_pass . '</b><br><br>Have fun!<br><br>The Ironbane Team<br><br>') ? "true" : "false" . "</div>
";
        //mailto($safe_email, "Welcome to Ironbane!", 'Hi ' . $safe_name . ', thanks for registering!<br><br>Here are your account details, I thought it would come in handy should you ever forget.<br><br><b>Username: ' . $safe_name . '<br>Password: ' . $safe_pass . '</b><br><br>I hope you\'ll have a great time!<br>And kick some butt while you\'re at it!<br><br>Sincerely,<br>GameBot<br><br>') ? "true" : "false" . "<br>";



    // Log the player in
    $s_auth = TRUE;
    $_SESSION['logged_in'] = TRUE;
    $_SESSION['user_id'] = $newid;


    $cookietime = 31536000;
    // Set cookies
    setcookie("bcs_username", $safe_name, time()+$cookietime);
    setcookie("bcs_password", $safe_pass, time()+$cookietime);

    die("OK;Registration successful!");

}
else if ( $action == "login" ) {

	    if ( $s_auth ) errmsg("Already logged in!");

		$user = $_POST['user'];
		$pass = $_POST['pass'];
		$remember = $_POST['remember'];

		$s_auth = FALSE;

		$resolution = $_POST['fieldresolution'];

		if ( empty($user) ) {
			die("Please enter a username.");
		}
		if ( empty($pass) ) {
			die("Please enter a password.");
		}


		$safeuser = (strip_tags($user));
		$safepass = (strip_tags($pass));

		// Get information about given user
		$query = "SELECT * FROM bcs_users WHERE name = '$safeuser'";
		$result = mysql_query($query) or bcs_error("Error retrieving user: ".mysql_error()."");
		$row = mysql_fetch_array($result);

//		if ( $row[activationkey] != '' ) {
//			die("You need to activate your account first!<br><br>Please check your e-mail for an activation link.");
//		}

		if ( mysql_num_rows($result) == 1 && $safepass == $row[pass] ) {
			$s_auth = TRUE;
			$_SESSION['logged_in'] = TRUE;
			$_SESSION['user_id'] = $row['id'];

			//if ( $remember ) {
				$cookietime = 31536000;
				// Set cookies
				setcookie("bcs_username", $safeuser, time()+$cookietime);
				setcookie("bcs_password", $safepass, time()+$cookietime);
			//}

			if ( !empty($_POST["redirect"]) ) {
				$redirect = $_POST["redirect"];
			}
			else {
				$redirect = "index.php";
			}

			if ( is_numeric($resolution) ) {
				$query = "UPDATE bcs_users SET info_width = '$resolution' WHERE id = '$row[id]'";
				$result = mysql_query($query) or bcs_error("<b>SQL ERROR</b> in <br>file " . __FILE__ . " on line " . __LINE__ . "<br><br><b>" . $query . "</b><br><br>" . mysql_error());
			}

			die("OK");
			//bcs_die("Hey, ".$safeuser."! You are now logged in.", $redirect);
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
                            array_push($itemlist, $row2[template]);
                        }

			$inv = implode(",", $itemlist);

			$chars .= '
			{
				id: '.$row[id].',
				name: "'.$row[name].'",
                                skin: "'.$row[skin].'",
                                eyes: "'.$row[eyes].'",
                                hair: "'.$row[hair].'",
                                equipment: "'.$inv.'"
			}'.($index<(mysql_num_rows($result)-1)?',':'').'
			';


		}

		$chars = 'chars = ['.$chars.'];
				startdata.user = '.$userdata[id].';
                startdata.name = "'.$userdata[name].'";
                startdata.pass = "'.$userdata[pass].'";
                startdata.characterUsed = "'.$userdata[characterused].'";
				charCount = chars.length;

				if ( charCount > 0 && startdata.characterUsed == 0 ) startdata.characterUsed = chars[0].id;

                                isEditor = '.$userdata[editor].';

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
    				id: '.$row[id].',
    				name: "'.$row[name].'"
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
