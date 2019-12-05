<?php

namespace app\casbin\model;

use think\Model;
use think\Exception;
use think\custom\common\RedisLog;

/**
 * Class BaseModel
 * @package app\casbin\model
 * @author tomlu 261738244@qq.com
 */
class BaseModel extends Model
{
    protected $pk;
    protected $table;
    protected $autoWriteTimestamp = true;

    function getPage($conditions = [], $page = 1, $limit = 20,$field=['*']) {
        try{
            $info['list']  = $this->where($conditions)->field($field)->page($page)->limit($limit)->order($this->pk, 'desc')->select();
            RedisLog::info(backtrace(),$this->getLastSql());
        }catch (\Exception $e){
            throw new \Exception($e->getMessage().' SQL:'.$this->getLastSql(),300);
        }
        try{
            $info['total'] = $this->where($conditions)->count('*');
            RedisLog::info(backtrace(),$this->getLastSql());
        }catch (\Exception $e){
            throw new \Exception($e->getMessage(). ' SQL:' . $this->getLastSql(), 300);
        }
        return $info;
    }

    function getOnce($conditions = [],$field=['*'],$group='') {
        try{
            $info = $this->where($conditions)->field($field)->group($group)->find();
            RedisLog::info(backtrace(),$this->getLastSql());
        }catch (\Exception $e){
            throw new \Exception($e->getMessage().' SQL:'.$this->getLastSql(),300);
        }
        return $info;
    }

    function getAll($conditions = [],$field=['*'],$group='',$order='') {
        try{
            $info =  $this->where($conditions)->field($field)->group($group)->order($order)->select();
            RedisLog::info(backtrace(),$this->getLastSql());
        }catch (\Exception $e){
            throw new \Exception($e->getMessage().' SQL:'.$this->getLastSql(),300);
        }
        return $info;
    }

    function add($data = []) {
        try{
            $info =  $this->insertGetId($data);
            RedisLog::info(backtrace(),$this->getLastSql());
        }catch (\Exception $e){
            throw new \Exception($e->getMessage().' SQL:'.$this->getLastSql(),300);
        }
        return $info;
    }

    function set($data = [], $conditions = []) {
        try{
            $info =  $this->save($data, $conditions);
            RedisLog::info(backtrace(),$this->getLastSql());
        }catch (\Exception $e){
            throw new \Exception($e->getMessage().' SQL:'.$this->getLastSql(),300);
        }
        return $info;
    }

    function del($conditions = []) {
        try{
            $info =  $this->where($conditions)->delete();
            RedisLog::info(backtrace(),$this->getLastSql());
        }catch (\Exception $e){
            throw new \Exception($e->getMessage().' SQL:'.$this->getLastSql(),300);
        }
        return $info;
    }


}