<?php
namespace mpm;

use pocketmine\scheduler\PluginTask;
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
use pocketmine\level\generator\Generator;
use pocketmine\event\player\PlayerMoveEvent;
use pocketmine\event\block\{BlockPlaceEvent, BlockBreakEvent};
use pocketmine\event\entity\EntitySpawnEvent;

use mpm\IsLandGenerator as LandGenerator;
use mpm\FieldGenerator;
use mpm\skylandGenerator;

/* Author : PS88
 *
 * This php file is modified by GoldBigDragon (OverTook).
 */

class IsLandMain extends PluginBase implements Listener{

    public $prefix = "§l§f[§bMPMLand§f]";
	public $c, $s;
  //private $nis = [];


      public function onLoad(){
        @mkdir($this->getDataFolder());
          $this->c = new Config($this->getDataFolder().'data.json', Config::JSON, [
              'island' => [],
              'islast' => 0,
              'land' => [],
              'llast' => 0
          ]);
          $this->c = $this->c->getAll();
          $this->s = new Config($this->getDataFolder().'setting.yml', Config::YAML, [
              'island' => [
                'prize' => 20000,
                'type' => "water",
                'make' => true,
                'pvp' => true,
                'max' => 3
              ],
              'field' => [
                'prize' => 20000,
                'pvp' => true,
                'make' => true,
                'max' => 3
              ],
              'skyland' => [
                'prize' => 20000,
                'pvp' => true,
                'make' => true,
                'max' => 3
             ]
          ]);
          $this->s = $this->s->getAll();
    }
    public function onEnable(){
        $this->getServer()->getPluginManager()->registerEvents($this, $this);

    if($this->s['island']['make']){
		Generator::addGenerator(LandGenerator::class, "island");
		$gener = Generator::getGenerator("island");

		if(!($this->getServer()->loadLevel("island"))){
			@mkdir($this->getServer()->getDataPath() . "/" . "worlds" . "/" . "island");
			$options = [];
			$this->getServer()->generateLevel("island", 0, $gener, $options);
			$this->getLogger()->info("섬 생성 완료.");
		}
		$this->getLogger()->info("섬 로드 완료.");
  }
  if($this->s['field']['make']){
    Generator::addGenerator(FieldGenerator::class, "field");
    $gener = Generator::getGenerator("field");

    if(!($this->getServer()->loadLevel("field"))){
      @mkdir($this->getServer()->getDataPath() . "/" . "worlds" . "/" . "field");
      $options = [];
      $this->getServer()->generateLevel("field", 0, $gener, $options);
      $this->getLogger()->info("땅 생성 완료.");
    }
    $this->getLogger()->info("땅 로드 완료.");
    }
   if($this->s['skyland']['make']){
		Generator::addGenerator(skylandGenerator::class, "skyland");
		$gener = Generator::getGenerator("skyland");

		if(!($this->getServer()->loadLevel("skyland"))){
			@mkdir($this->getServer()->getDataPath() . "/" . "worlds" . "/" . "skyland");
			$options = [];
			$this->getServer()->generateLevel("skyland", 0, $gener, $options);
			$this->getLogger()->info("skyland 생성 완료.");
		}
		$this->getLogger()->info("skyland 로드 완료.");
  }
  }
    public function onDisable(){
      $this->c->save();
      $this->s->save();
    }

