<?php
/**
 * Created by PhpStorm.
 * User: wzy12
 * Date: 2018/9/18
 * Time: 13:55
 */

namespace app\admin\validate;


use think\Validate;

class ComponentPay extends Validate
{
    protected $rule = [
        'appid'  =>  'require',
        'mchid'  =>  'require',
        '__token__' => 'token|require'
    ];

    protected $scene = [
        'status' => ['__token__', 'status' => 'require|number'],
        'alipay' => ['__token__', 'appid', 'mchid|商户私钥', 'appsecret|支付宝公钥' => 'require'],
        'wxpay' => ['__token__', 'appid', 'mchid|商户号', 'key|商户支付密钥' => 'require'],
    ];
}