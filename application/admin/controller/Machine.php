<?php
/**
 * Created by PhpStorm.
 * User: wzy12
 * Date: 2018/9/7
 * Time: 9:55
 */

namespace app\admin\controller;


class Machine extends Base
{
	/**
     * 面包机列表
     */
	public function machine()
	{
		$data = model('Machine')->order('id ASC')->paginate(10);

		$this->assign('list', $data);
		return $this->fetch();
	}


	/**
     * 面包机添加/修改
     */
    public function machine_form()
    {
        if($this->request->isAjax()){
            $data = input('post.');
            $result = model('Machine')->editMachine($data);
            if($result['code']){
                return $this->success($result['msg'], url('machine/machine'), ['token' => $this->request->token()]);
            }else{
                return $this->error($result['msg'], ['token' => $this->request->token()]);
            }
        }else{
            $id = input('id', 0);
            $res = null;
            if($id){
                $res = model('Machine')->resList($id);
            }

            $this->assign('id', $id);
            $this->assign('res', $res);
            return $this->fetch();
        }
    }


    /**
     * 面包机删除
     */
    public function machine_del()
    {
        $data = input('post.');
        $result = model('Machine')->whereIn('id', $data['id'])->delete();
        if($result){
            return echoArr(1, '删除成功');
        }else{
            return echoArr(0, '删除失败', $result->getError());
        }
    }


    /**
     * 面包机状态修改
     */
    public function machine_status()
    {
        if($this->request->isAjax()){
            $data = input('post.');
            $result = model('Machine')->editStatus($data);
            if($result['code'] == 1){
                return $this->success($result['msg'], url('machine/machine'), ['token' => $this->request->token()]);
            }else{
                return $this->error($result['msg'], ['token' => $this->request->token()]);
            }
        }else{
            return $this -> error('非法请求');
        }
    }
}