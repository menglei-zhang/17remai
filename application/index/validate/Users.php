<?php
/**
 * Created by PhpStorm.
 * User: Rain
 * Date: 2018/12/4
 * Time: 17:09
 */

namespace app\index\validate;

use think\Validate;

class Users extends Validate
{
    protected $rule = [
        'id' => 'require',
        'name|姓名' => 'require|max:20',
        'user_info|用户信息' => 'require',
        'iv|微信配置' => 'require',
        'encrypted_data|微信配置' => 'require',
        'signature|微信配置' => 'require',
        'code' => 'require',
        'openid' => 'require',
        'session_key' => 'require',
    ];

    protected $message = [
        'id.require' => '网络似乎有点延迟',
        'code.require' => '微信临时code不能为空',
        'openid.require' => '微信openId获取不能为空',
        'session_key.require' => '微信session_key获取不能为空',
    ];

    protected $scene = [
        'info' => ['name', 'id'],
        'auth' => ['openid', 'session_key'],
        'certification' => ['name', 'IDcard', 'bankCard', 'bankAccount', 'telephone'],
        'store' => ['storeId'],
        'userInfo' => ['userInfo', 'iv', 'encryptedData', 'signature']
    ];
}