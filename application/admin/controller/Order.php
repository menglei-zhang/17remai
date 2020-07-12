<?php
/**
 * Created by PhpStorm.
 * User: wzy12
 * Date: 2018/9/20
 * Time: 13:49
 */

namespace app\admin\controller;

class Order extends Base
{
	/**
	 * 订单列表
	 */
	public function order()
	{
		$order_sn = input();
		$data = model('Order')->resList($order_sn);
		unset($order_sn['page']);
		if(!$order_sn){
			$order_sn = null;
		}
      
		$this->assign('order_sn', $order_sn);
		$this->assign('list', $data['list']);
      	$this->assign('count', $data['count']);
		return $this->fetch();
	}


	/**
	 * 查看订单
	 */
	public function order_form()
	{
	    if($this->request->isAjax()){
	        $data = input('post.');
	        $result = model('Order')->operation($data);
	        if($result['code']){
	            return $this->success($result['msg'], url('order/order'), ['token' => $this->request->token()]);
	        }else{
	            return $this->error($result['msg'], ['token' => $this->request->token()]);
	        }
	    }else{
	        $id = input('id');

	        $res = model('Order')->resFind($id);
	        $req = model('OrderGoods')->where('order_id', $id)->field('goods_name,goods_price,goods_num,is_send')->find();
	        
	        $res['goods_name'] = $req['goods_name'];
	        $res['goods_price'] = $req['goods_price'];
	        $res['goods_num'] = $req['goods_num'];
          	$res['shipping_status'] = $req['is_send'];

			
	        $this->assign('id', $id);
	        $this->assign('res', $res);
	        return $this->fetch();
	    }
	}

}