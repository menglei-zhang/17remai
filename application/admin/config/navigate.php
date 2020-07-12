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
    'admin/users' => [
        'name' => '会员管理',
        'action' => [
            'user' => '会员列表',
            'user_form' => '添加修改会员',
            'user_del' => '会员删除',
            'user_status' => '会员状态修改',
        ]
    ],

    
    'admin/component' => [
        'name' => '插件管理',
        'action' => [
            'plugin' => '插件列表',
            'plugin_form' => '添加修改插件',
            'status' => '修改插件状态',
            'alipay' => '调用支付宝支付示例',
            'wxpay' => '调用微信支付示例',
            'alisms' => '调用短信示例',
            'kdniao' => '查看物流信息',
        ]
    ],

    'admin/goods' => [
        'name' => '商品管理',
        'action' => [
            'goods' => '商品列表',
            'goods_form' => '添加修改商品',
            'goods_del' => '删除商品',
            'goods_upload' => '商品图片上传',
        ]
    ],


    'admin/order' => [
        'name' => '订单管理',
        'action' => [
            'order' => '订单列表',
            'order_form' => '添加修改订单',
        ]
    ],


    'admin/banner' => [
        'name' => '商城配置',
        'action' => [
            'banner' => '广告列表',
            'banner_form' => '添加修改广告',
            'banner_del' => '广告删除',
            'banner_status' => '广告状态修改',
            'goods_upload' => '广告图片上传',
            'config' => '商城设置列表',
            'config_form' => '修改商城设置列表',
            'config_upload' => '商城logo上传',
        ]
    ],


    'admin/machine' => [
        'name' => '面包机管理',
        'action' => [
            'machine' => '面包机列表',
            'machine_form' => '添加修改面包机',
            'machine_del' => '面包机删除',
            'machine_status' => '面包机状态修改',
        ]
    ],

    'admin/promotion' => [
        'name' => '促销管理',
        'action' => [
            'coupon' => '优惠券管理',
            'coupon_form' => '添加修改优惠券',
            'coupon_del' => '优惠券删除',
            'coupon_status' => '优惠券状态修改',
            'export' => '优惠券导出',
            'bread' => '面包券列表',
            'bread_form' => '添加修改面包券',
            'bread_status' => '面包券状态修改',
            'bread_del' => '面包券删除',
            'newpeople' => '新人券列表',
            'newpeople_form' => '添加修改新人券',
            'newpeople_status' => '新人券状态修改',
            'newpeople_del' => '新人券删除',
            'weimengcard' => '微盟会员卡'
        ]
    ],

    'admin/admin' => [
        'name' => '管理员管理',
        'action' => [
            'login' => '后台登录',
            'loginOut' => '退出登录',
            'user' => '用户管理',
            'user_form' => '添加修改用户',
            'user_del' => '删除用户',
            'user_status' => '修改用户状态',
            'role' => '角色管理',
            'role_form' => '添加修改角色',
            'role_status' => '修改角色状态',
            'role_del' => '删除角色',
            'rule' => '权限管理',
            'rule_actions' => '查看当前控制器下的方法',
            'rule_form' => '添加修改权限',
            'rule_del' => '删除权限',
            'classify' => '权限分类',
            'classify_form' => '添加修改权限分类',
            'log' => '登录日志',
        ]
    ],

];
