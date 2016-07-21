<?php
/*********************************************************************************
 * InitPHP 2.0
 *-------------------------------------------------------------------------------
 * 版权所有: hxqc
 * 您可以自由使用该源码，但是在使用过程中，请保留作者信息。尊重他人劳动成果就是尊重自己
 *-------------------------------------------------------------------------------
 * $Author:logan
 * $Dtime:2014-02-28
 *
 * 增加了检查set checckarrayset();
***********************************************************************************/
namespace learngit\RedisCache;
class RedisCache {
	
	private $redis; //redis对象
	
	/**
	 * 初始化Redis
	 * $RedisConfig = array(
	 *  'server' => '127.0.0.1' 服务器
	 *  'port'   => '6379' 端口号
	 * )
	 * @param array $config
	 */
	public function __construct($config = array()) {
		if ($config['server'] == '')  $config['server'] = C('redis_host');
		if ($config['port'] == '')  $config['port'] = C('redis_port');
		$this->redis = new \Redis();
		$this->redis->connect($config['server'], $config['port']);
        if(!empty($config['pwd'])){
            $this->redis->auth($config['pwd']);
        }
		return $this->redis;
	}
	
	/**
	 * 设置值
	 * @param string $key KEY名称
	 * @param string|array $value 获取得到的数据
	 * @param int $timeOut 时间
	 */
	public function setstr($key, $value, $timeOut = 0) {
		$value = json_encode($value);
        $key = C('redis_qz').$key;
		$retRes = $this->redis->set($key, $value);
		if ($timeOut > 0) $this->redis->setTimeout($key, $timeOut);
		return $retRes;
	}

	/**
	 * 通过KEY获取数据
	 * @param string $key KEY名称
	 */
	public function getstr($key) {
        $key = C('redis_qz').$key;
		$result = $this->redis->get($key);
		return json_decode($result, TRUE);
	}
	
	/**
	 * 删除一条数据
	 * @param string $key KEY名称
	 */
	public function deletestr($key) {
        $key = C('redis_qz').$key;
		return $this->redis->delete($key);
	}
	
	/**
	 * 清空整个数据
	 */
	public function flushAll() {
		return $this->redis->flushAll();
	}

    /**
     * 清空当前数据库
     */
    public function flushdb() {
        return $this->redis->flushdb();
    }
	
	/**
	 * 数据入队列
	 * @param string $key KEY名称
	 * @param string|array $value 获取得到的数据
	 * @param bool $right 是否从右边开始入
	 */
	public function push($key, $value ,$right = true) {
        $key = C('redis_qz').$key;
		$value = json_encode($value);
		return $right ? $this->redis->rPush($key, $value) : $this->redis->lPush($key, $value);
	}
	
	/**
	 * 数据出队列
	 * @param string $key KEY名称
	 * @param bool $left 是否从左边开始出数据
	 */
	public function pop($key , $left = true) {
        $key = C('redis_qz').$key;
		$val = $left ? $this->redis->lPop($key) : $this->redis->rPop($key);
		return json_decode($val);
	}
	
	/**
	 * 数据自增
	 * @param string $key KEY名称
	 */
	public function increment($key) {
        $key = C('redis_qz').$key;
		return $this->redis->incr($key);
	}

	/**
	 * 数据自减
	 * @param string $key KEY名称
	 */
	public function decrement($key) {
        $key = C('redis_qz').$key;
		return $this->redis->decr($key);
	}
	
	/**
	 * key是否存在，存在返回ture
	 * @param string $key KEY名称
	 */
	public function exists($key) {
        $key = C('redis_qz').$key;
		return $this->redis->exists($key);
	}
	
	/**
	 * 返回redis对象
	 * redis有非常多的操作方法，我们只封装了一部分
	 * 拿着这个对象就可以直接调用redis自身方法
	 */
	public function redis() {
		return $this->redis;
	}
    /**
     * 修改string数据库某个KEY的值
     * @param string $key KEY名称
     * @param string $value 值
     */
    public function editval($key,$value)
    {
        $key = C('redis_qz').$key;
        $value = json_encode($value);
        $retRes = $this->redis->getset($key, $value);
        return $retRes;
    }
    /**
     * 修改lisg类型数据库某个list中KEY的值
     * @param string $key KEY名称
     * @param string $index index名称
     * @param string $value 值
     */
    public function  editlistval($key,$index,$value)
    {
        $key = C('redis_qz').$key;
        $value = json_encode($value);
        $retRes = $this->redis->lset($key,$index,$value);
        return $retRes;
    }
    /**
     * 添加lisg类型数据库某个list中KEY的值
     * @param string $key KEY名称
     * @param string $index index名称
     * @param string $value 值
     */
    public function  addlistval($key,$value)
    {
        $key = C('redis_qz').$key;
        $value = json_encode($value);
        $retRes = $this->redis->lpush($key,$value);
        return $retRes;
    }
    /**
     * 修改lisg类型数据库某个list中KEY的值
     * @param string $key KEY名称
     * @param string $index index名称
     * @param string $value 值
     */
    public function  dellistval($key,$value)
    {
        $key = C('redis_qz').$key;
        $value = json_encode($value);
        $retRes = $this->redis->lrem($key,0,$value);
        return $retRes;
    }

