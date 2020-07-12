<?php
/**
 * Created by PhpStorm.
 * User: wzy12
 * Date: 2018/10/10
 * Time: 16:04
 */

namespace app\admin\validate;


use think\Validate;

class Config extends Validate
{
    protected $rule = [
        '__token__' => 'token|require'
    ];

    protected $message = [
        '__token__.require'  =>  '请不要进行非法操作',
    ];
}