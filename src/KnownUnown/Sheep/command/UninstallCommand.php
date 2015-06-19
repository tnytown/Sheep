<?php
/**
 * Created by PhpStorm.
 * User: Andrew
 * Date: 6/19/2015
 * Time: 3:45 PM
 */

namespace KnownUnown\Sheep\command;


use KnownUnown\Sheep\task\RemovePluginTask;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\Server;

class UninstallCommand extends Command{

    public function __construct(){
        parent::__construct("uninstall", "Unloads and removes a plugin", "/sheep uninstall <plugin>");
    }

    public function execute(CommandSender $sender, $label, array $args){
        $sender->sendMessage(sprintf('Removing plugin %s...', $args[0]));
        Server::getInstance()->getScheduler()->scheduleTask(new RemovePluginTask(Server::getInstance()->getPluginManager()->getPlugin('Sheep'), $args[0], $sender->getName()));
    }
}