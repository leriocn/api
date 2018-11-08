<?php

/**
 * 应用管理类接口
 */
interface App_Manager_Interface{

	/**
	 * [DoWork 处理应用请求并返回字符串]
	 */
	public function doWork();

	/**
	 * 以下用于反馈信息
	 */
	public function setInitData($type,$param,$paramJson);
	public function getLastDoTime();
	public function getCacheStatus();
	public function getSuccess();
	public function getInformation();
	public function getResultData();
}