<?php

namespace CJMustard1452\BowCoolDown\Tasks;

use CJMustard1452\BowCoolDown\Main;
use pocketmine\Player;use pocketmine\scheduler\Task;
use pocketmine\utils\Config;

class CoolDownTask extends Task{

	public $plugin;
	public $seconds = 0;
	private $getPlayer;
	public $coolDownFile;

	public function __construct(Main $plugin, Player $player) {
		$this->plugin = $plugin;
		$this->getPlayer = $player;
		$this->coolDownFile = new Config($this->plugin->getDataFolder() . "Cooldown", Config::YAML);
	}

	public function onRun($tick) {
		$this->seconds++;
		if(file_exists($this->plugin->getDataFolder() . "CoolDownTime")){
			if($this->seconds >= intval(file_get_contents($this->plugin->getDataFolder() . "CoolDownTime"))){
				$this->coolDownFile->reload();
				$this->coolDownFile->set($this->getPlayer->getName(), null);
				$this->coolDownFile->save();
				$this->plugin->getScheduler()->cancelTask($this->getTaskId());
			}
		}else{
			if($this->seconds >= 1.5){
				$this->coolDownFile->reload();
				$this->coolDownFile->set($this->getPlayer->getName(), null);
				$this->coolDownFile->save();
				$this->plugin->getScheduler()->cancelTask($this->getTaskId());
			}
		}	
	}
}