<?php
/**
 * Created by PhpStorm.
 * User: wzy12
 * Date: 2018/10/16
 * Time: 17:42
 */

namespace app\admin\validate;


use think\Validate;

class Users extends Validate
{
    protected $rule = [
        'mobile|手机号码'  =>  'require|number',
        'sex|性别' => 'require|number',
        '__token__' => 'token|require'
    ];

    protected $message = [
        '__token__.require'  =>  '请不要进行非法操作',
        'status.require' => '请不要进行非法操作',
    ];

    protected $scene = [
        'status' => ['__token__', 'status' => 'require|number'],
        'del' => ['__token__'],
    ];
}