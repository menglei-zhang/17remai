<?php
/**
 * Created by PhpStorm.
 * User: wzy12
 * Date: 2018/10/21
 * Time: 19:41
 */

namespace app\component\controller;


use think\Controller;
use KdApiSearch\Exp;

class Shipping extends Controller
{
    // 物流编码
    public $com;
    // 物流单号
    public $no;

    /**
     * 聚合查询物流
     */
    public function kdniao(){
        $res = model('Plugin') -> where('code', 'express') -> field('config_value,status') -> find();
        if($res['status'] == 0){
            return $this -> error('物流查询未开启，请在插件功能开启该功能');
        }
        $config = unserialize($res['config_value']);
      
        $exp = new Exp($config['AppKey']);
        $result = $exp->query($this -> com, $this -> no);

        if($result['error_code'] == 0){
            $list = $result['result']['list'];

            return echoArr(1, "操作成功", $list);
        }else{
            return echoArr(0, "获取失败，原因：".$result['reason']);
        }
    }
}