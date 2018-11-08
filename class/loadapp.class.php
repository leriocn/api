<?php
/**
 * 动态加载app
 */
class AppLoadManager_Class
{
    public static function loadApp($value='')
    {
        //1、先打开要操作的目录，并用一个变量指向它
        //打开当前目录下的目录pic下的子目录common。

        $handler = opendir('class/app');
        if(isset($handler))
        {
            $end = '.php';
            //2、循环的读取目录下的所有文件
            /*其中$filename = readdir($handler)是每次循环的时候将读取的文件名赋值给$filename，为了不陷于死循环，所以还要让$filename !== false。一定要用!==，因为如果某个文件名如果叫’0′，或者某些被系统认为是代表false，用!=就会停止循环*/
            while( ($filename = readdir($handler)) !== false )
            {
                  //3、目录下都会有两个文件，名字为’.'和‘..’，不要对他们进行操作
                  if($filename != '.' && $filename != '..')
                  {
                    if(substr($filename, 0-strlen($end)) == $end)
                      //4、进行处理
                      //这里简单的用echo来输出文件名
                      require_once("class/app/$filename");
                      //echo $filename;
                  }
            }

            //5、关闭目录
            closedir($handler);
        }
    }
}