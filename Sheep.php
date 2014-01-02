<?php

/*
__PocketMine Plugin__
name=Sheep
author=KnownUnown
version=3.0
apiversion=11
class=Sheep
*/

/*
Full list of contributors:
KnownUnown (project creator)
sekjun9878
ZacHack (gh#3)
*/

class Sheep implements Plugin {

    private $api;
    private $server;
    private $config;
    //private $confirm;
    private $nOfPlugins;
    private $questionableFunctionsList;
    //private $w;
    //private $cfgPath = "/plugins/Sheep";

    public function __construct(ServerAPI $api, $server = false){
        $this->api = $api;
        $this->server = ServerAPI::request();
    }

    public function init(){
        /*
        $g = $this->api->plugin->get($this->api->plugin->getIdentifier("Sheep", "KnownUnown"));
        safe_var_dump($g);
        $class = $g[0];
        if(!$this->api->plugin->configPath($class)){
            console("[Sheep] Error: Unable to create config.");
            $this->api->console->run("stop");
        } else {
            $this->cfgPath = $this->api->plugin->configPath($this);
        }
        */
        //if(!file_exists(DATA_PATH . $this->cfgPath)){
        //    if(!mkdir(DATA_PATH . $this->cfgPath)){
        //        console("[Sheep] Error: Unable to create config path.");
        //        $this->api->console->run("stop");
        //    }
        //}
        $this->pconfig = new Config($this->api->plugin->configPath($this) . "sheep.json", CONFIG_JSON, array(

            ));
        $this->config = new Config($this->api->plugin->configPath($this) . "sheep.yml", CONFIG_YAML, array(
                "api-url" => "http://forums.pocketmine.net/api.php",
                "debug" => false,
                "player-install" => false,
                "auto-update" => true,
                "update-interval" => "432000",
                "spapi-url" => null,
                "spapi-enabled" => false,
                "bad-functions" => array(
                    "create_function",
                    "passthru",
                    "exec",
                    "pnctl_exec",
                    "proc_open",
                    "popen",
                    "system",
                    "shell_exec",
                    "register_shutdown_function",
                    "register_tick_function",
                    "dl",
                    "eval",
                    "expect_popen",
                    "apache_child_terminate",
                    "link",
                    "posix_kill",
                    "posix_mkfifo",
                    "posix_setpgid",
                    "posix_setsid",
                    "posix_setuid",
                    "proc_close",
                    "proc_get_status",
                    "proc_nice",
                    "proc_terminate",
                    "putenv",
                    "touch",
                    "alter_ini",
                    "highlight_file",
                    "show_source",
                    "ini_alter",
                    "fgetcsv",
                    "fputcsv",
                    "fpassthru",
                    "ini_get_all",
                    "openlog",
                    "syslog",
                    "rename",
                    "copy",
                    "parse_ini_file",
                    "ftp_connect",
                    "ftp_ssl_connect",
                    "fsockopen",
                    "pfsockopen",
                    "socket_bind",
                    "socket_connect",
                    "socket_listen",
                    "socket_create_listen",
                    "socket_accept",
                    "socket_getpeername",
                    "socket_send",
                    "apache_get_modules",
                    "apache_get_version",
                    "apache_getenc",
                    "apache_note",
                    "apache_setenv",
                    "apache_request_headers",
                    "diskfreespace",
                    "disk_free_space",
                    "get_current_user",
                    "getmypid",
                    "getmyuid",
                    "getrusage",
                    "set_time_limit",
                    "show_source",
                    "symlink",
                    "tmpfile",
                    "virtual",
                    "phpinfo",
                    "max_execution_time",
                    "set_include_path",
                    "escapeshellcmd",
                    "escapeshellarg",
                    "basename",
                    "chgrp",
                    "chmod",
                    "chown",
                    "clearstatcache",
                    "copy",
                    "delete",
                    "dirname",
                    "disk_free_space",
                    "disk_total_space",
                    "diskfreespace",
                    "fclose",
                    "feof",
                    "fflush",
                    "fgetc",
                    "fgetcsv",
                    "fgets",
                    "fgetss",
                    "file_exists",
                    "file_get_contents",
                    "file_put_contents",
                    "file",
                    "fileatime",
                    "filectime",
                    "filegroup",
                    "fileinode",
                    "filemtime",
                    "fileowner",
                    "fileperms",
                    "filesize",
                    "filetype",
                    "flock",
                    "fnmatch",
                    "fopen",
                    "fpassthru",
                    "fputcsv",
                    "fputs",
                    "fread",
                    "fscanf",
                    "fseek",
                    "fstat",
                    "ftell",
                    "ftruncate",
                    "fwrite",
                    "glob",
                    "is_dir",
                    "is_executable",
                    "is_file",
                    "is_link",
                    "is_readable",
                    "is_uploaded_file",
                    "is_writable",
                    "is_writeable",
                    "lchgrp",
                    "lchown",
                    "link",
                    "linkinfo",
                    "lstat",
                    "mkdir",
                    "move_uploaded_file",
                    "parse_ini_file",
                    "parse_ini_string",
                    "pathinfo",
                    "pclose",
                    "popen",
                    "readfile",
                    "readlink",
                    "realpath_cache_get",
                    "realpath_cache_size",
                    "realpath",
                    "rename",
                    "rewind",
                    "rmdir",
                    "set_file_buffer",
                    "stat",
                    "symlink",
                    "tempnam",
                    "tmpfile",
                    "touch",
                    "umask",
                    "unlink",
                ),
            ));
        if(!file_exists(dirname(__FILE__) . "/../restart.php")){
            console("[Sheep] Creating restart script.");
            file_put_contents(dirname(__FILE__) . "/../restart.php",
            "<?php

                class Restart{

                function __construct(){
                    sleep(10);
                    \$this->restart();
                }

                public function getOS(){
               \$uname = trim(strtoupper(php_uname('s')));
                    if(strpos(\$uname, 'DARWIN') !== false){
                            return 'mac';
                    }elseif(strpos(\$uname, 'WIN') !== false){
                            return 'win';
                    }elseif(strpos(\$uname, 'LINUX') !== false){
                            return 'linux';
                    }else{
                            return 'other';
                    }
                }

                public function restart(){
                    if(\$this->getOS() == 'linux'){
                        `./start.sh`
                    } elseif(\$this->getOS() == 'win'){
                        `start.cmd`
                    } elseif(\$this->getOS() == 'mac'){
                        `d./start.sh`
                    }
                }

                function __destruct(){

                }

                }
            "
            );
        }
        $this->api->console->register("sheep", "Sheep version 3.0", array($this, "cmdHandle"));
        //if($this->config->get("spapi-enabled")){
        //    if($this->config->get("spapi-url") == (null || "")){
        //        if(!Utils::curl_post($this->config->get("spapi-url"), array($ip = $_SERVER["SERVER_ADDR"]))){
        //            console('[Sheep] ERROR: Unable to connect to remote SPanel API.');
        //        } else {
        //            console('[Sheep] SPanel has been enabled!');
        //        }
        //    } else {
        //        console('[Sheep] SPanel is disabled.');
        //    }
        //}
        $this->questionableFunctionsList = $this->config->get("bad-functions");
        $this->nOfPlugins = json_decode(file_get_contents($this->config->get("api-url")), true);
        $this->nOfPlugins = $this->nOfPlugins["count"];
        /*
        $g = $this->api->plugin->get($this->api->plugin->getIdentifier("Sheep", "KnownUnown"));
        safe_var_dump($g);
        $class = $g[0];
        if(!$this->api->plugin->configPath($class)){
            console("[Sheep] Error: Unable to create config.");
            $this->api->console->run("stop");
        } else {
            $this->cfgPath = $this->api->plugin->configPath($this);
        }
        */
        //if(!file_exists(DATA_PATH . $this->cfgPath)){
        //    if(!mkdir(DATA_PATH . $this->cfgPath)){
        //        console("[Sheep] Error: Unable to create config path.");
        //        $this->api->console->run("stop");
        //    }
        //}
        if(!$this->config->get("auto-update")){
            console("[Sheep] Warning: autoupdate is not enabled!");
        } else {
            $this->api->schedule($this->config->get("update-interval"), array($this, "autoUpdate"), array(), true);
        }
        console("[Sheep] Loaded Sheep! Current count of plugins on PocketMine Forums: {$this->nOfPlugins}");
    }

