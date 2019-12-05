<?php
namespace app\casbin\controller;

use Casbin;
use think\App;
use think\Controller;
use think\response\Json;

/**
 * Class Index
 * @package app\casbin\controller
 * @author tomlu 261738244@qq.com
 */
class Index extends Controller
{
    public function __construct()
    {
        //权限验证构造方法，设计权限验证代码请必须添加，方可自动执行用户验证规则
        User::checkuser();
    }
    public function index()
    {
        return json(Casbin::GetPolicy(),200);
    }
    public function goods()
    {
        $aaa = new \app\casbin\model\Role();
        $bb = $aaa->getAll();
        return json($bb,200);
    }
    public function demo(){
        exit($this->fetch('demo'));
    }
}
