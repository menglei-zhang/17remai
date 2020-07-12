<?php
    namespace app\index\controller;
	use think\Controller;
	use app\index\controller\GetMemberDetail;
    class GetDetil
    {
    	// 客户详情
    	public function getMemberDetail(){
            // $accesstoken = session('WmToken');
            $accesstoken = cache('WmToken');
            $url = "https://dopen.weimob.com/api/1_0/mc/member/getMemberDetail?accesstoken=".$accesstoken;
            $data = [
                "phone" => "13626719992"
            ];
		    // 生成包头
		    $header = array("Content-Type:application/json;charset=utf-8");
		    // 发送请求
		    $res = json_decode(curl_post($url,json_encode($data),$header,1),true);
		    return json($res);
		    // if($res['code']['errcode'] == 0){
			   // $arr = [];
	     //       $arr['currentAmount'] = number_format($res['data']['currentAmount']/100,2);
			   // $arr['wid'] = $res['data']['wid'];
      //      	return json(echoArr(1, '请求成功',$arr));
		    // }else{
      //      	return json(echoArr(0, '没有会员卡'));
		    // }
    	}
    	public function getList(){
    		
            $accesstoken = cache('WmToken');
            $url = "https://dopen.weimob.com/api/1_0/mc/member/getMemberAmountLogsForBackend?accesstoken=".$accesstoken;
            $data = [
                "wid" => "1069376392"
            ];
             $header = array("Content-Type:application/json;charset=utf-8");
		    // 发送请求
		    $res = json_decode(curl_post($url,json_encode($data),$header,1),true);
		    return json($res);
    	}
    	public function getDetil()
    	{
    		$GetMemberDetail = new GetMemberDetail();
    		$res = $GetMemberDetail -> getMemberDetail(13626719992);
    		// $res = json_decode($res,true);
    		return $res['name'];
		}
		public function addMemberPointAmount(){
			$accesstoken = cache('WmToken');
			return $accesstoken;
		}
    }
?>