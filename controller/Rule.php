<?php


namespace app\casbin\controller;


use CasbinAdapter\Think\Facades\Casbin;
use think\Controller;
use think\Request;

/**
 * Class Rule
 * @package app\casbin\controller
 * @author tomlu 261738244@qq.com
 */
class Rule extends Controller
{
    private $RuleModel;
    function __construct()
    {
        $this->RuleModel= new \app\casbin\model\Rule();
    }
    /**
     * 获取权限列表
     * @SWG\Get(
     *     path="/v1/rule?page",
     *     summary="获取权限列表",
     *     description="获取权限列表",
     *     operationId="Get-all",
     *     tags={"Rule"},
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
        $info = $this->RuleModel->getPage([],(int)$page,'20');
        return json(['msg'=>'success','data'=>$info],200);
    }
    /**
     * 获取权限详细
     * @SWG\Get(
     *     path="/v1/rule/:id",
     *     summary="获取权限详细",
     *     description="获取权限详细",
     *     operationId="Get-once",
     *     tags={"Rule"},
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
    public function once($id=''){
        if($id=='all'){
            $info = $this->RuleModel->getAll();
            return json(['msg'=>'success','data'=>$info],200);
        }else{
            $info = $this->RuleModel->getOnce(['id'=>intval($id)]);
            return json(['msg'=>'success','data'=>$info],200);
        }
    }

    /**
     * 创建权限
     * @SWG\Post(
     *     path="/v1/rule",
     *     summary="创建权限",
     *     description="创建权限",
     *     operationId="Post",
     *     tags={"Rule"},
     *     produces={"application/json"},
     *     @SWG\Parameter(
     *         description="权限类型：p/g",
     *         in="formData",
     *         name="type",
     *         required=true,
     *         type="string"
     *     ),
     *     @SWG\Parameter(
     *         description="权限主键：用户/角色",
     *         in="formData",
     *         name="name",
     *         required=true,
     *         type="string"
     *     ),
     *     @SWG\Parameter(
     *         description="权限内容：g 继承角色/p 路由地址",
     *         in="formData",
     *         name="obj",
     *         required=true,
     *         type="string"
     *     ),
     *     @SWG\Parameter(
     *         description="权限方法：GET/POST/PUT/DELETE  注：type参数为g时不填",
     *         in="formData",
     *         name="method",
     *         required=false,
     *         type="string"
     *     ),
     *     @SWG\Parameter(
     *         description="权限域  注：type参数为g时不填",
     *         in="formData",
     *         name="domain",
     *         required=false,
     *         type="string"
     *     ),
     *     @SWG\Response(
     *         response=201,
     *         description="['msg'=>'创建成功','data'=>'']",
     *     ),
     *     @SWG\Response(
     *         response=400,
     *         description="['msg'=>'参数错误','data'=>'']",
     *     ),
     *     @SWG\Response(
     *         response=500,
     *         description="['msg'=>'接口服务内部错误','data'=>'']",
     *     ),
     *     security={{
     *     "Device":{},
     *     "Authorization":{}
     *   }}
     * )
     */
    public function save($type='',$name='',$obj='',$method='',$domain='')
    {
        if(empty($type)||empty($name)||empty($obj)){
            return json(['msg'=>'参数错误','data'=>''], 400); //参数错误
        }
        if($type=='p'){
            if(empty($method)){
                return json(['msg'=>'选择METHOD','data'=>''], 400); //参数错误
            }
            $state = Casbin::addPermissionForUser($name, $obj ,$method,$domain);
        }else{
            $state = Casbin::addRoleForUser($name, $obj);
        }
        if($state){
            return json(['msg'=>'创建成功','data'=>''], 201);//post返回201表示创建成功,返回ID
        }else{
            return json(['msg'=>'接口服务内部错误','data'=>''], 500);//post返回201表示创建成功,返回ID
        }
    }

    /**
     * 删除权限
     * @SWG\Delete(
     *     path="/v1/rule",
     *     summary="删除权限",
     *     description="删除权限",
     *     operationId="Delete",
     *     tags={"Rule"},
     *     produces={"application/json"},
     *     @SWG\Parameter(
     *         description="权限类型：P/G",
     *         in="formData",
     *         name="type",
     *         required=true,
     *         type="string"
     *     ),
     *     @SWG\Parameter(
     *         description="权限主键：用户/角色",
     *         in="formData",
     *         name="name",
     *         required=true,
     *         type="string"
     *     ),
     *     @SWG\Parameter(
     *         description="权限内容：G 继承角色/P 路由地址",
     *         in="formData",
     *         name="obj",
     *         required=true,
     *         type="string"
     *     ),
     *     @SWG\Parameter(
     *         description="权限方法：GET/POST/PUT/DELETE  注：type参数为G时不填",
     *         in="formData",
     *         name="method",
     *         required=false,
     *         type="string"
     *     ),
     *     @SWG\Parameter(
     *         description="权限域  注：type参数为G时不填",
     *         in="formData",
     *         name="domain",
     *         required=false,
     *         type="string"
     *     ),
     *     @SWG\Response(
     *         response=301,
     *         description="['msg'=>'删除成功，永久删除','data'=>'']",
     *     ),
     *     @SWG\Response(
     *         response=304,
     *         description="['msg'=>'被其他权限继承，所以不能删除','data'=>'']",
     *     ),
     *     @SWG\Response(
     *         response=400,
     *         description="['msg'=>'参数错误','data'=>'']",
     *     ),
     *     @SWG\Response(
     *         response=500,
     *         description="['msg'=>'接口服务内部错误','data'=>'']",
     *     ),
     *     security={{
     *     "Device":{},
     *     "Authorization":{}
     *   }}
     * )
     */
    public function delete($type='',$name='',$obj='',$method='',$domain='')
    {
        if(empty($type)||empty($name)||empty($obj)){
            return json(['msg'=>'参数错误','data'=>''], 400); //参数错误
        }
        //判断要删除的权限是否被其他用户继承， 如果被继承则不可以删除
        $user = Casbin::getUsersForRole($name);
        if(count($user)>0){
            return json(['msg'=>'被其他权限继承，所以不能删除','data'=>''], 304);//post返回201表示创建成功,返回ID
        }
        //没有被其他人继承，根据类型删除相应权限
        if($type=='p'){
            if(empty($method)){
                return json(['msg'=>'选择METHOD','data'=>''], 400); //参数错误
            }
            $state = Casbin::deletePermissionForUser($name, $obj,$method);
        }else{
            $state = Casbin::deleteRoleForUser($name, $obj); //G-组操作-删除用户的角色
        }
        //根据删除权限结果，返回信息
        if($state){
            return json(['msg'=>'删除成功，永久删除','data'=>''], 301);//post返回201表示创建成功,返回ID
        }else{
            return json(['msg'=>'接口服务内部错误','data'=>''], 500);//post返回201表示创建成功,返回ID
        }
    }
}