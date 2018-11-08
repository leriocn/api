<?php

/**
* 缓存记录
*/
class CacheHelper_Class
{
	public static function setCache($key, $value, $expired = '')
	{
		return;
		
		global $Config;
		$host = $Config['memcached']['host'];
		$port = $Config['memcached']['port'];
		$prefix = $Config['memcached']['prefix'];

		if(empty($expired)){
			//设置默认的失效时间
			$expired = $Config['memcached']['expired'];
		}

		$expired = $expired*1000;

		// 初始化
		$cache = new memcache();
		$cache->addServer($host, $port);

		// 写入
		$cache->set($prefix . $key, $value, MEMCACHE_COMPRESSED, $expired);//(CACHE_PREFIX 为了避免命名冲突，最好加一个前缀，MEMCACHE_COMPRESSED一个标记，设置为0表示不压缩)
	}

	public static function getCache($key)
	{
	
		global $Config;
		$host = $Config['memcached']['host'];
		$port = $Config['memcached']['port'];
		$prefix = $Config['memcached']['prefix'];

return null;
		// 初始化
		$cache = new memcache();
		$cache->addServer($host, $port);
		// 读取
		return $cache->get($prefix . $key);
	}
}