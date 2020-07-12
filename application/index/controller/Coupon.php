<?php
/**
 * Created by PhpStorm.
 * User: wzy12
 * Date: 2018/9/18
 * Time: 11:11
 */  
namespace app\index\controller;

class Coupon extends Base
{
	/**
	 * @return 优惠券列表
	 */
	public function index()
	{
      	$data['uId'] = $this->userId;
		$result = model('Coupon')->resList($data);
		
        return json($result);
	}


	/**
	 * @return 优惠券兑换
	 */
	public function convert()
	{
		$data = input('post.');
		$data['uId'] = $this->userId;
		$result = model('Coupon')->addCoupon($data);
		
        return json($result);
	}

}