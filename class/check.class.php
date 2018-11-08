<?php
/**
 * 来源校验
 */
class IdentityCheck_Class
{

	/**
	 * [CheckAccessKey 检验请求有效性，根据Key和密钥]
	 * @param  [type] $param [description]
	 * @param string $value [bool]
	 */
	private static function checkAccessKey($param)
	{
		try
		{
			$expressStr = $param->name.$param->type.$param->expired.$param->top.$param->key.$param->time;
			$encryptStr = ExtendManager_Class::encryptValideData($expressStr);
			if($param->value != $encryptStr)
			{
				return false;
			}
			else
			{
				return true;
			}
		}
		catch(Exception $e)
		{
			return false;
		}
	}


	/**
	 * [check 身份校验]
	 * @param  [type] $param [description]
	 * @return [type]        [description]
	 */
	public static function check($param)
	{
		$result = IdentityCheck_Class::checkAccessKey($param);
		if($result)
		{
			//扩展需要校验的内容，直接赋值给$result

		}

		return $result;
	}
}

/**
 * 数据有效性校验
 */
class DataValideCheck_Class
{
	/**
	 * [check description]
	 * @param  [type] $param [参数json对象]
	 * @return [type]            [bool]
	 */
	public static function check($param)
	{
		try
		{
			if(empty($param))
			{
				return false;
			}

			if(empty($param->name) || empty($param->type))
			{
				return false;
			}

			if(empty($param->expired) || empty($param->top))
			{
				return false;
			}

			if(empty($param->key) || empty($param->time) || empty($param->value)) //value 为加密数据
			{
				return false;
			}

			//校验时间有效性
			$result = DataValideCheck_Class::checkTimeValide($param);
			if($result)
			{
				//扩展需要校验的内容，直接赋值给$result



			}

			return $result;
		}
		catch(Exception $e)
		{
			return false;
		}
	}

	/**
	 * [checkTimeValide 检查时间有效性]
 	 * @param  [type] $param [description]
	 * @return [type] [bool]
	 */
	private static function checkTimeValide($param)
	{
		global $Config;
		$curTime = ExtendManager_Class::getTimestamp();
		$timeout = $Config['setting']['check']['timeout'];
		if($curTime - $param->time > $timeout)
		{
			return false;
		}
		else
		{
			return true;
		}
	}
}
