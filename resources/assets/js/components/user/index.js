import React from 'react';
import { Link } from "react-router-dom";
import { 
    Table, 
    Input, 
    Icon, 
    Button, 
    Switch, 
    Popconfirm, 
    message,
    Select,
    DatePicker,
} from 'antd';
import moment from 'moment';
import 'moment/locale/zh-cn';
moment.locale('zh-cn');

import Api from '../public/api';
import Utils from '../public/utils';
import { RegisterTypes } from '../public/global';

const { RangePicker } = DatePicker;
const Search = Input.Search;
const Option = Select.Option;

class Users extends React.Component {
    constructor(props) {
        super(props);
        this.state = {
            datas: [],
            loading: false,
        };
        
        this.pagination = {};
        this.filter = {};
        this.sort = {};
        this.search = '';
        this.type = 'telephone';
        this.isActivate = 0;
        this.starttime = null;
        this.endtime = null;
    }

    componentDidMount() {
        this.fetch();
    }
    //获取列表数据
    fetch = (params = {}) => {
        this.setState({ loading: true });
        const recommer = this.props.recommer || null;
        params.search = params.search || {};
        params.search.isActive = this.isActivate;
        if(recommer) {
            params.search['user_recomm'] = recommer;
        }
        if(this.starttime) params.search.startTime = this.starttime;
        if(this.endtime) params.search.endtime = this.endtime;
        Utils.axios({
            key: 'users',
            url: Api.getUsers,
            params: params,
            isAlert: false,
            method: 'get',
        }, (result) => {
            // console.log(result);
            this.setState({
                total: result.total,
                datas: result.rows,
                loading: false,
                status: null,
            });
        });
    };
    //用户列表
    getUserList = (search = '') => {
        let limit = this.pagination.pageSize || '';
        let offset = limit ? ((this.pagination.current || 1) - 1) * limit : '';
        let order = this.sort.order || '';
        if(order == 'descend') order = 'desc';
        if(order == 'ascend') order = 'asc';
        let params = {
            'order': order,
            'sort': this.sort.field || '',
            'offset': offset,
            'limit': limit,
            'search': {},
        };
        params.search[this.type] = search;
        // console.log(params);
        this.fetch(params);
    };
    //切换状态
    onChangeStatus = (value, id) => {
        Utils.axios({
            url: Api.updateUserStatus,
            key: 'status',
            data: {
                id: id,
                status: value ? 1 : 0,
            },
        }, (result) => {
            let status = result !== undefined ? !!result : !value;
            this.setState({ 
                datas: this.state.datas.map((item, index) => {
                    return item.id == id ? Object.assign({}, item, {status: status}) : item;
                })
            });
        }, true);
    }
    //重置密码
    resetPassword = (id) => {
        Utils.axios({
            key: 'ret',
            url: Api.resetUserPasswrod,
            method: 'put',
            data: {id: id},
        });
    }

    render() {
        const { datas, total, loading, } = this.state;
        const columns = [{
            title: '手机',
            dataIndex: 'telephone',
        }, {
            title: '注册方式',
            dataIndex: 'recomm_type',
            sorter: true,
            render: (value, record) => {
                return RegisterTypes[value] ? RegisterTypes[value] : '';
            },
        }, {
            title: '是否激活',
            dataIndex: 'is_activate',
            render: (value, record) => value ? '已激活' : '未激活',
        }, {
            title: '当前状态',
            dataIndex: 'status',
            sorter: true,
            render: (value, record) => {
                return (
                    <Switch
                        checkedChildren="开启" 
                        unCheckedChildren="关闭"
                        // defaultChecked
                        checked={value ? true : false}
                        onChange={value => this.onChangeStatus(value, record.id)}
                    />
                );
            }
        }, {
            title: '注册时间',
            dataIndex: 'created_at',
            sorter: true,
        }, {
            title: '操作',
            render: (text, record) => {
                return (
                    <Button.Group>
                        <Button>
                            <Link to={'users/' + record.id}>查看</Link>
                        </Button>
                        <Button>
                            <Popconfirm title="确定要重置密码?" onConfirm={() => this.resetPassword(record.id)}>
                                <a href="#">重置密码</a>
                            </Popconfirm>
                        </Button>
                    </Button.Group>
                );
            },
        }];
        return (
            <div className="webkit-flex">
                <div className="toolbar">
                    <RangePicker
                        size="large"
                        showTime={{
                            defaultValue: [
                                moment('00:00:00', 'HH:mm:ss'), 
                                moment('00:00:00', 'HH:mm:ss'),
                            ]
                        }}
                        format="YYYY-MM-DD HH:mm:ss"
                        placeholder={['注册: 开始时间', '结束时间']}
                        onChange={(value) => {
                            if(value && value[0]) {
                                this.starttime = value[0].format('YYYY-MM-DD HH:mm:ss');
                            }else {
                                this.starttime = null;
                            }
                            if(value && value[1]) {
                                this.endtime = value[1].format('YYYY-MM-DD HH:mm:ss');
                            }else {
                                this.endtime = null;
                            }
                        }}
                    />
                    <Select
                        size="large"
                        style={{
                            width: 120,
                            marginLeft: 10,
                        }}
                        defaultValue={0}
                        onChange={(value) => this.isActivate = value}
                    >
                        <Option key={0} value={0}>是否激活</Option>
                        <Option key={1} value={1}>已激活</Option>
                        <Option key={2} value={2}>未激活</Option>
                    </Select>
                    <div className="searchBox">
                        <Search
                            onSearch={this.getUserList}
                            enterButton="Search"
                            size="large"
                            placeholder="输入手机号进行搜索"
                        />
                    </div>
                </div>
                <Table 
                    bordered
                    //size="middle"
                    dataSource={datas}
                    loading={loading}
                    rowKey={record => record.id}
                    columns={columns}
                    pagination={{
                        showSizeChanger: true,
                        showQuickJumper: true,
                        total: total,
                        showTotal: total => `共 ${total} 条记录`,
                    }}
                    onChange={(pagination, filter, sort) => {
                        this.pagination = pagination;
                        this.filter = filter;
                        this.sort = sort;
                        this.getUserList();
                    }}
                />
            </div>
        );
    }
}

const styles = {};
styles.icon = {
    maxHeight: '60px',
    margin: '-8px 0',
    borderRadius: '3px',
};
styles.emptyIcon = {
    maxHeight: '60px',
    backgroundColor: '#eee',
    margin: '-8px 0',
    borderRadius: '3px',
};

export default Users;