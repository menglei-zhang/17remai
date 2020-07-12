<?php

namespace app\index\controller;

use think\Request;
use app\index\model\Users;
use think\facade\Cache;

class Index extends Base
{
    /**
     * 微信登录
     */
    public function index()
    {
        if ($this->request->isPost()) {
            $data = input('post.');
            if (!$data) {
                return json(echoArr(500, '授权失败'));
            }
            $Wx = new Wx();
            $info = $Wx::wxAuthorization($data);
            if (!isset($info['errcode'])) {
                $user = new Users();
                $res = $user->form($info);

                return json($res);
            } else {
                return json(echoArr(500, '授权失败'));
            }
        } else {
            return json(echoArr(500, '登录失败'));
        }
    }

    /**
     *  完善用户信息
     */
    public function userInfo()
    {
        if($this->request->isPost()){
            $data = input('post.');
            $data['user_id'] = $this->userId;

            $user = new Users();
            $res = $user->editUserInfo($data);
            return json($res);
        } else {
            return json(echoArr(500, '登录失败'));
        }
    }
  
   /**
    *  绑定手机号
    */
    public function signIn()
    {
        if ($this->request->isPost()) {
            $data = input('post.');
            $token = $this->request->header('tokenAccess');
            if (empty($token)) {
                return json(echoArr(500, '授权失败'));
            }
            $openid = Cache::get($token);
            if(!empty($openid)){
                $user = new Users();
                $status = $user->upMobil($data['phone'],$openid);
                if(false !== $status){
                    $info['code'] = 200;
                    $info['msg'] = '成功绑定手机号';
                }else{
                    $info['code'] = 500;
                    $info['msg'] = '绑定手机号失败';
                }
                return json($info);
            }else{
                return json(echoArr(305, '没有该用户'));
            }
        } else {
            return json(echoArr(500, '非法请求'));
        }
    }


    /**
     * 新人红包
     */
    public function indexRedbag()
    {
        if($this->request->isPost()){
            $data = input('post.');
            $data['user_id'] = $this->userId;

            $user = new Users();
            $res = $user->redbag($data);
            return json($res);
        } else {
            return json(echoArr(500, '非法请求'));
        }
    }
}