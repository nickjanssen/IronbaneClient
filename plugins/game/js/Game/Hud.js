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


var props = ['skin', 'eyes', 'hair', 'feet', 'body', 'head', 'big'];


var selectedMale = true;
var selectedSkin = 1;
var selectedEyes = 1;
var selectedHair = 1;


var messageFadeTime = 0.2;

var BigMessage = Class.extend({
  Init: function(message, duration) {
    this.message = message;
    this.duration = duration;
    this.timeLeft = duration;
    this.opacity = 1;
  },
  Tick: function(dTime) {
    this.timeLeft -= dTime;

    var opac = 0;

    if ( this.timeLeft > this.duration-messageFadeTime ) {
      //      4.7 - 4.5 = 0.2
      opac = 1-((this.timeLeft - (this.duration-messageFadeTime))/messageFadeTime);
    }
    else if ( this.timeLeft < messageFadeTime ) {
      opac = this.timeLeft/messageFadeTime;
    }
    else {
      opac = 1;
    }

    this.opacity = opac;
  }
});

var HUDHandler = Class.extend({
  Init: function() {

    this.currentBookPage = 0;


    this.bigMessages = [];

    if ( stealth ) {
      $('#logo').css('background', 'none');
    }

    this.alertBoxActive = false;

    this.chatBuffer = "";

    this.allowSound = ISDEF(localStorage.allowSound) ? (localStorage.allowSound === 'true') : true;

    if ( Detector.webgl ) {
      if ( socketHandler.serverOnline ) {
        $('#loginContent').show();
      }
      else {
        setTimeout(function(){
          hudHandler.MessageAlert('The server is currently offline. Please try again later.', 'nobutton');
        }, 1000);
      }

      if ( stealth ) {
        $('#chatBox').hide();
        $('#chatInput').hide();
        $('#chatContent').hide();
      }




    }
    else {

      ironbane.showingGame = true;

      $("#gameFrame").css('opacity', '');
      // $("#loadingBar").hide();


      $('#noWebGL').show();

//      $('#devNews').hide();
//      $('#sideMenu').hide();
      //<button onclick="window.open('http://www.khronos.org/webgl/wiki_1_15/index.php/Getting_a_WebGL_Implementation','bs')" class="ibutton" style="width:304px">More information</button>

      $('#hquote').html('<h2>'+hquote+'</h2>');
      // Add a solution
      if ( startdata.using_ie ) {
        $('#webglsolution').html('You seem to be using <b>Internet Explorer</b>.<br>Don\'t worry, there is a solution for you!<div class="spacer"></div><button id="getiewebgl" class="ibutton" style="width:304px">Get IEWebGL</button>');

        $('#getiewebgl').click(function(){
          window.open('http://iewebgl.com/','iewebgl');
        //window.open('http://www.khronos.org/webgl/wiki_1_15/index.php/Getting_a_WebGL_Implementation','bs')
        });
      }
      else if ( startdata.using_safari ) {

        var sol = '';

        sol += 'In Safari, open the <b>Safari menu</b> and select <b>Preferences</b>.<br><br>';
        sol += 'Then, click the <b>Advanced</b> tab in the Preferences window.<br><br>';
        sol += 'Then, at the bottom of the window, check the <b>Show Develop menu in menu bar</b> checkbox.<br><br>';
        sol += 'Then, open the <b>Develop</b> menu in the menu bar and select <b>Enable WebGL</b>.';

        $('#webglsolution').html('You seem to be using <b>Safari</b>.<br>Don\'t worry, there is a solution for you!<div class="spacersmall"></div><div class="insideInfo" style="width:280px;">'+sol+'</div>');

        $('#getiewebgl').click(function(){
          window.open('http://iewebgl.com/','iewebgl');
        //window.open('http://www.khronos.org/webgl/wiki_1_15/index.php/Getting_a_WebGL_Implementation','bs')
        });
      }
      else {

        $('#webglsolution').html('<h2>What you can do</h2><div class="spacersmall"></div>Please make sure your <b>browser is up-to-date</b>.<br>Also check if you have the <b>latest video card drivers</b> installed.<div class="spacersmall"></div><button id="moreinfowebgl" class="ibutton" style="width:304px">More information</button>');

        $('#moreinfowebgl').click(function(){
          //window.open('http://iewebgl.com/','iewebgl');
          window.open('http://www.khronos.org/webgl/wiki_1_15/index.php/Getting_a_WebGL_Implementation','bs');
        });
      }

      $('#webglsolution').append('<div class="spacersmall"></div>If you think this error is false or you are sure your system is able to run WebGL, please contact <a href="mailto:support@ironbane.com">support@ironbane.com</a> with your computer and browser specifications.');
    }


    this.oldButtonClasses = {};

    this.devNewsScrollPane = $('#devNews').jScrollPane().data('jsp');
    this.chatContentScroller = $('#chatContent').jScrollPane({
      // showArrows: true,
      // arrowScrollOnHover: true
      animateScroll: true
    }).data('jsp');



    setTimeout(function(){
      hudHandler.MakeSlotSpace(false);
      hudHandler.MakeSlotSpace(true);

      $('#gameFrame').droppable({
        drop: hudHandler.ItemSwitchEvent
      });



      hudHandler.MakeNewsPage();
      hudHandler.ShowMainMenuHUD();




    },0);

    var clickAction = function () {
      soundHandler.Play("click");
    };

    var handleClick = function(noSound) {
      $("button").unbind('click.sound');

      if ( !noSound ) clickAction();

      setTimeout(function(){
        $("button").bind('click.sound', function(){
          handleClick();
        });
      }, 50);
    };


    setTimeout(function(){
      handleClick(true);
    }, 1000);



  //        setTimeout(function(){
  //            hudHandler.MakeSlotSpace(false);
  //            hudHandler.MakeSlotSpace(true);
  //
  //
  //        },5000);

  //this.MakeSlotSpace(false);

  },
  MakeSoundButton: function() {
          var checkSoundToggle = function(value) {

        // We can only change the toggle in the main menu, so always stop/play
        // the theme music
          hudHandler.allowSound = value;


          if ( value ) {
            $("#btnToggleSound").html("&#9834;");
            $("#btnToggleSound").css("color", "");

            soundHandler.Play("theme");
          }
          else {
            $("#btnToggleSound").html("<del>&#9834;</del>");
            $("#btnToggleSound").css("color", "red");

            soundHandler.StopAll();
          }

          localStorage.allowSound = value;
      };
      checkSoundToggle(hudHandler.allowSound);
      $("#btnToggleSound").unbind('click');
      $("#btnToggleSound").click(function(){
        checkSoundToggle(!hudHandler.allowSound);
      });
    },
  ShowMainMenuHUD: function() {
    $("#versionNumber, #devNews, #logo, #loadingBar").show();
  },
  MakeNewsPage: function() {
      // Fill the side menu

      var content = '';

      content += '<div>';
      content += '<b>';

      content += '<a href="forum.php" target="_new">Forum</a>';
      content += ' | ';
      content += '<a href="https://twitter.com/IronbaneMMO" target="_new">Twitter</a>';
      content += ' | ';
      content += '<a href="forum.php?action=topic&topic=353" target="_new">Credits</a>';


//      content += ' | ';
//      content += '<a href="https://www.youtube.com/user/IronbaneMMO" target="_new">Youtube</a>';


      if ( isEditor ) {
        content += '<br>';
        content += '<a href="editor.php" target="_new">Editor</a>';
        content += ' | ';
        content += '<a href="teamwiki.php?action=viewuploads" target="_new">Uploads</a>';
      }


      content += '</b>';

//      content += '<button id="btnForum" class="ibutton" style="width:100px">Forum</button>';
//      content += '<button id="btnTwitter" class="ibutton" style="width:100px">Twitter</button>';
//      content += '<hr>';

      content += '<h2>News</h2>';

      content += newsPosts;

      content += '</div>';

      if ( isEditor ) {

      }

//      $('#devNews').html(content);

    this.devNewsScrollPane.getContentPane().html(content);
    this.devNewsScrollPane.reinitialise();


//      $('#sideMenu').jScrollPane();

//      $('#btnForum').click(function(){
//        window.open('forum.php', 'Forum');
//        //window.location = 'forum.php';
//      });
//
//      $('#btnTwitter').click(function(){
//        window.open('https://twitter.com/IronbaneMMO', 'Twitter');
//        //window.location = 'https://twitter.com/IronbaneMMO';
//      });
  },
  MakeSlotSpace: function(isLoot) {


    var div = isLoot ? '#lootBag' : '#itemBar';

    var spaces = isLoot ? 10 : 10;



    $(div).empty();

    for(var x=0;x<spaces;x++){
      var name = isLoot ? 'ls'+x : 'is'+x;

      $('#'+name).remove();

      var classname = isLoot ? 'lootBarSlot' : 'itemBarSlot';
      $(div).append('<div id="'+name+'" class="'+classname+'"></div>');
      var n = x + 1;
      if ( n == 10 ) n = 0;
      //n = 9;
      if ( !isLoot ) {
        $('#'+name).append('<div id="'+name+'_equip" class="unequipped"></div>');
        $('#'+name).append('<div style="width:48px;height:48px;background-image:url(plugins/game/images/misc/key_'+n+'.png);position:absolute"></div>');
      }
      //if ( !isLoot ) $("#"+name).append('<div style="margin-top:18px;margin-left:5px;width:40px;height:40px;color:white;font-size:10px">'+n+'</div>');
      $('#'+name).droppable({
        drop: hudHandler.ItemSwitchEvent,
        greedy: true
      });
    }

  },
  UpdateEquippedItems: function() {
    for(var x=0;x<10;x++){
      var item = hudHandler.FindItemBySlot(x, false);

      if( item ) {

        var template = items[item.template];


        if ( item.equipped ) {
          if ( template.type == "consumable" ) {
            this.SetUsed(x);
          }
          else {
            this.SetEquipped(x);
          }
        }
        else {
          this.SetUnequipped(x);
        }
      }
      else {
        this.SetUnoccupied(x);
      }
    }
  },
  SetEquipped: function(slot) {
    var name = 'is'+slot;
    $('#'+name+'_equip').attr('class', 'equipped');
  },
  SetUsed: function(slot) {
    var name = 'is'+slot;
    $('#'+name+'_equip').attr('class', 'used');
  },
  SetUnequipped: function(slot) {
    var name = 'is'+slot;
    $('#'+name+'_equip').attr('class', 'unequipped');
  },
  SetUnoccupied: function(slot) {
    var name = 'is'+slot;
    $('#'+name+'_equip').attr('class', 'unoccupied');
  },
  ItemSwitchEvent: function(event, ui ) {
    var draggable = ui.draggable;
    //alert( 'The square with ID '' + draggable.attr('id') + '' was dropped onto '' + $(this).attr('id') + ''!' );

    // Are we dragging an inventory item?
    var itemID = draggable.attr('id');
    var itemPrefix = draggable.attr('id').substr(0, 2);
    var itemNumber = parseInt(draggable.attr('id').substr(2));
    var slotID = $(this).attr('id');
    var slotPrefix = $(this).attr('id').substr(0, 2);
    var slotNumber = parseInt($(this).attr('id').substr(2));

    if ( hudHandler.alertBoxActive ) {
      var startItem = hudHandler.FindItemByID(itemNumber, false);
      TeleportElement(itemID, 'is'+startItem.slot);
      return;
    }

    if ( itemPrefix == 'ii') {
      var startItem = hudHandler.FindItemByID(itemNumber, false);
      if ( slotPrefix == 'is' ) {
        // Inventory to inventory
        // First check if the target slot is taken, and switch it first


        hudHandler.SwitchItem(slotNumber, startItem, itemID, slotID, false);

      }
      else if ( slotPrefix == 'ls' ) {
        // Inventory to loot
        var switchItem = hudHandler.FindItemBySlot(slotNumber, true);

        var data = null;

        //                // It's the same as a normal lootItem, but inverted
        //                if ( switchItem ) {
        //                    data = {
        //                        "npcID":ironbane.player.lootUnit.id,
        //                        "switchID":startItem.id,
        //                        "slotNumber":slotNumber,
        //                        "itemID":switchItem.id
        //                    }
        //                }
        //                else {
        //                    data = {
        //                        "npcID":ironbane.player.lootUnit.id,
        //                        "slotNumber":slotNumber,
        //                        "itemID":startItem.id
        //                    }
        //                }

        if ( switchItem ) {

          TeleportElement(itemID, 'is'+startItem.slot);

          hudHandler.LootItem(startItem, switchItem, startItem.slot, 'is'+startItem.slot);

        }
        else {
          hudHandler.PutItem(startItem, slotNumber, slotID);
        }

      }
      else if ( !ironbane.player.canLoot && slotID == 'gameFrame' ) {
        // Send a request
        hudHandler.DropItem(startItem, itemID, itemNumber);
      }
      else {
        // Revert
        TeleportElement(itemID, 'is'+startItem.slot);
      }
    }
    else if ( itemPrefix == 'li') {
      var startItem = hudHandler.FindItemByID(itemNumber, true);

      if ( slotPrefix == 'ls' ) {
        // loot to loot
        // First check if the target slot is taken, and switch it first

        hudHandler.SwitchItem(slotNumber, startItem, itemID, slotID, true);
      }
      else if ( slotPrefix == 'is' ) {
        // Loot to inventory
        // Delete the item from the loot array, and add it to the player items
        // If there is an item present at the slot, switch it

        var switchItem = hudHandler.FindItemBySlot(slotNumber, false);

        hudHandler.LootItem(switchItem, startItem, slotNumber, slotID);
      }
      else {
        // Revert
        TeleportElement(itemID, 'ls'+startItem.slot);
      }
    }

  //ui.draggable.position( { of: $(this), my: 'left top', at: 'left top' } );
  //ui.draggable.draggable( 'option', 'revert', false );
  },
  PutItem: function(startItem, slotNumber, slotID, acceptOffer) {

    // We put something from the inventory to the loot

    var data = {
      "npcID":ironbane.player.lootUnit.id,
      "slotNumber":slotNumber,
      "itemID":startItem.id
    }
    if ( ISDEF(acceptOffer) ) data['acceptOffer'] = true;
    socketHandler.socket.emit('putItem', data, function(reply) {

      if ( ISDEF(reply.errmsg) ) {
        hudHandler.MessageAlert(reply.errmsg);

        // Teleport back!
        TeleportElement('ii'+startItem.id, 'is'+startItem.slot);
        //if ( switchItem ) TeleportElement('li'+switchItem.id, 'ls'+switchItem.slot);
        return;
      }

      if ( ISDEF(reply.offeredPrice) ) {

        var goldPieces = hudHandler.GetStatContent(reply.offeredPrice, 'misc/coin_medium', reply.offeredPrice, true, true);

        var doReturn = false;

        hudHandler.MessageAlert("I'd offer "+goldPieces+" for yer "+items[startItem.template].name+". What do ye think?", 'question',
          function() {
            hudHandler.PutItem(startItem, slotNumber, slotID, true)
          },
          function() {                            // Teleport back!
            TeleportElement('ii'+startItem.id, 'is'+startItem.slot);
            ironbane.unitList.push(new ChatBubble(ironbane.player.lootUnit, "Then take yer stuff with ye!"));
          });


        return;
      }

      if ( ISDEF(reply.newCoins) ) {
        ironbane.player.coins = reply.newCoins;
        hudHandler.MakeCoinBar(true);

        // Remove the loot bag
        $('#lootBag').hide();

        // Todo: remove the items via UI
        for(var i=0;i<ironbane.player.lootItems.length;i++){
          var lootItem = ironbane.player.lootItems[i];

          $('#li'+lootItem.id).remove();

          if ( currentHoverDiv == 'li'+lootItem.id ) $('#tooltip').hide();
        }

        ironbane.player.lootItems = [];
        ironbane.player.canLoot = false;
        ironbane.player.lootUnit = null;
      }

      // Delete from playerData
      socketHandler.playerData.items = _.without(socketHandler.playerData.items, startItem);

      //            // Remove the pricetag if present
      //            if ( ISDEF(startItem.price) ) {
      //                delete startItem.price;
      //            }

      ironbane.player.lootItems.push(startItem);

      // If it was armor, update our appearance
      if ( startItem.equipped ) {
        if ( items[startItem.template].type == 'armor' ) {
          ironbane.player.UpdateAppearance();
        }
        if ( items[startItem.template].type == 'weapon' ) {
          ironbane.player.UpdateWeapon(0);
        }
      }

      startItem.equipped = 0;

      startItem.slot = slotNumber;

      // Adjust the DOM element's name from ii to li
      $('#ii'+startItem.id).attr('id', 'li'+startItem.id);

      // Do the UI actions
      TeleportElement('li'+startItem.id, slotID);

      hudHandler.UpdateEquippedItems();

      soundHandler.Play(ChooseRandom(["bag1"]));

    })
  },
  DropItem: function(startItem, itemID, itemNumber){
    socketHandler.socket.emit('dropItem', {
      'itemID': itemNumber
    }, function(reply) {

      if ( ISDEF(reply.errmsg) ) {
        hudHandler.MessageAlert(reply.errmsg);

        // Teleport back!
        TeleportElement('li'+startItem.id, 'ls'+startItem.slot);

        return;
      }

      // Remove the DOM, remove from item array and send a request to the server to drop the item on the ground
      //alert('remove');
      $('#'+itemID).remove();

      // Hide the tooltip
      $('#tooltip').hide();

      for (var i = 0; i < socketHandler.playerData.items.length; ++i) {
        if (socketHandler.playerData.items[i].id == itemNumber) {
          socketHandler.playerData.items.splice(i--, 1);
        }
      }


      // If it was armor, update our appearance
      if ( startItem.equipped ) {
        if ( items[startItem.template].type == 'armor' ) {
          ironbane.player.UpdateAppearance();
        }
        if ( items[startItem.template].type == 'weapon' ||
          items[startItem.template].type == 'tool') {
          ironbane.player.UpdateWeapon(0);
        }
      }

      startItem.equipped = 0;

      hudHandler.UpdateEquippedItems();

      soundHandler.Play(ChooseRandom(["bag1"]));

    });
  },
  SwitchItem: function(slotNumber, startItem, itemID, slotID, inLoot){

    var data = {
      'slotNumber': slotNumber,
      'itemID': startItem.id
    }

    if ( inLoot ) {
      data['npcID'] = ironbane.player.lootUnit.id;
    }

    socketHandler.socket.emit('switchItem', data, function(reply) {

      if ( ISDEF(reply.errmsg) ) {
        hudHandler.MessageAlert(reply.errmsg);

        // Teleport back!
        if ( inLoot ) {
          TeleportElement('li'+startItem.id, 'ls'+startItem.slot);
        }
        else {
          TeleportElement('ii'+startItem.id, 'is'+startItem.slot);
        }

        return;
      }

      var switchItem = hudHandler.FindItemBySlot(slotNumber, inLoot);
      if ( switchItem ) {
        switchItem.slot = startItem.slot;

        if ( inLoot ) {
          TeleportElement('li'+switchItem.id, 'ls'+startItem.slot);
        }
        else {
          TeleportElement('ii'+switchItem.id, 'is'+startItem.slot);
        }
      }

      // To an inventory slot
      startItem.slot = slotNumber;

      // Perform the UI
      TeleportElement(itemID, slotID);

      hudHandler.UpdateEquippedItems();

      soundHandler.Play(ChooseRandom(["bag1"]));

    })
  },
  LootItem: function(switchItem, startItem, slotNumber, slotID){
    var data = {
      'npcID':ironbane.player.lootUnit.id,
      'switchID': switchItem ? switchItem.id : 0,
      'slotNumber': slotNumber,
      'itemID': startItem.id
    };


    socketHandler.socket.emit('lootItem', data, function(reply) {

      if ( ISDEF(reply.errmsg) ) {
        hudHandler.MessageAlert(reply.errmsg);

        // Teleport back!
        TeleportElement('li'+startItem.id, 'ls'+startItem.slot);

        return;
      }

      if ( ISDEF(reply.newCoins) ) {
        ironbane.player.coins = reply.newCoins;
        hudHandler.MakeCoinBar(true);
      }

      if ( switchItem ) {
        switchItem.slot = startItem.slot;

        TeleportElement('ii'+switchItem.id, 'ls'+startItem.slot);

        // Change the name to something temporarily
        $('#ii'+switchItem.id).attr('id', 'temp'+switchItem.id);

        // Unequip it first
        socketHandler.playerData.items = _.without(socketHandler.playerData.items, switchItem);

        // Add to lootitems
        ironbane.player.lootItems.push(switchItem);

        // If it was armor, update our appearance
        if ( switchItem.equipped ) {
          if ( items[switchItem.template].type == 'armor' ) {
            ironbane.player.UpdateAppearance();
          }
          if ( items[switchItem.template].type == 'weapon' ) {
            ironbane.player.UpdateWeapon(0);
          }
        }

        switchItem.equipped = 0;
      }

      // Delete from lootitems
      ironbane.player.lootItems = _.without(ironbane.player.lootItems, startItem);

      // Remove the pricetag if present
      if ( ISDEF(startItem.price) ) {
        delete startItem.price;

        hudHandler.MakeItemHover('li'+startItem.id, startItem);
      }

      socketHandler.playerData.items.push(startItem);

      startItem.slot = slotNumber;

      // Adjust the DOM element's name from li to ii
      $('#li'+startItem.id).attr('id', 'ii'+startItem.id);

      if ( switchItem ) {
        // Change the name to the loot slot
        $('#temp'+switchItem.id).attr('id', 'li'+switchItem.id);
      }

      // Do the UI actions
      TeleportElement('ii'+startItem.id, slotID);

      hudHandler.UpdateEquippedItems();

      soundHandler.Play(ChooseRandom(["bag1"]));

    });
  },
  FindItemBySlot: function(slot, inLoot) {
    var list = inLoot ? ironbane.player.lootItems : socketHandler.playerData.items;
    for(var i=0;i<list.length;i++){
      var item = list[i];
      if ( item.slot == slot ) return item;
    }
    return null;
  },
  FindItemByID: function(id, inLoot) {
    var list = inLoot ? ironbane.player.lootItems : socketHandler.playerData.items;
    for(var i=0;i<list.length;i++){
      var item = list[i];
      if ( item.id == id ) return item;
    }
    return null;
  },
  MakeItemHover: function(div, item) {

    var template = items[item.template];

    var content = '';

    var itemurl;

    if ( template.type == 'armor' ) {
      itemurl = 'plugins/game/images/characters/base/'+(template['subtype'])+'/medium.php?i='+(template['image'])+'';
    }
    else {
      itemurl = 'plugins/game/images/items/medium.php?i='+(template['image'])+'';
    }

    content += '<div style="min-height:20px;">';
    content += '<div style="margin-top:-3px;width:33px;height:30px;float:left;"><img src="'+itemurl+'"></div>';
    content += '<div style="margin-top:3px;">'+template.name+'</div>';
    content += '</div>';





    var itemInfo = "";

    switch (template.type) {
      case 'weapon':
        if ( item.attr1 > 0 ) {
          itemInfo += '<tr><td style="text-align:right;"><b>Damage</b></td><td style="padding-left:10px;">'+this.GetStatContent(item.attr1, "misc/heart", 0, false, true)+'</td></tr>';
        }
        else {
          itemInfo += '<tr><td style="text-align:right;"><b>Heals</b></td><td style="padding-left:10px;">'+this.GetStatContent(Math.abs(item.attr1), "misc/heart", 0, false, true)+'</td></tr>';
        }
        break;
      case 'armor':
        itemInfo += '<tr><td style="text-align:right;"><b>Armor</b></td><td style="padding-left:10px;">'+this.GetStatContent(item.attr1, "misc/armor", 0, false, true)+'</td></tr>';
        break;
      case 'consumable':
        switch (template.subtype) {
          case "restorative":
            itemInfo += '<tr><td style="text-align:right;"><b>Restores</b></td><td style="padding-left:10px;">'+this.GetStatContent(item.attr1, "misc/heart", 0, false, true)+'</td></tr>';
            break;
        }
        break;
    }

    if ( ISDEF(item.price) ) {
      itemInfo += '<tr><td style="text-align:right;"><b>Price</b></td><td style="padding-left:10px;">'+this.GetStatContent(item.price, "misc/coin", item.price, true, true)+'</td></tr>';
    }

    if ( debugging ) {
      itemInfo += '<tr><td style="text-align:right;"><b>ID</b></td><td style="padding-left:10px;">'+item.id+'</td></tr>';
      itemInfo += '<tr><td style="text-align:right;"><b>Slot</b></td><td style="padding-left:10px;">'+item.slot+'</td></tr>';
    }

    if ( itemInfo ) {
      content += "<hr>";

      content += '<table>';

      content += itemInfo;

      content += '</table>';
    }


    MakeHoverBox(div, content);
  },
  MakeSlotItems: function(isLoot) {

    var data = isLoot ? ironbane.player.lootItems : socketHandler.playerData.items;

    if ( isLoot ) {
      $('div[id^="li"]').remove();
    }
    else {
      $('div[id^="ii"]').remove();
    }

    for(var i=0;i<data.length;i++){
      var item = data[i];

      var template = items[item.template];


      var name = isLoot ? 'li'+item.id : 'ii'+item.id;
      $('#gameFrame').append('<div id="'+name+'" class="itemSlot"></div>');

      var targetName = isLoot ? 'ls'+item.slot : 'is'+item.slot;
      TeleportElement(name, targetName);


      //
      this.MakeItemHover(name, item);

      //bm("item:"+item.id+",slot"+item.slot+"");

      var itemurl;
      if ( template.type == 'armor' ) {
        itemurl = 'plugins/game/images/characters/base/'+(template['subtype'])+'/big.php?i='+(template['image'])+'';
      }
      else {
        itemurl = 'plugins/game/images/items/big.php?i='+(template['image']);
      }

      $('#'+name).css('background-image','url('+itemurl+')');
      //$('#'+name).css('background-color','orange');
      //var hue = 'rgb(' + getRandomInt(50,255) + ',' + getRandomInt(50,255) + ',' + getRandomInt(50,255) + ')';
      //$('#'+name).css('background-color', hue);


      $('#'+name).css('background-repeat','no-repeat');
      $('#'+name).css('background-position','center');


      // Clicking it uses it!
      // But not dragging!
      //if ( !isLoot ) {
      (function(item){
        $('#'+name).click(function() {
          if ($(this).hasClass('noclick')) {
            $(this).removeClass('noclick');
          }
          else {
            if ( _.contains(socketHandler.playerData.items, item) ) {
              ironbane.player.UseItem(item.slot);
            }
          }

        });
      })(item);
      //}


      $('#'+name).draggable({
        containment: "#gameFrame",
        start: function(event, ui) {
          $(this).addClass('noclick');
        }
      });
    }

    hudHandler.UpdateEquippedItems();

  },
  ReloadInventory: function() {
    if ( ironbane.player ) {
      this.MakeSlotItems(false);
      if ( ironbane.player.canLoot ) {
        this.MakeSlotItems(true);
      }
    }
  },
  ResizeFrame: function() {
    frameWidth = $(window).width();
    frameHeight = $(window).height();
    $('#gameFrame').css('width', frameWidth);
    $('#gameFrame').css('height', frameHeight);

    this.PositionHud();

    this.ReloadInventory();
    //this.MakeSlotItems(true);

    if ( ironbane.stats && ironbane.stats.domElement ) {
      ironbane.stats.domElement.style.top = ($(window).height()-55)+'px';
    }
  },
  PositionHud: function() {
    $('#chatBox').css('width', (frameWidth-10)+'px');
    $('#chatBox').css('left', '5px');
    $('#chatBox').css('top', (0)+'px');

    $('#loginBox').css('left', ((frameWidth/2)-300)+'px');
    $('#loginBox').css('top', ((frameHeight/2)-300)+'px');

    $('#soundToggleBox').css('left', (frameWidth-50)+'px');
    $('#soundToggleBox').css('top', 0+'px');


    $('#debugBox').css('left', (frameWidth-310)+'px');
    $('#debugBox').css('top', stealth?20:(frameHeight - 310)+'px');


    $('#bigMessagesBox').css('width', (frameWidth)+'px');
    $('#bigMessagesBox').css('left', '0px');
    $('#bigMessagesBox').css('top', (frameHeight/3)+'px');


    $('#statBar').css('width', (frameWidth)+'px');
    $('#statBar').css('left', '20px');
    $('#statBar').css('top', (20)+'px');

    $('#itemBar').css('left', ((frameWidth/2)-240)+'px');
    $('#itemBar').css('top', ((frameHeight)-48)+'px');

    $('#coinBar').css('left', ((frameWidth/2)-240)+'px');
    $('#coinBar').css('top', ((frameHeight)-72)+'px');

    $('#lootBag').css('left', ((frameWidth/2)-240)+'px');
    $('#lootBag').css('top', ((frameHeight)-120)+'px');

    $('#book').css('left', ((frameWidth/2)-230)+'px');
    $('#book').css('top', ((frameHeight/2)-210)+'px');

    $('#map').css('left', ((frameWidth/2)-250)+'px');
    $('#map').css('top', ((frameHeight/2)-250)+'px');

    $('#loadingBar').css('left', ((frameWidth/2)-100)+'px');
    $('#loadingBar').css('top', ((frameHeight/2)-50)+'px');

    $('#alertBox').css('left', ((frameWidth/2)-250)+'px');
    $('#alertBox').css('top', ((frameHeight/2)-75)+'px');
    $('#alertBox').hide();

    $('#chatInputBox').hide();

    $('#devNews').css('left', ((frameWidth/2)+200)+'px');
    $('#devNews').css('top', ((frameHeight/2)-57)+'px');



  //        $('#dropItemsBox').css('left', '0px');
  //        $('#dropItemsBox').css('top', '0px');
  //        $('#dropItemsBox').css('width', frameWidth);
  //        $('#dropItemsBox').css('height', frameHeight);

  },
  GetStatContent: function(amount, prefix, fullStat, onlyFull, noMarginSpace) {
    var content = '';



    fullStat = fullStat || 0;
    onlyFull = onlyFull || false;
    noMarginSpace = noMarginSpace || false;

    if ( onlyFull ) {
      amount *= 2;
      fullStat *= 2;
    }

    var fullHearts = Math.floor(amount);
    var halfHeart = false;
    if ( fullHearts % 2 == 1 ) {
      fullHearts--;
      halfHeart = true;
    }
    for(var x=0;x<fullHearts/2;x++){
      if ( fullStat ) fullStat -= 2;
      content += '<img src="plugins/game/images/'+prefix+'_full.png" style="'+(!noMarginSpace?'margin-right:1px;':'')+'">';
    }
    // Spawn all the half hearts
    if (halfHeart ) {
      if ( fullStat ) fullStat -= 2;
      content += '<img src="plugins/game/images/'+prefix+'_half.png" style="'+(!noMarginSpace?'margin-right:1px;':'')+'">';
    }

    if ( fullStat ) {
      for(var x=0;x<fullStat/2;x++){
        content += '<img src="plugins/game/images/'+prefix+'_empty.png" style="'+(!noMarginSpace?'margin-right:1px;':'')+'">';
      }
    }

    return content;
  },
  MakeCoinBar: function(doFlash) {
    doFlash = doFlash || false;
    var content = this.GetStatContent(ironbane.player.coins, doFlash ? 'misc/coin_medium_flash' : 'misc/coin_medium', ironbane.player.coins, true, true);
    //var content = this.GetStatContent(1, 'misc/heart_medium', 6);
    $('#coinBar').html(content);
    if ( doFlash ) setTimeout(function(){
      hudHandler.MakeCoinBar()
    },50);
  },
  MakeHealthBar: function(doFlash) {
    doFlash = doFlash || false;
    var content = this.GetStatContent(ironbane.player.health, doFlash ? 'misc/heart_medium_flash' : 'misc/heart_medium', ironbane.player.healthMax);
    //var content = this.GetStatContent(1, 'misc/heart_medium', 6);
    $('#healthBar').html(content);
    if ( doFlash ) setTimeout(function(){
      hudHandler.MakeHealthBar()
    },50);
  },
  MakeArmorBar: function(doFlash) {
    doFlash = doFlash || false;
    var content = this.GetStatContent(ironbane.player.armor, doFlash ?  'misc/armor_medium_flash' :  'misc/armor_medium', ironbane.player.armorMax);
    $('#armorBar').html(content);
    if ( doFlash ) setTimeout(function(){
      hudHandler.MakeArmorBar()
    },50);
  },
  HideAlert: function() {
    $('#alertBox').hide();
    hudHandler.alertBoxActive = false;

    if ( ISDEF(hudHandler.doYes) ) hudHandler.doYes = undefined;
    if ( ISDEF(hudHandler.doNo) ) hudHandler.doNo = undefined;

  },
  MessageAlert: function(message, options, doYes, doNo) {

    var options = options || null;

    this.doYes = doYes;
    this.doNo = doNo;

    this.alertBoxActive = true;

    // Store all document

    $('#btnOK').hide();
    $('#btnNo').hide();

    switch(options){
      case 'nobutton':
        break;
      case 'question':
        $('#btnOK').show();
        $('#btnNo').show();
        break;
      default:
        $('#btnOK').show();
        break;
    }


    $(document).keydown(function(event) {
      if ( event.keyCode == 13 ) {
        $('#alertBox').hide();
        hudHandler.alertBoxActive = false;
      }
    });


    $('#alertBox').show();
    $('#alertMessage').html(message);
    $('#alertImage').css('height', $('#alertBox').css('height'));
    $('#btnOK').click(function(){
      $('#alertBox').hide();
      hudHandler.alertBoxActive = false;

      if ( ISDEF(hudHandler.doYes) ) hudHandler.doYes();

      hudHandler.doYes = undefined;
    });

    $('#btnNo').click(function(){
      $('#alertBox').hide();
      hudHandler.alertBoxActive = false;

      if ( ISDEF(hudHandler.doNo) ) hudHandler.doNo();

      hudHandler.doNo = undefined;
    });
  },
  DisableButtons: function(buttons) {
    for(var b=0;b<buttons.length;b++) {
      this.oldButtonClasses[buttons[b]] = $('#'+buttons[b]).attr('class');
      $('#'+buttons[b]).attr('class', 'ibutton_disabled');
    }
  },
  EnableButtons: function(buttons) {
    for(var b=0;b<buttons.length;b++) {
      if ( ISDEF(this.oldButtonClasses[buttons[b]]) ) {
        $('#'+buttons[b]).attr('class', this.oldButtonClasses[buttons[b]]);
      }
      else {
        $('#'+buttons[b]).attr('class', 'ibutton');
      }
    }
  },
  UpdateChatBoxScroll: function() {
    $('#chatContent').prop({
      scrollTop: $('#chatContent').prop('scrollHeight')
    });
  },
  //    MakeCharSelectionScreen: function() {
  //        var text = '';
  //        text += '<button id='btnPlay' class='ibutton_fixed'>Play</button>';
  //        text += '<div class='spacer'>';
  //        text += '<button id='btnPlay' class='ibutton_fixed'>Forum</button>';
  //        text += '<div class='spacer'>';
  //        text += '<button id='btnPlay' class='ibutton_fixed'>Play</button>';
  //        text += '<div class='spacer'>';
  //
  //$('#loginContent').html(text);
  //
  //    },
  HideHUD: function() {
      $('#itemBar').hide();
      $('#lootBag').hide();
      $("#coinBar").hide();
      $("#statBar").hide();
      $('div[id^="li"]').hide();
      $('div[id^="ii"]').hide();
      // $('#chatBox').hide();
  },
  ShowHUD: function() {
      $('#itemBar').show();
      // $('#lootBag').show();
      $("#coinBar").show();
      $("#statBar").show();
      $('div[id^="li"]').show();
      $('div[id^="ii"]').show();
      // $('#chatBox').show();
  },
  HideMenuScreen: function() {

    $('#loginBox').hide();
    $('#devNews').hide();
    $('#sideMenu').hide();
    $("#soundToggleBox").hide();

//    $('canvas').show();
    $('#chatBox').show();
    // $('#debugBox').show();
    // $('#loadingBar').show();
    $('#itemBar').show();
    $("#coinBar").show();
    $("#statBar").show();


    // Stop music


    soundHandler.FadeOut("theme", 5000);

  },
  ShowMenuScreen: function() {

    $('#sideMenu').show();
    $('#loginBox').show();
    $('#devNews').show();
    $("#soundToggleBox").show();

//    $('canvas').hide();
    $('#chatBox').hide();
    // $('#debugBox').hide();
    // $('#loadingBar').hide();
    $('#itemBar').hide();
    $('#lootBag').hide();
    $("#coinBar").hide();
    $("#statBar").hide();


    soundHandler.FadeIn("theme", 5000);

  },
  MakeCharSelectionScreen: function() {

    var slotsLeft = slotsAvailable - charCount;

    var text = '';
    text += '<div id="charSelect"></div>';

    $('#loginContent').html(text);

    var charSelect = '';
    //var charSelect = '<button id='btnNewChar' class='ibutton'>Make new character</button><div class='spacer'></div>';

    if ( startdata.loggedIn ) {
      charSelect += '<button class="ibutton_disabled" style="width:180px">'+startdata.name+'</button>';
      charSelect += '<button id="btnLogOut" class="ibutton" style="width:120px">Log out</button>';
      charSelect += '<div class="spacersmall"></div>';
    }
    else {

      charSelect += '<button id="btnEnterChar" class="ibutton_attention" style="width:305px">Play as Guest</button>';
      charSelect += '<div class="spacer"></div>';
      charSelect += '<button id="btnLogin" class="ibutton" style="width:150px">Log in</button>';
      charSelect += '<button id="btnRegister" class="ibutton" style="width:150px">Register</button>';
    //charSelect += '<div class="spacersmall"></div>';
    }

    var myChar = null;
    for(var c=0;c<chars.length;c++) {
      if ( chars[c].id == startdata.characterUsed ) {
        myChar = chars[c];
        break;
      }
    }

    if ( startdata.loggedIn ) {
      charSelect += '<button id="btnPrevChar" class="ibutton'+(charCount==0?'_disabled':'')+'" style="float:left;width:40px">&#9664;</button>';
    }

    if ( startdata.loggedIn ) {
      if ( startdata.characterUsed == 0 ) {
        charSelect += '<button id="btnNewChar" class="ibutton'+(slotsLeft==0?'_disabled':'')+'" style="width:216px">Create Character</button>';
      }
      else {

        //charSelect += '<button id="btnEnterChar" class="ibutton" style="width:214px">Enter Ironbane</button>';
        charSelect += '<div style="width:220px;height:40px;float:left;text-align:center;padding-top:10px;">'+myChar.name+'</div>';

        charSelect += '<button id="btnDelChar" class="ibutton" style="float:left;width:40px;position:absolute;left:276px;top:120px">&#10006;</button>';
      }
    }


    if ( startdata.loggedIn ) {
      charSelect += '<button id="btnNextChar" class="ibutton'+(charCount==0?'_disabled':'')+'" style="width:40px">&#9654;</button><br>';
    }

    if ( startdata.loggedIn ) {

      if ( myChar ) {
        charSelect += '';

        var head = 0;
        var body = 0;
        var feet = 0;

        if ( myChar.equipment != '' ) {
          var charItems = myChar.equipment.split(',');
          for(var i=0;i<charItems.length;i++){
            var item = items[charItems[i]];
            if ( item.type == 'armor' ) {

              switch (item.subtype) {
                case 'head':
                  head = item.image;
                  break;
                case 'body':
                  body = item.image;
                  break;
                case 'feet':
                  feet = item.image;
                  break;
              }

            }
          }
        }


        getCharacterTexture({
          skin:myChar.skin,
          eyes:myChar.eyes,
          hair:myChar.hair,
          feet:feet,
          body:body,
          head:head,
          big:1
        }, function(texture) {
          $('#charImage').attr('src', texture);
        });


        charSelect += '<div id="charPreview"><img id="charImage"></div>';

      }
      else {

        charSelect += '<div id="charPreview"><div>'+(charCount===0?'No characters yet':(slotsLeft===0?"No":slotsLeft)+' slot'+(slotsLeft===1?"":"s")+' remaining')+'</div></div>';
      }

      charSelect += '<div class="spacersmall"></div>';


      if ( myChar ) {
        charSelect += '<button id="btnEnterChar" class="ibutton_attention" style="width:305px">Enter Ironbane</button>';
      }
    }







    $('#charSelect').html(charSelect);


    if ( startdata.loggedIn ) {
      $('#charSelect').css("height", "321px");
    }
    else {
      $('#charSelect').css("height", "");
    }


    $('#btnPrevChar').click(function() {

      if ( startdata.characterUsed == 0 ) {
        startdata.characterUsed = chars[((chars.length)-1)].id;
      }
      else {
        for(var c=0;c<chars.length;c++) {
          if ( chars[c].id == startdata.characterUsed ) {
            var next = parseInt(c) - 1;
            if ( ISDEF(chars[next]) ) {
              startdata.characterUsed = chars[next].id;
            }
            else {
              startdata.characterUsed = 0;
            }
            break;
          }
        }
      }



      hudHandler.MakeCharSelectionScreen();

    //$('#charSelect').html(charSelect);
    });

    $('#btnNextChar').click(function() {

      if ( startdata.characterUsed == 0 ) {


        startdata.characterUsed = chars[0].id;


      }
      else {
        for(var c=0;c<chars.length;c++) {
          if ( chars[c].id == startdata.characterUsed ) {
            var next = parseInt(c) + 1;
            if ( ISDEF(chars[next]) ) {
              startdata.characterUsed = chars[next].id;
            }
            else {
              startdata.characterUsed = 0;
            }
            break;
          }
        }
      }



      hudHandler.MakeCharSelectionScreen();

    //$('#charSelect').html(charSelect);
    });

    var enterChar = function() {

      if ( !socketHandler.serverOnline ) return;

      hudHandler.DisableButtons(['btnLogOut','btnEnterChar',
        'btnNextChar','btnPrevChar','btnDelChar']);


      $('#gameFrame').animate({
        opacity: 0.00
      }, 1000, function() {





        hudHandler.HideMenuScreen();

        var tryConnect = function() {

  //        var tween = new TWEEN.Tween( {
  //          pixelWidth: 1,
  //          pixelHeight: 1
  //        } )
  //        .to( {
  //          pixelWidth: 1000,
  //          pixelHeight: 1000
  //        }, 2000 )
  //    //    .easing( TWEEN.Easing.Elastic.InOut )
  //        .onStart(function() {
  //          ironbane.postprocessing.enabled = true;
  //        })
  //        .onUpdate( function () {
  //          ironbane.postprocessing.pixelationUniforms[ "pixelWidth" ].value = this.pixelWidth;
  //          ironbane.postprocessing.pixelationUniforms[ "pixelHeight" ].value = this.pixelHeight;
  //
  //        } )
  //        .onComplete(function() {
  //          ironbane.postprocessing.enabled = false;
  //          ironbane.canDraw = false;
  //          socketHandler.Connect();
  //        })
  //        .start();

          socketHandler.Connect();

        };

        if ( startdata.loggedIn ) {
          tryConnect();
        }
        else {
          // Quickly make a character as a guest

          $.post('gamehandler.php?action=makechar', function(string) {

            data = JSON.parse(string);

            if ( ISDEF(data.errmsg) ) {
              hudHandler.MessageAlert(data.errmsg);
              return;
            }

            $.post('gamehandler.php?action=getchars', function(data) {

              eval(data);

              tryConnect();
            });


          });
        }
        // $("#gameFrame").css('opacity', '');
        // $("#loadingBar").hide();
      });

    //$('#charSelect').html(charSelect);
    };



    $('#btnEnterChar').click(enterChar);

    $('#btnLogOut').click(function() {

      hudHandler.DisableButtons(['btnLogOut']);

      $.post('gamehandler.php?action=logout', function(string) {

        if ( string == 'OK' ) {

          chars = [];
          startdata.loggedIn = false;
          startdata.characterUsed = 0;

          hudHandler.MakeCharSelectionScreen();

        }
        else {
          hudHandler.EnableButtons(['btnLogOut']);
          hudHandler.MessageAlert(string);
        }

      });

    //$('#charSelect').html(charSelect);
    });

    $('#btnDelChar').click(function() {

      var newChar = '';

      newChar += 'To confirm the deletion of this character, please enter your password.<div class="spacersmall"></div><label for="password">Password</label><div class="spacersmall"></div><input type="password" id="password" class="iinput" style="width:305px"><div class="spacersmall"></div><button id="btnConfirmDeletion" class="ibutton_attention" style="width:150px">Delete</button><button id="btnBack" class="ibutton" style="width:150px">Back</button>';

      $('#charSelect').html(newChar);

      $('#password').focus();

      var confirmDeletion = (function() {

        hudHandler.DisableButtons(['btnConfirmDeletion','btnBack']);

        var password = $('#password').val();

        $.post('gamehandler.php?action=delchar&id='+startdata.characterUsed, {
          pass:password
        }, function(string) {

          data = JSON.parse(string);

          if ( ISDEF(data.errmsg) ) {
            hudHandler.MessageAlert(data.errmsg);
            hudHandler.EnableButtons(['btnConfirmDeletion','btnBack']);
            return;
          }

          // Get id

          for(var c=0;c<chars.length;c++) {
            if ( chars[c].id == data.id ) {

              if ( chars[c].id == startdata.characterUsed ) startdata.characterUsed = 0;

              chars.splice(c, 1);

              break;
            }
          }

          charCount = chars.length;
          if ( charCount > 0 && startdata.characterUsed == 0 ) startdata.characterUsed = chars[0].id;

          hudHandler.MakeCharSelectionScreen();

        });
      });

      (function(confirmDeletion){
        $('#charSelect').keydown(function(event) {
          if ( event.keyCode == 13 && !hudHandler.alertBoxActive ) {
            confirmDeletion();
          }
        });
      })(confirmDeletion);
      $('#btnConfirmDeletion').click(confirmDeletion);

      $('#btnBack').click(function() {

        hudHandler.MakeCharSelectionScreen();

      });
    //$('#charSelect').html(charSelect);
    });

    $('#btnLogin').click(function() {

      if ( slotsLeft <= 0 ) return;

      var newChar = '';

      newChar += '<label for="username">Username</label><div class="spacersmall"></div><input type="text" id="username" class="iinput" style="width:305px"><div class="spacersmall"></div><label for="password">Password</label><div class="spacersmall"></div><input type="password" id="password" class="iinput" style="width:305px"><div class="spacersmall"></div><button id="btnConfirmLogin" class="ibutton_attention" style="width:150px">Log in</button><button id="btnBack" class="ibutton" style="width:150px">Back</button>';

      $('#charSelect').html(newChar);

      $('#username').focus();


      var doLogin = (function() {
        var username = $('#username').val();
        var password = $('#password').val();


        hudHandler.DisableButtons(['btnConfirmLogin','btnBack']);

        $.post('gamehandler.php?action=login', {
          user:username,
          pass:password
        }, function(string) {

          if ( string == 'OK' ) {

            $.post('gamehandler.php?action=getchars', function(data) {
              eval(data);

              startdata.loggedIn = true;

              //hudHandler.EnableButtons(['btnConfirmLogin','btnBack']);
              hudHandler.MakeCharSelectionScreen();

            });

          }
          else {
            hudHandler.MessageAlert(string);
            hudHandler.EnableButtons(['btnConfirmLogin','btnBack']);
          }


        });
      });

      (function(doLogin){
        $('#charSelect').keydown(function(event) {
          if ( event.keyCode == 13 && !hudHandler.alertBoxActive ) {
            doLogin();
          }
        });
      })(doLogin);

      $('#btnConfirmLogin').click(doLogin);

      $('#btnBack').click(function() {

        hudHandler.MakeCharSelectionScreen();

      });
    });

    $('#btnRegister').click(function() {

      if ( slotsLeft <= 0 ) return;

      var newChar = '';

      newChar += '<label for="username">Username</label><div class="spacersmall"></div><input type="text" id="username" class="iinput" style="width:305px"><div class="spacersmall"></div><label for="password">Password</label><div class="spacersmall"></div><input type="password" id="password" class="iinput" style="width:305px"><div class="spacersmall"></div><label for="email">E-mail</label><div class="spacersmall"></div><input type="text" id="email" class="iinput" style="width:305px"><input type="text" id="url" style="display:none"><div class="spacersmall"></div><button id="btnConfirmRegister" class="ibutton_attention" style="width:150px">Register</button><button id="btnBack" class="ibutton" style="width:150px">Back</button>';

      $('#charSelect').html(newChar);

      $('#username').focus();

      var confirmRegister = (function() {
        var username = $('#username').val();
        var password = $('#password').val();
        var email = $('#email').val();
        var url = $('#url').val();


        hudHandler.DisableButtons(['btnConfirmRegister','btnBack']);

        $.post('gamehandler.php?action=register', {
          Ux466hj8:username,
          Ed2h18Ks:password,
          s8HO5oYe:email,
          url:url
        }, function(string) {

          if ( string.split(';')[0] == 'OK' ) {
            hudHandler.MessageAlert(string.split(';')[1]);

            $.post('gamehandler.php?action=getchars', function(data) {
              eval(data);

              startdata.loggedIn = true;

              //hudHandler.EnableButtons(['btnConfirmLogin','btnBack']);
              hudHandler.MakeCharSelectionScreen();

            });
          }
          else {
            hudHandler.EnableButtons(['btnConfirmRegister','btnBack']);
            hudHandler.MessageAlert(string);
          }

        });
      });

      (function(confirmRegister){
        $('#charSelect').keydown(function(event) {
          if ( event.keyCode == 13 && !hudHandler.alertBoxActive ) {
            confirmRegister();
          }
        });
      })(confirmRegister);
      $('#btnConfirmRegister').click(confirmRegister);

      $('#btnBack').click(function() {

        hudHandler.MakeCharSelectionScreen();

      });
    });

    $('#btnNewChar').click(function() {

      if ( slotsLeft <= 0 ) return;


      for(var x=skinIdMaleStart;x<=skinIdMaleEnd;x++){
          getCharacterTexture({skin:x,big:1}, function(texture) {
            preload([texture]);
          });
      }
      for(var x=skinIdFemaleStart;x<=skinIdFemaleEnd;x++){
          getCharacterTexture({skin:x,big:1}, function(texture) {
            preload([texture]);
          });
      }
      for(var x=eyesIdMaleStart;x<=eyesIdMaleEnd;x++){
          getCharacterTexture({eyes:x,big:1}, function(texture) {
            preload([texture]);
          });
      }
      for(var x=eyesIdFemaleStart;x<=eyesIdFemaleEnd;x++){
          getCharacterTexture({eyes:x,big:1}, function(texture) {
            preload([texture]);
          });
      }
      for(var x=hairIdMaleStart;x<=hairIdMaleEnd;x++){
          getCharacterTexture({hair:x,big:1}, function(texture) {
            preload([texture]);
          });
      }
      for(var x=hairIdFemaleStart;x<=hairIdFemaleEnd;x++){
          getCharacterTexture({hair:x,big:1}, function(texture) {
            preload([texture]);
          });
      }
      // for(var x=1;x<=hairIdLimit;x++){
      //   preload(['plugins/game/images/characters/base/skin/'+x+'_big.png']);
      // }

      var newChar = '';

      newChar += '<label for="ncname">Name</label><div class="spacersmall"></div><input type="text" id="ncname" class="iinput" style="width:305px" maxlength="12"><div id="charCustomizationContainer"><div id="charCustomizationButtonsLeft"></div><div id="charCustomizationPreview"></div><div id="charCustomizationButtonsRight"></div></div><button id="btnConfirmNewChar" class="ibutton_attention" style="width:150px">Create</button><button id="btnBackMainChar" class="ibutton" style="width:150px">Cancel</button>';

      $('#charSelect').html(newChar);

      var custButtons = '';

//      custButtons += '<div style="float:left">';


      custButtons += 'Gender<br>';
      custButtons += '<button id="btnGenderChange" class="ibutton" style="width:70px;">Boy</button>';

      custButtons += '<br>';
      custButtons += 'Skin<br>';
      custButtons += '<button id="btnSkinPrev" class="ibutton" style="width:30px;">&#9664;</button>';
      custButtons += '<button id="btnSkinNext" class="ibutton" style="width:30px">&#9654;</button>';

//      custButtons += '</div>';

      $('#charCustomizationButtonsLeft').html(custButtons);

      custButtons = '';


      custButtons += 'Hair<br>';
      custButtons += '<button id="btnHairPrev" class="ibutton" style="width:30px;">&#9664;</button>';
      custButtons += '<button id="btnHairNext" class="ibutton" style="width:30px">&#9654;</button>';

      custButtons += 'Eyes<br>';
      custButtons += '<button id="btnEyesPrev" class="ibutton" style="width:30px;">&#9664;</button>';
      custButtons += '<button id="btnEyesNext" class="ibutton" style="width:30px">&#9654;</button>';

//      custButtons += '</div>';

      $('#charCustomizationButtonsRight').html(custButtons);


      var custChar = '';


      var constrainCustomizers = function() {
        if ( selectedMale ) {
          selectedSkin = Math.min(skinIdMaleEnd, selectedSkin);
          selectedSkin = Math.max(skinIdMaleStart, selectedSkin);
          selectedEyes = Math.min(eyesIdMaleEnd, selectedEyes);
          selectedEyes = Math.max(eyesIdMaleStart, selectedEyes);
          selectedHair = Math.min(hairIdMaleEnd, selectedHair);
          selectedHair = Math.max(hairIdMaleStart, selectedHair);
        }
        else {
          selectedSkin = Math.min(skinIdFemaleEnd, selectedSkin);
          selectedSkin = Math.max(skinIdFemaleStart, selectedSkin);
          selectedEyes = Math.min(eyesIdFemaleEnd, selectedEyes);
          selectedEyes = Math.max(eyesIdFemaleStart, selectedEyes);
          selectedHair = Math.min(hairIdFemaleEnd, selectedHair);
          selectedHair = Math.max(hairIdFemaleStart, selectedHair);
        }
      };

      var refreshChar = function() {

        constrainCustomizers();

        console.log("skin:"+selectedSkin+",eyes:"+selectedEyes+",hair:"+selectedHair);

        var cachefile = '';



        cachefile = 'plugins/game/images/characters/cache/'+
        selectedSkin+'_0_0_0_0_0_1.png';
        $('#charSkinLayer').css('background-image', 'url('+cachefile+')');

        cachefile = 'plugins/game/images/characters/cache/0_'+
        selectedEyes+'_0_0_0_0_1.png';
        $('#charEyesLayer').css('background-image', 'url('+cachefile+')');

        cachefile = 'plugins/game/images/characters/cache/0_0_'+
        selectedHair+'_0_0_0_1.png';
        $('#charHairLayer').css('background-image', 'url('+cachefile+')');

        // getCharacterTexture({skin:selectedSkin,big:1}, function(texture) {
        //   $('#charSkinLayer').css('background-image', 'url('+texture+')');
        // });

        // getCharacterTexture({eyes:selectedEyes,big:1}, function(texture) {
        //   $('#charEyesLayer').css('background-image', 'url('+texture+')');
        // });

        // getCharacterTexture({hair:selectedHair,big:1}, function(texture) {
        //   $('#charHairLayer').css('background-image', 'url('+texture+')');
        // });

      };

      custChar += '<div id="charSkinLayer"></div>';
      custChar += '<div id="charEyesLayer"></div>';
      custChar += '<div id="charHairLayer"></div>';


      $('#charCustomizationPreview').html(custChar);


      (function(refreshChar){



        $('#btnGenderChange').click(function() {
          selectedMale = !selectedMale;

          $('#btnGenderChange').html(selectedMale ? 'Boy' : 'Girl');


          refreshChar();

        });



        $('#btnSkinNext').click(function() {
          selectedSkin++;
          refreshChar();
        });

        $('#btnSkinPrev').click(function() {
          selectedSkin--;
          refreshChar();
        });

        $('#btnEyesNext').click(function() {
          selectedEyes++;
          refreshChar();
        });

        $('#btnEyesPrev').click(function() {
          selectedEyes--;
          refreshChar();
        });

        $('#btnHairNext').click(function() {
          selectedHair++;
          refreshChar();
        });

        $('#btnHairPrev').click(function() {
          selectedHair--;
          refreshChar();
        });

      })(refreshChar);

      refreshChar();

      //location.href = $('#charHairLayer').css('background-image'));
      //$('#charHairLayer').css('background-image', 'url(images/characters/base/hair/'+selectedHair+'.png)');

      var confirmNewChar = (function() {


        hudHandler.DisableButtons(['btnConfirmNewChar','btnBackMainChar']);

        var ncname = $('#ncname').val();



        $.post('gamehandler.php?action=makechar&name='+ncname, {
          skin:selectedSkin,
          eyes:selectedEyes,
          hair:selectedHair
        }, function(string) {

          data = JSON.parse(string);

          if ( ISDEF(data.errmsg) ) {
            hudHandler.MessageAlert(data.errmsg);
            hudHandler.EnableButtons(['btnConfirmNewChar','btnBackMainChar']);
            return;
          }

          // Get id

          chars.push(data);

          charCount = chars.length;
          if ( charCount > 0 && startdata.characterUsed == 0 ) startdata.characterUsed = chars[0].id;

          hudHandler.MakeCharSelectionScreen();
        });
      });

      (function(confirmNewChar){
        $('#charSelect').keydown(function(event) {
          if ( event.keyCode == 13 && !hudHandler.alertBoxActive ) {
            confirmNewChar();
          }
        });
      })(confirmNewChar);
      $('#btnConfirmNewChar').click(confirmNewChar);

      $('#btnBackMainChar').click(function() {

        hudHandler.MakeCharSelectionScreen();

      });
    });

  },
  Tick: function(dTime) {

    var output = '';

    for(var m=0;m<this.bigMessages.length;m++) {
      var msg = this.bigMessages[m];

      msg.Tick(dTime);

      if ( msg.timeLeft <= 0 ) {
        this.bigMessages.splice(m, 0);
      }
      else {
        output += '<div style="opacity:'+msg.opacity+'">'+msg.message+'</div><br>';
      }

    }


    $('#bigMessagesBox').html(output);


  },
  AddBigMessage: function(msg, duration) {
    this.bigMessages.push(new BigMessage(msg, duration));
  },
  ShowMap: function() {

    $("#map").css("background-image", "url(plugins/game/data/"
      +terrainHandler.zone+"/map.png"+(isEditor?"?"+(new Date()).getTime():"")+")");

    $("#map").show();

  },
  HideMap: function() {
    $("#map").hide();
  },
  ShowBook: function(text, page) {

    //<button id="bookPrevPage" class="ibutton_book" style="width:150px">Previous Page</button>
    //<button id="bookNextPage" class="ibutton_book" style="width:150px">Next Page</button>

    $("#book").show();

    page = page || 0;

    textArray = text.split("|");

    if ( ISDEF(textArray[page]) ) {
      $("#bookPageLeft").html(textArray[page]);
    }
    else {
      $("#bookPageLeft").empty();
    }
    if ( ISDEF(textArray[page+1]) ) {
      $("#bookPageRight").html(textArray[page+1]);
    }
    else {
      $("#bookPageRight").empty();
    }


    if ( ISDEF(textArray[page-2]) ) {
      $("#bookFooterLeft").html('<button id="bookPrevPage" class="ibutton_book" style="width:150px">Previous Page</button>');
      $("#bookPrevPage").click(function(){
        hudHandler.ShowBook(text, page-2)
      });
    }
    else {
      $("#bookFooterLeft").empty();
    }
    if ( ISDEF(textArray[page+2]) ) {
      $("#bookFooterRight").html('<button id="bookNextPage" class="ibutton_book" style="width:150px">Next Page</button>');
      $("#bookNextPage").click(function(){
        hudHandler.ShowBook(text, page+2)
      });
    }
    else {
      $("#bookFooterRight").empty();
    }
  },
  HideBook: function() {
    $("#book").hide();
  },
  AddChatMessage: function(msg) {
    //$('#chatContent').append(msg+'<br>');
    this.chatBuffer += msg+'<br>';


    this.chatContentScroller.getContentPane().html(this.chatBuffer);
    this.chatContentScroller.reinitialise();
    this.chatContentScroller.scrollToBottom();

    // $('#chatBox, #chatContent').click(function(e) {
    //   e.stopPropagation();
    //   e.preventDefault();
    //   return false;
    // });

    // hudHandler.UpdateChatBoxScroll();
  }
});



