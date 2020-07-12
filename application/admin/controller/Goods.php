<?php
/**
 * Created by PhpStorm.
 * User: wzy12
 * Date: 2018/9/20
 * Time: 13:49
 */

namespace app\admin\controller;


class Goods extends Base
{
    /**
     * 商品列表
     */
    public function goods()
    {
        $goods_name = input();
        $data = model('Goods')->resList($goods_name);
        unset($goods_name['page']);
        if (!$goods_name) {
            $goods_name = null;
        }
        $this->assign('list', $data);
        $this->assign('goods_name', $goods_name);
        return $this->fetch();
    }

    /**
     * 商品添加/修改
     */
    public function goods_form()
    {
        if($this->request->isAjax()){
            $data = input('post.');
            $result = model('Goods')->editGoods($data);
            if($result['code']){
                return $this->success($result['msg'], url('goods/goodsCate'), ['token' => $this->request->token()]);
            }else{
                return $this->error($result['msg'], '', ['token' => $this->request->token()]);
            }
        }else{
            $id = input('id');
            $result = null;
            $group_img = null;
            if($id){
                $result = model('Goods')->where('id', $id)->find();
                // 商品组图
                $group_img = model('GoodsImg')->where('goods_id', $id)->select();
            }
            $this->assign('result', $result);
            $this->assign('id', $id);
            $this->assign('group_img', $group_img);
            return $this->fetch();
        }
    }

    /**
     * 商品单个删除/批量删除
     */
    public function goods_del()
    {
        $data = input('post.');

        $goods = model('Goods')->del($data);
        if($goods['code']){
            return $this->success($goods['msg'], url('goods/goods'), ['token' => $this->request->token()]);
        }else{
            return $this->error($goods['msg'], '', ['token' => $this->request->token()]);
        }
    }


    /**
     * 商品图片上传
     */
    public function goods_upload()
    {
        $file = input('file.img');
        $num = 1;

        if (!$file) {
            $num = 0;
            $file = input('file.file');
        }

        $msg = $this->upload($file);
        if (!$msg['code']) {
            return $this->error($msg['msg']);
        } else {
            return echoArr($num, $msg['msg'], ['img' => $msg['data']['img'], 'src' => config('imgRoute') . $msg['data']['img']]);
        }
    }


}