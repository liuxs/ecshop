<?php

/**
 * ECSHOP 银联在线支付主文件
 ****唯一一条备注，使用者请尊重劳动成果，手下留情！****
	插件名称：银联在线支付(UPOP)插件 FOR ECSHOP
	插件版本：1.0
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

if (!defined('IN_ECS'))
{
    die('Hacking attempt');
}

// 包含配置文件
$payment_lang = ROOT_PATH . 'languages/' .$GLOBALS['_CFG']['lang']. '/payment/upop.php';

if (file_exists($payment_lang))
{
    global $_LANG;

    include_once($payment_lang);
}

/* 模块的基本信息 */
if (isset($set_modules) && $set_modules == TRUE)
{
    $i = isset($modules) ? count($modules) : 0;

    /* 代码 */
    $modules[$i]['code']    = basename(__FILE__, '.php');

    /* 描述对应的语言项 */
    $modules[$i]['desc']    = 'upop_desc';

    /* 是否支持货到付款 */
    $modules[$i]['is_cod']  = '0';

    /* 是否支持在线支付 */
    $modules[$i]['is_online']  = '1';

    /* 作者 */
    $modules[$i]['author']  = '中国程项目组';

    /* 网址 */
    $modules[$i]['website'] = 'http://www.cnvar.net';

    /* 版本号 */
    $modules[$i]['version'] = '1.0.0';

    /* 配置信息 */
    $modules[$i]['config'] = array(
		array('name' => 'upop_charset', 'type' => 'select', 'value' => 'gbk'),
		array('name' => 'upop_evn', 'type' => 'select', 'value' => '1'),
		array('name' => 'upop_merAbbr', 'type' => 'text', 'value' => 'CNVAR'),
		array('name' => 'upop_account_test', 'type' => 'text', 'value' => '105550149170027'),
		array('name' => 'upop_security_key_test', 'type' => 'text', 'value' => '88888888'),
        array('name' => 'upop_account', 'type' => 'text', 'value' => ''),
		array('name' => 'upop_security_key', 'type' => 'text', 'value' => ''),
    );

    return;
}

/**
 * 类
 */
class UPOP
{
    /**
     * 生成支付代码
     * @param   array   $order  订单信息
     * @param   array   $payment    支付方式信息
     */
    function get_code($order, $payment)
    {
		// 初始化变量
		$upop_charset	= $payment['upop_charset'];	// 编码
		$upop_evn		= $payment['upop_evn'];		// 环境
		$lib_path		= ROOT_PATH . 'includes/modules/payment/upop/' . $upop_charset . '/';
		
		// 包含库接口文件
		include_once($lib_path . 'quickpay_service.php');

		if (!class_exists('quickpay_conf') || !class_exists('quickpay_service'))
			return '缺少支付方式文件。';

		
		// 商户名称
		quickpay_conf::$pay_params['merAbbr']		= $payment['upop_merAbbr'];

		// 生产环境
		if ($upop_evn == '2')
		{
			quickpay_conf::$security_key			= $payment['upop_security_key'];
			quickpay_conf::$pay_params['merId']		= $payment['upop_account'];
			quickpay_conf::$front_pay_url			= "https://unionpaysecure.com/api/Pay.action";
			quickpay_conf::$back_pay_url			= "https://unionpaysecure.com/api/BSPay.action";
			quickpay_conf::$query_url				= "https://unionpaysecure.com/api/Query.action";
		}

		// 测试环境
		else if ($upop_evn == '1')
		{
			quickpay_conf::$security_key			= $payment['upop_security_key_test'];
			quickpay_conf::$pay_params['merId']		= $payment['upop_account_test'];
			//quickpay_conf::$front_pay_url			= "http://58.246.226.99/UpopWeb/api/Pay.action";
			//quickpay_conf::$back_pay_url			= "http://58.246.226.99/UpopWeb/api/BSPay.action";
			//quickpay_conf::$query_url				= "http://58.246.226.99/UpopWeb/api/Query.action";

			quickpay_conf::$front_pay_url			= "https://www.epay.lxdns.com/UpopWeb/api/Pay.action";
			quickpay_conf::$back_pay_url			= "https://www.epay.lxdns.com/UpopWeb/api/BSPay.action";
			quickpay_conf::$query_url				= "https://www.epay.lxdns.com/UpopWeb/api/Query.action";
		}

		mt_srand(quickpay_service::make_seed());

		$param = array();

		$param['transType']             = quickpay_conf::CONSUME;  // 交易类型，CONSUME or PRE_AUTH
		$param['orderAmount']           = $order['order_amount'] * 100;  // 交易金额 转化为分
		$param['orderNumber']           = $order['order_sn'] . '-' . $this->_formatSN($order['log_id']);		   // 订单号，必须唯一
		$param['orderTime']             = date('YmdHis');		   // 交易时间, YYYYmmhhddHHMMSS
		$param['orderCurrency']         = quickpay_conf::CURRENCY_CNY;  //交易币种，CURRENCY_CNY=>人民币

		$param['customerIp']            = $_SERVER['REMOTE_ADDR'];  // 用户IP
		$param['frontEndUrl']           = return_url(basename(__FILE__, '.php'));   // 前台回调URL
		$param['backEndUrl']            = return_url(basename(__FILE__, '.php'));    // 后台回调URL

		/* 可填空字段
		   $param['commodityUrl']          = "http://www.example.com/product?name=商品";  //商品URL
		   $param['commodityName']         = '商品名称';   //商品名称
		   $param['commodityUnitPrice']    = 11000;        //商品单价
		   $param['commodityQuantity']     = 1;            //商品数量
		*/
		

		$button = "<input type='submit' value='" . $GLOBALS['_LANG']['upop_button'] . "' />";
		$pay_service = new quickpay_service($param, quickpay_conf::FRONT_PAY);
		$html = $pay_service->create_html($button);

        return $html;
    }

