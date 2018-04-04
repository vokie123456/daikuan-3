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
    //获取app的公司
    getAppCompanies: host + 'getappcompanies',
    //添加app的公司
    addAppCompany: host + 'addappcompany',
    //更新app的公司
    updateCompany: host + 'updatecompany'
};

export default urls;