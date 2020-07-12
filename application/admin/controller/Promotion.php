<?php
/**
 * Created by PhpStorm.
 * User: wzy12
 * Date: 2018/10/31
 * Time: 9:30
 */

namespace app\admin\controller;

use app\admin\validate\Bread;

class Promotion extends Base
{
    /**
     * 优惠券管理
     */
    public function coupon()
    {
        $list = model('Coupon')->order('id ASC')->paginate(10);
        $this->assign('list', $list);
        return $this->fetch();
    }

    /**
     * 优惠券添加/修改
     */
    public function coupon_form()
    {
        if($this->request->isAjax()){
            $data = input('post.');
            $result = model('Coupon')->operation($data);
            if($result['code']){
                return $this->success($result['msg'], url('promotion/coupon'), ['token' => $this->request->token()]);
            }else{
                return $this->error($result['msg'], ['token' => $this->request->token()]);
            }
        }else{
            $id = input('id', 0);
            $res = null;
            if($id){
                $res = model('Coupon')->resFind($id);
            }

            $this->assign('id', $id);
            $this->assign('res', $res);
            return $this->fetch();
        }
    }


    /**
     * 优惠券删除
     */
    public function coupon_del()
    {
        if($this->request->isAjax()){
            $data = input('post.');

            $res = model('Coupon')->del($data);
            if($res['code']){
                return $this->success($res['msg'], url('promotion/coupon'), ['token' => $this->request->token()]);
            }else{
                return $this->error($res['msg'], ['token' => $this->request->token()]);
            }
        }else{
            return $this->error('非法请求', url('index/index'));
        }
    }


    /**
     * 优惠券导出excel
     * @param $strTable 表格内容
     * @param $filename 文件名
     */
    public function export()
    {
        $data = model('Coupon')->order('add_time desc')->select();

        $strTable ='<table width="800" border="1">';

        $strTable .= '<tr><td colspan="5" style="text-align:center;">优惠券信息</td></tr>';

        $strTable .= '<tr>';

        $strTable .= '<td style="text-align:center;font-size:12px;" width="*">优惠券券码</td>';

        $strTable .= '<td style="text-align:center;font-size:12px;" width="*">优惠券密码</td>';
        
        $strTable .= '<td style="text-align:center;font-size:12px;" width="*">优惠券金额</td>';

        $strTable .= '<td style="text-align:center;font-size:12px;" width="*">使用开始日期</td>';

        $strTable .= '<td style="text-align:center;font-size:12px;" width="*">使用截止日期</td>';

        $strTable .= '</tr>';

        foreach($data as $key => $value){

            $strTable .= '<tr>';

            $strTable .= '<td style="text-align:center;font-size:12px;">'.$value['coupon'].'</td>';

            $strTable .= '<td style="text-align:center;font-size:12px;">'.$value['coupon_pass'].'</td>';
            
            $strTable .= '<td style="text-align:center;font-size:12px;">'.$value['money'].'</td>';

            $strTable .= '<td style="text-align:center;font-size:12px;">'.date("Y-m-d H:i",$value['use_start_time']).'</td>';

            $strTable .= '<td style="text-align:center;font-size:12px;">'.date("Y-m-d H:i",$value['use_end_time']).'</td>';

            $strTable .= '</tr>';

        }
        $strTable .='</table>';

        $this->downloadExcel($strTable,'boards');

        exit();

    }

    public function downloadExcel($strTable,$filename)
    {

       header("Content-type: application/vnd.ms-excel");

       header("Content-Type: application/force-download");

       header("Content-Disposition: attachment; filename=".$filename."_".date('Y-m-d').".xls");

       header('Expires:0');

       header('Pragma:public');

       echo '<html><meta http-equiv="Content-Type" content="text/html; charset=utf-8" />'.$strTable.'</html>';

    }


    /**
     * 优惠券状态
     */
    public function coupon_status()
    {
        if($this->request->isAjax()){
            $data = input('post.');
            $result = model('Coupon')->editStatus($data);
            if($result['code']){
                return $this->success($result['msg'], url('promotion/coupon'), ['token' => $this->request->token()]);
            }else{
                return $this->error($result['msg'], ['token' => $this->request->token()]);
            }
        }else{
            return $this->error('非法请求');
        }
    }



