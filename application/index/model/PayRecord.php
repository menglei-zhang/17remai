<?php
/**
 * Created by PhpStorm.
 * User: wzy12
 * Date: 2018/9/18
 * Time: 16:18
 */

namespace app\index\model;

use think\Model;
use app\index\model\Order;
// use app\index\model\Plugin;
use app\index\model\Users;
use app\component\controller\Pay;
use app\index\validate\PayRecord as Vali;

class PayRecord extends Model
{
	/**
     * 微信支付
     */
    public function pay($data)
    {
        // 验证
        $validate = new Vali();
        if(!$validate -> check($data)){
            return echoArr(500, $validate->getError());
        }

        // 订单信息
        $order = new Order();
        $info = $order->where('user_id', $data['user_id'])
              ->field('id,order_sn,pay_status,order_count')
              ->find($data['orderId']);
        
        // 判断订单是否支付过
        if($info['pay_status'] != 0){
            return echoArr(500, '订单已被支付');
        }

        // 用户openId
        $user = new Users();
        $data = $user->field('id,wx_open_id')->find($data['user_id']);
        $openId = $data['wx_open_id'];

        $wxpay = new Pay();
        // 商品描述
        $wxpay->body = "订单{$info['order_sn']}";
        // 订单号
        $wxpay->out_trade_no = $info['order_sn'];
        // 订单金额
        $wxpay->total_fee = $info['order_count'];
        // 商品id
        $wxpay->product_id = $info['id'];
        // 用户的openId
        $wxpay->openId = $openId;
        $res = $wxpay -> wxpay();
        $res['time'] = (string) time();

        return echoArr(200, '请求成功', $res);
    }
	/**
     * 微信支付余额
     */
    public function yePay($data)
    {
        // 验证
        $validate = new Vali();
        if(!$validate -> check($data)){
            return echoArr(500, $validate->getError());
        }
		// return $data;
        // 订单信息
        $order = new Order();
        $info = $order->where('id', $data['orderId'])
              ->field('id,order_sn,pay_status,order_count')
              ->find($data['orderId']);
        $info['order_sn'] = "17remai".time();
        if($info['pay_status'] != 0){
            return echoArr(500, '订单已被支付');
        }

        // 用户openId
        $user = new Users();
        $user = $user->field('id,wx_open_id')->find($data['user_id']);
        // return $data;
        $openId = $user['wx_open_id'];

        $wxpay = new Pay();
        // 商品描述
        $wxpay->body = "订单{$info['order_sn']}";
        // 订单号
        $wxpay->out_trade_no = $info['order_sn'];
        // 订单金额
        $wxpay->total_fee = $data['rest'];
        // 商品id
        $wxpay->product_id = $info['id'];
        // 用户的openId
        $wxpay->openId = $openId;
        $res = $wxpay -> wxpay();
        $res['time'] = (string) time();
		// appId: "wxfba40f3898fbd101"
		// nonceStr: "l2svcxbl8kltql8nkcxode81p72t0ttd"
		// package: "prepay_id=wx302008510664433cef2b84501686635700"
		// paySign: "13BA1710FCB58507D626F6574830C63F"
		// signType: "MD5"
		// time: "1593518931"
		// timeStamp: "1593518931"
        return $res;
    }
    
    /**
     * 微信支付余额
     */
    public function restPay($data)
    {
		// orderId: "8859"
		// rest: 8
		// user_id: 97
        // 验证
        $validate = new Vali();
        if(!$validate -> check($data)){
            return echoArr(500, $validate->getError());
        }

        // 订单信息
        $order = new Order();
        $info = $order->where('user_id', $data['user_id'])
              ->field('id,order_sn,pay_status,order_count')
              ->find($data['orderId']);
        return $info;
        // 判断订单是否支付过
        if($info['pay_status'] != 0){
            return echoArr(500, '订单已被支付');
        }

        // 用户openId
        $user = new Users();
        $data = $user->field('id,wx_open_id')->find($data['user_id']);
        $openId = $data['wx_open_id'];

        $wxpay = new Pay();
        // 商品描述
        $wxpay->body = "订单{$info['order_sn']}";
        // 订单号
        $wxpay->out_trade_no = $info['order_sn'];
        // 订单金额
        $wxpay->total_fee = $data['rest'];
        // 商品id
        $wxpay->product_id = $info['id'];
        // 用户的openId
        $wxpay->openId = $openId;
        $res = $wxpay -> wxpay();
        $res['time'] = (string) time();

        return echoArr(200, '请求成功', $res);
    }
}