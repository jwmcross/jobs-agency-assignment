<?php
namespace Core;

interface Routes
{

    public function getRoutes():array;
    public function getAuthentication():\Core\Authentication;
    public function checkPermission($permissions) : bool;


}