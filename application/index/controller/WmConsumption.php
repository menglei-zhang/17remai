<?php
    namespace app\index\controller;
    use think\Controller;
	use app\index\model\WeimengCard;
	use think\Db;
    class WmConsumption extends Controller
    {
        public function WmConsumption(){
            $accesstoken = cache('WmToken');
            $url = "https://dopen.weimob.com/api/1_0/mc/member/useMemberPointAmountOffLine?accesstoken=".$accesstoken;
            $data = [
				"amount" => $amount,
				"channelType" => 946,
				"reason" => "线下消费",
				"attachId" => $attachId,
				"requestId" => "17remai".time().$attachId,
				"wid" => $wid
                
            ];
		    // 生成包头
		    $header = array("Content-Type:application/json;charset=utf-8");
		    // 发送请求
		    $res = json_decode(curl_post($url,json_encode($data),$header,1),true);
		    if($res['code']['errcode'] == 0){
		  //  	$data = [
				// 	'username' => 'zhang',
				// 	'mobile' => 12345678901,
				// 	'wid' => 123123123,
				// 	'desc' => '测试数据3',
				// 	'use_money' => 500,
				// 	'balance_money' => 1000,
				// 	'add_time' => 1591880481
				// ];
				// b::name('weimeng_card')->insert($data);
		    	
		    	
            	return json(echoArr(1, '支付成功'));
		    }else{
		    	return json(echoArr(0, '支付失败',$res['code']['errmsg']));
		    }
        }
    }

