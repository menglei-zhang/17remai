<?php
/**
 * Created by PhpStorm.
 * User: wzy12
 * Date: 2018/9/12
 * Time: 15:48
 */

namespace app\admin\validate;


use think\Validate;

class AdminRole extends Validate
{
    protected $rule = [
        'role_rule_id'  =>  'require',
        'describe|描述' =>  'require',
        'role_name|角色名' => 'require',
        '__token__' => 'token|require'
    ];

    protected $message = [
        '__token__.require'  =>  '请不要进行非法操作',
        'role_rule_id.require' => '请勾选权限',
        'status.require' => '请不要进行非法操作'
    ];

    protected $scene = [
        'status' => ['__token__', 'status' => 'require|number'],
        'del' => ['__token__'],
    ];
}