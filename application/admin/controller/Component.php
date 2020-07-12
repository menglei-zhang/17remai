<?php
/**
 * Created by PhpStorm.
 * User: wzy12
 * Date: 2018/9/18
 * Time: 11:11
 */

namespace app\admin\controller;
use app\component\controller\Ddysms;
use app\component\controller\Pay;
use app\component\controller\Shipping;

class Component extends Base
{
    /**
     * 插件
     */
    public function plugin(){

          // 插件名称
//        $config_name = [
//            ['name' => 'EBusinessID', 'label' => '用户ID', 'type' => '', 'status' => 1],
//            ['name' => 'AppKey', 'label' => 'API key', 'type' => '', 'status' => 1],
//        ];
//        dump(serialize($config_name));die;

          // 插件值
//        $config_value = [
//            'EBusinessID' => '1393875',
//            'AppKey' => 'c5ac5681-a1bd-45d6-8b44-8a05fe53bf1f',
//        ];
//
//        dump(serialize($config_value));die;

        // 调用微信示例
        //$this -> wxpay();die;

        // 调用支付宝示例
//        $this -> alipay();die;

        // 调用短信示例
        //$sms = $this -> alisms();
        //dump($sms);die;

        // 查询物流信息示例
        // $kdniao = $this -> kdniao();
        // dump($kdniao);die;

        $type = input('type', '0');
        if(!$type){
            return $this -> error('非法请求', url('index/index'));
        }

        $list = model('Plugin') -> resList($type);

        $this -> assign('list', $list);
        return $this -> fetch();
    }

    public function plugin_form(){
        if($this -> request -> isAjax()){
            $data = input('post.');

            // 验证当前数据
            $res = $this -> validate($data, 'Plugin.weixi');
            if(true !== $res){
                return $this -> error($res, '', ['token' => $this -> request -> token()]);
            }

            // 提交
            $result = model('Plugin') -> operation($data);
            if(!$result['code']){
                return $this -> error($result['msg'], '', ['token' => $this -> request -> token()]);
            }else{
                return $this -> success($result['msg'], url('component/plugin', ['type' => $data['type']]), ['token' => $this -> request -> token()]);
            }
        }else{
            $id = input('id', 0);
            if($id){
                $res = model('Plugin') -> resFind($id);

                $this -> assign('id', $id);
                $this -> assign('res', $res);
                return $this -> fetch();
            }else{
                return $this -> error('非法请求', url('index/index'));
            }
        }
    }

    /**
     * 启用或禁用该插件
     */
    public function status(){
        if($this -> request -> isAjax()){
            $data = input('post.');

            $result = model('Plugin') -> editStatus($data);
            if(!$result['code']){
                return $this -> error($result['msg'], '', ['token' => $this -> request -> token()]);
            }else{
                return $this -> success($result['msg'], url('component/plugin'), ['token' => $this -> request -> token(), 'status' => $result['data']]);
            }
        }else{
            return $this -> error('非法请求', url('index/index'));
        }
    }

    /**
     * 调用支付宝支付示例
     */
    public function alipay(){
        $wxpay = new Pay();
        // 商品描述
        $wxpay -> body = 'sadffbds';
        // 订单号
        $wxpay -> out_trade_no = '23421234';
        // 订单金额
        $wxpay -> total_fee = 0.01;
        // 订单名称
        $wxpay -> subject = 'asfasf';
        $wxpay -> aliPay();
    }

    /**
     * 调用微信支付示例
     */
    public function wxpay(){
        $wxpay = new Pay();
        // 商品描述
        $wxpay -> body = '321412';
        // 订单号
        $wxpay -> out_trade_no = 'qwrqjwrnqrlkqn';
        // 订单金额
        $wxpay -> total_fee = 0.01;
        // 商品id
        $wxpay -> product_id = 214;
        $res = $wxpay -> wxpay();

        $url = url('component/pay/qcode');
        echo "<img src='$url?data=$res'>";
    }

    /**
     * 调用短信示例
     */
    public function alisms(){
        $sms = new Ddysms();
        $sms -> receive_phone = '15870659394';
        $res = $sms -> aliSms();
        return $res;
    }

    /**
     * 查看物流信息
     */
    public function kdniao(){
        $shipping = new Shipping();
        // 订单号
        $shipping -> OrderCode = '227270784399007347';
        // 快递编码
        $shipping -> ShipperCode = 'ZTO';
        // 物流单号
        $shipping -> LogisticCode = '73104307880116';
        $res = $shipping -> kdniao();
        return $res;
    }
}