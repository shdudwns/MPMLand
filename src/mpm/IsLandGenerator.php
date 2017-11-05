<?php
namespace mpm;

use mpm\Sphere;
use mpm\IsLandMain as Main;
use pocketmine\math\Vector3;
use pocketmine\level\ChunkManager;
use pocketmine\utils\Random;
use pocketmine\level\generator\Generator;
use pocketmine\level\generator\biome\Biome;
use pocketmine\level\generator\object\{ Tree, TallGrass };

class IsLandGenerator extends Generator {

	/** @var ChunkManager */
	private $level;
	/** @var Random */
	private $random;
	/** @var Main*/
	private $main;

	public function init(ChunkManager $level, Random $random){
		$this->level = $level;
		$this->random = $random;
	}

	public function __construct(array $options = []){

	}

	public function getSettings() : array {
		return [];
	}

	public function getName() : string {
		return "island";
	}

	public function generateChunk(int $chunkX, int $chunkZ){
		$chunk = $this->level->getChunk($chunkX, $chunkZ);

		for($x = 0; $x < 16; $x++){
			for($z = 0; $z < 16; $z++){
				foreach([7, 1, 1, 1, 1, 1, 1, 12, 8, 8] as $y => $blockId){
					$chunk->setBlock($x, $y, $z, $blockId);
				}
			}
		}
		
		if($chunkX > 0 and $chunkZ > 0){
			$islandX = ($chunkX * 16) % 200;
			$islandZ = ($chunkZ * 16) % 200;
			if($islandX <= 100 and 100 <= $islandX + 15 and $islandZ <= 100 and 100 <= $islandZ + 15){
				foreach(Sphere::getElements(8, 7, 8, 7) as $el){
					list($x, $y, $z) = $el;

					if($y < 7){
						continue;
					} else if($y < 10) {
						$chunk->setBlock($x, $y, $z, 1);
					} else if($y < 12) {
						$chunk->setBlock($x, $y, $z, 3);
					}
				}	
			}
		}
		$this->level->setChunk($chunkX, $chunkZ, $chunk);
	}

	public function populateChunk($chunkX, $chunkZ){
		if($chunkX > 0 and $chunkZ > 0){
			$islandX = ($chunkX * 16) % 200;
			$islandZ = ($chunkZ * 16) % 200;
			if($islandX <= 100 and 100 <= $islandX + 15 and $islandZ <= 100 and 100 <= $islandZ + 15){
				$x = $chunkX * 16 + 8;
				$z = $chunkZ * 16 + 8;
				$y = $this->level->getHighestBlockAt($x, $z);
				Tree::growTree($this->level, $x, $y + 1, $z, $this->random);

				TallGrass::growGrass($this->level, new Vector3($x, $y, $z), $this->random);
			}
		}
		$chunk = $this->level->getChunk($chunkX, $chunkZ);
		$biome = Biome::getBiome(Biome::OCEAN);
		$biome->populateChunk($this->level, $chunkX, $chunkZ, $this->random);
	}

	public function getSpawn() : Vector3 {
		return new Vector3(100, 25, 100);
	}
}

?>
