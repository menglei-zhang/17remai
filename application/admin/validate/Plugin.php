<?php
/**
 * Created by PhpStorm.
 * User: wzy12
 * Date: 2018/10/20
 * Time: 12:26
 */

namespace app\admin\validate;


use think\Validate;

class Plugin extends Validate
{
    protected $rule = [
        '__token__' => 'token|require'
    ];

    protected $message = [
        '__token__.require'  =>  '请不要进行非法操作',
        'status.require' => '请不要进行非法操作'
    ];

    protected $scene = [
        'status' => ['__token__', 'status' => 'require|number'],
        'weixi' => ['__token__', 'appid' => 'require|number', 'mchid' => 'require|number', 'key' => 'require|number', 'appsecret' => 'require|number'],
    ];
}