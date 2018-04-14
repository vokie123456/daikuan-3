# 首页


首页的接口为三个小接口的合集

> url: {{ book.host }}api/moudle/home

```PHP
@method GET

@token  [不需要]

@param  无

@return
{
    "errno": 0,
    "error": "获取成功",
    // 附带数据
    "datas": {
        // 广告数据
        "banners": [
            {
                // 广告id
                "id": 1,
                // 广告名称
                "name": "测试广告1",
                // 广告跳转类型: 0 app详情, 1 web页面
                "type": 0,
                // 跳转至的app id
                "app_id": 12,
                // 跳转至的web url
                "url": "#",
                // 图片
                "image": "http://...",
            },
            ...
        ],
        // 首页图标数据(顶部广告之下)
        "icons": [
            {
                "id": 1,
                // 名称
                "name": "天猫",
                // 图片
                "image": "http://...",
            },
            ...
        ],
        // 类别数据
        "category_apps": [
            {
                "id": 2,
                "name": "推荐",
                // 该类别下的APP
                "apps": [
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
            },
            ...
        ],
    },
}
```