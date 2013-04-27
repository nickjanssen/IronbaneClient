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



if ( !isset($page) ) {
	die();
}

if ( !is_numeric($page) ) {
	die();
}



$query = "SELECT * FROM bcs_pages WHERE id='$page'";
$result = mysql_query($query) or bcs_error("<b>SQL ERROR</b> in <br>file " . __FILE__ . " on line " . __LINE__ . "<br><br><b>" . $query . "</b><br><br>" . mysql_error());
$row = mysql_fetch_array($result);

$queryb = "SELECT * FROM bcs_users WHERE id='$row[madeby]'";
$resultb = mysql_query($queryb) or bcs_error("<b>SQL ERROR</b> in <br>file " . __FILE__ . " on line " . __LINE__ . "<br><br><b>" . $query . "</b><br><br>" . mysql_error());
$rowb = mysql_fetch_array($resultb);

$c_title = $row[title];

if ( $action == "edit" ) {
	if ( $submit ) {
		$safe_content = mysql_real_escape_string($_POST[content]);
		$safe_title = strip_tags($_POST[title]);
	$query = "UPDATE bcs_pages SET lastupdated = '".time()."', madeby = '$userdata[id]', title = '$safe_title', content = '$safe_content' WHERE id='$page'";
	$result = mysql_query($query) or bcs_error("<b>SQL ERROR</b> in <br>file " . __FILE__ . " on line " . __LINE__ . "<br><br><b>" . $query . "</b><br><br>" . mysql_error());

	header("Location: index.php?plugin=page&page=".$page);
	}
	else {
$c_main = '

<div align="center"><h1>'.$row[title].'</h1></div>
	  <table width="800" cellpadding="20" cellspacing="0" border="0" class="forumline" align="center">
	   <tr>
		<td class="row1">
<form action="index.php?plugin=page&amp;action=edit&amp;page='.$page.'" method="POST">
<span class="gen"><input type="text" name="title" value="'.$row[title].'" style="width:100%;height:30px;font-size:25px">

<textarea name="content" style="width:100%;height:500px">'.($row[content]).'</textarea><br /><br />
    <div align=center><input type="submit" name="submit" value="Submit" class=mainoption></div>

</form>
                </td>
	   </tr>
	  </table>

';
	}
}
else {

if ( $s_admin == TRUE ) {
	$s_admin_link = " ".createLink("Edit this page","index.php?plugin=page&amp;action=edit&amp;page=".$page)."";
}



	$c_main = '

<h1>'.$row[title].''.$s_admin_link.'</h1>

'.$row[content].'


';

}





?>
