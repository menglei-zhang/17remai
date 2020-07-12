<?php
/**
 * Created by PhpStorm.
 * User: Rain
 * Date: 2018/12/3
 * Time: 11:45
 */

namespace app\index\controller;

use think\Controller;
use app\index\model\Users;
use think\facade\Cache;

class Base extends Controller
{
    public $userId = null;

    public function initialize()
    {
        // 获取头部token
        $token = $this->request->header('tokenAccess');

        // 获取接口路径
        $module = $this->request->module();
        $controller = $this->request->controller();
        $action = $this->request->action();
        $route = strtolower($module) . '/' . strtolower($controller) . '/' . strtolower($action);

        // Cache::set('762bb62002f99ffe279d63ac920afa6f', 'oDJls5fQH0VJMp-aU54h00x2B8OE', 21600);
        
        $while = ['index/index/index','index/index/userInfo','index/index/signIn', 'index/order/notify'];
        if(!in_array($route, $while)){
            if($token){
                // 通过token获取openId
                $openId = Cache::get($token);
                if(!$openId){
                    die(json_encode(echoArr(300, '对不起，打扰了')));
                }

                $user = new Users();
                $info = $user -> where('wx_open_id', $openId) -> field('id,mobile') -> find();

                // 判断是否有该用户
              	$while = ['index/personal/index', 'index/index/signin'];
              	if(!in_array($route, $while)){
                	if(!$info['id']){
                      die(json_encode(echoArr(300, '对不起')));
                  	}
                  	
                	$this->userId = $info['id'];
                }

                // 判断当前不是登录接口，判断是否登录
                // if($route != 'api/user/login'){
                //     if(!$info['mobile']){
                //         die(json_encode(echoArr(305, '用户未登录')));
                //     }
                // }
            }else{
                die(json_encode(echoArr(300, '获取tokenAccess失败')));
            }
        }
    }

}