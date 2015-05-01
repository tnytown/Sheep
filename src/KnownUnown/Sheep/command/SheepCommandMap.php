<?php
/**
 * Created by PhpStorm.
 * User: Andrew
 * Date: 5/1/2015
 * Time: 5:16 PM
 */

namespace KnownUnown\Sheep\command;


use pocketmine\command\CommandSender;
use pocketmine\command\SimpleCommandMap;
use pocketmine\event\TranslationContainer;
use pocketmine\Server;
use pocketmine\utils\MainLogger;
use pocketmine\utils\TextFormat;

class SheepCommandMap extends SimpleCommandMap{

    private $server;

    public function __construct(Server $server){
        $this->server = $server;
    }

    public function dispatch(CommandSender $sender, $commandLine){
        $args = explode(" ", $commandLine);
        if(count($args) === 0){
            return false;
        }
        array_shift($args);
        $sentCommandLabel = strtolower(array_shift($args));
        $target = $this->getCommand($sentCommandLabel);
        if($target === null){
            return false;
        }
        $target->timings->startTiming();
        try{
            $target->execute($sender, $sentCommandLabel, $args);
        }catch(\Exception $e){
            $sender->sendMessage(new TranslationContainer(TextFormat::RED . "%commands.generic.exception"));
            $this->server->getLogger()->critical("Unhandled exception executing command '" . $commandLine . "' in " . $target . ": " . $e->getMessage());
            $logger = $sender->getServer()->getLogger();
            if($logger instanceof MainLogger){
                $logger->logException($e);
            }
        }
        $target->timings->stopTiming();
        return true;
    }
}