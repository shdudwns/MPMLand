<?php

namespace mpm;

use pocketmine\math\Vector3;

class Sphere {
	
	public static function getElements(int $originX, int $originY, int $originZ, int $radius) : array{
		$sphereSerialized = [];
		
		$originVector = new Vector3($originX, $originY, $originZ);
		$temporalVector = new Vector3();
		
		for($x = $originX - $radius, $maxX = $originX + $radius; $x < $maxX; $x++){
			for($y = $originY - $radius, $maxY = $originY + $radius; $y < $maxY; $y++){
				for($z = $originZ - $radius, $maxZ = $originZ + $radius; $z < $maxZ; $z++){
					if($temporalVector->setComponents($x, $y, $z)->distance($originVector) < $radius){
						$sphereSerialized[] = [$x, $y, $z];
					}
				}
			}
		}
		return $sphereSerialized;
	}
}

?>
