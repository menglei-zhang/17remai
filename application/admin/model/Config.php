<?php
/**
 * Created by PhpStorm.
 * User: wzy12
 * Date: 2018/10/10
 * Time: 14:08
 */

namespace app\admin\model;


use think\Model;
use app\admin\validate\Config as Vali;

class Config extends Model
{
    public function operation($data)
    {
        // 验证
        $validate = new Vali();
        if(!$validate->check($data)){
            return echoArr(0, $validate->getError());
        }

        $result = $this->allowField(true)->isUpdate(true)->save($data);

        if(false === $result){
            return echoArr(0, $this->getError());
        }else{
            return echoArr(1, '操作成功', ['type' => $data['type']]);
        }
    }
}