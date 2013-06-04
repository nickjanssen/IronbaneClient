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

var Chunk = Class.extend({
    Init: function(position) {

        this.position = position;

        this.isAddedToWorld = false;

        this.hasMeshesLoaded = false;

        this.removeNextTick = false;

        this.objects = [];
        this.waypointMeshes = [];

        this.octree = new THREE.Octree();

        // Used to construct the model geometry, so we can still cast shadows
        this.modelGeometry = null;
        this.modelMesh = null;

        // Numbers of models that must be 0 before we can add the chunk mesh
        this.modelsToBuild = 0;

    },
    Tick: function(dTime) {
        if ( !this.isAddedToWorld ) {
            if ( !this.hasMeshesLoaded ) {


                this.LoadObjects();

                this.hasMeshesLoaded = true;


            }
            else if ( !this.removeNextTick ) {

                if ( terrainHandler.isLoaded
                    && this.modelsToBuild <= 0 ) {
                    this.AddMesh();
                }

            }
        }
    },
    AddMesh: function() {
        // Loaded, but not added to the world yet
        // Let's do that and set isAddedToWorld


          this.FinalizeMesh();


          //this.octree.add( this.models, true );
          terrainHandler.skybox.terrainOctree.add(this.models, true);

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

        //ironbane.octree.remove( this.models );
        terrainHandler.skybox.terrainOctree.remove(this.models);

        ironbane.scene.remove(this.models);
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


                        var geometry = meshHandler.SpiceGeometry(geometry, rotation,
                            metadata, meshData, param, false);

                        // geometry.position = pos;

                        _.each(geometry.vertices, function(v) {
                            v.addSelf(pos);
                        });

                        // // Merge it with the chunk geometry we have so far
                        THREE.GeometryUtils.merge( chunk.modelGeometry, geometry );

                        geometry.deallocate();

                        // Ready! Decrease modelsToBuild
                        chunk.modelsToBuild--;



                }, meshData['scale']);
                })(this, pos, rotation, metadata, meshData, param);



            }



        // Keep track of the ID's in a list of the chunk
        }

        if ( showEditor && levelEditor.editorGUI.enablePathPlacer ) {

            var graph = terrainHandler.world[cellPos.x][cellPos.z]['graph'];

            if ( graph && graph['nodes'] !== undefined ) {
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

        }

    }
});
