<?php

declare(strict_types=1);

namespace CJMustard1452\BowCoolDown;

use CJMustard1452\BowCoolDown\Tasks\CoolDownTask;
use DaveRandom\CallbackValidator\Type;
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
			$this->coolDownFile = json_decode(file_get_contents($this->getDataFolder() . "Cooldown"), true);
	}

	public function onDisable() :Void{
		if(file_exists($this->getDataFolder() . "Cooldown")){
			unlink($this->getDataFolder() . "Cooldown");
		}
	}

	public function onJoin(PlayerJoinEvent $event){
		if(file_exists($this->getDataFolder() . "Cooldown")){
			unset($this->coolDownFile[$event->getPlayer()->getName()]);
			file_put_contents($this->getDataFolder() . "Cooldown", json_encode($this->coolDownFile));
		}
	}

	public function onShoot(EntityShootBowEvent $event){
		if($event->getEntity() instanceof Player){
			if(isset(json_decode(file_get_contents($this->getDataFolder() . "Cooldown"), true)[$event->getEntity()->getName()])){
				$this->getServer()->getPlayer($event->getEntity()->getName())->sendActionBarMessage("§6Bow cooldown §31.5§6 seconds.");
				$event->setCancelled(true);
			}else{
				$this->getScheduler()->scheduleRepeatingTask(new CoolDownTask($this, $this->getServer()->getPlayer($event->getEntity()->getName())), 20);
				$this->coolDownFile[$event->getEntity()->getName()] = true;
				file_put_contents($this->getDataFolder() . "Cooldown", json_encode($this->coolDownFile));
			}
		}
	}
}
