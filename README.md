### 项目简介
    基于casbin AND think-casbin 应用于THINKPHP 5.* 提供 RESTFUL API 权限管理
    并提供 RESTFUL API 可以自由封装可视化界面  
    开发的简易版本的权限管理插件
    使用方式简单，灵活

### 安装方法
    1、创建thinkphp项目（如果没有）：
        composer create-project topthink/think tp
    2、在ThinkPHP项目里，安装Think-Casbin扩展：
        composer require casbin/think-adapter
    3、发布资源:
        php think casbin:publish
            这将自动创建model配置文件config/casbin-basic-model.conf，和Casbin的配置文件config/casbin.php。
    4、数据迁移:   
            由于Think-Casbin默认将Casbin的策略（Policy）存储在数据库中，所以需要初始化数据库表信息。
            执行前，请确保数据库连接信息配置正确，如需单独修改Casbin的数据库连接信息或表名，可以修改config/casbin.php里的配置。
        php think casbin:migrate
            这将会自动创建Casbin的策略（Policy）表casbin_rule。
    5、代码迁移:
            将本代码 /casbin/ 文件夹完整复制到 /application/ 目录下，并且将下放路由信息复制进 /route/route.php  中
            完成此操作可以正常使用
    6、权限管理可视化操作模块
            浏览器访问  http://127.0.0.1/demo
        

### 使用方法
    //测试代码详见 \casbin\controller\index
    public function __construct()
        {
            //权限验证构造方法，设计权限验证代码请必须添加，方可自动执行用户验证规则
            //※※※※该静态方法必须修改自己的用户获取规则，验证入口 ※※※※//
            User::checkuser();
            //自动验证用户信息，如果出现授权或者权限错误，接口直接返回错误信息
            //////////////////////////////////////////////////////////////////////
            //       header("HTTP/1.1 401 Unauthorized");
            //       exit(json_encode(['msg'=>'登录授权错误，重新登录','data'=>'']));
            //////////////////////////////////////////////////////////////////////
            //       header("HTTP/1.1 203 Non-Authoritative Information");
            //       exit(json_encode(['msg'=>'资源未授权，请获得权限','data'=>'']));
            //////////////////////////////////////////////////////////////////////
            //       header("HTTP/1.1 200 Success");
            //       exit(json_encode(['msg'=>'获得权限','data'=>'']));
            //////////////////////////////////////////////////////////////////////
        }
        
### 用户信息验证代码(※※必须按照自己项目修改※※)   
        代码地址 ：app\casbin\controller\User
            
        static public function checkuser()
        {
            $userinfo = getuserinfo(); //※※※※※※修改自定义获取用户方法 获取登录用户信息
            //判断是否登录用户
            if(!$userinfo){
                header("HTTP/1.1 401 Unauthorized");
                header('Content-Type:application/json; charset=utf-8');
                exit(json_encode(['msg'=>'登录授权错误，重新登录','data'=>'']));
            }
            $user   = $userinfo['username'];  //※※※※※※取得权限绑定信息 例：username
            $url    = explode('?',$_SERVER['REQUEST_URI'])[0];  //获取路由地址
            $action = $_SERVER['REQUEST_METHOD'];   //获取请求方法
            //验证用户路由权限
            if (true === Casbin::enforce($user, $url, $action)) {
                header("HTTP/1.1 200 Success");
                header('Content-Type:application/json; charset=utf-8');
                exit(json_encode(['msg'=>'获得权限','data'=>'']));
            } else {
                header("HTTP/1.1 203 Non-Authoritative Information");
                header('Content-Type:application/json; charset=utf-8');
                exit(json_encode(['msg'=>'资源未授权，请获得权限','data'=>'']));
            }
        }
### 验证权限规则
    http://127.0.0.1/a/goods?id=111&asd=dsa
    路由验证：/a/goods

### 测试代码
    测试代码详见 \casbin\controller\index

### 模块路由
    Route::get('/demo', 'casbin/demo/demo'); //可视化操作
    Route::get('/', 'casbin/index/index');   //功能测试
    Route::get('/a/goods', 'casbin/index/goods');   //功能测试
    $version = 'v1';    //※※※※※※自定义访问 API 地址※※※※※※
    Route::group($version, function () {
        Route::get('role/:id', 'casbin/role/once'); //获取单个角色组
        Route::get('role', 'casbin/role/all'); //获取角色组列表
        Route::post('role', 'casbin/role/save'); //添加角色组
        Route::put('role', 'casbin/role/update'); //修改角色
        Route::delete('role', 'casbin/role/delete'); //删除角色
    
        Route::get('rule/:id', 'casbin/rule/once'); //获取单个权限信息
        Route::get('rule', 'casbin/rule/all'); //获取权限列表
        Route::post('rule', 'casbin/rule/save'); //添加权限
        Route::delete('rule', 'casbin/rule/delete'); //删除权限
    
        Route::get('user/:id', 'casbin/user/once'); //获取单个用户信息
        Route::get('user', 'casbin/user/all'); //获取用户列表
    });

