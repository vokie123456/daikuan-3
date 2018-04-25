import React from 'react';
import { Link } from "react-router-dom";
import { Table, Input, Icon, Button, Switch, Popconfirm, message } from 'antd';

import Api from '../public/api';
import Utils from '../public/utils';
import { RegisterTypes } from '../public/global';

const Search = Input.Search;

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
    }

    componentDidMount() {
        this.fetch();
    }
    //获取列表数据
    fetch = (params = {}) => {
        this.setState({ loading: true });
        Utils.axios({
            key: 'users',
            url: Api.getUsers,
            params: params,
            isAlert: false,
            method: 'get',
        }, (result) => {
            // console.log(result);
            this.setState({
                datas: result,
                loading: false,
                status: null,
            })
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
            'search': {
                name: search,
            },
        };
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
    //删除app
    onDelete = (id) => {
        // Utils.axios({
        //     key: 'ret',
        //     url: Api.delBanner + id,
        //     method: 'get',
        // }, (result) => {
        //     const datas = [...this.state.datas];
        //     this.setState({ datas: datas.filter(item => item.id !== id) });
        // });
    }

    render() {
        const { datas } = this.state;
        const columns = [{
            title: '姓名',
            dataIndex: 'name',
            sorter: true,
        }, {
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
                            <Link to={'#'}>Edit</Link>
                        </Button>
                        <Button>
                            <Link to={'#'}>重置密码</Link>
                        </Button>
                    </Button.Group>
                );
            },
        }];
        return (
            <div className="webkit-flex">
                <div className="toolbar">
                    <div className="searchBox">
                        <Search
                            onSearch={this.getUserList}
                            enterButton="Search"
                            size="large"
                        />
                    </div>
                </div>
                <Table 
                    bordered
                    //size="middle"
                    dataSource={datas}
                    loading={this.state.loading}
                    rowKey={record => record.id}
                    columns={columns}
                    pagination={{
                        showSizeChanger: true,
                        showQuickJumper: true,
                        total: datas.length,
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