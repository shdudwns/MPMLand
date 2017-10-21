<?php
namespace mpm;

use pocketmine\plugin\PluginBase;
use pocketmine\event\Listener;
use pocketmine\utils\Config;
use pocketmine\Player;
use pocketmine\math\Vector3;
use pocketmine\block\Block;
use pocketmine\level\Level;
use pocketmine\level\Position;
use pocketmine\command\CommandSender;
use pocketmine\command\Command;
use onebone\economyapi\EconomyAPI;

use mpm\LandGenerator;

/* Author : PS88
 * 
 * This php file is modified by GoldBigDragon (OverTook).
 */

class main extends PluginBase implements Listener{
	
  //  private $Instace;
    public $prefix = "§l§f[§bMPMLand§f]";
	private $c;
	
    public function onLoad(){
        $this->c = new Config($this->getDataFolder().'data.json', Config::JSON, [
            'island' => [],
            'land' => []
        ]);
        if( $this->c->__isset('islast')) return true;
        $this->c->set('islast', "0");
      //  self::$Instance = $this;
    }
    public function onEnable(){
        $this->getServer()->getPluginManager()->registerEvents($this, $this);
        // Island Name "Land"
		
		Generator::addGenerator(LandGenerator::class, "land");
		$gener = Generator::getGenerator("land");
		
		if(!($this->getServer()->loadLevel("Land"))){
			@mkdir($this->file_build_path($this->getServer()->getDataPath(), "worlds", "Land"));
			$options = [];
			$this->getServer()->generateLevel("Land", 0, $gener, $options);
			$this->getLogger()->info("섬 생성 완료.");
		}
		$this->getLogger()->info("섬 로드 완료.");
    }
	
	function file_build_path(...$segments) {
    	return join(DIRECTORY_SEPARATOR, $segments);
	}
	
