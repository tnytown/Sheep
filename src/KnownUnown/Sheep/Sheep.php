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
use KnownUnown\Sheep\task\FetchInfoTask;
use pocketmine\command\ConsoleCommandSender;
use pocketmine\plugin\PluginBase;
use pocketmine\utils\Config;

class Sheep extends PluginBase{

    private $commandMap;

    /* Configuration Files */
    /** @var Config */
    public $sourceList;
    /** @var Config */
    public $installedList;
    /** @var Config */
    public $config;

    public function onLoad(){

    }

    public function onEnable(){
        /* Configuration Setup */
        $this->getLogger()->info("Loading configuration.");
        $this->saveResource("dist-sources.yml");
        $this->saveResource("config.yml");
        $this->sourceList = new Config($this->getDataFolder() . "dist-sources.yml", Config::YAML);
        $this->installedList = new Config($this->getDataFolder() . "installed-plugins.json", Config::JSON);
        $this->config = new Config($this->getDataFolder() . "config.yml", Config::YAML);
        /* Command Setup */
        $this->getLogger()->info("Setting up commands.");
        $this->commandMap = new SheepCommandMap($this->getServer());
        $this->getServer()->getCommandMap()->register("sheep", new SheepCommand($this, $this->commandMap));

    }

    public function onTaskFinished($result){
        switch($result['initiator']){
            case InitiatorType::PLUGIN:
                /** @var $response Response */
                $response = $result['response'];
                switch($response->getType()){
                    case ResponseType::SUCCESS_SINGLE_RESULT:
                        break;
                    case ResponseType::SUCCESS_MULTIPLE_RESULTS:
                        break;
                    case ResponseType::FAILURE_NO_RESULTS:
                        break;
                    case ResponseType::FAILURE_GENERAL:
                        break;
                }
        }
    }

    public function onDisable(){

    }
} 