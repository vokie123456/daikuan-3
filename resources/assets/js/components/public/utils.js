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
     * @param  config  Object  配置参数
     * - String  url      请求的url地址, 默认: null,
     * - Object  params   请求参数(get), 默认: {},
     * - String  key      返回数据的键名, 默认: null,
     * - String  method   请求类型 post/get, 默认: 'post',
     * - Bool    isAlert  是否提示, 默认: true
     * - Bool    debug    是否打印返回数据, 默认: false,
     * - Object  data     请求参数(post), 默认: {},
     * - Object  headers  请求头, 默认: {},
     * - Bool    force    是否强制执行回调, 默认: false,
     * @param  success  Function  成功回调
     * @param  fail     Function  失败回调, 当为true时执行成功回调
     * @return Void
     */
    axios: (config = {}, success = null, fail = null) => {
        let _config = {
            url: null,
            key: null,
            isAlert: true,
            method: 'post',
            params: {},
            data: {},
            debug: false,
            headers: {},
            force: false,
        };
        (config instanceof Object) && Object.assign(_config, config);
        if(!_config.url || typeof(_config.url) != 'string') return;
        axios({
            method: _config.method,
            url: _config.url,
            params: _config.params,
            data: _config.data,
            headers: _config.headers,
        })
        .then((res) => {
            if(res.data) {
                let result = res.data;
                if(_config.isAlert && result.error) {
                    if(result.errno == '0') message.success(result.error);
                    else message.error(result.error);
                }
                let data = (_config.key && result[_config.key]) ? result[_config.key] : result;
                if(result.errno == '0' || fail === true || _config.force) {  //成功或强制执行
                    success && success(data);
                }else if(fail) {  //失败
                    fail(data);
                }
            }else {
                console.log('axios失败: ', res);
            }
        })
        .catch((error) => {
            console.log('axios错误: ', error);
            message.error(String(error));
        });
    },
};

export default utils;