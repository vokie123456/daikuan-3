<?php

return [
    // 现有模块
    'moudle_type' => ['首页', '首页活动', '贷款', '秒放款'],

    // public的文件目录(软链接)
    'storage' => 'storage/',

    // 这里模块文字需与 'moudle_type' 一致
    'banner_position' => ['首页', '秒放款'],

    // 用户性别
    'sexs' => ['未知', '男', '女'],

    // 利率单位
    'rate_types' => ['日', '周', '月', '年'],

    // 推荐类型(对应的表名)
    'recomm_types' => [1 => 'users', 2 => 'agents'],

    // 推荐码键名
    'recomm' => 'recomm',

    // register url
    'register_path' => 'https://wangtougongshe.com/register/index.html',
    
    // 同一ip每日限制的条数
    'same_ip_day_number' => 3,

    // 重置后的密码
    'reset_password' => '123456',
];