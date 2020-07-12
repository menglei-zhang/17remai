<?php
/**
 * Created by PhpStorm.
 * User: Rain
 * Date: 2018/12/12
 * Time: 17:38
 */

namespace app\index\validate;


use think\Validate;

class PayRecord extends Validate
{
    protected $rule = [
        'orderId|订单id' => 'require|number',
    ];
}