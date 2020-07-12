<?php
/**
 * Created by PhpStorm.
 * User: wzy12
 * Date: 2018/10/29
 * Time: 16:10
 */

namespace app\admin\validate;

use think\Validate;

class Bread extends Validate
{
    protected $rule = [
        'name|面包券名称'  =>  'require',
        'code|面包券券码'  =>  'require|number',
        'money|面包券金额' => 'require',
        'start_time|有效期开始时间' => 'require',
        'end_time|有效期结束时间' => 'require',
        '__token__' => 'token|require'
    ];

    protected $message = [
        '__token__.require'  =>  '请不要进行非法操作',
    ];

    protected $scene = [
        'status' => ['status' => 'require|number', 'activate' => 'require|number'],
        'del' => ['__token__'],
        'form' => ['name', 'code', 'money', '__token__']
    ];
}