    /**
     * 响应操作
     */
    function respond()
    {
        $payment        = get_payment('upop');

		// 初始化变量
		$upop_charset	= $payment['upop_charset'];	// 编码
		$upop_evn		= $payment['upop_evn'];		// 环境
		$merchant_id		= $upop_evn == 2 ? $payment['paypal_account'] : $payment['upop_account_test'];
		$lib_path			= ROOT_PATH . 'includes/modules/payment/upop/' . $upop_charset . '/';

		// 包含库接口文件
		include_once($lib_path . 'quickpay_service.php');

		if (!class_exists('quickpay_conf') || !class_exists('quickpay_service'))
			return false;

		// 商户名称
		quickpay_conf::$pay_params['merAbbr']		= $payment['upop_merAbbr'];

		// 生产环境
		if ($upop_evn == '2')
		{
			quickpay_conf::$security_key			= $payment['upop_security_key'];
			quickpay_conf::$pay_params['merId']		= $payment['upop_account'];
			quickpay_conf::$front_pay_url			= "https://unionpaysecure.com/api/Pay.action";
			quickpay_conf::$back_pay_url			= "https://unionpaysecure.com/api/BSPay.action";
			quickpay_conf::$query_url				= "https://unionpaysecure.com/api/Query.action";
		}

		// 测试环境
		else if ($upop_evn == '1')
		{
			quickpay_conf::$security_key			= $payment['upop_security_key_test'];
			quickpay_conf::$pay_params['merId']		= $payment['upop_account_test'];
			quickpay_conf::$front_pay_url			= "http://58.246.226.99/UpopWeb/api/Pay.action";
			quickpay_conf::$back_pay_url			= "http://58.246.226.99/UpopWeb/api/BSPay.action";
			quickpay_conf::$query_url				= "http://58.246.226.99/UpopWeb/api/Query.action";
		}

		try {
			$response = new quickpay_service($_POST, quickpay_conf::RESPONSE);
			if ($response->get('respCode') != quickpay_service::RESP_SUCCESS) 
			{
				$err = sprintf("Error: %d => %s", $response->get('respCode'), $response->get('respMsg'));
				throw new Exception($err);
			}

			$arr_ret = $response->get_args();
			
			if(!strpos($arr_ret['orderNumber'], '-')) return false;
			$order_sn_arr = explode('-', $arr_ret['orderNumber']);
			
			$order_sn		= $order_sn_arr[0];
			$pay_id = (int)$order_sn_arr[1];
			$payment_amount = (int)$arr_ret['settleAmount'];

			// 检查商户账号是否一致。
			if (quickpay_conf::$pay_params['merId'] != $arr_ret['merId'])
			{
				return false;
			}

			// 检查价格是否一致。
			$sql = "SELECT p.order_amount FROM " . $GLOBALS['ecs']->table('pay_log') . " AS p LEFT JOIN " . $GLOBALS['ecs']->table('order_info') . " AS o ON p.order_id = o.order_id WHERE o.order_sn = '"
			. $order_sn . "'";
			$order_amount = $GLOBALS['db']->getOne($sql) * 100;

			if ($order_amount != $payment_amount)
			{
				return false;
			}

			// 如果未支付成功。
			if ($arr_ret['respCode'] != '00')
			{
				return false;
			}

			$action_note = $arr_ret['respCode'] . ':' 
					. $arr_ret['respMsg'] 
					. $GLOBALS['_LANG']['upop_txn_id'] . ':' 
					. $arr_ret['qid'];

			// 完成订单。
			order_paid($pay_id, PS_PAYED, $action_note);

			//告诉用户交易完成
			return true;
        
		}
		catch(Exception $exp) 
		{
			return false;
		}
    }


	/**
	* 格式订单号
	*/
	function _formatSN($sn)
	{
		return str_repeat('0', 9 - strlen($sn)) . $sn;
	}
}
?>