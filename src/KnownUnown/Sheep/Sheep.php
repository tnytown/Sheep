<?php
/**
 * Created by PhpStorm.
 * User: andrew
 * Date: 7/3/14
 * Time: 7:14 PM
 */

namespace KnownUnown\Sheep;

use KnownUnown\Sheep\command\SheepCommand;
use KnownUnown\Sheep\command\SheepCommandMap;
use pocketmine\plugin\PluginBase;

class Sheep extends PluginBase{

    private $commandMap;

    public function onLoad(){

    }

    public function onEnable(){
        $this->commandMap = new SheepCommandMap($this->getServer());
        $this->getServer()->getCommandMap()->register("sheep", new SheepCommand($this, $this->commandMap));
    }

    public function onDisable(){

    }
} 