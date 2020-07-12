<?php
/**
 * Created by PhpStorm.
 * User: wzy12
 * Date: 2018/9/5
 * Time: 17:10
 */

namespace app\admin\controller;

use think\Controller;

class Base extends Controller
{
    public function initialize(){
      	// 标题
      	$title = model('Config') -> where('key', 'admin_home_title') -> value('value');
      	$this -> assign('title', $title);

        // 判断是否登录
        $user = session('user');

        // 获取当前模块和控制器和方法
        $module = $this -> request -> module();
        $controller = $this -> request -> controller();
        $action = $this -> request -> action();

        // 菜单导航
        $nav = config('navigate.');
        $navigate = [];
        foreach($nav as $k => $v){
            $round = strtolower($module) . '/' . strtolower($controller);
            if($k == $round){
                $navigate[] = $v['name'];
                foreach($v['action'] as $key => $val){
                    if(strtolower($action) == strtolower($key)){
                        $navigate[] = $val;
                    }
                }
            }
        }
        $this -> assign('navigate', $navigate);

        // 存入操作记录日志
        $white_log = ['admin/log', 'admin/login', 'admin/loginOut', 'index/index', 'index/welcome'];
        $is_curr = strtolower($controller) . '/' . strtolower($action);
        if(!in_array($is_curr, $white_log)){
            $this -> log_in($navigate);
        }

        // 是否允许进入登录页面
        if(!$user && $action != 'login'){
            return $this -> redirect('admin/login');
        }else if($action == 'login' && $user){
            return $this -> redirect('index/index');
        }

        if($user){
            // 当前用户状态
            $status = model('AdminUser') -> where('id', $user['id']) -> value('status');
            if($status == 0){
                session(null);
                return $this -> error('账户被禁用', url('admin/login'));
            }

            // 用户拥有的角色
            $user_role = model('AdminUser') -> resFind($user['id']);
            $this->userRole=$user_role['role_id'];
            // 超级管理员，跳过权限判断
            if(!in_array(1, $user_role['role_id'])){
                // 查询未禁用的角色
                $roleList = model('AdminRole') -> whereIn('id', $user_role['role_id']) -> where('status', 1) -> select();

                // 角色拥有的权限
                $rule = [];
                foreach($roleList as $k => $v){
                    $roleList[$k]['controller'] = $v -> rules;

                    $temp = [];
                    foreach($roleList[$k]['controller'] as $val){
                        $test = explode('@', $val -> controller);
                        $test[0] = strtolower($test[0]);
                        $test[1] = strtolower($test[1]);
                        $test = implode('@', $test);
                        $temp[] = $test;
                    }

                    $rule[] = $temp;
                }
                $temp = [];
                foreach($rule as $k => $v){
                    if($temp){
                        $temp = array_merge($temp, $v);
                    }else{
                        $temp = $v;
                    }
                }
                $user['rule'] = array_unique($temp);
                $this->userRules=$user['rule'];

                $user_rule = strtolower($controller) . '@' . strtolower($action);
                if(!in_array($user_rule, $user['rule'])){
                    return $this -> error('权限不足', url("$controller/$action"));
                }
            }
        }
    }
    

    // 上传图片
    public function upload($file){
        dump($file);exit;
        $info = $file->validate(['size'=>2000000,'ext'=>'jpg,png,gif'])->move(env('root_path') . 'public' . DIRECTORY_SEPARATOR . 'uploads');
        if($info){
            // 输出 20160820/42a79759f284b767dfcb2a0197904287.jpg
            return echoArr(1, '上传成功', ['img' => addslashes($info->getSaveName())]);
        }else{
            // 上传失败获取错误信息
            return echoArr(0, $file->getError());
        }
    }

    // 日志记录
    public function log_in($navigate){
        // 开启日志
        ini_set('error_log',env('runtime_path') . 'log/login.log');
        $ip = $this -> request -> ip();
        $temp = $this -> log_cat();
        $id = $temp ? $temp[count($temp) -1]['id'] + 1 : 1;

        // 存入日志
        $temp = json_encode(['id' => $id, 'username' => session('user.username'), 'ip' => $ip, 'control' => $navigate, 'time' => date('Y-m-d H:i:s')]);
        error_log($temp);
    }

    // 读取日志
    public function log_cat(){
        // 读取日志内容
        $temp = [];
        $log = fopen(env('runtime_path') . 'log/login.log', 'r');
        $isSize = filesize(env('runtime_path') . 'log/login.log');
        if($isSize == 0){
            fclose($log);
            return $temp;
        }
        $str = fread($log,filesize(env('runtime_path') . 'log/login.log'));
        fclose($log);
        $temp = explode("\n", trim($str));
        foreach($temp as $k => $v){
            $temp[$k] = json_decode(trim(substr($v, strpos($v, '{'))), true);
        }

        return $temp;
    }
}
