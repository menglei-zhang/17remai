<?php
/**
 * Created by PhpStorm.
 * User: wzy12
 * Date: 2018/10/29
 * Time: 16:09
 */

namespace app\admin\model;


use think\Model;
use app\admin\validate\Coupon as Vali;

class Coupon extends Model
{
    public function resFind($id)
    {
        $res = $this -> find($id);

        $res['use_start_time'] = date('Y-m-d', $res['use_start_time']);
        $res['use_end_time'] = date('Y-m-d', $res['use_end_time']);

        return $res;
    }


    public function operation($data)
    {
        // 验证
        $validate = new Vali();
        if(!$validate->scene('form')->check($data)){
            return echoArr(0, $validate->getError());
        }

        // 判断有效期是否符合规范
        if(strtotime($data['use_start_time']) >= strtotime($data['use_end_time'])){
            return echoArr(0, '有效期开始时间不能比有效期结束时间大');
        }

        // 修改优惠券
        if(isset($data['id'])){
            $data['use_start_time'] = strtotime($data['use_start_time']);
            $data['use_end_time'] = strtotime($data['use_end_time']);
            $result = $this->allowField(true)->isUpdate(true)->save($data);
            if(false === $result){
                return echoArr(0, $result->getError());
            }else{
                return echoArr(1, '修改成功');
            }
        }

        // 判断该面包券券码是否已经存在
        $query = $this->where('coupon', $data['coupon']);
        $res = $query->value('id');
        if($res){
            return echoArr(0, '面包券券码不能重复');
        }

        // 优惠券生成
        $number = $this->order('coupon DESC')->find();
        $num = 0;
        for ($i = 0; $i < $data['num']; $i++){
            // 优惠券券码
            $num = $num + 1;
            $temp['coupon'] = date('ym') . str_pad(substr($number['coupon'], -4) + $num, 4, 0, STR_PAD_LEFT);

            // 优惠券密码规则
            $capslk = chr(rand(65,90)) . chr(rand(65,90));
            $num_rand = rand(10, 99);
            $capslk_next = chr(rand(65,90)) . chr(rand(65,90));
            $temp['coupon_pass'] = $capslk . $num_rand . $capslk_next . rand(10, 99);

            $temp['money'] = $data['money'];
            $temp['use_start_time'] = strtotime($data['use_start_time']);
            $temp['use_end_time'] = strtotime($data['use_end_time']);
            $temp['add_time'] = time();

            $Bread[] = $temp;
        }

        // 添加优惠券
        $result = $this->allowField(true)->isUpdate(false)->saveAll($Bread);
        if(false === $result){
            return echoArr(0, $this->getError());
        }else{
            return echoArr(1, '操作成功');
        }
    }



    public function del($data)
    {
        // 验证
        $validate = new Vali();
        if(!$validate->scene('del')->check($data)){
            return echoArr(0, $validate->getError());
        }

        $result = $this->whereIn('id', $data['id'])->delete();
        if(false === $result){
            return echoArr(0, $this->getError());
        }else{
            return echoArr(1, '操作成功');
        }
    }

    /**
     * 更改状态
     */
    public function editStatus($data){
        // 验证
        $validate = new Vali();
        if(!$validate -> scene('status') -> check($data)){
            return echoArr(0, $validate->getError());
        }
        $result = $this -> allowField(true) -> isUpdate(true) -> save($data);
        if(false === $result){
            return echoArr(0, $this -> getError());
        }else{
            return echoArr(1, '操作成功');
        }
    }
}