<?php
/**
 * Created by PhpStorm.
 * User: Andrew
 * Date: 6/21/2015
 * Time: 3:53 PM
 */

namespace KnownUnown\Sheep\task;


use pocketmine\scheduler\AsyncTask;

class FetchDependTask extends AsyncTask{

    private $plugin;
    private $pluginData;
    private $softDepend;
    private $depend;

    private $commandSender;

    public function __construct($plugin, $pluginData, array $softDepend, array $depend, $commandSender = "CONSOLE"){
        $this->plugin = $plugin;
        $this->pluginData = $pluginData;
        $this->softDepend = $softDepend;
        $this->depend = $depend;
        $this->commandSender = $commandSender;
    }

    public function onRun(){

    }
}