### 数据库
    CREATE TABLE `casbin_role` (
      `id` int(11) NOT NULL AUTO_INCREMENT,
      `role_name` varchar(255) DEFAULT NULL COMMENT '角色名称',
      `role_tag` varchar(255) DEFAULT NULL COMMENT '角色标识',
      `create_time` int(11) DEFAULT NULL,
      `update_time` int(11) DEFAULT NULL,
      PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
    
    CREATE TABLE `casbin_user` (
      `id` int(11) NOT NULL AUTO_INCREMENT,
      `username` varchar(32) DEFAULT NULL,
      `password` varchar(11) DEFAULT NULL,
      `name` varchar(50) DEFAULT NULL,
      `mobile` varchar(32) DEFAULT NULL,
      `email` varchar(50) DEFAULT NULL,
      `create_time` int(11) DEFAULT NULL,
      `update_time` int(11) DEFAULT NULL,
      PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
    
    CREATE TABLE `migrations` (
      `version` bigint(20) NOT NULL,
      `migration_name` varchar(100) DEFAULT NULL,
      `start_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
      `end_time` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
      `breakpoint` tinyint(1) NOT NULL DEFAULT '0',
      PRIMARY KEY (`version`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
    
    CREATE TABLE `casbin_rule` (
      `id` int(11) NOT NULL AUTO_INCREMENT,
      `ptype` varchar(255) DEFAULT NULL,
      `v0` varchar(255) DEFAULT NULL,
      `v1` varchar(255) DEFAULT NULL,
      `v2` varchar(255) DEFAULT NULL,
      `v3` varchar(255) DEFAULT NULL,
      `v4` varchar(255) DEFAULT NULL,
      `v5` varchar(255) DEFAULT NULL,
      PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8;


### 测试数据
    INSERT INTO `casbin_user` (`id`, `username`, `password`, `name`, `mobile`, `email`, `create_time`, `update_time`)
    VALUES
        (1, 'tomlu', '321123', '猫儿', '13888888888', '261738244@qq.com', 1575451059, NULL),
        (2, 'test', 'test', '测试用户', '13333333333', 'test@test.com', 1575451059, NULL);
    
    INSERT INTO `casbin_rule` (`id`, `ptype`, `v0`, `v1`, `v2`, `v3`, `v4`, `v5`)
    VALUES
        (1, 'p', 'root', '/a/good', 'PUT', '', NULL, NULL),
        (2, 'p', 'root', '/a/good', 'GET', '', NULL, NULL),
        (3, 'g', 'tomlu', 'root', NULL, NULL, NULL, NULL),
        (4, 'p', 'tomlu', '/index', 'DELETE', '', NULL, NULL);
    
    INSERT INTO `casbin_role` (`id`, `role_name`, `role_tag`, `create_time`, `update_time`)
    VALUES
        (1, '超级管理员', 'root', 1575437813, NULL),
        (2, '普通管理员', 'admin', 1575437833, NULL);

### 返回格式
错误返回格式：  

    [‘msg’=>’参数错误’,’data’=>’’]

数据返回格式(直接返回数据内容)：
  
    {
        "msg": "success",
        "data": {
            "id": 2,
            "role_name": "超级管理员",
            "role_tag": "root",
            "create_time": "1970-01-01 08:00:00",
            "update_time": "1970-01-01 08:00:00"
        }
    }




### httpcode
    - 200 GET 操作成功
    - 201 POST 创建成功
    - 205 PUT 修改成功
    - 200 DELETE 删除成功
    - 204 DELETE 删除失败
    - 400 参数校验错误
    - 401 未授权
    - 500 服务器处理错误


### RBAC方法使用备注
    - $info = Casbin::addRoleForUser($user, $role); //G-组操作-添加用户与角色
    - $info = Casbin::getRolesForUser($user);  //G-组操作-获取用户绑定的角色
    - $info = Casbin::getUsersForRole($role);  //G-组操作-获取绑定角色的用户
    - $info = Casbin::hasRoleForUser($user, $role); //G-组操作-判断用户是否存在角色
    - $info = Casbin::deleteRoleForUser($user, $role); //G-组操作-删除用户的角色
    - $info = Casbin::deleteRolesForUser($user); //G-组操作-删除用户的所有角色
    - $info = Casbin::deleteUser($user);  //G-组操作-删除用户的所有角色
    - $info = Casbin::deleteRole($role);  //G-组操作-删除角色的所有用户
    - $info = Casbin::deletePermission($permission); //P-人操作-删除权限的所有用户
    - $info = Casbin::addPermissionForUser($user, $permission ,'get');  //P-人操作-添加用户与角色权限
    - $info = Casbin::deletePermissionForUser($user, $permission);//P-人操作-删除用户权限关系，向后字段兼容
    - $info = Casbin::deletePermissionsForUser($user);    //P-人操作-删除用户所有权限
    - $info = Casbin::getPermissionsForUser($user);//P-人操作-获取用户的所有权限
    - $info = Casbin::hasPermissionForUser($user, $permission); //P-人操作-判断用户是否有权限
    - $info = Casbin::getImplicitRolesForUser($name, $domain = '');//G-组操作-递归获取所有的组
    - $info = Casbin::getImplicitPermissionsForUser($user, $domain = ''); //P+G-人和组操作-根据人获取所有继承的组的权限和个人独立权限
    - $info = Casbin::getImplicitUsersForPermission($permission);//P-人操作-根据权限获取人
