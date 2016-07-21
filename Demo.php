<?php
/**
 * Created by PhpStorm.
 * User: CPR096
 * Date: 2016/7/21
 * Time: 11:33
 */

require_once('./RedisClass.php');
echo 'this is the first php file!';

$redis = new \learngit\RedisClass();
$key = 'yiranleer_test';
$value = 'the first redis test!';
$redis->setStr($key,$value);

