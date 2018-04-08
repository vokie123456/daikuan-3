/**
 | -------------------------------------
 |  api url地址
 | -------------------------------------
 |
 |
 |
 */


var host = '/admin/';

var urls = {
    //根路径
    host: host,
    //获取登录的管理员信息
    getAdminInfo: host + 'getadmininfo',
    //获取app公司
    getAppCompanies: host + 'getappcompanies',
    //添加app公司
    addAppCompany: host + 'addappcompany',
    //更新app公司
    updateCompany: host + 'updatecompany',
    //删除app公司
    delCompany: host + 'delcompany',
    //添加APP
    addApp: host + 'appstore',
};

export default urls;