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
use pocketmine\level\generator\Generator;
use pocketmine\event\player\PlayerMoveEvent;
use pocketmine\event\block\{BlockPlaceEvent, BlockBreakEvent};

use mpm\IsLandGenerator as LandGenerator;
use mpm\FieldGenerator;

/* Author : PS88
 *
 * This php file is modified by GoldBigDragon (OverTook).
 */

class IsLandMain extends PluginBase implements Listener{

    public $prefix = "§l§f[§bMPMLand§f]";
	private $c, $s;
  private $nis = [];


      public function onLoad(){
        @mkdir($this->getDataFolder());
          $this->c = new Config($this->getDataFolder().'data.json', Config::JSON, [
              'island' => [],
              'land' => []
          ]);
          $this->s = new Config($this->getDataFolder().'setting.yml', Config::YAML, [
              'island' => [
                'prize' => 20000,
                'istype' => 'water',
                'make' => true,
                'pvp' => true
              ],
              'field' => [
                'prize' => 20000,
                'pvp' => true,
                'make' => true
              ]
          ]);
          if( $this->c->__isset('flast')){
          $this->c->set('flast', "0");
        }

         if( $this->c->__isset('islast')){
         $this->c->set('islast', "0");
       }
    /*  while (true) {
      if(! $this->c->__isset('islast')){
				$this->c->set('islast', 0);
			}
			$num = $this->c->get('islast');
			$this->c->get('island')[$num] = [
				'share' => [],
				'welcomeM' => "섬".$num."번입니다. 가격 : 20000원",
        'pos' => 103 + $num * 200
			];
			$this->c->__unset('islast');
			$this->c->set('islast', $num + 1);
    }*/
    }
    public function onEnable(){
        $this->getServer()->getPluginManager()->registerEvents($this, $this);
        // Island Name "Land"

    if($this->s->get('island')['make']){
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
  if($this->s->get('field')['make']){
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
  }
    public function onDisalbe(){
      $this->c->save();
    }
    public function getAll(){
      return $this->c->getAll();
    }
    public function Move(PlayerMoveEvent $ev){
      if($ev->getPlayer()->getLevel()->getName() !== "island"){
        unset($this->nis[$ev->getPlayer()->getName()]]);
        return true;
      }

      if(! isset($this->nis[$ev->getPlayer()->getName()]) or $this->nis[$ev->getPlayer()->getName()] !== $this->getIsnum($ev->getPlayer())){
        $num = $this->getIsnum($ev->getPlayer());
        $ev->getPlayer()->sendPopup($this->c->get('island')[$num] ['WelcomeM']);
        return true;
      }
    }
    public function distroy(BlockPlaceEvent $ev){
      if($ev->getPlayer()->getLevel()->getName() !== "island") return true;
      $d = $ev->getPlayer();
      $x = $d->getX();
      $z = $d->getZ();
      $num = $this->getIsnum($ev->getPlayer());
      if($this->c->get('island')[$num] ['owner'] == $ev->getPlayer()->getName()){
        $ev->setCancelled(false);
      }else{
        $ev->setCancelled(true);
        $ev->getPlayer()->sendMessage($this->prefix."당신이 수정할 수 있는 섬이 아닙니다");
      }
    }
    public function distroy(BlockBreakEvent $ev){
      if($ev->getPlayer()->getLevel()->getName() !== "island") return true;
      $d = $ev->getPlayer();
      $x = $d->getX();
      $z = $d->getZ();
      $num = $this->getIsnum($ev->getPlayer());
      if($this->c->get('island')[$num] ['owner'] == $ev->getPlayer()->getName()){
        $ev->setCancelled(false);
      }else{
        $ev->setCancelled(true);
        $ev->getPlayer()->sendMessage($this->prefix."당신이 수정할 수 있는 섬이 아닙니다");
      }
    }

    public function onCommand(CommandSender $sender, Command $command, string $label, array $args) : bool{
        if(! $sender instanceof Player) return true;
        $pl = $sender;
        switch($command->getName()){
          case '섬':
        switch ($args[0]) {
          case '구매':
            if(! EconomyAPI::getInstance()->myMoney($pl->getName()) >= 20000){
              $pl->sendMessage($this->prefix."돈이 부족합니다.");
              return true;
            }
            /*if(! isset($args[1])){
              $pl->sendMessage($this->prefix."/섬 구매 [번호]");
              return true;
            }*/
            if($this->getPlIs($pl->getName()) !== true){
            if(count($this->getPlIs($pl->getName())) >= 3 ){
              $pl->sendMessage($this->prefix."더이상의 섬을 구매하실 수 없습니다.");
              return true;
            }
          }
          /*if(! isset($this->c->get('island')[$args[1]] ['owner'])){
            $pl->sendMessage($this->prefix."이 섬은 주인이 있습니다.");
            return true;
          }*/
          if(! $this->c->__isset('islast')){
            $this->c->set('islast',0);
          }
            $num = $this->c->get('islast');
            $this->c->get('island')[$num] = [
              'owner' => $pl->getName(),
              'share' => [],
              'pos' => 103 + $num * 200,
              'welcomeM' => "섬".$num."번에 오신것을 환영합니다."
            ];
            $bnum = $num;
              $pl->sendMessage($this->prefix."당신은 섬".$bnum."번을 구매하셨습니다.");
            break;
            case '공유':
            $num = $this->getIsnum($pl);
            if($num == false){
              $pl->sendMessage($this->prefix."당신은 아무 섬에도 있지 않습니다.");
              return true;
            }
            if(! $this->c->get('island')[$num] ['owner'] <= $pl->getName()){
              $pl->sendMessage($this->prefix."당신의 섬이 아닙니다..");
              return true;
            }
            if(! isset($args[1])){
              $pl->sendMessage($this->prefix."/섬 공유 [플레이어]");
              return true;
            }
            array_push($this->c->get('island')[$num] ['share'], $args[1]);
            $pl->sendMessage($this->prefix."당신의 섬을".$args[1]."님께 공유하셨습니다.");
            return true;
            case '공유해제':
            $num = $this->getIsnum($pl);
            if($num == false){
              $pl->sendMessage($this->prefix."당신은 아무 섬에도 있지 않습니다.");
              return true;
            }
            if(! $this->c->get('island')[$num] ['owner'] <= $pl->getName()){
              $pl->sendMessage($this->prefix."당신의 섬이 아닙니다..");
              return true;
            }
            if(! isset($args[1])){
              $pl->sendMessage($this->prefix."/섬 공유해제 [플레이어]");
              return true;
            }
            for ($i=0; $i < count($this->c->get('island')[$i] ['share']); $i++) {
              if($this->c->get('island')[$i] ['share'][$i] == $args[1]){
                $exist = $i;
                break;
              }
            }
            if(! isset($exist)){
              $pl->sendMessage($this->prefix.$args[1]."님은 이 섬 공유자가 아닙니다");
              return true;
            }
            unset($this->c->get('island')[$i] ['share'][$exist]);
            $pl->sendMessage($this->prefix."공유자".$args[1]."님을 섬에서 공유 해제하였습니다.");
            break;
            case '양도':
            $num = $this->getIsnum($pl);
            if($num == false){
              $pl->sendMessage($this->prefix."당신은 아무 섬에도 있지 않습니다.");
              return true;
            }
            if(! $this->c->get('island')[$num] ['owner'] <= $pl->getName()){
              $pl->sendMessage($this->prefix."당신의 섬이 아닙니다..");
              return true;
            }
            if(! isset($args[1])){
              $pl->sendMessage($this->prefix."/섬 양도 [플레이어]");
              return true;
            }
            unset($this->c->get('island')[$num] ['owner']);
            $this->c->get('island')[$num] ['owner'] = $args[1];
            $pl->sendMessage($this->prefix."이 섬을".$args[1]."님께 양도하셨습니다.");
            break;
            case '이동':
            if(! isset($args[1])){
              $pl->sendMessage($this->prefix."/섬 이동 [섬번호]");
              return true;
            }
            $pl->teleport(new Position(103 + $args[1] * 200, 12, 297),0,0);
            $pl->sendPopup($this->prefix.$this->c->get('island')[$args[1]] ['welcomeM']);
            break;
          default:
          $pl->sendMessage($this->prefix."/섬 [구매/양도/이동/공유/공유해제]");
            break;
        }
        break;
        case '땅':
        switch ($args[0]) {
          case '구매':
            if(! EconomyAPI::getInstance()->myMoney($pl->getName()) >= 100000){
              $pl->sendMessage($this->prefix."돈이 부족합니다.");
              return true;
            }
            if(! isset($args[1])){
              $pl->sendMessage($this->prefix."/땅 구매 [번호]");
              return true;
            }
            if($this->getPlFi($pl) == true){
            if(count($this->getPlFi($pl)) >= 3 ){
              $pl->sendMessage($this->prefix."더이상의 땅을 구매하실 수 없습니다.");
              return true;
            }
          }
          if(! isset($this->c->get('field')[$args[1]] ['owner'])){
            $pl->sendMessage($this->prefix."더이상의 땅을 구매하실 수 없습니다.");
            return true;
          }
            $bnum = $args[1];
            $this->c->get('field')[$bnum] ['owner'] = $pl->getName();
            $this->c->get('field')[$bnum] ['welcomeM'] = "땅".$bnum."번에 오신것을 환영합니다.";
              $pl->sendMessage($this->prefix."당신은 땅".$bnum."번을 구매하셨습니다.");
            break;
            case '공유':
            $num = $this->getFinum($pl);
            if($num == false){
              $pl->sendMessage($this->prefix."당신은 아무 땅에도 있지 않습니다.");
              return true;
            }
            if(! $this->c->get('field')[$num] ['owner'] <= $pl->getName()){
              $pl->sendMessage($this->prefix."당신의 땅이 아닙니다..");
              return true;
            }
            if(! isset($args[1])){
              $pl->sendMessage($this->prefix."/땅 공유 [플레이어]");
              return true;
            }
            array_push($this->c->get('field')[$num] ['share'], $args[1]);
            $pl->sendMessage($this->prefix."당신의 땅을".$args[1]."님께 공유하셨습니다.");
            return true;
            case '공유해제':
            $num = $this->getFinum($pl);
            if($num == false){
              $pl->sendMessage($this->prefix."당신은 아무 땅에도 있지 않습니다.");
              return true;
            }
            if(! $this->c->get('field')[$num] ['owner'] <= $pl->getName()){
              $pl->sendMessage($this->prefix."당신의 땅이 아닙니다..");
              return true;
            }
            if(! isset($args[1])){
              $pl->sendMessage($this->prefix."/땅 공유해제 [플레이어]");
              return true;
            }
            for ($i=0; $i < count($this->c->get('field')[$i] ['share']); $i++) {
              if($this->c->get('field')[$i] ['share'][$i] == $args[1]){
                $exist = $i;
                break;
              }
            }
            if(! isset($exist)){
              $pl->sendMessage($this->prefix.$args[1]."님은 이 땅 공유자가 아닙니다");
              return true;
            }
            unset($this->c->get('field')[$i] ['share'][$exist]);
            $pl->sendMessage($this->prefix."공유자".$args[1]."님을 땅에서 공유 해제하였습니다.");
            break;
            case '양도':
            $num = $this->getFinum($pl);
            if($num == false){
              $pl->sendMessage($this->prefix."당신은 아무 땅에도 있지 않습니다.");
              return true;
            }
            if(! $this->c->get('field')[$num] ['owner'] <= $pl->getName()){
              $pl->sendMessage($this->prefix."당신의 땅이 아닙니다..");
              return true;
            }
            if(! isset($args[1])){
              $pl->sendMessage($this->prefix."/땅 양도 [플레이어]");
              return true;
            }
            unset($this->c->get('field')[$num] ['owner']);
            $this->c->get('field')[$num] ['owner'] = $args[1];
            $pl->sendMessage($this->prefix."이 땅을".$args[1]."님께 양도하셨습니다.");
            break;
            case '이동':
            if(! isset($args[1])){
              $pl->sendMessage($this->prefix."/땅 이동 [땅번호]");
              return true;
            }
            $num = $args[1];
            $a = $this->c->get('field')[$num] ['pos'];
            $x = $a['x'];
            $y = $a['y'];
            $z = $a['z'];
            $level = $a['level'];
            $pl->teleport(new Position($x, $y, $z, $level),0,0);
            $pl->sendPopup($this->prefix.$this->c->get('field')[$args[1]] ['welcomeM']);
            break;
          default:
          $pl->sendMessage($this->prefix."/섬 [구매/양도/이동/공유/공유해제]");
            break;
        }
      }
        return true;
    }
    public function getPlIs($pname){
      $a = [];
      if($this->c->get('islast') <= 0) return true;
      for($i = 0; $i >= $this->c->get('islast'); $i++){
        if($this->c->get('island')[$i] ['owner'] == $pname){
          array_push($a, $i);
        }
      }
      return $a;
    }
    public function getIsnum(Player $pl){
      if($pl->getLevel()->getName() !== "island"){$return = false; return true;}
      for($i = 0; $i >= $this->c->get('islast'); $i++){
        if($pl->distance(new Vector3(103 + $i * 200, 12, 297)) <= 200){
          $return = $i;
          break;
        }
        if($i >= $this->c->get('islast')){
          $return = false;
          break;
        }
      }
      return $return;
    }
    public function getPlFi($pname){
      $a = [];
      if($this->c->get('flast') <= 0) return true;
      for($i = 0; $i >= $this->c->get('flast'); $i++){
        if($this->c->get('field')[$i] ['owner'] == $pname){
          array_push($a, $i);
        }
      }
      return $a;
    }
    public function getFinum(Player $pl){
      for($i = 0; $i >= $this->c->get('islast'); $i++){
        $af = $this->c->get('field')[$i] ['fpos'];
        $xf = $af['x'];
        $zf = $af['z'];
        $a = $this->c->get('field')[$num] ['lpos'];
        $xl = $a['x'];
        $zl = $a['z'];
        if(! $xf <= $pl->getX())continue;
        if(! $pl->getX() <= $xl) continue;
        if(! $zf <= $pl->getZ())continue;
        if(! $pl->getZ() <= $zl) continue;
          if($pl->getLevel()->getName() !== $this->c->get('field')[$i] ['pos'] ['level']) continue;
          $return = $i;
          break;

        if($i >= $this->c->get('flast')){
          $return = false;
          return true;
          break;
        }
      }
        return $return;
      }
    }
