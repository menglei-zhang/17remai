<?php
/**
 * Created by PhpStorm.
 * User: wzy12
 * Date: 2018/9/6
 * Time: 16:44
 */

namespace app\admin\controller;

use think\Db;

class Index extends Base
{
    public function index(){
      	// 后台页面名称
        $home_name = model('Config')->where('key', 'admin_home_name')->value('value');
		$this -> assign('home_name', $home_name);

		// 栏目菜单
        $menu = config('menu.');
        if(!in_array(1, $this->userRole)){
            foreach($menu as $k => $v){
                if(!isset($v['child'])){
                    unset($menu[$k]);
                }
                foreach($v['child'] as $l => $w){
                    if(isset($w['child'])){
                        foreach($w['child'] as $m => $x){
                            $rule=strtolower($x['op'].'@'.$x['act']);
                            if(!in_array($rule, $this->userRules)){
                                unset($menu[$k]['child'][$l]['child'][$m]);
                            }
                        }
                        if(empty($menu[$k]['child'][$l]['child'])){
                            unset($menu[$k]['child'][$l]);
                        }
                    }else{
                        $rule=strtolower($w['op'].'@'.$w['act']);
                        if(!in_array($rule, $this->userRules)){
                            unset($menu[$k]['child'][$l]);
                        }
                    }
                }
                if(empty($menu[$k]['child'])){
                    unset($menu[$k]);
                }
            }
        }
        $this -> assign('menu', $menu);

        return $this -> fetch();
    }

    public function welcome(){
        // 获取当前域名
        $server['domain'] = $this -> request -> domain();

        // 获取当前系统
        $server['os'] = php_uname();

        // 获取服务器地址
        $server['ip'] = input('server.SERVER_ADDR');

        // 获取服务器端口
        $server['port'] = input('server.SERVER_PORT');

        // 获取当前环境
        $server['software'] = input('server.SERVER_SOFTWARE');

        // 获取php当前版本
        $server['php'] = PHP_VERSION;

        // 获取php当前运行方式
        $server['sapi'] = php_sapi_name();

        // 上传文件最大限制
        $server['max_size'] = get_cfg_var("upload_max_filesize") ? get_cfg_var("upload_max_filesize") : "不允许";

        // 执行时间限制
        $server['max_time'] = get_cfg_var("max_execution_time")."秒 ";

        // 数据库版本
        $version = Db::query("select version() as ver");
        $server['mysql'] = $version[0]['ver'];

        // 商品总数
        $count['goods'] = model('Goods') -> count();

        // 用户总数
        $count['admin'] = model('AdminUser') -> count();

        // 会员总数
        $count['user'] = model('Users') -> count();

        // 插件总数
        $count['plugin'] = model('Plugin') -> count();

        $this -> assign('count', $count);
        $this -> assign('server', $server);
        return $this -> fetch();
    }
}
