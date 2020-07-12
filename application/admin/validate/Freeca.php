<?php
/**
 * Created by PhpStorm.
 * User: wzy12
 * Date: 2018/10/29
 * Time: 16:10
 */

namespace app\admin\validate;

use think\Validate;

class Freeca extends Validate
{
    protected $rule = [
        'code|福利卡号'  =>  'require|number',
        'money|福利卡金额' => 'require',
        '__token__' => 'token|require'
    ];

    protected $message = [
        '__token__.require'  =>  '请不要进行非法操作',
    ];

    protected $scene = [
        'status' => ['use_status' => 'require|number', 'add_status' => 'require|number'],
        'del' => ['__token__'],
        'form' => ['freeca_code', 'money', '__token__']
    ];
}