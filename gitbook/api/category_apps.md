# 类内APP


可用于几乎所有的APP列表接口  
首页: 广告下图标内的app列表, 今日推荐、热门搜索等类别的更多数据   
贷款: 单次加载  
秒放货: 单次加载


> url: {{ book.host }}api/category/apps/{id}?page={page}

```PHP
@method GET

@token  [不需要]

@param  Int  id    必须, 类别id
@param  Int  page  非必须, 页码

@return
{
    "errno": 0,
    "error": "获取成功",
    "datas": {
        "id": 3,
        // 类别名称
        "name": "今日推荐",
        // 类别图片
        "image": "",
        // 该类下的APP
        "apps": [
            // APP数据
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
            // 当前页码
            "current_page": 1,
            // 每页数量
            "per_page": 15,
        ],
    },
}
```