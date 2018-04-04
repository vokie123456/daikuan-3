/**
 | -------------------------------------
 |  常用工具
 | -------------------------------------
 |
 |
 |
 */


import { message, } from 'antd';

var utils = {
    /**
     * 重新封装axios方法, 统一返回格式
     * 
     * @param  String    url       请求的url地址
     * @param  Object    datas     请求参数
     * @param  Function  callback  回调函数
     * @param  String    key       返回数据的键名
     * @param  Bool      isAlert   是否提示
     * @param  Int       type
     *         ret = 1 : 只在成功时才回调
     *         ret = 2 : 不管正确和失败都回调
     * @param  String    method    请求类型 post/get
     * @return Void
     */
    axios: (url, datas, callback, key = null, isAlert = true, method = 'post', type = 1) => {
        axios({
            method: method,
            url: url,
            params: datas || {},
        })
        .then((res) => {
            if(res.status == 200 && res.data) {
                let result = res.data;
                if(isAlert && result.error) {
                    if(result.errno == 0) message.success(result.error);
                    else message.error(result.error);
                }
                // 回调函数
                if(callback && ((result.errno == 0 && type == 1) || type == 2)) {
                    callback((key && result[key]) ? result[key] : result);
                }
            }
        })
        .catch((error) => {
            console.log(error);
        });
    },
};

export default utils;