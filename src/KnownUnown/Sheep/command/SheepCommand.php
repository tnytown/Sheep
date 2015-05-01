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
use pocketmine\command\CommandExecutor;
use pocketmine\command\CommandSender;

class SheepCommand extends Command implements CommandExecutor{

    private $plugin;
    private $commandMap;

    public function __construct(Sheep $plugin, SheepCommandMap $commandMap){
        $this->plugin = $plugin;
        $this->commandMap = $commandMap;
        parent::__construct("sheep", "Base command for the Sheep plugin", "/sheep <install|uninstall|update|upgrade|about>");
    }

    /**
     * @param CommandSender $sender
     * @param Command $command
     * @param string $label
     * @param string[] $args
     *
     * @return boolean
     */
    public function onCommand(CommandSender $sender, Command $command, $label, array $args){
        if($args === (null || [] || false)){
            $this->execute($sender, $label, $args);
        }
        $label = array_shift($args);
        $this->commandMap->getCommand($command)->execute($sender, $label, $args);
    }

    public function execute(CommandSender $sender, $command, array $args){
        $sender->sendMessage($this->getUsage());
    }
}