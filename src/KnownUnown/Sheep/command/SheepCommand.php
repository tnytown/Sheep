<?php
/**
 * Created by PhpStorm.
 * User: Andrew
 * Date: 5/1/2015
 * Time: 5:03 PM
 */

namespace KnownUnown\Sheep\command;


use KnownUnown\Sheep\Sheep;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\utils\TextFormat;

class SheepCommand extends Command{

    private $plugin;
    private $commandMap;

    public function __construct(Sheep $plugin, SheepCommandMap $commandMap){
        $this->plugin = $plugin;
        $this->commandMap = $commandMap;
        parent::__construct("sheep", "Base command for the Sheep plugin", "/sheep <install|uninstall|info|about>");
    }

    public function execute(CommandSender $sender, $command, array $args){
        if(!$sender->hasPermission('sheep.command.base')){
            //DIEEE POTATOOOOOO
            $sender->sendMessage(TextFormat::RED . 'Haha, you can\'t use this!');
            return false;
        }
        if($args === []){
            $sender->sendMessage(TextFormat::RED . "Usage: " . $this->getUsage());
        } else {
            $label = array_shift($args);
            $command = $this->commandMap->getCommand($label);
            if($command !== null){
                $command->execute($sender, $label, $args);
            } else {
                $sender->sendMessage(TextFormat::RED . "Usage: " . $this->getUsage());
            }
        }
        return true;
    }
}