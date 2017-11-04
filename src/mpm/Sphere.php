<?php

namespace mpm;

use mpm\Sphere;

class Sphere {
	
	public static function getElements(int $originX, int $originY, int $originZ, int $radius) : array{
		$ret = [];
		$radiusSquared = ($radius ** 2) * 3;
		
		for($x = $originX - $radius, $maxX = $originX + $radius; $x <= $maxX; $x++){
			for($y = $originY - $radius, $maxY = $originY + $radius; $y <= $maxY; $y++){
				for($z = $originZ - $radius, $maxZ = $originZ + $radius; $z <= $maxZ; $z++){
					if((($originX - $x) ** 2) + (($originY - $y) ** 2) + (($originZ - $z) ** 2) < $radiusSquared){
						$ret[] = [$x, $y, $z];
					}
				}
			}
		}
		return $ret;
	}
}

?>
