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
use KnownUnown\Sheep\task\FetchPluginTask;
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
        if($result['task'] === 0){
            /** @var Response $response */
            $response = $result['response'];
            switch($result['initiator']){
                case InitiatorType::COMMAND_INSTALL:
                    switch($response->getType()){
                        case ResponseType::SUCCESS_SINGLE_RESULT:
                            $task = new FetchPluginTask($response->getData(), true, InitiatorType::COMMAND_INSTALL, $result['commandSender']);
                            $this->getServer()->getScheduler()->scheduleAsyncTask($task);
                            break;
                        case ResponseType::SUCCESS_MULTIPLE_RESULTS:
                            $this->getLogger()->error(sprintf('Couldn\'t find plugin %s. You may have meant: %s.', $result['plugin'], $response->getData()));
                            break;
                        default:
                            $this->getLogger()->error(sprintf('There was an unknown error while fetching information for plugin %s.', $result['plugin']));
                    }
                    break;
                case InitiatorType::COMMAND_INFO:
                    switch($response->getType()){
                        case ResponseType::SUCCESS_SINGLE_RESULT:
                    }
            }
        }
    }

    public function onDisable(){

    }
} 