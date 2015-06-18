<?php
/**
 * Created by PhpStorm.
 * User: Andrew
 * Date: 5/3/2015
 * Time: 10:40 AM
 */

namespace KnownUnown\Sheep;


class DownloadURL {

    public $id;
    public $name;
    public $version;

    public function __construct($id, $name, $version){
        $this->id = $id;
        $this->name = $name;
        $this->version = $version;
    }

    public function get(){
        return "forums.pocketmine.net/plugins/" . $this->name . "." . $this->id . "/download/&version=" . $this->version;
    }
}