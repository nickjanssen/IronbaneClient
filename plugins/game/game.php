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


header("Expires: Tue, 03 Jul 2001 06:00:00 GMT");
header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");


$noTitlePostFix = true;
$c_title = "Ironbane";
$no_site_css = true;
$use_jscrollpane = true;

if ($guest) {
    $s_admin = false;
    $s_auth = false;
}

$hermesquote = ChooseRandom("Sweet llamas of the Bahamas!
Sweet lion of Zion!
Sweet three-toed sloth of ice planet Hoth!
Sweet bongo of the Congo!
Sweet Yeti of the Serengeti!
Sweet guinea pig of Winnipeg!
Sweet gorilla of Manila!
Sweet manatee of Galilee!
Sweet giant anteater of Santa Anita!
Sweet squid of Madrid!
Sweet sacred boa of Western and Eastern Samoa!
Sweet honeybee of infinity!
Cursed bacteria of Liberia!
Great cow of Moscow!
Sweet lamprey of Santa Fe!
Sweet coincidence of Port-au-Prince!
Sweet topography of cosmology!
Sweet Georgia Brown of Kingston Town!
Sweet dodo of Lesotho!
Sweet tornadoes of Barbados!
Sweet File-not-found of Puget Sound!
Sweet Robot Swan of Botswana.
Sweet candelabra of La Habra, LaBarbara!
Sweet ego of Montego!
Sweet freak of Mozambique!");


$externals = array(


    "External/Three_r52",
    "External/ThreeOctree",
    //"External/Three_r52.min",

    //"External/soundmanager2",
    "External/soundmanager2-nodebug-jsmin",

    "External/seedrandom",
    "External/ImprovedNoise",
    "External/dat.gui",
    "External/Detector",
    "External/Init",

    "External/Util",
    "External/Shared",
    "External/Stats",
    "External/SteeringBehaviourLight",
    "External/underscore-min",
    "External/jquery.mousewheel",
    "External/tween.min",

    "External/NodeHandler"
);

$internals = array(
    "Engine/Debug",
    "Engine/Events",
    "Engine/Input",
    "Engine/SocketHandler",
    "Engine/SoundHandler",
    "Engine/TextureHandler",
    "Engine/MeshHandler",

    "Engine/Shaders/PixelationShader",

    "Game",
    "Game/Hud",
    "Game/PhysicsObject",
    "Game/Unit",


    "Game/Billboard",
    "Game/Waypoint",
    "Game/ChatBubble",
    "Game/Mesh",
    "Game/DynamicMesh",
    "Game/MovingObstacle",
    "Game/Train",
    "Game/ToggleableObstacle",
    "Game/Lever",
    "Game/TeleportEntrance",
    "Game/TeleportExit",
    "Game/HeartPiece",
    "Game/MusicPlayer",
    "Game/Sign",
    "Game/Skybox",
    "Game/LootBag",
    "Game/LootableMesh",
    "Game/Fighter",
    "Game/Player",
    "Game/Chunk",
    "Game/Cinema",
    "Game/Cutscenes",

    "Game/ParticleTypes",
    "Game/Projectile",



    "Game/ParticleEmitter",
    "Game/ParticleHandler",

    "Game/TerrainHandler",
    "Game/LevelEditor"
);


if ($s_admin && $deploy) {

    //putenv("PATH=/usr/local/sbin:/usr/local/bin:/sbin:/bin:/usr/sbin:/usr/bin:/root/bin");

//  echo getcwd() . "\n";
//    chdir("/root");
//      echo getcwd() . "\n";
//    $command = './daemongameserver 2>&1';
//
//
//    if ( !system($command, $return) ) {
//        die("error running command: ".$command);
//    }
//
//    echo $return;

    function replaceOutsideSingleQuotes($search, $replace, $string) {
        $out = '';
        $a = explode("'", $string);
        for ($i = 0; $i < count($a); $i++) {
            if ($i % 2)
                $out .= $a[$i] . "'";
            else
                $out .= str_replace($search, $replace, $a[$i]) . "'";
        }
        return substr($out, 0, -1);
    }

    // Attempt to deploy a hard-to-hack, lightweight script
    // Get all external files
    $externalfile = "";
    foreach ($externals as $value) {
        $externalfile .= file_get_contents('plugins/game/js/' . $value . '.js');
    }

    // Get all internals, they are hidden behind a password-named dir
    $file = "";
    foreach ($internals as $value) {
        $file .= file_get_contents('plugins/game/js/Game/' . $value . '.js');

    }



    file_put_contents('plugins/game/js/engine.js', $file);


    //die("uncompressed deploy ok!");

    include("php-closure.php");

    $c = new PhpClosure();
    $file = $c->add("plugins/game/js/engine.js")
            //->advancedMode()
            //->useClosureLibrary()
            ->cacheDir("plugins/game/js/")
            ->write();

    file_put_contents('plugins/game/js/engine.js', $file);


    die("compressed deploy ok!");

    $file = str_replace("\'", "#TEMPQUOTE#", $file);
    $file = replaceOutsideSingleQuotes('"', "'", $file);
    $file = str_replace("#TEMPQUOTE#", "\'", $file);

    // We got all things that we like to compress
    $toBeStripped = $file;

    //$toBeStripped = file_get_contents('plugins/game/js/PClarLeDXC2DuPo/engine_pre.js');

    $toBeStripped = preg_replace('!/\*.*?\*/!s', '', $toBeStripped);
    $toBeStripped = preg_replace('/\n\s*\n/', "\n", $toBeStripped);

    $toBeStripped = preg_replace('/#.*/', '', preg_replace('#//.*#', '', preg_replace('#/\*(?:[^*]*(?:\*(?!/))*)*\*/#', '', ($toBeStripped))));

    $toBeStripped = preg_replace("/[^a-zA-Z0-9\s]/", " ", $toBeStripped);
    $toBeStripped = preg_replace('/\s+/', ' ', $toBeStripped);

    $toBeStripped = preg_replace('/[0-9]/', ' ', $toBeStripped);


    $vars = explode(" ", "break case catch continue default delete do else finally for function if in instanceof new return switch this throw try typeof var void while with abstract boolean byte char class const debugger double enum export extends final float goto int interface implements import long native package private protected public short static super synchronized throws transient volatile isFinite Helvetica cssFloat plugins game data inArray SERVER Arial emit startdata jQuery Init Class String ironbane loggedIn characterUsed using_ie using_safari runningLocal hquote debugging chars charCount preCatsTiles preCatsObjects preGameObjects zone zones items units unitTemplates setInterval clearInterval ajax Lines globalEnable enableWorldBuilder zoomLevel enableWorldPainter selectedTile setDigMode setFlattenMode brushWidth brushFeather brushHeight paintSize enableObjectPlacer setDeleteMode selectTile selectObject guest pass characterID id players isLoaded isLoading _ camDistance camHeight wbMode flattenHeight setPlayerHeightAsFlattenHeight opMode enableNPCEditor neMode npcTemplate addNPC cheatClimb MakeHoverBox draggable droppable success getJSON getSelection removeAllRanges without each currentHoverDiv cx cz tx tz setPlayerHeightAsFlattenHeight substr split join errmsg subtype equipped measureText strokeText fillText");

    function sorter($a, $b) {
        return strlen($b) - strlen($a);
    }

    usort($vars, 'sorter');



    $toBeStripped = preg_replace('/(".*?")/', ' ', $toBeStripped);
    $toBeStripped = preg_replace('/(\'.*?\')/', ' ', $toBeStripped);
    $toBeStripped = preg_replace('/\s+/', ' ', $toBeStripped);

    $toBeStripped = explode(" ", $toBeStripped);


    $toBeStripped = array_unique($toBeStripped);


    foreach ($vars as $value) {
        //$toBeStripped = str_replace(" " . $value . " ", " ", $toBeStripped);
//        if ( in_array($value, $toBeStripped) ) {
//            unset($toBeStripped[$value]);
//        }
        $key = array_search($value, $toBeStripped);
        if ($key) {
            array_splice($toBeStripped, $key, 1);
        }
    }

    //die(implode(" ", $toBeStripped));

    usort($toBeStripped, 'sorter');

    $frame = file_get_contents('plugins/game/frame.html');

    foreach ($toBeStripped as $key => $value) {
        if (empty($value)) {
            unset($toBeStripped[$key]);
            continue;
        }

        $pos = strpos($externalfile, $value);

        if ($pos != false) {
            unset($toBeStripped[$key]);
        }

        $pos = strpos($frame, $value);

        if ($pos != false) {
            unset($toBeStripped[$key]);
        }

        if (is_numeric($value) || is_numeric(substr($value, 0, 1)))
            unset($toBeStripped[$key]);
    }


    file_put_contents('plugins/game/js/stripped.txt', implode(" ", $toBeStripped));

    $hashwords = $toBeStripped;

    function rand_case($str) {
        for ($i = 0; $i < strlen($str); ++$i) {
            $ret .= ( rand(0, 1) == 0 ) ? strtolower($str[$i]) : strtoupper($str[$i]);
        }

        return $ret;
    }

    // Replace escaped single quotes
    $file = str_replace("\'", "#TEMPQUOTE#", $file);
    //$hashwords = explode(" ", file_get_contents('plugins/game/js/stripped.txt'));

    $count = 0;
    foreach ($hashwords as $value) {

        // Check if the word is a subword inside one of the keywords
        $found = false;
        foreach ($vars as $kw) {
            $pos = strpos($kw, $value);

            if ($pos != false) {
                $found = true;
                break;
            }
        }
        if ($found)
            continue;

        $count++;

        //$file = str_replace($value, "v".($count++), $file);
        $file = replaceOutsideSingleQuotes($value, "v" . ($count++), $file);
        //$file = replaceOutsideSingleQuotes($value, "__" . rand_case($value), $file);
        //$file = replaceOutsideSingleQuotes($value, "_".md5($value), $file);
        // $replace = $value;
        // $with = "v".($count++);
        // echo $replace ." with ".$with."<br>";
        // die($file);
        // $file = preg_replace ( '/\G(?:"[^"]*"|\'[^\']*\'|[^"\''.$replace.']+)*\K'.$replace.'+/', $with, $file);
    }



    $file = str_replace("#TEMPQUOTE#", "\'", $file);
    //
    //
    file_put_contents('plugins/game/js/engine.js', $file);
    //
    //TODO compiler
    //http://bohuco.net/blog/2010/09/use-google-closure-compiler-local-with-php/



    die("Deploy OK! <br><br><a href=game.php>Continue</a> <a href=game.php?guest=1>Continue as Guest</a>");
}



$c_head .= '<script src="http:/'.$ironbane_server_hostname.':'.$ironbane_server_port.'/socket.io/socket.io.js"></script>
';




// Load news posts for in-game
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
    $newsPosts .= '<b><a href="forum.php?action=topic&amp;topic='.$row[topic_id].'">'.$row[title].'</a></b><br>'.(timeAgo($row[time])).' ago<div class="spacersmall"></div>'.post_parse($row[content]).'<hr>';
}
$newsPosts = preg_replace("/<img[^>]+\>/i", "", $newsPosts);
$newsPosts = str_replace("'", "\'", $newsPosts);
$newsPosts = preg_replace('/\s+/', ' ', trim($newsPosts));


