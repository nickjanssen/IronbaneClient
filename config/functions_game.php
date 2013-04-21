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


function WorldToCellCoordinates($x, $z, $cellsize) {

	if ( $cellsize % 2 != 0 ) die("Cellsize not dividable by 2!");

        $cellhalf = $cellsize / 2;
	//  5 / 20 = 0
	// 20 / 20 = 1
	$x = floor(($x + $cellhalf)/$cellsize);
	$z = floor(($z + $cellhalf)/$cellsize);

        $coord["x"] = $x;
        $coord["z"] = $z;
	return $coord;
}

function CellToWorldCoordinates($x, $z, $cellsize) {

	if ( $cellsize % 2 != 0 ) die("Cellsize not dividable by 2!");

	$cellhalf = $cellsize / 2;
	// 0 * 20 - 10 = -10;
	// 1 * 20 - 10 = 10;
	$x = ($x * $cellsize);
	$z = ($z * $cellsize);

        $coord["x"] = $x;
        $coord["z"] = $z;
	return $coord;
}

//function depCreateFullCharacterImage($character, $skin, $hair) {
//
//
//    $bigfrontmultiplier = 8;
//
//    $final_front = imagecreatetruecolor(16*$bigfrontmultiplier, 18*$bigfrontmultiplier);
//		// Create an image from the base skin color, without hair and eyes
//
//    // Start with the skin
//    $final_base = imagecreatefrompng("plugins/game/images/characters/base/skin/$skin.png");
//
//
//    $hair = imagecreatefrompng("plugins/game/images/characters/base/hair/$hair.png");
//    imagecopyresampled ($final_base, $hair, 0, 0, 0, 0, 48, 144, 48, 144);
//
//    // Copy all layers
//
//
//		// Put on
//
//    imagesavealpha($final_base, true);
//    imagealphablending($final_base, false);
//
//    imagesavealpha($final_front, true);
//    imagealphablending($final_front, false);
//
//
//    imagecopyresampled ($final_front, $final_base, 0, 0, 16, 72, 16*$bigfrontmultiplier, 18*$bigfrontmultiplier, 16, 18);
//
//
//    $dir = "plugins/game/images/characters/$character/";
//
//    if ( !is_dir($dir) ) {
//        mkdir($dir);
//    }
//
//    imagepng ($final_base, $dir."full.png");
//    imagepng ($final_front, $dir."big.png");
//
//}

function CreateFullCharacterImage($skin, $eyes, $hair, $feet, $body, $head, $big=false) {

    // Todo:make cache

    $big = $big ? 1 : 0;

    if  ( $big ) {
        $bigfrontmultiplier = 8;

        $final_front = imagecreatetruecolor(16*$bigfrontmultiplier, 18*$bigfrontmultiplier);
    }

    // Start with the skin
    $skin = intval($skin);
    $eyes = intval($eyes);
    $hair = intval($hair);

    $feet = intval($feet);
    $body = intval($body);
    $head = intval($head);

    // Start with the skin
    if ( !is_int($skin) ) die("bad skin id");

    if ( !file_exists("plugins/game/images/characters/base/skin/$skin.png") ) {
      //die( "not found: plugins/game/images/characters/base/skin/$skin.png");
      $final_base = create_blank(48, 144);
    }
    else {
      $final_base = imagecreatefrompng("plugins/game/images/characters/base/skin/$skin.png");
    }

    // Eyes
    if ( is_int($eyes) && $eyes > 0 ) {
        if ( !file_exists("plugins/game/images/characters/base/eyes/$eyes.png") ) die("eyes not found ($eyes)");

        $eyes_img = imagecreatefrompng("plugins/game/images/characters/base/eyes/$eyes.png");
        imagecopy($final_base, $eyes_img, 0, 0, 0, 0, 48, 144);
    }


    // Only helmet or hair, not both
    if ( is_int($head) && $head > 0) {
        if ( !file_exists("plugins/game/images/characters/base/head/$head.png") ) die("head not found");

        $head_img = imagecreatefrompng("plugins/game/images/characters/base/head/$head.png");
        //imagecopyresampled ($final_base, $head_img, 0, 0, 0, 0, 48, 144, 48, 144);
        imagecopy($final_base, $head_img, 0, 0, 0, 0, 16, 144);
        imagecopy($final_base, $head_img, 16, 0, 0, 0, 16, 144);
        imagecopy($final_base, $head_img, 32, 0, 0, 0, 16, 144);

    }
    else if ( is_int($hair) && $hair > 0) {
        if ( !file_exists("plugins/game/images/characters/base/hair/$hair.png") ) die("hair not found");

        $head_img = imagecreatefrompng("plugins/game/images/characters/base/hair/$hair.png");
        //imagecopyresampled ($final_base, $head_img, 0, 0, 0, 0, 48, 144, 48, 144);
        imagecopy($final_base, $head_img, 0, 0, 0, 0, 16, 144);
        imagecopy($final_base, $head_img, 16, 0, 0, 0, 16, 144);
        imagecopy($final_base, $head_img, 32, 0, 0, 0, 16, 144);
    }


    if ( is_int($feet) && $feet > 0 ) {
        if ( !file_exists("plugins/game/images/characters/base/feet/$feet.png") ) die("feet not found");

        $feet_img = imagecreatefrompng("plugins/game/images/characters/base/feet/$feet.png");
        imagecopy($final_base, $feet_img, 0, 0, 0, 0, 48, 144);
    }

    if ( is_int($body) && $body > 0 ) {
        if ( !file_exists("plugins/game/images/characters/base/body/$body.png") ) die("body not found");

        $body_img = imagecreatefrompng("plugins/game/images/characters/base/body/$body.png");
        imagecopy($final_base, $body_img, 0, 0, 0, 0, 48, 144);
    }



    header('Content-Type: image/png');


    imagesavealpha($final_base, true);
    imagealphablending($final_base, false);

    if  ( $big ) {
        imagesavealpha($final_front, true);
        imagealphablending($final_front, false);

        imagecopyresampled ($final_front, $final_base, 0, 0, 16, 72, 16*$bigfrontmultiplier, 18*$bigfrontmultiplier, 16, 18);
    }

    $dir = "plugins/game/images/characters/cache/";

    //$filename = $dir."{$character}_{$skin}_{$hair}_{$head}_{$body}_{$feet}_{$big}.png";
    $filename = $dir."{$skin}_{$eyes}_{$hair}_{$head}_{$body}_{$feet}_{$big}.png";

    if ( !file_exists($filename) ) {
        imagepng ($big ? $final_front : $final_base, $filename);
    }

    imagepng ($big ? $final_front : $final_base);


}


