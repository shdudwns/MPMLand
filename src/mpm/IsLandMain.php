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

use mpm\IsLandGenerator as LandGenerator;

/* Author : PS88
 *
 * This php file is modified by GoldBigDragon (OverTook).
 */

class IsLandMain extends PluginBase implements Listener{

  //  private $Instace;
    public $prefix = "§l§f[§bMPMLand§f]";
	private $c;

    public function onLoad(){
			@mkdir($this->getDataFolder());
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

		Generator::addGenerator(LandGenerator::class, "island");
		$gener = Generator::getGenerator("island");

		if(!($this->getServer()->loadLevel("island"))){
			@mkdir($this->getServer()->getDataPath() . "/" . "worlds" . "/" . "island"); //Okay, Remove method.
			$options = [];
			$this->getServer()->generateLevel("island", 0, $gener, $options);
			$this->getLogger()->info("섬 생성 완료.");
		}
		$this->getLogger()->info("섬 로드 완료.");
    }

    public function onCommand(CommandSender $sender, Command $command, string $label, array $args) : bool{
        if($command->getName() !== "섬") return true;
        if(! $sender instanceof Player) return true;
        $pl = $sender;
        switch ($args[0]) {
          case '구매':
            if(! EconomyAPI::getInstance()->myMoney($pl->getName()) >= 20000){
              $pl->sendMessage($this->prefix."돈이 부족합니다.");
              return true;
            }
            if(! isset($args[1])){
              $pl->sendMessage($this->prefix."/섬 구매 [번호]");
              return true;
            }
            if($this->getPlIs($pl->getName()) !== true){
            if(count($this->getPlIs($pl->getName())) >= 3 ){
              $pl->sendMessage($this->prefix."더이상의 섬을 구매하실 수 없습니다.");
              return true;
            }
          }
          if(! isset($this->c->get('island')[$args[1]] ['owner'])){
            $pl->sendMessage($this->prefix."더이상의 섬을 구매하실 수 없습니다.");
            return true;
          }
            $bnum = $args[1];
            array_push($this->c->get('island')[$bnum],
              'owner' => $pl->getName(),
              'welcomeM' => "섬".$bnum."번에 오신것을 환영합니다."
            );
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
            break;
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
}