    /*
     * For later use.
    public function updateConfig(){
        $this->dbgMsg("Now updating sheep.json.");
        file_put_contents($this->api->plugin->configPath($this) . "sheep.json", "");
        foreach()
    }
    */

    public function cmdHandle($cmd, $params, $issuer){
        if($issuer instanceof Player)
        {
            if(!$this->config->get("player-install")){
                return "[Sheep] You are not allowed to use this command. Consider asking your administrator to enable player-install in sheep.yml.";
            }
        }

        $output = "";
        switch($cmd){
            case "sheep":
                if($params[0] == ""){
                    $output = "Usage: /sheep <install|remove|uninstall|load> <plugin name> (for load: plugin name and filetype)";
                }
                switch($params[0]){
                    case "install":
                        $derp = $params;
                        array_shift($derp);
                        $pn = implode(" ", $derp);
                        console($pn);
                        console("[Sheep] Installing...");
                        if(!isset($pn) or $pn == ""){
                            return "[Sheep] No plugin specified to install.";
                        }
                        if(!$this->getUrl($pn)){
                            console("[Sheep] Error: Plugin not found.");
                        } else {
                            $array = $this->getUrl($pn);
                            $name = $array["title"];
                            $author = $array["author"];
                            $link = $array["link"];
                            $updatet = $array["times-updated"];
                            $this->dbgMsg($array["times-updated"]);
                            $this->dbgMsg($updatet);
                            console("[Sheep] Downloading plugin {$name} by {$author}...");
                            $plugin = file_get_contents($link);
                            $type = $this->getPluginType($plugin);
                            console("[Sheep] Checking for malware...\n");
                            foreach($this->questionableFunctionsList as $q)
                            {
                                if (strpos($plugin, $q) !== false) {
                                    console("[Sheep] Plugin contains file system function(s). This plugin can be planning to do something nasty! To remove, do /sheep remove {$name}.");
                                }
                            }
                            $this->putPlugin($plugin, $name);
                            $this->loadPlugin($name, $type);
                            $this->pconfig->set($name, array(
                                    "name" => $name,
                                    "updatet" => $updatet,
                                    "author" => $author,
                                )
                            );
                            $this->pconfig->save();
                            //if(!$this->api->plugin->load($name . "." . $this->getPluginType($plugin))){
                            //console("[Sheep] Starting plugin...");
                            //$this->api->plugin->loadAll();
                            //$this->api->plugin->initAll();
                            //} else {
                            $output = "[Sheep] Successfully downloaded and installed plugin " . $name . " .";
                            //}
                        }
                        break;
                    case "uninstall":
                    case "remove":
                        switch($params[1]){
                            case '':
                                $output = "[Sheep] Plugin name cannot be blank!";
                                break;
                            default:
                                unlink(DATA_PATH  . "/plugins/" . $params[1] . ".php");
                                unlink(DATA_PATH . "/plugins/" . $params[1] . ".pmf");
                                $this->dbgMsg(DATA_PATH  . "/plugins/" . $params[1] . ".php");
                                    //$this->api->plugin->loadAll();
                                    //$this->api->plugin->initAll();
                                $output = "[Sheep] Successfully removed plugin named " . $params[1] . ", restarting now.";
                                $this->restartServer();
                                $this->api->console->run('stop');
                        }
                        break;
                    case "load":
                        if(!$this->loadPlugin($params[1], $params[2])){
                            console("[Sheep] Error: Plugin is invalid or information is incorrect.");
                        } else {
                            console("[Sheep] Loaded plugin {$params[1]}.");
                        }
                        break;
                    case "updateall":
                        //TODO: add individual plugin updating
                        //if($params[1] == "null" || ""){
                        ///    console("[Sheep] Error: omaigod invalid plugin name halp");
                        //} else 
                        $this->autoUpdate();
                        break;

                }
        }
        return $output;
    }