if ($userdata[editor]) {
    // Preload cats
    // 1 = Terrain tile
    // 2 = Gameobjects
    $query = "SELECT * FROM ib_editor_cats";
    $result = mysql_query($query) or bcs_error("<b>SQL ERROR</b> in <br>file " . __FILE__ . " on line " . __LINE__ . "<br><br><b>" . $query . "</b><br><br>" . mysql_error());

    for ($x = 0; $x < mysql_num_rows($result); $x++) {
        $row = mysql_fetch_array($result);

//        if ($row[terrain_only] == 1) {
            $preCatsTilesLoad .= 'preCatsTiles.push({name: "' . $row[name] . '", range: "' . $row[range] . '", limit_x: ' . $row[limit_x] . '});';
//        } else {
//            $preCatsObjectsLoad .= 'preCatsObjects.push({name: "' . $row[name] . '", range: "' . $row[range] . '", limit_x: ' . $row[limit_x] . '});';
//        }
    }

//    $query = "SELECT * FROM ib_gameobjects";
//    $result = mysql_query($query) or bcs_error("<b>SQL ERROR</b> in <br>file " . __FILE__ . " on line " . __LINE__ . "<br><br><b>" . $query . "</b><br><br>" . mysql_error());
//
//    for ($x = 0; $x < mysql_num_rows($result); $x++) {
//        $row = mysql_fetch_array($result);
//
//        $preGameObjectsLoad .= 'preGameObjects[' . $row[id] . '] = {name: "' . $row[name] . '", type: ' . $row[type] . ', param: ' . $row[param] . '};
//		';
//    }
}

