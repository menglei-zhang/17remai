<?php
/**
 * Created by PhpStorm.
 * User: wzy12
 * Date: 2018/10/16
 * Time: 17:13
 */

namespace app\index\model;

use think\Model;

class Record extends Model
{
	public function resList($data)
	{
		$data = $this->where('user_id', $data['uId'])->field('trade_type,user_money,budget,add_time')->select();
		
      	if(!$data){
        	return echoArr(500, '暂无记录');
        }
      
		foreach($data as $key =>$val){
			$data[$key]['add_time'] = date('Y-m-d H:i:s', $val['add_time']);
		}

		return echoArr(200, '请求成功', $data);
	}
}