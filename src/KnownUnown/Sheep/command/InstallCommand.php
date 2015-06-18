<?php
/**
 * Created by PhpStorm.
 * User: Andrew
 * Date: 5/2/2015
 * Time: 10:50 AM
 */

namespace KnownUnown\Sheep\command;


use pocketmine\command\Command;
use pocketmine\command\CommandSender;

class InstallCommand extends Command {

    public function __construct(){
        parent::__construct("install", "Installs a plugin from sources", "/sheep install <plugin>");
    }

    public function execute(CommandSender $sender, $label, array $args){

    }
}