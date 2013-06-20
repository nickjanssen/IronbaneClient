<?php

if ( !defined('BCS') ) {
	die("ERROR");
}

if (!$s_auth){
    die('not logged in!');
};

if ( $_GET['action'] === "getlines" ) {


    $last = intval(parseToDB($_GET['last']));

    if ( !$last ) $last = 0;

    $query = "SELECT * from bcs_chat WHERE ".
        ($last===0?"time > $time - 86400":"id > $last")."";
    $result = bcs_query($query) or bcs_error("<b>SQL ERROR</b> in <br>file " . __FILE__ . " on line " . __LINE__ . "<br><br><b>" . $query . "</b><br><br>" . mysql_error());

    $array = array();

    while($row = mysql_fetch_assoc($result)) {

        $row['authorName'] = memberName($row['author']);

        array_push($array, $row);
    }



    $output = array('lines' => ($array),
        'chatters' => getRawListOfOnlineMembers());

    die(json_encode($output, JSON_NUMERIC_CHECK));
}
else {

    $text = $_POST['text'];

    if ( empty($text) ) die('no text given!');

    writeChatMessage($userdata['id'],
        (htmlspecialchars($text, ENT_QUOTES)),
        ChatTypes::Chat);
}

?>
