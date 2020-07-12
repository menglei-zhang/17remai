<?php
/**
 * Created by PhpStorm.
 * User: wzy12
 * Date: 2018/9/7
 * Time: 10:36
 */

namespace app\admin\model;

use think\Model;
use app\admin\validate\AdminUser as Vali;

class AdminUser extends Model
{
    public function Roles()
    {
        return $this->belongsToMany('AdminRole', 'AdminUserRole', 'role_id', 'user_id');
    }

    public function login($data)
    {
        // 判断账号密码
        $password = encryption(trim($data['password']));
        $isexis = $this->where('username', $data['username'])->where('password', $password)->find();
        if(!$isexis){
            return echoArr(0,'账号或密码错误');
        }else{
            if($isexis['status'] == 0){
                return echoArr(0,'账号已被禁用');
            }

            session('user', $isexis);
            cookie('username', $isexis['username'], 3600 * 24 * 7);
            return echoArr(200, '登录成功');
        }
    }

    public function operation($data){
        $data['password'] = trim($data['password']);
        $data['repassword'] = trim($data['repassword']);

        $query = $this -> where('username', $data['username']);
        $action = false;
        if(isset($data['id'])){
            $action = true;

            // 超级管理员不需要勾选角色
            if($data['id'] != 1){
                if(!isset($data['role_id'])){
                    return echoArr(0, '请勾选角色');
                }
            }

            // 禁止修改系统默认管理员角色
            if($data['id'] == 1){
                if(isset($data['role_id'])){
                    return echoArr(0, '非法操作，正在记录你的ip地址');
                }
            }

            // 修改管理员密码如果为空，则不修改
            if($data['password'] === '' && $data['repassword'] === ''){
                unset($data['password']);
                unset($data['repassword']);
            }

            unset($data['username']);
            $query -> where('id', '<>', $data['id']);
        }else{
            if(!$data['username']){
                return echoArr(0, '登录名不能为空');
            }
            $data['create_time'] = time();
        }

        // 判断该管理员是否存在
        $res = $query -> value('id');
        if($res){
            return echoArr(0, '该管理员已存在');
        }

        // 判断密码是否一致
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
        if(!$validate -> scene('user') -> check($data)){
            return echoArr(0, $validate->getError());
        }

        $result = $this -> allowField(true) -> isUpdate($action) -> save($data);
        if(false === $result){
            return echoArr(0, $this -> getError());
        }

        // 删除该管理员拥有的角色，并重新赋值新的角色，此操作对默认管理员无效
        if(isset($data['id'])){
            if($data['id'] != 1){
                $current = $this -> resFind($data['id']);

                $this -> Roles() -> detach($current['role_id']);
            }

            $res = $this -> Roles() -> saveAll($data['role_id']);
        }

        if($result !== false){
            return echoArr(1, '操作成功');
        }else{
            return echoArr(0, '操作失败');
        }
    }

    // 更改状态
    public function editStatus($data){
        if($data['id'] == 1){
            return echoArr(0, '你想禁用我，你怕是在做梦');
        }

        // 验证
        $validate = new Vali();
        if(!$validate -> scene('status') -> check($data)){
            return echoArr(0, $validate->getError());
        }

        $result = $this -> allowField(true) -> isUpdate(true) -> save($data);
        if(false === $result){
            return echoArr(0, $this -> getError());
        }else{
            return echoArr(1, '操作成功', $data['status']);
        }
    }

    public function resList($where){
        $query = $this -> where(1);

        // 筛选条件
        $is_where = $where;
        unset($is_where['page']);
        if($is_where){
            if($where){
                // 查询时间日期
                if($where['start'] && $where['end']){
                    $create_time = search($where, 'create_time');
                    if($create_time != 1){
                        $query -> where($create_time);
                    }
                }
                // 查询用户名
                if($where['key']){
                    $query -> where('username|phone', 'like',  "%{$where['key']}%");
                }
            }
        }

        $list = $query -> order('id desc') -> paginate(10,false,['query'=>request()->param()]);

        foreach($list as $k => $v){
            $list[$k]['role_name'] = $v -> Roles;
            $temp = [];
            foreach($list[$k]['role_name'] as $val){
                $temp[] = $val -> role_name;
            }

            $list[$k]['role_name'] = implode(', ', $temp);
        }

        return $list;
    }

    public function resFind($id){
        $result = $this -> find($id);
        $arr = $result -> Roles;

        $temp = [];
        foreach($arr as $role){
            $temp[] = $role->id;
        }
        $result['role_id'] = $temp;

        return $result;
    }

    public function del($data){
        // 验证
        $validate = new Vali();
        if(!$validate -> scene('status') -> check($data)){
            return echoArr(0, $validate->getError());
        }

        is_array($data['id']) ? $ids = $data['id'] : $ids[] = $data['id'];
        if(in_array(1, $ids)){
            return echoArr(0, '你想删除我，你怕是在做梦');
        }

        $res = model('AdminUserRole') -> whereIn('user_id', $data['id']) -> delete();
        $result = $this -> destroy($data['id']);

        if(false === $result){
            return echoArr(0, '操作失败');
        }else{
            return echoArr(1, '操作成功');
        }
    }
}