    public function onCommand(CommandSender $pl, Command $cmd, String $label, array $args) : bool{
      if(! $pl instanceof Player){
        $this->getLogger()->info($this->prefix."서버에서만 사용가능합니다.");
        return true;
      }
      $pr = $this->prefix;
          switch($cmd->getName()){
            case '섬구매':
              if(EconomyAPI::getInstance()->myMoney($pl->getName()) < $this->s['island'] ['prize']){
                $pl->sendMessage($pr."돈이 부족합니다. 섬 가격 : ".$this->s['island'] ['prize']);
                return true;
              }
              if(count($this->getPlIslands($pl->getName())) >= $this->s['island'] ['max']){
                $pl->sendMessage($pr. "당신의 섬 개수가 이미 제한 개수만큼 채워졌습니다."); return true;
              }
            //  echo "1";
              $this->SetIsland($this->c['island'], $pl);
            //  echo "2";
              EconomyAPI::getInstance()->reduceMoney($pl->getName(), $this->s['island'] ['prize']);
             break;
           case '섬양도':
             if(! isset($args[0])){$pl->sendMessage($pr."/섬양도 [플레이어]"); return true;}
             if($this->nowIsland($pl) == false or $this->c['island'] [$this->nowIsland($pl)] ['owner'] !== $pl->getName()){$pl->sendMessage($pr."당신은 섬에 있지 않거나 당신의 섬이 아닌곳에 있습니다."); return true;}
             $this->SetIsland($this->nowIsland($pl), $this->getServer()->getPlayer($args[0]));
            break;
           case '섬이동':
             if(! isset($args[0])){$pl->sendMessage($pr."/섬이동 [번호]"); return true;}
             $this->WarpIsland($args[0], $pl);
            break;
           case '섬공유':
             if(! isset($args[0])){$pl->sendMessage($pr."/섬공유 [플레이어]"); return true;}
             if($this->nowIsland($pl) == false or $this->c['island'] [$this->nowIsland($pl)] ['owner'] !== $pl->getName()){$pl->sendMessage($pr."당신은 섬에 있지 않거나 당신의 섬이 아닌곳에 있습니다."); return true;}
             $this->ShareIsland($this->nowIsland($pl), $this->getServer()->getPlayer($args[0]));
            break;
           case '섬공유해제':
             if(! isset($args[0])){$pl->sendMessage($pr."/섬공유해제 [플레이어]"); return true;}
             if($this->nowIsland($pl) == false or $this->c['island'] [$this->nowIsland($pl)] ['owner'] !== $pl->getName()){$pl->sendMessage($pr."당신은 섬에 있지 않거나 당신의 섬이 아닌곳에 있습니다."); return true;}
             $this->OutIsland($this->nowIsland($pl), $this->getServer()->getPlayer($args[0]));
            break;
           case '섬':
             $pl->sendMessage($pr." /섬구매 §o§8- 섬을 구매합니다.");
             $pl->sendMessage($pr." /섬양도 [플레이어] §o§8- 섬을 [플레이어] 에게 양도합니다.");
             $pl->sendMessage($pr." /섬이동 [번호] §o§8- [번호] 섬으로 갑니다.");
             $pl->sendMessage($pr." /섬공유 [플레이어] §o§8- 이섬을 [플레이어]에게 공유 시킵니다.");
             $pl->sendMessage($pr." /섬공유해제 [플레이어] §o§8- 이섬 공유자인 [플레이어]를 섬에서 공유해제시킵니다.");
            break;
        case '땅':
          $pl->sendMessage("준비중..");
          #현재 연구 중입니다..
      } return true;
    }

    /**EventListning Point*/
    public function blockbreak(BlockBreakEvent $ev){
      $pl = $ev->getPlayer();
      $num = $this->nowIsland($pl);
      if($pl->isOp() or $this->c['island'] [$this->nowIsland($pl)] ['owner'] == $pl->getName() or isset($this->c['island'] [$this->nowIsland($pl)] ['share'] [$pl->getName()])){
        $ev->setCancelled(false);
      }elseif($pl->getLevel()->getName() == 'island'){
        $ev->setCancelled();
        $pl->sendMessage($this->prefix."수정권한이 없습니다.");
      }
      if($pl->isOp() or $this->c['field'] [$this->nowIsland($pl)] ['owner'] == $pl->getName() or isset($this->c['field'] [$this->nowIsland($pl)] ['share'] [$pl->getName()])){
        $ev->setCancelled(false);
      }elseif($pl->getLevel()->getName() == 'field'){
        $ev->setCancelled();
        $pl->sendMessage($this->prefix."수정권한이 없습니다.");
    }
      if($pl->isOp() or $this->c['skyland'] [$this->nowIsland($pl)] ['owner'] == $pl->getName() or isset($this->c['skyland'] [$this->nowIsland($pl)] ['share'] [$pl->getName()])){
        $ev->setCancelled(false);
      }elseif($pl->getLevel()->getName() == 'skyland'){
        $ev->setCancelled();
        $pl->sendMessage($this->prefix."수정권한이 없습니다.");
    }
    }