$query = "SELECT * FROM ib_zones";
$result = mysql_query($query) or bcs_error("<b>SQL ERROR</b> in <br>file " . __FILE__ . " on line " . __LINE__ . "<br><br><b>" . $query . "</b><br><br>" . mysql_error());

for ($x = 0; $x < mysql_num_rows($result); $x++) {
    $row = mysql_fetch_array($result);

    $preZonesLoad .= 'zones[' . $row[id] . '] = {
            id: ' . $row[id] . ',
            name: "' . $row[name] . '",
            type: "' . $row[type] . '"
        };
        zoneSelection["' . $row[name] . '"] = ' . $row[id] . ';
        ';
}

$query = "SELECT * FROM ib_item_templates";
$result = mysql_query($query) or bcs_error("<b>SQL ERROR</b> in <br>file " . __FILE__ . " on line " . __LINE__ . "<br><br><b>" . $query . "</b><br><br>" . mysql_error());

for ($x = 0; $x < mysql_num_rows($result); $x++) {
    $row = mysql_fetch_array($result);



    $preItemsLoad .= 'items[' . $row[id] . '] = {
            id: ' . $row[id] . ',
            name: "' . $row[name] . '",
            type: "' . $row[type] . '",
            image: ' . $row[image] . ',
            //charimage: ' . $row[charimage] . ',
            delay: ' . $row[delay] . ',
            attr1: ' . $row[attr1] . ',
            particle: "' . $row[particle] . '",
            subtype: "' . $row[subtype] . '"
        };
        ';
}

