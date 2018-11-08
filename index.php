<?php session_start();//开启Session ?>
<?php require_once('config/global.config.php');?>
<?php require_once('class/extend.class.php');?>
<?php require_once('class/check.class.php');?>
<?php require_once('class/database.class.php');?>
<?php require_once('class/cache.class.php');?>
<?php require_once('class/api.interface.php');?>

<?php //开始加载APP ?>
<?php require_once('class/loadapp.class.php');?>
<?php AppLoadManager_Class::loadApp();?>

<?php //开始加载外部插件 ?>
<?php //require_once('plugin/simple_html_dom.php');?>
<?php require_once('plugin/querylist.class.php');?>
<?php require_once('plugin/AipSpeech.php');?>
<?php

	//通用扩展类
	if(ExtendManager_Class::isPost())
	{
		//进行各变量的赋值工作
		@$paramJson = $_POST['param'];//参数列表
//echo $paramJson;
		@$param = json_decode($paramJson);
		if(DataValideCheck_Class::check($param) == false)
		{
			$result = array('success' => 'no',
							'information' => '参数调用有误.');
			echo ExtendManager_Class::getResultJson($result);
			exit();
		}

		//身份校验
		if(IdentityCheck_Class::check($param) == false)
		{
			$result = array('success' => 'no',
							'information' => '身份验证有误.');
			echo ExtendManager_Class::getResultJson($result);
			exit();
		}

		$appId = $param->name;//插件ID
		$operateType = $param->type;//操作类型

		//替换关键
		//appid样例：API_www_feixiaohao_com->www.feixiaohao.com
		$appId	= str_replace('.', '_', $appId);

		//创建app管理接口
		$appManager = ExtendManager_Class::createAPPManager($appId);
		if(isset($appManager)){
			//初始化数据
			$appManager->setInitData($operateType,$param,$paramJson);

			$curTime = ExtendManager_Class::getTimestamp();
			$lastDoTime = $appManager->getLastDoTime();
			$expired = $param->expired;
			$top = $param->top;

			if($expired < 5)
			{
				$expired = 5;
				$param->expired = $expired;
			}

			if($top >= 100)
			{
				$top = 100;
			}

			if($curTime - $lastDoTime <= $expired && $appManager->getCacheStatus())
			{
				//可以用缓存
				//getCacheStatus 方式中若已经存在缓存，直接进行了赋值处理
				$dataType =  'cache';
			}
			else
			{
				$dataType =  'dowork';
				$appManager->doWork();
			}

			$result = array('success' => $appManager->getSuccess(),
							'information' => $appManager->getInformation(),
							'copyright' => 'lerio.com.cn',
							'qq' => '764109520',
							'expired' => $param->expired,
							'resulttype' => $dataType,
							'resultdata' => $appManager->getResultData());

			echo ExtendManager_Class::getResultJson($result);
			exit();
		}
		else{

			$result = array('success' => 'no',
							'information' => '不识别的app调用.');
			echo ExtendManager_Class::getResultJson($result);
			exit();
		}
	}
	else{
		$result = array('success' => 'no',
						'information' => 'Hello World!');
		echo ExtendManager_Class::getResultJson($result);
		exit();
	}