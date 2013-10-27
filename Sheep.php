<?php

/*
__PocketMine Plugin__
name=Sheep
author=KnownUnown
version=1.0
apiversion=9,10,11
class=Sheep
*/

class Sheep implements Plugin {
    
    private $api;
    private $server;
    private $config;
    
    public function __construct(ServerAPI $api, $server = false){
        
        $this->api = $api;
        $this->server = ServerAPI::request();
        
        $this->config = new Config($this->api->plugin->configPath($this) . "config.yml", CONFIG_YAML, array(
        "update-interval" => 24,
        "update-auto" => true,
        "api-url" => "http://mcpedevs.pocketmine.net/api.php?ID=",
        "plugins-installed" => strtolower(implode(", ", $this->api->plugin->getList())),
        ));
        console("[Sheep] Loaded Sheep!");
        console("[Sheep] Plugins currently loaded:" . $this->config["plugins-installed"]);
    }
    
    public function init(){
        $this->api->console->register("sheep", "Sheep version 1.0", array($this, "cmdHandle"));
    }
    
    public function cmdHandle($cmd, $params, $issuer){
        switch($cmd){
            case "sheep":
                switch($params){
                    case "install":
                        if($params[1] == null || $params[1] == ""){
                            $output = "[Sheep] No plugin specified to install.";
                        } else {
                            $url = $this->config["api-url"];
                            $derp = implode("", array($url, $params[1]));
                            $fetch = json_decode(Utils::curl_get($derp));
                            if($fetch["error"]){
                                console("Unexpected error occured. Check that the plugin ID is correct.");
                            } elseif($fetch["link"]){
                                
                            }
                        }
                }
        }
        return $output;
    }
}

?>