    public function getUrl($name){
        $apil = $this->config->get("api-url");
        $json = json_decode(file_get_contents($apil), true);
        foreach($json["resources"] as $index => $res){
            $this->dbgMsg("Searching array key {$index}...");
            if($res["title"] == $name){
                if($res["state"] !== "visible"){
                    console("[Sheep] Plugin is awaiting review. Sheep will not download these types of plugins.");
                    return false;
                } else {
                    /*
                    $dlink = $this->config->get("dlurl");
                    $dlink[2] = $res["title"];
                    $dlink[4] = $res["id"];
                    $dlink[6] = $res["version_id"];
                    $dlink = implode($dlink);
                    */
                    $dlink = "http://forums.pocketmine.net/index.php?plugins/" . $res["title"] . "." . $res["id"] . "/download&version=" . $res["version_id"];
                    $this->dbgMsg($dlink);
                    return array(
                        "author" => $res["author_username"],
                        "title" => $res["title"],
                        "link" => $dlink,
                        "times-updated" => $res["times_updated"],
                    );
                }
            }
        }
    }

    public function putPlugin($code, $name){
        if(strtolower(substr($code, 0, 5)) === "<?php"){
            file_put_contents(DATA_PATH . "/plugins/" . $name . ".php", $code);
        } else {
            file_put_contents(DATA_PATH . "/plugins/" . $name . ".pmf", $code);
        }
    }

