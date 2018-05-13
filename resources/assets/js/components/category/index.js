import React from 'react';
import { Link } from "react-router-dom";
import { Table, Input, Icon, Button, Switch, Popconfirm, message } from 'antd';

import Api from '../public/api';
import Utils from '../public/utils';

const Search = Input.Search;

class Categories extends React.Component {
    constructor(props) {
        super(props);
        this.state = {
            total: 0,
            datas: [],
            loading: false,
            filterDropdownVisible: false,
        };
        
        this.pagination = {};
        this.filter = {};
        this.sort = {};
        this.search = '';

        const { params = {} } = this.props.match;
        this.type_id = (params.type && params.type == 1) ? 1 : 0;
    }

    componentDidMount() {
        this.fetch();
    }

    componentWillReceiveProps(nextProps) {
        // console.log(nextProps);
        const { params = {} } = nextProps.match;
        this.type_id = (params.type && params.type == 1) ? 1 : 0;
        this.fetch();
    }
    //获取列表数据
    fetch = (params = {}) => {
        this.setState({ loading: true });
        params.search = params.search || {};
        params.search.type = this.type_id;
        Utils.axios({
            key: 'category',
            url: Api.getCategories,
            params: params,
            isAlert: false,
            method: 'get',
        }, (result) => {
            this.setState({
                total: result.total,
                datas: result.rows,
                loading: false,
                filterDropdownVisible: false,
            });
        });
    };
    //类别列表
    getCategoryList = (search = '') => {
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
            key: 'status',
            url: Api.updateCategoryStatus,
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
    //删除类别
    onDelete = (id) => {
        Utils.axios({
            key: 'ret',
            url: Api.delCategory + id,
            method: 'get',
        }, (result) => {
            const datas = [...this.state.datas];
            this.setState({ datas: datas.filter(item => item.id !== id) });
        });
    }

    render() {
        const { datas, total, showSearch, companyName, loading, } = this.state;
        let columns = [{
            title: '类别名称',
            dataIndex: 'name',
            sorter: true,
        }, {
            title: '显示位置',
            dataIndex: 'type',
            sorter: true,
            render: (value, record) => {
                return record.type_name ? record.type_name : '';
            }
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
                        checked={value ? true : false}
                        onChange={value => this.onChangeStatus(value, record.id)}
                    />
                );
            }
        }, {
            title: '操作',
            render: (text, record) => {
                return (
                    <Button.Group>
                        <Button>
                            <Link to={'/category/' + this.type_id + '/form/' + record.id}>Edit</Link>
                        </Button>
                        <Button>
                            <Popconfirm title="确定要删除?" onConfirm={() => this.onDelete(record.id)}>
                                <a href="#">Delete</a>
                            </Popconfirm>
                        </Button>
                    </Button.Group>
                );
            },
        }];

        if(this.type_id == 1) {
            columns.unshift({
                title: '图标',
                dataIndex: 'image',
                render: (value, record) => {
                    return (
                        value ? <img src={value} style={styles.icon} /> : <div style={styles.emptyIcon}></div>
                    );
                }
            });
        }

        return (
            <div className="webkit-flex">
                <div className="toolbar">
                    <div className="addBox">
                        <Button type="primary" size="large">
                            <Link to={`/category/${this.type_id}/form`}>添加</Link>
                        </Button>
                    </div>
                    <div className="searchBox">
                        <Search
                            onSearch={this.getCategoryList}
                            enterButton="Search"
                            size="large"
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
                        this.getCategoryList();
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

export default Categories;