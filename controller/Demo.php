<?php
namespace app\casbin\controller;

use Casbin;
use think\App;
use think\Controller;
use think\response\Json;

/**
 * Class Demo
 * @package app\casbin\controller
 * @author tomlu 261738244@qq.com
 */
class Demo extends Controller
{
    public function demo(){
        exit($this->fetch('demo'));
    }
}
