<?php 

namespace app\admin\model;

use think\Model;
use app\admin\validate\Bread as Vali;

class Bread extends Model
{
    /**
     * 面包券列表
     */
	public function resList($id)
	{
		$query = $this->find($id);
		$query['start_time'] = date('Y-m-d', $query['start_time']);
		$query['end_time'] = date('Y-m-d', $query['end_time']);

		return $query;
	}
	
    /**
	 * 筛选列表
	 */
  	public function reqList($where)
    {
    	$query = $this->where(1);
      	//筛选条件
		unset($where['page']);
		if($where){
          	// 查询有效期时间
          	if ($where['start']) {
                $query->where('start_time', strtotime($where['start']));
            }
            if($where['end']){
            	$query->whereTime('end_time', 'between', [strtotime($where['start']),strtotime($where['end'])]);
            }
			// 查询面包券
			if ($where['code']) {
				$query->where('code', $where['code']);
			}
          	
        }
		$list['list'] = $query->paginate(10, false, ['query' => request()->param()]);
      	return $list;
    }

    /**
     * 面包券添加/修改
     */
	public function editBread($data)
	{
		/*$validate = new Vali();
        if(!$validate->scene('form')->check($data)){
            return echoArr(0, $validate->getError());
        }*/
		
        // 判断有效期是否符合规范
        if(strtotime($data['start_time']) >= strtotime($data['end_time'])){
        	return echoArr(0, '有效期开始时间不能比有效期结束时间大');
        }
        // 修改面包券
        if(isset($data['id'])){
        	$data['start_time'] = strtotime($data['start_time']);
        	$data['end_time'] = strtotime($data['end_time']);
        	$result = $this->allowField(true)->isUpdate(true)->save($data);
	        if(false === $result){
	        	return echoArr(0, $result->getError());
	        }else{
	        	return echoArr(1, '修改成功');
	        }
        }

		// 判断该面包券券码是否已经存在
        $query = $this->where('code', $data['code']);
        $res = $query->value('id');
        if($res){
        	return echoArr(0, '面包券券码不能重复');
        }

        // 面包券生成
        $number = $this->order('code DESC')->find();
        for ($i = 0; $i < $data['num']; $i++){
			$temp['name'] = $data['name'];
			$temp['money'] = $data['money'];
			$temp['start_time'] = strtotime($data['start_time']);
			$temp['end_time'] = strtotime($data['end_time']);
			$temp['add_time'] = time();

			// 面包券券码
			$temp['code'] = date('yd') . str_pad(rand(0000,9999), 4, 0, STR_PAD_LEFT);

			$Bread[] = $temp;
        }

		// 添加面包券
        $result = $this->isUpdate(false)->saveAll($Bread);
        if(false === $result){
        	return echoArr(0, $result->getError());
        }else{
        	return echoArr(1, '添加成功');
        }
	}

	/**
     * 更改状态
     */
    public function editStatus($data){
        // 验证
        $validate = new Vali();
        if(!$validate->scene('status')->check($data)){
            return echoArr(0, $validate->getError());
        }
        $result = $this->allowField(true)->isUpdate(true)->save($data);
        if(false === $result){
            return echoArr(0, $this->getError());
        }else{
            return echoArr(1, '操作成功');
        }
    }
}