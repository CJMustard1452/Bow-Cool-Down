<?php

namespace CJMustard1452\BowCoolDown\Tasks;

use CJMustard1452\BowCoolDown\Main;
use pocketmine\Player;use pocketmine\scheduler\Task;
use pocketmine\utils\Config;

class CoolDownTask extends Task{

	public $plugin;
	private $coolDownFile;
	public $seconds = 0;
	private $getPlayer;

	public function __construct(Main $plugin, Player $player) {
		$this->plugin = $plugin;
		$this->getPlayer = $player;
		$this->coolDownFile = json_decode(file_get_contents($this->plugin->getDataFolder() . "Cooldown"), true);
	}

	public function onRun($tick) {
		$this->seconds++;
		if(file_exists($this->plugin->getDataFolder() . "CoolDownTime")){
			if($this->seconds >= intval(file_get_contents($this->plugin->getDataFolder() . "CoolDownTime"))){
				unset($this->coolDownFile[$this->getPlayer->getName()]);
				file_put_contents($this->plugin->getDataFolder() . "Cooldown", json_encode($this->coolDownFile));
				$this->plugin->getScheduler()->cancelTask($this->getTaskId());
			}
		}else{
			if($this->seconds >= 1.5){
				unset($this->coolDownFile[$this->getPlayer->getName()]);
				file_put_contents($this->plugin->getDataFolder() . "Cooldown", json_encode($this->coolDownFile));
				$this->plugin->getScheduler()->cancelTask($this->getTaskId());
			}
		}	
	}
}