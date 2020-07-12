<?php
/**
 * Created by PhpStorm.
 * User: wzy12
 * Date: 2018/10/16
 * Time: 17:13
 */

namespace app\index\model;

use think\Model;
use app\index\model\GoodsImg;

class Goods extends Model
{
    public function getGoodsAll()
    {
        $data = $this->field('id,goods_name,shop_price,original_img')->select();
      
        foreach($data as $k => $v){
            $data[$k]['original_img'] = request()->domain() . '/uploads/'.$v['original_img'];
          	$data[$k]['shop_price'] = (float)substr($v['shop_price'], 0, -1);
        }

        return $data;
    }

}
   