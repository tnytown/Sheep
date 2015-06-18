<?php
/**
 * Created by PhpStorm.
 * User: Andrew
 * Date: 6/18/2015
 * Time: 4:33 PM
 */

namespace KnownUnown\Sheep;


class Response {

    public $type;
    public $data;

    public function __construct($type = ResponseType::FAILURE_GENERAL, $data = null){
        $this->type = $type;
        $this->data = $data;
    }

    /**
     * @return int
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @return mixed
     */
    public function getData()
    {
        return $this->data;
    }


}