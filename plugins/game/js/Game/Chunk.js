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

var amountOfChunksRequested = 0;

var invisibleTile = 1653;


var planeGeoChunk = new THREE.PlaneGeometry(worldScale, worldScale, 1, 1);
// var planeGeo = null;

var Chunk = Class.extend({
    Init: function(position) {

        this.position = position;

        this.isAddedToWorld = false;

        this.hasTilesLoaded = false;
        this.hasMeshesLoaded = false;
        this.hasStartedAddingTiles = false;

        this.removeNextTick = false;

        this.tiles = [];
        this.objects = [];
        this.waypointMeshes = [];

        // When farther away from the player, simplify
        this.quality = worldScale;

        this.octree = new THREE.Octree();


        // Used to construct the model geometry, so we can still cast shadows
        this.modelGeometry = null;
        this.modelMesh = null;

        this.terrainGeo = null;
        this.mesh = null;

        // Numbers of models that must be 0 before we can add the chunk mesh
        this.modelsToBuild = 0;
        this.tilesToAdd = 0;

    },
    Tick: function(dTime) {
        if ( !this.isAddedToWorld ) {
            if ( !this.hasTilesLoaded && !GetZoneConfig("noTerrain") ) {
                // Request a chunk from our local cache
                // We have all terrain preloaded to make sure the server is not overbalasted



                var cellPos = WorldToCellCoordinates(this.position.x, this.position.z, cellSize);

                //var chunkPos = WorldToCellCoordinates(this.position.x, this.position.z, chunkSize);

                var cx = this.position.x;
                var cz = this.position.z;

                if ( typeof terrainHandler.world[cellPos.x] == 'undefined' ) {
                    //console.log('Cell X '+cellPos.x+' does not exist!');
                    return;
                }
                if ( typeof terrainHandler.world[cellPos.x][cellPos.z] == 'undefined' ) {
                    //console.log('Cell Z '+cellPos.z+' does not exist!');
                    return;
                }

                if ( typeof terrainHandler.world[cellPos.x][cellPos.z]['terrain'] == 'undefined' ) {
                    //console.log('Terrain for '+cellPos.x+','+cellPos.z+' not available!');
                    return;
                }

                if ( typeof terrainHandler.world[cellPos.x][cellPos.z]['objects'] == 'undefined' ) {
                    //console.log('Terrain for '+cellPos.x+','+cellPos.z+' not available!');
                    return;
                }

                for(var x=cx-chunkHalf;x<cx+chunkHalf;x+=worldScale){
                    if ( typeof terrainHandler.world[cellPos.x][cellPos.z]['terrain'][x] != 'undefined') {
                        for(var z=cz-chunkHalf;z<cz+chunkHalf;z+=worldScale){
                            // We only need the Y and tile data from cache here
                            if ( typeof terrainHandler.world[cellPos.x][cellPos.z]['terrain'][x][z] != 'undefined') {
                                this.tiles.push({position:new THREE.Vector3(x,
                                    terrainHandler.world[cellPos.x][cellPos.z]['terrain'][x][z].y, z),
                                image:terrainHandler.world[cellPos.x][cellPos.z]['terrain'][x][z].t});
                            }
                        }
                    }
                }






                this.hasTilesLoaded = true;


            }
            else if ( !this.hasMeshesLoaded ) {


                // (function(chunk){
                //     setTimeout(function() {
                //         chunk.LoadObjects();
                //     }, 10000);
                // })(this);

                this.LoadObjects();

                this.hasMeshesLoaded = true;


            }
            else if ( !this.removeNextTick ) {

                if ( this.hasStartedAddingTiles ) {

                    var ready = true;
                    for(var m=0;m<this.objects.length;m++) {
                        if ( !this.objects[m].mesh ) {
                            ready = false;
                            break;
                        }
                    }

                    if ( terrainHandler.isLoaded
                        && this.modelsToBuild <= 0
                        && this.tilesToAdd <= 0
                        && ready ) {
                        this.AddMesh();
                    }

                }
                else {

                    if ( this.terrainGeo ) this.terrainGeo.deallocate();

                    this.terrainGeo = new THREE.Geometry();




                    for(var j=0;j<this.tiles.length;j++){

                        this.tilesToAdd++;

                        // // if ( terrainHandler.readyToReceiveUnits ) {

                        //     (function(tile, chunk, time){
                        //         setTimeout(function() {
                        //             chunk.BuildTile(tile);
                        //         }, time);
                        //     })(this.tiles[j], this, 5*this.tilesToAdd);

                        // }
                        // else {
                        //     this.BuildTile(this.tiles[j]);
                        // }


                        if ( !ironbane.showingGame ) {
                            this.BuildTile(this.tiles[j]);
                        }



                    //THREE.GeometryUtils.triangulateQuads( terrainGeo);
                    }

                    if ( ironbane.showingGame ) {
                        timedChunk(this.tiles, function(tile) {
                            if ( GetZoneConfig("noTerrain") ) return;



                            this.BuildTile(tile);

                            // console.log("building tile")
                        }, this, function() {
                            // bm("all done!");
                        });
                    }

                    this.hasStartedAddingTiles = true;
                }

            }
        }
        else {





        //            if ( ironbane.player ) {
        //                this.mesh.geometry.materials[0].uniformsList[3][0].value = terrainHandler.GetReferenceLocationNoClone();
        //
        //                // Calculate and alter quality if desired
        ////                var oldQuality = this.quality;
        ////
        ////
        ////                if ( ironbane.player.InRangeOfPosition(this.position, 40) ) this.quality = 1;
        ////                else if ( ironbane.player.InRangeOfPosition(this.position, 60) ) this.quality = 2;
        ////                else this.quality = 5;
        ////
        ////
        ////
        ////                if ( this.quality != oldQuality ) this.ReloadTerrainOnly();
        ////
        //                //this.mesh.geometry.materials[0].uniformsList[3][0].value = ironbane.player.position;
        //            }
        // Check when all the 3D meshes are loaded so we know when to add the player



        //this.mesh.geometry.dynamic = true;
        //                    this.mesh.geometry.__dirtyVertices = true;
        //                    this.mesh.geometry.vertices[0].position.y += dTime/10;
        }
    },
    AddMesh: function() {
        // Loaded, but not added to the world yet
        // Let's do that and set isAddedToWorld


          this.FinalizeMesh();
          ironbane.scene.add(this.mesh);




          // if ( socketHandler.inGame )  {
            this.octree.add( this.mesh, true );

            this.octree.add( this.models, true );
          // }

          //        this.mesh.visible = false;
          //        this.mesh.depthWrite = false;
          //        this.mesh.depthTest = false;

          ironbane.renderer.shadowMapEnabled = true;
          ironbane.renderer.shadowMapAutoUpdate = true;
          ironbane.renderer.shadowMapSoft = false;



          // console.log('Add chunk: '+this.position.ToString());

          if ( ISDEF(ironbane.shadowMapUpdateTimer) ) {
            //log("clearTimer");
            clearTimeout(ironbane.shadowMapUpdateTimer);
          }
          ironbane.shadowMapUpdateTimer = setTimeout(function() {
                        ironbane.renderer.shadowMapAutoUpdate = false;
          }, 100);


        this.isAddedToWorld = true;
    },
    RemoveMesh: function() {


        if ( this.modelGeometry ) {
            _.each(this.modelGeometry.materials, function(material) {
              material.deallocate();
            });

            this.modelGeometry.deallocate();
        }
      if ( this.models ) {
//        this.mesh.geometry.deallocate();
//        this.mesh.deallocate();
//        //this.mesh.material.deallocate();
//
//        ironbane.renderer.deallocateObject( this.mesh );
        //renderer.deallocateTexture( texture );

        ironbane.octree.remove( this.models );

        ironbane.scene.remove(this.models);
    }

      if ( this.mesh ) {
//        this.mesh.geometry.deallocate();
//        this.mesh.deallocate();
//        //this.mesh.material.deallocate();
//
//        ironbane.renderer.deallocateObject( this.mesh );
        //renderer.deallocateTexture( texture );

        ironbane.octree.remove( this.mesh );

        ironbane.scene.remove(this.mesh);

        _.each(this.mesh.geometry.materials, function(material) {
          material.deallocate();
        });

        this.mesh.geometry.deallocate();




        this.mesh.deallocate();





//        this.mesh.traverse( function ( object ) {
//            ironbane.renderer.deallocateObject( object );
//            object.geometry.deallocate();
//            //object.material.deallocate();
//            object.deallocate();
//        } );
      }

        for(var o=0;o<this.objects.length;o++) {
            this.objects[o].Destroy();
            // Remove from unitList
            ironbane.unitList = _.without(ironbane.unitList, this.objects[o]);
        }



        this.objects = [];

        // console.log('Remove chunk: '+this.position.ToString());

        this.isAddedToWorld = false;
    },
    ScheduleCompute: function() {

        if ( ISDEF(this.computeTimer) ) {
            //console.log('clearTimer');
            clearTimeout(this.computeTimer);
        }
        this.computeTimer = setTimeout(
            (function(mesh) {
                return function() {
                    mesh.geometry.computeCentroids();
                    mesh.geometry.computeFaceNormals();
                //mesh.geometry.computeVertexNormals();
                }
            })(this.mesh), 500);



    },
    ReloadWaypointsOnly: function() {



        for(var o=0;o<this.objects.length;++o) {

            if ( !(this.objects[o] instanceof Waypoint) ) continue;

            this.objects[o].Destroy();
            // Remove from unitList

            ironbane.unitList = _.without(ironbane.unitList, this.objects[o]);

            this.objects.splice(o--, 1);
        }

        for(var m=0;m<this.waypointMeshes.length;m++) {
            ironbane.scene.remove(this.waypointMeshes[m]);
        }
        this.waypointMeshes = [];

        this.LoadObjects(true);
    },
    ReloadObjectsOnly: function() {



        for(var o=0;o<this.objects.length;o++) {
            this.objects[o].Destroy();
            // Remove from unitList

            ironbane.unitList = _.without(ironbane.unitList, this.objects[o]);
        }

        for(var m=0;m<this.waypointMeshes.length;m++) {
            ironbane.scene.remove(this.waypointMeshes[m]);
        }
        this.waypointMeshes = [];

        this.objects = [];

        this.LoadObjects();
    },
    ReloadTerrainOnly: function() {
        var mesh = this.GetMesh();
        ironbane.scene.remove(this.mesh);
        this.mesh = mesh;
        ironbane.scene.add(this.mesh);
    },
    Reload: function() {
        if ( this.isAddedToWorld ) {
            this.RemoveMesh();
        }
    //this.AddMesh();
    },
    AddTile: function (p, tile) {
        this.tiles.push(new Tile(p, tile));
    },
    //        FindTile: function(pos) {
    //
    //			// Search in this list of chunks
    //			var chunkList = [];
    //
    //			// start with ourselves
    //			chunkList.push(this);
    //
    //			var chunk = null;
    //
    //			chunk = terrainHandler.chunks[(this.position.x+chunkSize)+'-'+(this.position.z+chunkSize)];
    //			if ( chunk ) chunkList.push(chunk);
    //
    //			chunk = terrainHandler.chunks[(this.position.x+chunkSize)+'-'+(this.position.z-chunkSize)];
    //			if ( chunk ) chunkList.push(chunk);
    //
    //			chunk = terrainHandler.chunks[(this.position.x-chunkSize)+'-'+(this.position.z+chunkSize)];
    //			if ( chunk ) chunkList.push(chunk);
    //
    //			chunk = terrainHandler.chunks[(this.position.x-chunkSize)+'-'+(this.position.z-chunkSize)];
    //			if ( chunk ) chunkList.push(chunk);
    //
    //			for (c = 0; c < chunkList.length; c++) {
    //				for (i = 0; i < chunkList[c].tiles.length; i++) {
    //					//if ( this.tiles[i].position.equals(pos) ) return this.tiles[i];
    //					if ( chunkList[c].tiles[i].position.x == pos.x && chunkList[c].tiles[i].position.z == pos.z ) return chunkList[c].tiles[i];
    //				}
    //			}
    //
    //			console.warn('tile not found: '+pos.ToString());
    //			// We didn't find anything because the tile we're looking for is in another chunk, or it doesn't exist
    //
    //
    //            return null;
    //        },
    BuildTile: function(tile) {

        this.tilesToAdd--;

        var cellPos = WorldToCellCoordinates(this.position.x, this.position.z, cellSize);

        var cx = this.position.x;
        var cz = this.position.z;

        var planeMat;

        if ( !(showEditor && levelEditor.editorGUI.wpDisplaySpecials) && tile.image == invisibleTile ) return;


        if ( showEditor && levelEditor.editorGUI.tbEnableTransparency ) {
            planeMat = textureHandler.GetFreshTexture(tilesPath+''+tile.image+'.png', false, {
                seeThrough:true
            //vertexShader:"vertex_world"
            });
        }
        else {
            planeMat = textureHandler.GetTexture(tilesPath+''+tile.image+'.png', false, {
                seeThrough:false,
                uvScaleX: 1,
                uvScaleY: 1,
                useLighting: true
            //vertexShader:"vertex_world"
            });
        }

        if ( stealth ) planeMat.wireframe = true;

        planeGeoChunk.materials = [planeMat];
        planeGeoChunk.faces[0].materialIndex = 0;


        // 4 vertices: 0, 1, 2, 3
        // 8 vertices: 0, 2, 6, 8

        var tx = tile.position.x;
        var tz = tile.position.z;

        for(var cx=cellPos.x-1;cx<=cellPos.x+1;cx++){
            for(var cz=cellPos.z-1;cz<=cellPos.z+1;cz++){


                if ( typeof terrainHandler.world[cx] == 'undefined' ) continue;
                if ( typeof terrainHandler.world[cx][cz] == 'undefined' ) continue;
                if ( typeof terrainHandler.world[cx][cz]['terrain'] == 'undefined' ) continue;

                var estimatedQuality = this.quality;


                if ( typeof terrainHandler.world[cx][cz]['terrain'][tx] != 'undefined' ) {
                    if ( typeof terrainHandler.world[cx][cz]['terrain'][tx][tz] != 'undefined' ) {
                        var height = terrainHandler.world[cx][cz]['terrain'][tx][tz].y;
                        planeGeoChunk.vertices[0].z = height;
                    }
                }
                if ( typeof terrainHandler.world[cx][cz]['terrain'][(tx+estimatedQuality)] != 'undefined' ) {
                    if ( typeof terrainHandler.world[cx][cz]['terrain'][(tx+estimatedQuality)][tz] != 'undefined' ) {
                        var height = terrainHandler.world[cx][cz]['terrain'][(tx+estimatedQuality)][tz].y;
                        planeGeoChunk.vertices[1].z = height;
                    }
                }
                if ( typeof terrainHandler.world[cx][cz]['terrain'][tx] != 'undefined' ) {
                    if ( typeof terrainHandler.world[cx][cz]['terrain'][tx][(tz+estimatedQuality)] != 'undefined' ) {
                        var height = terrainHandler.world[cx][cz]['terrain'][tx][(tz+estimatedQuality)].y;
                        planeGeoChunk.vertices[2].z = height;
                    }
                }
                if ( typeof terrainHandler.world[cx][cz]['terrain'][(tx+estimatedQuality)] != 'undefined' ) {
                    if ( typeof terrainHandler.world[cx][cz]['terrain'][(tx+estimatedQuality)][(tz+estimatedQuality)] != 'undefined' ) {
                        var height = terrainHandler.world[cx][cz]['terrain'][(tx+estimatedQuality)][(tz+estimatedQuality)].y;
                        planeGeoChunk.vertices[3].z = height;
                    }
                }
            }
        }


        var plane = new THREE.Mesh(planeGeoChunk,planeMat);

        //plane.geometry.dynamic = true;

        plane.rotation.x = -Math.PI/2;

        plane.position.x = tx+(0.5*this.quality);
        plane.position.z = tz+(0.5*this.quality);
        //plane.position = this.tiles[j].position;

        plane.material.side = THREE.DoubleSide;

        //plane.material.wireframe = true;

        THREE.GeometryUtils.merge(this.terrainGeo, plane);

        plane.deallocate();



    },
    FinalizeMesh: function() {


        this.models = new THREE.Mesh(this.modelGeometry,  new THREE.MeshFaceMaterial());
        this.models.castShadow = true;
        ironbane.scene.add(this.models);

        //bm("getmesh called");




        // planeGeo.deallocate();

        var terrainMat = new THREE.MeshFaceMaterial();

        // this.terrainGeo.mergeVertices();
        THREE.GeometryUtils.triangulateQuads(this.terrainGeo);

        var terrainMesh = new THREE.Mesh(this.terrainGeo, terrainMat);

        terrainMesh.receiveShadow = true;

        // terrainMesh.geometry.computeCentroids();
        // terrainMesh.geometry.computeFaceNormals();

        terrainMesh.geometry.dynamic = true;

        terrainMesh.material.side = THREE.DoubleSide;
        //        terrainMesh.material.transparent = true;
        //        terrainMesh.material.opacity = 0.5;
        //        terrainMesh.boundingBox = null;
        //        terrainMesh.boundingSphere = null;


        this.mesh = terrainMesh;


    },
    LoadObjects: function(waypointsOnly) {




        this.modelGeometry = new THREE.Geometry();

        // We just want to load the objects in memory, not actually add them to
        // the scene. Later, merge in the geometry with the terrain mesh

        waypointsOnly = waypointsOnly || false;

        var cellPos = WorldToCellCoordinates(this.position.x, this.position.z, cellSize);

        var cx = this.position.x;
        var cz = this.position.z;

        if ( !ISDEF(terrainHandler.world[cellPos.x][cellPos.z]['objects']) ) return;

        for(var o=0;o<terrainHandler.world[cellPos.x][cellPos.z]['objects'].length;o++) {


            // continue;

            if ( waypointsOnly ) continue;

            var gObject = terrainHandler.world[cellPos.x][cellPos.z]['objects'][o];



            var pos = new THREE.Vector3(gObject.x, gObject.y, gObject.z);

            if ( pos.x < cx-chunkHalf ||
                pos.x > cx+chunkHalf ||
                pos.z < cz-chunkHalf ||
                pos.z > cz+chunkHalf ) continue;





            var unit = null;


            var param = gObject.p;

            // metadata could be undefined, but the Mesh class should handle that
            var metadata = gObject.metadata ? gObject.metadata : {};

            var meshData = preMeshes[param] ? preMeshes[param] : null;

            var rotation = new THREE.Vector3(gObject.rX, gObject.rY, gObject.rZ);

            if ( meshData && (meshData.special || le("globalEnable")) ) {

                switch (gObject.t) {
                    case UnitTypeEnum.BILLBOARD:
                        unit = new Billboard(pos, gObject.r, 0, gObject.p);
                        break;
                    case UnitTypeEnum.MESH:
                        //if ( !debugging ) {
                        unit = new Mesh(pos, rotation, 0, gObject.p, metadata);


                        //}
                        break;
                }

                if ( unit ) {
                    ironbane.unitList.push(unit);
                    this.objects.push(unit);
                }

            }
            else {

                this.modelsToBuild++;

                if ( !meshData ) {
                  meshData = preMeshes[0];
                }


                var filename = (meshData['filename'].split("."))[0]+".js";

                var model = meshPath + filename;



                (function(chunk, pos, rotation, metadata, meshData, param){
                meshHandler.Load(model, function(geometry) {

                    // setTimeout(function() {



                        var geometry = meshHandler.SpiceGeometry(geometry, rotation,
                            metadata, meshData, param, false);

                        // geometry.position = pos;

                        _.each(geometry.vertices, function(v) {
                            v.addSelf(pos);
                        });

                        // // Merge it with the chunk geometry we have so far
                        // THREE.GeometryUtils.merge( chunk.modelGeometry, geometry );
                        THREE.GeometryUtils.merge( chunk.modelGeometry, geometry );
                        // ironbane.worker.postMessage({
                        //     geoA: chunk.modelGeometry,
                        //     geoB: geometry
                        // });

                        geometry.deallocate();

                        // Ready! Decrease modelsToBuild
                        chunk.modelsToBuild--;

                    // }, chunk.modelsToBuild*100);

                }, meshData['scale']);
                })(this, pos, rotation, metadata, meshData, param);



            }



        // Keep track of the ID's in a list of the chunk
        }

        if ( showEditor && levelEditor.editorGUI.enablePathPlacer ) {

            var graph = terrainHandler.world[cellPos.x][cellPos.z]['graph'];

            if ( graph['nodes'] !== undefined ) {
                for(var n=0;n<graph['nodes'].length;n++) {
                    var node = graph['nodes'][n];

                    var pos = ConvertVector3(node.pos);

                    if ( pos.x < cx-chunkHalf ||
                        pos.x > cx+chunkHalf ||
                        pos.z < cz-chunkHalf ||
                        pos.z > cz+chunkHalf ) continue;

                    var texture = "misc/waypoint";
                    if ( levelEditor.selectedNode && levelEditor.selectedNode['id'] == parseInt(node['id']) ) {
                        texture = "misc/waypoint_red";
                    }

                    var nodeID = parseInt(node['id']);
                    var unit = new Waypoint(pos, node);

                    if ( unit ) {
                        ironbane.unitList.push(unit);
                        this.objects.push(unit);

                        for (var e=0;e<node['edges'].length;e++ ) {
                            var edge = node['edges'][e];

                            // Find the node in adjacent cells

                            var p = this.position.clone();
                            var cp = WorldToCellCoordinates(p.x, p.z, cellSize);
                            // Load cells around us

                            this.isLoaded = true;

                            for(var x=cp.x-1;x<=cp.x+1;x+=1){
                                for(var z=cp.z-1;z<=cp.z+1;z+=1){

                                    if ( terrainHandler.world[x] === undefined ) continue;
                                    if ( terrainHandler.world[x][z] === undefined ) continue;
                                    if ( terrainHandler.world[x][z]['graph'] === undefined ) continue;
                                    if ( terrainHandler.world[x][z]['graph']['nodes'] === undefined ) continue;

                                    var subnodes = terrainHandler.world[x][z]['graph']['nodes'];

                                    for( var sn=0;sn<subnodes.length;sn++ ) {

                                        if ( edge == subnodes[sn]['id'] ) {
                                            var subpos = ConvertVector3(subnodes[sn]['pos']);
                                            var vec = subpos.subSelf(pos);
                                            if ( !vec.isZero() ) {
                                                var aH = new THREE.ArrowHelper(vec, pos.clone().addSelf(new THREE.Vector3(0, 0.5, 0)), vec.length()-1, 0x00FFFF);
                                                this.waypointMeshes.push(aH);
                                                ironbane.scene.add(aH);
                                            }

                                        }

                                    }

                                }
                            }
                        }





                    }


                }
            }
        // Todo: edges

        }

    }
});
