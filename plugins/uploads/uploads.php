<?php


if ( !defined('BCS') ) {
	die("ERROR");
}

//$special_message = "The new Wiki is still under construction and needs cleanup from the converting of the old articles. Stay tuned!";

if ( isset($n) ) {

    $n = parseToDB($n);

    $query = "SELECT id FROM bcs_help_articles WHERE title = '$n'";
    $result = mysql_query($query) or bcs_error("<b>SQL ERROR</b> in <br>file " . __FILE__ . " on line " . __LINE__ . "<br><br><b>" . $query . "</b><br><br>" . mysql_error());
    $row = mysql_fetch_array($result);

    $id = $row[id];
}

if ( !isset($id) ) {
	$id = 6;
}

if ( !isset($show) ) $show = "general";

class UploadFolder {
  public $title = "";
  public $dir = "";
  public function __construct($title, $dir) {
      $this->title = $title;
      $this->dir = $dir;
  }
}

$notUsingNumberFolders = array("general", "media", "hud", "misc", "particles", "projectiles", "scripts");


$uploadFolders = array();
$uploadFolders["general"] = new UploadFolder("General", "uploads/teamwiki/");
$uploadFolders["media"] = new UploadFolder("Media", "uploads/media/");
$uploadFolders["hud"] = new UploadFolder("In-game: HUD", "plugins/game/images/hud/");
$uploadFolders["misc"] = new UploadFolder("In-game: Miscellaneous", "plugins/game/images/misc/");
$uploadFolders["textures"] = new UploadFolder("In-game: Model textures", "plugins/game/images/textures/");
$uploadFolders["skin"] = new UploadFolder("In-game: Skin templates", "plugins/game/images/characters/base/skin/");
$uploadFolders["eyes"] = new UploadFolder("In-game: Eye templates", "plugins/game/images/characters/base/eyes/");
$uploadFolders["hair"] = new UploadFolder("In-game: Hair templates", "plugins/game/images/characters/base/hair/");
$uploadFolders["head"] = new UploadFolder("In-game: Head Equipment", "plugins/game/images/characters/base/head/");
$uploadFolders["body"] = new UploadFolder("In-game: Body Equipment", "plugins/game/images/characters/base/body/");
$uploadFolders["feet"] = new UploadFolder("In-game: Feet Equipment", "plugins/game/images/characters/base/feet/");
$uploadFolders["items"] = new UploadFolder("In-game: Item sprites", "plugins/game/images/items/");
$uploadFolders["tiles"] = new UploadFolder("In-game: Terrain Tiles", "plugins/game/images/tiles/");
$uploadFolders["particles"] = new UploadFolder("In-game: Particles", "plugins/game/images/particles/");
$uploadFolders["projectiles"] = new UploadFolder("In-game: Projectiles", "plugins/game/images/projectiles/");
$uploadFolders["scripts"] = new UploadFolder("In-game: Scripts", "plugins/game/images/scripts/");



$id = parseToDB($id);

$c_title = "Team Wiki";




        $c_head .= '
                <link href="config/editor.css" rel="Stylesheet" type="text/css" />
	<script src="config/editor.js" type="text/javascript"></script>
<style type="text/css">
#toc
{
	border: 1px dashed #257541;
	background-color: #083420;
	padding: 10px;
	text-align: center;
	width:300px;

}

#toc-header
{
	display: inline;
	padding: 0;
	font-weight: bold;
}

#toc ul
{
	list-style-type: none;
	margin-left: 0;
	padding-left: 0;
	text-align: left;
}

.toc3
{
	margin-left: 2em;
}

.toc4
{
	margin-left: 4em;
}
</style>

	';

if ( !$s_editor ) bcs_die("No access.");


