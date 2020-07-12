<?php
/**
 * Created by PhpStorm.
 * User: wzy12
 * Date: 2018/9/18
 * Time: 11:11
 */  
namespace app\index\controller;

use app\component\controller\Ddysms;

class Personal extends Base
{
    public function index()
    {
        if($this->request->isPost())
        {
            $phone = input('post.');
            $mobile = $phone['phone'];
            $sms = $this -> alisms($mobile);
            return json($sms);
        }else{
            return json(echoArr(500,'非法请求'));
        }
    }
    /**
     * 调用短信示例
     */
    public function alisms($mobile){
        $sms = new Ddysms();
        $sms -> receive_phone = $mobile;
        $res = $sms -> aliSms();
        return $res;
    }
   

}