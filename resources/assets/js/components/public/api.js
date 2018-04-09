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
    //获取APP列表
    getApps: host + 'getapps',
    //获取单个APP信息
    getApp: host + 'getapp',
    //添加APP
    addApp: host + 'appstore',
    //更新App
    updateApp: host + 'updateapp',
    //更新App状态
    updateAppStatus: host + 'updateappstatus',
    //删除App
    deletaApp: host + 'deleteapp',
};

export default urls;