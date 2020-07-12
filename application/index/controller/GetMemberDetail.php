<?php
    namespace app\index\controller;
	use think\Controller;
    class GetMemberDetail
    {
    	// 客户详情
    	public function getMemberDetail($phone){
            $accesstoken = cache('WmToken');
            $url = "https://dopen.weimob.com/api/1_0/mc/member/getMemberDetail?accesstoken=".$accesstoken;
            $data = [
                "phone" => $phone
            ];
		    // 生成包头
		    $header = array("Content-Type:application/json;charset=utf-8");
		    // 发送请求
		    $res = json_decode(curl_post($url,json_encode($data),$header,1),true);
		    if($res['code']['errcode'] == 0){
			    $arr = [];
	            $arr['currentAmount'] = number_format($res['data']['currentAmount']/100,2);
			    $arr['wid'] = $res['data']['wid'];
			    $arr['name'] = $res['data']['name'];
            	return $arr;
		    }else{
            	return json(echoArr(0, '没有会员卡'));
		    }
    	}
    }
?>