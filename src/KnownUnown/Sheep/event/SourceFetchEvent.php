<?php
/**
 * Created by PhpStorm.
 * User: Andrew
 * Date: 5/1/2015
 * Time: 8:45 PM
 */

namespace KnownUnown\Sheep\event;


use pocketmine\command\CommandSender;
use pocketmine\event\plugin\PluginEvent;
use pocketmine\utils\PluginException;

class SourceFetchEvent extends PluginEvent{

    private $initiator;
    private $plugin;

    public function __construct(CommandSender $initiator, $plugin){
        if(!is_string($plugin)){
            throw new PluginException("Plugin provided to SourceFetchEvent must be of type String");
        }
        $this->initiator = $initiator;
        $this->plugin = $plugin;
    }


}