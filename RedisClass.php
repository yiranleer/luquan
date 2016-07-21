<?php
/**
 * Created by PhpStorm.
 * User: CPR096
 * Date: 2016/7/21
 * Time: 14:06
 */

namespace learngit;


class RedisClass {

    private $redis;

    /**
     * initial redis
     * @param array $config
     */
    public function __construct($config=array()){

        if($config['host'] == '')    $config['host'] = '127.0.0.1';
        if($config['port'] == '')    $config['port'] = '6379';
        if($config['timeout'] == '') $config['timeout'] = '5000';
        $this->redis = new \Redis();
        $this->redis->connect($config['host'],$config['port'],$config['timeout'] );

        if(!empty($config['pwd'])){
            $this->redis->auth($config['pwd']);
        }

        return $this->redis;
    }

    /**
     * set str
     * @param $key
     * @param $value
     * @param int $timeout
     * @return bool
     */
    public function setStr($key,$value,$timeout = 0 ){
        if(is_array($value))  $result =  $this->redis->set($key,json_encode($value));
        if(is_string($value)) $result =  $this->redis->set($key,$value);
        if($timeout>0) $this->redis->setTimeout($key,$timeout);
        return $result;
    }

    public function getStr($key){

        $result = $this->redis->get($key);
        return json_decode($result,true);
    }
} 