    /**
     * 面包券列表
     */
    public function bread()
    {
        $search = input();
      	$data = model('Bread')->reqList($search);
		unset($search['page']);
		if (!$search) {
			$search = null;
		}
        $this->assign('list', $data);
		$this->assign('search', $search);
        return $this->fetch();
    }


    /**
     * 面包券添加/修改
     */
    public function bread_form()
    {
        if($this->request->isAjax()){
            $data = input('post.');
            $result = model('Bread')->editBread($data);
            if($result['code']){
                return $this->success($result['msg'], url('promotion/bread'), ['token' => $this->request->token()]);
            }else{
                return $this->error($result['msg'], ['token' => $this->request->token()]);
            }
        }else{
            $id = input('id', 0);
            $res = null;
            if($id){
                $res = model('Bread')->resList($id);
            }

            $this->assign('id', $id);
            $this->assign('res', $res);
            return $this->fetch();
        }

    }

    /**
     * 面包券状态修改
     */
    public function bread_status()
    {
        if($this->request->isAjax()){
            $data = input('post.');
            $result = model('Bread')->editStatus($data);
            if($result['code']){
                return $this->success($result['msg'], url('promotion/bread'), ['token' => $this->request->token()]);
            }else{
                return $this->error($result['msg'], ['token' => $this->request->token()]);
            }
        }else{
            return $this->error('非法请求');
        }
    }

    /**
     * 面包券删除
     */
    public function bread_del()
    {
        if($this->request->isAjax()){
            $data = input('post.');
            $result = model('Bread')->whereIn('id', $data['id'])->delete();
            if($result){
                return echoArr(1, '删除成功');
            }else{
                return echoArr(0, '删除失败', $result->getError());
            }
        }else{
            return $this->error('非法请求');
        }
    }




    /**
     * 新人券列表
     */
    public function newpeople()
    {
        $data = model('Newpeople')->order('id ASC')->paginate(10);

        $this->assign('list', $data);
        return $this->fetch();
    }


    /**
     * 新人券添加/修改
     */
    public function newpeople_form()
    {
        if($this->request->isAjax()){
            $data = input('post.');
            $result = model('Newpeople')->editnewpeople($data);
            if($result['code']){
                return $this->success($result['msg'], url('promotion/newpeople'), ['token' => $this->request->token()]);
            }else{
                return $this->error($result['msg'], ['token' => $this->request->token()]);
            }
        }else{
            $id = input('id', 0);
            $res = null;
            if($id){
                $res = model('Newpeople')->resList($id);
            }

            $this->assign('id', $id);
            $this->assign('res', $res);
            return $this->fetch();
        }

    }


    /**
     * 新人券状态修改
     */
    public function newpeople_status()
    {
        if($this->request->isAjax()){
            $data = input('post.');
            $result = model('Newpeople')->editStatus($data);
            if($result['code'] == 1){
                return $this->success($result['msg'], url('promotion/newpeople'), ['token' => $this->request->token()]);
            }else{
                return $this->error($result['msg'], ['token' => $this->request->token()]);
            }
        }else{
            return $this->error('非法请求');
        }
    }


    /**
     * 新人券删除
     */
    public function newpeople_del()
    {
        if($this->request->isAjax()){
            $data = input('post.');
            $result = model('Newpeople')->whereIn('id', $data['id'])->delete();
            if($result){
                return echoArr(1, '删除成功');
            }else{
                return echoArr(0, '删除失败', $result->getError());
            }
        }else{
            return $this->error('非法请求');
        }
    }
	
    /**
     * 微盟会员卡
     */
    public function weimengCard()
    {
        $search = input();
      	$data = model('WeimengCard')->reqList($search);
		unset($search['page']);
		if (!$search) {
			$search = null;
		}
        $this->assign('list', $data);
		$this->assign('search', $search);
        return $this->fetch();
    }
}