<?php
/**
 * Created by PhpStorm.
 * User: wzy12
 * Date: 2018/10/26
 * Time: 14:43
 */

namespace app\admin\model;


use think\Model;
use app\admin\validate\Comment as Vali;

class Comment extends Model
{
    /**
     * 查询多条
     * @return \think\Paginator
     * @throws \think\exception\DbException
     */
    public function resList(){
        $list = $this -> alias('c')
            -> where('c.pid', 0)
            -> join('Goods g', 'g.id = c.goods_id')
            -> field('c.*,g.goods_name')
            -> paginate(10);

        return $list;
    }

    /**
     * @param $data 查询条件id
     * @return array 返回的新数组
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function resFind($data){
        $list = $this -> select();

        $result = cateFind($list, 0, $data);

        return $result;
    }

    /**
     * @param $data 发送的数据
     */
    public function send($data){
        $data['username'] = session('user.username');
        $data['email'] = session('user.email');
        $data['add_time'] = time();
        $data['ip_address'] = request()->ip();
        $data['content'] = strip_tags($data['content'], '<img>');

        // 验证
        $validate = new Vali();
        if(!$validate -> check($data)){
            return echoArr(0, $validate->getError());
        }

        $result = $this -> allowField(true) -> isUpdate(false) -> save($data);

        if($result !== false){
            return echoArr(1, '操作成功', ['data' => $data]);
        }else{
            return echoArr(0, '网络似乎有些延迟，请重新发送');
        }

        return $result;
    }


    public function del($data){
        // 验证
        $validate = new Vali();
        if(!$validate -> scene('del') -> check($data)){
            return echoArr(0, $validate->getError());
        }

        $this -> whereIn('pid', $data['id']) -> delete();
        $result = $this -> destroy($data['id']);

        if(false === $result){
            return echoArr(0, '操作失败');
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