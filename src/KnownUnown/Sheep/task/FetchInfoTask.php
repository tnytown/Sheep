<?php
/**
 * Created by PhpStorm.
 * User: Andrew
 * Date: 5/1/2015
 * Time: 6:13 PM
 */

namespace KnownUnown\Sheep\task;


use KnownUnown\Sheep\Sheep;
use pocketmine\command\CommandSender;
use pocketmine\command\ConsoleCommandSender;
use pocketmine\scheduler\AsyncTask;
use pocketmine\utils\Config;
use pocketmine\utils\PluginException;
use pocketmine\utils\Utils;

class FetchInfoTask extends AsyncTask{

    private $pluginToFetch;
    private $plugin;

    private $initiator;
    private $isDetailed;

    public function __construct($pluginToFetch, Sheep $plugin, CommandSender $initiator){
        if(!is_string($pluginToFetch)){
            throw new PluginException("Plugin provided to FetchInfoTask must be of type String");
        } else {
            $this->pluginToFetch = $pluginToFetch;
        }
        if($initiator instanceof ConsoleCommandSender){
            $this->isDetailed = true;
        }
        $this->plugin = $plugin;
        $this->initiator = $initiator;
    }

    public function onRun(){
        $this->initiator->sendMessage("Starting local info update.");
        $fetchList = new Config($this->plugin->getDataFolder() . "fetch-list.json", Config::JSON, $this->plugin->getResource("fetch-list.json"));
        foreach($this->plugin->sourceList->getAll() as $source){
            if($this->isDetailed){
                $this->initiator->sendMessage("Get: " . $source . "[" . filesize($source) . "]");
            }
            $fetchList->setAll(Utils::getURL($source));
        }
        $fetchList->save();
        $this->initiator->sendMessage("Done.");
    }
}