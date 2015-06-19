<?php
/**
 * Created by PhpStorm.
 * User: Andrew
 * Date: 6/19/2015
 * Time: 4:23 PM
 */

namespace KnownUnown\Sheep\command;


use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\Server;

class AboutCommand extends Command{

    public function __construct(){
        parent::__construct('about', 'About Sheep.', '/sheep about');
    }

    public function execute(CommandSender $sender, $label, array $args){
        $plugin = Server::getInstance()->getPluginManager()->getPlugin('Sheep');
        $sender->sendMessage("Sheep v%s by %s", $plugin->getDescription()->getVersion(), $plugin->getDescription()->getAuthors());
    }
}