<?php
/**
 * Created by PhpStorm.
 * User: wzy12
 * Date: 2018/10/29
 * Time: 16:10
 */

namespace app\admin\validate;


use think\Validate;

class Coupon extends Validate
{
    protected $rule = [
        'money|优惠券金额' => 'require',
        'use_start_time|使用起始日期' => 'require',
        'use_end_time|使用结束日期' => 'require',
        '__token__' => 'token|require'
    ];

    protected $message = [
        '__token__.require'  =>  '请不要进行非法操作',
    ];

    protected $scene = [
        'status' => ['status' => 'require|number', 'useStatus' => 'require|number'],
        'del' => ['__token__'],
        'form' => ['money', 'use_start_time', 'use_end_time', '__token__']
    ];
}