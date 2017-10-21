<?php
namespace mpm\SubCommand;

use pocketmine\plugin\PluginBase;
use pocketmine\event\Listener;
use pocketmine\command\CommandSender;
use mpm\main;
use onebone\economyapi\EconomyAPI;

class SubCommand extends PluginBase implements Listener{
    public function __construct(CommandSender $pl, array $args){
        $this->pl = $pl;
        $this->args = $args;
        //parent::__construct($pl, $args);
    }
    public function execute() : bool{
        $main = new main();
        $pl = $this->pl;
        $args = $this->args;
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
                    if($main->getIsOwner($i) !== $args[1]) return true;
                    array_push($as, $i);
                }
                if(count($as) >= 3){
                    $pl->sendMessage($main->prefix."당신의 섬 개수가 최대를 채웠습니다.");
                    return true;
                }
                $main->setIs($pl->getName(), count($main->c->get('island')));
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
                if(! is_numerick($args[1])){
                    $as = [];
                    for($i = 0; $i >= count($main->c->get('island')); $i++){
                        if($main->getIsOwner($i) !== $args[1]) return true;
                        array_push($as, "[".$i."]");
                    }
                    $pl->sendMessage($main->prefix.$args[1]."님의 섬 목록을 표시합니다.");
                    $pl->sendMessage($as);
                    return true;
                }
                $main->WarpIs($pl, $args[1]);
                return true;
        }
    }
}