<?php

/**
* 数据库操作类
*/
class DataBase_Class
{
	/**
	 * [initConnect 获取数据库连接]
	 * @return [type] [链接失败返回空]
	 */
	private static function initConnect()
	{
		global $Config;
		$mysqli=new mysqli($Config['database']['db_host'],$Config['database']['db_user'],$Config['database']['db_psw'],$Config['database']['db_name']);
		if ($mysqli->connect_errno) {
	    	return null;
		}
		else{
			return $mysqli;
		}
	}

	/**
	 * [Query 查询到数组]
	 * @param [type] $query [description]
	 */
	public static function Query($query)
	{
		$mysqli = DataBase_Class::initConnect();
		if(isset($mysqli))
		{
			$mysqli->query("set names utf8");
			$result = $mysqli->query($query);
			if (isset($result) && $result->num_rows>0) 
			{
				#一次获取所有数据
				while($row = $result->fetch_array())
				{
					$data[] = $row;
				}

				$result->free();
			}
			
			$mysqli->close();
		}
		
		if(isset($data))
		{
			return $data;
		}
		else
		{
			return null;
		}
	}

	/**
	 * [Exec 执行脚本]
	 * @param string $value [description]
	 */
	public static function Exec($queryArray)
	{
		$rtn = false;
		$mysqli = DataBase_Class::initConnect();
		if(isset($mysqli))
		{
			$mysqli->query("set names utf8");
			for($i=0;$i<count($queryArray);$i++){
				$result = $mysqli->query($queryArray[$i]);
			}

			if (isset($result)) 
			{
				$rtn = true;
			}

			$mysqli->close();
		}
		return $rtn;
	}
}
