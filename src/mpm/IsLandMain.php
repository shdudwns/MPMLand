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
              'skyland' => [],
              'skylast' => 0,
              'land' => [],
              'llast' => 0
          ]);
          $this->c = $this->c->getAll();
          $this->s = new Config($this->getDataFolder().'setting.yml', Config::YAML, [
              'island' => [
                'prize' => 20000,
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
             ],
             'istype' => 'water'
          ]);
          $this->s = $this->s->getAll();
    }
    public function onEnable(){
        $this->getServer()->getPluginManager()->registerEvents($this, $this);
    if(!file_exists($this->getDataFolder())){
			mkdir($this->getDataFolder());
		}
        $downRP = false;
        if(!file_exists($this->getDataFolder() . "sspe12_2.mcpack")) {
            $downRP = true;
            echo TextFormat::toANSI("§l§f[§bMPMLand§f] 리소스팩 다운중");
            file_put_contents($this->getDataFolder() . "sspe12_2.mcpack", Utils::getURL("http://ryfol.weebly.com/uploads/4/7/6/5/47651895/sspe13_1.mcpack"));
        }
        echo str_repeat("\010", $downRP ? strlen(TextFormat::toANSI("§l§f[§bMPMLand§f] 리소스팩 다운중")) : 0) . TextFormat::toANSI("§f[Spooky] ⚪ Applying resource pack...   "); // Replacing latest message
        $pack = new ZippedResourcePack($this->getDataFolder() . "sspe12_2.mcpack");
        $r = new \ReflectionClass("pocketmine\\resourcepacks\\ResourcePackManager");
        // Reflection because devs thought it was a great idea to not let plugins manage resource packs :/
        $resourcePacks = $r->getProperty("resourcePacks");
        $resourcePacks->setAccessible(true);
        $rps = $resourcePacks->getValue($this->getServer()->getResourceManager());
        $rps[] = $pack;
        $resourcePacks->setValue($this->getServer()->getResourceManager(), $rps);
        $resourceUuids = $r->getProperty("uuidList");
        $resourceUuids->setAccessible(true);
        $uuids = $resourceUuids->getValue($this->getServer()->getResourceManager());
        $uuids[$pack->getPackId()] = $pack;
        $resourceUuids->setValue($this->getServer()->getResourceManager(), $uuids);
        // Forcing resource packs. We want the client to hear the music!
        $forceResources = $r->getProperty("serverForceResources");
        $forceResources->setAccessible(true);
        $forceResources->setValue($this->getServer()->getResourceManager(), true);
        echo str_repeat("\010", strlen("⚪ Applying resource pack... ")) . TextFormat::toANSI("§a✔️ 리소스팩 로드완료    \n");
    
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
      $ntype = $pl->getLevel()->getName();
      switch($ntype){
        case 'island': $nname = "수중섬"; break;
        case 'skyland': $nname = "하늘섬"; break;
        case 'field': $nname = "땅"; break;
        default: $nname = false;
      }
      if(! isset(array_shift($args))){
        $type = false;
      }else{
      switch(array_shift($args)){
        case "수중섬": $type = "island"; $a = 'islast'; break;
        case "하늘섬": $type = 'skyland'; $a = 'skylast'; break;
        case "땅": $type = 'field'; $a = 'flast'; break;
        default: $type = false; $a = false;
      }
    }
      $pr = $this->prefix;
          switch($cmd->getName()){
            case '땅구매':
            if(! $nname){
              $pl->sendMessage($pr." 타입종류 : 하늘섬, 수중섬, 땅"); return true;
            }
              if(EconomyAPI::getInstance()->myMoney($pl->getName()) < $this->s[$ntype] ['prize']){
                $pl->sendMessage($pr."돈이 부족합니다. ".$type." 가격 : ".$this->s[$ntype] ['prize']);
                return true;
              }
              if(count($this->getPllands($pl->getName(), $type)) >= $this->s[$type] ['max']){
                $pl->sendMessage($pr. "당신의 ".$chosen." 개수가 이미 제한 개수만큼 채워졌습니다."); return true;
              }
            //  echo "1";
              $this->Setland($this->c[$a], $pl, $type);
            //  echo "2";
              EconomyAPI::getInstance()->reduceMoney($pl->getName(), $this->s[$chosen] ['prize']);
             break;
           case '땅양도':
             if(! isset($args[0])){$pl->sendMessage($pr."/땅양도 [플레이어]"); return true;}
             if($nname == false or $this->c[$ntype] [$this->nowIsland($pl, $ntype)] ['owner'] !== $pl->getName()){$pl->sendMessage($pr."당신은 땅에 있지 않거나 당신의 땅이 아닌곳에 있습니다."); return true;}
             $this->Setland($this->nowland($pl, $ntype), $this->getServer()->getPlayer($args[0]), $ntype);
            break;
           case '땅이동':
           if(! $nname){
             $pl->sendMessage($pr." 타입종류 : 하늘섬, 수중섬, 땅"); return true;
           }
             if(! isset($args[0])){$pl->sendMessage($pr."/땅이동 [타입] [번호]"); return true;}
             $this->Warpland($args[0], $pl, $type);
            break;
           case '땅공유':
           if(! isset($args[0])){$pl->sendMessage($pr."/땅양도 [플레이어]"); return true;}
           if($nname == false or $this->c[$ntype] [$this->nowIsland($pl, $ntype)] ['owner'] !== $pl->getName()){$pl->sendMessage($pr."당신은 땅에 있지 않거나 당신의 땅이 아닌곳에 있습니다."); return true;}
           $this->Shareland($this->nowland($pl, $ntype), $this->getServer()->getPlayer($args[0]), $ntype);
          break;
           case '땅공유해제':
           if(! isset($args[0])){$pl->sendMessage($pr."/땅양도 [플레이어]"); return true;}
           if($nname == false or $this->c[$ntype] [$this->nowIsland($pl, $ntype)] ['owner'] !== $pl->getName()){$pl->sendMessage($pr."당신은 땅에 있지 않거나 당신의 땅이 아닌곳에 있습니다."); return true;}
           $this->Outland($this->nowland($pl, $ntype), $this->getServer()->getPlayer($args[0]), $ntype);
          break;
           case '땅':
             $pl->sendMessage($pr." /땅구매 [타입] §o§8- [타입]을 구매합니다.");
             $pl->sendMessage($pr." /땅양도 [플레이어] §o§8- 땅을 [플레이어] 에게 양도합니다.");
             $pl->sendMessage($pr." /땅이동 [타입] [번호] §o§8- [타입]의 [번호]으로 갑니다.");
             $pl->sendMessage($pr." /땅공유 [플레이어] §o§8- 이땅을 [플레이어]에게 공유 시킵니다.");
             $pl->sendMessage($pr." /땅공유해제 [플레이어] §o§8- 이땅 공유자인 [플레이어]를 섬에서 공유해제시킵니다.");
             $pl->sendMessage($pr." 타입종류 : 하늘섬, 수중섬, 땅");
            break;
    }return true;
}

    /**EventListning Point*/
    public function blockbreak(BlockBreakEvent $ev){
      $pl = $ev->getPlayer();
      $num = $this->nowland($pl, $pl->getLevel()->getName());
      if($pl->isOp() or $this->c['island'] [$this->nowland($pl, 'island')] ['owner'] == $pl->getName() or isset($this->c['island'] [$this->nowland($pl, 'island')] ['share'] [$pl->getName()])){
        $ev->setCancelled(false);
      }elseif($pl->getLevel()->getName() == 'island'){
        $ev->setCancelled();
        $pl->sendMessage($this->prefix."수정권한이 없습니다.");
      }
      if($pl->isOp() or $this->c['field'] [$this->nowland($pl)] ['owner'] == $pl->getName() or isset($this->c['field'] [$this->nowland($pl, 'field')] ['share'] [$pl->getName()])){
        $ev->setCancelled(false);
      }elseif($pl->getLevel()->getName() == 'field'){
        $ev->setCancelled();
        $pl->sendMessage($this->prefix."수정권한이 없습니다.");
    }
      if($pl->isOp() or $this->c['skyland'] [$this->nowland($pl)] ['owner'] == $pl->getName() or isset($this->c['skyland'] [$this->nowland($pl, 'skyland')] ['share'] [$pl->getName()])){
        $ev->setCancelled(false);
      }elseif($pl->getLevel()->getName() == 'skyland'){
        $ev->setCancelled();
        $pl->sendMessage($this->prefix."수정권한이 없습니다.");
      }
    }

    public function blockplace(BlockPlaceEvent $ev){
      $pl = $ev->getPlayer();
      $num = $this->nowland($pl, $pl->getLevel()->getName());
      if($pl->isOp() or $this->c['island'] [$this->nowland($pl, 'island')] ['owner'] == $pl->getName() or isset($this->c['island'] [$this->nowland($pl, 'island')] ['share'] [$pl->getName()])){
        $ev->setCancelled(false);
      }elseif($pl->getLevel()->getName() == 'island'){
        $ev->setCancelled();
        $pl->sendMessage($this->prefix."수정권한이 없습니다.");
      }
      if($pl->isOp() or $this->c['field'] [$this->nowland($pl)] ['owner'] == $pl->getName() or isset($this->c['field'] [$this->nowland($pl, 'field')] ['share'] [$pl->getName()])){
        $ev->setCancelled(false);
      }elseif($pl->getLevel()->getName() == 'field'){
        $ev->setCancelled();
        $pl->sendMessage($this->prefix."수정권한이 없습니다.");
    }
      if($pl->isOp() or $this->c['skyland'] [$this->nowland($pl)] ['owner'] == $pl->getName() or isset($this->c['skyland'] [$this->nowland($pl, 'skyland')] ['share'] [$pl->getName()])){
        $ev->setCancelled(false);
      }elseif($pl->getLevel()->getName() == 'skyland'){
        $ev->setCancelled();
        $pl->sendMessage($this->prefix."수정권한이 없습니다.");
      }
    }

    /** 다른 곳에서 사용할 섬 메소드들*/
    public function Setland(int $num, Player $owner, $type){
      if(isset($this->c[$type] [$num] ['owner'])){
        unset($this->c[$type] [$num] ['owner']);
      }else{
        switch($type){
          case 'island': $a = "수중섬"; break;
          case 'skyland': $a = "하늘섬"; break;
          case 'field': $a = "땅"; break;
          default: 'island'; break;
        }
        $this->c[$type] [$num] = [
  				'share' => [],
  				'welcomeM' => $a.$num."번에 오신것을 환영합니다."
  			];
      //  echo "setted1";
      $a = ($type == 'water')? 'islast':'skylast';
        $this->c[$a]++;
      }
      $this->c[$type] [$num] ['owner'] = $owner->getName();
    //  echo "setted2";
      $owner->sendMessage($this->prefix.$a.$num."을 가지셨습니다!"); return true;
    }
    public function Shareland(int $num, Player $share, $type){
      array_push($this->c[$type] [$num] ['share'], $share->getName());
      switch($type){
        case 'island': $a = "수중섬"; break;
        case 'skyland': $a = "하늘섬"; break;
        case 'field': $a = "땅"; break;
        default: 'island'; break;
      }
      $share->sendMessage($this->prefix.$a.$num."번을 공유 받았습니다."); return true;
    }
    public function Outland(int $num, Player $outed, $type){
      for($i = 0; $i >= count($this->c[$type] [$num] ['share']); $i++){
        if(! $this->c[$type] [$num] ['share'][$i] == $outed->getName()) continue;
        unset($this->c[$type] [$num] ['share'][$i]);
        switch($type){
          case 'island': $a = "수중섬"; break;
          case 'skyland': $a = "하늘섬"; break;
          case 'field': $a = "땅"; break;
          default: $a = 'island'; break;
        }
        $outed->sendMessage($this->prefix."당신은 ".$a.$num."번에서 퇴출당하셨습니다.");
        break;
      } return true;
    }
    public function WarpIsland(int $num, Player $player, $type){
      $player->teleport($this->getServer()->getDefaultLevel($type)->getSafeSpawn());
      $player->teleport(new Vector3($num * 200 + 103, 13, 297));
      switch($type){
        case 'island': $a = "수중섬"; break;
        case 'skyland': $a = "하늘섬"; break;
        case 'field': $a = "땅"; break;
        default: $a = 'island'; break;
      }
      $player->sendMessage($this->prefix.$a.$num."번으로 이동하셨습니다."); return true;
    }
    public function getPllands($pname, $type){
      $lands = [];
      foreach($this->c[$type] as $islandId => $islandData){
        if(strcasecmp($islandData['owner'], $pname) == 0){
          $lands[] = $islandId;
	}
      }
      return $lands;
    }
    public function nowland(Player $player, $type){
      if($player->getLevel()->getName() !== $type) return false;
      switch($type){
        case 'island': $a = 'islast'; break;
        case 'skyland': $a = 'skylast'; break;
        case 'field': $a = 'flast'; break;
        default: $a = 'islast'; break;
      }
      for ($i=0; $i >= $this->c[$a] ; $i++) {
        if($player->distance(new Vector3(103 + $i * 200, 12, 297)) > 200) continue;
        return $i;
        break;
      }
    }
}
