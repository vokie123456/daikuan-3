# 秒放款


> url: {{ book.host }}api/moudle/secloan

```PHP
@method GET

@token  [不需要]

@param  无

@return

{
    "errno": 0,
    "error": "获取成功",
    "datas": {
        // 与首页广告属性一致
        "banner": [
            {
                "id": 1,
                "name": "测试广告1",
                "type": 0,
                "app_id": 1,
                "url": "#",
                "image": "http://...",
            },
            ...
        ],
        // 与类内APP属性一致
        "category": {
            "id": 4,
            "name": "秒放货",
            "image": "",
            "apps": {
                "data": [
                    {
                        "id": 4,
                        "name": "测试APP",
                        // app图标
                        "icon": "http://...",
                        // 已注册人数
                        "apply_number": 66,
                        // 简介
                        "synopsis": "我是一段简介",
                        // 利率
                        "rate": 23.00,
                        // 利率单位对应的名称
                        "rate_type_name": "日",
                        // 最大可借金额
                        "money_max": 2999,
                        // 可借金额的范围(字符串)
                        "money_rand": "666-2999",
                        // 归还数据(字符串)
                        "term_rand": "7天-14天"
                    },
                    ...
                ],
                "current_page": 1,
                "per_page": 15,
            },
        },
    },
}
```

> 加载更多: 通过`datas`.`category`.`id` 请求 [类内APP](./category_apps.md) 的接口
