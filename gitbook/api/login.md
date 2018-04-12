# 登录


> url: {{ book.host }}api/login

```PHP
@method POST

@param  String  telephone  手机
@param  String  password   密码

@return
{
    "errno": 0,
    "error": "登录成功",
    "token": "eyJ0eXAiOiJKV1QiLCJ..."
}
```

+ `errno` 登录结果: 0 登录成功, 返之不成功
+ `error` 结果说明
+ `token` 用户token, 请保存在本地
