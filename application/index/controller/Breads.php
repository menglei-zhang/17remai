<?php
/**
 * Created by PhpStorm.
 * User: wzy12
 * Date: 2018/9/18
 * Time: 11:11
 */  
namespace app\index\controller;

use think\Controller;
use app\index\model\Plugin;
use wxPay\lib\WxPayConfig;
use think\facade\Cache;
use app\index\model\Order as Morder;
use app\index\model\OrderGoods;
use app\index\model\Coupon;
use app\index\model\Users;
use app\index\model\Record;
use app\index\model\Bread;
use app\index\model\PayRecord;

class Breads extends Controller
{
  	/**
	 * @return 查询面包券状态
	 */
  	public function bread()
	{
		$data = json_decode(file_get_contents("php://input"), true);
		
		$result = model('Bread')->getList($data);
		
      	return json($result);
	}
  
  	/**
	 * @return 使用面包券
	 */
  	public function useBread()
	{
		$data = json_decode(file_get_contents("php://input"), true);
      	
		if($data['use'] == 0){
			$result = model('Bread')->where('code', $data['coupon'])->find();
			$dataAll['status'] = $result['status'];
          	$dataAll['couponPrice'] = $result['money'];
          	$temp['activate'] = 0;
          	$res = $result->isUpdate(true)->save($temp);
			return json(echoArr(200, '请求成功', $dataAll));
		}else{
			$result = model('Bread')->useList($data);
          	if($result['code'] == 500){
            	return json($result);
            }
          	
			$dataAll['status'] = $result['data']['status'];
			$dataAll['couponPrice'] = $result['data']['money'];
			foreach($data['goods'] as $k => $v){
				$dataAll['goods'][] = $v;
			}
			return json(echoArr(200, '请求成功', $dataAll));
		}

	}
  
  	/**
	 * @return 支付结果回调
	 */
    public function diaoyong()
    {
      	$order['orderSn'] = Cache::get('orderSn');
        $url = "http://2o038u1170.iok.la/Digital/breakpay!gateway.action?m=payBack&orderSn=".$order['orderSn']."&payment=1";
		
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_HTTPGET, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); //TRUE 将curl_exec()获取的信息以字符串返回，而不是直接输出。

