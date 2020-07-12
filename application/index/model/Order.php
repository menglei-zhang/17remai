<?php
/**
 * Created by PhpStorm.
 * User: wzy12
 * Date: 2018/10/16
 * Time: 17:13
 */

namespace app\index\model;

use think\Model;
use app\index\model\Bread;
use app\index\model\Record;
use app\index\model\OrderGoods;

class Order extends Model
{
	public function buy($where)
	{	
		$Bread = new Bread();
		$OrderGoods = new OrderGoods();
		$this -> startTrans();
		$OrderGoods -> startTrans();

		$breadAll = $Bread->where(['status'=>0, 'user_id'=>0, 'activate'=>0])->select();
		$count = count($breadAll);

		// 验证商品数量是否充足
		if($where['gNum'] > $count){
			return echoArr(500, '商品数量不足');
		}

		foreach($breadAll as $key => $val){
          	$rep = model('Users')->where('id', $where['uid'])->where('hd_status', 1)->find();
          	if(empty($rep)){
				$data['order_count'] = $val['money'] * $where['gNum'] / 10;
				$temp['goods_price'] = $val['money'] / 10;
              	$data['status'] = 1;
              	if($where['gNum'] > 1){
                	return echoArr(500, '活动期间只有一次一元购买十元面包券的机会');
                }
            }else{
				$data['order_count'] = $val['money'] * $where['gNum'];
				$temp['goods_price'] = $val['money'];
            }
			$data['order_sn'] = $this->getOrderNo();
			$data['user_id'] = $where['uid'];
			$temp['goods_name'] = $val['name'];
			$temp['goods_num'] = $where['gNum'];
			$data['add_time'] = time();
		}
		
		// 生成订单
		$result = $this->allowField(true)->isUpdate(false)->save($data);
		if(false === $result){
			$this -> rollback();
			$OrderGoods -> rollback();
			return echoArr(500, '购买失败');
		}
      
		// 生成订单商品
		$temp['order_id'] = $this->id;
		$res = $OrderGoods->allowField(true)->isUpdate(false)->save($temp);
		if(false === $res){
			$this -> rollback();
			$OrderGoods -> rollback();
			return echoArr(500, '购买失败');
		}

		$this -> commit();
		$OrderGoods -> commit();
		return echoArr(200, '购买成功', ['oId' => $this->id, 'order_count' => $this->order_count]);
	}


	// 生成订单号
    public function getOrderNo()
    {
        while (true) {
            $order_no = date('YmdHis') . rand(100000, 999999);
            $exist_order_no = $this->where('order_sn', $order_no)->find();
            if (!$exist_order_no) break;
        }
        return $order_no;
    }
}
