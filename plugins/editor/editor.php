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




$plugin_name = "Editor";
$plugin_version = "1.0.0";
$plugin_author = "Beather (admin@ironbane.com)";


//TODO add default value for ADD

if (!defined('BCS')) {
    die("ERROR");
}
$manualid = false;
$use_nicedit = true;
$autoincrementid = false;

if ( !$s_editor ) {
    bcs_die("You are not allowed to access this page");
}

$limitchar = 3;

$links_n = "Item Templates,Unit Templates,Painter/Placer Cats,3D meshes,Books";
$links_u = "items,units,cats,meshes,books";



include('plugins/explore/functions_rpg.php');

//  &#44;

if ($action == "item") {

	$maindesc = "Items are used for various uses: players can equip, use, pickup and drop them. Items include weapons, armor, potions, quest items, food, drinks, recipes and more in the future.";

    $values_1 = "Name,Description,Image ID,Type,Armor Type,Level requirement,Quality,Cooldown,Weight,Cost,Equip Spot,Durability,Classes,Max Carry Quantity,Strength Modifier,Vitality Modifier,Willpower Modifier,Spirit Modifier,Dexterity Modifier,Special,Special Attribute,Special Duration";
    $values_2 = "
    A unique name for this item&#44; e.g. Apple&#44; Sword Of Justice&#44; Rusty Coin. Try <a target=_new href=http://www.seventhsanctum.com>Seventh Sanctum</a> for some cool names,
    Optional: text that will display under the item's name in <i><font color=yellow>yellow italic text</font></i>,
    The Icon ID of a 32x32 image for this item<br>" . createLink("Show available ID's", "plugins/game/images/items/showimages.php", " target=\"_blank\"") . ",
    The type of this item&#44; must be written in UPPERCASE LETTERS<br />Possible values: " . str_replace(",", " ", $rpg_config['weapon_list']) . " ARMOR FOOD DRINK<br>For weapons use the respective weapon type<br>For all sorts of armor type ARMOR and specify the armor type (see below) as well<br>FOOD gives health over time<br>DRINK gives magic over time,
    <b>Only when item type is ARMOR (see above)</b><br>What material is this armor made of<br>Possible values: CLOTH LEATHER MAIL PLATE,
    The item's power level on which the stats will be calculated as well as the minimum level a player has to be before they can use this item,
    Specify the quality level of this item (a number between 1 and 5)<br>In general quality alters the item in a way so it gets better stats and becomes more expensive.<br>Possible values:<br>1 = Mediocre<br>2 = Good<br>3 = Excellent<br>4 = Epic<br>5 = Legendary,
    Time in seconds the item needs to cool down before it can be used again.<br>Leave 0 to generate,
    Weight of the item measured in stones (A player can carry about 1500 stones of items: e.g. a heavy weapon should be around 200s)<br>Leave blank or 0 to generate,
    The amount of " . $rpg_config[name_money]." this item costs<br>Leave 0 to generate,
    If the item can be equipped (weapons and armor) enter the part of the body<br>Possible values:<br>
    " . str_replace(",", " ", $rpg_config['wield_list']) . " HAND HANDS<br>HAND = One-hand<br>HANDS = Dual-wield,
    <b>Warning: Not implemented yet!<br>Only for Armor or Weapons</b><br>The amount of damage this weapon or armor can take before it has to be repaired. Leave blank or 0 to generate,
    <b>Warning: Not implemented yet!</b><br>The classes that are allowed to use this item; this is a list of Class ID's separated by commas. See the Class Editor for valid ID's,
    <b>Warning: Not implemented yet!</b><br>How many times a player can carry this item,
	,
	,
	,
	,
	,
	Every item can have a special modifier that alters its stats or function<br>Possible values:<br>RESISTANCE_[element name in uppercase] e.g. RESISTANCE_FIRE : grants [special_attribute] resistance or immunity to an element<br>LIGHT : makes this item 0.5x as light as usual<br>ELEMENTAL : grants [special_attribute] resistance or immunity to all elements<br>CURSE : not implemented yet. Any ideas let me know<br>POISON: Chance On Hit: Poison [special_attribute] damage for [special_duration] turns.<br>BLEED: Chance On Hit: Bleed [special_attribute] damage for [special_duration] turns.<br />,
	Used in combination with Special,
	Used in combination with Special,
    ";
    $values_3 = "name,desc,img,type,type_armor,level,quality,cooldown_max,weight,cost,equips_at,durability_max,classes,carry_max,mod_strength,mod_vitality,mod_willpower,mod_spirit,mod_dexterity,special,special_attribute,special_duration";
    $values_4 = "name,equips_at,type,type_armor,level";
	$values_5 = "name,desc,imgitempreview,med,med,small,small,small,small,small,med,small,med,small,small,small,small,small,small,med,small,small";

	$defaults = ",
,
,
,
,
1,
2,
0,
0,
0,
,
0,
,
0,
0,
0,
0,
0,
0,
,
0,
0,";

	//$values_4 = $values_3;

    $table_edit = "rpg_items";
    $table_crit = " WHERE status = 'DEFAULT'";
} elseif ($action == "shop") {

	$maindesc = "Shoplists are lists of items that are used by vendors. They exist so you can make an item list, and set a vendor to sell the items on that list.";


    $values_1 = "Name,Description,Image ID,Item List";
    $values_2 = "
    The name of this item list,
    A description that will be displayed next to the shopname like \"Jerry's finest apple pies of the land\",
    An image ID to be shown for this shoplist on the vendor screen. " . createLink("Show available ID's", "plugins/game/images/items/showimages.php", " target=\"_blank\"") . ",
    The actual list of item ID's. <b>Must be separated by commas</b> e.g. \"8645&#44;646&#44;348\" Don't add a comma at the beginning or at the end of the string!";
    $values_3 = "name,description,img,itemlist";
    $values_4 = "" . $values_3;
	$values_5 = "name,description,imgitempreview,itemlist";

    $table_edit = "rpg_shops";
    $table_crit = "";
} elseif ($action == "class") {
	$maindesc = "Used by players and NPC's to calculate stats at any given level&#44; and to give them special features. (<a href=http://en.wikipedia.org/wiki/Character_class target=_new>Wikipedia article</a>) <b>Please don't add new player classes</b>, instead if you have a good suggestion let the team know. Adding a new class requires the whole site to be reconfigured and can't be done from this editor alone. You can however add classes for use with NPCTypes should you require so.";

    $values_1 = "Name,Description,Weapon wields,Armor wields,Initial Spells,Initial Items,Initial Equip,Hp,Mp,Strength,Vitality,Willpower,Spirit,Dexterity,Allowed for Registration";
    $values_2 = ",
Will be displayed on the 'Select a Class' page and shows the features (=advantages + disadvantages),
The weapons this class can use. Can be one or multiple of the following list: " . str_replace(",", " ", $rpg_config['wield_list']) . "<br><b>Must be separated by commas!</b>,
The type of armor this class can use. Can be one or multiple of the following list: " . str_replace(",", " ", $rpg_config['armor_list']) . "<br><b>Must be separated by commas!</b>,
A list of spell ID's which this class starts with<br><b>Must be separated by commas!</b>,
A list of item ID's which this class has in the inventory on start<br><b>Must be separated by commas!</b>,
A list of item ID's which this class has equipped on start<br><b>Must be separated by commas!</b><br><br><br><hr><b>Note: the following fields are factors used to calculate stats. They are all relative to eachother. See existing classes for a reference when balancing</b><hr><br>,
The amount of Hit Points this class has on any level,
The amount of Magic Points this class has on any level,
The amount of Strength this class has on any level,
The amount of Vitality this class has on any level,
The amount of Willpower this class has on any level,
The amount of Spirit this class has on any level,
The amount of Dexterity this class has on any level,
Whether players can use this class or if it is only used by NPC Types. Only 0 or 1.";
    $values_3 = "name,description,wields,wields_armor,initial_spells,initial_items,initial_equip,hp,mp,strength,vitality,willpower,spirit,dexterity,regallowed";
    $values_4 = "name,wields,wields_armor,initial_spells,initial_items,initial_equip,hp,mp,strength,vitality,willpower,spirit,dexterity,regallowed";

	$values_5 = "Name,textarea,Weapon wields,Armor wields,Initial Spells,Initial Items,Initial Equip,Hp,Mp,Strength,Vitality,Willpower,Spirit,Dexterity,Allowed for Registration";

    $table_edit = "rpg_classes";
    $table_crit = "";
} elseif ($action == "trainer") {

	$maindesc = "Similar to Shoplists, the Trainlists are lists of spells that are used by trainers. They exist so you can make a spell list, and set a trainer to sell the spells on that list.";

    $values_1 = "Name,Description,Image ID,Spell List";
    $values_2 = "
    The name of this spell list,
    A description that will be displayed next to the shopname like \"The finest in dark magic since a thousand years.\",
    An image ID to be shown for this shoplist on the vendor screen. " . createLink("Show available ID's", "plugins/game/images/spells/showimages.php", " target=\"_blank\"") . ",
    The actual list of spell ID's. <b>Must be separated by commas</b> e.g. \"8645&#44;646&#44;348\" Don't add a comma at the beginning or at the end of the string!";
    $values_3 = "name,description,img,spelllist";
    $values_4 = "" . $values_3;
	$values_5 = "name,description,imgspellpreview,itemlist";

    $table_edit = "rpg_trainers";
    $table_crit = "";
} elseif ($action == "spell") {
	$maindesc = "Spells are a primary aspect of the game and come in multiple forms. They can require time to cast, can be set to act like a projectile and have special properties that alter the spell.";

    $values_1 = "Name,Description,Quality,Image ID,Particle ID,Rank,Type,Damage Type,Level requirement,Train Cost,MP Cost,Cooldown,Cooldown Share,Special,Special Attribute,Special Duration,Multiplier Damage,Multiplier Magic Points";
    $values_2 = "e.g. 'Fireball'&#44; try <a target=_new href=http://www.seventhsanctum.com>Seventh Sanctum</a> for some cool names,
	Optional: text that will display under the item's name in <i><font color=yellow>yellow italic text</font></i>,
	Specify the quality level of this spell (a number between 1 and 5)<br>In general quality alters the spell in a way so it gets better stats and becomes more expensive.<br>Possible values:<br>1 = Mediocre<br>2 = Good<br>3 = Excellent<br>4 = Epic<br>5 = Legendary,
    The Icon ID of a 32x32 image for this spell<br>" . createLink("Show available ID's", "plugins/game/images/items/showimages.php", " target=\"_blank\"") . ",
	The particle type that will be used in-game when this spell is used. Use an ID from the following list:<br><br>Projectile Arrow: 101<br>
Projectile Fireball: 102<br>
Projectile Iceball: 103<br>
Projectile Shadowball: 104<br>
Projectile Holyball: 105<br>
Projectile Shock: 106<br><br>

Projectile Iceshard: 107<br>
Projectile Purpleshard: 108<br>
Projectile Greenshard: 109<br>
Projectile Orangeshard: 110<br><br>

Animation Rune: 201<br>
Animation Heal: 202<br>
Animation Heart: 203<br>
Animation Poison: 204<br>
Animation Sand: 205<br><br>
If you would like new particle sprites let me know,
	The rank of this spell that displays under the spell name. Only for visual purposes! Rank does not alter the stats in any way.,
	The type of this spell&#44; must be written in UPPERCASE LETTERS<br />Possible values:
	<br>HEAL: (not implemented yet)<br>
	USEWEAPON: allows the usage of weapons. <b>Requires Special field to be filled in with weapon type (one the following: " . str_replace(",", " ", $rpg_config['weapon_list']) . ").</b><br>
	USEARMOR: allows the wearing of armor. <b>Requires Special field to be filled in with armor type (one the following: " . str_replace(",", " ", $rpg_config['armor_list']) . ").</b><br>
	ELEMENTAL: for elemental attacks. <b>Requires Damage Type to be filled in! (see below)</b>,
	Only when spell type is ELEMENTAL<br>Damage type must be one of the following: " . str_replace(",", " ", $rpg_config[element_list]) . ",
	The spell's power level on which the stats will be calculated as well as the minimum level a player has to be before they can use this spell,
	The amount of " . $rpg_config[name_money]." this item costs to train<br>Leave 0 to generate,
	The amount of Magic Points this spell uses.<br>Leave 0 to generate,
	Time in seconds it takes for this spell before it can be used again<br>Leave 0 to generate,
	(Optional) A unique ID for cooldown sharing: enter any number here&#44; and other spells with the same number will share the cooldown. Leave 0 to ignore,
	(Optional) Every spell can have a special modifier that alters its stats or function. <b>Still in development: Attribute and Duration are still unused</b>,
	Used in combination with Special,
	Used in combination with Special,
	Total spell damage is multiplied with this value,
	Total MP needed is multiplied with this value,,,,,,";
    $values_3 = "name,desc,quality,img,spellimg,rank,type,damagetype,level,cost,cost_mp,cooldown_max,cooldown_share,special,special_attribute,special_duration,multiplier_damage,multiplier_magic";
    $values_4 = "name,desc,type,level,rank";
	$limitchar = 80;
	$defaults = ",
,
2,
,
,
1,
ELEMENTAL,
,
1,
0,
0,
0,
0,
,
,
,
1.0,
1.0
";
    $values_3 = "name,desc,quality,imgspellpreview,spellimg,rank,type,damagetype,level,cost,cost_mp,cooldown_max,cooldown_share,special,special_attribute,special_duration,multiplier_damage,multiplier_magic";
    $table_edit = "rpg_spells";
    $table_crit = " WHERE status = 'DEFAULT'";
} elseif ($action == "monster") {
    $values_1 = "Name,Image,Class ID,Difficulty,Extra Loot,Background IMG,Elemental Resistances,special_attack,spell_list";
    $values_2 = "
    Name,Image ID<br>" . createLink("Show available ID's", "plugins/battle/images/monsters/right/showimages.php", " target=\"_blank\"") . ",
    ,
    Default 1.0 (stats modifier),
    Additional loot items that may be looted when this monster dies<br>Following format:<br>chance:item;chance:item;...<br>chance = 1-100,
    0 = Random,
    0 = None&#44; 100 = Fully Resistant&#44; 200 = Fully Absorb<br>" . str_replace(",", "&#44;", $rpg_config[element_list]) . ",
    Special attacks that can be used by monster<br>Following format:<br>chance:special;chance:special;...<br>chance = 1-100,
    Spells that can be used by monster<br>Following format:<br>chance:spell;chance:spell;...<br>chance = 1-100";
    $values_3 = "name,img,class,difficulty,loot,bg,elemental_resistance,special_attack,spell_list";
    $values_4 = "" . $values_3;

    $table_edit = "rpg_monsters";
    $table_crit = "";
} elseif ($action == "npctype") {
	$maindesc = "NPC Types are the templates used by NPC's. This means that every NPC in the game links to a template that shares data such as the name, difficulty, spritesets and other special properties that are unique.";

    $values_1 = "Name,Class,Type,Difficulty Multiplier,Spriteset,Resistance,Features,Extra loot,Melee Animation";
    $values_2 = ",
A class ID used to calculate the NPC's stats,
A number which can be one of the following values:<br><br>
Normal: 1<br>
Aggressive: 2<br>
Vendor: 3<br>
Trainer: 4<br>
Defensive: 5<br>
Sign: 6<br>
Guard: 7<br>
Interact: 8<br>
Passive: 9<br>
Item: 10<br>
Quest: 11<br>
Teleport: 12<br>
Escort: 13<br>
Money: 14<br>
Chest: 15<br>
Boss: 16<br>
,A multiplier for the NPC's stats. <b>You should leave this at 1.0 most of the time except for special NPC's such as bosses</b>,
A spriteset ID that refers to 8 sprites the NPC will be using,
A special string that defines the elemental resistance for this NPCType. The string consists of 8 numbers separated by semicolons. <br><b>Format:</b> ".implode(";",(explode(",",$rpg_config[element_list])))."<br>
Example: 0;100;200;50;1100;0;0;0
gives 0% Fire resistance&#44;
100% Ice resistance&#44;
100% Lightning absorb &#44;
50% Wind resistance&#44;
10% Water absorb &#44; and no resistance for the other elements.,
A special list of battle features that this NPCType has. When an NPC is in battle&#44; it needs to know what to do. By default it will melee attack an enemy&#44; but it should also be able to cast spells and inflict temporary spell effects on an enemy (such as Poison&#44; Bleed&#44; etc...). To control which feature the NPC will take and when&#44; this field is used.<br><br>A feature can be either SPELL or BUFF. Spell simply means that the NPC will fire a spell&#44; and BUFF means that the NPC will inflict a temporary spell effect like Poison.<br>Every feature has a chance of being triggered. If no feature is triggered the NPC will simply melee attack.<br><br>
<b>The following format is used for the feature list:</b><br><i>[CHANCE]:[FEATURE]:[PARAM1]:[PARAM2];[CHANCE]:[FEATURE]:[PARAM1]:[PARAM2]...</i><br><br>If the field is left empty the NPC will always melee attack.<br><br>Possible values for BUFF: POISON and BLEED (more to come in the future&#44; any ideas let me know!)<br><br>POISON: inflicts [PARAM2] damage for [PARAM1] seconds<br>BLEED: inflicts [PARAM2] damage for [PARAM1] seconds<br><br>For spell casting you only need to fill [PARAM1] which will be the spell ID.
<br><br>A monster can for example have 10% chance of inflicting a poison that does 8 damage of poison for 60 seconds&#44; and in addition have a 50% chance of casting a spell. To get this behaviour we start with the percentage&#44; then the featurename&#44; and the parameters.
So our example solution is: 10:BUFF:POISON:60:8;50:SPELL:15
Note that we have split two features using a semicolon&#44; and the feature data using colons.,
A list of additional items that have a chance of being looted when this monster dies<br><b>Use the following format:</b> chance:itemID;chance:itemID;chance:itemID<br>chance is a value between 0 and 100 where 99 means almost certain and 1 means almost no chance
,A particle ID that spawns on the enemy when the NPC melee attacks<br>If you leave this 0 the Sparks animation will be used.<br>Can be one of the following values:<br>
Animation Sword: 211<br>
Animation Spear: 213<br>
Animation Bow: 215<br>
Animation Whip: 217<br>
Animation Staff: 219<br>
Animation Axe: 221<br>
Animation Boomerang: 223<br>
Animation Dagger: 225<br>
Animation Spark: 227<br>";
    $values_3 = "name,class,type,difficulty,spriteset,resistance,features,extraloot,meleeanim";
    $values_4 = "" . $values_3;

	$defaults = ",,,1.0,1,0;0;0;0;0;0;0;0,,,0";


    $table_edit = "rpg_world_npc_types";
} elseif ($action == "quest") {

	$maindesc = "Quests also form an important part of gameplay and come in a few forms. (If you have more ideas for quest mechanics please make a thread in the forum)";

    $values_1 = "Title,
Start text,
Completed text,
Minimum level required,
Requires quest ID,
Requires class ID,
Experience reward multiplier,Item ID reward list,{$rpg_config[name_money]} Reward,Spells ID reward list,Kill requirement,Time limit,Items requirement,Interact requirement,Escort requirement";
    $values_2 = ",
The text that is shown when the player opens up this quest for the first time,
The text that is shown when the player completes this quest,
The minimum level the player <b>should</b> be before they accept this quest. Note that players can accept quests of all levels. If you think this is a bad idea let me know,
A quest ID that needs to be completed before this one can be accepted,
The class ID the player needs to be in order to accept (and view?) this quest. Used to restrict quests on player class.,
The experience reward is auto calculated using the formula: playerLevel * multiplier * {$rpg_config[quest_base_exp]} and can be altered using this multiplier,
A list of item ID's that the player will receive when this quest is completed.<br><b>Must be separated by commas</b>,
The amount of {$rpg_config[name_money]} the players gets when they complete this quest,
A list of spell ID's that the player will receive when this quest is completed.<br><b>Must be separated by commas</b>,
A list of NPCType ID's that the player must kill before this quest can be completed,<b>Not implemented yet</b>,
A list of Item ID's that the player must have before this quest can be completed,
<b>Not implemented yet</b>,
An NPCType ID that needs to be escorted before this quest can be completed";
    $values_3 = "title,text_start,text_complete,requires_level,requires_quest,requires_class,reward_exp_multiplier,reward_items,reward_money,reward_spells,goal_kills,goal_time,goal_items,goal_interact,goal_escort";
    $values_4 = "title,requires_level,requires_quest,requires_class";
	$values_5 = "title,textarea,textarea,requires_level,requires_quest,requires_class,reward_exp_multiplier,reward_items,reward_money,reward_spells,goal_kills,goal_time,goal_items,goal_interact,goal_escort";

    $limitchar = 80;

	$defaults = ",,,1,,,1.0,,,,,,,,";

    $table_edit = "rpg_world_quests";
    $table_crit = "";

} elseif ($action == "gameobjects") {

    $manualid = true;

	$maindesc = "The GameObjects that can placed in-game using the ObjectPlacer.";

    $values_1 = "ID,Name,Type,Param";
    $values_2 = ",Optional,
    What kind of gameobject is this? Enter the appropriate number:<br>
    Billboard: 3 (always faces camera)<br>
    Multiboard : 4 (always faces camera but uses 8 different sprites for rendering)<br>
    Mesh : 5 (3D model)<br>
    Plane : 6 (does not always face camera)<br>,
    The number of the file associated with this object. (See <a href=teamwiki.php?action=viewuploads target=_blank>uploads</a>)"
    ;
    $values_3 = "id,name,type,param";
    $values_4 = $values_3;



    //$values_5 = "imgtilepreview,";

    $limitchar = 80;

    $table_edit = "ib_gameobjects";
    $table_crit = "";
} elseif ($action == "meshes") {

    $manualid = true;

	$maindesc = "The 3D models that can placed in-game using the ModelPlacer.";

    $values_1 = "ID,Name,Filename,Scale Multiplier,Tile 1,Tile 2,Tile 3,Tile 4,Tile 5,Tile 6,Tile 7,Tile 8,Tile 9,Tile 10";
    $values_2 = ",,.OBJ file format,,,,,,,,,,,,,,";

    $values_3 = "id,name,filename,scale,t1,t2,t3,t4,t5,t6,t7,t8,t9,t10";
    $values_4 = $values_3;



    $values_5 = ",,,,imgtilepreview,imgtilepreview,imgtilepreview,imgtilepreview,imgtilepreview,imgtilepreview,imgtilepreview,imgtilepreview,imgtilepreview,imgtilepreview,";

    $limitchar = 80;

    $table_edit = "ib_meshes";
    $table_crit = "";
} elseif ($action == "books") {

    $manualid = true;

        $values_4 = $values_3;





    $values_4 = "name,text";



    $limitchar = 80;

    $table_edit = "ib_books";
    $table_crit = "";
} elseif ($action == "items") {

    $manualid = true;

	$maindesc = "Used as a reference for items in-game. All items have a referenced Item Template.";

    $values_1 = "Name,Image,Type,Sub-Type,Attribute,Delay,Particle";
    $values_2 = ",Image ID used for this item,
Can be any of the following values: weapon armor potion,
Can be any of the following values (depending on the main type of the item):<br>
For weapons: dagger axe sword bow staff<br>
For armor: head body feet<br>,
Used to determine how powerful this item is:<br>
For weapons: damage inflicted<br>
For armor: amount of armor gained<br>,
Time in seconds between attacks/uses for this item,
Particle that is spawned when using/wearing this item (e.g. the projectile used for bows/wands)
";
    $values_3 = "name,image,type,subtype,attr1,delay,particle";
    $values_4 = $values_3;
    //$values_5 = "imgtilepreview,";
    $defaults = ",1,,0,1.0,0";
    $limitchar = 80;

    $table_edit = "ib_item_templates";
    $table_crit = "";
} elseif ($action == "units") {

    $manualid = true;

	$maindesc = "Used as a reference for placed Units in-game. All units have a referenced Unit Template.";

    $values_1 = "Name,Type,Health,Armor,Param,Size";
    $values_2 = ",Use 1 for NPC's,The default amount of hearts an NPC starts with (2 equals one heart),The default amount of armor an NPC starts with (2 equals one armor block),The image ID for NPC's,In-game scale multiplier";
    $values_3 = "name,type,health,armor,skin,hair,head,body,legs";
    $values_4 = $values_3;
    //$values_5 = "imgtilepreview,";
    $defaults = ",1,10,10,0,1.0";
    $limitchar = 80;

    $table_edit = "ib_unit_templates";
    $table_crit = "";
} elseif ($action == "cats") {

    $manualid = true;

	$maindesc = "These are the categories used for the WorldPainter and ObjectPlacer.";

    $values_1 = "Name,Range,Is Used For WorldPainter,Line Limit";
    $values_2 = ",
        Much like you print paper&#44; specify a range of tiles/objects to show. Use commas to customize your range.<br><b>Example:</b> '1-5&#44;7' will select tiles/objects 1 until 5&#44; and 7<br><br>'7&#44;18&#44;27&#44;25-100' will select 7&#44;17&#44;27 and all tiles between 25 and 100.,
        Is this category used for tiles or for gameobjects?<br>1: Used for WorldPainter<br>0: Used for ObjectPlacer,
        The amount of tiles/objects to show per line (useful for exported tilesets)";
    $values_3 = "name,range,terrain_only,limit_x";
    $values_4 = $values_3;
    //$values_5 = "imgtilepreview,";

    $limitchar = 80;

    $table_edit = "ib_editor_cats";
    $table_crit = "";
} else {
    $main = '<h1 align="center">Please select an editor from above.</h1>';
}

if (!empty($action)) {


    $explo_3 = array();
    $explo_2 = array();
    $explo_5 = array();
    $explo_d = array();

    $explo_6 = array();

    // Quickly get all column names from mysql
    $result = mysql_query("SHOW FULL COLUMNS FROM " . $table_edit . "");
    if (!$result) {
        echo 'Could not run query: ' . mysql_error();
        exit;
    }
    if (mysql_num_rows($result) > 0) {
        while ($row = mysql_fetch_assoc($result)) {
            array_push($explo_3, $row["Field"]);





            $comment = explode("|", $row["Comment"]);

            if ( $comment[1] == "imgtexturepreview" ) {
                array_push($explo_d, $row["Default"]);
            }
            else {
                array_push($explo_d, $row["Default"]);
            }


            array_push($explo_2, $comment[0]);

            array_push($explo_5, $comment[1] === NULL ? "" : $comment[1]);

            array_push($explo_6, $comment[2] === NULL ? "" : $comment[2]);
        }
    }

    //$explo_3 = explode(",", $values_3);
    if ( $manualid ) {
        $explo_4 = explode(",", "" . $values_4);
    }
    else {
        $explo_4 = explode(",", "id," . $values_4);
    }

    $sql = "SELECT * FROM " . $table_edit . $table_crit;
    $result = bcs_query($sql) or bcs_error(mysql_error() . $sql);

    $values_added = "<table width=\"100%\" border=\"1\" style=\"border-collapse:collapse;\" cellspacing=\"0\" cellpadding=0>";

    $td_width = ceil(90 / (count($explo_4) - 1));

    $values_added .= "<tr>";
    $maxavc = count($explo_3) > 12 ? 12 : count($explo_3);
    for ($y = 0; $y < $maxavc; $y++) {
        $values_added .= "<td align=\"center\"><b>".(substr($explo_3[$y], 0, $limitchar))."</b></td>";
    }
        $values_added .= "<td align=\"center\"></td>";
    $values_added .= "</tr>";

    for ($x = 0; $x < mysql_num_rows($result); $x++) {
        $row = mysql_fetch_array($result);



        $edit = "<a href=\"editor.php?action=" . $action . "&action2=edit&id_edit=" . $row[id] . "\"><img alt=Edit src=plugins/editor/images/edit.png></a><a href=\"editor.php?action=" . $action . "&action2=del&id_edit=" . $row[id] . "\"><img alt=Delete src=plugins/editor/images/delete.png></a>";


        $values_added .= "<tr>";
        for ($y = 0; $y < $maxavc; $y++) {

            if ( $explo_5[($y-1)] == "imgspritepreview" ) {
                $values_added .= "<td align=\"left\"><span class=\"gensmall\"><img src=plugins/game/images/sprites/" . $row[$explo_3[$y]] . ".png></span></td>";
            }
            else if ( $explo_5[$y] == "imgtilepreview" ) {
                $values_added .= "<td align=\"left\"><span class=\"gensmall\"><img src=plugins/game/images/tiles/" . $row[$explo_3[$y]] . ".png></span></td>";
            }
            else if ( $explo_5[$y] == "imgtexturepreview" ) {
                $temp_preview = $row[$explo_3[$y]];

                $temp_preview = explode("/", $temp_preview);

                //if ( $temp_preview[0] == "tiles" ) $temp_preview[0] = "tiles";
                $temp_preview = implode("/", $temp_preview);

                $values_added .= "<td align=\"left\"><span class=\"gensmall\"><img src=plugins/game/images/" . $temp_preview . ".png></span></td>";
            }
            else {
                $values_added .= "<td align=\"left\"><span class=\"gensmall\">" . $row[$explo_3[$y]] . "</span></td>";
            }

//            if ($y == (count($explo_3) - 1)) {
//                $values_added .= "<td align=\"left\"><span class=\"gensmall\"><b>" . $edit . "</b></span></td>";
//            }
        }
        $values_added .= "<td align=\"left\"><span class=\"gensmall\"><b>" . $edit . "</b></span></td>";
        $values_added .= "</tr>";
    }

    $values_added .= "</table>";

    if ( isset($id_edit) ) {
        $sql = "SELECT * FROM " . $table_edit . $table_crit . " WHERE id = '$id_edit'";
        $result = bcs_query($sql) or bcs_error("<b>SQL ERROR</b> in <br>file " . __FILE__ . " on line " . __LINE__ . "<br><br><b>" . $sql . "</b><br><br>" . mysql_error());
        $table_info = mysql_fetch_array($result);
    }




    if ($go_add || $go_copy) {

        if ( $userdata[pending_editor] ) {
            bcs_die("Sorry, you still need to be approved before you can use the editor.");
        }

        // Create a new ID
        $sql3 = "SELECT id FROM " . $table_edit . " ORDER BY id DESC LIMIT 1";
        $result3 = bcs_query($sql3) or bcs_error("<b>SQL ERROR</b> in <br>file " . __FILE__ . " on line " . __LINE__ . "<br><br><b>" . $query . "</b><br><br>" . mysql_error());
        $row3 = mysql_fetch_array($result3);
        $newid = (int)$row3['id'] + 1; // Simulates auto-increment


        $y = count($explo_3);
        for ($x = 0; $x < $y; $x++) {

            if ( $go_copy && $explo_3[$x] == "id" ) continue;

            //$_POST[$explo_3[$x]] = str_replace("'", "[]", $_POST[$explo_3[$x]]);
			$_POST[$explo_3[$x]] = addslashes($_POST[$explo_3[$x]]);

            if ( empty($_POST[$explo_3[$x]]) ) {
                $_POST[$explo_3[$x]] = $_POST[$explo_3[$x]."_save"];
            }


            $sql_names .= "`" . $explo_3[$x] . "`";
            if ($explo_3[$x] == "id" && empty($_POST[$explo_3[$x]])) {
                 $sql_values .= "'" . $newid . "'";
            } else if ( $explo_5[$x] == "upload" && empty($_POST[$explo_3[$x]."_save"]) ) {

                // Upload the actual file

                $folder = $explo_6[$x];
                $target_path = $explo_6[$x];


                //die(basename( $_FILES['uploadedfile']['name']));

                $fileArray = explode(".", $_FILES[$explo_3[$x]]['name']);

                if ( count($fileArray) > 2 ) bcs_die("Your filename contains too many dots", "back");

                $filename = basename( $_FILES[$explo_3[$x]]['name']);
                $target_path = $target_path . $filename;


                if ( strlen($filename) < 3) {
                    bcs_die("The name of the file you uploaded for '$explo_3[$x]' is too short! ('".$filename."')", "back");
                }

                if ( file_exists($target_path) && $go_add ) {
                    unlink($target_path);
                }

                if ( file_exists($target_path) ) {
                    //bcs_die("Your filename already exists on the server!", "back");
                }
                else {
                    if(move_uploaded_file($_FILES[$explo_3[$x]]['tmp_name'], $target_path)) {
                    //bcs_die("<h1>The file ".  basename( $_FILES[$explo_3[$x]]['name']). " has been uploaded to ".$target_path.".</h1>");
                    } else{
                        bcs_die( "There was an error uploading the file!");
                    }
                }


                if ( strtolower($fileArray[1]) == "obj" ) {


//echo system('C:\\Python27\\python.exe "C:\Users\Nick\Documents\xampp\htdocs\Ironbane\plugins\game\images\meshes\convert_obj_three.py" -i C:\Users\Nick\Documents\xampp\htdocs\Ironbane\plugins\game\images\meshes\1.obj -o C:\Users\Nick\Documents\xampp\htdocs\Ironbane\plugins\game\images\meshes\1.js 2>&1', $output);
                    if ( $running_local ) {
                        $command = 'C:\\Python27\\python.exe "C:\Users\Nick\Documents\xampp\htdocs\Ironbane\plugins\game\images\meshes\convert_obj_three.py" -i C:\Users\Nick\Documents\xampp\htdocs\Ironbane\plugins\game\images\meshes\\'.$filename.' -o C:\Users\Nick\Documents\xampp\htdocs\Ironbane\plugins\game\images\meshes\\'.($fileArray[0]).'.js 2>&1';
                    }
                    else {
                        $command = '/usr/bin/python /var/www/vhosts/nickjanssen.com/ironbane/plugins/game/images/meshes/convert_obj_three.py -i /var/www/vhosts/nickjanssen.com/ironbane/plugins/game/images/meshes/'.$filename.' -o /var/www/vhosts/nickjanssen.com/ironbane/plugins/game/images/meshes/'.($fileArray[0]).'.js 2>&1';
                    }

                    if ( !system($command, $return) ) {
                        die("error running command: ".$command);
                    }

                    //bcs_die($return);

                }


                $sql_values .= "'" . $_FILES[$explo_3[$x]]['name'] . "'";


            } else {
                $sql_values .= "'" . $_POST[$explo_3[$x]] . "'";
            }

            if ($x != ($y - 1)) {
                $sql_names .= ", ";
                $sql_values .= ", ";
            }
        }

        AddTeamActionSelf("Editor add ".ucfirst($action)."", "editor{$action}", "(" . $sql_values . ")");


        if ( $manualid ) {
            $sql = "INSERT INTO " . $table_edit . " (" . $sql_names . ") VALUES(" . $sql_values . ")";
        }
        else {
            $sql = "INSERT INTO " . $table_edit . " (id," . $sql_names . ") VALUES($newid," . $sql_values . ")";
        }

        $result = bcs_query($sql) or bcs_error(mysql_error() . $sql);

        //bcs_die($return, "none");
        header("Location: editor.php?action=" . $action);
    }
    if ($go_del) {
        if (!is_numeric($id_edit)) {
            die();
        }

        if ( $userdata[pending_editor] ) {
            bcs_die("Sorry, you still need to be approved before you can use the editor.");
        }

        $y = count($explo_3);
        for ($x = 0; $x < $y; $x++) {
            if ( $explo_5[$x] == "upload" ) {


                $target_path = $explo_6[$x];



                $fileArray = explode(".", $_FILES[$explo_3[$x]]['name']);

                if ( count($fileArray) > 2 ) bcs_die("Your filename contains too many dots", "back");

                $filename = $table_info[$explo_3[$x]];
                $target_path = $target_path . $filename;

//                if ( file_exists($target_path) ) {
//                    unlink($target_path);
//                    //bcs_die("Your filename already exists on the server!", "back");
//                }


            }

        }

        AddTeamActionSelf("Editor delete ".ucfirst($action)."", "editor{$action}", "");

        $sql = "DELETE FROM " . $table_edit . " WHERE id = '$id_edit'";
        $result = bcs_query($sql) or bcs_error(mysql_error() . $sql);

        header("Location: editor.php?action=" . $action);
    } elseif ($go_edit) {

        if ( $userdata[pending_editor] ) {
            bcs_die("Sorry, you still need to be approved before you can use the editor.");
        }

        $y = count($explo_3);
        for ($x = 0; $x < $y; $x++) {



            if ( $explo_5[$x] == "upload" ) {

                // Upload the actual file

                $target_path = $explo_6[$x];


                //die(basename( $_FILES['uploadedfile']['name']));

                if ( empty($_FILES[$explo_3[$x]]['name']) ) continue;


                $fileArray = explode(".", $_FILES[$explo_3[$x]]['name']);

                if ( count($fileArray) > 2 ) bcs_die("Your filename contains too many dots", "back");

                $filename = basename( $_FILES[$explo_3[$x]]['name']);
                $target_path = $target_path . $filename;

                if ( strlen($filename) < 3) {
                    bcs_die("The name of the file you uploaded for '$explo_3[$x]' is too short! ('".$filename."')", "back");
                }

                if ( file_exists($target_path) ) {
                    unlink($target_path);
                    //bcs_die("Your filename already exists on the server!", "back");
                }

                if(move_uploaded_file($_FILES[$explo_3[$x]]['tmp_name'], $target_path)) {
                   //bcs_die("<h1>The file ".  basename( $_FILES[$explo_3[$x]]['name']). " has been uploaded to ".$target_path.".</h1>");
                } else{
                    bcs_die( "There was an error uploading the file!");
                }


                if ( strtolower($fileArray[1]) == "obj" ) {


//echo system('C:\\Python27\\python.exe "C:\Users\Nick\Documents\xampp\htdocs\Ironbane\plugins\game\images\meshes\convert_obj_three.py" -i C:\Users\Nick\Documents\xampp\htdocs\Ironbane\plugins\game\images\meshes\1.obj -o C:\Users\Nick\Documents\xampp\htdocs\Ironbane\plugins\game\images\meshes\1.js 2>&1', $output);
                    if ( $running_local ) {
                        $command = 'C:\\Python27\\python.exe "C:\Users\Nick\Documents\xampp\htdocs\Ironbane\plugins\game\images\meshes\convert_obj_three.py" -i C:\Users\Nick\Documents\xampp\htdocs\Ironbane\plugins\game\images\meshes\\'.$filename.' -o C:\Users\Nick\Documents\xampp\htdocs\Ironbane\plugins\game\images\meshes\\'.($fileArray[0]).'.js 2>&1';
                    }
                    else {
                        $command = '/usr/bin/python /var/www/vhosts/nickjanssen.com/ironbane/plugins/game/images/meshes/convert_obj_three.py -i /var/www/vhosts/nickjanssen.com/ironbane/plugins/game/images/meshes/'.$filename.' -o /var/www/vhosts/nickjanssen.com/ironbane/plugins/game/images/meshes/'.($fileArray[0]).'.js 2>&1';
                    }

                    if ( !system($command, $return) ) {
                        die("error running command: ".$command);
                    }

                    //bcs_die($return);

                }

                // Delete the old file

//                $filename_old = $table_info[$explo_3[$x]];
//                $target_path_old = $explo_6[$x] . $filename_old;
//;
//
//                if ( file_exists($target_path_old) ) {
//                    unlink($target_path_old);
//                    $fileArrayOld = explode(".", $filename_old);
//                    if ( strtolower($fileArrayOld[1]) == "obj" ) {
//                        unlink($explo_6[$x] . $fileArrayOld[0] . ".js");
//                    }
//                    //bcs_die("Your filename already exists on the server!", "back");
//                }


                $_POST[$explo_3[$x]] = $filename;


            }


            $_POST[$explo_3[$x]] = addslashes($_POST[$explo_3[$x]]);

            //$_POST[$explo_3[$x]] = str_replace("'", "[]", $_POST[$explo_3[$x]]);
            $sql_values .= "`" . $explo_3[$x] . "` = '" . $_POST[$explo_3[$x]] . "'";
            if ($x != ($y - 1)) {
                $sql_values .= ", ";
            }
        }
        AddTeamActionSelf("Editor edit ".ucfirst($action)."", "editor{$action}", "$sql_values");

        $sql = "UPDATE " . $table_edit . " SET " . $sql_values . " WHERE id = '$id_edit'";
        $result = bcs_query($sql) or bcs_error(mysql_error() . $sql);

        if ( $manualid && $explo_3[0] == "id" ) $id_edit = $_POST[$explo_3[0]];

        header("Location: editor.php?action=" . $action."&action2=edit&done=1&id_edit=".$id_edit);
    } elseif ($action2 == 'del') {
        if (!is_numeric($id_edit)) {
            die();
        }


        bcs_die('Are you sure you wish to delete this value ?<br /><form action="editor.php?action=' . $action . '&action2=del&id_edit=' . $id_edit . '" method="POST"><input type="submit" name="go_del" value="DELETE" class=mainoption></form>', 'none');
    } else {
        //} elseif ($action2 == 'edit') {
        if (!is_numeric($id_edit) && $action2 == 'edit') {
            die();
        }

        if ($action2 == "edit") {
            $row = $table_info;
        } else {
            unset($row);
        }




        for ($x = 0; $x < count($explo_3); $x++) {

			unset($path);

			switch ($explo_5[$x]) {
				case "textarea":
					$editfield = '<textarea name="' . $explo_3[$x] . '" style="width:100%;height:200px">'.($action2=="edit"?$row[$explo_3[$x]]:$explo_d[$x]).'</textarea>';
					break;

				// case "small":
					// $editfield = '<input type="text" style="width:50px" name="' . $explo_3[$x] . '" value="'.($action2=="edit"?$row[$explo_3[$x]]:$explo_d[$x]).'"  />';
					// break;
				// case "med":
				case "upload":
					$editfield = '<input type="file" name="' . $explo_3[$x] . '" style="width:100%;">'.($action2=="edit"?'<br><span class="gensmall">Currently uploaded: <b>'.$row[$explo_3[$x]].'</b><input type="hidden" name="' . $explo_3[$x] . '_save" value="'.$row[$explo_3[$x]].'"><br>Selecting a new file will replace the old one.</span>':$explo_d[$x]).'';
					break;

				// case "small":
					// $editfield = '<input type="text" style="width:50px" name="' . $explo_3[$x] . '" value="'.($action2=="edit"?$row[$explo_3[$x]]:$explo_d[$x]).'"  />';
					// break;
				// case "med":
				case "imgtexturepreview":
					$path = "plugins/game/images/";

                                        $temp_preview = ($action2=="edit"?$row[$explo_3[$x]]:$explo_d[$x]);

                                        $temp_preview = explode("/", $temp_preview);

                                        if ( $temp_preview[0] == "tiles" ) $temp_preview[0] = "tiles";
                                        $temp_preview = implode("/", $temp_preview);


					$editfield = '<input type="text" style="width:100px" name="' . $explo_3[$x] . '" id="' . $explo_3[$x] . '" value="'.($action2=="edit"?$row[$explo_3[$x]]:$explo_d[$x]).'"  /><img src="'.$path.$temp_preview.'.png" id="imgpreview_' . $explo_3[$x] . '"><img id="up_' . $explo_3[$x] . '" src="plugins/game/images/misc/up.png"><img id="down_' . $explo_3[$x] . '" src="plugins/game/images/misc/down.png">';

					$c_jquery .= '
						$("#' . $explo_3[$x] . '").keyup(function(){
							var val = $("#' . $explo_3[$x] . '").attr("value");

                                                        val = val.split("/");

                                                        if ( val[0] == "tiles" ) val[0] = "tiles";
                                                        val = val.join("/");

								$("#imgpreview_' . $explo_3[$x] . '").attr("src", "'.$path.'"+val+".png");

						});
						$("#up_' . $explo_3[$x] . '").click(function(){
                                                        var val = ($("#' . $explo_3[$x] . '").attr("value")).split("/");
							var imgid = parseInt(val[(val.length-1)]);

							val[(val.length-1)] = parseInt(val[(val.length-1)]) + 1;

                                                        val = val.join("/");

							$("#' . $explo_3[$x] . '").attr("value", val);

                                                        val = val.split("/");



                                                        if ( val[0] == "tiles" ) val[0] = "tiles";
                                                        val = val.join("/");


							if ( imgid > 0 ){
								$("#imgpreview_' . $explo_3[$x] . '").attr("src", "'.$path.'"+val+".png");
							}
						});
						$("#down_' . $explo_3[$x] . '").click(function(){
                                                        var val = ($("#' . $explo_3[$x] . '").attr("value")).split("/");
							var imgid = parseInt(val[(val.length-1)]);

							val[(val.length-1)] = parseInt(val[(val.length-1)]) - 1;

                                                        val = val.join("/");

							$("#' . $explo_3[$x] . '").attr("value", val);

                                                        val = val.split("/");
                                                        if ( val[0] == "tiles" ) val[0] = "tiles";
                                                        val = val.join("/");


							if ( imgid > 0 ){
								$("#imgpreview_' . $explo_3[$x] . '").attr("src", "'.$path.'"+val+".png");
							}
						});
					';

					break;
				case "imgtilepreview":
					if ( !isset($path) ) $path = "plugins/game/images/tiles/";
				case "imgspritepreview":
					if ( !isset($path) ) $path = "plugins/game/images/sprites/";
				case "imgspellpreview":
					if ( !isset($path) ) $path = "plugins/game/images/spells/";
                case "imgskinpreview":
                    if ( !isset($path) ) $path = "plugins/game/images/characters/base/skin/";
                case "imgeyespreview":
                    if ( !isset($path) ) $path = "plugins/game/images/characters/base/eyes/";
                case "imghairpreview":
                    if ( !isset($path) ) $path = "plugins/game/images/characters/base/hair/";
                case "imglegspreview":
                    if ( !isset($path) ) $path = "plugins/game/images/characters/base/legs/";
                case "imgbodypreview":
                    if ( !isset($path) ) $path = "plugins/game/images/characters/base/body/";
                case "imgheadpreview":
                    if ( !isset($path) ) $path = "plugins/game/images/characters/base/head/";
				case "imgitempreview":
					if ( !isset($path) ) $path = "plugins/game/images/items/";
					$editfield = '<input type="text" style="width:100px" name="' . $explo_3[$x] . '" id="' . $explo_3[$x] . '" value="'.($action2=="edit"?$row[$explo_3[$x]]:$explo_d[$x]).'"  /><img src="'.$path.''.($action2=="edit"?$row[$explo_3[$x]]:$explo_d[$x]).'.png" id="imgpreview_' . $explo_3[$x] . '"><img id="up_' . $explo_3[$x] . '" src="plugins/game/images/misc/up.png"><img id="down_' . $explo_3[$x] . '" src="plugins/game/images/misc/down.png">';

					$c_jquery .= '
						$("#' . $explo_3[$x] . '").keyup(function(){
							var imgid = parseInt($("#' . $explo_3[$x] . '").attr("value"));
							if ( imgid > 0 ){
								$("#imgpreview_' . $explo_3[$x] . '").attr("src", "'.$path.'"+imgid+".png");
							}
						});
						$("#up_' . $explo_3[$x] . '").click(function(){
							var imgid = parseInt($("#' . $explo_3[$x] . '").attr("value"));
							imgid++;
							$("#' . $explo_3[$x] . '").attr("value", imgid);
							if ( imgid > 0 ){
								$("#imgpreview_' . $explo_3[$x] . '").attr("src", "'.$path.'"+imgid+".png");
							}
						});
						$("#down_' . $explo_3[$x] . '").click(function(){
							var imgid = parseInt($("#' . $explo_3[$x] . '").attr("value"));
							imgid--;
							$("#' . $explo_3[$x] . '").attr("value", imgid);
							if ( imgid > 0 ){
								$("#imgpreview_' . $explo_3[$x] . '").attr("src", "'.$path.'"+imgid+".png");
							}
						});
					';

					break;

				default:
					$editfield = '<input type="text" style="width:100%" name="' . $explo_3[$x] . '" value="'.($action2=="edit"?$row[$explo_3[$x]]:$explo_d[$x]).'"  />';
					break;
			}
			// if ( strpos("[BIGINFO]", $explo_2[$x]) ) {
				// $explo_2[$x] = str_replace("[BIGINFO]", "", $explo_2[$x]);
			// }
            $edit_values .= '<tr>
    <td align="right" width="200"  valign="top" rowspan=2><b>' . ($explo_3[$x]) . '</b></td>
    <td width="100%" valign="top">'.$editfield.'</td>
 </tr>
 <tr>
    <td width="100%">'.$explo_2[$x].'</td>
 </tr>
  <tr>
    <td width="100%" colspan="2" height=20>&nbsp;</td>
 </tr>';

            // $edit_values .= '<tr>
    // <td align="right" width="25%"><span class="gen"><b>' . $explo_1[$x] . '</b></span><br /><span class="gensmall">' . $explo_2[$x] . '</span></td>
    // <td width="75%" valign="top">' . ($explo_3[$x] != "description" ? '<input type="text" style="width:100%" name="' . $explo_3[$x] . '" value="' . $row[$explo_3[$x]] . '"  />' : '<textarea name="' . $explo_3[$x] . '" style="width:100%;height:200px">' . $row[$explo_3[$x]] . '</textarea>') . '</td>
 // </tr>';
        }

        if ($action2 == "edit") {
            $extrabutton = "<input type=submit  class=mainoption value=\"Add Copy\" name=\"go_copy\">";

			// Make a preview item/spell

			if ( $action == "item" ) {
				$preview = makeitemlist(array($id_edit));
			}
			if ( $action == "spell" ) {
				$preview = makespelllist(array($id_edit));
			}
			if ( $action == "shop" ) {
				$preview = makeitemlist(explode(",", $row[$explo_3[3]]));
			}
			if ( $action == "trainer" ) {
				$preview = makespelllist(explode(",", $row[$explo_3[3]]));
			}
                        if ( $action == "quest" ) {
                                $preview = '<iframe frameBorder="0" src="character.php?action=quest&preview=1&quest='.$id_edit.'" style="width:700px;height:520px"></iframe>';
                        }
			if ( $preview ) $preview = "<b>Preview:</b><br>".$preview."<hr>";
        }

		if ( $done == 1 ) {
			$donemsg = "<h1 style=color:green>Edit successful!</h1><br><br>";
		}

		if ( $maindesc ) $maindesc = "<b>".$maindesc."</b><hr>";

        $main = "<center><br /><br /><h1>".ucfirst($action)." Editor</h1>".$maindesc."".$donemsg."" . ($action2 == "edit" ? "<div style=\"width:100%;height:100px;border:2px dashed red;background-color:#341616\"><h1 style=\"color:red\">Warning&nbsp;</h1><b>You are in editing mode! " . createLink("Back", "editor.php?action=" . $action) . "</b></div><br><br>" : "") . "<form action=\"editor.php?action=" . $action . "\" enctype=\"multipart/form-data\" method=post><br>
		".$preview."

<table cellpadding=\"2\" cellspacing=\"5\">
" . $edit_values . "
</table><br /><br /><input type=hidden name=\"id_edit\" value=\"" . $row[id] . "\"><input type=submit  class=mainoption value=\"" . ($action2 == "edit" ? "Edit" : "Add") . "\" name=\"" . ($action2 == "edit" ? "go_edit" : "go_add") . "\">" . $extrabutton . "</form><br /><br /><h2>Currently these values are in the table " . $table_edit . ":</h2><hr>" . $values_added;
    }
}




$links_x1 = explode(',', $links_n);
$links_x2 = explode(',', $links_u);



for ($x = 0; $x < count($links_x1); $x++) {
    $editorcats .= '<td width="' . floor(100 / count($links_x1)) . '%">
                          <table width="100%" cellpadding="2" cellspacing="1" border="0" style="border-collapse:collapse">
                           <tr>
                                <td class="' . ($action != $links_x2[$x] ? 'row2' : 'row1') . '" align="center"><span class="gen">' . ($action == $links_x2[$x] ? "<b>$links_x1[$x]</b>" : createLink($links_x1[$x], "editor.php?action=" . $links_x2[$x])) . '</span>
                                </td>
                           </tr>
                          </table>
                         </td>
                         ';
}




$c_main = '


<h1>Editor</h1>
<table width="784" cellspacing="0" cellpadding="5" border="0" align="center">
  <tr>

	<td valign="top" width="100%">
        <hr>
	  <table width="100%"  cellspacing="0" cellpadding="0">
	   <tr>
                ' . $editorcats . '
           </tr>
          </table>
          <hr>
	  <table width="100%" cellpadding="2" cellspacing="1" border="0" class="forumline">
	   <tr>
		<td class="row1" align="left"><span class="gen">

            ' . $main . '

                </span></td>
           </tr>
          </table>
        </td>
 </tr>
 </table>




    ';
?>
