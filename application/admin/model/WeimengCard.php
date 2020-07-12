<?php
	namespace app\admin\model;
	use think\Model;
	class WeimengCard extends Model
	{
		public function reqList($where)
	    {
	    	$query = $this->where(1);
	      	// 筛选条件
			unset($where['page']);
			if($where){
				// 用户名查询
				if ($where['username']) {
					$query->where('username', $where['username']);
				}
	          	// 查询有效期时间
	          	if ($where['start']) {
	                $query->where('add_time', strtotime($where['start']));
	            }
	            if($where['end']){
	            	$query->whereTime('add_time', 'between', [strtotime($where['start']),strtotime($where['end'])]);
	            }
	        }
			$list['list'] = $query->order('id desc')->paginate(10, false, ['query' => request()->param()]);
			// $list = $this -> select();
	      	return $list;
	    }

	}
	

