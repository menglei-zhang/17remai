<?php
/**
 * Created by PhpStorm.
 * User: wzy12
 * Date: 2018/10/16
 * Time: 17:13
 */

namespace app\index\model;

use think\Model;

class Banner extends Model
{
	public function resList()
	{
		$list = $this->where('status', 1)->select();
      	
		foreach($list as $key => $val){
			$list[$key]['banner'] = request() -> domain() .'/uploads/'.$val['banner'];
		}
		
		return $list;
	}
}