function rnd($minv, $maxv){
	if ($maxv < $minv) return 0;
	return rand($minv, $maxv);
}

function getName($minlength, $maxlength, $prefix, $suffix)
{
	$prefix = isset($prefix) ? $prefix : '';
	$suffix = isset($suffix) ? $suffix : '';
	//these weird character sets are intended to cope with the nature of English (e.g. char 'x' pops up less frequently than char 's')
	//note: 'h' appears as consonants and vocals
	$vocals = 'aeiouyh' . 'aeiou' . 'aeiou';
	$cons = 'bcdfghjklmnpqrstvwxz' . 'bcdfgjklmnprstvw' . 'bcdfgjklmnprst';
	$allchars = $vocals . $cons;
	//minlength += prefix.length;
	//maxlength -= suffix.length;
	$length = rnd($minlength, $maxlength) - strlen($prefix) - strlen($suffix);
	if ($length < 1) $length = 1;
	//echo($minlength . ' ' . $maxlength . ' ' . $length);
	$consnum = 0;
	//alert(prefix);
	/*if ((prefix.length > 1) && (cons.indexOf(prefix[0]) != -1) && (cons.indexOf(prefix[1]) != -1)) {
		//alert('a');
		consnum = 2;
	}*/
	if (strlen($prefix) > 0) {
		for ($i = 0; $i < strlen($prefix); $i++) {
			if ($consnum == 2) $consnum = 0;
			if ( strpos($cons, $prefix[$i]) != false ) $consnum++;
			// if (cons.indexOf(prefix[i]) != -1) {
				// consnum++;
			// }
		}
	}
	else {
		$consnum = 1;
	}

	$name = $prefix;

	for ($i = 0; $i < $length; $i++)
	{
		//if we have used 2 consonants, the next char must be vocal.
		if ($consnum == 2)
		{
			$touse = $vocals;
			$consnum = 0;
		}
		else $touse = $allchars;
		//pick a random character from the set we are goin to use.
		$number = (rnd(0, strlen($touse) - 1));
		$c = $touse{$number};
		$name = $name . $c;
		if ( strpos($cons, $c) != false) $consnum++;
		//if (cons.indexOf(c) != -1) consnum++;
	}
	$name = strtoupper($name{0}) . substr($name, 1, strlen($name)) . $suffix;
	//name = name.charAt(0).toUpperCase() + name.substring(1, name.length) + suffix;
	return $name;
}

function GetPing($ip=NULL) {
 if(empty($ip)) {$ip = $_SERVER['REMOTE_ADDR'];}
 if(getenv("OS")=="Windows_NT") {
  $exec = exec("ping -n 3 -l 64 ".$ip);
  return end(explode(" ", $exec ));
 }
 else {
  $exec = exec("ping -c 3 -s 64 -t 64 ".$ip);
  $array = explode("/", end(explode("=", $exec )) );
  return ceil($array[1]) . 'ms';
 }
}



?>
