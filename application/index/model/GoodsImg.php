<?php
/**
 * Created by PhpStorm.
 * User: wzy12
 * Date: 2018/10/16
 * Time: 17:13
 */

namespace app\index\model;

use think\Model;

class GoodsImg extends Model
{
	public function getGoodsImg($data)
	{
		$data = $this->where('goods_id', $data['gid'])->field('img')->select();

      	foreach($data as $k => $v){
			$data[$k]['img'] = request() -> domain() .'/uploads/'.$v['img'];
		}
		return echoArr(200, '请求成功', $data);
	}
}