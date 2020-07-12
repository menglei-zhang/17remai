<?php

namespace app\index\controller;

use think\Controller;
use think\Request;
use think\facade\Cache;
use app\index\model\Order;
use app\index\model\OrderGoods;
use app\index\model\Record;
use app\index\model\Coupon;
use app\index\model\Bread;
use app\index\model\Users;

class Myuser extends Base
{
	// 用户信息
    public function userInfo()
    {
        if ($this->request->isPost()) {
            $token = $this->request->header('tokenAccess');
            if(empty($token)){
                return json(echoArr(300, 'tokenAccess失效'));
            }
            $openId = Cache::get($token);
            $userModel = new Users();
            $data = $userModel->getUserFind($openId);
            return json($data);
        } else {
            return json(echoArr(500, '非法请求'));
        }
    }
  

    // 图片上传
    public function upImg()
    {
        $file = input('file.img');
        if($file){
            $info = $file->move('./weixin/');
            if($info){
                $file = $info->getSaveName();
                $ip = $_SERVER['REQUEST_SCHEME'] . '://' .$_SERVER['SERVER_NAME'];
                $temp['0'] = $ip.'/weixin/'.$file;
                $res = ['errCode' => 0, 'errMsg' => '图片上传成功', 'file' => $temp];
                return json($res);
            }
        }
    }


    // 查看余额
    public function balance()
    {
        $input['uId'] = $this->userId;
        
        // 用户余额
        $users = new Users();
        $user = $users->where('id', $input['uId'])->sum('user_money');
        
        $dataAll['user_money'] = $user;
    	return json($dataAll);
    }
    // 查看微盟会员卡余额
    public function getMemberDetail(){
        $input['uId'] = $this->userId;
        // 用户余额
        $users = new Users();
        $mobile = $users->where('id', $input['uId'])->value('mobile');
        if(!$mobile){
            return json(echoArr(-1, '没有绑定手机号'));
        }
        $accesstoken = cache('WmToken');
        $url = "https://dopen.weimob.com/api/1_0/mc/member/getMemberDetail?accesstoken=".$accesstoken;
        $data = [
            "phone" => $mobile
        ];
        // 生成包头
        $header = array("Content-Type:application/json;charset=utf-8");
        // 发送请求
        $res = json_decode(curl_post($url,json_encode($data),$header,1),true);
        if($res['code']['errcode'] == 0){
        	$wid = $res['data']['wid'];
            $currentAmount = number_format($res['data']['currentAmount']/100,2);
            return json(echoArr(1,'请求成功',$currentAmount));
        }else{
            return json(echoArr(0,'没有会员卡'));
        }
    }
}
