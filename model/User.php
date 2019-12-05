<?php
namespace app\casbin\model;

/**
 * Class User
 * @package app\casbin\model
 * @author tomlu 261738244@qq.com
 */
class User extends BaseModel
{
    protected $pk = 'id';
    protected $table = 'casbin_user';
    protected $autoWriteTimestamp = true;
}