    public function getPluginType($code){
        if(strtolower(substr($code, 0, 5)) === "<?php"){
            return "php";
        } else {
            return "pmf";
        }
    }

    public function loadPlugin($name, $type){
        $this->api->plugin->load(DATA_PATH . "/plugins/" . $name . "." . $type);
        if($type == "pmf"){
            $pmf = new PMFPlugin(DATA_PATH . "/plugins/" . $name . "." . $type);
            $info = $pmf->getPluginInfo();
        } elseif($type == "php"){
            $content = file_get_contents(DATA_PATH . "/plugins/" . $name . "." . $type);
            $info = strstr($content, "*/", true);
            $content = str_repeat(PHP_EOL, substr_count($info, "\n")).substr(strstr($content, "*/"),2);
            if(preg_match_all('#([a-zA-Z0-9\-_]*)=([^\r\n]*)#u', $info, $matches) == 0){ //false or 0 matches
                console("[ERROR] Failed parsing of ".basename(DATA_PATH . "/plugins/" . $name . "." . $type));
                return false;
            }
            $info = array();
            foreach($matches[1] as $k => $i){
                $v = $matches[2][$k];
                switch(strtolower($v)){
                    case "on":
                    case "true":
                    case "yes":
                        $v = true;
                        break;
                    case "off":
                    case "false":
                    case "no":
                        $v = false;
                        break;
                }
                $info[$i] = $v;
            }
            $info["code"] = $content;
            $info["class"] = trim(strtolower($info["class"]));
        }
        //if(method_exists($this->api->plugin, "getIdentifier")){
        //safe_var_dump($info["class"]);
        //safe_var_dump($info["author"]);
        //safe_var_dump($info["apiversion"]);
        $id = $this->api->plugin->getIdentifier($info["name"], $info["author"]);
        $p = $this->api->plugin->get($id);
        $this->dbgMsg(safe_var_dump($p));
        $p[0]->init();
        /*} else {
            console("[Sheep] Warning: A lower API version than 11 was detected. Sheep currently half-supports API >11, but keep\n in mind support would be removed in the near future.");
            include_once DATA_PATH . "/plugins/" . $name . "." . $type;
            $nbj = new $name($this->api, false);
            if(!$nbj){
                return false;
            }
            $nbj->init();
            unset($nbj);
            $nbj = null;
        }
        */
    }
    public function autoUpdate(){
        console("[Sheep] Updating plugins...");
        $u = null;
        $json = json_decode(file_get_contents($this->api->plugin->configPath($this) . "sheep.json"), true);
        $this->dbgMsg($json);
        $this->dbgMsg($this->api->plugin->configPath($this) . "sheep.json");
        foreach($json as $a){
            $this->dbgMsg($a);
            $info = $this->getUrl($a["name"]);
            if($info["updatet"] !== $a["updatet"]){
                $this->pconfig->set($a["name"], array(
                    "name" => $info["title"],
                    "updatet" => $info["times-updated"],
                    "author" => $info["author"],
                    ));
                $this->pconfig->save();
                $this->dbgMsg("Found an update and updating plugin {$a['name']}...");
                $plugin = file_get_contents($info["link"]);
                unlink($a["name"] . "." . $this->getPluginType($plugin));
                $this->putPlugin($plugin, $info["name"]);
                //$this->loadPlugin($info["name"], $this->getPluginType($plugin));
                $u++;
            } else {
                console("[Sheep] No updates found for your plugins.");
                return;
            }
            console("[Sheep] Updated {$u} plugins. Stopping server in 5 minutes to apply changes.");
            $this->api->chat->broadcast("[Sheep] Stopping server in 5 minutes.");
            $this->api->schedule(20 * 60 * 5, array($this, "restartServer"), array(), true);
            // 20 ticks = 1 second, 20 ticks * 60 seconds = 1 minute, 1 minute * 5 minutes = 5 minutes, You made it 50 hours
        }
    }

    public function restartServer(){
        $this->api->chat->broadcast("[Sheep] Stopping server now to apply plugin changes. Rejoin if possible!");
        //include_once(dirname(__FILE__) . "/../restart.php");
        //$restart = new Restart();
        $this->api->console->run("stop");
    }

    public function dbgMsg($message){
        if($this->config->get("debug")){
            console("[Sheep] Debug: {$message}");
        }
    }

    public function __destruct(){
        $this->config->save();
        $this->pconfig->save();
        console('[Sheep] Sheep exiting.');
    }
}
?>
