<?php
/**
 * Created by PhpStorm.
 * User: wzy12
 * Date: 2018/10/16
 * Time: 17:13
 */

namespace app\index\model;
use think\Model;
class CouponList extends Model
{
     public  function resFind($uid)
     {
         $data = $this->alias('p')
                      ->join('coupon c','p.cid=c.id')
                      ->where('uid',$uid)
                      ->where('p.status','0,2')
                      ->select();
         var_dump($data->toArray());die;
         return $data;
     }
}

