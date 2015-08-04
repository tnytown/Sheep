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
use pocketmine\scheduler\AsyncTask;
use pocketmine\utils\Utils;
use pocketmine\Server;

class FetchInfoTask extends AsyncTask{

    private $pluginToFetch;
    private $source;

    private $initiator;
    private $commandSender;

    public function __construct(array $pluginToFetch, $initiator = InitiatorType::PLUGIN, $source, $commandSender = "CONSOLE"){
        $this->pluginToFetch = $pluginToFetch;
        $this->initiator = $initiator;
        $this->source = $source;
        $this->commandSender = $commandSender;
    }

    public function onRun(){
        $resultarr = [];
        foreach($this->pluginToFetch as $plugin) {
            $data = json_decode(Utils::getURL($this->source . "/search?q=$plugin"), true); //temp: who would even write another search api anyway
            if ($data === false) $resultarr[] = new Response();
            $hits = $data['hits']['total'];
            $info = $data['hits']['hits'];
            $actual = 0;
            foreach ($info as $key => $val) {
                if (strtolower($val['_source']['title']) === strtolower($plugin)) {
                    $actual = $key;
                    $hits = 1;
                }
            }
            if ($hits === 1) {
                $plugininfo = $info[$actual]['_source'];
                $result = new Response(ResponseType::SUCCESS_SINGLE_RESULT, new PluginInfo($plugininfo['title'], $plugininfo['username'], $plugininfo['tag_line'],
                    $plugininfo['category_title'], $plugininfo['rating_avg'], $plugininfo['download_count'], $plugininfo['resource_id'], $plugininfo['current_version_id']));
                $resultarr[] = $result;
            } elseif ($hits === 0) {
                $resultarr[] = new Response(ResponseType::FAILURE_NO_RESULTS);
            } elseif ($hits > 1) {
                $sdata = json_decode(Utils::getUrl($this->source . "/autocomplete?q=$plugin"), true);
                if ($sdata === false) $resultarr[] = new Response();
                $suggestions = "";
                foreach ($sdata['plugin-suggest'][0]['options'] as $option) {
                    $suggestions .= $option['text'] . ", ";
                }
                $suggestions = rtrim($suggestions, ', ');
                $resultarr[] = new Response(ResponseType::SUCCESS_MULTIPLE_RESULTS, $suggestions);
            }
        }
        $this->setResult($resultarr);
    }

    public function onCompletion(Server $server){
        $result = ['response' => $this->getResult(), 'commandSender' => $this->commandSender, 'initiator' => $this->initiator, 'plugin' => $this->pluginToFetch, 'task' => 0];
        $server->getPluginManager()->getPlugin("Sheep")->onTaskFinished($result);
    }
}