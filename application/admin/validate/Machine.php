<?php
/**
 * Created by PhpStorm.
 * User: wzy12
 * Date: 2018/10/29
 * Time: 16:10
 */

namespace app\admin\validate;

use think\Validate;

class Machine extends Validate
{
    protected $rule = [
        'bread_name|面包机名称'  =>  'require',
        'address|详细地址' => 'require',
        '__token__' => 'token|require'
    ];

    protected $message = [
        '__token__.require'  =>  '请不要进行非法操作',
    ];

    protected $scene = [
        'status' => ['status' => 'require|number'],
        'del' => ['__token__'],
        'form' => ['bread_name', 'province', 'city', 'district', 'address', '__token__']
    ];
}