<?php
$httpHost = strtolower($_SERVER['SERVER_NAME']);
if( $httpHost==="kongjian.yuansuzhouqi.cn"){
	$ip = '**********';//（生产环境）
	$dbname = '**********';
	$dbUser = '**********';
	$dbPwd = '**********';

}elseif($httpHost ==="testkongjian.yuansuzhouqi.cn"){
	$ip = '**********';//（测试环境）
	$dbname = '**********';
	$dbUser = '**********';
	$dbPwd = '**********';
}else{
/*	$ip = '**********';//（线下环境）
	$dbname = '**********';
	$dbUser = '**********';
	$dbPwd = '';*/
    $ip = 'localhost';//（线下环境）
    $dbname = '**********';
    $dbUser = '**********';
    $dbPwd  = '**********';
}
return array(

//*************************************数据库设置*************************************
    'DB_TYPE'               =>  'mysqli',                 // 数据库类型
    'DB_HOST'               =>  $ip,     // 服务器地址
    'DB_NAME'               =>  $dbname,     // 数据库名
    'DB_USER'               =>  $dbUser,     // 用户名
    'DB_PWD'                =>  $dbPwd,      // 密码
    'DB_PORT'               =>  '3306',     // 端口
    'DB_PREFIX'             =>  '',   // 数据库表前缀
//***********************************缓存设置**********************************
	'DATA_CACHE_TIME'        => 1800,        // 数据缓存有效期s
	'DATA_CACHE_PREFIX'      => 'mem_',      // 缓存前缀
	'DATA_CACHE_TYPE'        => 'Memcached', // 数据缓存类型,
	'MEMCACHED_SERVER'       =>  $ip, // 服务器ip
	'ALIOSS_CONFIG'          => array(
				'KEY_ID'             => '', // 阿里云oss key_id
				'KEY_SECRET'         => '', // 阿里云oss key_secret
				'END_POINT'          => '', // 阿里云oss endpoint
				'BUCKET'             => ''  // bucken 名称
	),
);
