<?php
/**
* 扩展处理类
*/
class ExtendManager_Class
{
	/**
	 * 是否是AJAx提交的
	 * @return bool
	 */
	public static function isAjax(){
	    if(isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest'){
	        return true;
	    }else{
	        return false;
	    }
	}

	/**
	 * 是否是GET提交的
	 */
	public static function isGet(){
	    return $_SERVER['REQUEST_METHOD'] == 'GET' ? true : false;
	}

	/**
	 * 是否是POST提交
	 * @return bool
	 */
	public static function isPost() {
	    return ($_SERVER['REQUEST_METHOD'] == 'POST') ? true : false;
	}

	public static function startWith($str,$pattern) {
	    if(strpos($str,$pattern) === 0)
	          return true;
	    else
	          return false;
	}

	/**
	 *
	* 返回一定位数的时间戳，多少位由参数决定
	*
	* @param type 多少位的时间戳；默认是10位
	* @return 时间戳
	 */
	public static function getTimestamp($digits = false) {
		$digits = $digits > 10 ? $digits : 10;
		$digits = $digits - 10;
		if ((!$digits) || ($digits == 10))
		{
			return time();
		}
		else
		{
			return number_format(microtime(true),$digits,'','');
		}
	}

	/**
	 * 返回16位md5值
	 *
	 * @param string $str 字符串
	 * @return string $str 返回16位的字符串
	 */
	public static function shortMd5($str) {
	    return substr(md5($str), 8, 16);
	}

	/**
	 * [encryptValideData 加密校验字符串]
	 * @param  [type] $str [description]
	 * @return [type]      [description]
	 */
	public static function encryptValideData($str){
		global $Config;
		$encryptKey = $Config['setting']['encryptkey'];
		return ExtendManager_Class::shortMd5($encryptKey.$str);
	}

	/**
	 * [createGuid 生成唯一标志]
	 * @param  string $namespace [description]
	 * @return [type]            [description]
	 */
	public static function createGuid($namespace = '') {
	    static $guid = '';
	    $uid = uniqid('', true);
	    $data = $namespace;
	    $data .= $_SERVER['REQUEST_TIME'];
	    $data .= $_SERVER['HTTP_USER_AGENT'];
	    $data .= $_SERVER['LOCAL_ADDR'];
	    $data .= $_SERVER['LOCAL_PORT'];
	    $data .= $_SERVER['REMOTE_ADDR'];
	    $data .= $_SERVER['REMOTE_PORT'];
	    $hash = strtoupper(hash('ripemd128', $uid . $guid . md5($data)));
	    $guid = '{' .
	            substr($hash,  0,  8) .
	            '-' .
	            substr($hash,  8,  4) .
	            '-' .
	            substr($hash, 12,  4) .
	            '-' .
	            substr($hash, 16,  4) .
	            '-' .
	            substr($hash, 20, 12) .
	            '}';
	    return $guid;
  }

  	/**
	 * [getResultJson 返回处理完毕的Json数据，已经对校验进行了处理]
	 * @param  [type] $result [此内容可以是数组也可以是文本]
	 * @return [type]         [返回的Json字符串]
	 */
	public static function getResultJson($result)
	{
		return json_encode($result,JSON_UNESCAPED_UNICODE);
		//return json_encode($result);
	}

	/**
	 * [createAPPManager ]
	 * @param  [type] $appId [appID]
	 * @return [type] [description]
	 */
	public static function createAPPManager($appId)
	{
		try
		{
			$class = new ReflectionClass('API_'.$appId);//建立 Person这个类的反射类
			$instance  = $class->newInstanceArgs();//相当于实例化Person 类
		}
		catch(Exception $e)
		{
			//不合法的app调用
		}

		return @$instance;
	}

	/**
	 * [POSTDATA]
	 * @param  [type] $url    [登录的网址]
	 * @param  [type] $cookie [保存cookie到文件]
	 * @param  [type] $post   [post的内容]
	 * @return [type]         [返回抓取内容]
	 */
	public static function postData($url, $header, $postdata=null, $timeout=5) {
	    $curl = curl_init();//初始化curl模块
	    if(strpos(strtolower($url), 'https') === 0){
	    	//SSL
		    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false); // 跳过证书检查
		    curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, true);  // 从证书中检查SSL加密算法是否存在
	    }

	    curl_setopt($curl, CURLOPT_URL, $url);//登录提交的地址
	    curl_setopt($curl, CURLOPT_HEADER, 0);//是否显示头信息
	    curl_setopt($curl, CURLOPT_HTTPHEADER, $header);
		curl_setopt($curl, CURLOPT_FOLLOWLOCATION,0);
		curl_setopt($curl, CURLOPT_AUTOREFERER, 1); // 自动设置Referer
	    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);//是否自动显示返回的信息
     	curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, $timeout);
	    curl_setopt($curl, CURLOPT_POST, 1);//post方式提交
	    if(isset($postdata)){
	    	curl_setopt($curl, CURLOPT_POSTFIELDS, $postdata);//要提交的信息
		}	
	    $data = curl_exec($curl);//执行cURL
	    curl_close($curl);//关闭cURL资源，并且释放系统资源
	    return $data;
	}

	/**
	 * [get_content 登录成功后获取数据]
	 * @param  [type] $url    [抓取网址]
	 * @param  [type] $cookie [保存的cookie信息]
	 * @return [type]         [返回抓取内容]
	 */
	public static function getContent($url, $cookie='',$proxy='') {
	    $ch = curl_init();
	    curl_setopt($ch, CURLOPT_URL, $url);
	    if(strpos(strtolower($url), 'https') === 0){
	    	//SSL
		    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // 跳过证书检查
		    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);  // 从证书中检查SSL加密算法是否存在
	    }
	    if($proxy){
	    	curl_setopt ($ch, CURLOPT_PROXY, $proxy);
		}
	    curl_setopt($ch, CURLOPT_HEADER, false);
	    curl_setopt($ch,CURLOPT_USERAGENT,"Mozilla/5.0 (Windows NT 10.0; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/63.0.3239.132 Safari/537.36");
		curl_setopt($ch,CURLOPT_FOLLOWLOCATION,false);
		curl_setopt($ch, CURLOPT_FAILONERROR, false);
	    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	    if($cookie){
	    	curl_setopt($ch, CURLOPT_COOKIE, $cookie); //读取cookie	
	    }
	    curl_setopt($ch, CURLOPT_ENCODING, "gzip");
	    $rs = curl_exec($ch); //执行cURL抓取页面内容
	    curl_close($ch);
	    return $rs;
	}

	/**
	 * [mkFolder description]
	 * @param  [type] $path [description]
	 * @return [type]       [description]
	 */
	public static function makeFolder($path)
	{
	    if(!is_readable($path))
	    {
	        is_file($path) or mkdir($path, 0700);
	    }
	}

	public static function writeLogToFile($log)
	{
		$myfile = fopen("log.txt", "w") or die("Unable to open file!");
		fwrite($myfile, $log);
		fclose($myfile);
	}
}
