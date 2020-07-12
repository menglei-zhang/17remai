<?php
/**
 * Created by PhpStorm.
 * User: wzy12
 * Date: 2018/9/26
 * Time: 18:06
 */

namespace app\admin\validate;

use think\Validate;

class Goods extends Validate
{
    protected $rule = [
        'id'  =>  'require|number',
        '__token__' => 'token|require',
        'goods_name|商品名称' => 'require',
        'shop_price|商品售价' => 'require',
        'original_img|商品主图' => 'require',
    ];

    protected $message = [
        '__token__.require'  =>  '请不要进行非法操作',
        'original_img.require' => '请上传商品主图'
    ];

    protected $scene = [
        'del' => ['__token__'],
        'form' => ['__token__', 'original_img', 'goods_name','shop_price'],
    ];
}