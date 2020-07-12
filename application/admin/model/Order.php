<?php
/**
 * Created by PhpStorm.
 * User: wzy12
 * Date: 2018/9/26
 * Time: 15:48
 */

namespace app\admin\model;

use think\Model;
use app\admin\validate\Order as Vali;

class Order extends Model
{
	/**
	 * 订单列表
	 */
	public function resList($order_sn)
	{
		$query = $this -> search($order_sn);
		$list = $query->order('id desc')->paginate(10, false, ['query'=>request()->param()]);
      
      	$query = $this -> search($order_sn);
      	$count = $query -> where('order_status', 1) -> sum('order_count');
      
      	$arr = [
        	'list' => $list,
          	'count' => $count
        ];

		return $arr;
	}
  
  	private function search($order_sn){
    	$query = $this->where(1);
		unset($order_sn['page']);
		if($order_sn){
			if($order_sn['order_sn']){
				// 查询订单号，模糊查询
				$query->where('order_sn', 'like', "%{$order_sn['order_sn']}%");
			}
          
          	if($order_sn['order_status'] || $order_sn['order_status'] === '0'){
				// 查询订单状态
				$query->where('order_status', $order_sn['order_status']);
			}
          
          	if($order_sn['start'] && $order_sn['end']){
				// 查询订单号，模糊查询
				$query->whereTime('add_time', [$order_sn['start'], $order_sn['end']]);
			}
		}
      
      	return $query;
    }


	public function resFind($id)
    {
        $res = $this -> find($id);

        return $res;
    }

}