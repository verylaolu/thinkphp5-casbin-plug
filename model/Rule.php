<?php

namespace app\casbin\model;

/**
 * Class Rule
 * @package app\casbin\model
 * @author tomlu 261738244@qq.com
 */
class Rule extends BaseModel
{
    protected $pk = 'id';
    protected $table = 'casbin_rule';
    protected $autoWriteTimestamp = true;
}