//setTimeout(function(){hudHandler.ShowBook('Saturn is the sixth planet from the Sun and the second largest planet in the Solar System, after Jupiter. Named after the Roman god Saturn, its astronomical symbol (?) represents the god\'s sickle.|Saturn is a gas giant with an average radius about nine times that of Earth.[12][13] While only one-eighth the average density of Earth, with its larger volume Saturn is just over 95 times as massive as Earth.[14][15][16] Saturn\'s interior is probably composed of a core of iron, nickel and rock (silicon and oxygen compounds), surrounded by a deep layer of metallic hydrogen, an intermediate layer of liquid hydrogen and liquid helium and an outer gaseous layer.[17]| The planet exhibits a pale yellow hue due to ammonia crystals in its upper atmosphere. Electrical current within the metallic hydrogen layer is thought to give rise to Saturn\'s planetary magnetic field, which is slightly weaker than Earth\'s and around one-twentieth the strength of Jupiter\'s.[18]| The outer atmosphere is generally bland and lacking in contrast, although long-lived features can appear. Wind speeds on Saturn can reach 1,800 km/h (1,100 mph), faster than on Jupiter, but not as fast as those on Neptune.[19] Saturn has a prominent ring system that consists of nine continuous main rings and three discontinuous arcs, composed mostly of ice particles with a smaller amount of rocky debris and dust. |Sixty-two[20] known moons orbit the planet; fifty-three are officially named. This does not include the hundreds of "moonlets" within the rings.| Titan, Saturn\'s largest and the Solar System\'s second largest moon, is larger than the planet Mercury and is the only moon in the Solar System to retain a substantial atmosphere.[21]')}, 1000);

var hudHandler = new HUDHandler();
