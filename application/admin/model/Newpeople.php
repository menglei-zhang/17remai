<?php 

namespace app\admin\model;

use think\Model;
use app\admin\validate\Newpeople as Vali;

class Newpeople extends Model
{
	/**
     * 新人券列表
     */
	public function resList($id)
	{
		$query = $this->find($id);

		return $query;
	}


	/**
     * 新人券添加/修改
     */
	public function editNewpeople($data)
	{
		// 验证
        $validate = new Vali();
        if(!$validate->scene('form')->check($data)){
            return echoArr(0, $validate->getError());
        }

		// 修改新人券
		if(isset($data['id'])){
			$result = $this->allowField(true)->isUpdate(true)->save($data);
	        if(false === $result){
	        	return echoArr(0, $result->getError());
	        }else{
	        	return echoArr(1, '修改成功');
	        }
		}

		// 添加新人券
		$data['add_time'] = time();
		$result = $this->allowField(true)->isUpdate(false)->save($data);
        if(false === $result){
        	return echoArr(0, $result->getError());
        }else{
        	return echoArr(1, '添加成功');
        }
	}

	/**
     * 更改状态
     */
    public function editStatus($data)
    {
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