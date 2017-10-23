<?php
namespace mpm;

//use pocketmine\level\generator\Generator;
use mpm\Sphere;
use mpm\IslandMain as FieldMain;
use pocketmine\math\Vector3;
use pocketmine\level\ChunkManager;
use pocketmine\utils\Random;
use pocketmine\level\generator\Generator;
use pocketmine\block\Block;

class FieldGenerator extends Generator {

	/** @var ChunkManager */
	private $level;
	/** @var Random */
	private $random;

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
		return "field";
	}

	public function generateChunk(int $chunkX, int $chunkZ){
		$chunk = $this->level->getChunk($chunkX, $chunkZ);
    $y = 10;

		for($x = 0; $x < 16; $x++){
			for($z = 0; $z < 16; $z++){
				$chunk->setBlock($x, 0, $z, 7);
				$chunk->setBlock($x, 1, $z, 1);
				$chunk->setBlock($x, 2, $z, 1);
				$chunk->setBlock($x, 3, $z, 1);
				$chunk->setBlock($x, 4, $z, 1);
				$chunk->setBlock($x, 5, $z, 1);
				$chunk->setBlock($x, 6, $z, 1);
				$chunk->setBlock($x, 7, $z, 12);
				$chunk->setBlock($x, 8, $z, 1);
				$chunk->setBlock($x, 9, $z, 2);
			}
		}

		while(true){
      $y = 12;
			$bad = 7;//베드락 코드가 이게 맞겠죠..?
      $iv1 = 1; //일단 돌로 했어요.. 실험하려고..;
      $iv = 3; //이것도 위와 같이..;;
			//$worldX = $chunkX * 16;
			//$worldZ = $chunkZ * 16;
			if($chunkX < 0 or $chunkZ < 0){
				break;
			}
      for($i = 0; $num - $i * 20 < 20; $i++){
        if($num - $i * 20 < 20){
          $zl = $num - $i * 20;
          $xl = $i;
          break;
        }
      }
      $this->setBlockArea($xl * 35 + 3, $y, $zl * 35 + 3, $xl * 35 + 32, $y, $zl * 35 + 32, $this->getServer()->getdefaultLevel('field'), 2);
			for($i = 0; $i >= 29; $i++){
			for($ii = 1; $ii >= 3; $ii++){
				$this->level->setBlock($xl * 35 + 2 + $i, $y - $ii, $zl * 35 + 2, $bad);
			}
			$this->level->setBlock($xl * 35 + 2 + $i, $y, $zl * 35 + 2, $iv1);
		}
		for($i = 0; $i >= 29; $i++){
		for($ii = 1; $ii >= 3; $ii++){
			$this->level->setBlock($xl * 35 + 2, $y - $ii, $zl * 35 + 2 + $i, $bad);
		}
		$this->level->setBlock($xl * 35 + 2, $y, $zl * 35 + 2 + $i, $iv1);
	}
	$this->setBlockArea($xl * 35, $y, $zl * 35, $xl * 35 + 1, $y, $zl * 35 + 35, $this->getServer()->getdefaultLevel('field'), $iv2);
	$this->setBlockArea($xl * 35 + 33, $y, $zl * 35, $xl * 35 + 34, $y, $zl * 35 + 34, $this->getServer()->getdefaultLevel('field'), $iv2);
$this->setBlockArea($xl * 35 + 33, $y, $zl * 35, $xl * 35 + 34, $y, $zl * 35 + 35, $this->getServer()->getdefaultLevel('field'), $iv2);
$this->setBlockArea($xl * 35, $y, $zl * 35 + 33, $xl * 35 + 1, $y, $zl * 35 + 34, $this->getServer()->getdefaultLevel('field'), $iv2);
}
	for($i = 0; $i >= 29)
      $main = new FieldMain();
			$num = $this->c->get('filast');
			$main->c->get('field')[$num] = [
				'share' => [],
				'welcomeM' => "평야".$num."번입니다. 가격 : 100000원"
			];
			$this->c->__unset('filast');
			$this->c->set('fillast', $num + 1);
			break;
		}
		$this->level->setChunk($chunkX, $chunkZ, $chunk);
	}

	public function populateChunk($chunkX, $chunkZ){

	}

	public function getSpawn() : Vector3 {
		return new Vector3(100, 25, 100);
	}


 /*
  *  이 코드는 솔로월엣 플긴에서 가져 왔습니다.
  */

  public function calculateArea($x1, $y1, $z1, $x2, $y2, $z2) {

    $xlength = (abs($x1 - $x2)+1);
    $ylength = (abs($y1 - $y2)+1);
    $zlength = (abs($z1 - $z2)+1);

    return ($xlength*$ylength*$zlength);
  }
  public function setBlockArea($x1, $y1, $z1, $x2, $y2, $z2, Level $level, $id) {

    $pos1 = [];
    $pos2 = [];

    if($x1 > $x2) {$pos1[0] = $x2; $pos2[0] = $x1;}
    else if($x1 < $x2) {$pos1[0] = $x1; $pos2[0] = $x2;}
    else {$pos1[0] = $x1; $pos2[0] = $x1;}

    if($y1 > $y2) {$pos1[1] = $y2; $pos2[1] = $y1;}
    else if($y1 < $y2) {$pos1[1] = $y1; $pos2[1] = $y2;}
    else {$pos1[1] = $y1; $pos2[1] = $y1;}

    if($z1 > $z2) {$pos1[2] = $z2; $pos2[2] = $z1;}
    else if($z1 < $z2) {$pos1[2] = $z1; $pos2[2] = $z2;}
    else {$pos1[2] = $z1; $pos2[2] = $z1;}

    $block = [];
    if(is_array($id)) {
    foreach($id as $i) {
      $i = explode (':', $i);
      if(count($i) == 1)
        array_push ($block, Block::get($i[0], 0));
      else if (count($i) == 2)
        array_push ($block, Block::get($i[0], $i[1]));
      else
        continue;
      }
    } else {
      $i = explode (':', $id);
      if(count($i) == 1)
        array_push($block, Block::get($i[0], 0));
      else if (count($i) == 2)
        array_push($block, Block::get($i[0], $i[1]));
      else
        return;
    }

    $count = 0;
    $max = $this->calculateArea($pos1[0], $pos1[1], $pos1[2], $pos2[0], $pos2[1], $pos2[2]);
    $microt = microtime(true);

    if(count ($block) == 1)
    for($x = $pos1[0]; $x <= $pos2[0]; $x++)
      for($y = $pos1[1]; $y <= $pos2[1]; $y++)
        for($z = $pos1[2]; $z <= $pos2[2]; $z++) {
          ++$count;
          if((microtime(true) - $microt) > 0.25||$count == 0||$max == $count) { $microt = microtime(true);}
          $level->setBlock( $pos = new Vector3((int)$x,(int)$y,(int)$z) , $block[0], false, false);
        }
    else if(count ($block) > 1) {
    $endid = (count($block) - 1);
    for($x = $pos1[0]; $x <= $pos2[0]; $x++)
      for($y = $pos1[1]; $y <= $pos2[1]; $y++)
        for($z = $pos1[2]; $z <= $pos2[2]; $z++) {
          ++$count;
          if((microtime(true) - $microt) > 0.25||$count == 0||$max == $count) { $microt = microtime(true);}
          $select = $block[mt_rand(0, $endid)];
          $level->setBlock( $pos = new Vector3((int)$x,(int)$y,(int)$z) , $select, false, false);
        }
      }

  }
}

?>
