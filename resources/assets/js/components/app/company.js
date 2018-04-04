import React from 'react';
import { Table, Input, Icon, Button, Popconfirm } from 'antd';

import Api from '../public/api';
import Utils from '../public/utils';

const Search = Input.Search;

class EditableCell extends React.Component {
    state = {
        value: this.props.value,
        editable: false,
    }
    handleChange = (e) => {
        const value = e.target.value;
        this.setState({ value });
    }
    check = () => {
        this.setState({ editable: false });
        if (this.props.onChange) {
            this.props.onChange(this.state.value);
        }
    }
    edit = () => {
        this.setState({ editable: true });
    }
    render() {
        const { value, editable } = this.state;
        return (
            <div className="editable-cell">
                {editable ?
                    <div className="editable-cell-input-wrapper">
                        <Input
                            value={value}
                            onChange={this.handleChange}
                            onPressEnter={this.check}
                        />
                        <Icon
                            type="check"
                            className="editable-cell-icon-check"
                            onClick={this.check}
                        />
                    </div> :
                    <div className="editable-cell-text-wrapper">
                        {value || ' '}
                        <Icon
                            type="edit"
                            className="editable-cell-icon"
                            onClick={this.edit}
                        />
                    </div>
                }
            </div>
        );
    }
}

class AppCompany extends React.Component {
    constructor(props) {
        super(props);
        this.state = {
            datas: [],
            loading: false,
            filterDropdownVisible: false,
            searchText: '',
            filtered: false,
            companyName: '',
            showSearch: false,
        };
        this.pagination = {};
        this.filter = {};
        this.sort = {};
    }

    componentDidMount() {
        this.fetch();
    }

    fetch = (params = {}) => {
        this._searchText = this.state.searchText;
        this.setState({ loading: true });
        Utils.axios(Api.getAppCompanies, params, (result) => {
            this.setState({
                datas: result,
                loading: false,
                filterDropdownVisible: false,
                showSearch: this.state.searchText ? true : false,
            })
        }, 'companies', false, 'get');
    };

    onCellChange = (key, dataIndex) => {
        return (value) => {
            Utils.axios(Api.updateCompany, {
                name: value,
                id: key,
            }, (result) => {
                const datas = [...this.state.datas];
                const target = datas.find(item => item.key === key);
                if (target) {
                    target[dataIndex] = value;
                    this.setState({ datas });
                }
            });
        }
    }

    onDelete = (key) => {
        const datas = [...this.state.datas];
        this.setState({ datas: datas.filter(item => item.key !== key) });
    }

    // 添加公司
    handleAdd = (value) => {
        const { datas } = this.state;

        Utils.axios(Api.addAppCompany, {
            name: value,
        }, (result) => {
            this.setState({
                companyName: '',
                datas: [...datas, result],
            });
        }, 'company');
    }

    onAddInputChange = (e) => {
        this.setState({ companyName: e.target.value });
    };

    onSearchInputChange = (e) => {
        this.setState({ searchText: e.target.value });
    };

    getCompanyList = () => {
        let order = this.sort.order || '';
        if(order == 'descend') order = 'desc';
        if(order == 'ascend') order = 'asc';
        let limit = this.pagination.pageSize || '';
        let offset = limit ? ((this.pagination.current || 1) - 1) * limit : '';
        let params = {
            'order': order,
            'sort': this.sort.field || '',
            'offset': offset,
            'limit': limit,
            'search': {
                name: this.state.searchText,
            },
        };
        // console.log(params);
        this.fetch(params);
    };

    render() {
        const { datas, searchText, showSearch, companyName, filtered } = this.state;
        const columns = [{
            title: '公司名称',
            dataIndex: 'name',
            width: '30%',
            render: (text, record) => (
                <EditableCell
                    value={text}
                    onChange={this.onCellChange(record.id, 'name')}
                />
            ),
            sorter: true,
            filterDropdown: (
                <div className="custom-filter-dropdown">
                    <Search
                        ref={ele => this.searchInput = ele}
                        placeholder="Search name"
                        onSearch={this.getCompanyList}
                        onChange={this.onSearchInputChange}
                        value={searchText}
                        enterButton
                    />
                </div>
                ),
            filterIcon: <Icon type="search" style={{ color: filtered ? '#108ee9' : '#aaa' }} />,
            filterDropdownVisible: this.state.filterDropdownVisible,
            onFilterDropdownVisibleChange: (visible) => {
                this.setState({
                    filterDropdownVisible: visible,
                }, () => {
                    this.searchInput && this.searchInput.focus();
                });
            },
        }, {
            title: '添加时间',
            dataIndex: 'created_at',
        }, {
            title: '操作',
            render: (text, record) => {
                return (
                    <Popconfirm title="Sure to delete?" onConfirm={() => this.onDelete(record.id)}>
                        <a href="#">Delete</a>
                    </Popconfirm>
                );
            },
        }];
        return (
            <div>
                <div className="companyBox">
                    <Search
                        ref={ele => this.addInput = ele}
                        onChange={this.onAddInputChange}
                        onSearch={this.handleAdd}
                        value={companyName}
                        enterButton="添加公司"
                        size="large"
                    />
                </div>
                {showSearch ?
                    <div className="searchMart">
                        正在搜索 '{this._searchText && this._searchText}'
                    </div> :
                    null
                }
                <Table 
                    bordered
                    size="middle"
                    dataSource={datas}
                    loading={this.state.loading}
                    rowKey={record => record.id}
                    columns={columns}
                    pagination={{
                        //pageSize: 3,
                        showSizeChanger: true,
                        showQuickJumper: true,
                    }}
                    onChange={(pagination, filter, sort) => {
                        this.pagination = pagination;
                        this.filter = filter;
                        this.sort = sort;
                        this.getCompanyList();
                    }}
                />
            </div>
        );
    }
}

export default AppCompany;