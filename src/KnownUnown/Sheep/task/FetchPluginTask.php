<?php
/**
 * Created by PhpStorm.
 * User: Andrew
 * Date: 6/18/2015
 * Time: 5:48 PM
 */

namespace KnownUnown\Sheep\task;


use KnownUnown\Sheep\InitiatorType;
use KnownUnown\Sheep\PluginInfo;
use pocketmine\scheduler\AsyncTask;
use pocketmine\utils\PluginException;
use pocketmine\Server;

class FetchPluginTask extends AsyncTask{

    private $plugin;
    private $pluginToFetch;

    private $initiator;
    private $commandSender;

    public function __construct($pluginToFetch, $initiator = InitiatorType::PLUGIN, $commandSender = null){
        if(!($pluginToFetch instanceof PluginInfo)){
            throw new PluginException("Plugin to fetch provided to FetchPluginTask must be of type PluginInfo");
        } else $this->pluginToFetch = $pluginToFetch;
        $this->plugin = Server::getInstance()->getPluginManager()->getPlugin("Sheep");
    }

    public function onRun(){
        
    }
}