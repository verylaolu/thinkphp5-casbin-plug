<?php

namespace app\casbin\controller;


use CasbinAdapter\Think\Facades\Casbin;
use think\Controller;
use think\Exception;
use think\Request;

/**
 * Class Role
 * 角色管理
 * @package app\casbin\controller
 * @author tomlu 261738244@qq.com
 */
class Role extends Controller
{
    private $RoleModel;
    public function __construct()
    {

        $this->RoleModel = new \app\casbin\model\Role();
    }

    /**
     * 获取角色列表
     * @SWG\Get(
     *     path="/v1/role?page",
     *     summary="获取角色列表",
     *     description="获取角色列表",
     *     operationId="Get-all",
     *     tags={"Role"},
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
    public function all($page=1)
    {
        $info = $this->RoleModel->getPage([],$page,'20');
        return json(['msg'=>'success','data'=>$info],200);
    }
    /**
     * 获取角色详细
     * @SWG\Get(
     *     path="/v1/role/:id",
     *     summary="获取角色详细",
     *     description="获取角色详细",
     *     operationId="Get-once",
     *     tags={"Role"},
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
            $info = $this->RoleModel->getAll();
            return json(['msg'=>'success','data'=>$info],200);
        }else{
            $info = $this->RoleModel->getOnce(['id'=>intval($id)]);
            return json(['msg'=>'success','data'=>$info],200);
        }

    }
    /**
     * 创建角色
     * @SWG\Post(
     *     path="/v1/role",
     *     summary="创建角色",
     *     description="创建角色",
     *     operationId="Post",
     *     tags={"Role"},
     *     produces={"application/json"},
     *     @SWG\Parameter(
     *         description="权限名称-汉字",
     *         in="formData",
     *         name="role_name",
     *         required=true,
     *         type="string"
     *     ),
     *     @SWG\Parameter(
     *         description="权限标签-英文",
     *         in="formData",
     *         name="role_tag",
     *         required=true,
     *         type="string"
     *     ),
     *     @SWG\Response(
     *         response=201,
     *         description="['msg'=>'创建成功','data'=>['id'=>$id]]",
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
    public function save($role_name ='',$role_tag ='')
    {
        //校验参数
        if (empty($role_tag) || empty($role_name)) {
            return json(['msg'=>'参数错误','data'=>''], 400); //参数错误
        }
        try {
            $data=[
                'role_name'=>$role_name,
                'role_tag'=>$role_tag,
                'create_time'=>time(),
            ];
            $id =$this->RoleModel->add($data);
        } catch (Exception $e) {
            return json(['msg'=>'接口服务内部错误','data'=>''], 500);
        }
        return json(['msg'=>'创建成功','data'=>['id'=>$id]], 201);//post返回201表示创建成功,返回ID
    }
    /**
     * 更新角色
     * @SWG\Put(
     *     path="/v1/role",
     *     summary="更新角色",
     *     description="更新角色",
     *     operationId="Put",
     *     tags={"Role"},
     *     produces={"application/json"},
     *     @SWG\Parameter(
     *         description="更新节点ID",
     *         in="formData",
     *         name="id",
     *         required=true,
     *         type="number"
     *     ),
     *     @SWG\Parameter(
     *         description="权限名称-汉字",
     *         in="formData",
     *         name="role_name",
     *         required=true,
     *         type="string"
     *     ),
     *     @SWG\Parameter(
     *         description="权限标签-英文",
     *         in="formData",
     *         name="role_tag",
     *         required=true,
     *         type="string"
     *     ),
     *     @SWG\Response(
     *         response=205,
     *         description="['msg'=>'重设置内容成功','data'=>'']",
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
    public function update($id='',$role_name='',$role_tag='')
    {
        //校验参数

        if (empty($id) || empty($role_name) || empty($role_tag)) {
            return json(['msg'=>'参数错误','data'=>''], 400); //参数错误
        }
        try {

            $old_data = $this->RoleModel->getOnce(['id'=>$id]);
            $data=[
                'role_name'=>$role_name,
                'role_tag'=>$role_tag,
                'update_time'=>time(),
            ];
            $state =$this->RoleModel->set($data,['id'=>$id]);
            $ruleModel = new \app\casbin\model\Rule();
            $ruleModel->set(['v0'=>$role_tag],['v0'=>$old_data['role_tag']]);
            $ruleModel->set(['v1'=>$role_tag],['v1'=>$old_data['role_tag']]);
        } catch (Exception $e) {
            return json(['msg'=>'接口服务内部错误','data'=>''], 500);
        }
        return json(['msg'=>'重设置内容成功','data'=>''], 205);//post返回201表示创建成功,返回ID
    }

    /**
     * 删除角色
     * @SWG\Delete(
     *     path="/v1/role",
     *     summary="删除角色",
     *     description="删除角色",
     *     operationId="Delete",
     *     tags={"Role"},
     *     produces={"application/json"},
     *     @SWG\Parameter(
     *         description="权限标签-英文",
     *         in="formData",
     *         name="role_tag",
     *         required=true,
     *         type="string"
     *     ),
     *     @SWG\Response(
     *         response=200,
     *         description="['msg'=>'删除成功，永久删除','data'=>'']",
     *     ),
     *     @SWG\Response(
     *         response=204,
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
    public function delete($role_tag)
    {
        if (empty($role_tag)) {
            return json(['msg'=>'参数错误','data'=>''], 400); //参数错误
        }
        try {
            $user = Casbin::getUsersForRole($role_tag);
            if(count($user)>0){
                return json(['msg'=>'被其他权限继承，所以不能删除','data'=>''],204);//存在绑定关系，需先接触关系在删除角色
            }else{
                $this->RoleModel->del(['role_tag'=>$role_tag]);
                Casbin::deleteRole($role_tag);
                return json(['msg'=>'删除成功，永久删除','data'=>''], 200);
            }
        } catch (Exception $e) {
            return json(['msg'=>'接口服务内部错误','data'=>''], 500);
        }
    }
}
