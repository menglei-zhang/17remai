<?php
/**
 * Created by PhpStorm.
 * User: wzy12
 * Date: 2018/9/10
 * Time: 15:05
 */

namespace app\admin\model;

use think\Model;
use think\Db;
use app\admin\validate\AdminRule as Vali;

class AdminRule extends Model
{
    public function operation($data){
        $data['controller'] = $data['controller_name'] . '@' . $data['action'];
        $query = $this -> where('controller', $data['controller']);
        $action = false;
        if(isset($data['id'])){
            $action = true;

            $query -> where('id', '<>', $data['id']);
        }

        // 判断是否有该权限
        $res = $query -> value('id');
        if($res){
            return echoArr(0, '权限已存在');
        }

        // 验证
        $validate = new Vali();
        if(!$validate -> check($data)){
            return echoArr(0, $validate->getError());
        }

        // 操作角色数据
        $result = $this -> allowField(true) -> isUpdate($action) -> save($data);
        if(false === $result){
            return echoArr(0, $this -> getError());
        }

        // 操作角色和规则数据
        $res = model('AdminRuleClassify') -> allowField(true) -> isUpdate($action);
        if(isset($data['id'])){
            $data['rule_id'] = $data['id'];
            unset($data['id']);

            $res -> save($data, ['rule_id' => $data['rule_id']]);
        }else{
            $data['rule_id'] = $this -> id;
            $res -> save($data);
        }

        if($res !== false){
            return echoArr(1, '操作成功');
        }else{
            return echoArr(0, '操作失败');
        }
    }

    public function resList($page = 10){
        $list = $this
                -> alias('r')
                -> join('AdminRuleClassify rc', 'rc.rule_id = r.id')
                -> join('AdminClassify c', 'c.id = rc.classify_id')
                -> order('r.id desc')
                -> field('r.*,rc.classify_id,c.classify_name')
                -> paginate($page);

        return $list;
    }

    public function del($data){
        // 验证
        $validate = new Vali();
        if(!$validate -> scene('del') -> check($data)){
            return echoArr(0, $validate->getError());
        }

        $isRole = model('AdminRoleRule') -> whereIn('rule_id', $data['id']) -> value('id');
        if($isRole){
            return echoArr(0, '请先去除拥有此权限的角色');
        }

        $res = model('AdminRuleClassify') -> whereIn('rule_id', $data['id']) -> delete();

        $result = $this -> destroy($data['id']);
        if(false === $result){
            return echoArr(0, '操作失败');
        }else{
            return echoArr(1, '操作成功');
        }
    }

    // 查询单条
    public function resFind($id){
        $result = $this -> find($id);
        $classify_id = model('AdminRuleClassify') -> where('rule_id', $id) -> value('classify_id');
        $result['classify_id'] = $classify_id;

        return $result;
    }
}