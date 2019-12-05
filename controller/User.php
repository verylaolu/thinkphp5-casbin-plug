<?php


namespace app\casbin\controller;

use CasbinAdapter\Think\Facades\Casbin;
use think\Controller;

/**
 * Class User
 * @package app\casbin\controller
 * @author tomlu 261738244@qq.com
 */
class User extends Controller
{
    private $UserModel;
    function __construct()
    {
        $this->UserModel= new \app\casbin\model\User();
    }
    /**
     * 获取用户列表
     * @SWG\Get(
     *     path="/v1/user?page",
     *     summary="获取用户列表",
     *     description="获取用户列表",
     *     operationId="Get-all",
     *     tags={"User"},
     *     produces={"application/json"},
     *     @SWG\Parameter(
     *         description="页码",
     *         in="formData",
     *         name="page",
     *         required=false,
     *         type="number"
     *     ),
     *     @SWG\Response(
     *         response=200,
     *         description="success",
     *     ),
     *     security={{
     *     "Device":{},
     *     "Authorization":{}
     *   }}
     * )
     */
    public function all($page = 1)
    {
        $info = $this->UserModel->getPage([],$page,'20');
        return json(['msg'=>'success','data'=>$info],200);
    }
    /**
     * 获取角色详细
     * @SWG\Get(
     *     path="/v1/user/:id",
     *     summary="获取角色详细",
     *     description="获取角色详细",
     *     operationId="Get-once",
     *     tags={"User"},
     *     produces={"application/json"},
     *     @SWG\Parameter(
     *         description="信息ID   ID='all'则显示全部",
     *         in="formData",
     *         name="id",
     *         required=false,
     *         type="number"
     *     ),
     *     @SWG\Response(
     *         response=200,
     *         description="success",
     *     ),
     *     security={{
     *     "Device":{},
     *     "Authorization":{}
     *   }}
     * )
     */
    public function once($id){
        if($id=='all'){
            $info = $this->UserModel->getAll();
            return json(['msg'=>'success','data'=>$info],200);
        }else{
            $info = $this->UserModel->getOnce(['id'=>intval($id)]);
            return json(['msg'=>'success','data'=>$info],200);
        }
    }
    static public function checkuser()
    {
        //-----------------------------------------------------------------
        $model = new \app\casbin\model\User();
        $userinfo = $model->getOnce(['id'=>intval(1)]);; //修改自定义获取用户方法 获取登录用户信息
        //-----------------------------------------------------------------
        //判断是否登录用户
        if(!$userinfo){
            header("HTTP/1.1 401 Unauthorized");
            header('Content-Type:application/json; charset=utf-8');
            exit(json_encode(['msg'=>'登录授权错误，重新登录','data'=>'']));
        }
        //-----------------------------------------------------------------
        $user   = $userinfo['username'];  //取得权限绑定信息 例：username
        //-----------------------------------------------------------------

        $url    = explode('?',$_SERVER['REQUEST_URI'])[0];  //获取路由地址
        $action = $_SERVER['REQUEST_METHOD'];   //获取请求方法
        //验证用户路由权限
        if (true === Casbin::enforce($user, $url, $action)) {
            return $userinfo; //权限验证通过
        } else {
            header("HTTP/1.1 203 Non-Authoritative Information");
            header('Content-Type:application/json; charset=utf-8');
            exit(json_encode(['msg'=>'资源未授权，请获得权限','data'=>'']));
        }
    }
}
