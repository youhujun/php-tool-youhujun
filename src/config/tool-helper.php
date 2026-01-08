<?php
/*
 * @Descripttion: 自定义助手函数
 * @Author: YouHuJun
 * @Date: 2020-02-20 11:25:39
 * @LastEditors: youhujun 2900976495@qq.com
 * @LastEditTime: 2025-08-31 18:11:31
 */


 if(!function_exists('p'))
 {
     /**
      * 打印函数
      *
      * 该函数用于格式化输出传入的参数，便于调试和查看数据结构。
      *
      * @param mixed $param 要打印的参数，可以是任意类型。
      * @return void 无返回值。
      */
     function p($param):void
     {
         echo "<pre>";
         print_r($param);
         echo "</pre>";
     }
 }




 if(!function_exists('f'))
 {
     /**
      * 过滤字符串中的标签
      *
      * 此函数用于过滤输入参数中的 HTML 标签。可以处理字符串或数组类型的参数。
      * 
      * @param mixed $param 输入的字符串或数组
      * @param int $type 过滤类型，0 表示转换为 HTML 实体，1 表示去除 HTML 标签
      * @return mixed 返回过滤后的字符串或数组
      */
     function f($param,$type = 0)
     {
         if(is_array($param))
         {
              foreach($param as $key => $value)
              {
                  if(is_array($value))
                  {
                      $value = f($value);
                  }
                  else
                  {
					if(!$type)
					{
						$value = htmlspecialchars($value);
					}
					else
					{
						$value = strip_tags($value);
					}

                  }
              }
         }
         else
         {
            if(!$type)
			{
				$value = htmlspecialchars($value);
			}
			else
			{
				$value = strip_tags($value);
			}
         }
         return $param;
     }
 }

 if(!function_exists('code'))
 {
     /**
      * 处理请求返回的数据
      *
      * @param array $code 配置文件中的代码定义
      * @param array $add 需要手动添加的数据
      * @return array 合并后的结果数组
      *
      * 如果 $code 和 $add 均为 null，返回空数组；
      * 如果 $code 为 null，返回 $add；
      * 如果 $add 为 null，返回 $code；
      * 否则，返回 $code 和 $add 的合并结果。
      */
     function code($code=[],$add=[])
     {
         $resArr = [];
         if(is_null($code)&&is_null($add))
         {
            $resArr = [];
         }
         else if(is_null($code)&&!is_null($add))
         {
            $resArr = $add;
         }
         else if(!is_null($code)&&is_null($add))
         {
            $resArr = $code;
         }
         else
         {
            $resArr = array_merge($code,$add);
         }
        return  $resArr;
     }
 }




 if(!function_exists('is_serialized'))
{
    /**
     * 检测给定字符串是否为 PHP 序列化格式。
     *
     * 此函数通过检查字符串的格式来判断其是否为序列化数据。
     * 支持的序列化类型包括：数组、对象、字符串、布尔值、整数和浮点数。
     *
     * @param mixed $data 需要检测的字符串数据。
     * @return boolean 如果字符串是序列化的，返回 true；否则返回 false。
     */
    /**
     * 检测字符串是否是序列化的
     *
     * @param [type] $data
     * @return boolean
     */
    function is_serialized( $data )
    {
        $result = 0;
        $data = trim( $data );
        if ( 'N;' == $data )
        $result = 1;
        if ( !preg_match( '/^([adObis]):/', $data, $badions ) )
        $result = 0;
        switch ( $badions[1] )
        {
            case 'a' :
            case 'O' :
            case 's' :
                if ( preg_match( "/^{$badions[1]}:[0-9]+:.*[;}]\$/s", $data ) )
                $result = 1;
            break;
            case 'b' :
            case 'i' :
            case 'd' :
                if ( preg_match( "/^{$badions[1]}:[0-9.E-]+;\$/", $data ) )
                $result = 1;
            break;
        }
        return $result;
    }
}

/**
 * 请求处理 ++++++++++++++++++++++++++++++++++++++++++
 */

if(!function_exists('httpGet'))
{
   /**
    * 发起 CURL 的 GET 请求
    *
    * @param string $url 请求地址
    * @return mixed 请求的结果
    *
    * @throws Exception 如果请求失败
    */
   function httpGet($url)
   {
       $curl = curl_init();
       curl_setopt($curl, CURLOPT_RETURNTRANSFER, true );
       curl_setopt($curl, CURLOPT_TIMEOUT, 10 );
       curl_setopt($curl, CURLOPT_URL, $url );
       $res = curl_exec($curl);
       curl_close($curl);
       return $res;
   }
}
if(!function_exists('httpPost'))
{
      /**
       * 发起 CURL 的 POST 请求
       *
       * @param string $url 请求的 URL 地址
       * @param array $headers 请求头数组，默认为空
	   * $headers = ['X-TOKEN:'.$this->token,'X-VERSION:1.1.3','Content-Type:application/json','charset=utf-8'];
       * @param mixed $data 请求的数据，默认为 null
       * @return mixed 返回请求结果
       */
    function httpPost($url, $headers=[],$data=null)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL,$url);
        curl_setopt($ch, CURLOPT_HEADER,0);
        //设置请求头
        if(is_array($headers) && count($headers) > 0)
        {
           curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        }

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        if(!empty($data)){
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        }
        $res = curl_exec($ch);
        curl_close($ch);
        return $res;
    }
}
/**
 * 请求处理结束--------------------------------------------------

 */

/**
 * 协助处理数组++++++++++++++++++++++++++++
 */

if (!function_exists('array_level')) {
    /**
     * 计算给定数组的维度。
     *
     * 此函数通过调用 total 函数来计算数组的层级，并返回数组的最大维度。
     *
     * @param Array $arr 需要计算的数组
     * @return int 返回数组的维度
     */
    function array_level(array $arr)
    {
        $al = [0];
        total($arr, $al);
        return max($al);
    }
}
if (!function_exists('total')) {
    /**
     * 计算给定数组的维度。
     *
     * 此函数递归地遍历数组，并统计其维度。每当遇到一个数组时，维度计数器增加。
     *
     * @param array $arr 需要计算维度的数组。
     * @param array &$al 用于存储每一层的维度信息的引用数组。
     * @param int $level 当前的维度层级，默认为0。
     * @return void 此函数没有返回值。
     */
    function total($arr, &$al, $level = 0)
    {
        if (is_array($arr)) {

            $level++;

            $al[] = $level;

            foreach ($arr as $v) {
                total($v, $al, $level);
            }
        }
    }
}

if (!function_exists('toArray'))
{
    /**
     * 将给定的数组转换为包含其元素的数组。
     *
     * @param array $array 输入的数组
     * @return array 转换后的数组
     */
   
    function toArray($array)
    {
        if(is_array($array))
        {
            foreach($array as $k => &$v)
            {
                $v =  array($v);
            }
        }

        return $array;
    }
}

/**
 * 协助处理数组结束--------------------------
 */



if(!function_exists('checkId'))
{
    /**
     * 检查给定的值是否为有效的 ID。
     *
     * 该函数使用正则表达式验证输入的 $id 是否仅由数字组成。
     * 如果验证失败，则返回 0。
     *
     * @param mixed $id 要检查的值
     * @return int 返回有效的 ID 或 0
     */
   
    function checkId($id)
    {
        $partten = "/^[0-9]+$/";

        $regexResult = \preg_match($partten,$id);

        if(!$regexResult)
        {
            $id = 0;
        }

        return $id;
    }
}



