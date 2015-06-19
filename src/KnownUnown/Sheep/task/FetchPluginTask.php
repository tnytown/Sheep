<?php
/**
 * Created by PhpStorm.
 * User: Andrew
 * Date: 6/18/2015
 * Time: 5:48 PM
 */

namespace KnownUnown\Sheep\task;


use KnownUnown\Sheep\DownloadURL;
use KnownUnown\Sheep\InitiatorType;
use KnownUnown\Sheep\PluginInfo;
use pocketmine\scheduler\AsyncTask;
use pocketmine\utils\PluginException;
use pocketmine\Server;
use pocketmine\utils\Utils;

class FetchPluginTask extends AsyncTask{

    private $plugin;
    private $pluginToFetch;
    private $isRepo;

    private $initiator;
    private $commandSender;

    public function __construct($pluginToFetch, $isRepo = true, $initiator = InitiatorType::PLUGIN, $commandSender = null){
        if(!($pluginToFetch instanceof PluginInfo)){
            throw new PluginException("Plugin to fetch provided to FetchPluginTask must be of type PluginInfo");
        } else $this->pluginToFetch = $pluginToFetch;
        $this->isRepo = $isRepo;
        $this->plugin = Server::getInstance()->getPluginManager()->getPlugin("Sheep");
    }

    public function onRun(){
        if($this->isRepo){
            $plugin = Utils::getURL((new DownloadURL($this->pluginToFetch->getId(), $this->pluginToFetch->getName(), $this->pluginToFetch->getVersion()))->get());
            if($plugin !== false){
                $path = Server::getInstance()->getPluginPath() . $this->pluginToFetch->getName() . ".phar";
                file_put_contents($path, $plugin);
                Server::getInstance()->getPluginManager()->loadPlugin($path);
            }
        }
    }
}