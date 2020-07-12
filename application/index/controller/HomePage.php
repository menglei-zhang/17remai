<?php

namespace app\index\controller;

use app\index\model\Banner;
use app\index\model\Bread;
use app\index\model\Goods;
use app\index\model\GoodsImg;
use app\index\model\Machine;

class HomePage extends Base
{
    /**
     * @return 首页
     */
    public function index()
    {
        // 首页搜索框
        $info = input('post.');
      	if(empty($info)){
        	$info['lng'] = 121.3969;
            $info['lat'] = 31.34526;
        }
        $Machine = new Machine();
        $result = $Machine->resFind($info);
        
        // 获取banner图
        $BannerModel = new Banner();
        $list = $BannerModel->resList();
		
        // 购买面包券
        $BreadModel = new Bread();
        $data = $BreadModel->getBread();
       
        // 商品展示
        $GoodsModel = new Goods();
        $GoodsData = $GoodsModel->getGoodsAll();

        $dataAll['Machine'] = $result;
        $dataAll['Banner'] = $list;
        $dataAll['Bread'] = $data;
        $dataAll['GoodsData'] = $GoodsData;
        return json(echoArr(200, '请求成功', $dataAll));
    }


    /**
     * @return 商品图片
     */
    public function goodsImg()
    {
        $data = input();

        $GoodsImg = new GoodsImg();
        $goodsImg = $GoodsImg->getGoodsImg($data);

        return json($goodsImg);
    }
}

