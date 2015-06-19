<?php
/**
 * Created by PhpStorm.
 * User: Andrew
 * Date: 6/19/2015
 * Time: 2:30 PM
 */

namespace KnownUnown\Sheep\task;


use KnownUnown\Sheep\Sheep;
use pocketmine\scheduler\PluginTask;

class RemovePluginTask extends PluginTask{

    private $plugin;
    private $pluginToRemove;

    private $commandSender;

    public function __construct(Sheep $plugin, $pluginToRemove, $commandSender){
        parent::__construct($plugin);
        $this->plugin = $plugin;
        $this->pluginToRemove = $this->plugin->getServer()->getPluginManager()->getPlugin($pluginToRemove);
        $this->commandSender = $commandSender;
    }

    public function onRun($currentTick){
        if($this->pluginToRemove === null){
            $this->plugin->message($this->commandSender, "Plugin not found!", "error");
        } else {
            $this->plugin->getServer()->getPluginManager()->disablePlugin($this->pluginToRemove);
            foreach(new \RegexIterator(new \DirectoryIterator($this->plugin->getServer()->getPluginPath()), sprintf('.*%s.*', $this->pluginToRemove->getName())) as $file){
                if(!is_dir($file)){
                    @unlink($file);
                    $deleted = true;
                }
            }
            if(!isset($deleted)){
                $this->plugin->message($this->commandSender, sprintf("Failed to remove plugin %s", $this->pluginToRemove->getName()), "error");
            } else {
                $this->plugin->message($this->commandSender, sprintf("Successfully removed plugin %s", $this->pluginToRemove->getName()));
            }
        }
    }
}