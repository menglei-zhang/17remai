<?php
/**
 * Created by PhpStorm.
 * User: wzy12
 * Date: 2018/10/26
 * Time: 15:18
 */

namespace app\admin\validate;


use think\Validate;

class Comment extends Validate
{
    protected $rule = [
        'pid'  =>  'require',
        'content|内容' =>  'require',
    ];

    protected $message = [
        '__token__.require'  =>  '请不要进行非法操作',
        'id.require'   => '请不要进行非法操作',
    ];

    protected $scene = [
        'del' => ['__token__' => 'require|token'],
        'status' => ['id' => 'require|number'],
    ];
}