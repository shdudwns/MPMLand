<?php
namespace mpm;

use pocketmine\event\Listener;
use pocketmine\event\block\BlockBreakEvent;
use mpm\main;
use pocketmine\event\block\BlockPlaceEvent;

class EventListener implements Listener{
    public $main = new main();
    public function blockbreak(BlockBreakEvent $ev){
        if($pl->getLevel()->getName() !== "island") return true;
        $pl = $ev->getPlayer();
        if($this->main->getIsOwner($this->main->getIsNum($pl)) !== $pl->getName() or $this->main->getShare($this->main->getIsNum($pl)) !== $pl->getName()){
            $ev->setCancelled();
        }else{
            $ev->setCancelled(false);
        }
    }
    public function BlockPlace(BlockPlaceEvent $ev){
        $pl = $ev->getPlayer();
        if($pl->getLevel()->getName() !== "island") return true;
        if($this->main->getIsOwner($this->main->getIsNum($pl)) !== $pl->getName() or $this->main->getShare($this->main->getIsNum($pl)) !== $pl->getName()){
            $ev->setCancelled();
        }else{
            $ev->setCancelled(false);
        }
    }
}