<?php
	namespace app\index\controller;
	use app\index\model\OrderGoods;
	
	class GoodsMes
	{
		public function goodsMes($data){
			$goods = explode("=",$data);
	        $order = explode("and",$goods['1']);
	        $orderId = $order['0'];
	        $goodsPrice = str_replace('dot','.',$order['1']);
	        $orderGoods = new OrderGoods();
	        $res = model('OrderGoods')->where('order_id', $orderId)->field('goods_name,goods_price,goods_num')->select();
	        if(!$res){
	            return json(echoArr(500, '订单不存在'));
	        }
	        $arr = [];
	        $arr['msg']=$res;
	        $arr['count']=$goodsPrice;
	        return json($arr);
		}
	}