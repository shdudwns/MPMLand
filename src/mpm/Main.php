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
	    //코드 수정 필요 (GoldBigDragon님의 리퀘스트 넣은대로 수정 필요)
        return true;
    }
    public function WarpIs(Player $pl, $num){
        if(! $num <= $this->c->get('islast')){
            $pl->sendMessage($this->prefix."당신의 워프하고싶어하는 섬은 없습니다. 꺌꺌꺌");
            return true;
	}
        $pl->sendPopup($this->c->get('island')[$num] ['welcomeM']);
        return true;
    }
  /*  public static function getInstance(){
        return self::$Instance;
    }*/
}
