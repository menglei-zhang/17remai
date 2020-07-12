<?php
/**
 * Created by PhpStorm.
 * User: wzy12
 * Date: 2018/10/16
 * Time: 17:13
 */

namespace app\index\model;

use think\Model;

class Bread extends Model
{
	public function resList($data)
	{
		if($data['code'] == 0){
			$result = $this->where(['status'=>0, 'user_id'=>$data['uId']])->field('id,name,code,money,start_time,end_time,status')->order('id DESC')->select();
			foreach($result as $key => $val){
				$result[$key]['start_time'] = date('Y-m-d', $val['start_time']);
				$result[$key]['end_time'] = date('Y-m-d', $val['end_time']);
      			$result[$key]['money'] = (float)substr($val['money'], 0, -1);
			}
		}else{
			$result = $this->where(['status'=>1, 'user_id'=>$data['uId']])->field('id,name,code,money,start_time,end_time,status')->order('id DESC')->select();
			foreach($result as $key => $val){
				$result[$key]['start_time'] = date('Y-m-d', $val['start_time']);
				$result[$key]['end_time'] = date('Y-m-d', $val['end_time']);
      			$result[$key]['money'] = (float)substr($val['money'], 0, -1);
			}
		}

		return echoArr(200, '请求成功', $result);
	}


	public function getBread()
	{
		$data = $this->field('name,money,desc')->order('id desc')->find();
		$data['money'] = (float)substr($data['money'], 0, -1);
      	
		return $data;
	}
  
  
	public function getList($data)
	{
		$data = $this->where('code', $data['coupon'])->field('status,money,activate')->find();
		$list = [
          'status' => $data['status'],
          'couponPrice' => $data['money'],
          'activate' => $data['activate']
        ];
      
      	if($list['activate'] == 1){
        	return echoArr(500, '面包券已在使用中');
        }
      
      	$list['activate'] = 1;
		$outcome = $data->allowField(true)->isUpdate(true)->save($list);
      
		return echoArr(200, '请求成功', $list);
	}
  
  	// 使用面包券
	public function useList($data)
	{
      	$this->startTrans();
		$data = $this->where('code', $data['coupon'])->find();
		
		// 验证面包券
		if(!$data){
			return echoArr(500, '面包券不存在');
		}
		
		// 验证面包券是否过期
		$expire = time();
		if($expire >= $data['end_time']){
			return echoArr(500, '面包券已过期');
		}
		
		// 验证面包券状态
		if($data['status'] == 1){
			return echoArr(500, '面包券已使用');
		}

      	if($data['activate'] == 0){
        	return echoArr(500, '面包券上报解锁失败');
        }
		
		// 使用面包券
		$temp['status'] = 1;
      	$temp['activate'] = 0;
		$outcome = $data->allowField(true)->isUpdate(true)->save($temp);
      	if(false === $outcome){
        	$this -> rollback();
          	return echoArr(500, '使用不成功');
        }
		$this -> commit();
		return echoArr(200, '请求成功', $data);
	}
}