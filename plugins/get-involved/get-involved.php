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

$interest = $_GET['i'];


switch ($interest) {
    case 'code':
        $c_main .= '

        <h1>Coding</h1>

        <h2>How\'s the code like?</h2>
        <p>Ironbane runs on pure Javascript, on both client and server. Javascript is arguably an easy language to work with, and it allows you to program more productively with easy debugging and no need to recompile (atleast on the client).</p>

        <h2>What am I allowed to work on?</h2>

        <p>Anything you like! You can work on the game code, the 3D renderer, boss scripts, forum software or even on this page you\'re reading right now! You must work on whatever you want to work on, to keep yourself motivated. You basically have the power to change anything you like.

        <p>All the source code is waiting for you on <a href="https://github.com/ironbane" target="_new">GitHub</a>. With that said, you need to learn how to use Git if you haven\'t yet. It\'s an incredible powerful tool and you will need it at some point in your life anyway.</p>

        <h2>What\'s Git? I\'m scared!</h2>

        <p>No need to be. <a href="http://git-scm.com/book/en/Getting-Started-Git-Basics" target="_new">Read this tutorial</a>, it explains Git well and will get you up and running in no time.

        <h2>How do I contribute?</h2>

        <p>By making pull requests on <a href="https://github.com/ironbane" target="_new">the repository</a> you\'re working on! It\'s that simple. Make a pull request and one of the lead developers will check it out and give you feedback. If the code looks fine, it will be pushed live to Ironbane.com instantly. In the meantime, you earn <b>reputation</b>!</p>

        <p>Start by reading the <b>Getting started</b> pages on the <a href="https://github.com/ironbane" target="_new">GitHub repository</a>. If you need help, feel free to make an issue there or here on the <a href="forum.php">forums</a>.





        ';
        break;
    case 'models':
        $c_main .= '

        <h1>3D Models</h1>

        <h2>How do I start?</h2>
        <p>You\'re going to need a 3D modeling software package. <a href="http://www.blender.org/" target="_new">Blender</a> works well, but you can also use others if you are more comfortable with them. I for instance use 3ds Max.</p>

        <p>It is also advisable to get your own version of Ironbane running, so you can test out your models directly in-game. To do so, first read the "Getting Started" guides on the <a href="https://github.com/ironbane" target="_new">GitHub repository</a> for both the client and server.</p>

        <p>If you can\'t or don\'t want to get a local copy running on your machine, you can still post your model with screenshots on the forums.</p>

        <h2>What\'s the model format?</h2>
        <p>.OBJ, and then it is converted by a script to a .JS file.</p>

        <p>First, to get an .OBJ file, you have to use your modeling package\'s export function and then select the OBJ file format. if you\'re working with Blender, <a href="https://github.com/ironbane/IronbaneClient/blob/master/plugins/game/images/meshes/convert_obj_three.py#L86" target="_new">here are some good settings</a> to ensure your file gets exported correctly:</p>

		<h2>Which models can I make?</h2>

		<p>We need a lot of things in-game that decorate the world more. Outside of these models, you can also design entire levels if you like using your favorite modeling package. These can be dungeons, castles, entire landscapes, and more.</p>

		<p>At the moment we need things like statues, scenery objects (rocks, trees, bushes), transports (trains, zeppelins, boats), models for chests/signs and a tutorial level.</p>

		<p>Of course, if have other suggestions just give it a go and post it in the forum!</p>

		<h2>What about the textures?</h2>

		<p>You are free to completely UV map them, but usually that is not nessecary as we just use a tiled worldtile on them. To make your model use several textures, you should split up model in sub-meshes (e.g. Detach some parts, such as a door, ceiling, floor) and then assign those parts a different texture.</p>

		<img src="plugins/get-involved/images/examplemodel.png">

		<p>In this example, we have just put a simple box unwrap modifier and adjust the size of the UV box to 2,2,2 so it looks consistent among all models in-game.</p>

		<p>The textures used for this model are just simple 16x16 world tiles:</p>


		<img src="plugins/game/images/tiles/1.png">
		<img src="plugins/game/images/tiles/35.png">
		<img src="plugins/game/images/tiles/9.png">

        <h2>How does the game know which textures my model uses?</h2>

        <p>This is a bit complicated. <b>Ideally</b> (only tested in 3ds max) you can drag and drop textures on your model and the game will detect them, if you used world tiles. If you didn\'t use world tiles and you manually unwrapped and made a texture for it, this texture must be found in <b>plugins/game/images/textures</b> with the same filename used as the one you dragged on the model.</p>

        <p><b>Should the game not detect your textures and it shows up gray/invisible</b> there\'s still something you can do.</p>

        <p>In the Editor, you can overwrite the textures used for each sub mesh manually. Texture 1 will be used for the first sub-mesh found in your mesh, texture 2 for the second, and so on. I don\'t know how to find which sub-meshes are counted first, so you need to just test out the models yourself for now. Simple save the new settings and reload the game to see your changes.</p>

        <h2>How do I test out my own models locally?</h2>

        <p>To really see your own stuff in-game, you will need to have <a href="http://www.python.org/" target="_new">Python 2.7.4</a> installed. <b>Do not use the latest version of Python, only 2.7.4 currently works with the converter.</b></p>

        <p>Once you\'ve installed Python on your machine, copy your .OBJ to "/plugins/game/images/meshes/". Next, you will need to open a command-line prompt on this folder. In Windows 7, you can shift-click on the "meshes" folder and select "Open command prompt here".</p>

        <p>When you have the command prompt open on the meshes folder, enter the following command:</p>

        <p><b>convert_obj_three.py -i file.obj -o file.js</b></p>

        Replace "file" with the name of the OBJ (without extension) you copied in the folder. The command will generate a .JS file for you.</p>

        <p>Now it\'s time to add the model to the game. Log in the game as an admin (the default admin account is "TestUser" with password "test") and click on the Editor link on the navigation bar. Click on "Meshes Editor" and fill in the fields as best you can. <b>For filename, fill in the original full .OBJ filename (e.g. chair.obj)!</b> Press "Add" at the bottom when you\'re ready. Now go in-game, and check the Model Placer for your newly added model.</p>

        <h2>My model doesn\'t show up!</h2>

        <p>Something went wrong, apparently. Fortunately, you have a lot of example material right next to you. Check the meshes folder for other .OBJ files and open them in your modeling package to see what their scale is like, and try to adjust yours.</p>

		<p>The good news is that you only need to run the converter again, and you can just refresh the page. How cool is that!</p>

        <h2>General advice</h2>

        <p>Try to keep your polycount as low as possible. See the other models as a reference. I will post more hints here later.</p>


        ';
        break;
    case 'art':
        //$special_message = 'This page needs additional information from an artist.<br>Please make a pull request with more information/help for beginners.';

        $c_main .= '

        <h1>Art</h1>



        <h2>How do I start?</h2>

        <p>You need a good painting software. I personally prefer Photoshop, but you can also use <a href="http://www.getpaint.net/" target="_new">Paint.net</a>, <a href="http://www.gimp.org/" target="_new">GIMP</a> or others.</p>

        <h2>What can I draw?</h2>

        <p>Anything you like! New NPC\'s, textures, items, a better logo, stuff for the website. Do whichever you like best.</p>

        <h2>What is the image format and resolution which you use?</h2>

        <p>Please use the .PNG format for everything you submit. The game is expecting .PNG and will throw errors if you save it as a JPG or something else.</p>

        <p>We\'d like to maintain a 16-bit style on the game and website at all times. To do so, the resolution of items, textures, and character sprites are limited.</p>

        <p>As a rule of thumb, the usual resolution is 16x16 pixels. These include items, character sprites and world tiles. In some rare occasions you can go over this limit, such as Boss NPC\'s who are larger than players (e.g. Ironbane himself). If they were to be 2x the size in-game, their resolution can also be upscaled 2x to 32x32, allowing you to focus on extra detail.</p>

        <p>You should make sure that your contribution doesn\'t look out of place when compared to other images that already exist.<p>

        <h2>How do I make new NPC\'s?</h2>

        <p>NPC\'s and players are composed out of different <b>layers</b>.</p>

        <table style="width:300px">
            <tr>
                <th>Layer</th>
                <th>Image</th>
            </tr>
            <tr>
                <td class="row1">Skin</td>
                <td class="row1"><img src="plugins/game/images/characters/base/skin/1001.png"></td>
            </tr>
            <tr>
                <td class="row1">Eyes</td>
                <td class="row1"><img src="plugins/game/images/characters/base/eyes/1001.png"></td>
            </tr>
            <tr>
                <td class="row1">Hair</td>
                <td class="row1"><img src="plugins/game/images/characters/base/hair/1001.png"></td>
            </tr>
            <tr>
                <td class="row1">Body</td>
                <td class="row1"><img src="plugins/game/images/characters/base/body/1.png"></td>
            </tr>
            <tr>
                <td class="row1">Feet</td>
                <td class="row1"><img src="plugins/game/images/characters/base/feet/1.png"></td>
            </tr>
            <tr>
                <td class="row1">Head</td>
                <td class="row1"><img src="plugins/game/images/characters/base/head/1.png"></td>
            </tr>
        </table>

        <p>Together, they form a whole character.</p>

        <img src="plugins/get-involved/images/fullchar.png">

        <p>This approach allows everyone to build unique creatures easily, using different parts without making new sprites.</p>

        <p>To start making new creatures, try to split the creature up in the layers discussed as before, it will make your life easier later!</p>

        <p>When you are done, post it in the forums and we\'ll take a look at it!</p>

        <h2>How do I make new items?</h2>

        <p>Items are pretty straight-forward. Just create a new transparent 16x16 image in your graphics program and start building!</p>

        <p>Some examples:</p>

        <img src="plugins/game/images/items/4.png">
        <img src="plugins/game/images/items/5.png">
        <img src="plugins/game/images/items/6.png">
        <img src="plugins/game/images/items/7.png">
        <img src="plugins/game/images/items/8.png">
        <img src="plugins/game/images/items/9.png">
        <img src="plugins/game/images/items/10.png">
        <img src="plugins/game/images/items/11.png">
        <img src="plugins/game/images/items/12.png">

        <h2>I\'m done! WHere do I put it?</h2>

        <p>You can either post them in the forums for others to look at, or if you are technically skilled you can also make a pull request with the art in place. See the <a href="get-involved.php?i=code" target="_new">Code section</a>.</p>

        ';
        break;
    default:
        $c_main .= '

        <h2>How does this work?</h2>

        <p>Anyone can contribute something at any time. All you need is an account for Ironbane.</p>

        <p>When you add a contribution, you earn <b>reputation</b> (rep), which will be visible on your profile and on the forum. A higher rep allows you to get more privileges, such as becoming a moderator or game master, GitHub access, the ability to give others reputation for their work and more. We want to reward people for their work.</p>

        <h2>How much rep do I get for a contribution?</h2>

        <p>This will depends on the quality of work you provide, but here are some generic guidelines:

        <h4>Generic reputation award table</h4>

        <table width="100%" border="1" cellspacing="0" cellpadding="4" class="forumcontainer">
            <tr>
                <th>Action</th>
                <th>Reward</th>
            </tr>
            <tr>
                <td class="row1">Forum post</td>
                <td class="row1">1 rep</td>
            </tr>
            <tr>
                <td class="row2">Bug report</td>
                <td class="row2">5 rep</td>
            </tr>
             <tr>
                <td class="row1">Contributed content (art/music/model)</td>
                <td class="row1">10 rep (+ Bonus depending on quality)</td>
            </tr>
            <tr>
                <td class="row2">Accepted pull request</td>
                <td class="row2">20 rep (+ Bonus depending on quality)</td>
            </tr>
        </table>

        <h2>What can I work on?</h2>

        <p>If you were to join, what would you love to work on? Skill is important, but more important is your love for the skill. It doesn\'t matter if you suck, the only thing that is important is motivation. You must be willing to learn from others.</p>

        <div class="ib-gi-section">
            <div class="ib-gi-section-link"><a href="get-involved.php?i=code">I want to code!</a></div>
            <div class="ib-gi-section-image ib-gi-section-image-code">
            </div>
        </div>
        <div class="ib-gi-section">
            <div class="ib-gi-section-link"><a href="get-involved.php?i=models">I want to make 3D models!</a></div>
            <div class="ib-gi-section-image ib-gi-section-image-models">
            </div>
        </div>
        <div class="ib-gi-section">
            <div class="ib-gi-section-link"><a href="get-involved.php?i=art">I want to draw!</a></div>
            <div class="ib-gi-section-image ib-gi-section-image-art">
            </div>
        </div>

        Looking for something else? Give a shout at the forums and let us know what you\'d like to help out with!
        ';
        break;
}


if ( $interest ) {
    $c_main .= '<div class="spacer"></div><a href="get-involved.php">Go back</a>';
}



?>