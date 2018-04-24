import React from 'react';
import { Link } from "react-router-dom";
import { Table, Input, Icon, Button, Switch, Popconfirm, message } from 'antd';

import Api from '../public/api';
import Utils from '../public/utils';

const Search = Input.Search;

class Apps extends React.Component {
    constructor(props) {
        super(props);
        this.state = {
            datas: [],
            loading: false,
            filterDropdownVisible: false,
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
            key: 'apps',
            url: Api.getApps,
            params: params,
            isAlert: false,
            method: 'get',
        }, (result) => {
            // console.log(result);
            this.setState({
                datas: result,
                loading: false,
                filterDropdownVisible: false,
            })
        });
    };
    //app列表
    getAppList = (search = '') => {
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
            url: Api.updateAppStatus,
            data: {
                id: id,
                status: value ? 1 : 0,
            },
        });
    }
    //删除app
    onDelete = (id) => {
        Utils.axios({
            key: 'ret',
            url: Api.deletaApp + id,
            method: 'get',
        }, (result) => {
            const datas = [...this.state.datas];
            this.setState({ datas: datas.filter(item => item.id !== id) });
        });
    }

    render() {
        const { datas, } = this.state;
        const columns = [{
            title: 'ID',
            dataIndex: 'id',
        }, {
            title: 'APP图标',
            dataIndex: 'appicon',
            render: (value, record) => {
                return (
                    value ? 
                        <img src={value} style={styles.icon} /> : 
                        <div style={styles.emptyIcon}></div>
                );
            }
        }, {
            title: 'APP名称',
            dataIndex: 'name',
            sorter: true,
        }, {
            title: '公司名称',
            dataIndex: 'company_name',
            sorter: true,
        }, {
            title: '添加时间',
            dataIndex: 'created_at',
            sorter: true,
        }, {
            title: '当前状态',
            dataIndex: 'status',
            sorter: true,
            render: (value, record) => {
                return (
                    <Switch 
                        checkedChildren="开启" 
                        unCheckedChildren="关闭" 
                        defaultChecked={value ? true : false}
                        onChange={value => this.onChangeStatus(value, record.id)}
                    />
                );
            }
        }, {
            title: '操作',
            render: (text, record) => {
                return (
                    <Button>
                        <Link to={'/apps/update/' + record.id}>Edit</Link>
                    </Button>
                );
            },
        }];
        return (
            <div>
                <div className="toolbar">
                    <div className="searchBox">
                        <Search
                            onSearch={this.getAppList}
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
                    }}
                    onChange={(pagination, filter, sort) => {
                        this.pagination = pagination;
                        this.filter = filter;
                        this.sort = sort;
                        this.getAppList();
                    }}
                />
            </div>
        );
    }
}

const styles = {};
styles.icon = {
    width: '60px',
    maxHeight: '60px',
    margin: '-8px 0',
    borderRadius: '3px',
};
styles.emptyIcon = {
    width: '60px',
    height: '60px',
    backgroundColor: '#eee',
    margin: '-8px 0',
    borderRadius: '3px',
};

export default Apps;