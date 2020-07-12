<?php
/**
 * Created by PhpStorm.
 * User: wzy12
 * Date: 2018/9/18
 * Time: 14:47
 */

namespace app\component\controller;

class Pay extends Base
{
    // 商品描述
    public $body;
    // 订单号
    public $out_trade_no;
    // 订单金额
    public $total_fee;
    // 商品id
    public $product_id;
    // 商品名称
    public $subject;

    /**
     * @throws \WxPayException
     * 微信支付
     */
    public function wxPay(){
        $status = model('Plugin') -> where('code', 'weixin') -> value('status');
        if($status == 0){
            return $this -> error('微信支付未开启，请在插件功能开启该功能');
        }

        require_once EXTEND_PATH . "wxPay/lib/WxPay.Api.php";
        $input = new \WxPayUnifiedOrder;
        $input->SetBody($this -> body);
        $input->SetOut_trade_no($this -> out_trade_no);
        $input->SetTotal_fee((int) ($this -> total_fee * 100));
        $input->SetNotify_url(url('component/pay/notify', [], false, true));
        $input->SetTrade_type("NATIVE");
        $input->SetProduct_id($this -> product_id);
        $result = \WxPayApi::unifiedOrder($input);
        $code_url = $result["code_url"];

        return $code_url;
    }

    public function notify(){
        $xml = file_get_contents("php://input");
        $arr = json_decode(json_encode(simplexml_load_string($xml, 'SimpleXMLElement', LIBXML_NOCDATA)), true);
		
      	// 应支付金额
        $price = model('Order') -> where('order_sn', $arr['out_trade_no']) -> value('order_amount');
        $price = (int) ($price * 100);
      
      	// 判断支付状态
      	if(isset($arr['total_fee'])){
        	if($price ==  $arr['total_fee']){
              $status = 1;
              $desc = '支付成功';
            }else{
               $status = 3;
               $desc = '支付异常，支付金额和订单金额不相等';
            }
        }else{
          $status = 0;
          $desc = '支付失败';
        }
      	
      	// 支付状态
      	$data['pay_status'] = $status;
      	// 支付状态描述
      	$data['pay_desc'] = $desc;
      	// 支付订单号
      	$data['transaction_id'] = $arr['transaction_id'];
      	// 订单金额
      	$data['order_price'] = (float) ($price/100);
      	// 支付金额
        $data['pay_price'] = (float) ($arr['total_fee']/100);
      	// 支付时间
		$data['pay_time'] = time();
      	// 支付code和支付方式名称
      	$data['pay_code'] = 'weixin';
      	$data['pay_name'] = model('Plugin') -> where('code', $data['pay_code']) -> value('name');
      	// 订单号
      	$data['order_sn'] = $arr['out_trade_no'];
      	// 修改订单支付状态
      	if($status == 1){
      		$this -> editOrder($data);
        }
      	// 订单支付记录
        $res = model('PayRecord') -> allowField(true) -> save($data);

        return "<xml><return_code><![CDATA[SUCCESS]]></return_code><return_msg><![CDATA[OK]]></return_msg></xml>";
        die;
    }

    protected function editOrder($data){
      	// 修改订单支付状态
      	model('Order') -> allowField(true) -> isUpdate(true) -> save($data, ['order_sn' => $data['order_sn']]);
    }

    /**
     * 微信回调二维码
     */
    public function qcode(){
        require_once EXTEND_PATH . "wxPay/example/qrcode.php";
    }

    /**
     * 支付宝支付
     */
    public function aliPay(){
        $status = model('Plugin') -> where('code', 'alipay') -> value('status');
        if($status == 0){
            return $this -> error('支付宝支付未开启，请在插件功能开启该功能');
        }
		
        require_once EXTEND_PATH . "aliPay/pagepay/pagepay.php";
    }

    public function alinotify(){
      	$arr=$_GET;
      	
      	// 应支付金额
        $price = model('Order') -> where('order_sn', $arr['out_trade_no']) -> value('order_amount');
      
      	if($price ==  $arr['total_amount']){
          $status = 1;
          $desc = '支付成功';
        }else{
          $status = 2;
          $desc = '支付异常，支付金额和订单金额不相等';
        }
      	
      	// 支付状态
      	$data['pay_status'] = $status;
      	// 支付状态描述
      	$data['pay_desc'] = $desc;
      	// 支付订单号
      	$data['transaction_id'] = $arr['trade_no'];
      	// 订单金额
      	$data['order_price'] = $price;
      	// 支付金额
        $data['pay_price'] = $arr['total_amount'];
      	// 支付时间
		$data['pay_time'] = time();
      	// 支付code和支付方式名称
      	$data['pay_code'] = 'alipay';
      	$data['pay_name'] = model('Plugin') -> where('code', $data['pay_code']) -> value('name');
      	// 订单号
      	$data['order_sn'] = $arr['out_trade_no'];
      	// 修改订单支付状态
      	if($status == 1){
      		$this -> editOrder($data);
        }
      	// 订单支付记录
        $res = model('PayRecord') -> allowField(true) -> save($data);
      	return $this -> redirect(url('admin/index/index'));
      
      	//require_once EXTEND_PATH . "aliPay/config.php";
      	//require_once EXTEND_PATH . "aliPay/pagepay/service/AlipayTradeService.php";	
      	//$alipaySevice = new \AlipayTradeService($config); 
		//$result = $alipaySevice->check($arr);

		//if($result) {//验证成功
          /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
          //请在这里加上商户的业务逻辑程序代码

          //——请根据您的业务逻辑来编写程序（以下代码仅作参考）——
          //获取支付宝的通知返回参数，可参考技术文档中页面跳转同步通知参数列表

          //商户订单号
			//$out_trade_no = htmlspecialchars($_GET['out_trade_no']);

          //支付宝交易号
          	//$trade_no = htmlspecialchars($_GET['trade_no']);

        	//echo "验证成功<br />支付宝交易号：".$trade_no;

        	//——请根据您的业务逻辑来编写程序（以上代码仅作参考）——

        	/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
      	//}else {
        	//验证失败
        	//echo "验证失败";
      	//}
    }
}