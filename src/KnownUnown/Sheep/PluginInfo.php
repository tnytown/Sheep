<?php
/**
 * Created by PhpStorm.
 * User: Andrew
 * Date: 6/18/2015
 * Time: 4:03 PM
 */

namespace KnownUnown\Sheep;


class PluginInfo {

    private $name;
    private $author;

    private $desc;
    private $cat;
    private $rating;
    private $downloads;

    private $id;
    private $version;

    public function __construct($name, $author, $desc, $cat, $rating, $downloads, $id, $version){ //haha
        $this->name = $name;
        $this->author = $author;

        $this->desc = $desc;
        $this->cat = $cat;
        $this->rating = $rating;
        $this->downloads = $downloads;

        $this->id = $id;
        $this->version = $version;
    }

    /**
     * @return String
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return String
     */
    public function getAuthor()
    {
        return $this->author;
    }

    /**
     * @return String
     */
    public function getDesc()
    {
        return $this->desc;
    }

    /**
     * @return String
     */
    public function getCat()
    {
        return $this->cat;
    }

    /**
     * @return float
     */
    public function getRating()
    {
        return $this->rating;
    }

    /**
     * @return int
     */
    public function getDownloads()
    {
        return $this->downloads;
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return float
     */
    public function getVersion()
    {
        return $this->version;
    }


}