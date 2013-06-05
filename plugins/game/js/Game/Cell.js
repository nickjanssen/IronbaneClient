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

var Cell = Class.extend({
    Init: function(cellX, cellZ) {

        this.cellX = cellX;
        this.cellZ = cellZ;

        this.isAddedToWorld = false;

        this.hasMeshesLoaded = false;

        this.removeNextTick = false;

        this.objects = [];
        this.waypointMeshes = [];

        // Used to construct the model geometry, so we can still cast shadows
        this.modelGeometry = null;
        this.modelMesh = null;

        // Numbers of models that must be 0 before we can add the chunk mesh
        this.modelsToBuild = 0;

    },
    Tick: function(dTime) {
        if ( this.isAddedToWorld ) return;

        if ( !this.hasMeshesLoaded ) {
            this.LoadObjects();

            this.hasMeshesLoaded = true;
        }
        else if ( !this.removeNextTick ) {

            if ( terrainHandler.isLoaded && this.modelsToBuild <= 0 ) {
                this.AddMesh();
                this.isAddedToWorld = true;
            }

        }

    },
    AddMesh: function() {

        // Load all 3D models that belong to this group
        this.models = new THREE.Mesh(this.modelGeometry, new THREE.MeshFaceMaterial());
        this.models.castShadow = true;
        ironbane.scene.add(this.models);

        // Collision data goes to one big octree that sits on the skybox
        terrainHandler.skybox.terrainOctree.add(this.models, true);

        ironbane.renderer.shadowMapEnabled = true;
        ironbane.renderer.shadowMapAutoUpdate = true;
        ironbane.renderer.shadowMapSoft = false;


        if ( ISDEF(ironbane.shadowMapUpdateTimer) ) {
            clearTimeout(ironbane.shadowMapUpdateTimer);
        }

        ironbane.shadowMapUpdateTimer = setTimeout(function() {
            ironbane.renderer.shadowMapAutoUpdate = false;
        }, 100);
    },
    RemoveMesh: function() {

        if ( this.modelGeometry ) {
            _.each(this.modelGeometry.materials, function(material) {
              material.deallocate();
            });

            this.modelGeometry.deallocate();
        }

        if ( this.models ) {
            terrainHandler.skybox.terrainOctree.remove(this.models);

            ironbane.scene.remove(this.models);
        }


        for(var o=0;o<this.objects.length;o++) {
            this.objects[o].Destroy();

            // Remove from unitList
            ironbane.unitList = _.without(ironbane.unitList, this.objects[o]);
        }

        this.objects = [];

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
    Reload: function() {
        if ( this.isAddedToWorld ) {
            this.RemoveMesh();
        }
    },
    LoadObjects: function(waypointsOnly) {

        this.modelGeometry = new THREE.Geometry();

        // We just want to load the objects in memory, not actually add them to
        // the scene. Later, merge in the geometry with the terrain mesh

        waypointsOnly = waypointsOnly || false;


        if ( !ISDEF(terrainHandler.world[this.cellX][this.cellZ]['objects']) ) return;

        for(var o=0;o<terrainHandler.world[this.cellX][this.cellZ]['objects'].length;o++) {

            if ( waypointsOnly ) continue;

            var gObject = terrainHandler.world[this.cellX][this.cellZ]['objects'][o];


            var pos = new THREE.Vector3(gObject.x, gObject.y, gObject.z);

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
                        unit = new Mesh(pos, rotation, 0, gObject.p, metadata);
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

            var graph = terrainHandler.world[this.cellX][this.cellZ]['graph'];

            if ( graph && graph['nodes'] !== undefined ) {
                for(var n=0;n<graph['nodes'].length;n++) {
                    var node = graph['nodes'][n];

                    var pos = ConvertVector3(node.pos);

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

                            // Load cells around us
                            this.isLoaded = true;

                            for(var x=this.cellX-1;x<=this.cellX+1;x+=1){
                                for(var z=this.cellZ-1;z<=this.cellZ+1;z+=1){

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
