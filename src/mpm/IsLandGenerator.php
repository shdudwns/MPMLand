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
				//if($this->main->getIsType() == "water" or $this->main->getIsType() !== null){
				$chunk->setBlock($x, 0, $z, 7);
				$chunk->setBlock($x, 1, $z, 1);
				$chunk->setBlock($x, 2, $z, 1);
				$chunk->setBlock($x, 3, $z, 1);
				$chunk->setBlock($x, 4, $z, 1);
				$chunk->setBlock($x, 5, $z, 1);
				$chunk->setBlock($x, 6, $z, 1);
				$chunk->setBlock($x, 7, $z, 12);
				$chunk->setBlock($x, 8, $z, 8);
				$chunk->setBlock($x, 9, $z, 8);
			/*}else{
				$chunk->setBlock($x, 0, $z, 0);
				$chunk->setBlock($x, 1, $z, 0);
				$chunk->setBlock($x, 2, $z, 0);
				$chunk->setBlock($x, 3, $z, 0);
				$chunk->setBlock($x, 4, $z, 0);
				$chunk->setBlock($x, 5, $z, 0);
				$chunk->setBlock($x, 6, $z, 0);
				$chunk->setBlock($x, 7, $z, 0);
				$chunk->setBlock($x, 8, $z, 0);
				$chunk->setBlock($x, 9, $z, 0);
			}*/
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
				Tree::growTree(10, $this->level->getHighestBlockAt(10, 10) + 1, 10, $this->random);

				TallGrass::growGrass($this->level, new Vector3(10, $this->level->getHighestBlockAt(10, 10), 10), $this->random);
			}
		}
		$chunk = $this->level->getChunk($chunkX, $chunkZ);
		$biome = Biome::getBiome($chunk->getBiomeId(7, 7));
		$biome->populateChunk($this->level, $chunkX, $chunkZ, $this->random);
	}

	public function getSpawn() : Vector3 {
		return new Vector3(100, 25, 100);
	}
}

?>
