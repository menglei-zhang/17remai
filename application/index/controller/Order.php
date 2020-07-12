<?php
/**
 * Created by PhpStorm.
 * User: wzy12
 * Date: 2018/9/18
 * Time: 11:11
 */  
namespace app\index\controller;
//use think\Controller;
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
use think\Db;

class Order extends Base
{
	/**
	 * @return 购买面包券
	 */
	public function index()
	{
		$data = input('post.');
		$data['uid'] = $this->userId;
      	
		$result = model('Order')->buy($data);

		return json($result);
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
      * @return 订单支付页面
      */
     public function orderDetail()
     {
        $data = input('post.');
		$data['uId'] = $this->userId;
      
        $result = model('Order')->where('id', $data['oId'])->field('order_count,pay_status')->find();
        $res = model('OrderGoods')->where('order_id', $data['oId'])->field('goods_name')->find();

        $temp['order_count'] = $result['order_count'];
        $temp['goods_name'] = $res['goods_name'];
        $temp['user_money'] = model('Users')->where('id', $data['uId'])->sum('user_money');
        if($result['pay_status'] == 0){
            return json(echoArr(200, '请求成功', $temp));
        }else{
            return json(echoArr(500, '订单已支付'));
        }
     }
  
  
    /**
     * @return 订单支付
     */
    public function payment()
    {
        if($this -> request -> isPost()){
            $data = input('post.');
            $data['user_id'] = $this->userId;
            
        	$Users = new Users();
	        $Morder = new Morder();
	        $Record = new Record();
          	$breads = new Bread();
          
	        $Morder -> startTrans();
        	$Record -> startTrans();
        	$Users -> startTrans();
			$breads -> startTrans();
          
            if($data['code'] == 1){
            	// 使用余额支付
		        $user = $Users->where('id', $data['user_id'])->field('user_money')->find();
		        $order = $Morder->where('id', $data['orderId'])->find();
              	$oGoods = model('OrderGoods')->where('order_id', $order['id'])->field('goods_name,goods_num')->find();
                if($order['pay_status'] == 1){
                    return json(echoArr(500, '订单已支付'));
                }

		        if($user['user_money'] >= $order['order_count']){
		            $user['user_money'] -= $order['order_count'];
		            $res = $user->isUpdate(true)->save($user);
		            if(false === $res){
		                $Users -> rollback();
		                return json(echoArr(500, '购买失败'));
		            }                                
                 
                  	$pos = strpos($oGoods['goods_name'], '面包券');
                  	if($pos == true){
                        // 发放面包券
                        $bread = $breads->where(['status'=>0, 'user_id'=>0, 'activate'=>0])->limit($oGoods['goods_num'])->select();
                        foreach($bread as $key => $val){
                            $temp[] = [
                                'id' => $val['id'],
                                'user_id' => $order['user_id']
                            ];
                        }
                        $res = $breads->isUpdate(true)->saveAll($temp);
                        if(false === $res){
                            $Record -> rollback();
                            $Users -> rollback();
                            $breads -> rollback();
                            return json(echoArr(500, '发放失败'));
                        }
                      	$name = '面包券';
                      	$this->Record($data,$name,$order['order_count']);
                    	
                    }else{
                      	$this->Record($data,$oGoods['goods_name'],$order['order_count']);
                    }
	
                  	if($order['status'] == 1){
                    	$temp['status'] = 1;
                      	$like = [
                        	'id' => $data['user_id'],
                          	'hd_status' => 1
                        ];
                      	$res = $Users->isUpdate(true)->save($like);
                      	if(false === $res){
                          	$Users -> rollback();
                          	return echoArr(500, '更新状态失败');
                      	}
                    }
                  
		            // 修改支付状态
		            $temp = [
		                'id' => $order['id'],
		                'order_status' => 1,
		                'pay_status' => 1,
		                'pay_code' => 'wxCode',
		                'pay_name' => '小程序支付',
		                'pay_time' => time()
		            ];
			        $req = $Morder->isUpdate(true)->save($temp);
			        if(false === $req){
			            $Morder -> rollback();
                        $Record -> rollback();
                        $Users -> rollback();
                        $breads -> rollback();
			            return json(echoArr(500, '支付失败'));
			        }else{
			            $Morder -> commit();
		            	$Users -> commit();
		            	$Record -> commit();
                        $breads -> commit();
			            return json(echoArr(200, '支付成功', ['orderSn' => $order['order_sn'], 'payment' => $temp['pay_status']]));
			        }
		        }else{
		            return json(echoArr(500, '余额不足,请使用其他方式支付'));
		        }
		        
            }else{
	            $pay = new PayRecord();
	            $res = $pay -> pay($data);
            	return json($res);
            }
        }else{
            return json(echoArr(500, '打扰呢'));
        }
    }
    
  	public function Record($data, $GName, $Amount)
    {
        $Record = new Record();
        $Record -> startTrans();

        // 余额明细产生记录
        $info['trade_type'] = '购买'.$GName;
        $info['user_id'] = $data['user_id'];
        $info['user_money'] = $Amount;
        $info['budget'] = 1;
        $info['add_time'] = time();

        $res = $Record->isUpdate(false)->save($info);
        if(false === $res){
            $Record -> rollback();
            return json(echoArr(500, '记录失败'));
        }
        $Record -> commit();
    }
  
  	
  
  
      /**
       * @return 支付回调
       */
      public function notify()
      {
          $xml = file_get_contents("php://input");
          $arr = json_decode(json_encode(simplexml_load_string($xml, 'SimpleXMLElement', LIBXML_NOCDATA)), true);
		 
          $this -> BreadUser($arr['out_trade_no']);
        
          return "<xml><return_code><![CDATA[SUCCESS]]></return_code><return_msg><![CDATA[OK]]></return_msg></xml>";
          die;
      }
  
  	  /**
       * @return 微信支付回调后操作
       */
      public function BreadUser($orderSn)
      {
          $Users = new Users();
          $breads = new Bread();
	      $Record = new Record();
          $ordergoods = new OrderGoods();
          $orders = new Morder();
          $Users -> startTrans();
          $orders -> startTrans();
          $breads -> startTrans();
       	  $Record -> startTrans();
          $order = $orders->where('order_sn', $orderSn)->find();
          $oGoods = $ordergoods->where('order_id', $order['id'])->find();
          $bread = $breads->where(['status'=>0, 'user_id'=>0, 'activate'=>0])->limit($oGoods['goods_num'])->select();
        	
          foreach($bread as $key => $val){
              $temp[] = [
                  'id' => $val['id'],
                  'user_id' => $order['user_id']
              ];

          }
          $res = $breads->isUpdate(true)->saveAll($temp);
          if(false === $res){
			   $breads -> rollback();
			   return json(echoArr(500, '发放失败'));
		  }	
        
        
          // 余额明细产生记录
          $info['user_id'] = $order['user_id'];
          $info['trade_type'] = '购买面包券';
          $info['user_money'] = $order['order_count'];
          $info['budget'] = 1;
          $info['add_time'] = time();

          $result = $Record->allowField(true)->isUpdate(false)->save($info);
          if(false === $result){
          	   $Record -> rollback();
               $breads -> rollback();
               return json(echoArr(500, '记录失败'));
          }
		
         if($order['status'] == 1){
         	$temp['status'] = 1;
            $like = [
                'id' => $order['user_id'],
                'hd_status' => 1
            ];
            $res = $Users->isUpdate(true)->save($like);
            if(false === $res){
                $Users -> rollback();
                return echoArr(500, '更新状态失败');
            }
         }
         // 修改支付状态
         $temp = [
               'id' => $order['id'],
               'order_status' => 1,
               'pay_status' => 1,
               'pay_code' => 'weixin',
               'pay_name' => '微信支付',
               'pay_time' => time()
         ];
         $req = $orders->isUpdate(true)->save($temp);
         if(false === $req){
          	   $orders -> rollback();
           	   $breads -> rollback();
           	   $Record -> rollback();
               return json(echoArr(500, '修改状态失败'));
         }
        		
         $Users -> commit();
		 $orders -> commit();
		 $Record -> commit();
         $breads -> commit();
         return $res;
      }

  
  	/**
     * 出货状态
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
    // 小程序获取订单信息
    public function goodsMes(){
    	 if($this -> request -> isPost()){
	    	$data = input('post.');
			$goods = explode("=",$data['data']);
	        $order = explode("and",$goods['1']);
	        $orderId = $order['0'];
	        $goodsPrice = str_replace('dot','.',$order['1']);
	        $orderGoods = new OrderGoods();
	        $res = model('OrderGoods')->where('order_id', $orderId)->field('goods_name,goods_price,goods_num')->select();
	        if(!$res){
	            return json(echoArr(500, '订单不存在'));
	        }
	        $arr = [];
	        $arr['msg']=$res;
	        $arr['count']=$goodsPrice;
	        $arr['orderId']=$orderId;
	        return json(echoArr(200, '请求成功',$arr));
    	 }else{
    	 	return json(echoArr(500,'打扰了'));
    	 }
	}
	
	 /**
     * 小程序支付
     */
    public function xcxPay()
    {
        if($this -> request -> isPost()){
            $data = input('post.');
            $data['user_id'] = $this->userId;
            $Users = new Users();
            $Morder = new Morder();
            $Record = new Record();
            $breads = new Bread();

            $Morder -> startTrans();
        	$Record -> startTrans();
        	$Users -> startTrans();
            $breads -> startTrans();
            // 使用余额支付
            $user = $Users->where('id',$data['user_id'])->field('user_money')->find();
            $order = $Morder->where('id',$data['orderId'])->find();
            $oGoods = model('OrderGoods')->where('order_id', $order['id'])->field('goods_name,goods_num')->find();
            if($order['pay_status'] == 1){
                return json(echoArr(500, '订单已支付'));
            }
            if($user['user_money'] >= $order['order_count']){
            	// 小程序余额支付
                $user['user_money'] -= $order['order_count'];
                $user->isUpdate(true)->save($user);
                $Morder->where('id',$data['orderId'])->setField('user_id',$this->userId);
                // 修改支付状态
                $temp = [
                    'id' => $order['id'],
                    'order_status' => 1,
                    'pay_status' => 1,
                    'pay_code' => 'wxCode',
                    'pay_name' => '小程序余额支付',
                    'pay_time' => time()
                ];
                $req = $Morder->isUpdate(true)->save($temp);
                if(false === $req){
                    $Morder -> rollback();
                    $Record -> rollback();
                    $Users -> rollback();
                    $breads -> rollback();
                    return json(echoArr(500, '支付失败'));
                }else{
                    $Morder -> commit();
                    $Users -> commit();
                    $Record -> commit();
                    $breads -> commit();
                    return json(echoArr(200, '支付成功', ['orderSn' => $order['order_sn'], 'payment' => $temp['pay_status']]));
                }
            }else{
            	// 小程序余额不够支付 需要配合别的支付方式
                $user_money = $user['user_money'];
                $order['order_count'] = number_format(($order['order_count'] - $user_money),2);
                $user['user_money'] = 0;
                $res = $user->isUpdate(true)->save($user);
                $mobile = $Users->where('id',$data['user_id'])->field('mobile')->find();
                if(!$mobile['mobile']){
                    // 没有绑定手机号，用微信支付 （小程序+微信支付）
                    $data['rest'] = $order['order_count'];
                    $pay = new PayRecord();
                    $res = $pay -> yePay($data);
                    $res['xcx_count'] = $user_money;
                    $res['wm_count'] = number_format(0.00,2);
                    $res['wx_count'] = $order['order_count'];
                    $res['order_sn'] = $order['order_sn'];
                    return json($res);
                }else{
                    $accesstoken = cache('WmToken');
                    $url = "https://dopen.weimob.com/api/1_0/mc/member/getMemberDetail?accesstoken=".$accesstoken;
                    $phone = [
                        "phone" => $mobile['mobile']
                    ];
                    // 生成包头
                    $header = array("Content-Type:application/json;charset=utf-8");
                    // 发送请求
                    $res = json_decode(curl_post($url,json_encode($phone),$header,1),true);
                    if($res['code']['errcode'] == 0){
                        $wid = $res['data']['wid'];
                        $username = $res['data']['name'];
                        $currentAmount = number_format($res['data']['currentAmount']/100,2);
                        if($currentAmount == 0.00){
		                    // 微盟没有余额，用微信支付
		                    $data['rest'] = $order['order_count'];
		                    $pay = new PayRecord();
		                    $res = $pay -> yePay($data);
		                    $res['xcx_count'] = $user_money;
		                    $res['wm_count'] = number_format(0.00,2);
		                    $res['wx_count'] = $order['order_count'];
		                    $res['order_sn'] = $order['order_sn'];
		                    return json($res);
                        }
                        if($currentAmount < $order['order_count']){
                            $order['rest'] = $order['order_count'] - $currentAmount;
                            $sale_money = $currentAmount;
                        }else{
                            $order['rest'] = 0;
                            $sale_money = $order['order_count'];
                        }
                        $url = "https://dopen.weimob.com/api/1_0/mc/member/useMemberPointAmountOffLine?accesstoken=".$accesstoken;
                        $data = [
                            "amount" => $sale_money,
                            "channelType" => 946,
                            "reason" => "线下消费",
                            "attachId" => $order['order_sn'],
                            "requestId" => "17remai".time().$order['order_sn'],
                            "wid" => $wid
                        ];
                        // 生成包头
                        $header = array("Content-Type:application/json;charset=utf-8");
                        // 发送请求
                        $res = json_decode(curl_post($url,json_encode($data),$header,1),true);
                        return json($res);
                        if($res['code']['errcode'] == 0){
                        	$data = [
                                    'username' => $username,
                                    'mobile' => $mobile['mobile'],
                                    'wid' => $wid,
                                    'desc' => '',
                                    'use_money' => $order['order_count'],
                                    'balance_money' => $currentAmount - $order['order_count'],
                                    'add_time' => time(),
                                    'order_sn' => $order['order_sn'],
                                    'status' => "已支付"
                        		];
                        	Db::name('weimeng_card')->insert($data);
                            if($order['rest'] == 0){
                                // 修改支付状态
                                if($user_money > 0){
                                    $pay_code = "wxCode + wmCode";
                                    $pay_name = "小程序余额支付 + 微盟会员卡支付";
                                }else{
                                    $pay_code = "wmCode";
                                    $pay_name = "微盟会员卡支付";
                                }
                                $temp = [
                                    'id' => $order['id'],
                                    'order_status' => 1,
                                    'pay_status' => 1,
                                    'pay_code' => $pay_code,
                                    'pay_name' => $pay_name,
                                    'pay_time' => time()
                                ];
                                $req = $Morder->isUpdate(true)->save($temp);
                                if(false === $req){
                                    $Morder -> rollback();
                                    $Record -> rollback();
                                    $Users -> rollback();
                                    $breads -> rollback();
                                    return json(echoArr(500, '支付失败'));
                                }else{
                                    $Morder -> commit();
                                    $Users -> commit();
                                    $Record -> commit();
                                    $breads -> commit();
                                    return json(echoArr(200, '支付成功', ['orderSn' => $order['order_sn'], 'payment' => $temp['pay_status']]));
                                }
                            }else{
                                // 微盟会员卡余额不够，需要微信支付余额
                                $data['rest'] = $order['rest'];
                                $pay = new PayRecord();
	                            $res = $pay -> yePay($data);
			                    $res['xcx_count'] = $user_money;
			                    $res['wm_count'] = $currentAmount;
			                    $res['wx_count'] = $order['rest'];
			                    $res['order_sn'] = $order['order_sn'];
            	                return json($res);
                            }
                        }else{
                            return json(echoArr(0, '支付失败',$res['code']['errmsg']));
                        }
                    }else{
                        // 没有微盟，需要用微信支付余额
                        
                        $data['rest'] = $order['order_count'];
                        $pay = new PayRecord();
	                    $res = $pay -> yePay($data);
	                    $res['xcx_count'] = $user_money;
	                    $res['wm_count'] = number_format(0.00,2);
	                    $res['wx_count'] = $order['order_count'];
	                    $res['order_sn'] = $order['order_sn'];
            	        return json($res);
                    }
                }
            }
        }else{
            return json(echoArr(500,'打扰了'));
        }
    }
	
	 /**
     * 微信支付结果
     */
    public function del_order(){
        $Morder = new Morder();
    	if($this->request->isPost()){
    		$data = input('post.');
    		return json($data);
    		if($data['code'] == 500 && data['wm_count'] != 0.00 && data['xcx_count'] == 0.00){
    			// 支付失败，退微盟
    			// retrun "支付失败，退微盟";
    			$accesstoken = cache('WmToken');
				$url = "https://dopen.weimob.com/api/1_0/mc/member/addMemberPointAmount?accesstoken=".$accesstoken;            
				$data = [
	                "wid" => "1056922329",
					"addAmountReason" => "订单取消",
					"amount" => data['wm_count'],
					"channelType" => 947,
					"attachId" => time(),
					"requestId" => "remai17".time()
	            ];
	            $header = array("Content-Type:application/json;charset=utf-8");
			    // 发送请求
			    $res = json_decode(curl_post($url,json_encode($data),$header,1),true);
			    return json($res);
    		}else if($data['code'] == 500 && data['xcx_count'] != 0.00 && data['wm_count'] == 0.00){
    			// 支付失败，退小程序
    			// retrun "支付失败，退小程序";
    			$users = new Users();
    			$user = $Users->where('id',$data['user_id'])->field('user_money')->find();
                $user['user_money'] = $user['user_money']+data['wx_count'];
                $user->isUpdate(true)->save($user);
    		}else if($data['code'] == 500 && data['xcx_count'] != 0.00 && data['wm_count'] != 0.00){
    			// 支付失败，退微盟和小程序
    			// retrun "支付失败，退微盟和小程序";
    			$accesstoken = cache('WmToken');
				$url = "https://dopen.weimob.com/api/1_0/mc/member/addMemberPointAmount?accesstoken=".$accesstoken;            
				$data = [
	                "wid" => "1056922329",
					"addAmountReason" => "订单取消",
					"amount" => $data['wm_count'],
					"channelType" => 947,
					"attachId" => time(),
					"requestId" => "remai17".time()
	            ];
	            $header = array("Content-Type:application/json;charset=utf-8");
			    // 发送请求
			    $res = json_decode(curl_post($url,json_encode($data),$header,1),true);
			    $users = new Users();
    			$user = $Users->where('id',$data['user_id'])->field('user_money')->find();
                $user['user_money'] = $user['user_money']+data['wx_count'];
                $user->isUpdate(true)->save($user);
                Db::name('weimeng_card')->where('order_sn',data['order_sn'])->setField('status', '未支付');
    		}else if($data['code'] == 200 && data['wm_count'] == 0.00 && data['wx_count'] == 0.00){
    			// 微信支付
                // 修改支付状态
    			// retrun "微信支付";
                $temp = [
                    'order_sn' => data['order_id'],
                    'order_status' => 1,
                    'pay_status' => 1,
                    'pay_code' => 'wxCode',
                    'pay_name' => '微信支付',
                    'pay_time' => time()
                ];
                $req = $Morder->isUpdate(true)->save($temp);
    		}else if($data['code'] == 200 && data['wm_count'] != 0.00 && data['wx_count'] == 0.00){
    			// 微信 + 微盟
                // 修改支付状态
    			// retrun "微信 + 微盟支付";
                $temp = [
                    'order_sn' => data['order_id'],
                    'order_status' => 1,
                    'pay_status' => 1,
                    'pay_code' => 'wxCode + wmCode',
                    'pay_name' => '微信 + 微盟',
                    'pay_time' => time()
                ];
                $req = $Morder->isUpdate(true)->save($temp);
    		}else if($data['code'] == 200 && data['wm_count'] == 0.00 && data['wx_count'] != 0.00){
    			// 微信 + 小程序
                // 修改支付状态
    			// retrun "微信 + 小程序支付";
                $temp = [
                    'order_sn' => data['order_id'],
                    'order_status' => 1,
                    'pay_status' => 1,
                    'pay_code' => 'wxCode + xcxCode',
                    'pay_name' => '微信 + 小程序',
                    'pay_time' => time()
                ];
                $req = $Morder->isUpdate(true)->save($temp);
    		}else if($data['code'] == 200 && data['wm_count'] != 0.00 && data['wx_count'] != 0.00){
    			// 微信 + 微盟 + 小程序
                // 修改支付状态
    			// retrun "微信 + 微盟 + 小程序支付";
                $temp = [
                    'order_sn' => data['order_id'],
                    'order_status' => 1,
                    'pay_status' => 1,
                    'pay_code' => 'wxCode + wmCode + xcxCode',
                    'pay_name' => '微信 + 微盟 + 小程序',
                    'pay_time' => time()
                ];
                $req = $Morder->isUpdate(true)->save($temp);
    		}
    	}else{
    		return json(echoArr(500,"打扰了"));
    	}
    }
}