    public function onCommand(CommandSender $sender, Command $command, string $label, array $args) : bool{
        if($command->getName() !== "섬") return true;
        if(! $sender instanceof Player) return true;
        $pl = $sender;
        $main = $this;
        switch ($args[0]){
            /*case '시작':
                if(! $pl->isOp()) return true;*/
            case '구매':
                if(EconomyAPI::getInstance()->myMoney($pl->getName()) < 20000){
                    $pl->sendMessage($main->prefix."돈이 없습니다.");
                    return true;
                }
                $as = [];
                for($i = 0; $i >= count($main->c->get('island')); $i++){
                  if(! isset($this->c->get('island')[$i])) break;
                    if($main->getIsOwner($i) !== $args[1]) return true;
                    array_push($as, $i);
                }
                if(isset($as)){
                if(count($as) >= 3){
                    $pl->sendMessage($main->prefix."당신의 섬 개수가 최대를 채웠습니다.");
                    return true;
                }
              }
                $main->mkIs($this->c->get('islast'), $pl->getName());
                $pl->sendMessage($this->prefix."당신은".$this->c->get('islast')."번섬을 구매하셨습니다.");
                $w = $this->c->get('islast');
                $this->c->__unset('islast');
                $this->c->set('islast',$w + 1);
                return true;
            case '양도':
                if(! isset($args[1])){
                    $pl->sendMessage($main->prefix." /섬 양도 [플레이어]");
                    return true;
                }
                if($main->getIsNum($pl) !== null){
                    $pl->sendMessage($main->prefix."당신은 아무 섬에도 있지 않습니다.");
                    return true;
                }
                if($main->getIsOwner($main->getIsNum($pl)) !== $pl->getName()){
                    $pl->sendMessage($main->prefix."당신의 섬이 아닙니다.");
                    return true;
                }
                $main->c->get('island')[$main->getIsNum($pl)] ['owner'] = $args[1];
                $pl->sendMessage($main->prefix."이 섬을".$args[1]."님에게 양도하였습니다.");
                return true;
            case '공유':
                if(! isset($args[1])){
                }
                $as = [];
                for($i = 0; $i >= count($main->c->get('island')); $i++){
                  if(! isset($this->c->get('island')[$i])) break;
                    if($main->getIsOwner($i) !== $args[1]) return true;
                    array_push($as, $i);
                }
                if(isset($as)){
                if(count($as) >= 3){
                    $pl->sendMessage($main->prefix."당신의 섬 개수가 최대를 채웠습니다.");
                    return true;
                }
              }
                $main->mkIs($this->c->get('islast'), $pl->getName());
                $pl->sendMessage($this->prefix."당신은".$this->c->get('islast')."번섬을 구매하셨습니다.");
                $w = $this->c->get('islast');
                $this->c->__unset('islast');
                $this->c->set('islast',$w + 1);
                return true;
            case '양도':
                if(! isset($args[1])){
                    $pl->sendMessage($main->prefix." /섬 양도 [플레이어]");
                    return true;
                }
                if($main->getIsNum($pl) !== null){
                    $pl->sendMessage($main->prefix."당신은 아무 섬에도 있지 않습니다.");
                    return true;
                }
                if($main->getIsOwner($main->getIsNum($pl)) !== $pl->getName()){
                    $pl->sendMessage($main->prefix."당신의 섬이 아닙니다.");
                    return true;
                }
                $main->c->get('island')[$main->getIsNum($pl)] ['owner'] = $args[1];
                $pl->sendMessage($main->prefix."이 섬을".$args[1]."님에게 양도하였습니다.");
                return true;
            case '공유':
                if(! isset($args[1])){
                    $pl->sendMessage($main->prefix." /섬 공유 [플레이어]");
                    return true;
                }elseif($main->getIsNum($pl) !== null){
                    $pl->sendMessage($main->prefix."당신은 아무 섬에도 있지 않습니다.");
                    return true;
                }elseif($main->getIsOwner($main->getIsNum($pl)) !== $pl->getName()){
                    $pl->sendMessage($main->prefix."당신의 섬이 아닙니다.");
                    return true;
                }elseif(! $pl->isOp()){
                }
                for($i = 0; $i >= count($main->c->get('island')[$main->getIsNum($pl)] ['share']); $i++){
                    if ($main->c->get('island')[$main->getIsNum($pl)] ['share'][$i] !== $args[1]) return true;
                    $a = true;
                }
                if(isset($a)){
                    array_unshift($main->c->get('island')[$main->getIsNum($pl)] ['share'], $args[1]);
                    $pl->sendMessage($main->prefix.$args[1]."님을 섬 공유자에서 박탈시키셨습니다.");
                    return true;
                }
                array_push($main->c->get('island')[$main->getIsNum($pl)] ['share'], $args[1]);
                $pl->sendMessage($main->prefix."이 섬을".$args[1]."님에게 공유하였습니다."); return true;
            case '이동':
                $main->WarpIs($pl, $args[1]);
                return true;
        }
    }
    public function getIsOwner(int $num){
      if(! isset($this->c->get('island')[$num])){
        $ew = false;
      }else{
        $ew = $this->c->get('island')[$num] ['owner'];
      }
        return $ew;
    }
    public function getIsNum(Player $pl){
        if($pl->getLevel()->getName() !== 'island') return true;
        for($i = 0; $i >= count($this->c->get('island')); $i++){
            $d = $this->c->get('island')[$i];
            if(! $pl->getX() <= $d ['fir'] ['x'] && ! $pl->getZ() <= $d ['fir'] ['z']) return true;
            if(! $pl->getX() >= $d ['las'] ['x'] && ! $pl->getZ() >= $d ['las'] ['z']) return true;
            $num = $i;
            break;
        }
        return $num;
    }
    public function getShare(int $num){
        return $this->c->get('island')[$num] ['share'];
    }
    public function getplIs($pname){
        $s = [];
        for ($i = 0; $i >= count($this->c->get('island')); $i++) {
            if($this->c->get('island')[$i] ['owner'] !== $pname or $this->c->get('island')[$i] ['share'] !== $pname) return true;
            array_push($s, $i);
        }
        return $s;
    }
    public function setIs($pname, $num){
        $this->c->get('island')[$num] ['owner'] = $pname;
        return true;
    }
    public function mkIs($num, $owner){
        $this->c->get('island')[$num] = [
          'owner' => $owner,
          'share' => [],
            'senter' => [
                'x' => $num * 201,
                'z' => 0
            ],
            'welcomeM' => $this->prefix."섬".$num."번에 오신것을 환영합니다."
        ];
        $this->setBlockArea($num * 201 - 5, 6, 5, $num * 201 + 5, 4, -5, $this->getServer()->getLevelByName("island"), 2);
        return true;
    }
    public function WarpIs(Player $pl, $num){
        if(! $num <= $this->c->get('islast')){
            $pl->sendMessage($this->prefix."당신의 워프하고싶어하는 섬은 없습니다. 꺌꺌꺌");
            return true;
        }
        $pl->teleport(new Position($this->c->get('island')[$num] ['senter'] ['x'], 8, $this->c->get('island')[$num] ['sender'] ['z'], 'island'),0,0);
        $pl->sendPopup($this->c->get('island')[$num] ['welcomeM']);
        return true;
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
                        if((microtime(true) - $microt) > 0.25||$count == 0||$max == $count) {$microt = microtime(true);}
                        $level->setBlock( $pos = new Vector3((int)$x,(int)$y,(int)$z) , $block[0], false, false);
                    }
                else if(count ($block) > 1) {
                    $endid = (count($block) - 1);
                    for($x = $pos1[0]; $x <= $pos2[0]; $x++)
                        for($y = $pos1[1]; $y <= $pos2[1]; $y++)
                            for($z = $pos1[2]; $z <= $pos2[2]; $z++) {
                                ++$count;
                                if((microtime(true) - $microt) > 0.25||$count == 0||$max == $count) {$microt = microtime(true);}
                                $select = $block[mt_rand(0, $endid)];
                                $level->setBlock( $pos = new Vector3((int)$x,(int)$y,(int)$z) , $select, false, false);
                            }
                }

    }
    public function calculateArea($x1, $y1, $z1, $x2, $y2, $z2) {
        
        $xlength = (abs($x1 - $x2)+1);
        $ylength = (abs($y1 - $y2)+1);
        $zlength = (abs($z1 - $z2)+1);
        
        return ($xlength*$ylength*$zlength);
    }
  /*  public static function getInstance(){
        return self::$Instance;
    }*/
}
