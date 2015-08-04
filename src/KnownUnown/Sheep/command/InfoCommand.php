<?php
/**
 * Created by PhpStorm.
 * User: Andrew
 * Date: 6/19/2015
 * Time: 1:19 PM
 */

namespace KnownUnown\Sheep\command;


use KnownUnown\Sheep\InitiatorType;
use KnownUnown\Sheep\task\FetchInfoTask;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\Server;
use pocketmine\utils\TextFormat;

class InfoCommand extends Command{

    public function __construct(){
        parent::__construct("info", "Displays plugin information from the official PocketMine plugin repository", "/sheep info <plugin>");
    }

    public function execute(CommandSender $sender, $label, array $args){
        $plugin = implode(' ', $args);
        if(trim($plugin) === '' || $plugin === null){
            $sender->sendMessage(TextFormat::RED . 'Please input a valid plugin.');
            return;
        }
        $sender->sendMessage(sprintf("Fetching information for plugin %s, please wait...", $plugin));
        Server::getInstance()->getScheduler()->scheduleAsyncTask(new FetchInfoTask([$plugin], InitiatorType::COMMAND_INFO, Server::getInstance()->getPluginManager()->getPlugin("Sheep")->sourceList->get(0), $sender->getName()));
    }
}