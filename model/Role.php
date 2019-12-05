<?php

namespace app\casbin\model;
/**
 * Class Role
 * @package app\casbin\model
 * @author tomlu 261738244@qq.com
 */
class Role extends BaseModel
{
    protected $pk = 'id';
    protected $table = 'casbin_role';
    protected $autoWriteTimestamp = true;
}