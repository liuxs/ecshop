<?php
$typeCom = $_GET["com"];
$typeNu = $_GET["nu"];

//echo $typeCom.'<br/>' ;
//echo $typeNu ;
include_once("kuaidi100_company.php");

if(isset($typeCom)&&isset($typeNu)){

	$AppKey='XXXXXX';//请将XXXXXX替换成您在http://kuaidi100.com/app/reg.html申请到的KEY
	$url ='http://api.kuaidi100.com/api?id='.$AppKey.'&com='.$typeCom.'&nu='.$typeNu.'&show=2&muti=1&order=asc';
	
	//请勿删除变量$powered 的信息，否者本站将不再为你提供快递接口服务。
	$powered = '查询服务由：<a href="http://www.kuaidi100.com" target="_blank" style="color:blue">友商快递100</a> 网站提供';
	
	
	//优先使用curl模式发送数据
	if (function_exists('curl_init') == 1){
	  $curl = curl_init();
	  curl_setopt ($curl, CURLOPT_URL, $url);
	  curl_setopt ($curl, CURLOPT_HEADER,0);
	  curl_setopt ($curl, CURLOPT_RETURNTRANSFER, 1);
	  curl_setopt ($curl, CURLOPT_USERAGENT,$_SERVER['HTTP_USER_AGENT']);
	  curl_setopt ($curl, CURLOPT_TIMEOUT,5);
	  $get_content = curl_exec($curl);
	  curl_close ($curl);
	}else{
	  include("snoopy.php");
	  $snoopy = new snoopy();
	  $snoopy->referer = 'http://www.google.com/';
	  $snoopy->fetch($url);
	  $get_content = $snoopy->results;
	}
	//$get_content=iconv('UTF-8', 'GB2312//IGNORE', $get_content);
	//if(strpos($get_content,'地点和跟踪进度')== false){
	//  echo '查询失败，请重试';
	//}
	print_r($get_content . '<br/>' . $powered);
	
}else{
	echo '查询失败，请重试';
}
exit();
?>
