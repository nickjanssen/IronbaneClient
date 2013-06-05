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


// Variables
var hasChatFocus = false;
var lastUsedChat = new Array();
var lastUsedChatCounter = 0;
var lastUsedChatSelectCounter = 0;



// Client input
var msg = '';

$('#chatInput').attr('value', msg);

$('#chatInput').focus(function(){

  if ( !socketHandler.inGame ) return;

  if ( $('#chatInput').attr('value') == msg ) {
    $('#chatInput').attr('value', '');
  }
  hasChatFocus = true;
});
$('#chatInput').blur(function(){
  if ( !socketHandler.inGame ) return;

  if ( $('#chatInput').attr('value') == '' ) {
    $('#chatInput').attr('value', msg);
  }
  hasChatFocus = false;
});
$('#chatInput').keypress(function(e)
{
  if ( !socketHandler.inGame ) return;

  code= (e.keyCode ? e.keyCode : e.which);
  if (code == 13) {
    var clientmsg = $('#chatInput').val();
    lastUsedChat[lastUsedChatCounter++] = clientmsg;
    lastUsedChatSelectCounter = lastUsedChatCounter;

    socketHandler.socket.emit('chatMessage', {
      message: clientmsg
    });

    $('#chatInput').attr('value', '');
    $('#chatInput').blur();
    $('#chatInputBox').hide();
  }
});
$('#chatInput').keydown(function(e)
{
  if ( !socketHandler.inGame ) return;

  code= (e.keyCode ? e.keyCode : e.which);
  if ( code == 45 ) {
    if ( player.target_unit ) {
      $('#chatInput').attr('value',
        $('#chatInput').attr('value') +
        ' '+player.target_unit.id+' ');
    }
  }
  if ( code == 38 ) {
    if ( lastUsedChatSelectCounter > 0 ) lastUsedChatSelectCounter--;
    $('#chatInput').attr('value',
      lastUsedChat[lastUsedChatSelectCounter]);
  }
  if ( code == 40 ) {
    if ( lastUsedChatSelectCounter < lastUsedChat.length - 1 )
      lastUsedChatSelectCounter++;

    $('#chatInput').attr('value',
      lastUsedChat[lastUsedChatSelectCounter]);
  }
});

// Disable right-click
$(function() {
  $(this).bind('contextmenu', function(e) {
    e.preventDefault();
  });
});

var isHoveringHud = false;
//$('#statBar,#coinBar,#itemBar,#lootBag,div[id^="li"],div[id^="ii"]').on("mouseover",
//  function() { isHoveringHud = true; },
//  function() { isHoveringHud = false; }
//);
//$('#gameFrame').on("mouseover",
//  function(event) { event.stopPropagation(); isHoveringHud = false; },
//  function(event) { isHoveringHud = true; }
//);
$('#gameFrame').on("mouseover",
  function(event) {
    isHoveringHud = false;
  }
  );
$('#statBar,#coinBar,#itemBar,#lootBag,div[id^="li"],div[id^="ii"]').on("mouseover",
  function(event) {
    event.stopPropagation();
    isHoveringHud = true;
  }
  );

$(window).resize(function() {
  //alert('resize');
  hudHandler.ResizeFrame();

  // notify the renderer of the size change
  ironbane.renderer.setSize( window.innerWidth, window.innerHeight );
  // update the camera
  ironbane.camera.aspect  = window.innerWidth / window.innerHeight;
  ironbane.camera.updateProjectionMatrix();
});

var noDisconnectTrigger = false;

