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
use pocketmine\utils\Config;

class Sheep extends PluginBase{

    private $commandMap;

    /* Configuration Files */
    public $sourceList;
    public $installedList;
    public $config;

    public function onLoad(){

    }

    public function onEnable(){
        /* Configuration Setup */
        $this->saveResource("dist-sources.yml");
        $this->saveResource("config.yml");
        $this->sourceList = new Config($this->getDataFolder() . "dist-sources.yml", Config::YAML);
        $this->installedList = new Config($this->getDataFolder() . "installed-plugins.json", Config::JSON);
        $this->config = new Config($this->getDataFolder() . "config.yml", Config::YAML);
        /* Command Setup */
        $this->commandMap = new SheepCommandMap($this->getServer());
        $this->getServer()->getCommandMap()->register("sheep", new SheepCommand($this, $this->commandMap));
    }

    public function onDisable(){

    }
} 