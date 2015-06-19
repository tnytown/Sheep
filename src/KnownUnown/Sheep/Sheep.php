<?php
/**
 * Created by PhpStorm.
 * User: andrew
 * Date: 7/3/14
 * Time: 7:14 PM
 */

namespace KnownUnown\Sheep;

use KnownUnown\Sheep\command\InfoCommand;
use KnownUnown\Sheep\command\InstallCommand;
use KnownUnown\Sheep\command\SheepCommand;
use KnownUnown\Sheep\command\SheepCommandMap;
use KnownUnown\Sheep\command\UninstallCommand;
use KnownUnown\Sheep\task\FetchPluginTask;
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
        $this->commandMap->register("sheep", new InstallCommand());
        $this->commandMap->register('sheep', new UninstallCommand());
        $this->commandMap->register("sheep", new InfoCommand());
        $this->getLogger()->info('Done!');
    }

    public function onTaskFinished($result){
        $sender = $result['commandSender'];
        if($result['task'] === 0){
            /** @var Response $response */
            $response = $result['response'];
            switch($result['initiator']){
                case InitiatorType::COMMAND_INSTALL:
                    switch($response->getType()){
                        case ResponseType::SUCCESS_SINGLE_RESULT:
                            $task = new FetchPluginTask($response->getData(), true, InitiatorType::COMMAND_INSTALL, $sender);
                            $this->getServer()->getScheduler()->scheduleAsyncTask($task);
                            break;
                        case ResponseType::SUCCESS_MULTIPLE_RESULTS:
                            $this->message($sender, sprintf('Couldn\'t find plugin %s. You may have meant: %s.', $result['plugin'], $response->getData()));
                            break;
                        default:
                            $this->message($sender, sprintf('There was an unknown error while fetching information for plugin %s.', $result['plugin']));
                    }
                    break;
                case InitiatorType::COMMAND_INFO:
                    switch($response->getType()){
                        case ResponseType::SUCCESS_SINGLE_RESULT:
                            /** @var PluginInfo $info */
                            $info = $response->getData();
                            $this->message($sender, sprintf('Information about plugin %s, version id %d:', $info->getName(), $info->getVersion()));
                            $this->message($sender, sprintf('tagline: %s', $info->getDesc()));
                            $this->message($sender, sprintf('Category: %s, Rating: %s/5, Download count: %d', $info->getCat(), $info->getRating(), $info->getDownloads()));
                            $this->message($sender, sprintf('Install %s by running /sheep install %s!', $info->getName(), $info->getName()));
                            break;
                        case ResponseType::SUCCESS_MULTIPLE_RESULTS:
                            $this->message($sender, sprintf('Couldn\'t find plugin %s. You may have meant: %s.', $result['plugin'], $response->getData()));
                    }
            }
        } else {
            $this->message($sender, sprintf('Successfully installed plugin %s!', $result['response']->getName()));
        }
    }

    public function message($identifier, $message, $level = "info"){
        if($identifier === "CONSOLE"){
            $this->getLogger()->log($level, $message);
        } else {
            $player = $this->getServer()->getPlayer($identifier);
            if($player !== null) $player->sendMessage($message);
        }
    }

    public function onDisable(){

    }
} 