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
use pocketmine\utils\TextFormat;

class UninstallCommand extends Command{

    public function __construct(){
        parent::__construct("uninstall", "Unloads and removes a plugin", "/sheep uninstall <plugin>");
    }

    public function execute(CommandSender $sender, $label, array $args){
        $plugin = implode(' ', $args);
        if(trim($plugin) === '' || $plugin === null){
            $sender->sendMessage(TextFormat::RED . 'Please input a valid plugin.');
            return;
        }
        $sender->sendMessage(sprintf('Removing plugin %s...', $plugin));
        Server::getInstance()->getScheduler()->scheduleTask(new RemovePluginTask(Server::getInstance()->getPluginManager()->getPlugin('Sheep'), $args[0], $sender->getName()));
    }
}