<?php

class NewsController
{
    public function __construct()
    {

    }

    public function list()
    {
        echo "news";
    }

    public function post($id)
    {
        echo $id;
    }
}