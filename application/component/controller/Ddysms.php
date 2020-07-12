<?php
/**
 * Created by PhpStorm.
 * User: wzy12
 * Date: 2018/10/16
 * Time: 13:35
 */

namespace app\component\controller;

use think\Controller;
use aliyunDysms\apiDemo\SmsDemo;

class Ddysms extends Controller
{

    // 接收验证码的手机号
    public $receive_phone;
    /**
     * @return array|void
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function aliSms()
    {
        $demo = new SmsDemo();

        $res = model('Plugin') -> where('code', 'aliyun') -> field('config_value,status') -> find();
        $config = unserialize($res['config_value']);
        $config['receive_phone'] = $this -> receive_phone;

        // 验证码
        $config['smscode'] = (string)rand(100000,999999);
        session('code', $config['smscode']);
        session('on_time', time());

        if(!$res['status']){
            return echoArr(500, '短信验证未开启，请在插件功能开启该功能');
        }else{
            $response = $demo::sendSms($config);
			$data = ['code' => $config['smscode']];
            if($response -> Message == 'OK'){
                return echoArr(200, $response -> Message, $data);
            }else{
                return echoArr(500, $response -> Message, $data);
            }
        }
    }
}