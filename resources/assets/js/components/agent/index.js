import React from 'react';
import { Link } from "react-router-dom";
import { Table, Input, Button, Icon, Tooltip, DatePicker, } from 'antd';

import Api from '../public/api';
import Utils from '../public/utils';
import { BannerPositions } from '../public/global';

const { RangePicker } = DatePicker;
const Search = Input.Search;

export default class Agents extends React.Component {
    constructor(props) {
        super(props);
        this.state = {
            total: 0,
            datas: [],
            loading: false,
        };
        
        this.pagination = {};
        this.filter = {};
        this.sort = {};
        this.search = '';
        this.starttime = null;
        this.endtime = null;
    }

    componentDidMount() {
        this.fetch();
    }
    //获取列表数据
    fetch = (params = {}) => {
        this.setState({ loading: true });
        Utils.axios({
            key: 'agents',
            url: Api.getAgents,
            params: params,
            isAlert: false,
            method: 'get',
        }, (result) => {
            // console.log(result);
            if(result) {
                this.setState({
                    total: result.total,
                    datas: result.rows,
                    loading: false,
                });
            }
        });
    };
    //代理商列表
    getAgentList = (search = '') => {
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
        if(this.starttime) params.stime = this.starttime;
        if(this.endtime) params.etime = this.endtime;
        // console.log(params);
        this.fetch(params);
    };

    render() {
        const { datas, loading, total, } = this.state;
        const columns = [{
            title: '代理商名称',
            dataIndex: 'name',
            sorter: true,
            render: (text, record) => {
                let username = record.username || '';
                return (
                    <Tooltip title={record.note}>
                        <span>{text + `(${username})`}</span>
                    </Tooltip>
                );
            }
        }, {
            title: '推广链接',
            dataIndex: 'share_url',
            render: (text, record) => {
                return <Input type="text" defaultValue={text} />;
            }
        }, {
            title: '上级',
            dataIndex: 'parent',
            render: (text, record) => {
                if(text && text.name) return text.name;
                else return '-';
            }
        }, {
            title: '添加时间',
            dataIndex: 'created_at',
            sorter: true,
        }, {
            title: '注册人数',
            dataIndex: 'register',
        }, {
            title: '激活人数',
            dataIndex: 'activate',
        }, {
            title: '操作',
            render: (text, record) => {
                return (
                    <a href={"/agents/login/" + record.id} target="_blank">
                        <Icon type="disconnect" />
                    </a>
                );
            },
        }];
        return (
            <div className="webkit-flex">
                <div className="toolbar">
                    <div className="addBox">
                        <Button type="primary" size="large">
                            <Link to={`/agents/0`}>添加</Link>
                        </Button>
                    </div>
                    <RangePicker
                        size="large"
                        showTime={{ format: 'HH:mm' }}
                        format="YYYY-MM-DD HH:mm"
                        placeholder={['Start Time', 'End Time']}
                        onChange={(value) => {
                            if(value && value[0]) {
                                this.starttime = value[0].format('YYYY-MM-DD HH:mm');
                            }else {
                                this.starttime = null;
                            }
                            if(value && value[1]) {
                                this.endtime = value[1].format('YYYY-MM-DD HH:mm');
                            }else {
                                this.endtime = null;
                            }
                        }}
                    />
                    <div className="searchBox">
                        <Search
                            onSearch={this.getAgentList}
                            enterButton="Search"
                            size="large"
                            placeholder="输入代理商名称搜索"
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
                        this.getAgentList();
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
