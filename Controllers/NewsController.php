<?php
require_once ROOT.'/Models/News.php';

class NewsController
{
    public function __construct()
    {

    }

    public function list()
    {
        echo News::getItemList();
    }

    public function post($id = null)
    {
        echo $id;
    }
}