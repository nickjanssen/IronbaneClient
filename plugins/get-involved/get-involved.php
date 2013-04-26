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

        <p>All the source code is waiting for you on <a href="https://github.com/ironbane" target="_new">GitHub</a>. With that said, you need to learn how to use Git if you haven\'t yet. It\'s an incredible powerful tool and you will need it at some point in your life anyway.</p>

        <h2>What\'s Git? I\'m scared!</h2>

        <p>No need to be. <a href="http://git-scm.com/book/en/Getting-Started-Git-Basics" target="_new">Read this tutorial</a>, it explains Git well and will get you up and running in no time.

        <h2>How do I contribute?</h2>

        <p>By making pull requests! It\'s that simple. Make a pull request and one of the lead developers will check it out and give you feedback. If the code looks fine, it will be pushed live to Ironbane.com instantly.</h2>



        ';
        break;

    default:
        $c_main .= '

        <h2>How does this work?</h2>

        <p>Anyone can contribute something at any time. All you need is an account for Ironbane.</p>

        <p>When you add a contribution, you earn <b>reputation</b> (rep), which will be visible on your profile and on the forum. A higher rep allows you to get more privileges, such as becoming a moderator or game master, GitHub access, the ability to give others reputation for their work and more. We want to reward people for their work.

        <h2>How much rep do I get for a contribution?</h2>

        <p>This depends solely on the quality of work you provide. Do your best, and if we notice that you\'re really delivering awesome stuff your reputation will skyrocket.

        <h2>What can I work on?</h2>

        If you were to join, what would you love to work on? Skill is important, but more important is your love for the skill. It doesn\'t matter if you suck, the only thing that is important is motivation. You must be willing to learn from others.

        <div class="ib-gi-section">

            <div class="ib-gi-section-link"><a href="get-involved.php?i=code">I want to code!</a></div>


            <div class="ib-gi-section-image ib-gi-section-image-code">

            </div>

        </div>


        ';
        break;
}


if ( $interest ) {
    $c_main .= '<div class="spacer"></div><a href="get-involved.php">Go back</a>';
}



?>