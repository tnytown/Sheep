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

class SheepCommand extends Command{

    private $plugin;
    private $commandMap;

    public function __construct(Sheep $plugin, SheepCommandMap $commandMap){
        $this->plugin = $plugin;
        $this->commandMap = $commandMap;
        parent::__construct("sheep", "Base command for the Sheep plugin", "/sheep <install|uninstall|update|upgrade|about>");
    }

    public function execute(CommandSender $sender, $command, array $args){
        if($args === []){
            $sender->sendMessage($this->getUsage());
        } else {
            $label = array_shift($args);
            $this->commandMap->getCommand($label)->execute($sender, $label, $args);
        }
    }
}