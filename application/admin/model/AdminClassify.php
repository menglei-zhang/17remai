<?php
/**
 * Created by PhpStorm.
 * User: wzy12
 * Date: 2018/9/10
 * Time: 15:28
 */

namespace app\admin\model;

use think\Model;
use app\admin\validate\AdminClassify as Vali;

class AdminClassify extends Model
{
    public function operation($data){
        $query = $this -> where('classify_name', $data['classify_name']);
        $action = false;
        if(isset($data['id'])){
            $action = true;

            $query -> where('id', '<>', $data['id']);
        }
        // 判断是否有该分类
        $res = $query -> value('id');
        if($res){
            return echoArr(0, '该分类已存在');
        }

        // 验证
        $validate = new Vali();
        if(!$validate -> check($data)){
            return echoArr(0, $validate->getError());
        }

        $result = $this -> allowField(true) -> isUpdate($action) -> save($data);
        if(false === $result){
            return echoArr(0, $this -> getError());
        }else{
            return echoArr(1, '操作成功');
        }
    }

    public function del($data){
        // 验证
        $validate = new Vali();
        if(!$validate-> scene('del') -> check($data)){
            return echoArr(0, $validate->getError());
        }

        $res = model('AdminRuleClassify') -> whereIn('classify_id', $data['id']) -> value('id');

        $result = false;
        if(!$res){
            $result = $this -> destroy($data['id']);
        }else{
            return echoArr(0, '请先去除拥有此规则的权限');
        }

        if(false === $result){
            return echoArr(0, '操作失败');
        }else{
            return echoArr(1, '操作成功');
        }
    }
}