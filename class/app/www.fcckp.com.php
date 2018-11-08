<?php
/**
* 应用处理类
* 应用ID：API_www_fcckp_com
*/
class API_www_fcckp_com implements App_Manager_Interface
{
	private $type;
	private $param;
	private $paramJson;
	public function setInitData($type,$param,$paramJson)
	{
		$this->type = $type;
		$this->param = $param;
		$this->paramJson = $paramJson;
	}

	private $success;
	private $information;
	private $resultData;
	public function getSuccess(){
		return $this->success;
	}
	public function getInformation(){
		return $this->information;
	}
	public function getResultData(){
		return $this->resultData;
	}

	public function getLastDoTime(){
		$cacheKey = $this->getCacheKey('lastDoTime');
		$cacheValue = CacheHelper_Class::getCache($cacheKey);
		if(isset($cacheValue))
		{
			return (int)$cacheValue;
		}
		else
		{
			return 0;
		}
	}

	public function getCacheStatus()
	{
		$cacheKey = $this->getCacheKey('resultData');
		$cacheResult = CacheHelper_Class::getCache($cacheKey);
		if(isset($cacheResult) && $cacheResult)
		{
			$this->success = 'yes';
			$this->resultData = $cacheResult;
			return true;
		}
		else
		{
			return false;
		}
	}

	/**
	 * [getCacheKey description]
	 * @param  [type] $cahceType [description]
	 * @return [type]            [description]
	 */
	private function getCacheKey($cahceType)
	{
		return'API_www_fcckp_com'.$cahceType.$this->type.$this->param->expired.$this->param->kw;
	}

	/**
	 * [DoWork 处理应用请求并返回字符串]
	 */
	public function doWork()
	{
		switch ($this->type) {
			case 'getData':
				# code...
				$this->getData();
				break;
			default:
				# code...
				$this->success = 'no';
				$this->information = 'App不存在操作'.$this->type.'.';
				break;
		}

		if($this->success == 'yes')
		{
			//Cache
			$cacheKey = $this->getCacheKey('resultData');
			CacheHelper_Class::setCache($cacheKey,$this->resultData,$this->param->expired);

			$curTime = ExtendManager_Class::getTimestamp();
			$cacheKey = $this->getCacheKey('lastDoTime');
			CacheHelper_Class::setCache($cacheKey,$curTime);
		}
	}

	/**
	 * [getData description]
	 * @return [type] [description]
	 */
	private function getData()
	{
		//目前一共25页，随机获取一页的一个数据
		$url = "http://www.fcckp.com/search.php";

		$reg = array('title'=>array('.xs3 a','text'),'view' => array('.xg1','text'),"actor" => array('p:eq(1)','text'),"a" => array('.xs3 a','href'),'time' => array('span:eq(0)','text'),'class' => array('span:eq(2)','text'));
		$rang = '.pbw';
		//使用curl抓取源码并以gbk编码格式输出
		$qy = new QueryList($url,$reg,$rang,'curlpost',false,array('srchtxt'=>$this->param->kw));
		$arr = $qy->jsonArr;

		$arrCount = count($arr,0);
		$topCount = $this->param->top;
		if($topCount > $arrCount)
		{
			$topCount = $arrCount;
		}

		if($topCount > 0)
		{
			$arr = array_slice($arr,0,$topCount,true);
		}

		if($topCount > 0)
		{
			$this->success = 'yes';
			$this->resultData = $arr;
		}
		else
		{
			$this->success = 'no';
			$this->information = '没有获取到数据！';
		}
	}
}