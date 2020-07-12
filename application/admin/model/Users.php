<?php
/**
 * Created by PhpStorm.
 * User: wzy12
 * Date: 2018/10/16
 * Time: 17:13
 */

namespace app\admin\model;


use think\Model;
use app\admin\validate\Users as Vali;

class Users extends Model
{
    public function operation($data){
        $data['password'] = trim($data['password']);
        $data['repassword'] = trim($data['repassword']);

        $query = $this -> where("username = '{$data['username']}' OR mobile = {$data['mobile']}");
        $action = false;
        if(isset($data['id'])){
            $action = true;

            unset($data['username']);
            if($data['password'] === '' && $data['repassword'] === ''){
                unset($data['password']);
                unset($data['repassword']);
            }
            $query -> where('id', '<>', $data['id']);
        }else{
            $data['reg_time'] = time();
            if(!$data['username']){
                return echoArr(0, '会员昵称不能为空');
            }
        }
        // 判断是否已有该用户
        $res = $query -> value('id');
        if($res){
            return echoArr(0, '该用户已存在');
        }

        if(isset($data['password'])){
            if($data['password'] === ''){
                return echoArr(0, '密码不能为空');
            }

            if($data['password'] != $data['repassword']){
                return echoArr(0, '密码不一致');
            }else{
                $data['password'] = encryption($data['password']);
            }
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
        if(!$validate->scene('del')->check($data)){
            return echoArr(0, $validate->getError());
        }

        $result = $this->destroy($data['id']);

        if(false === $result){
            return echoArr(0, '删除失败');
        }else{
            return echoArr(1, '删除成功');
        }
    }

    public function resList($where)
    {
        $query = $this->where(1);

        // 筛选条件
        $is_where = $where;
        unset($is_where['page']);
        if($is_where){
            if($where){
                // 查询用户名
                if($where['search_key']){
                    $query -> where('username|mobile', 'like',  "%{$where['search_key']}%");
                }
            }
        }

        $list = $query ->  paginate(10, false,['query'=>request()->param()]);

        return $list;
    }

    // 更改状态
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
            return echoArr(1, '操作成功', $data['status']);
        }
    }

}