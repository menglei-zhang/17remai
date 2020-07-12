<?php
/**
 * Created by PhpStorm.
 * User: wzy12
 * Date: 2018/11/8
 * Time: 16:45
 */

namespace app\admin\model;


use think\Model;
use app\admin\validate\Machine as Vali;

class Machine extends Model
{
	/**
     * 面包机列表
     */
	public function resList($id)
	{
		$query = $this->find($id);

		return $query;
	}

	/**
     * 面包机添加/修改
     */
	public function editMachine($data)
	{
        // 验证
        $validate = new Vali();
        if(!$validate->scene('form')->check($data)){
            return echoArr(0, $validate->getError());
        }
        
        // 经纬度
        $address = $data['province'] . $data['city'] . $data['district'] . $data['address'];
        $temp = getLatng($address);
        
        // 合并数组
        $data = array_merge($data, $temp);
        
		// 修改面包机
		if(isset($data['id'])){
			$result = $this->allowField(true)->isUpdate(true)->save($data);
	        if(false === $result){
	        	return echoArr(0, $result->getError());
	        }else{
	        	return echoArr(1, '修改成功');
	        }
		}

		// 添加面包机
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