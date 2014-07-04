<?php
/**
 * Created by PhpStorm.
 * User: andrew
 * Date: 7/3/14
 * Time: 7:14 PM
 */

namespace KnownUnown\Sheep;


use pocketmine\command\CommandSender;
use pocketmine\plugin\PluginBase;
use pocketmine\command\Command;
use KnownUnown\Sheep\command\CommandProcessor;

class Sheep extends PluginBase {


    public function onLoad(){

    }

    public function onEnable(){

    }

    public function onCommand(CommandSender $sender, Command $command, $label, array $args){
        switch($command->getName()){
            case "sheep":

        }
    }

    public function onDisable(){

    }
} 