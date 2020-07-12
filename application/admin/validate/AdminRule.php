<?php
/**
 * Created by PhpStorm.
 * User: wzy12
 * Date: 2018/9/11
 * Time: 15:14
 */

namespace app\admin\validate;

use think\Validate;

class AdminRule extends Validate
{
    protected $rule = [
        'classify_id'  =>  'require',
        'controller_name' =>  'require',
        'action' => 'require',
        'rule_name|权限名称' => 'require',
        '__token__' => 'token|require'
    ];

    protected $message = [
        '__token__.require'  =>  '请不要进行非法操作',
        'classify_id.require'   => '请选择规则分类',
        'controller_name.require' =>  '请选择控制器',
        'action.require' =>  '请选择方法',
    ];

    protected $scene = [
        'del' => ['__token__'],
    ];
}