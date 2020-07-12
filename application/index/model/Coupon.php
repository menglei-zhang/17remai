<?php
/**
 * Created by PhpStorm.
 * User: wzy12
 * Date: 2018/10/16
 * Time: 17:13
 */

namespace app\index\model;

use think\Model;
use app\index\model\Users;
use app\index\model\Record;

class Coupon extends Model
{
	public function resList($data)
	{
		$data = $this->where('user_id', $data['uId'])->field('coupon,money,use_start_time,use_end_time,useStatus')->select();
      	
      	if(!$data){
        	return echoArr(500, '暂无优惠券');
        }

		foreach($data as $key =>$val){
			$data[$key]['use_start_time'] = date('Y-m-d', $val['use_start_time']);
			$data[$key]['use_end_time'] = date('Y-m-d', $val['use_end_time']);
      		$data[$key]['money'] = (float)substr($val['money'], 0, -1);
		}

		return echoArr(200, '请求成功', $data);
	}

	/**
	 * @return 兑换优惠券
	 */
	public function addCoupon($where)
	{
		$coupon = $this->where('coupon', $where['coupon'])->find();
		// 验证卡号
		if(!$coupon){
			return echoArr(500, '优惠券不存在');
		}

		// 验证卡号和密码
		$data = $this->where(['coupon'=>$where['coupon'], 'coupon_pass'=>$where['coupon_pass']])->find();
		if(!$data){
			return echoArr(500, '优惠券密码不正确');
		}

		// 验证优惠券激活状态
		if($data['status'] == 0){
			return echoArr(500, '优惠券未激活');
		}
      
      	// 验证优惠券使用状态
		if($data['useStatus'] == 1){
			return echoArr(500, '优惠券已使用');
		}

		// 验证优惠券是否过期
		$expire = time();
		if($expire >= $data['use_end_time']){
			return echoArr(500, '优惠券已过期');
		}
			
		// 兑换优惠券
		$user = new Users();
		$result = $user->where('id', $where['uId'])->find();

		$result['user_money'] += $data['money'];
		$res = $result->allowField(true)->isUpdate(true)->save($result);
      
		// 优惠券状态改变
		$data['useStatus'] = 1;
		$data['user_id'] = $where['uId'];
		$outcome = $data->allowField(true)->isUpdate(true)->save($data);

		// 余额明细产生记录
		$Record = new Record();
		$info['user_id'] = $where['uId'];
		$info['trade_type'] = '活动礼券';
		$info['user_money'] = $data['money'];
		$info['add_time'] = time();
		$req = $Record->allowField(true)->isUpdate(false)->save($info);
		
		if(false === $res){
			return echoArr(500, '兑换失败', $res->getError());
		}else{
			return echoArr(200, '兑换成功');
		}

	}
}