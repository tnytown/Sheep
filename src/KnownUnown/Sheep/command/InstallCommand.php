<?php
/**
 * Created by PhpStorm.
 * User: Andrew
 * Date: 5/2/2015
 * Time: 10:50 AM
 */

namespace KnownUnown\Sheep\command;


use KnownUnown\Sheep\InitiatorType;
use KnownUnown\Sheep\task\FetchInfoTask;
use pocketmine\Server;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;

class InstallCommand extends Command {

    public function __construct(){
        parent::__construct("install", "Installs a plugin from sources", "/sheep install <plugin>");
    }

    public function execute(CommandSender $sender, $label, array $args){
        $plugin = implode(" ", $args);
        $sender->sendMessage(sprintf("Fetching info for plugin %s", $plugin));
        Server::getInstance()->getScheduler()->scheduleAsyncTask(new FetchInfoTask([$plugin], InitiatorType::COMMAND_INSTALL, Server::getInstance()->getPluginManager()->getPlugin("Sheep")->sourceList->get(0), $sender->getName()));
    }
}