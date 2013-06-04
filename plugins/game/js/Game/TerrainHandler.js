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
/*
 * Terrain exists out of tiles
 *
 *
 * Units: 1.0 = 1 meter = one '
 *
 */



// How big the server will send us a chunk
// Must be dividable by 2
var chunkSize =  le("globalEnable") ? 16 : 96+16;
//var chunkSize = 16;
var chunkHalf = chunkSize / 2;

//var chunkLoadRange = ISDEF(localStorage.chunkDistance) ? parseFloat(localStorage.chunkDistance) : chunkSize+16;
var chunkLoadRange = chunkSize+16;


// chunkLoadRange = 224;

//var previewLocation = new THREE.Vector3(40, 50, 20);
var previewLocation = new THREE.Vector3(0, 10, 0);
//var previewLocation = new THREE.Vector3(0, 0, 0);
var previewDistance = 15;
var previewHeight = 5;

//var cellLoadRange = 400;
//var cellUnloadRange = 1000;
//var chunkBufferRange = 1000;

var TerrainHandler = Class.extend({
  Init: function() {
    // Multidimensional array per x/y chunk that holds tiles
    this.chunks = {};

    this.previewZone = 1;
    this.zone = this.previewZone;

    // Add a chunk with a test tile
    // this.chunks.push(new Chunk(new THREE.Vector3(chunkHalf, 0, chunkHalf)));
    // this.chunks[0].AddTile(new THREE.Vector3(0.5, 0, 0.5), 11);
    // this.chunks[0].isLoaded = true;

    // [cellX][cellZ]
    this.world = {};

    this.chunkCheckTimer = 0.0;


    //this.LoadTiles();
    this.waterMesh = null;

    this.skybox = null;

    this.hasChunksLoaded = false;

    this.readyToReceiveUnits = false;

    this.isLoaded = false;

    this.lastOctreeBuildPosition = new THREE.Vector3(0, 1000000000, 0);

    this.currentMusic = "";
  },
  Destroy: function() {
    for(var c in this.chunks) this.chunks[c].RemoveMesh();
    this.chunks = {};
    this.world = {};
    this.isLoaded = false;
    this.hasChunksLoaded = false;
    this.zone = this.previewZone;

    if ( this.skybox ) this.skybox.Destroy();
    this.skybox = null;

    this.readyToReceiveUnits = false;


    particleHandler.RemoveAll();
  },
  Awake: function() {
    // Called after everything is loaded



    this.BuildWaterMesh();



    // Add a skybox

    this.skybox = new Skybox();

    // particleHandler.RemoveAll();

    if ( GetZoneConfig("enableClouds") ) {
      particleHandler.Add(ParticleTypeEnum.CLOUD, {});
    }

    var zoneMusicPiece = ChooseRandom(GetZoneConfig("music"));

    if ( socketHandler.loggedIn ) {
      if ( this.currentMusic != zoneMusicPiece ) {
        if ( this.currentMusic ) {
          soundHandler.FadeOut(this.currentMusic, 5.00);

          setTimeout(function() {
            soundHandler.FadeIn(zoneMusicPiece, 5.00) ;
          }, 5000);
        }
        else {
          soundHandler.FadeIn(zoneMusicPiece, 5.00) ;
        }

        this.currentMusic = zoneMusicPiece;
      }
    }
    else {
        if ( this.currentMusic ) {
          soundHandler.FadeOut(this.currentMusic, 5.00);
          this.currentMusic = "";
        }
    }


  },
  LoadCell: function(cellX, cellZ) {

    // ba('dont make too many ajax calls! if one is already busy, wait a frame before we send the next request. or try something else')

    terrainHandler.world[cellX][cellZ]['isLoading'] = true;

    terrainHandler.world[cellX][cellZ]['hasObjectsLoaded'] = false;

    var objectsFile = 'plugins/game/data/'+this.zone+'/'+cellX+'/'+cellZ+'/objects.json?'+(new Date()).getTime();
    $.getJSON(objectsFile, function(data) {
      terrainHandler.world[cellX][cellZ]['objects'] = data;
      console.log('Loaded: '+objectsFile);
      terrainHandler.world[cellX][cellZ]['hasObjectsLoaded'] = true;
    }).error(function() {
      terrainHandler.world[cellX][cellZ]['hasObjectsLoaded'] = true;
      console.warn('Not found: '+objectsFile);
    });



    if ( isEditor ) {

      var graphFile = 'plugins/game/data/'+this.zone+'/'+cellX+'/'+cellZ+'/graph.json?'+(new Date()).getTime();
      $.getJSON(graphFile, function(data) {
        terrainHandler.world[cellX][cellZ]['graph'] = data;
        console.log('Loaded graph: '+graphFile);
      }).error(function() {
        console.warn('No graph found: '+graphFile);
      });

    }


  },
  BuildWaterMesh: function() {
    //if ( debugging ) return;



    // Build the water mesh
    if ( this.waterMesh ) {
      ironbane.scene.remove(this.waterMesh);
    }

    if ( !GetZoneConfig('enableWater') ) return;

    var texture = textureHandler.GetTexture( 'plugins/game/images/tiles/'+GetZoneConfig('waterTexture')+'.png', true);
    texture.wrapS = THREE.RepeatWrapping;
    texture.wrapT = THREE.RepeatWrapping;
    texture.repeat.x = 1000;
    texture.repeat.y = 1000;

    var texture2 = textureHandler.GetTexture( 'plugins/game/images/tiles/'+GetZoneConfig('waterTextureGlow')+'.png', true);
    texture2.wrapS = THREE.RepeatWrapping;
    texture2.wrapT = THREE.RepeatWrapping;
    texture2.repeat.x = 1000;
    texture2.repeat.y = 1000;

    var planeGeo = new THREE.PlaneGeometry(100, 100, 30, 30);
    var uniforms = {
      uvScale : {
        type: 'v2',
        value: new THREE.Vector2(0.02,0.02)
      },
      size : {
        type: 'v2',
        value: new THREE.Vector2(1,1)
      },
      hue : {
        type: 'v3',
        value: new THREE.Vector3(1,1,1)
      },
      vSun : {
        type: 'v3',
        value: new THREE.Vector3(0,0,0)
      },
      texture1 : {
        type: 't',
        value: texture
      },
      texture2 : {
        type: 't',
        value: texture2
      },
      scrollSpeed : {
        type: 'v2',
        value: new THREE.Vector2(1,1)
      },
      time : {
        type: 'f',
        value: 0.0
      }
    //            camPos : {
    //                type: 'v3',
    //                value: ironbane.camera.position
    //            }
    };

    var shaderMaterial = new THREE.ShaderMaterial({
      uniforms : uniforms,
      vertexShader : $('#vertex_water').text(),
      fragmentShader : $('#fragment_water').text(),
      transparent: true
    //alphaTest: 0.5
    });

    //    this.waterMesh = new THREE.Mesh(planeGeo,
    //      textureHandler.GetTexture('plugins/game/images/tiles/'+
    //      GetZoneConfig('waterTexture')+'.png',
    //      false,
    //      {
    //        transparent:true,
    //        alphaTest:0.01
    //      },
    //      {
    //        repeat: new THREE.Vector2(50, 50)
    //      }));

    this.waterMesh = new THREE.Mesh(planeGeo, shaderMaterial);

    this.waterMesh.rotation.x = -Math.PI/2;

    this.waterMesh.position.y = GetZoneConfig('waterLevel');
    //this.mesh.p

    this.waterMesh.geometry.dynamic = true;

    ironbane.scene.add(this.waterMesh);
  },
  UpdateCells: function(dTime) {

    var p = this.GetReferenceLocation();

    var cp = WorldToCellCoordinates(p.x, p.z, cellSize);

    debug.SetWatch('Player Cell X', cp.x);
    debug.SetWatch('Player Cell Z', cp.z);


    var chp = WorldToCellCoordinates(p.x, p.z, chunkSize);

    debug.SetWatch('Player Chunk X', chp.x);
    debug.SetWatch('Player Chunk Z', chp.z);

    debug.SetWatch('Unit count', ironbane.unitList.length);


    // Load cells around us



    var isLoaded = true;

    var loadRange = cellLoadRange;

    if ( !socketHandler.inGame ) loadRange = 1;


    // Do a pre-check, and if some are already loading we just return silently
    if ( ironbane.showingGame ) {
      for(var x=cp.x-loadRange;x<=cp.x+loadRange;x+=1){
        if ( typeof this.world[x] == 'undefined' ) this.world[x] = {};

        for(var z=cp.z-loadRange;z<=cp.z+loadRange;z+=1){
          if ( typeof this.world[x][z] == 'undefined' ) this.world[x][z] = {
            isLoading: false
          };

          if ( typeof this.world[x][z].objects == 'undefined' ) {

            if ( this.world[x][z]['isLoading'] ) {

              if ( !this.world[x][z]['hasObjectsLoaded'] ) {
                return;
              }

            }

          }
        }
      }
    }




    for(var x=cp.x-loadRange;x<=cp.x+loadRange;x+=1){
      if ( typeof this.world[x] == 'undefined' ) this.world[x] = {};

      for(var z=cp.z-loadRange;z<=cp.z+loadRange;z+=1){

        if ( typeof this.world[x][z] == 'undefined' ) this.world[x][z] = {
          isLoading: false
        };

        if ( typeof this.world[x][z].objects == 'undefined' ) {

          if ( !this.world[x][z]['isLoading'] ) {
            this.LoadCell(x, z);
          }

          if ( !this.world[x][z]['hasObjectsLoaded'] ) {
            isLoaded = false;
          }

        }


      }
    }

    if ( !this.isLoaded && isLoaded ) {
      this.isLoaded = true;

      this.Awake();
    }

  //        debug.SetWatch('this.isLoaded', this.isLoaded);
  //        debug.SetWatch('this.hasChunksLoaded', this.hasChunksLoaded);

  //        for(var x=0;x<=0;x+=cellSize){
  //            if ( typeof this.world[x] == 'undefined' ) this.world[x] = {};
  //
  //            for(var z=0;z<=0;z+=cellSize){
  //
  //                if ( typeof this.world[x][z] == 'undefined' ) this.world[x][z] = { isLoading: false };
  //
  //                if ( typeof this.world[x][z]['terrain'] == 'undefined' ) {
  //
  //                    if ( !this.world[x][z]['isLoading'] ) {
  //
  //                        this.LoadCell(x, z);
  //
  //                    }
  //                }
  //
  //
  //            }
  //        }

  },
  GetChunkByAccurateWorldPosition: function(x, z) {
    // Op welke chunk bevind ik me?
    //        x = Math.floor(x / chunkSize);
    //        z = Math.floor(z / chunkSize);
    //



    var cp = WorldToCellCoordinates(x, z, chunkSize);
    cp = CellToWorldCoordinates(cp.x, cp.z, chunkSize);

    var id = cp.x+'-'+cp.z;

    if ( typeof this.chunks[id] == 'undefined'  ) {
      this.chunks[id] = new Chunk(new THREE.Vector3(cp.x, 0, cp.z));
    }

    return this.chunks[id];
  },
  GetChunkByWorldPosition: function(x, z) {
    // Op welke chunk bevind ik me?
    //        x = Math.floor(x / chunkSize);
    //        z = Math.floor(z / chunkSize);

    var id = x+'-'+z;

    if ( typeof this.chunks[id] == 'undefined' ) {
      this.chunks[id] = new Chunk(new THREE.Vector3(x, 0, z));
    }

    return this.GetChunk(id);
  },
  GetChunk: function(id) {
    if ( typeof this.chunks[id] == 'undefined' ) {
      this.chunks[id] = new Chunk();
    }
    return this.chunks[id];
  },
  GetReferenceLocation: function() {
    return this.GetReferenceLocationNoClone().clone();
  },
  GetReferenceLocationNoClone: function() {
    var p;

    if ( ironbane.player ) {
      p = ironbane.player.position;
    }
    else if ( socketHandler.spawnLocation ) {
      p = socketHandler.spawnLocation;
    }
    else {
      p = previewLocation;
    }

    return p;
  },
  // RayTest: function(ray, testMeshesNearPosition, noTerrain, noMeshes,
  //   extraRange, reverseRaySortOrder) {
  RayTest: function(ray, options) {

    options = options || {};

    var noTerrain = _.isUndefined(options.noTerrain) ?
                      false : options.noTerrain;
    var noMeshes = _.isUndefined(options.noMeshes) ?
                      false : options.noMeshes;
    var extraRange = _.isUndefined(options.extraRange) ?
                      1.0 : options.extraRange;
    var reverseRaySortOrder = _.isUndefined(options.reverseRaySortOrder) ?
                      false : options.reverseRaySortOrder;

    var unitReference = _.isUndefined(options.unitReference) ?
                      null : options.unitReference;

    var unitRayName = options.unitRayName;

    var testMeshesNearPosition = options.testMeshesNearPosition;

    if ( le("mpIgnoreOtherModels") ) {
      noMeshes = true;
    }


    // To optimize, we keep track of the last mesh & face that had a succesful
    // hit. In our use case, it is very likely that the next hit will be the
    // same face/object
    var intersects = [];

    if ( unitReference ) {
      if ( !unitReference.lastRayData ) {
        unitReference.lastRayData = {};
      }

      if ( unitReference.lastRayData[unitRayName] ) {
        var rayData = unitReference.lastRayData[unitRayName];
        // Check for the stuff that's inside
        // Do a simple raycast on one plane
        var subIntersects = ray.intersectObject( rayData.mesh,
          false, rayData.faceId );

        intersects = intersects.concat(subIntersects);

        if ( intersects.length > 0 ) return intersects;
      }
    }

    var meshList = []

    if ( !noMeshes ) {
      for(var u=0;u<ironbane.unitList.length;u++) {
        var unit = ironbane.unitList[u];

        if ( !(unit instanceof Mesh) ) continue;

        if ( !unit.boundingSphere ) continue;

        if ( unit.InRangeOfPosition(testMeshesNearPosition, unit.boundingSphere.radius+extraRange) ) {
          meshList.push(unit);
        }
      }
    }


    if ( !noMeshes ) {
      for(var m=0;m<meshList.length;m++) {
        var subIntersects = ray.intersectOctreeObjects( meshList[m].octree.objects )

        intersects = intersects.concat(subIntersects);
      }
    }


    if ( !noTerrain ) {

      if ( DistanceSq(this.lastOctreeBuildPosition, ironbane.player.position) > 10*10 ) {
          this.lastOctreeBuildPosition = ironbane.player.position.clone();
          this.octreeResults = terrainHandler.skybox.terrainOctree.search(this.lastOctreeBuildPosition, 20, true);
      }

      var subIntersects = ray.intersectOctreeObjects( this.octreeResults );

      //var subIntersects = ray.intersectOctreeObjects( terrainHandler.skybox.terrainOctree.objects );
      intersects = intersects.concat(subIntersects);
    }


    if ( reverseRaySortOrder ) {
      intersects.sort(function(a,b) { return b.distance - a.distance } );
    }
    else {
      intersects.sort(function(a,b) { return a.distance - b.distance } );
    }

    if ( intersects.length > 0 && unitReference ) {
      unitReference.lastRayData[unitRayName] = {
        mesh: intersects[0].object,
        faceId: intersects[0].faceIndex
      };
    }

    return intersects;
  },
  Tick: function(dTime) {
    if ( this.waterMesh ) {
      this.waterMesh.material.uniforms.time.value = (new Date().getTime() - ironbane.startTime)/1000.0;

      if ( this.skybox ) this.waterMesh.material.uniforms.vSun.value.copy(this.skybox.sunVector);

      var p = this.GetReferenceLocationNoClone();
      var cellPos = WorldToCellCoordinates(p.x, p.z, 10);
      var worldPos = CellToWorldCoordinates(cellPos.x, cellPos.z, 10);

      var id = worldPos.x+'-'+worldPos.z;

      terrainHandler.waterMesh.position.x = worldPos.x;
      terrainHandler.waterMesh.position.z = worldPos.z;

    }

    if ( !this.readyToReceiveUnits && this.isLoaded && this.hasChunksLoaded && socketHandler.loggedIn ) {

      // soundHandler.Play("enterGame");

      this.readyToReceiveUnits = true;

      // Bring it on!
      socketHandler.socket.emit('readyToReceiveUnits', true, function (reply) {
        if ( ISDEF(reply.errmsg) ) {
          hudHandler.MessageAlert(reply.errmsg);
          return;
        }
      });


    }

    this.UpdateCells(dTime);
    this.UpdateChunks(dTime);
  },
  UpdateChunks: function(dTime) {

    this.chunkCheckTimer += dTime;

    if ( this.skybox ) this.skybox.Tick(dTime);


    // If one of us is still loading, don't tick further!
    var goFurther = true;
    _.each(this.chunks, function(chunk) {
      if ( chunk.tilesToAdd > 0 || chunk.modelsToBuild > 0 ) {
        goFurther = false;
      }
    });

    if ( !goFurther && ironbane.showingGame ) return;


    if ( !levelEditor.ready || (levelEditor.ready && !levelEditor.editorGUI.globalEnable) ) {
      if ( this.chunkCheckTimer < 0.2 ) return;
    }

    this.chunkCheckTimer = 0.0;

    for(var c in this.chunks) {
      this.chunks[c].Tick(dTime);
    }

    // We ask the server for a chunk that has tiles near a point we give


    // Check if there are new chunks available for us using chunkLoadRange and request them
    // Also see if there are chunks that are out of range using chunkUnloadRange and unload them



    // Remove chunks
    for(var c in this.chunks) {
      if ( this.chunks[c].removeNextTick ) {
        delete this.chunks[c];
      //newChunkListArray.push(this.chunks[x]);
      }
    }


    var p = this.GetReferenceLocation();

    // p.x = (Math.floor(p.x / chunkSize) * chunkSize) + chunkHalf;
    // p.z = (Math.floor(p.z / chunkSize) * chunkSize) + chunkHalf;

    // First see if we can unload chunks


    var chunkUnloadRange = chunkLoadRange+1;
    for(var c in this.chunks) {
      var chunk = this.chunks[c];
      if ( !chunk.removeNextTick ) {
        var pground = p.clone();
        pground.y = 0;
        var distance = pground.subSelf(chunk.position).lengthSq();

        if ( distance > chunkUnloadRange*chunkUnloadRange ) {
          chunk.RemoveMesh();

          chunk.removeNextTick = true;
        }
      }
    }

    p = this.GetReferenceLocation();
    //        p.x = (Math.round(p.x / chunkSize) * chunkSize) + 0;
    //        p.z = (Math.round(p.z / chunkSize) * chunkSize) + 0;
    var pcp = WorldToCellCoordinates(p.x, p.z, chunkSize);

    var loadcount = 1;


    this.hasChunksLoaded = true;


    var bogusLoadRange = 10;

    for(var x=pcp.x-bogusLoadRange;x<=pcp.x+bogusLoadRange;x+=1){
      for(var z=pcp.z-bogusLoadRange;z<=pcp.z+bogusLoadRange;z+=1){


        var pcp2 = CellToWorldCoordinates(x, z, chunkSize);


        var chunk = this.chunks[pcp2.x+'-'+pcp2.z];

        var distance = p.clone().subSelf(new THREE.Vector3(pcp2.x, 0, pcp2.z)).lengthSq();

        if ( distance > chunkLoadRange*chunkLoadRange ) continue;


        if ( !ISDEF(this.chunks[pcp2.x+'-'+pcp2.z]) || !this.chunks[pcp2.x+'-'+pcp2.z].isAddedToWorld) {
          this.hasChunksLoaded = false;
        }


          terrainHandler.GetChunkByWorldPosition(pcp2.x, pcp2.z);



      }
    }



  }

});


var terrainHandler = new TerrainHandler();
