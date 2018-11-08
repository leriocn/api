<?php
/**
* 应用处理类
* 应用ID：API_api_speech_baidu
*/
class API_api_speech_baidu implements App_Manager_Interface
{
	// 你的 APPID AK SK
	const APP_ID = '11568580';
	const API_KEY = 'tPLaXs98N954I3OSYhzvwiy0';
	const SECRET_KEY = 'hG2eTljWnnsvdVol64r3CpLqlb3767Xq';
		
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

	private function getCacheKey($cahceType)
	{
		return'API_api_speech_baidu'.$cahceType.$this->type.$this->param->Word;
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


	public function getData()
	{
		@$word = $this->param->Word;
		$client = new AipSpeech(APP_ID, API_KEY, SECRET_KEY);
		
		// 识别正确返回语音二进制 错误则返回json 参照下面错误码
		if($word){
			$rootPath = $_SERVER['DOCUMENT_ROOT'];
			$directory = 'temp/audio/';
			
			$absDirectory = $rootPath.'/'.$directory;
			if (!is_dir($absDirectory)) mkdir($absDirectory);
			
			$audioArr = [];
			
			//按照1024长度拆分
			while(strlen($word)>0){
				if(strlen($word)>1020){
					$need = substr($word,0,1020);
					$word = substr($word,1020);
				}
				else{
					$need = $word;
					$word = '';
				}
				
				$result = $client->synthesis($need, 'zh', 1, array( 'vol' => 5,));
				if(is_array($result))
				{
					$relaFileName = $directory.ExtendManager_Class::createGuid().'.mp3';
					$absFileName = $rootPath.'/'.$relaFileName;
					file_put_contents($absFileName, $result);
					array_push($audioArr,$relaFileName);
				}
			}
			
			if(count($audioArr)>0){
				$this->success = 'yes';
				$this->resultData = $audioArr;
			}
			else{
				$this->success = 'no';
				$this->information = '语音合成失败';
			}
		}
		else{
			$this->success = 'no';
			$this->information = '文本不可为空';
		}
	}
}