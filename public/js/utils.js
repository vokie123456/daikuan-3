/*!
 * js常用工具库
 *
 * 常用的js函数,会在日常使用中逐渐添加
 * 注: 如有用的jquery库的,需在本库之前引入jquery
 * 
 */


//jquery插件
(function ($) {
    "use strict";

    //bootstrap-table
    $.fn.bs_table = function(option = {}) {
        var _option = {
            locale : "zh-CN",					// 中文语言
			// dataField : "rows", 				// 服务端返回数据键值 就是说记录放的键值是rows(默认)，分页时使用总记录数的键值为total
			// height : 500,						// 高度
            // search : false,						// 是否自动搜索 (searchText为默认搜索表单值), 默认: false
            // searchOnEnterKey : false,			// false: 自动搜索(默认), true: 回车搜索
			searchPlaceholder: '',              // 搜索提示文字 [自定义]
			pagination : true,					// 是否分页
			pageSize : 10,		                // 单页记录数
			pageList : [ 5, 10, 20, 50 ],       // 分页步进值
			sidePagination : "server",			// 服务端分页
			// dataType : "json",					// 期待返回数据类型
			sortOrder : 'desc',					// 定义默认排序
			// sortName : '',
			// searchAlign : "right",				// 查询框对齐方式, 默认: right
			showRefresh : true,					// 刷新按钮, 默认: false
			showColumns : true,					// 列选择按钮, 默认: false
            showToggle : false, 				// 显示切换视图, 默认: false
            buttonsAlign : "left",				// 按钮(刷新、列选择、显示切换)对齐方式, 默认: right
            method : "post",					// 请求方式
            // 发送到服务器的数据编码类型
			// 如果值为'application/json'(json字符串, 默认值)
			// PHP服务端必须用$GLOBALS['HTTP_RAW_POST_DATA'] 接收并json_decode解析
			contentType : 'application/x-www-form-urlencoded',
			// toolbar : toolbar,					// 指定工具栏
			toolbarAlign : "right",			    // 工具栏对齐方式, 默认: left
			// detailView : false,					// 是否显示详情折叠, 默认: false
			showInputPagination: true,			// 手动输入页码的输入框 [自定义]
			queryParams : function(params) {    // 参数传递
                return params;
            },
            onLoadSuccess: function(data) {     // 加载成功后触发的事件
				// $("[data-toggle='tooltip']").tooltip();
			},
        };
        _option = Object.assign(_option, option);
        $(this).bootstrapTable(_option);
    };

    //添加jquery扩展
    $.extend({
        /**
         * 重新封装ajax方法, 统一返回格式
         * 
         * @param  config  Object  配置参数
         * - String  url      请求的url地址, 默认: null,
         * - Object  params   请求参数, 默认: {},
         * - String  key      返回数据的键名, 默认: null,
         * - String  method   请求类型 post/get, 默认: 'post',
         * - Bool    isAlert  是否提示, 默认: true
         * - Bool    async    是否异步, 默认: true
         * - Bool    debug    是否打印返回数据, 默认: false,
         * - Bool    force    是否强制执行回调, 默认: false,
         * @param  success  Function  成功回调
         * @param  fail     Function  失败回调, 当为true时执行成功回调
         * @return Void
         */
        _ajax: function(config = {}, success = null, fail = null) {
            let _config = {
                url: null,
                key: null,
                isAlert: true,
                method: 'post',
                params: {},
                async: true,
                debug: false,
                force: false,
            };
            (config instanceof Object) && Object.assign(_config, config);
            if(!_config.url || typeof(_config.url) != 'string') return;
            if(!_config.params.skip_menu) _config.params.skip_menu = 1;
            $.ajax({
                type: _config.method,
                url: _config.url,
                async: _config.async,
                data: _config.params,
                timeout: 3000,
                dataType: 'json',
                success: function(result, status) {
                    if(result) {
                        if(_config.isAlert && result.error) {
                            if(layer) layer.msg(result.error);
                            else alert(result.error);
                        }
                        let data = _config.key ? result[_config.key] : result;
                        if(result.errno == 0 || fail === true || _config.force) {  //成功或强制执行
                            success && success(data);
                        }else if(fail) {  //失败
                            fail(result);
                        }
                    }else {
                        console.log('无返回数据');
                    }
                },
                error: function(xhr, textStatus, exc) {
                    console.log(textStatus, exc);
                    if(exc) {
                        if(layer) layer.msg(exc);
                        else alert(exc);
                    }
                }
            });
        },
        
        /**
         * 图片预览功能
         * 
         * @param  Element         _this     图片元素
         * @param  String|Element  parent    图片的根父元素
         * @param  String          title     图片标题
         * @param  Float           zoomout   缩小倍数
         * @param  Float           zoomin    放大倍数
         * @return Void
         */
        previewImage: function(_this, parent, title = null, zoomout= 0.6, zoomin = 1.6) {
            var src = _this.src || null;
            if(!parent || !src) return;
            title = title ? ('<h3>' + title + '</h3>') : '';
            var prewimg = 
                '<div class="fixedStyle">' +
                    title +
                    '<div class="zoomBtnBox">' +
                        '<button class="btn btn-primary" data-zoom="' + zoomout + '">' +
                            '<i class="fa fa-search-minus"></i> 缩小' +
                        '</button>' +
                        '<button class="btn btn-primary selected" data-zoom="1">' +
                            '<i class="fa fa-arrows"></i> 原图' +
                        '</button>' +
                        '<button class="btn btn-primary" data-zoom="' + zoomin + '">' +
                            '<i class="fa fa-search-plus"></i> 放大' +
                        '</button>' +
                    '</div>' +
                    '<img src="' + src + '" title="' + title + '" class="attrPrewImg img-thumbnail" />' +
                '</div>';
            if($(parent).length == 0) {
                $('body').append('<div id="' + parent.replace(/\W/g, '') + '">' + prewimg + '</div>');
            }else {
                $(parent).html(prewimg);
            }
            $(parent).css('display', 'block');
            var width = $(parent).find('.attrPrewImg').width();
            $('.zoomBtnBox button').click(function() {
                var zoom = parseFloat($(this).attr('data-zoom')) || 0;
                if(zoom > 0) {
                    // 无法撑大父容器, 超出部分不能出现滚动条
                    // $(parent).find('.attrPrewImg').css('transform', 'scale(' + zoom + ')');
                    // 设置宽度
                    $(parent).find('.attrPrewImg').width(width * zoom);
                    $(this).addClass('selected').siblings().removeClass('selected');
                    return false;
                }
            });
            $(parent).off("click").click(function() {
                $(this).css('display', 'none');
            });
        },
    });
})(jQuery);
