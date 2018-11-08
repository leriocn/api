<?php
/**
 * [$Config 全局配置]
 * @var array
 */
$Config = array(

	/*数据库设置*/
	'database' => array('db_host' => 'localhost',//mysql主机地址
						'db_user' => 'root',//用户名
						'db_psw' => 'root',//密码
						'db_name' => 'test',//数据库
						'db_provider' => 'mysql'),//提供者

	/*基本设置*/
	'setting' => array('encryptkey' => 'myprivatekey',
					   'check' => array('timeout' => 3000/*身份校验超时时间s*/)),

	/*缓存服务器设置*/
	'memcached' => array('host' => '127.0.0.1',
					 'port' => 11211,
					 'prefix' => 'myprefix',
					 'expired' => 5*60/*统一失效时间ms*/)

	);
?>