<?php

declare(strict_types=1);

namespace CJMustard1452\BowCoolDown;

use CJMustard1452\BowCoolDown\Tasks\CoolDownTask;
use pocketmine\event\entity\EntityShootBowEvent;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\Player;
use pocketmine\plugin\PluginBase;
use pocketmine\utils\Config;

class Main extends PluginBase implements Listener{

	public $coolDownFile;

	public function onEnable() :Void{
		$this->getServer()->getPluginManager()->registerEvents($this, $this);
			new Config($this->getDataFolder() . "Cooldown", Config::JSON);
			$this->coolDownFile = new Config($this->getDataFolder() . "Cooldown", Config::YAML);
	}

	public function onDisable() :Void{
		if(file_exists($this->getDataFolder() . "Cooldown")){
			unlink($this->getDataFolder() . "Cooldown");
		}
	}

	public function onJoin(PlayerJoinEvent $event){
		if(file_exists($this->getDataFolder() . "Cooldown")){
			$this->coolDownFile->reload();
			$this->coolDownFile->set($event->getPlayer()->getName(), false);
			$this->coolDownFile->save();
		}
	}

	public function onShoot(EntityShootBowEvent $event){
		if($event->getEntity() instanceof Player){
			$this->coolDownFile->reload();
			if($this->coolDownFile->get($event->getEntity()->getName()) == true){
				$this->getServer()->getPlayer($event->getEntity()->getName())->sendActionBarMessage("§6Bow cooldown §31.5§6 seconds.");
				$event->setCancelled(true);
			}else{
				$this->getScheduler()->scheduleRepeatingTask(new CoolDownTask($this, $this->getServer()->getPlayer($event->getEntity()->getName())), 20);
				$this->coolDownFile->reload();
				$this->coolDownFile->set($event->getEntity()->getName(), true);
				$this->coolDownFile->save();
			}
		}
	}
}