        $header = ['User-Agent: php test']; //设置一个你的浏览器agent的header
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);

        curl_setopt($ch, CURLOPT_URL, $url);
        $result = curl_exec($ch);

        return $result;
    }

	/**
	 * @return 订单查询
	 */
    public function diaoyongs()
    {
      	$order['orderSn'] = Cache::get('orderSn');
        $url = "http://2o038u1170.iok.la/Digital/breakpay!gateway.action?m=orderList&orderSn=".$order['orderSn'];

        $ch = curl_init();

        curl_setopt($ch, CURLOPT_HTTPGET, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); //TRUE 将curl_exec()获取的信息以字符串返回，而不是直接输出。

        $header = ['User-Agent: php test']; //设置一个你的浏览器agent的header
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);

        curl_setopt($ch, CURLOPT_URL, $url);
        $result = curl_exec($ch);

        return $result;
    }
  
  	/**
     * @return 出货状态
     */
  	public function shipment()
    {
       // 获取订单信息
        $data = json_decode(file_get_contents("php://input"), true);
      
        $order = new Morder();
        $ordergoods = new OrderGoods();
        $users = new Users();
        $users -> startTrans();
        $orderData = $order->where('order_sn', $data['orderSn'])->find();
      
        if($data['shipment'] == 1){
            if($orderData['pay_code'] == 'wxCode'){
                $user = $users->where('id', $orderData['user_id'])->find();
                $oGoods = $ordergoods->where('order_id', $orderData['id'])->find();
                // 修改出货状态
                $oGoods['is_send'] = 1;
                $res = $oGoods->isUpdate(true)->save($oGoods);
                if($user){
                    $users -> commit();
                    return json(echoArr(200, '余额支付成功'));
                }else{
                    $users -> rollback();
                    return json(echoArr(500, '余额支付失败'));
                }
            }else{
                return json(echoArr(500, '请求错误'));
            }
        }else{
           if($orderData['pay_code'] == 'wxCode'){
                $user = $users->where('id', $orderData['user_id'])->field('user_money')->find();
                // 修改余额
                $user['user_money'] += $orderData['order_count'];

                $res = $user->isUpdate(true)->save($user);
                if(false === $res){
                    $users -> rollback();
                    return json(echoArr(500, '出货失败'));
                }else{
                    $users -> commit();
                    return json(echoArr(200, '出货失败,订单金额已退还您的账户'));
                }
            }else{
                return json(echoArr(500, '出货失败'));
            }
        }
    }
  
	/**
	 * @return 扫描二维码关注并支付
     */
	public function qrcode()
    {
      	// 获取订单id
      	$data = json_decode(file_get_contents("php://input"), true);
		Cache::set('orderSn', $data['orderSn']);
      	
      	$order = new Morder();
      	$orderGoods = new OrderGoods();
      
      	// 开启事务
      	$order -> startTrans();
      	$orderGoods -> startTrans();
      
      	// 添加订单
      	$orderData = [
        	'order_sn' => $data['orderSn'],
          	'pay_code' => 'wxCode',
          	'pay_name' => '小程序支付',
          	'order_count' => $data['totalPrice'],
        ];
      	$res = $order -> isUpdate(false) -> save($orderData);
      	if(false === $res){
          	$order -> rollback();
        	return echoArr(500, '订单保存失败');
        }
      
      	// 获取订单id
      	$orderId = $order -> id;
      
      	// 订单商品信息
      	$orderGoodsData = [];
      	foreach($data['goodsList'] as $k => $v){
         	$orderGoodsData = [
            	'goods_name' => $v['goodsName'],
              	'goods_num' => $v['goodsNum'],
              	'goods_price' => $v['goodsPrice'],
              	'order_id' => $orderId,
            ];
        }
      	$res = $orderGoods -> isUpdate(false) -> save($orderGoodsData);
     	if(false === $res){
          	// 事务回滚
          	$orderGoods -> rollback();
        	return echoArr(500, '订单商品保存失败');
        }
      	
      	// 提交事务
      	$order -> commit();
      	$orderGoods -> commit();
          
      	// 获取图片路径
      	$img = $this -> qCode($data['orderSn'], rand(0, 9999));
      
      	// 获取当前域名
      	$domain = $this -> request -> domain();
      
      	return json(echoArr(200, '请求成功', ['QRcode' => $domain . '/' . $img]));
    }
  
  	/**
     * 获取access_token，有效2小时
     */
    public function getAccessToken(){
        // 判断缓存是否存在
        $accessToken = Cache::get('accessToken');
        if($accessToken){
            return $accessToken;
        }

        // 微信的appid和appsecret
        $wx = new Plugin();
        $config = $wx -> wxConfig();

        // 请求微信，获取 accessToken
        $url = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid={$config['appid']}&secret={$config['appsecret']}";
        $info = sendCURL($url);
        $temp = json_decode($info, true);//对json数据解码

        // 存入缓存
        Cache::set('accessToken', $temp['access_token'], 7000);

        return $temp['access_token'];
    }

    
    /**
     * 生成小程序二维码
     * @param $orderSn   邀请码
     * @param $uId          用户Id
     * @param string $page  页面路径
     * @return string       返回文件存储路径
     */
    public function qCode($orderSn, $uId, $page = 'pages/purchase/purchase'){
      
      	$order = new Morder();
      
      	$user = $order->where('order_sn', $orderSn)->field('id,order_count')->find();
      	$sub = trim(strrchr($user['order_count'], '.'), '.');
        $par = $user['id'].'and'.(int)$user['order_count'].'dot'.$sub;
      	
        // 获取accessToken
        $accessToken = $this -> getAccessToken();
      
        // 获取二维码的二进制流
        $url = "https://api.weixin.qq.com/wxa/getwxacodeunlimit?access_token=$accessToken";
        $data = [
            'scene' => $par,
            'page' => $page,
        ];
      	
        $info = sendCURL($url, json_encode($data));
      
        // 保存为图片
        $route = env('root_path') . 'public/';
        $imgName = 'qcode' . DIRECTORY_SEPARATOR . $uId . uniqid() . '.png';
        $filename = $route . $imgName;
        file_put_contents($filename, $info);

        return $imgName;
    }
  
}