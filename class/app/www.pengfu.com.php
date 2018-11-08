<?php
/**
* 应用处理类
* 应用ID：API_www_pengfu_com
*/
class API_www_pengfu_com implements App_Manager_Interface
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
		return'API_www_pengfu_com'.$cahceType.$this->type;
	}

	/**
	 * [DoWork 处理应用请求并返回字符串]
	 */
	public function doWork()
	{
		switch ($this->type) {
			case 'getImg':
				# code...
				$this->getImg();
				break;
			case 'getXiaoHua':
				# code...
				$this->getXiaoHua();
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
	public function getImg()
	{
		//目前一共25页，随机获取一页的一个数据
		$pageIndex = rand(1,25);
		$url = "https://www.pengfu.com/qutu_$pageIndex.html";
		$reg = array('title'=>array('dd h1','text'),'gif' => array('dd .content-img img','gifsrc'),"jpg" => array('dd .content-img img','src'));
		$rang = '.list-item';
		//使用curl抓取源码并以utf-8编码格式输出
		$qy = new QueryList($url,$reg,$rang,'curl','utf-8');
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

		//替换动图
		for($i=0;$i<count($arr,0);$i++)
		{
			if(isset($arr[$i]['gif']))
			{
				$arr[$i]['jpg'] = $arr[$i]['gif'];
			}
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

	/**
	 * [getXiaoHua description]
	 * @return [type] [description]
	 */
	public function getXiaoHua()
	{
		//目前一共50页，随机获取一页的一个数据
		$pageIndex = rand(1,50);
		$url = "https://www.pengfu.com/xiaohua_$pageIndex.html";
		$reg = array('title'=>array('dd h1','text'),'content' => array('dd .content-img','text'));
		$rang = '.list-item';
		//使用curl抓取源码并以utf-8编码格式输出
		$qy = new QueryList($url,$reg,$rang,'curl','utf-8');
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