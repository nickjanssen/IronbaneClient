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
	die();
}


$validation_ok = 1;

if ( !isset($redirect) ) {
	$redirect = "portal";
}

//if ( $action == "resend" ) {
//
//    $query = "SELECT * FROM bcs_users WHERE name = '$uid' ";
//    $result = mysql_query($query) or bcs_error("Error on #" . __LINE__ . ": ".mysql_error());
//
//    if ( mysql_num_rows($result) == 0 ) {
//        bcs_die("That user was not found! Please contact Nikke (nikke@ironbane.com) or create a new account.");
//    }
//
//    mailto($safe_email, "Welcome to Ironbane!", 'Hi ' . $safe_name . ', thanks for registering!<br><br>Before you can play, you must activate your account by clicking on the following link: <a href="http://www.ironbane.com/login.php?action=activate&uid='.$newid.'&key='.$activationkey.'">http://www.ironbane.com/login.php?action=activate&uid='.$newid.'&key='.$activationkey.'</a><br><br>Here are your account details, I thought it would come in handy should you ever forget.<br><br><b>Username: ' . $safe_name . '<br>Password: ' . $safe_pass . '</b><br><br>I hope you\'ll have a great time!<br>And kick some butt while you\'re at it!<br><br>Sincerely,<br>GameBot<br><br>') ? "true" : "false" . "<br>";
//
//
//
//}
if ( $action == "activate" ) {
    //if ( $s_auth ) bcs_die("You have already activated.");

    $uid = (int)parseToDB($uid);

    $query = "SELECT * FROM bcs_users WHERE id = '$uid' ";
    $result = mysql_query($query) or bcs_error("Error on #" . __LINE__ . ": ".mysql_error());

    if ( mysql_num_rows($result) == 0 ) {
        bcs_die("That user was not found! Please contact Nikke (nikke@ironbane.com) or create a new account.");
    }

    $row = mysql_fetch_array($result);

    if ( $row["activationkey"] != $_GET["key"] ) {
        bcs_die("Your activation key is not valid. Please contact Nikke (nikke@ironbane.com) or create a new account.");
    }

    $query = "UPDATE bcs_users SET activationkey = '' WHERE id = '$uid' ";
    $result = mysql_query($query) or bcs_error("Error on #" . __LINE__ . ": ".mysql_error());


    // Log the player in
    $s_auth = TRUE;
    $_SESSION['logged_in'] = TRUE;
    $_SESSION['user_id'] = $row["id"];


    $cookietime = 31536000;
    // Set cookies
    setcookie("bcs_username", $row["name"], time()+$cookietime);
    setcookie("bcs_password", $row["pass"], time()+$cookietime);

    bcs_die("Thanks, $row[name]!<br><br>Your account is now active!", "game.php");

    //header("Location: game.php?activated=1");
}
else if ( $action == "forgotpassword" ) {

        $c_title = "Forgot password";

        if ( $_POST["dosend"] ) {



				if ( $_POST["verification"] != $_SESSION["vercode"] || empty($_SESSION["vercode"]) )  {
                                        $_SESSION["vercode"] = "";
					 bcs_die("Sorry, the image code you entered was incorrect. Please try again.", "index.php?plugin=login&action=forgotpassword");
				}

                $_SESSION["vercode"] = "";

                $email = parseToDB($_POST["email"]);

                $query = "SELECT * FROM bcs_users WHERE email = '$email' ";
                $result = mysql_query($query) or bcs_error("Error on #" . __LINE__ . ": ".mysql_error());

                if ( mysql_num_rows($result) == 0 ) {
                        bcs_die("Your e-mail address was not found. Please try again.", "index.php?plugin=login&action=forgotpassword");
                }

                $userdata = mysql_fetch_array($result);



                $bericht = "
                Hey ".$userdata["name"]."!<br><br>

                Here is the password of your Ironbane account that you requested on ".createDate(time(), $userdata["gmt"]).".<br>
                If you think this message was sent by abuse, please send an e-mail to nikke@ironbane.com with as much details as possible.<br><br>

                Password: ".$userdata["pass"]."<br><br>

                Best of luck!<br>
                GameBot
                ";

                mailto($userdata["email"], "Password request", $bericht);


                bcs_die("Your password has been sent to your e-mail address. This usually happens instantly, but it may take several minutes.<br /><br /><b>Attention:</b> Be sure to check your spam folder as well.", $filename);
        }
        else {
                $c_main = "


                If you have forgotten your password, we can retrieve it by sending it to your e-mail address.<br /><br />


				<form action=\"index.php?plugin=login&action=forgotpassword\" method=POST>
				<table width=\"100%\" border=\"0\">
				<tr>
				<td align=\"left\"><span class=\"gen\">Your e-mail address:</span></td>
				<td align=\"left\"><span class=\"gen\"><input type=text name=email size=25 maxlength=100></span></td>
				</tr>
				<tr>
				<td align=\"left\"><span class=\"gen\">Please enter the number shown in the image:</span><br /><span class=\"gensmall\">".$ts." This image verification is used to prevent spam-bots.</span></td>
				<td align=\"left\"><span class=\"gen\"><table width=200 border=0><tr><td align=left width=50%><input type=text name=verification size=10 maxlength=10></td><td align=left width=50%><img src=\"captcha.php\"></td></tr></table></span></td>
				</tr>
				<tr>
				<td></td>
				<td align=\"left\"><input type=submit name=dosend value=\"Submit password request\"></td>
				</tr>
				</table>
				</form>
				";

	$c_main = '

<div align="center"><h1>Forgot password</h1></div>
	  <table width="600" cellpadding="5" cellspacing="0" border="0" class="forumline" align="center">
	   <tr>
		<td class="row1"><span class="gen">
'.$c_main.'
                </span></td>
	   </tr>
	  </table>



        ';
        }
}
else if ( $s_check ) {

		$user = $_POST['user'];
		$pass = $_POST['pass'];
		$remember = $_POST['remember'];

		$s_auth = FALSE;

                $resolution = $_POST['fieldresolution'];

		if ( empty($user) ) {
			bcs_die("Please enter a username.");
		}
		if ( empty($pass) ) {
			bcs_die("Please enter a password.");
		}



		$safeuser = (parseToDB($user));
		$safepass = (parseToDB($pass));

		// Get information about given user
		$query = "SELECT * FROM bcs_users WHERE name = '$safeuser'";
		$result = mysql_query($query) or bcs_error("Error retrieving user: ".mysql_error()."");
		$row = mysql_fetch_array($result);

                if ( $row["activationkey"] != '' ) {
                    bcs_die("You need to activate your account first!<br><br>Please check your e-mail for an activation link.");
                }

		if ( mysql_num_rows($result) == 1 && $safepass == $row["pass"] ) {
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

                        //writeChatMessage("<i>".  memberLink($_SESSION['user_id'])." logged in.</i>");

			header("Location: forum.php");
			//bcs_die("Hey, ".$safeuser."! You are now logged in.", $redirect);
		}
		else {
			bcs_die("Sorry, you entered a wrong username or password! Please try again.", "index.php?plugin=login");
		}
}
else {
	// Output login screen

	// Check to add the register button


	$c_title = "Log in";

	$c_main = '



<form action="index.php?plugin=login" method="post">

<div align="center">
<span class="genbig">Simply use your game account to log-in to the forums.<div style="height:20px"></div></span>
<input type="hidden" name="redirect" value="'.$_SESSION["nm_redirect"].'" />

<script type="text/javascript">
document.write(\'<input type="hidden" name="fieldresolution" value="\'+screen.width+\'" />\');
</script>

<input type="text" name="user" id="user" maxlength="20" style="width:300px" class="regfield">
<div style="height:20px"></div>
<input type="password" name="pass" id="pass" maxlength="20" style="width:300px;display:none" class="regfield">
<input type="text" name="fakepass" id="fakepass" maxlength="20" style="width:300px" class="regfield">

<div style="height:20px"></div>
<input type="submit" class="mainoption" name="s_check" value="Log in" />
<div style="height:20px"></div>
<span class="genbig">
<a href=index.php?plugin=login&action=forgotpassword>Forgot your password?</a>
<br>No account yet? Create one from <a href="game.php">within the game</a>.</span>
</div>
</form>
';

        $c_jquery .= '

var username = "Your username";
var password = "Your password";

$(document).ready(function(){

     var current_date = new Date( );
     var gmt_offset = current_date.getTimezoneOffset( ) / 60;
     $("#gmt").val(-gmt_offset);



    $("#user").attr("value", username);

    $("#user").focus(function(){
        if ( $("#user").attr("value") == username ) {
            $("#user").attr("value", "");
        }
    });
    $("#user").blur(function(){
        if ( $("#user").attr("value") == "" ) {
            $("#user").attr("value", username);
        }
    });

    $("#fakepass").attr("value", password);

    $("#fakepass").focus(function(){
        $("#fakepass").hide();
        $("#pass").show();
        $("#pass").focus();
    });

    $("#pass").blur(function(){
        if ( $("#pass").attr("value") == "" ) {
            $("#fakepass").show();
            $("#pass").hide();
        }
    });

});
';

	$c_main = '

<div align="center"><h1>Log in</h1></div>

'.$c_main.'




        ';
}

?>