    /**
     * 获取数据库某个list中所有的值
     * @param string $key KEY名称
     */
    public function getlistval($key)
    {
        $key = C('redis_qz').$key;
        //获取元素长度
      //  $len_list =  $this->redis->llen($key);
        $retRes = $this->redis->lrange($key,0,-1);
        return $retRes;
    }

    /**
     * 针对set数据
     * 添加数据方法
     * @param string $key KEY名称
      * @param string $value 值
     */
    public function  addarrayset($key,$value)
    {
        $key = C('redis_qz').$key;
        $value = json_encode($value);
        $retRes = $this->redis->sadd($key,$value);
        return $retRes;
    }
    /**
     * 针对set数据
     * 删除数据方法
     * @param string $key KEY名称
     * @param string $value 值
     */
    public function  delarrayset($key,$value)
    {
        $key = C('redis_qz').$key;
        $value = json_encode($value);
        $retRes = $this->redis->srem($key,$value);
        return $retRes;
    }

    /**
     * 针对set数据
     * 检查数据方法
     * @param string $key KEY名称
     * @param string $value 值
     */
    public function  checkarrayset($key,$value)
    {
        $key = C('redis_qz').$key;
        $value = json_encode($value);
        $retRes = $this->redis->sismember($key,$value);
        return $retRes;
    }

    /**
     * 针对set数据
     * 获取数据方法
     * @param string $key KEY名称
     */
    public function getarrayset($key){
        $key = C('redis_qz').$key;
        $retRes = $this->redis->smembers($key);
        return $retRes;
    }

    /**
     * 返回所有KEY
     * @param string $key KEY名称
     */
    public function getHashkeys($key) {
        $key = C('redis_qz').$key;
        $result = $this->redis->hkeys($key);
        return json_decode($result, TRUE);
    }

    /**
     * 针对Hash数据操作
     * 添加Hash 数据
     * @param string $key KEY名称
     * @param string $field 唯一主键名称
     * @param string $value json值
     */
    public function addHashval($key, $field, $value)
    {
        $key = C('redis_qz').$key;
        $value = json_encode($value);
        $retRes = $this->redis->hset($key,$field,$value);
        return $retRes;
    }

    /**
     * 针对Hash数据操作
     * 删除Hash 数据
     * @param string $key KEY名称
     * @param string $field 唯一主键名称
     * @param string $value json值
     */
    public function delHashval($key, $field)
    {
        $key = C('redis_qz').$key;
        $retRes = $this->redis->hdel($key,$field);
        return $retRes;
    }
    /**
     * 针对Hash数据操作
     * 修改Hash 数据
     * @param string $key KEY名称
     * @param string $field 唯一主键名称
     * @param string $value json值
     */
    public function upHashval($key, $field,$value)
    {
        $key = C('redis_qz').$key;
        $value = json_encode($value);
        $retRes = $this->redis->hmset($key,$field,$value);
        return $retRes;
    }

    /**判断哈希key 是否存在**/
    public function existsHaSh($key,$field){
        $key = C('redis_qz').$key;
        return $this->redis->hExists($key,$field);
    }
    /**
     * 针对Hash数据操作
     * 获取Hash 所有数据
     * @param string $key KEY名称
     */
    public function getallHashval($key)
    {
        $key = C('redis_qz').$key;
        $retRes = $this->redis->hgetall($key);
        return $retRes;
    }
    /**
 * 针对Hash数据操作
 * 获取Hash 某个数据
 * @param string $key KEY名称
 * @param string $field 唯一主键名称
 */
    public function getoneHashval($key,$field)
    {
        $key = C('redis_qz').$key;
        $retRes = $this->redis->hget($key,$field);
        return $retRes;
    }
    /**
     * 针对Hash数据操作
     * 获取Hash中所对应的键值个数
     * @param string $key KEY名称
     */
    public function getHashCount($key)
    {
        $key = C('redis_qz').$key;
        $retRes = $this->redis->hlen($key);
        return $retRes;
    }
    /**
     * 针对Hash数据操作
     * 获取Hash中是否存在键为field的域
     * @param string $key KEY名称
     */
    public function checkHashVal($key,$field)
    {
        $key = C('redis_qz').$key;
        $retRes = $this->redis->hexists($key,$field);
        return $retRes;
    }

    /**
     * 设定一个KEY的活动时间
     * @param string $key KEY
     * @param int $time 秒
     */
    public function expire($key,$time)
    {
        $key = C('redis_qz').$key;
        $retRes = $this->redis->expire($key,$time);
        return $retRes;
    }

    /**
     * 选择数据库
     * @param int $num 数据库编号
     */
    public function select($num){
        $retRes = $this->redis->select($num);
        return $retRes;
    }


    /** 异步保存数据*/
    public function redissave()
    {
        $this->redis->bgsave();
    }
    /**列出哈希类型 所有的key**/
    public function getHXKey($key){
        $key = C('redis_qz').$key;
        return $this->redis->hKeys($key);
    }

    public function hmsetall($key,$array){
        $key = C('redis_qz').$key;
        return $this->redis->hMset($key,$array);
    }

    public function zAdd($k,$score,$v){
        $k = C('redis_qz').$k;
        return $this->redis->zAdd($k,$score,$v);
    }

    public function zRange($k,$start,$stop){
        $k = C('redis_qz').$k;
        return $this->redis->zRange($k,$start,$stop);
    }

}