    public function blockplace(BlockPlaceEvent $ev){
      $pl = $ev->getPlayer();
      $num = $this->nowIsland($pl);
      if($pl->isOp() or $this->c['island'] [$this->nowIsland($pl)] ['owner'] == $pl->getName() or isset($this->c['island'] [$this->nowIsland($pl)] ['share'] [$pl->getName()])){
        $ev->setCancelled(false);
      }elseif($pl->getLevel()->getName() == 'island'){
        $ev->setCancelled();
        $pl->sendMessage($this->prefix."수정권한이 없습니다.");
      }
      if($pl->isOp() or $this->c['field'] [$this->nowIsland($pl)] ['owner'] == $pl->getName() or isset($this->c['field'] [$this->nowIsland($pl)] ['share'] [$pl->getName()])){
        $ev->setCancelled(false);
      }elseif($pl->getLevel()->getName() == 'field'){
        $ev->setCancelled();
        $pl->sendMessage($this->prefix."수정권한이 없습니다.");
    }
      if($pl->isOp() or $this->c['skyland'] [$this->nowIsland($pl)] ['owner'] == $pl->getName() or isset($this->c['skyland'] [$this->nowIsland($pl)] ['share'] [$pl->getName()])){
        $ev->setCancelled(false);
      }elseif($pl->getLevel()->getName() == 'skyland'){
        $ev->setCancelled();
        $pl->sendMessage($this->prefix."수정권한이 없습니다.");
      }
    }

    /** 다른 곳에서 사용할 섬 메소드들*/
    public function SetIsland(int $num, Player $owner){
      if(isset($this->c['island'] [$num] ['owner'])){
        unset($this->c['island'] [$num] ['owner']);
      }else{
        $this->c['island'] [$num] = [
  				'share' => [],
  				'pos' => 103 + $num * 200,
  				'welcomeM' => "섬".$num."번에 오신것을 환영합니다."
  			];
      //  echo "setted1";
        $this->c['island']++;
      }
      $this->c['island'] [$num] ['owner'] = $owner->getName();
    //  echo "setted2";
      $owner->sendMessage($this->prefix."섬 ".$num."을 가지셨습니다!"); return true;
    }
    public function ShareIsland(int $num, Player $share){
      array_push($this->c['island'] [$num] ['share'], $share->getName());
      $share->sendMessage($this->prefix."섬 ".$num."번을 공유 받았습니다."); return true;
    }
    public function OutIsland(int $num, Player $outed){
      for($i = 0; $i >= count($this->c['island'] [$num] ['share']); $i++){
        if(! $this->c['island'] [$num] ['share'][$i] == $outed->getName()) continue;
        unset($this->c['island'] [$num] ['share'][$i]);
        $outed->sendMessage($this->prefix."당신은 섬".$num."번에서 퇴출당하셨습니다.");
        break;
      } return true;
    }
    public function WarpIsland(int $num, Player $player){
      $player->teleport($this->getServer()->getDefaultLevel('island')->getSafeSpawn());
      $player->teleport(new Vector3($num * 200 + 103, 13, 297));
      $player->sendMessage($this->prefix."섬".$num."번으로 이동하셨습니다."); return true;
    }
    public function getPlIslands($pname){
      $lands = [];
      foreach($this->c['island'] as $islandId => $islandData){
        if(strcasecmp($islandData['owner'], $pname) == 0){
          $lands[] = $islandId;
	}
      }
      return $lands;
    }
    public function nowIsland(Player $player){
      if($player->getLevel()->getName() !== 'island') return false;
      for ($i=0; $i >= $this->c['island'] ; $i++) {
        # code...
        if($player->distance(new Vector3(103 + $i * 200, 12, 297)) > 200) continue;
        return $i;
        break;
      }
    }
    public function getIsType(){
      return $this->s['island'] ['type'];
    }
}
