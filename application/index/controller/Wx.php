<?php
/**
 * Created by PhpStorm.
 * User: Rain
 * Date: 2018/12/4
 * Time: 16:23
 */

namespace app\index\controller;

use app\index\model\Plugin;
use think\facade\Cache;

class Wx extends Base
{
    /**
     * 微信授权
     */
    public static function wxAuthorization($data){
        // 微信的appid和appsecret
        $wx = new Plugin();
        $config = $wx -> wxConfig();
        // 换取openId,session_key
        $url = "https://api.weixin.qq.com/sns/jscode2session?appid={$config['appid']}&secret={$config['appsecret']}&js_code={$data['code']}&grant_type=authorization_code";
        $info = sendCURL($url);
        $temp = json_decode($info, true);//对json数据解码
        return $temp;
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
     * @param $invitation   邀请码
     * @param $uId          用户Id
     * @param string $page  页面路径
     * @return string       返回文件存储路径
     */
    public function qCode($invitation, $uId, $page = 'pages/login/login'){
        // 获取accessToken
        $accessToken = $this -> getAccessToken();

        // 获取二维码的二进制流
        $url = "https://api.weixin.qq.com/wxa/getwxacodeunlimit?access_token=$accessToken";
        $data = [
            'scene' => $invitation,
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