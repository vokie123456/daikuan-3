# 文档说明


### 这是一个贷款超市的api文档


**全局说明**

1. 请把服务器地址定义成一个变量, 以便于随时更换。   
如: `$host = 'http://localhost';`

2. 该API采用统一的返回格式
```js
{
    "errno" : 0,   // 请求结果: 0 成功, 1 失败
    "error" : "",  // 结果说明
    "datas" : Any  // 附带数据, 键名不定
}
```

3. 登录成功会返回一个token, 请求需要token的接口时, 需把token放在header的`Authorization`中