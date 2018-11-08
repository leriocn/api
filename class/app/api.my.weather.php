<?php
/**
* 应用处理类 天气预报
* 应用ID：API_api_my_weather
*/
class API_api_my_weather implements App_Manager_Interface
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

	private function getCacheKey($cahceType)
	{
		return'API_api_speech_baidu'.$cahceType.$this->type.$this->param->city;
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
		@$city = $this->param->city;
		if($city){
			$dataType = 'observe|forecast_24h|tips';//'observe|forecast_1h|forecast_24h|index|alarm|limit|tips|rise';

			$cityUrl = 'http://wis.qq.com/city/like?source=pc&city='.urlencode($city);
			$wthUrl = 'http://wis.qq.com/weather/common?source=pc&weather_type='.$dataType.'&province={0}&city={1}&county={2}';
			$jsonCitys = ExtendManager_Class::getContent($cityUrl);
			if($jsonCitys){
				$citys = json_decode($jsonCitys,true);
				if($citys['status'] == 200){
					$resultData = [];
					$dataArr = $citys['data'];
					if(count($dataArr) >0){
						foreach($dataArr as $k=>$v){
							$location = explode(',', $v);
							$tmpWhUrl = str_replace('{0}', urlencode($location[0]), $wthUrl);
							$tmpWhUrl = str_replace('{1}', urlencode($location[1]), $tmpWhUrl);
							$county = '';
							if(count($location)>2){
								$county = $location[2];
								$tmpWhUrl = str_replace('{2}', urlencode($county), $tmpWhUrl);
							}
							else{
								$tmpWhUrl = str_replace('{2}', '', $tmpWhUrl);
							}

							$jsonDetail = ExtendManager_Class::getContent($tmpWhUrl);
							$detailData = json_decode($jsonDetail);
							
							
							$itemData = [ 'province' => $location[0] , 'city' => $location[1] , 'county' => $county , 'ret' => $detailData];
							array_push($resultData, $itemData);
						}
						$this->success = 'yes';
						$this->resultData = $resultData;
					}
					else{
						$this->success = 'no';
						$this->information = '城市信息获取失败,未找到城市:'.$city; 
					}
				}
				else{
					$this->success = 'no';
					$this->information = '城市信息获取失败,status:' . $citys.status . $citys.message;
				}
			}
			else{
				$this->success = 'no';
				$this->information = '城市信息获取失败';
			}
		}
		else{
			$this->success = 'no';
			$this->information = '城市不可为空';
		}
	}
}