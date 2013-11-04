<?php

/*
__PocketMine Plugin__
name=Sheep
author=sekjun9878
version=1.0
apiversion=9,10,11,12,13,14,15,16,17,18,19,20
class=Sheep
*/

class Sheep implements Plugin {
    
    private $api;
    private $server;
    private $config;

	private $questionableFunctionsList = array(
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
	);
    
    public function __construct(ServerAPI $api, $server = false){
        $this->api = $api;
        $this->server = ServerAPI::request();
        
        $this->config = new Config($this->api->plugin->configPath($this) . "config.yml", CONFIG_YAML, array(
        "api-url" => "http://mcpedevs.pocketmine.net/api.php?ID=",
        //"plugins-installed" => strtolower(implode(", ", $this->api->plugin->getList())),
        ));
        console("[Sheep] Loaded Sheep!");
    }
    
    public function init(){
        $this->api->console->register("sheep", "Sheep version 1.1", array($this, "cmdHandle"));
    }
    
    public function cmdHandle($cmd, $params, $issuer){
	    if($issuer instanceof Player)
	    {
		    return "[Sheep] You are not allowed to use this command.";
	    }

        $output = "";
        switch($cmd){
            case "sheep":
                switch($params[0]){
                    case "install":
                        if(!isset($params[1]) or $params[1] == ""){
                            return "[Sheep] No plugin specified to install.";
                        }
                        $url = $this->config["api-url"];
                        $fetch = json_decode(file_get_contents($url.$params[1]));
                        if(isset($fetch["error"])){
                            $output = "[Sheep] An unexpected error occured. Check that the plugin ID is correct.";
                        } elseif(isset($fetch["link"])){
	                        $plugin = file_get_contents($fetch["link"]);
	                        foreach($this->questionableFunctionsList as $q)
	                        {
		                        if (strpos($plugin, $q) !== false) {
			                        return "[Sheep] Plugin contains file system functions. Please contact an Administrator to have this installed.";
		                        }
	                        }
                            file_put_contents($this->config["plugin-dir"].$fetch["title"].$fetch['filetype'], $plugin);
                            $this->api->plugin->load($fetch["title"].$fetch['filetype']);
                            $output = "[Sheep] Successfully downloaded and installed plugin " . $fetch["title"] . " .";
                        }
		                break;
	                case "uninstall":
	                case "remove":
		                unlink("string here");
		                break;
                }
        }
        return $output;
    }
    
    public function __destruct(){
        
    }
}

?>