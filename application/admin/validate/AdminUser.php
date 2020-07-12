<?php
/**
 * Created by PhpStorm.
 * User: wzy12
 * Date: 2018/9/7
 * Time: 10:51
 */

namespace app\admin\validate;

use think\Validate;

class AdminUser extends Validate
{
    protected $rule = [
        'username|用户名'  =>  'require|max:16',
        'email|邮箱' => 'require|email',
        '__token__' => 'token|require'
    ];

    protected $message = [
        '__token__.require'  =>  '请不要进行非法操作',
    ];

    protected $scene = [
        'status' => ['__token__', 'status' => 'require|number'],
        'login' => ['usernmae', 'password|密码' => 'require', '__token__'],
        'user' => ['password|密码' => 'require', '__token__'],
        'del' => ['__token__']
    ];
}