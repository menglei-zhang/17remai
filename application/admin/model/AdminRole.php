<?php
/**
 * Created by PhpStorm.
 * User: wzy12
 * Date: 2018/9/10
 * Time: 15:05
 */

namespace app\admin\model;

use think\Model;
use app\admin\validate\AdminRole as Vali;

class AdminRole extends Model
{
    public function rules(){
        return $this->belongsToMany('AdminRule', 'AdminRoleRule', 'rule_id', 'role_id');
    }

    public function operation($data){
        $query = $this -> where('role_name', $data['role_name']);
        $action = false;
        if(isset($data['id'])){
            $action = true;

            $query -> where('id', '<>', $data['id']);
        }
        // 判断是否已该角色
        $res = $query -> value('id');
        if($res){
            return echoArr(0, '该角色已存在');
        }

        // 验证
        $validate = new Vali();
        if(!$validate -> check($data)){
            return echoArr(0, $validate->getError());
        }

        $result = $this -> allowField(true) -> isUpdate($action) -> save($data);
        if(false === $result){
            return echoArr(0, $this -> getError());
        }

        if(isset($data['id'])){
            $current = $this -> resFind($data['id']);

            $this -> rules() -> detach($current['rule_id']);
        }

        $res = $this -> rules() -> saveAll($data['role_rule_id']);

        if($res !== false){
            return echoArr(1, '操作成功');
        }else{
            return echoArr(0, '操作失败');
        }
    }

    // 规则和权限
    public function rule(){
        $list = model('AdminRule') -> resList(1000);
        $result = array();
        foreach ($list as $data) {
            isset($result[$data['classify_name']]) || $result[$data['classify_name']] = array();
            $result[$data['classify_name']][] = $data;
        }

        return $result;
    }

    // 查询列表
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
                if($where['role_name']){
                    $query -> where('role_name', 'like',  "%{$where['role_name']}%");
                }
            }
        }

        $list= $query -> where('id', 'neq', 1) -> order('id desc') -> paginate(10,false,['query'=>request()->param()]);

        foreach($list as $k => $v){
            $list[$k]['rule_name'] = $v -> rules;
            $temp = [];
            foreach($list[$k]['rule_name'] as $val){
                $temp[] = $val -> rule_name;
            }

            $temp = implode(', ', $temp);
            $list[$k]['rule_name'] = interceptStr($temp);
        }

        return $list;
    }

    // 查询单条
    public function resFind($id){
        $result = $this -> find($id);

        $arr = $result -> rules;
        $temp = [];
        foreach($arr as $role){
            $temp[] = $role->id;
        }

        $result['rule_id'] = $temp;

        return $result;
    }

    // 更改状态
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
            return echoArr(1, '操作成功', $data['status']);
        }
    }

    public function del($data){
        // 验证
        $validate = new Vali();
        if(!$validate -> scene('del') -> check($data)){
            return echoArr(0, $validate->getError());
        }

        is_array($data) ? $ids = $data : $ids[] = $data['id'];
        if(in_array(1, $ids)){
            return echoArr(0, '你想删除我，你怕是在做梦');
        }

        $isUser = model('AdminUserRole') -> whereIn('role_id', $data['id']) -> value('id');
        if($isUser){
            return echoArr(0, '请先去除拥有此角色的用户');
        }

        $res = model('AdminRoleRule') -> whereIn('role_id', $data['id']) -> delete();
        $result = $this -> destroy($data['id']);

        if(false === $result){
            return echoArr(0, '操作失败');
        }else{
            return echoArr(1, '操作成功');
        }
    }

}