if ( $action == "viewchange" ) {
    $id = parseToDB($id);
    $query = "SELECT * FROM rpg_team_actions WHERE id = '$id'";
    $result = mysql_query($query) or bcs_error("<b>SQL ERROR</b> in <br>file " . __FILE__ . " on line " . __LINE__ . "<br><br><b>" . $query . "</b><br><br>" . mysql_error());

    $row = mysql_fetch_array($result);


    $c_main = '<h1>'.$row[action].' by '.memberlink($row[user]).' on '.  createdate($row[time]).' ('.  timeAgo($row[time]).' ago)</h1>'.post_parse($row[previous_data]).'';

}
else if ( $action == "viewchanges" ) {
    $query = "SELECT * FROM rpg_team_actions ORDER BY time DESC LIMIT 500";
    $result = mysql_query($query) or bcs_error("<b>SQL ERROR</b> in <br>file " . __FILE__ . " on line " . __LINE__ . "<br><br><b>" . $query . "</b><br><br>" . mysql_error());

    $c_main = "<h1>Team changes</h1>";

    for($count = 0; $count < mysql_num_rows($result); $count++) {
        $row = mysql_fetch_array($result);


        $c_main .= ' '.$row[action].' by '.memberlink($row[user]).' on '.  createdate($row[time]).' ('.  timeAgo($row[time]).' ago) - <a href="uploads.php?action=viewchange&id='.$row[id].'">View</a><br>';


    }

}
else if ( $action == "deletefile" ) {


  $dir = $uploadFolders[$show]->dir;


    if ( !in_array($show, $notUsingNumberFolders) ) {
        $temp = $id.".png";

        if ( !file_exists($dir.$temp) ) {
          $temp = $id.".PNG";
        }

        $id = $temp;
    }

    if ( !file_exists($dir.$id) ) {
        bcs_die("File not found: ".$dir.$id, "back");
    }

    unlink($dir.$id);

    //die("file: ".$id);

    header("Location: uploads.php?action=viewuploads&show=".$show."");

    die();

}
else if ( $action == "viewuploads" ) {
    $c_main .= '<h1>Uploaded files [<a href=uploads.php?action=upload&gotype='.$show.'>Upload a file</a>]</h1> ';




    $fs = 10;



    $c_main .= 'Category: <select id="gotype">';

    foreach ($uploadFolders as $key => $value) {
      $c_main .= '<option value="'.$key.'">'.$value->title.'</option>';
    }


  $c_main .= '</select><br>';



    $c_jquery .= '

$(function() {
    $("#gotype").val("'.$show.'");
});

$("#gotype").change(function(){
    var val = $("#gotype").val();
    location.href="uploads.php?action=viewuploads&show="+val;
});

';

    $c_main .= '<h2>'.$title.'</h2>';


            if ( !in_array($show, $notUsingNumberFolders) ) {
    $c_main .= '<h3>Click on an item to delete it.</h3><br>';
    }

    $dir = $uploadFolders[$show]->dir;

    $count = 0;

    $files = scandir($dir);
    foreach($files as $key => $value){
        if ( $value == "." || $value == ".."  ) continue;



                $count++;

                $file = explode(".", $value);

                if ( strtolower($file[1]) != "png" && $show != 0 && $show != 1 ) continue;

        if ( !in_array($show, $notUsingNumberFolders) ) {

//            $c_main .= '<a href="'.$dir.''.$value.'">'.$file[0].'</a><br>';
//            $c_main .= '<img src="'.$dir.''.$value.'"><br>';


            $imgf = "<img alt=".$file[0]." src=".$dir.$value.">";

            if  ( $show == "tiles" ) $imgf = "<img alt=".$file[0]." src=".$dir."medium.php?i=".$file[0].">";


            $c_main .= "<div style=float:left id=asset".$count."><span style=\"position:absolute;color:white;font-size:".$fs."px;\">" . $file[0] . "</span>".$imgf."</div>";



        }
        else {
            $c_main .= 'http://www.ironbane.com/'.$dir.''.$value.' [<a href="'.$dir.''.$value.'" target="_blank">Open</a>] [<a href="#" id="asset'.$count.'">Delete</a>]<br>';
        }
            $c_jquery .= '

$("#asset'.$count.'").click(function() {

    if ( prompt("Are you sure? The file will be permanently deleted!\nType DELETE to confirm.") == "DELETE" ) {
        window.location = "uploads.php?action=deletefile&id='.((!in_array($show, $notUsingNumberFolders))?$file[0]:$file[0].".".$file[1]).'&show='.$show.'";
    }

});


';
    }


}
else if ( $action == "upload" ) {

    $c_main .= '<h1>Upload file [<a href=uploads.php?action=viewuploads&show='.$gotype.'>View uploads</a>]</h1> ';

    if ( $userdata[pending_editor] ) {
        bcs_die("Sorry, you still need to be approved before you can upload files.");
    }

    if ( $submit ) {
        $target_path = "uploads/teamwiki/";


        //die(basename( $_FILES['uploadedfile']['name']));

        $fileArray = explode(".", $_FILES['uploadedfile']['name']);

        $fileArray[1] = strtolower($fileArray[1]);

        if ( count($fileArray) > 2 ) bcs_die("Your filename contains too many dots", "back");

        if ( !in_array($gotype, $notUsingNumberFolders) && strtolower($fileArray[1]) != "png" ) bcs_die("You can only upload PNG files for gameobjects!", "back");


        $target_path = $uploadFolders[$gotype]->dir;


        $filename = implode(".", $fileArray);



        $target_path = $target_path . $filename;

        if ( file_exists($target_path) ) {
            bcs_die("Your filename already exists on the server!", "back");
        }


        if(move_uploaded_file($_FILES['uploadedfile']['tmp_name'], $target_path)) {
//            bcs_die( "The file ".  basename( $_FILES['uploadedfile']['name']).
//            " has been uploaded");
            header("Location: uploads.php?action=viewuploads&show=".$gotype);
        } else{
            bcs_die( "There was an error uploading the file!");
        }
    }
    else {

    $c_jquery .= '

$(function() {
    $("#gotype").val("'.$gotype.'");
});

$("#gotype").change(function(){
    var val = $("#gotype").val();
    location.href="uploads.php?action=viewuploads&show="+val;
});

';

        $c_main .= '
<form enctype="multipart/form-data" action="uploads.php?action=upload" method="POST">
Category: <select name="gotype" id="gotype">';

    foreach ($uploadFolders as $key => $value) {
      $c_main .= '<option value="'.$key.'">'.$value->title.'</option>';
    }


$c_main .= '</select>
Choose a file to upload: <input name="uploadedfile" type="file" /><br />

<h2>Note: For in-game material, only use the PNG format for all images. Be sure it does not exist yet!</h2>
<input type="submit" name="submit" value="Upload File" />
</form>
';
    }
}
else if ( $action == "delete" ) {

    if ( $userdata[pending_editor] ) {
        bcs_die("Sorry, you still need to be approved before you can edit pages.");
    }

    if ( $confirm ) {
            $query = "SELECT * FROM bcs_help_articles WHERE id = '$id'";
            $result = mysql_query($query) or bcs_error("<b>SQL ERROR</b> in <br>file " . __FILE__ . " on line " . __LINE__ . "<br><br><b>" . $query . "</b><br><br>" . mysql_error());
            $row = mysql_fetch_array($result);
        AddTeamActionSelf("TeamWiki delete page '$row[title]'", "teamwiki{$id}", $row[content]);
	$query = "DELETE FROM bcs_help_articles WHERE id = '$id'";
	$result = mysql_query($query) or bcs_error("<b>SQL ERROR</b> in <br>file " . __FILE__ . " on line " . __LINE__ . "<br><br><b>" . $query . "</b><br><br>" . mysql_error());
        header("Location: uploads.php");
    }
    else {
        $c_main = "<h1>Are you sure you want to delete this article?<br><br><a href=\"uploads.php?action=delete&id=".$id."&confirm=1\">Yeah, delete it!</a></h1>";
    }
}
else if ( $action == "edit" || $action == "add" ) {

    if ( $userdata[pending_editor] ) {
        bcs_die("Sorry, you still need to be approved before you can edit pages.");
    }

        if ( $action == "edit" ) {
            $query = "SELECT * FROM bcs_help_articles WHERE id = '$id'";
            $result = mysql_query($query) or bcs_error("<b>SQL ERROR</b> in <br>file " . __FILE__ . " on line " . __LINE__ . "<br><br><b>" . $query . "</b><br><br>" . mysql_error());
            $row = mysql_fetch_array($result);
        }

	if ( $row[author] != $userdata[id] && !$s_editor ) {
		die();
	}
	if ( $submit ) {
            if ( $action == "edit" ) {

                AddTeamActionSelf("TeamWiki edit page '$title'", "teamwiki{$id}", $content);

		$title = parseToDB($title);
                //$content = $gocontent;
                $content = mysql_real_escape_string($content);
		//$content = parseToDB($content, true);
                //die($content);



		$query = "UPDATE bcs_help_articles SET title = '$title', priority = '$priority', author = '$userdata[id]', keywords = '$keywords', content = '$content', lastupdated = '".time()."' WHERE id = '$id'";
		$result = mysql_query($query) or bcs_error("<b>SQL ERROR</b> in <br>file " . __FILE__ . " on line " . __LINE__ . "<br><br><b>" . $query . "</b><br><br>" . mysql_error());
		bcs_die('Your article was edited succesfully.', 'index.php?plugin=uploads&amp;id='.$id);
            }
            else if ( $action == "add" ) {

                AddTeamActionSelf("TeamWiki add page '$title'", "teamwiki{$id}", $content);

                $title = parseToDB($title);
		$content = parseToDB($content);
		$keywords = parseToDB($keywords);



		$query = "INSERT INTO bcs_help_articles (title, content, keywords, author, lastupdated, priority) VALUES('$title', '$content', '$keywords', '$userdata[id]', '".time()."', '$priority')";
		$result = mysql_query($query) or bcs_error("<b>SQL ERROR</b> in <br>file " . __FILE__ . " on line " . __LINE__ . "<br><br><b>" . $query . "</b><br><br>" . mysql_error());
		bcs_die('Your article was added succesfully.', 'index.php?plugin=uploads');
            }
	}
	else {
		$c_main = '


            <h1>'.($action == "add"?'Add page':'Edit '.$row[title].'').'</h1>


<form action="index.php?plugin=uploads&amp;action='.($action == "add"?'add':'edit&id='.$id.'').'&submit=1" method="POST" onsubmit="doCheck();">

<div style="width:100px">Title</div> <input type="text" name="title" value="'.$row[title].'" style="width:700px;height:30px;font-size:25px"><br>
<div style="width:700px">Priority (any number between 1 and 100 to indicate the importance, default is 50)</div> <input type="text" maxsize=3 name="priority" value="'.($action == "add"?50:$row[priority]).'" style="width:100px;height:30px;font-size:25px"><div style="width:100px">Keywords</div><input type="text" name="keywords" value="'.$row[keywords].'" style="width:700px;height:30px;font-size:25px"><br><br>
<div class="richeditor">
	<div class="editbar">
		<button title="bold" onclick="doClick(\'bold\');" type="button"><b>B</b></button>
		<button title="italic" onclick="doClick(\'italic\');" type="button"><i>I</i></button>
		<button title="underline" onclick="doClick(\'underline\');" type="button"><u>U</u></button>
		<button title="strikeThrough" onclick="doClick(\'strikeThrough\');" type="button"><s>S</s></button>
		<button title="h1" onclick="doClick(\'heading1\');" type="button"><b>h1</b></button>
		<button title="h2" onclick="doClick(\'heading2\');" type="button">h2</button>
		<button title="h3" onclick="doClick(\'heading3\');" type="button"><i>h3</i></button>
		<button title="hyperlink" onclick="doLink();" type="button" style="background-image:url(\'config/images/url.gif\');"></button>
		<button title="image" onclick="doImage();" type="button" style="background-image:url(\'config/images/img.gif\');"></button>
		<button title="list" onclick="doClick(\'InsertUnorderedList\');" type="button" style="background-image:url(\'config/images/icon_list.gif\');"></button>
		<button title="color" onclick="showColorGrid2(\'none\')" type="button" style="background-image:url(\'config/images/colors.gif\');"></button><span id="colorpicker201" class="colorpicker201"></span>
		<button title="quote" onclick="doQuote();" type="button" style="background-image:url(\'config/images/icon_quote.png\');"></button>
		<button title="switch to source" type="button" onclick="javascript:SwitchEditor()" style="background-image:url(\'config/images/icon_html.gif\');"></button>
	</div>
	<div class="container">
            <textarea style="width:100%;height:500px" name="content" id="contentfield">'.$row[content].'</textarea>

	</div>
</div><br><br>
<div style="text-align:center;width:100%"><input type="submit" value="'.($action == "add"?'Add page':'Edit this page').'" name="submit" onclick="doCheck();" class="hugeoption"></div>
                                    	<script type="text/javascript">
                                            initEditor("contentfield", true);
                                        </script>
</form>

';



//                $c_jquery .= '
//
//$("#dosubmit").click(function(){
//
//    nicEditors.findEditor("gocontent").saveContent();
//
//    $("#testje").submit();
//
//
//});
//
//';

	}

}
elseif ( $action == "add" ) {

	// ???
	/*if ( $row[author] != $userdata[id] && !$s_admin ) {
		die();
	}*/

	if ( $submit ) {
		$title = parseToDB($title);
		$content = parseToDB($content);
		$keywords = parseToDB($keywords);
		$query = "INSERT INTO bcs_help_articles (title, content, keywords, author, lastupdated) VALUES('$title', '$content', '$keywords', '$userdata[id]', '".time()."')";
		$result = mysql_query($query) or bcs_error("<b>SQL ERROR</b> in <br>file " . __FILE__ . " on line " . __LINE__ . "<br><br><b>" . $query . "</b><br><br>" . mysql_error());
		bcs_die('Your article was added succesfully.', 'index.php?plugin=uploads');
	}
	else {

	}


}
else {
// Create a sidebar

// A list of help articles which are displayed on the sidebar, separated by a comma


$query = "SELECT * FROM bcs_help_articles ORDER BY priority DESC";
$result = mysql_query($query) or bcs_error("Error retrieving news: ".mysql_error());

for($count = 0; $count < mysql_num_rows($result); $count++) {
	$row = mysql_fetch_array($result);
	$sidebar_ids .= $row[id];

	if ( $count < mysql_num_rows($result)-1 ) {
		$sidebar_ids .= ",";
	}
}


//$sidebar_ids = "10,1,3,2,5";

$sidebar_explo = explode(",", $sidebar_ids);

$c_sidebar = "<h3>Navigation</h3><div style=width:100%>";

for($x=0;$x<count($sidebar_explo);$x++){

	$query = "SELECT title, priority FROM bcs_help_articles WHERE id = '$sidebar_explo[$x]'";
	$result = mysql_query($query) or bcs_error("<b>SQL ERROR</b> in <br>file " . __FILE__ . " on line " . __LINE__ . "<br><br><b>" . $query . "</b><br><br>" . mysql_error());
	$row = mysql_fetch_array($result);
        $size = 9+(($row[priority]/100)*20);
	$c_sidebar .= "<div style=\"float:left;padding-left:10px;padding-right:10px;height:35px;font-size:{$size}px\">";


	if ( $id == $sidebar_explo[$x] ) {
		$c_sidebar .= "<b>".$row[title]."</b>";
	}
	else {
		$c_sidebar .= "".createlink($row[title], "index.php?plugin=uploads&amp;id=".$sidebar_explo[$x])."";
	}

	$c_sidebar .= "</div>";
}

$c_sidebar .= "<div style=clear:both></div></div><hr>";


$query = "SELECT * FROM bcs_help_articles WHERE id = '$id'";
$result = mysql_query($query) or bcs_error("<b>SQL ERROR</b> in <br>file " . __FILE__ . " on line " . __LINE__ . "<br><br><b>" . $query . "</b><br><br>" . mysql_error());
$row = mysql_fetch_array($result);


if ( $row[author] == $userdata[id] || $s_editor ) {
	$link = " [ ".createlink("Edit page","index.php?plugin=uploads&amp;action=edit&id=".$id)." ]";
        $link .= " [ ".createlink("Delete page","index.php?plugin=uploads&amp;action=delete&id=".$id)." ]";
}
if ( $s_editor ) {
	$link2 = "[ ".createlink("Add page","index.php?plugin=uploads&amp;action=add")." ]&nbsp;";
}



if ( $id == 10 ) {

	// Define title
	$title = $language[8];

	// Get the news
	$query = "SELECT * FROM forum_topics WHERE board_id = 7 ORDER BY time DESC";
	$result = mysql_query($query) or bcs_error("Error retrieving news: ".mysql_error());

	for($count = 0; $count < mysql_num_rows($result); $count++) {
		$row5 = mysql_fetch_array($result);

		// Count number of comments on this newspost
		$query4 = "SELECT * FROM forum_posts WHERE topic_id='$row5[id]' ORDER BY time ASC LIMIT 1";
		$result4 = mysql_query($query4) or bcs_error("<b>SQL ERROR</b> in <br>file " . __FILE__ . " on line " . __LINE__ . "<br><br><b>" . $query . "</b><br><br>" . mysql_error());
		$row4 = mysql_fetch_array($result4);

		$query2 = "SELECT * FROM bcs_users WHERE id='$row4[user]'";
		$result2 = mysql_query($query2) or bcs_error("<b>SQL ERROR</b> in <br>file " . __FILE__ . " on line " . __LINE__ . "<br><br><b>" . $query . "</b><br><br>" . mysql_error());
		$row2 = mysql_fetch_array($result2);

		$c_title = $language[8];

		// Count number of comments on this newspost
		$query3 = "SELECT * FROM forum_posts WHERE topic_id='$row5[id]'";
		$result3 = mysql_query($query3) or bcs_error("<b>SQL ERROR</b> in <br>file " . __FILE__ . " on line " . __LINE__ . "<br><br><b>" . $query . "</b><br><br>" . mysql_error());
		$row3 = mysql_fetch_array($result3);

		$commentcount = mysql_num_rows($result3) - 1;

//<td width=\"32\"><img src=\"themes/".$c_theme."/images/bg_left.gif\"></td>




	}


	$h_main = $n_main;
}
else {
	$h_main = "<br /><br />".post_parse(help_parse($row[content], $id));

}

$content = ($row[content]);

function reptest2($text) {
  $special = preg_replace('/[^a-zA-Z0-9_ %\[\]\.\(\)%&-]/s', '', $text);
  $special = preg_replace('/\s+/', '', $special);

  return '<h2><a name="'.$special.'"></a>'.$text.'</h2>';
}
function reptest3($text) {
  $special = preg_replace('/[^a-zA-Z0-9_ %\[\]\.\(\)%&-]/s', '', $text);
  $special = preg_replace('/\s+/', '', $special);

  return '<h3><a name="'.$special.'"></a>'.$text.'</h3>';
}
function reptest4($text) {
  $special = preg_replace('/[^a-zA-Z0-9_ %\[\]\.\(\)%&-]/s', '', $text);
  $special = preg_replace('/\s+/', '', $special);

  return '<h4><a name="'.$special.'"></a>'.$text.'</h4>';
}

$content = post_parse($content);

$content = preg_replace("/<h2>(.*?)<\/h2>/ie",'reptest2("$1")', $content);
$content = preg_replace("/<h3>(.*?)<\/h3>/ie",'reptest3("$1")', $content);
$content = preg_replace("/<h4>(.*?)<\/h4>/ie",'reptest4("$1")', $content);


$content = stripslashes($content);
//$content = help_parse($content, $id);

$depth = 5;
//get the headings down to the specified depth
$pattern = '/<h[2-'.$depth.']*[^>]*>.*?<\/h[2-'.$depth.']>/';
$whocares = preg_match_all($pattern,$content,$winners);


//reformat the results to be more usable
$heads = implode("\n",$winners[0]);
//$heads = strip_tags($heads);
$heads = str_replace('<a name="','<a href="#',$heads);
$heads = str_replace('</a>','',$heads);
$heads = preg_replace('/<h([1-'.$depth.'])>/','<li class="toc$1">',$heads);
$heads = preg_replace('/<\/h[1-'.$depth.']>/','</a></li>',$heads);

//plug the results into appropriate HTML tags
$heads = '<div id="toc">
<p id="toc-header">Contents</p>
<ul>
'.stripslashes($heads).'
</ul>
</div>';


$content = '<h1>'.$row[title].'</h1> '.$content.' ';

$c_main = '
<h1>Team Wiki</h1>


                '.$link2.' '.$link.'


                '.$c_sidebar.'



    <div style="width:5px;float:left;height:1px;"></div>
    <div class="portlet" style="float:left;width:590px">
            <div style="padding:10px">
							'.$heads.'
                '.($content).'
            </div>
    </div>


';

}









?>