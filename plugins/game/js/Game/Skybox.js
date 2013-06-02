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

var skyboxPath = 'plugins/game/images/skybox/';

var Skybox = PhysicsObject.extend({
  Init: function() {

    var p = terrainHandler.GetReferenceLocation();

    this._super(p);

    this.sunVector = new THREE.Vector3(0, 1, 0);



    var size = 3000;

    //    var geometry = new THREE.CubeGeometry(size, size, size, 1, 1, 1);
    //
    //
    //    var faceIndices = [ 'a', 'b', 'c', 'd' ]
    //    for ( var i = 0; i < geometry.faces.length; i++ )
    //    {
    //      var face  = geometry.faces[ i ];
    //      // determine if current face is a tri or a quad
    //      var numberOfSides = ( face instanceof THREE.Face3 ) ? 3 : 4;
    //      // assign color to each vertex of current face
    //      for( var j = 0; j < numberOfSides; j++ )
    //      {
    //        var vertexIndex = face[ faceIndices[ j ] ];
    //        // store coordinates of vertex
    //        var point = geometry.vertices[ vertexIndex ];
    //        // initialize color variable
    //        var color = new THREE.Color( 0xffffff );
    //        color.setRGB( 0.5 + point.y / size, (0.5 + point.y / size)*0, (0.5 + point.y / size)*0 );
    //        face.vertexColors[ j ] = color;
    //      }
    //    }


    var geometry = new THREE.SphereGeometry(size);

    //    var material = new THREE.MeshBasicMaterial( {
    //      color : 0xffffff,
    //      shading: THREE.FlatShading
    //    } ) ;

    var uniforms = {

      vSun : {
        type: 'v3',
        value: this.sunVector
      }
    };

    var material = new THREE.ShaderMaterial({
      uniforms : uniforms,
      vertexShader : $('#vertex_skybox').text(),
      fragmentShader : $('#fragment_skybox_'+GetZoneConfig("skyboxShader")).text()
    });

    material.side = THREE.BackSide;

    this.skyboxMesh = new THREE.Mesh(geometry, material);

    this.terrainOctree = new THREE.Octree();

    ironbane.scene.add(this.skyboxMesh);

    // Add a sun
    // if ( zones[terrainHandler.zone]['type'] == ZoneTypeEnum.WORLD ) {

      geometry = new THREE.PlaneGeometry(600, 600, 1, 1);
      this.sunMesh = new THREE.Mesh(geometry, textureHandler.GetTexture('plugins/game/images/misc/sun.png', false, {
        transparent:true,
        alphaTest:0.01
      }));
      this.sunMesh.material.side = THREE.DoubleSide
      ;
      ironbane.scene.add(this.sunMesh);

    // }

    if ( zones[terrainHandler.zone]['type'] == ZoneTypeEnum.DUNGEON ) {
      this.sunMesh.visible = false;
    }

    // Add an ambient light
    this.ambientLight = new THREE.AmbientLight( 0x444444 );

    ironbane.scene.add( this.ambientLight );

    this.directionalLight = new THREE.DirectionalLight( 0xcccccc );

//    this.directionalLight.shadowCameraNear		= 5.1;
//    this.directionalLight.castShadow		= true;
//    this.directionalLight.shadowDarkness		= 0.3;



    ironbane.scene.add( this.directionalLight );

    this.shadowLight = new THREE.DirectionalLight( 0x000000 );

    this.shadowLight.onlyShadow = true;

    this.shadowLight.shadowMapWidth = 2048;
    this.shadowLight.shadowMapHeight = 2048;

    this.shadowLight.shadowCameraNear		= 5.1;
    this.shadowLight.castShadow		= true;
    this.shadowLight.shadowDarkness		= 0.3;



    ironbane.scene.add( this.shadowLight );



    // Add terrain

    if ( zones[terrainHandler.zone]['type'] == ZoneTypeEnum.WORLD ) {
      var model = skyboxPath + terrainHandler.zone+".js";
      //this.texture = textureHandler.GetTexture( texture, true);

      var jsonLoader = new THREE.JSONLoader();
      (function(skybox){
        jsonLoader.load( model, function( geometry ) {
          skybox.BuildMesh( geometry )
        }, null, 100);
      })(this);

      model = skyboxPath + terrainHandler.zone+"_collision.js";
      //this.texture = textureHandler.GetTexture( texture, true);

      var jsonLoader = new THREE.JSONLoader();
      (function(skybox){
        jsonLoader.load( model, function( geometry ) {
          skybox.BuildCollisionMesh( geometry )
        }, null, 100);
      })(this);
    }
    // meshHandler.GetMesh(this.param, this);




    this._super();
  },
  BuildMesh: function(geometry) {

    // Only push materials that are actually inside the materials
    // for (var i=0; i<geometry.jsonMaterials.length; i++) {

      var textures = [];

      // for (var i = 8; i < 20; i++) {
      //   textures.push("images/tiles/"+i);
      // }

      var tilesUsed = zones[terrainHandler.zone].tiles.split(",");

      // _.each(tilesUsed, function(tile) {
      //   textures.push("images/tiles/"+tile);
      // });

      // Only push materials that are actually inside the materials
      for (var i=0; i<geometry.jsonMaterials.length; i++) {

        // Check if there's a map inside the material, and if it contains a sourceFile
        if ( !_.isUndefined(geometry.jsonMaterials[i]["mapDiffuse"])) {
          // Extract the tile!
          textures.push("images/tiles/"+(geometry.jsonMaterials[i]["mapDiffuse"].split("."))[0]);
        }
      }

      // Check if there's a map inside the material, and if it contains a sourceFile
      _.each(textures, function(texture) {
        geometry.materials.push(textureHandler.GetTexture('plugins/game/'+texture + '.png', false, {
          transparent:false,
          alphaTest:0.1,
          useLighting:true
        }));
      });



        // Extract the tile!
        // var tile = "tiles/"+(geometry.jsonMaterials[i]["mapDiffuse"].split("."))[0];


    // this.terrainGeo.mergeVertices();
    THREE.GeometryUtils.triangulateQuads(geometry);

    geometry.computeCentroids();
    geometry.computeFaceNormals();

    this.terrainMesh = new THREE.Mesh( geometry, new THREE.MeshFaceMaterial() );
    this.terrainMesh.receiveShadow = true;

    ironbane.scene.add(this.terrainMesh);

    //this.terrainOctree.add( this.terrainMesh, true );

  },
  BuildCollisionMesh: function(geometry) {

    var tilesUsed = zones[terrainHandler.zone].tiles.split(",");

    _.each(tilesUsed, function(mat) {
      // Bogus materials
      geometry.materials.push(new THREE.MeshBasicMaterial());
    });

    THREE.GeometryUtils.triangulateQuads(geometry);

    geometry.computeCentroids();
    geometry.computeFaceNormals();

    this.terrainCollisionMesh = new THREE.Mesh( geometry, new THREE.MeshFaceMaterial() );

    // ironbane.scene.add(this.terrainCollisionMesh);

    this.terrainOctree.add( this.terrainCollisionMesh, true );

  },
  Destroy: function() {
    if ( this.skyboxMesh ) {
      ironbane.scene.remove(this.skyboxMesh);
      releaseMesh(this.skyboxMesh);
    }
    if ( this.sunMesh ) {
      ironbane.scene.remove(this.sunMesh);
      releaseMesh(this.sunMesh);
    }
    if ( this.terrainMesh ) {
      ironbane.scene.remove(this.terrainMesh);
      releaseMesh(this.terrainMesh);
    }
    if ( this.ambientLight ) {
      ironbane.scene.remove(this.ambientLight);
      releaseMesh(this.ambientLight);
    }
    if ( this.directionalLight ) {
      ironbane.scene.remove(this.directionalLight);
      releaseMesh(this.directionalLight);
    }
    if ( this.shadowLight ) {
      ironbane.scene.remove(this.shadowLight);
      releaseMesh(this.shadowLight);
    }
  },
  Tick: function(dTime) {


    var p = terrainHandler.GetReferenceLocationNoClone();

    this.skyboxMesh.position.copy(p);
    this.skyboxMesh.position.y = 0;



    this.directionalLight.position.copy( this.sunVector.clone().multiplyScalar(450) );

    //this.directionalLight.target.position.copy( this.sunVector.clone().multiplyScalar(-1) );
    this.directionalLight.target.position.copy( this.sunVector.clone().multiplyScalar(-450) );
    //this.directionalLight.target.position.copy( this.sunVector.clone().multiplyScalar(-1) );

    this.shadowLight.position.copy( new THREE.Vector3(0, 100, 0) );
    this.shadowLight.target.position.copy( new THREE.Vector3(0, -100, 0) );

    var time = (new Date()).getTime();
    var param = (((time/1000.0))* 3.6 * 100 / dayTime)%360;

    if ( le("chSunOffset") ) {
      param += levelEditor.editorGUI.chSunOffset;
    }

    if ( this.sunMesh ) {
      var rotationMatrix = new THREE.Matrix4();
      rotationMatrix.setRotationFromEuler(new THREE.Vector3((param).ToRadians(), (-30).ToRadians(), 0));


      if ( showEditor && levelEditor.editorGUI.chForceDay ) {
        this.sunVector.set(0,1,0);
      }
      else if ( (showEditor && levelEditor.editorGUI.chForceNight)
        || zones[terrainHandler.zone]['type'] == ZoneTypeEnum.DUNGEON ) {
        this.sunVector.set(0,-1,0);
      }
      else {
        this.sunVector.set(0,0,1);
        this.sunVector = rotationMatrix.multiplyVector3(this.sunVector);
      }

      sw("this.sunVector", this.sunVector);

      this.skyboxMesh.material.uniforms.vSun.value.copy(this.sunVector);

      //sw("this.sunVectorTest", this.sunVector.dot(new THREE.Vector3(0, 1, 0)).Round(2));

      var sunDistance = 1950;

      this.sunMesh.position.copy(p.clone().addSelf(this.sunVector.clone().multiplyScalar(sunDistance)));


      this.sunMesh.LookAt(p);

      var al = this.sunMesh.position.y/sunDistance;

      //al *= al;
      var alr = al;
      var alg = al;
      var alb = al;

      var str = 0;
      var stg = 0;
      var stb = 0;

      if ( alr > -0.3 && alr < 0.3 ) {
        var mod = alr / 0.3;
        if ( mod > 0 ) {
          alr += 1.0-mod
        }
        else {
          alr += 1.0+mod;
        }
      }

      alr = alr.clamp(0,1);

      alg = alg.clamp(0,1);
      alb = alb.clamp(0,1);


      //if ( alb < 0 ) alb *= 2;

      //this.ambientLight.color.setRGB( 0.2 + (al * 0.8), 0.2 + (al * 0.8), 0.4  + (al * 0.6));
      this.ambientLight.color.setRGB( 0.4, 0.4, 0.4);
      this.directionalLight.color.setRGB( str + (alr * 0.6), stg + (alg * 0.6), stb  + (alb * 0.6));


    }

    this._super(dTime);
  }
});
