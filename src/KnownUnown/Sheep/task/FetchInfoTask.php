<?php
/**
 * Created by PhpStorm.
 * User: Andrew
 * Date: 5/1/2015
 * Time: 6:13 PM
 */

namespace KnownUnown\Sheep\task;


use KnownUnown\Sheep\InitiatorType;
use KnownUnown\Sheep\PluginInfo;
use KnownUnown\Sheep\Response;
use KnownUnown\Sheep\ResponseType;
use KnownUnown\Sheep\Sheep;
use pocketmine\scheduler\AsyncTask;
use pocketmine\utils\PluginException;
use pocketmine\utils\Utils;
use pocketmine\Server;

class FetchInfoTask extends AsyncTask{

    private $pluginToFetch;
    private $source;

    private $initiator;
    private $commandSender;

    public function __construct($pluginToFetch, $initiator = InitiatorType::PLUGIN, $source, $commandSender = "CONSOLE"){
        if(!is_string($pluginToFetch)){
            throw new PluginException("Plugin to fetch provided to FetchInfoTask must be of type String");
        } else {
            $this->pluginToFetch = $pluginToFetch;
        }

        $this->initiator = $initiator;
        $this->source = $source;
        $this->commandSender = $commandSender;
    }

    public function onRun(){
        $data = json_decode(Utils::getURL($this->source . "/search?q=$this->pluginToFetch"), true); //temp: who would even write another search api anyway
        if($data === false) $this->setResult(new Response());
        $hits = $data['hits']['total'];
        $info = $data['hits']['hits'];
        if($hits === 1){
            $plugininfo = $info[0]['_source'];
            $result = new Response(ResponseType::SUCCESS_SINGLE_RESULT, new PluginInfo($plugininfo['title'], $plugininfo['username'], $plugininfo['description'],
                $plugininfo['category_title'], $plugininfo['rating_avg'], $plugininfo['download_count'], $plugininfo['resource_id'], $plugininfo['current_version_id']));
            safe_var_dump($result);
            $this->setResult($result);
        } elseif($hits === 0){
            $this->setResult(new Response(ResponseType::FAILURE_NO_RESULTS));
        } elseif($hits > 1){
            $sdata = json_decode(Utils::getUrl($this->source . "/autocomplete?q=$this->pluginToFetch"), true);
            var_dump($sdata);
            if($sdata === false) $this->setResult(new Response());
            $suggestions = "";
            foreach($sdata['plugin-suggest'] as $option){
                $suggestions .= $option['text'] . ", ";
            }
            $suggestions = rtrim($suggestions, ', ');
            $this->setResult(new Response(ResponseType::SUCCESS_MULTIPLE_RESULTS, $suggestions));
        }
    }

    public function onCompletion(Server $server){
        $result = ['response' => $this->getResult(), 'commandSender' => $this->commandSender, 'initiator' => $this->initiator, 'plugin' => $this->pluginToFetch, 'task' => 0];
        $server->getPluginManager()->getPlugin("Sheep")->onTaskFinished($result);
    }
}