$unitTypeEnum = array();
$sth = mysql_query("SELECT id,name,type,health,armor,param,size,special,weaponoffsetmultiplier,friendly FROM ib_unit_templates");
$rows = array();
while ($r = mysql_fetch_assoc($sth)) {

    if ( $r[special] != 1 ) {
        array_push($unitTypeEnum, '"' . $r[name] . '":' . $r[id] . '');
    }

    unset($r[special]);

    $rows[$r[id]] = $r;


}
$preUnitTemplates = implode(",", $unitTypeEnum);
$preUnitsLoad = json_encode($rows, JSON_NUMERIC_CHECK );



$modelEnum = array();


$sth = mysql_query("SELECT * FROM ib_meshes ORDER BY category, name");
$rows = array();
while ($r = mysql_fetch_assoc($sth)) {

    for($x=1;$x<=10;$x++){
        if ( $r["t".$x] == 1 ) {
            unset($r["t".$x]);
        }
        if ( $r["ts".$x] == 1.00 ) {
            unset($r["ts".$x]);
        }
    }

    $rows[$r[id]] = $r;



    array_push($modelEnum, '"' . $r[category] . ': ' . $r[name] . '":' . $r[id] . '');
}
$modelEnum = implode(",", $modelEnum);
$preMeshes = json_encode($rows, JSON_NUMERIC_CHECK );


//die($preItemsLoad);


$browser = getBrowser();

$using_ie = $browser[name] == "MSIE" ? "true" : "false";
$using_safari = $browser[name] == "Safari" ? "true" : "false";

$c_head .= '<link rel="stylesheet" href="plugins/game/style.css" type="text/css">
';

$c_footer .= '
	<script>
	var startdata = {
        loggedIn: ' . ($s_auth ? 'true' : 'false') . ',

        characterUsed: 0,

        using_ie: ' . $using_ie . ',
        using_safari: ' . $using_safari . ',

	};

    var ironbane_hostname = "'.$ironbane_hostname.'";
    var ironbane_port = '.$ironbane_port.';
    var ironbane_root_directory = '.$ironbane_root_directory.';

	var hquote = "' . $hermesquote . '";

    var isEditor = '.($userdata[editor] ? "true" : "false").';

	var debugging = ' . (($userdata[admin] || $debug) ? "true" : "false") . ';

    var items = {};
    ' . ($preItemsLoad) . '
    var units = ' . ($preUnitsLoad) . ';
    var unitTemplates = {
        ' . ($preUnitTemplates) . '
    };

	var chars = [];

	var charCount = 0;

	var preCatsTiles = [];
	' . ($preCatsTilesLoad) . '

	var preCatsObjects = [];
	' . ($preCatsObjectsLoad) . '

	var preGameObjects = {};
	' . ($preGameObjectsLoad) . '

	var zones = {};
    var zoneSelection = {};
	' . ($preZonesLoad) . '

    var preMeshes = ' . $preMeshes . '

    preMeshes["0"] = {id:0,name:"ERROR",filename:"modelerror.obj",scale:1.00,t1:"tiles/402"};

    var ModelEnum = {
        ' . ($modelEnum) . '
    };

    var newsPosts = \''.($newsPosts).'\';

	</script>';


foreach ($externals as $value) {
    $c_footer .= '
    <script src="plugins/game/js/' . $value . '.js" type="text/javascript"></script>';
}


foreach ($internals as $value) {
    $c_footer .= '
	<script src="plugins/game/js/' . $value . '.js'.($s_admin?'':'?'.$time.'').'" type="text/javascript"></script>';
}



$use_simple_rendering = 1;

$c_main = file_get_contents("plugins/game/shaders.html");
$c_main .= file_get_contents("plugins/game/frame.html");

?>
