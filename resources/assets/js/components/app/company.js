import React from 'react';
import { Table, Input, Icon, Button, Popconfirm, message } from 'antd';

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
    };
    handleBlur = (e) => {
        let obj = {};
        let { value, editable, } = this.state;
        if(editable) obj.editable = false;
        if(!value) obj.value = this.props.value;
        if(obj.hasOwnProperty('editable') || obj.value) {
            this.setState(obj);
        }
    };
    check = () => {
        let { value, } = this.state;
        if(!value) {
            message.warning('名称不能为空!');
            this.editInput && this.editInput.focus();
            return;
        }
        this.setState({ editable: false });
        if (this.props.onChange) {
            this.props.onChange(value);
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
                    <div className="editable-cell-input-wrapper" style={{
                        paddingRight: '44px',
                    }}>
                        <Input
                            ref={ele => this.editInput = ele}
                            value={value}
                            onChange={this.handleChange}
                            onPressEnter={this.check}
                            //onBlur={this.handleBlur}
                        />
                        <Icon
                            type="close"
                            className="editable-cell-icon-check"
                            onClick={()=>{
                                this.setState({
                                    editable: false,
                                    value: this.props.value,
                                });
                            }}
                            style={{right: '20px', }}
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
        this.normal = true;
    }

    componentDidMount() {
        this.fetch();
    }

    componentWillUnmount() {
        this.normal = false;
    }
    //获取列表数据
    fetch = (params = {}) => {
        this._searchText = this.state.searchText;
        this.setState({ loading: true });
        Utils.axios({
            key: 'companies',
            url: Api.getAppCompanies,
            params: params,
            isAlert: false,
            method: 'get',
        }, (result) => {
            if(this.normal) {
                this.setState({
                    datas: result,
                    loading: false,
                    filterDropdownVisible: false,
                    showSearch: this.state.searchText ? true : false,
                });
            }
        });
    };
    //公司列表
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
    //添加公司
    handleAdd = (value) => {
        const { datas } = this.state;
        Utils.axios({
            url: Api.addAppCompany,
            data: {name: value,},
            key: 'company',
        }, (result) => {
            result['share_url'] =
            this.setState({
                companyName: '',
                datas: [...datas, result],
            });
        });
    }
    //更新公司名称
    onCellChange = (key, dataIndex) => {
        return (value) => {
            Utils.axios({
                url: Api.updateCompany,
                data: {
                    name: value,
                    id: key,
                },
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
    //删除公司
    onDelete = (key) => {
        Utils.axios({
            url: Api.delCompany + key,
            method: 'get',
        }, (result) => {
            const datas = [...this.state.datas];
            this.setState({ datas: datas.filter(item => item.id !== key) });
        });
    }
    //输入新公司名称
    onAddInputChange = (e) => {
        this.setState({ companyName: e.target.value });
    };
    //输入搜索的公司
    onSearchInputChange = (e) => {
        this.setState({ searchText: e.target.value });
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
            title: '推广地址',
            dataIndex: 'share_url',
            // sorter: true,
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
            <div className="divStyle">
                <div className="toolbar" style={{width: 400}}>
                    <div className="addBox">
                        <Search
                            ref={ele => this.addInput = ele}
                            onChange={this.onAddInputChange}
                            onSearch={this.handleAdd}
                            value={companyName}
                            enterButton="添加公司"
                            size="large"
                        />
                    </div>
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
                        showSizeChanger: true,
                        showQuickJumper: true,
                        total: datas.length,
                        showTotal: total => `共 ${total} 条记录`,
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