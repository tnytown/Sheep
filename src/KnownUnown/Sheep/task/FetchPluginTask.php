<?php
/**
 * Created by PhpStorm.
 * User: Andrew
 * Date: 6/18/2015
 * Time: 5:48 PM
 */

namespace KnownUnown\Sheep\task;


use KnownUnown\Sheep\Sheep;
use KnownUnown\Sheep\DownloadURL;
use KnownUnown\Sheep\InitiatorType;
use KnownUnown\Sheep\PluginInfo;
use pocketmine\scheduler\AsyncTask;
use pocketmine\utils\PluginException;
use pocketmine\Server;
use pocketmine\utils\Utils;

class FetchPluginTask extends AsyncTask{

    private $pluginToFetch;
    private $isRepo;

    private $initiator;
    private $commandSender;

    public function __construct($pluginToFetch, $isRepo = true, $initiator = InitiatorType::PLUGIN, $commandSender = "CONSOLE"){
        if(!($pluginToFetch instanceof PluginInfo)){
            throw new PluginException("Plugin to fetch provided to FetchPluginTask must be of type PluginInfo");
        } else $this->pluginToFetch = $pluginToFetch;

        $this->commandSender = $commandSender;
        $this->initiator = $initiator;
        $this->isRepo = $isRepo;
    }

    public function onRun(){
        if($this->isRepo){
            $plugin = Utils::getURL((new DownloadURL($this->pluginToFetch->getId(), $this->pluginToFetch->getName(), $this->pluginToFetch->getVersion()))->get());
            if($plugin !== false){
                $this->setResult($plugin);
            }
        }
    }

    public function onCompletion(Server $server){
        /** @var Sheep $plugin */
        $plugin = $server->getPluginManager()->getPlugin('Sheep');
        $path = $server->getPluginPath() . $this->pluginToFetch->getName() . ".phar";
        $result = ['response' => $this->pluginToFetch, 'commandSender' => $this->commandSender, 'initiator' => $this->initiator, 'task' => 1];

        file_put_contents($path, $this->getResult());
        $plugin->message($this->commandSender, sprintf('Fetched plugin %s [%d b]', $this->pluginToFetch->getName(), filesize($path)));
        $installed = $server->getPluginManager()->loadPlugin($plugin);
        $installed->onLoad();
        $installed->onEnable();
        $plugin->onTaskFinished($result);
    }
}