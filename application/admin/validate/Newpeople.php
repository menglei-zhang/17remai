<?php
/**
 * Created by PhpStorm.
 * User: wzy12
 * Date: 2018/10/29
 * Time: 16:10
 */

namespace app\admin\validate;

use think\Validate;

class Newpeople extends Validate
{
    protected $rule = [
        'money|新人券金额' => 'require',
        '__token__' => 'token|require'
    ];

    protected $message = [
        '__token__.require'  =>  '请不要进行非法操作',
    ];

    protected $scene = [
        'status' => ['status' => 'require|number'],
        'del' => ['__token__'],
        'form' => ['money', '__token__']
    ];
}