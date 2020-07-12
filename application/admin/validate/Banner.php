<?php
/**
 * Created by PhpStorm.
 * User: wzy12
 * Date: 2018/10/29
 * Time: 16:10
 */

 
namespace app\admin\validate;

use think\Validate;

class Banner extends Validate
{
    protected $rule = [
        'banner|轮播图'  =>  'require',
        'banner_url|跳转链接' => 'require',
        '__token__' => 'token|require'
    ];

    protected $message = [
        '__token__.require'  =>  '请不要进行非法操作',
    ];

    protected $scene = [
        'status' => ['status' => 'require|number'],
        'del' => ['__token__'],
        'form' => ['banner', 'banner_url', '__token__']
    ];
}