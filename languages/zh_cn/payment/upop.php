<?php

/**
 * ECSHOP 银联在线语言包文件
 ****唯一一条备注，使用者请尊重劳动成果，手下留情！****
	插件名称：银联在线支付(UPOP)插件 FOR ECSHOP
	插件版本：1.0 Beta
	插件作者：中国程项目组（成都之达科技有限公司）
	插件编码：GBK
	适用版本：ECSHOP 2.60及以上
	更新地址：http://www.cnvar.net/9025511.html
	支持站点：www.cnvar.net
	反馈地址：http://www.cnvar.net/9025511.html
	反馈邮箱/QQ账号：cnvar@qq.com
	
	目前仅支持中文版本，英文、繁体版本请自行调试
	有问题请及时反馈谢谢，可进群在线邀请群友进行帮助调试
	唯一官方QQ交流群：87010870
	
 */

global $_LANG;

$_LANG['upop']                       = '银联在线支付';
$_LANG['upop_desc']                  = '银联在线支付是中国银联推出的网上支付平台，支持多家发卡银行，涵盖借记卡和信用卡等，包含认证支付、快捷支付和网银支付多种方式，其中认证和快捷支付无需开通网银，仅需一张银行卡，即可享受安全、快捷的网上支付服务！';

// 接口运行环境
$_LANG['upop_evn']					= '环境选择';
$_LANG['upop_evn_range']['1']		= '测试环境';
$_LANG['upop_evn_range']['2']		= '生产环境';

// 接口运行环境
$_LANG['upop_charset']				= '选择编码';
$_LANG['upop_charset_range']['gbk']	= 'GBK';
$_LANG['upop_charset_range']['utf8']= 'UTF8';

$_LANG['upop_merAbbr']				= '商户名称';

// 测试环境
$_LANG['upop_account_test']         = '测试商户账号';
$_LANG['upop_security_key_test']    = '测试商户密钥';

$_LANG['upop_front_pay_test']		= '测试前台支付';
$_LANG['upop_back_pay_test']		= '测试后台交易';
$_LANG['upop_query_test']			= '测试信息查询';

// 生产环境
$_LANG['upop_account']              = '商户帐号';
$_LANG['upop_security_key']			= '商户密钥';

$_LANG['upop_front_pay']			= '前台支付';
$_LANG['upop_back_pay']				= '后台交易';
$_LANG['upop_query']				= '信息查询';


$_LANG['upop_button']               = '马上使用银联支付';
$_LANG['upop_txn_id']               = '银联交易号';

?>