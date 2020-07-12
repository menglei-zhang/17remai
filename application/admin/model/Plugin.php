<?php
/**
 * Created by PhpStorm.
 * User: wzy12
 * Date: 2018/10/20
 * Time: 11:47
 */

namespace app\admin\model;


use think\Model;
use app\admin\validate\Plugin as Vali;

class Plugin extends Model
{
    /**
     * 查询多条
     * @param $type 根据类型跳转对应的页面
     * @return \think\Paginator
     * @throws \think\exception\DbException
     */
    public function resList($type){
        $list = $this -> where('type', $type) -> paginate(10) -> each(function($item, $key){
            $item['config'] = unserialize($item['config']);
            $item['config_value'] = unserialize($item['config_value']);

            return $item;
        });

        return $list;
    }

    /**
     * 查询单条
     * @param $id
     * @return array|false|\PDOStatement|string|Model
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function resFind($id){
        $result = $this -> find($id);

        $result['config'] = unserialize($result['config']);
        $result['config_value'] = unserialize($result['config_value']);

        return $result;
    }

    public function operation($data){
        unset($data['__token__']);
        $config_value  = $data;
        unset($config_value['id']);
        unset($config_value['type']);
        $data['config_value'] = serialize($config_value);

        $result = $this -> allowField(true) -> isUpdate(true) -> save($data);
        if(false === $result){
            return echoArr(0, $this -> getError());
        }else{
            return echoArr(1, '操作成功');
        }
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
}