<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: yunwuxin <448901948@qq.com>
// +----------------------------------------------------------------------

return [
    'admin' => [
        'name' => '管理员管理', 'icon' => '&#xe726;', 'child' => [
            ['name' => '管理员列表', 'icon' => '&#xe6a7;', 'op' => 'admin', 'act' => 'user',],
            ['name' => '角色管理', 'icon' => '&#xe6a7;', 'op' => 'admin', 'act' => 'role',],
            ['name' => '权限分类', 'icon' => '&#xe6a7;', 'op' => 'admin', 'act' => 'classify',],
            ['name' => '权限管理', 'icon' => '&#xe6a7;', 'op' => 'admin', 'act' => 'rule',],
            ['name' => '登录日志', 'icon' => '&#xe6a7;', 'op' => 'admin', 'act' => 'log',],
        ]
    ],

    'users' => [
        'name' => '会员管理', 'icon' => '&#xe6b8;', 'child' => [
            ['name' => '会员列表', 'icon' => '&#xe6a7;', 'op' => 'users', 'act' => 'user',],
        ]
    ],

    'goods' => [
        'name' => '商品管理', 'icon' => '&#xe83b;', 'child' => [
            ['name' => '商品列表', 'icon' => '&#xe6a7;', 'op' => 'goods', 'act' => 'goods',],
        ]
    ],

    'order' => [
        'name' => '订单管理', 'icon' => '&#xe83b;', 'child' => [
            ['name' => '订单列表', 'icon' => '&#xe6a7;', 'op' => 'order', 'act' => 'order',],
        ]
    ],

    'mallConfig' => [
        'name' => '商城配置', 'icon' => '&#xe6ae;', 'child' => [
            ['name' => '广告列表', 'icon' => '&#xe6a7;', 'op' => 'banner', 'act' => 'banner',],
            ['name' => '网站信息', 'icon' => '&#xe6a7;', 'op' => 'banner', 'act' => 'config'],
        ]
    ],
    
    'machine' => [
        'name' => '面包机管理', 'icon' => '&#xe6b3;', 'child' => [
            ['name' => '面包机列表', 'icon' => '&#xe6a7;', 'op' => 'machine', 'act' => 'machine',],
        ]
    ],


    'promition' => [
        'name' => '促销管理', 'icon' => '&#xe6c5;', 'child' => [
            ['name' => '优惠券管理', 'icon' => '&#xe6a7;', 'op' => 'promotion', 'act' => 'coupon',],
            ['name' => '面包券管理', 'icon' => '&#xe6a7;', 'op' => 'promotion', 'act' => 'bread',],
            ['name' => '新人券管理', 'icon' => '&#xe6a7;', 'op' => 'promotion', 'act' => 'newpeople',],
            ['name' => '微盟会员卡', 'icon' => '&#xe6a7;', 'op' => 'promotion', 'act' => 'weimengcard',],
        ]
    ],

    'plugin' => [
        'name' => '插件管理', 'icon' => '&#xe75f;', 'child' => [
            ['name' => '支付插件', 'icon' => '&#xe6a7;', 'op' => 'component', 'act' => 'plugin', 'type' => 'payment',],
            ['name' => '短信插件', 'icon' => '&#xe6a7;', 'op' => 'component', 'act' => 'plugin', 'type' => 'sms',],
            ['name' => '物流插件', 'icon' => '&#xe6a7;', 'op' => 'component', 'act' => 'plugin', 'type' => 'shipping',],
        ]
    ],

];
