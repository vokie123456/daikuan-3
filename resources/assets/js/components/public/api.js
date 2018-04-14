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
    getAdminInfo: host + 'admin/info',
    //获取app公司
    getAppCompanies: host + 'companies',
    //添加app公司
    addAppCompany: host + 'company/create',
    //更新app公司
    updateCompany: host + 'company/update',
    //删除app公司
    delCompany: host + 'company/delete/',
    //获取APP列表
    getApps: host + 'getapps',
    //获取单个APP信息
    getApp: host + 'getapp/',
    //添加APP
    addApp: host + 'app/store',
    //更新App
    updateApp: host + 'app/update',
    //更新App状态
    updateAppStatus: host + 'appstatus/update',
    //删除App
    deletaApp: host + 'app/delete/',
    //获取所有类别
    getCategories: host + 'getcategories',
    //获取按组分类的所有类别
    getCategoryGroup: host + 'getcategories/group',
    //获取单个类别
    getCategory: host + 'getcategory/',
    //添加类别
    addCategory: host + 'category/create',
    //更新类别
    updateCategory: host + 'category/update',
    //更新类别状态
    updateCategoryStatus: host + 'categorystatus/update',
    //删除类别
    delCategory: host + 'category/delete/',
    //获取分类下的APP
    getCategoryApps: host + 'getcategoryapps/',
    //设置分类下的APP
    setCategoryApps: host + 'setcategoryapps',
    //添加广告
    addBanner: host + 'banner/create',
    //更新广告
    updateBanner: host + 'banner/update',
    //获取单个广告信息
    getBanner: host + 'getbanner/',
    //获取广告列表
    getBanners: host + 'getbanners',
    //更新广告状态
    updateBannerStatus: host + 'bannerstatus/update',
    //删除广告
    delBanner: host + 'banner/delete/',
};

export default urls;