$(document).keydown(function(event){

  if ( !socketHandler.inGame ) return;

  if ( hasChatFocus ) return;

  keyTracker[event.keyCode] = true;

  if ( event.keyCode == 13 ) {
    setTimeout(function(){
      $('#chatLine').focus();
      hasChatFocus = true;
    }, 100);
    return;
  }


  if ( ironbane.player ) {
    if ( event.keyCode == 49 ) {
      ironbane.player.UseItem(0);
    }
    if ( event.keyCode == 50 ) {
      ironbane.player.UseItem(1);
    }
    if ( event.keyCode == 51 ) {
      ironbane.player.UseItem(2);
    }
    if ( event.keyCode == 52 ) {
      ironbane.player.UseItem(3);
    }
    if ( event.keyCode == 53 ) {
      ironbane.player.UseItem(4);
    }
    if ( event.keyCode == 54 ) {
      ironbane.player.UseItem(5);
    }
    if ( event.keyCode == 55 ) {
      ironbane.player.UseItem(6);
    }
    if ( event.keyCode == 56 ) {
      ironbane.player.UseItem(7);
    }
    if ( event.keyCode == 57 ) {
      ironbane.player.UseItem(8);
    }
    if ( event.keyCode == 48 ) {
      ironbane.player.UseItem(9);
    }
  }


  if ( event.keyCode == 27 ) {

    if ( !cinema.IsPlaying() ) {
      hudHandler.MessageAlert("Back to the Main Menu?", "question", function() {

        terrainHandler.readyToReceiveUnits = false;

        socketHandler.socket.emit('backToMainMenu', {}, function (reply) {

          if ( ISDEF(reply.errmsg) ) {
            hudHandler.MessageAlert(reply.errmsg);
            return;
          }

          $('#gameFrame').animate({
            opacity: 0.00
          }, 1000, function() {

            setTimeout(function(){ironbane.showingGame = false;}, 100);

            socketHandler.inGame = false;

            for(var u=0;u<ironbane.unitList.length;u++) ironbane.unitList[u].Destroy();

            ironbane.unitList = [];

            terrainHandler.Destroy();

            ironbane.player = null;

            socketHandler.loggedIn = false;

            $('div[id^="li"]').remove();
            $('div[id^="ii"]').remove();

            $.post('gamehandler.php?action=getchars', function(data) {
              eval(data);

              hudHandler.ShowMenuScreen();
              hudHandler.MakeCharSelectionScreen();

            });

          });


        });
      }, function() {

      });

    }
    else {
      hudHandler.MessageAlert("Skip Cutscene?", "question", function() {
        ironbane.player.canMove = true;
        cinema.Clear();
      }, function() {});
    }
  }


});

// Disable text selection
(function($){

  $.fn.disableSelection = function() {
    return this.each(function() {
      $(this).attr('unselectable', 'on')
      .css({
        '-moz-user-select':'none',
        '-webkit-user-select':'none',
        'user-select':'none',
        '-ms-user-select':'none'
      })
      .each(function() {
        this.onselectstart = function() {
          return false;
        };
      });
    });
  };

})(jQuery);

var mouse = new THREE.Vector2();


var mouseCheckHoldInterval = null;

var eClientX = 0;
var eClientY = 0;

$(document).mousedown(function(event) {


  var id = event.target.id;
  if (event.target == ironbane.renderer.domElement || $.inArray(id, ['chatContent','debugBox']) != -1 ) {
    event.preventDefault();
    if ( mouseCheckHoldInterval ) clearInterval(mouseCheckHoldInterval);
    mouseCheckHoldInterval = setInterval(function(){
      mouseIntervalFunction(event)
    }, (showEditor && levelEditor.editorGUI.enablePathPlacer) ? 100 : 100);

  }


//return false;
}
);
$(document).mouseup(function() {
  clearInterval(mouseCheckHoldInterval);
//return false;
});

var lastMouseToWorldData = null;
var currentMouseToWorldData = null;

$(document).mousemove(function(event) {
  mouse.x = ( event.clientX / window.innerWidth ) * 2 - 1;
  mouse.y = - ( event.clientY / window.innerHeight ) * 2 + 1;

//    if ( ironbane.player ) ironbane.player.UpdateMouseProjectedPosition();
});

