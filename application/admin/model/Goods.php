<?php
/**
 * Created by PhpStorm.
 * User: wzy12
 * Date: 2018/9/26
 * Time: 15:48
 */

namespace app\admin\model;


use think\Model;
use app\admin\validate\Goods as Vali;
use app\admin\model\GoodsImg;

class Goods extends Model
{
    public function resList($goods_name)
    {
        $query = $this->where(1);
        unset($goods_name['page']);
        if($goods_name){
            // 查询商品标题，模糊查询
            if ($goods_name['goods_name']) { 
                $query->where('goods_name', 'like', "%{$goods_name['goods_name']}%");
            } 
        }

        $list = $query->order('id desc')->paginate(10, false, ['query'=>request()->param()]);

        return $list;
    }


    /**
     * 编辑页面
     */
    public function editGoods($data)
    {
        $GoodsImg = new GoodsImg();
        $validate = new Vali();
        if(!$validate->scene('form')->check($data)){
            return echoArr(0, $validate->getError());
        }

        // 商品修改
        if(isset($data['id'])){
            $Gimg = $GoodsImg->where('goods_id', $data['id'])->select();
            if(isset($data['group_img'])){
                foreach($data['group_img'] as $k => $v){
                    $result[$k]['goods_id'] = $data['id'];
                    $result[$k]['img'] = $v;
                }
                $GoodsImg->where('goods_id', $data['id'])->delete();
                $gImg = $GoodsImg->insertAll($result);
                if($gImg){
                    return echoArr(1, '修改成功');
                }else{
                    return echoArr(0, $gImg->getError());
                }
            }
            
            $temp['id'] = $data['id'];
            $temp['original_img'] = $data['original_img'];
            $res = $this->isUpdate(true)->save($temp);
            if($res){
                return echoArr(1, '修改成功');
            }else{
                return echoArr(0, $res->getError());
            }
        }

        // 判断是否已有该商品
        $gName = $this->where('goods_name', $data['goods_name'])->value('id');
        if($gName){
            return echoArr(0, '商品名重复，请更改');
        }
        // 商品新增
        $data['on_time'] = time();
        $query = $this->allowField(true)->isUpdate(false)->save($data);
        if(false === $query){
            return echoArr(0, '添加失败', $query->getError());
        }
        foreach($data['group_img'] as $key => $val){
            $result['goods_id'] = $this->id;
            $result['img'] = $val;
            $gImg = $GoodsImg->insert($result);
           
        }
        if(false === $gImg){
            return echoArr(0,  $gImg->getError());
        }else{
            return echoArr(1, '添加成功');
        }

    }

    /**
     * 商品删除
     */
    public function del($data)
    {
        // 验证
        $validate = new Vali();
        if(!$validate -> scene('del') -> check($data)){
            return echoArr(0, $validate->getError());
        }

        // 删除与商品关联的图片
        $res_img = model('GoodsImg')->whereIn('goods_id', $data['id'])->delete();

        // 删除商品
        $result = $this -> destroy($data['id']);
        if(false === $result){
            return echoArr(0, $result->getError());
        }else{
            return echoArr(1, '删除成功');
        }
    }
}