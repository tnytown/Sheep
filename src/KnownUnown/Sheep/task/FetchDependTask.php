<?php
/**
 * Created by PhpStorm.
 * User: Andrew
 * Date: 6/21/2015
 * Time: 3:53 PM
 */

namespace KnownUnown\Sheep\task;


use KnownUnown\Sheep\Sheep;
use pocketmine\scheduler\PluginTask;

class FetchDependTask extends PluginTask{

    private $plugin;

    private $fetchPlugin;
    private $pluginData;
    private $softDepend;
    private $depend;

    private $commandSender;

    public function __construct(Sheep $plugin, $fetchPlugin, $pluginData, array $softDepend, array $depend, $commandSender = "CONSOLE"){
        parent::__construct($plugin);
        $this->plugin = $plugin;
        $this->fetchPlugin = $fetchPlugin;
        $this->pluginData = $pluginData;
        $this->softDepend = $softDepend;
        $this->depend = $depend;
        $this->commandSender = $commandSender;
    }

    public function onRun($currentTick){
        $this->plugin->message($this->commandSender, 'Not implemented :(');
    }
}