var mouseIntervalFunction = function(event){


  document.getSelection().removeAllRanges();



  if ( showEditor && levelEditor.editorGUI.globalEnable ) {

    //        var vector = new THREE.Vector3( mouse.x, mouse.y, 0.5 );
    //        ironbane.projector.unprojectVector( vector, ironbane.camera );
    //
    //        var ray = new THREE.Ray( ironbane.camera.position, vector.subSelf( ironbane.camera.position ).normalize() );
    //
    //        var meshList = [];
    //
    //        if ( (levelEditor.editorGUI.enableObjectPlacer && levelEditor.editorGUI.opMode == ObjectPlacerModeEnum.DELETE) ||
    //        levelEditor.editorGUI.enableModelPlacer && levelEditor.editorGUI.mpMode == ModelPlacerModeEnum.DELETE) {
    //
    //            for(var c in terrainHandler.chunks) {
    //                for(var o in terrainHandler.chunks[c].objects) {
    //                    if ( terrainHandler.chunks[c].objects[o].mesh ) {
    //                        meshList.push(terrainHandler.chunks[c].objects[o].mesh);
    //                    }
    //                }
    //            }
    //
    //            // Add meshes to the list
    //            for(var u in ironbane.unitList) {
    //                var unit = ironbane.unitList[u];
    //
    //                if ( !(unit instanceof Mesh) ) continue;
    //
    //                if ( unit.InRangeOfUnit(ironbane.player, 500) ) {
    //                    meshList.push(unit.mesh);
    //                }
    //            }
    //
    //        }
    //        if ( levelEditor.editorGUI.enableNPCEditor && (levelEditor.editorGUI.neMode == NPCEditorModeEnum.PICK || levelEditor.editorGUI.neMode == NPCEditorModeEnum.DELETE) ) {
    //
    //            for(var u in ironbane.unitList) {
    //                var unit = ironbane.unitList[u];
    //
    //                if ( unit.id >= 0 ) continue;
    //
    //                if ( unit.mesh ) {
    //                    meshList.push(unit.mesh);
    //                }
    //            }
    //
    //        }
    //        else {
    //
    //            for(var c in terrainHandler.chunks) {
    //                if ( terrainHandler.chunks[c].mesh ) {
    //                    meshList.push(terrainHandler.chunks[c].mesh);
    //                }
    //            }
    //
    //
    //        }




    if (currentMouseToWorldData) {


      if ( levelEditor.editorGUI.enableWorldPainter ) {
        var ix = roundNumber(
          currentMouseToWorldData.face.centroid.x-(0.5*worldScale)).Round2();
        var iz = roundNumber(
          currentMouseToWorldData.face.centroid.z-(0.5*worldScale)).Round2();


      var bw = (levelEditor.editorGUI.paintSize).Round();
      var bwr = (Math.round(levelEditor.editorGUI.paintSize)).Round();

      var tileToPaint = levelEditor.editorGUI.selectedTile;




    var cat = levelEditor.cats[levelEditor.currentCat];
    var amountoftilesperline = cat.amountoftilesperline;

    var countX = 0;
    var countZ = 0;

      for(var x = ix-bwr;x<=ix+bwr;x+=worldScale){
          countX ++;
        for(var z = iz-bwr;z<=iz+bwr;z+=worldScale){
            countZ ++;
            var tileCount4PaintMode = 0;

            var tempOffset = 0;
            var offset = bwr % 2 != 0 ? (0.5*worldScale) : 0;
            if ( le("wp4XTilePaintMode") ) {

                //if ( x % 2 ) tileCount4PaintMode += 1;

                //if ( countX % 2 )  tileCount4PaintMode += 1;
                //if ( countZ % 2 )  tileCount4PaintMode += amountoftilesperline;

                //tempOffset = ((amountoftilesperline+1)-tileCount4PaintMode);
                if ( bwr % 2 ) {
                    if ( (x+1) % 4 ) tempOffset += 1;
                    if ( (z+1) % 4 ) tempOffset += amountoftilesperline;
                }
                else {
                    offset = 0;

                    if ( (x) % 4 ) tempOffset += 1;
                    if ( (z) % 4 ) tempOffset += amountoftilesperline;


                }
            }

            //var d = DistanceBetweenPoints(ix, iz, x, z);
            //if ( d <= bw ) {


            levelEditor.SetTileImage(x - offset, z - offset, tileToPaint + tempOffset, true, true);
          //}





          }
        }


      //                    var chunkPos = WorldToCellCoordinates(ix, iz, chunkSize);
      //                    var chunkPosWorld = CellToWorldCoordinates(chunkPos.x, chunkPos.z, chunkSize);
      //
      //                    if ( ISDEF(tezzrrainHandler.chunks[chunkPosWorld.x+'-'+chunkPosWorld.z]) ) {
      //                        var chunk = terrainHandler.chunks[chunkPosWorld.x+'-'+chunkPosWorld.z];
      //                        chunk.Reload();
      //                    }
      }
      else if ( levelEditor.editorGUI.enableNPCEditor ) {

        var position = currentMouseToWorldData.point;

        // Find an object near that position which could be a waypoint
        var npc = null;

        for(var u=0;u<ironbane.unitList.length;u++){

          var obj = ironbane.unitList[u];

          if ( obj.InRangeOfPosition(position, 1)
            && ((obj instanceof Unit) && obj.id < 0) ) {
            npc = obj;
          }


        }

        if ( npc ) {
          socketHandler.socket.emit('deleteNPC', npc.id);
        }
        else {
          for(var u=0;u<ironbane.unitList.length;u++) {
            var unit = ironbane.unitList[u];

            if ( unit.id >= 0 ) continue;

            if ( currentMouseToWorldData.object == unit.mesh ) {
              if ( levelEditor.editorGUI.neDeleteMode ) {
                socketHandler.socket.emit('deleteNPC', unit.id);
              }

            }

          }
        }
      }
      // else if ( levelEditor.editorGUI.enableObjectPlacer ) {

      //   var position = currentMouseToWorldData.point;


      //   if ( levelEditor.editorGUI.opMode == ObjectPlacerModeEnum.DELETE ) {
      //     for(var c in terrainHandler.chunks) {
      //       for(var o=0;o<terrainHandler.chunks[c].objects.length;o++) {
      //         if ( currentMouseToWorldData.object == terrainHandler.chunks[c].objects[o].mesh ) {

      //           var obj = terrainHandler.chunks[c].objects[o];

      //           if ( !(obj instanceof Mesh) ) {

      //             // Send a request to destroy this object
      //             socketHandler.socket.emit('deleteGameObject', obj.position.Round(2));

      //             obj.Destroy();

      //             terrainHandler.chunks[c].objects.splice(o, 1);
      //           }

      //         }
      //       }
      //     }

      //   }
      //   else {
      //     // Place an object here!
      //     levelEditor.PlaceObject(position, levelEditor.editorGUI.selectedTile);
      //   }
      // }
      else if ( levelEditor.editorGUI.enablePathPlacer ) {

        var position = currentMouseToWorldData.point;

        // Find an object near that position which could be a waypoint
        var waypoint = null;

        for(var c in terrainHandler.chunks) {
          for(var o=0;o<terrainHandler.chunks[c].objects.length;o++) {
            if ( terrainHandler.chunks[c].objects[o] instanceof Waypoint && terrainHandler.chunks[c].objects[o].mesh ) {
              var obj = terrainHandler.chunks[c].objects[o];

              if ( obj.InRangeOfPosition(position, 1) ) {
                waypoint = obj;
              }
            }
          }
        }

        // what if the node is in the air?
        for(var c in terrainHandler.chunks) {
          for(var o=0;o<terrainHandler.chunks[c].objects.length;o++) {
            if ( currentMouseToWorldData.object == terrainHandler.chunks[c].objects[o].mesh ) {

              var obj = terrainHandler.chunks[c].objects[o];

              if ( (obj instanceof Waypoint) ) {
                waypoint = obj;
              }

            }
          }
        }

        if ( levelEditor.editorGUI.ppMode == PathPlacerModeEnum.NODES ) {

          // Send a request to destroy this object
          socketHandler.socket.emit('ppAddNode', position.Round(2), function(reply) {

            if ( ISDEF(reply.errmsg) ) {
              hudHandler.MessageAlert(reply.errmsg);
              return;
            }

            _.each(terrainHandler.chunks, function(chunk) {
                _.each(chunk.objects, function(obj) {

                    if ( obj instanceof Waypoint ) {
                      if ( obj.InRangeOfPosition(position, levelEditor.editorGUI.ppAutoConnectWithin) ) {
                        socketHandler.socket.emit('ppAddEdge', {
                          from:reply.newNodeID,
                          to:obj.nodeData['id'],
                          twoway:true
                          }, function(reply) {

                          if ( ISDEF(reply.errmsg) ) {
                            hudHandler.MessageAlert(reply.errmsg);
                            return;
                          }

                        });
                      }
                    }

                });
            });


          });





        }
        else if ( levelEditor.editorGUI.ppMode == PathPlacerModeEnum.EDGES ) {

          // If nothing selected, clear!


          if ( levelEditor.selectedNode ) {
            if ( waypoint && levelEditor.selectedNode['id'] != waypoint.nodeData['id']) {


              socketHandler.socket.emit('ppAddEdge', {
                from:levelEditor.selectedNode['id'],
                to:waypoint.nodeData['id'],
                twoway:levelEditor.editorGUI.ppTwoWay
                }, function(reply) {

                if ( ISDEF(reply.errmsg) ) {
                  hudHandler.MessageAlert(reply.errmsg);
                  return;
                }

              });
            }
            else {
              levelEditor.selectedNode = null;
            }

            for(var c in terrainHandler.chunks) terrainHandler.chunks[c].ReloadWaypointsOnly();
          }
          else {
            // Set selected node
            if ( waypoint ) {
              levelEditor.selectedNode = waypoint.nodeData;

              for(var c in terrainHandler.chunks) terrainHandler.chunks[c].ReloadWaypointsOnly();
            }
          }





        }
        else if ( levelEditor.editorGUI.ppMode == PathPlacerModeEnum.DELETE && waypoint) {
          socketHandler.socket.emit('ppDeleteNode', {
            id:waypoint.nodeData.id
            }, function(reply) {

            if ( ISDEF(reply.errmsg) ) {
              hudHandler.MessageAlert(reply.errmsg);
              return;
            }

          });
        }

      }
      else if ( levelEditor.editorGUI.enableModelPlacer ) {

        var position = currentMouseToWorldData.point;


        if ( levelEditor.editorGUI.mpMode == ModelPlacerModeEnum.DELETE ) {
          for(var c in terrainHandler.chunks) {
            for(var o=0;o<terrainHandler.chunks[c].objects.length;o++) {
              if ( currentMouseToWorldData.object == terrainHandler.chunks[c].objects[o].mesh ) {

                var obj = terrainHandler.chunks[c].objects[o];

                if ( obj instanceof Mesh ) {
                  // Send a request to destroy this object

                  socketHandler.socket.emit('deleteModel', obj.position.Round(2));

                }
              }
            }
          }

        }
        else {

          socketHandler.socket.emit('addModel', {
            position:levelEditor.previewMesh.position.clone().Round(2),
            type: 5,
            rX:levelEditor.editorGUI.mpRotX,
            rY:levelEditor.editorGUI.mpRotY,
            rZ:levelEditor.editorGUI.mpRotZ,
            param:levelEditor.editorGUI.selectModel
          });


          // Send a request to place an object here!
          // levelEditor.PlaceModel(levelEditor.previewMesh.position.clone(),
          //   levelEditor.editorGUI.mpRotX,
          //   levelEditor.editorGUI.mpRotY,
          //   levelEditor.editorGUI.mpRotZ,
          //   levelEditor.editorGUI.selectModel);
        }
      }
      else if ( levelEditor.editorGUI.enableModelPainter ) {



          for(var c in terrainHandler.chunks) {
            for(var o=0;o<terrainHandler.chunks[c].objects.length;o++) {
              if ( currentMouseToWorldData.object == terrainHandler.chunks[c].objects[o].mesh ) {

                var obj = terrainHandler.chunks[c].objects[o];

                if ( obj instanceof Mesh ) {



                  // Send a request to destroy this object

                  var currentMetadata = {};

                  // Alter it
                  var materialIndex = currentMouseToWorldData.face.materialIndex + 1;

                  var tileToPaint = levelEditor.editorGUI.selectedTile;

                  currentMetadata["t"+materialIndex] = tileToPaint;

                  socketHandler.socket.emit('paintModel', {
                    pos: obj.position.clone().Round(2),
                    id: obj.meshData.id,
                    metadata: le("mpClearMode") ? {} : currentMetadata,
                    global : le("mpSetForAllModels") ? true : false
                  });

                }
              }
            }
          }


      }
      else if ( levelEditor.editorGUI.enableWorldBuilder ) {
        var ix = roundNumber(currentMouseToWorldData.point.x, 0);
        var iz = roundNumber(currentMouseToWorldData.point.z, 0);
        ix = ix % 2 == 0 ? ix : ix+1;
        iz = iz % 2 == 0 ? iz : iz+1;

        if ( levelEditor.editorGUI.wbMode == WorldBuilderModeEnum.FLATTEN ) {
          levelEditor.editorGUI.brushWidth = levelEditor.editorGUI.brushWidth.Round().Round2();
        }

        // Take into account the brush width
        var bw = (levelEditor.editorGUI.brushWidth/2).Round2();

        var bh = levelEditor.editorGUI.brushHeight;
        var bf = (levelEditor.editorGUI.brushFeather).Round2();

        var t = bw+bf;
        var tabs = Math.abs(t)*2;

        var fh = false;

        for(var x = ix-tabs;x<=ix+tabs;x+=worldScale){
          for(var z = iz-tabs;z<=iz+tabs;z+=worldScale){
            var d = DistanceBetweenPoints(ix, iz, x, z);



            if ( d <= t) {
              //                            if ( !fh ) {
              //                                var chunkPos = WorldToCellCoordinates(x, z, chunkSize);
              //                                var chunkPosWorld = CellToWorldCoordinates(chunkPos.x, chunkPos.z, chunkSize);
              //                                var tiles = terrainHandler.GetChunkByWorldPosition(chunkPosWorld.x, chunkPosWorld.z).tiles;
              //                                for(var t in tiles) {
              //                                    if ( tiles[t].position.x == x && tiles[t].position.z == z ) {
              //                                        fh = tiles[t].position.y;
              //                                        break;
              //                                    }
              //                                }
              //                            }
              var h = 0;
              //var f = 1-(d / t);
              if ( levelEditor.editorGUI.wbMode == WorldBuilderModeEnum.FLATTEN ) {
                //if ( fh != false ) {
                if ( d <= bw ) {
                  h = levelEditor.editorGUI.flattenHeight;

                  levelEditor.SetTileHeight(x, z, h, false, true, true);
                }
              //}

              }
              else {

                if ( d > bw ) {
                  h = (1-((d-bw)/bf))*bh;
                }
                else {
                  h = bh;
                }

                h = Math.max(h, 0);
                h = Math.min(h, bh);

                levelEditor.SetTileHeight(x, z, levelEditor.editorGUI.wbMode == WorldBuilderModeEnum.DIG ? -h : h, true, true, true);
              }



            }
          }
        }


      }

    }
  }
  else if ( ironbane.player ) {

    if ( ironbane.player.dead ) return;

    if ( event.button === 0 ) {
      if (currentMouseToWorldData) {
        var position = currentMouseToWorldData.point;
        ironbane.player.AttemptAttack(position);
      }
    }
    else {

    }


  }

};
