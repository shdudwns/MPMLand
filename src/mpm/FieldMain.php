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

use mpm\FieldGenerator as LandGenerator;

/* Author : PS88
 *
 * This php file is modified by GoldBigDragon (OverTook).
 */

class fieldMain extends PluginBase implements Listener{

  //  private $Instace;
    public $prefix = "§l§f[§bMPMLand§f]";
	private $c;

    public function onLoad(){
			@mkdir($this->getDataFolder());
        $this->c = new fieldMain();
        if( $this->c->__isset('flast')) return true;
        $this->c->set('flast', "0");
      //  self::$Instance = $this;
    }
    public function onEnable(){
        // field Name "Land"

		Generator::addGenerator(LandGenerator::class, "field");
		$gener = Generator::getGenerator("field");

		if(!($this->getServer()->loadLevel("field"))){
			@mkdir($this->getServer()->getDataPath() . "/" . "worlds" . "/" . "field"); //Okay, Remove method.
			$options = [];
			$this->getServer()->generateLevel("field", 0, $gener, $options);
			$this->getLogger()->info("땅 생성 완료.");
		}
		$this->getLogger()->info("땅 로드 완료.");
    }

    public function onCommand(CommandSender $sender, Command $command, string $label, array $args) : bool{
        if($command->getName() !== "땅") return true;
        if(! $sender instanceof Player) return true;
        $pl = $sender;
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
            if($this->getPlIs($pl->getName()) !== true){
            if(count($this->getPlIs($pl->getName())) >= 3 ){
              $pl->sendMessage($this->prefix."더이상의 땅을 구매하실 수 없습니다.");
              return true;
            }
          }
          if(! isset($this->c->get('field')[$args[1]] ['owner'])){
            $pl->sendMessage($this->prefix."더이상의 땅을 구매하실 수 없습니다.");
            return true;
          }
            $bnum = $args[1];
            array_push($this->c->get('field')[$bnum],
              'owner' => $pl->getName(),
              'welcomeM' => "땅".$bnum."번에 오신것을 환영합니다."
            );
              $pl->sendMessage($this->prefix."당신은 땅".$bnum."번을 구매하셨습니다.");
            break;
            case '공유':
            $num = $this->getIsnum($pl);
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
            $num = $this->getIsnum($pl);
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
            $num = $this->getIsnum($pl);
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
            for($i = 0; $num - $i * 20 < 20; $i++){
              if($num - $i * 20 < 20){
                $zl = $num - $i * 20;
                $xl = $i;
                break;
              }
            }
            $pl->teleport(new Position($xl * 32 + 2, 12, $zl * 32 + 2),0,0);
            $pl->sendPopup($this->prefix.$this->c->get('field')[$args[1]] ['welcomeM']);
            break;
          default:
            break;
        }
        return true;
    }
    public function getPlIs($pname){
      $a = [];
      if($this->c->get('flast') <= 0) return true;
      for($i = 0; $i >= $this->c->get('flast'); $i++){
        if($this->c->get('field')[$i] ['owner'] == $pname){
          array_push($a, $i);
        }
      }
      return $a;
    }
    public function getIsnum(Player $pl){
      if($pl->getLevel()->getName() !== "field"){$return = false; return true;}
      for($i = 0; $i >= $this->c->get('flast'); $i++){
        if($pl->distance(new Vector3($i * 32 + 2, 10, $i * 32 + 2)) <= 200){
          $return = $i;
          break;
        }
        if($i >= $this->c->get('flast')){
          $return = false;
          break;
        }
      }
      return $return;
    }
}
