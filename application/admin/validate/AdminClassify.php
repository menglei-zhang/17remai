<?php
/**
 * Created by PhpStorm.
 * User: wzy12
 * Date: 2018/9/10
 * Time: 15:49
 */

namespace app\admin\validate;

use think\Validate;

class AdminClassify extends Validate
{
    protected $rule = [
        'classify_name|分类名称'  =>  'require|max:120',
        '__token__' => 'token|require'
    ];

    protected $message = [
        '__token__.require'  =>  '请不要进行非法操作',
    ];

    protected $scene = [
        'del'  =>  ['__token__'],
    ];
}