
(function($) {
	$(document).ready(function() {
		//初始化日期插件
		//时间选择器
		laydate.render({
			elem: '#inputStartDate',
			type: 'datetime',
		});
		laydate.render({
			elem: '#inputEndDate',
			type: 'datetime',
        });

        //初始化表格
        var table = '#mytab';
        $(table).bs_table({
            toolbar: '#toolbar',
            queryParams: function(params) {
                var stime = $("#inputStartDate").val();
                var etime = $("#inputEndDate").val();
                var isActive = parseInt($("#sel-activate").val()) || 0;
                var search = {};
                if(stime) search['startTime'] = stime;
                if(etime) search['endTime']   = etime;
                if(isActive) search['isActive'] = isActive;
                return {
                    limit : params.limit,
                    offset: params.offset,
                    order : params.order,
                    search: JSON.stringify(search),
                    sort  : params.sort,
                    parent: parseInt($("#sel-parent").val()) || 0,
                };
            },
            onLoadSuccess: function(data) {
                var register_total = data.register_total || 0;
                var activate_total = data.activate_total || 0;
                $('#register_total').html('注册人数: ' + register_total + '人。');
                $('#activate_total').html('激活人数: ' + activate_total + '人。');
                $('#mark_text').html('请勿以搜索结果为人数统计。 至于为什么, 以你的智商就不用我多说了吧。');
            },
        });

        //点击搜索按钮搜索
		$("#btnSearch").click(function(){
			var stime = $("#inputStartDate").val();
			var etime = $("#inputEndDate").val();
            $(table).bootstrapTable('refresh');
		});

		//点击重置按钮
		$('#btnReset').click(function() {
			$("#sel-parent option").removeAttr("selected");
			$("#sel-activate option").removeAttr("selected");
			$('#inputStartDate').val('');
			$('#inputEndDate').val('');
		});
    });
})(jQuery);

//格式化激活状态
var formatActivate = function(value, row, index) {
	return value ? '已激活' : '未激活';
};
