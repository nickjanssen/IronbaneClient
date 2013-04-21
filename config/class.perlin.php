<?php

/*~ class.perlin.php
.-----------------------------------------------------------------------------------.
|  Software: Perlin noise 2D                                                        |
|   Version: 0.1.0                                                                  |
|   Contact: http://dev.horemag.net                                                 |
| --------------------------------------------------------------------------------- |
|          Origin: http://freespace.virgin.net/hugo.elias/models/m_perlin.htm       |
| PHP Addaptation: Evgeni Vasilev (original founder)                                |
| --------------------------------------------------------------------------------- |
|   License: GNU Free Documentation License                                         |
| http://en.wikipedia.org/wiki/Wikipedia:Text_of_the_GNU_Free_Documentation_License |
| This program is distributed in the hope that it will be useful - WITHOUT          |
| ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or             |
| FITNESS FOR A PARTICULAR PURPOSE.                                                 |
'----------------------------------------------------------------------------------'

/**
 * Perlin - Perlin Noise 2D Generator
 * @package Perlin
 * @author Evgeni Vasilev
 */


class Perlin{
  var $persistence = .15;
  var $octaves = 1;
  var $r1 = 0;
  var $r2 = 0;
  var $r3 = 0;

  function Perlin(){
//      $this->r1 = 15731;
//      $this->r2 = 789221; 
//      $this->r3 = 1376312589;
     $this->r1 = rand(1000, 10000);
     $this->r2 = rand(100000, 1000000);
     $this->r3 = rand(1000000000, 2000000000);
  }

  function perlinNoise2d($x, $y){
    $total = 0;
    $p = $this->persistence;
    $n = $this->octaves;
    for ($i = 0; $i < $n; $i++){
      $frequency = pow(2, $i);
      $amplitude = pow($p, $i);
      $total = $total + $this->InterpolatedNoise($x * $frequency, $y * $frequency) * $amplitude;
    }
    return -$total;
  }

  function InterpolatedNoise($x, $y){
    $integer_X    = (int)$x;
    $fractional_X = $x - $integer_X;

    $integer_Y    = (int)$y;
    $fractional_Y = $y - $integer_Y;

    $v1 = $this->SmoothedNoise($integer_X,     $integer_Y);
    $v2 = $this->SmoothedNoise($integer_X + 1, $integer_Y);
    $v3 = $this->SmoothedNoise($integer_X,     $integer_Y + 1);
    $v4 = $this->SmoothedNoise($integer_X + 1, $integer_Y + 1);

    $i1 = $this->Interpolate($v1 , $v2 , $fractional_X);
    $i2 = $this->Interpolate($v3 , $v4 , $fractional_X);

    return $this->Interpolate($i1 , $i2 , $fractional_Y);
  }

  function SmoothedNoise($x, $y){
    $corners = ( $this->noise($x-1, $y-1)+$this->noise($x+1, $y-1)+$this->noise($x-1, $y+1)+$this->noise($x+1, $y+1) ) / 16;
    $sides   = ( $this->noise($x-1, $y)  +$this->noise($x+1, $y)  +$this->noise($x, $y-1)  +$this->noise($x, $y+1) ) /  8;
    $center  =  $this->noise($x, $y) / 4;
    return $corners + $sides + $center;
  }

  function noise($x,$y){
    $x = floor(intval($x));
    $y = floor(intval($y));
    $n = $x + $y * 57;
    
    while(abs($n)>4360) $n = $n - (($n-(4360*($n/abs($n))))*2); 
    
    $xl = ($n << 13) ^ $n;
    $x2 = intval($xl * $xl * $this->r1 + $this->r2);
    $t1 = intval($xl *  $x2);
    $t2 = intval($t1 + $this->r3);
    $t3 = $t2 & 0x7fffffff;
    return 1 - ($t3 / 1073741824.0);
  }

  function Interpolate($x, $y, $a){
    $val = (1 - cos($a * pi())) * .5;
    return $x * (1 - $val) + $y * $val;
  }
}