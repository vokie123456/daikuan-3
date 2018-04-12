# 贷款


此页面需要进行两次请求

1. 先获取切换栏的信息(主要为ID)
2. 再过ID请求 [类内APP](./category_apps.md) 的接口

> 请求id的url: {{ book.host }}api/moudle/loan

```PHP
@method GET

@token  [不需要]

@param  无

@return
{
    "errno": 0,
    "error": "获取成功",
    // 注: 如果请求的结果`datas`长度不等于2, 只取前2个
    "datas": [
        {
            "id": 3,
            "name": "热门TOP",
            "image": "",
        },
        {
            "id": 4,
            "name": "最新口子",
            "image": "",
        },
    ],
}
```

> 加载更多: 通过`datas[]`.`id` 请求 [类内APP](./category_apps.md) 的接口
