<?php
/**
 * Created by PhpStorm.
 * User: wzy12
 * Date: 2018/10/16
 * Time: 17:13
 */

namespace app\index\model;

use think\Model;

class Machine extends Model
{
	public function resList($data, $decimal = 2)
	{
		if(isset($data['province']) && isset($data['search'])){
			$result = $this->where(['province'=>$data['province'], 'city'=>$data['city'], 'district'=>$data['district'], 'status'=>1])->where('bread_name', 'like', "%{$data['search']}%")->where('status', 1)->field('bread_name,lng,lat,address')->limit(20)->select()->toArray();
        }elseif(isset($data['search'])){
			$result = $this->where('bread_name', 'like', "%{$data['search']}%")->where('status', 1)->field('bread_name,lng,lat,address')->limit(20)->select()->toArray();
		}elseif(isset($data['province'])){
			$result = $this->where(['province'=>$data['province'], 'city'=>$data['city'], 'district'=>$data['district'], 'status'=>1])->field('bread_name,lng,lat,address')->limit(20)->select()->toArray();
		}else{
			$result = $this->where('status', 1)->field('bread_name,lng,lat,address')->limit(20)->select()->toArray();
		}
      
      
        if(empty($result)){
        	return echoArr(500, '暂无面包机');
        }

		foreach($result as $key => $val){
			$earth_radius = 6378.137; 				// 地球半径
	        $lng1 = (M_PI / 180) * $data['lng'];	// 起始经度
	        $lng2 = (M_PI / 180) * $val['lng'];		// 结束经度
	        $lat1 = (M_PI / 180) * $data['lat'];	// 起始纬度
	        $lat2 = (M_PI / 180) * $val['lat'];		// 结束纬度

	        $d = acos(sin($lat1) * sin($lat2) + cos($lat1) * cos($lat2) * cos($lng2 - $lng1)) * $earth_radius;
		    $d = round($d, $decimal);
			$result[$key]['distance'] = $d;
			
          	$sort[] = $d;
		}
      
    	array_multisort($sort, SORT_ASC, $result);
      	
	    return echoArr(200, '请求成功', $result);
	}



	public function resFind($info, $decimal = 2)
	{
		$result = $this->where('status', 1)->field('bread_name,lng,lat,address')->select()->toArray();

		foreach($result as $key => $val){
			$earth_radius = 6378.137; 				// 地球半径
	        $lng1 = (M_PI / 180) * $info['lng'];	// 起始经度
	        $lng2 = (M_PI / 180) * $val['lng'];		// 结束经度
	        $lat1 = (M_PI / 180) * $info['lat'];	// 起始纬度
	        $lat2 = (M_PI / 180) * $val['lat'];		// 结束纬度

	        $d = acos(sin($lat1) * sin($lat2) + cos($lat1) * cos($lat2) * cos($lng2 - $lng1)) * $earth_radius;
		    $d = round($d, $decimal);
			$result[$key]['distance'] = $d;
          
          	$sort[] = $d;
		}

		array_multisort($sort, SORT_ASC, $result);

	    return $result[0];
	}
}