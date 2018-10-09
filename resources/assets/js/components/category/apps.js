import React from 'react';
import { 
    Transfer, 
    Select,
    Table, 
    Input, 
    Icon,
    Button,
    InputNumber,
    Checkbox,
} from 'antd';

import Api from '../public/api';
import Utils from '../public/utils';

const Search = Input.Search;

class AppTable extends React.Component {
    constructor(props) {
        super(props);
        this.state = {
            datas: [],
            loading: false,
            sortedInfo: null,
        };
        
        this.pagination = {};
        this.filter = {};
        this.sort = {};
        this.search = '';
        this.cate_id = 0;
        this.data = {};
        this.origin_datas = [];
        this.checked_only_add = false;
    }

    componentDidMount() {
        this.cate_id = this.props.cate_id;
        this.fetch();
    }

    componentWillReceiveProps(nextProps) {
        this.cate_id = nextProps.cate_id;
        this.fetch();
    }

    //获取列表数据
    fetch = (params = {}) => {
        if(this.cate_id <= 0) return;
        this.setState({ 
            loading: true,
            datas: [],
        });
        Utils.axios({
            key: 'datas',
            url: Api.getCategoryApps + this.cate_id,
            isAlert: false,
            method: 'get',
        }, (result) => {
            // console.log(result);
            this.setState({
                datas: this.getHandleData(this.checked_only_add, result || []),
                loading: false,
            });
        });
    };

    handleChange = (pagination, filters, sorter) => {
        this.setState({ 
            sortedInfo: sorter,
        });
    }

    handleClick = () => {
        let data = [];
        for(let item of this.state.datas) {
            if(item.is_checked) {
                data.push({
                    app_id: item.id,
                    sort: item.sort,
                });
            }
        }
        Utils.axios({
            url: Api.setCategoryApps,
            data: {
                category_id: this.cate_id,
                data,
            },
            isAlert: true,
        })
    };

    getHandleData = (checked, datas) => {
        let checked_datas = [];
        if(checked) {
            this.origin_datas = datas;
            for(let i in datas) {
                if(datas[i].is_checked) {
                    checked_datas.push(datas[i]);
                }
            }
        }else {
            let _datas = this.origin_datas.length ? this.origin_datas : datas;
            checked_datas = _datas.map((item, index) => {
                if(datas && datas.length) {
                    for(let i in datas) {
                        if(datas[i].id === item.id) {
                            return datas[i];
                        }
                    }
                }
                return item;
            });
        }
        return checked_datas;
    };

    render() {
        if((parseInt(this.cate_id) || 0) <= 0) return null;
        const { datas, sortedInfo, loading } = this.state;
        let sorter_info = sortedInfo || {};
        const columns = [{
            title: '排序序号',
            dataIndex: 'sort',
            render: (value, record) => {
                return (
                    <InputNumber 
                        min={0}
                        defaultValue={value}
                        onChange={(val) => {
                            this.setState({
                                datas: datas.map((item, index) => {
                                    return {
                                        ...item,
                                        sort: item.id == record.id ? val : item.sort,
                                    }
                                }),
                            });
                        }}
                    />
                );
            },
            sorter: (a, b) =>  a.sort - b.sort,
            sortOrder: sorter_info.columnKey === 'sort' && sorter_info.order,
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
            sorter: (a, b) => a.name.localeCompare(b.name, "zh"),
            sortOrder: sorter_info.columnKey === 'name' && sorter_info.order,
        }, {
            title: '添加时间',
            dataIndex: 'created_at',
            sorter: (a, b) => new Date(a.created_at) - new Date(b.created_at),
            sortOrder: sorter_info.columnKey === 'created_at' && sorter_info.order,
        }];
        let defaultSelectedRowKeys = [];
        for(let i in datas) {
            if(datas[i].is_checked) {
                defaultSelectedRowKeys.push(datas[i].id);
            }
        }
        const rowSelection = {
            selectedRowKeys:  defaultSelectedRowKeys,
            onChange: (selectedRowKeys, selectedRows) => {
                this.setState({
                    datas: datas.map((item, index) => {
                        return {
                            ...item,
                            is_checked: selectedRowKeys.find(val => val == item.id) ? true : false,
                        }
                    }),
                });
            },
            getCheckboxProps: record => ({
                value: record.id,
                // defaultChecked:  record.is_checked,
                checked: record.is_checked,
            }),
            onSelectAll: (selected, selectedRows, changeRows) => {
                this.setState({
                    datas: datas.map((item, index) => {
                        return {
                            ...item,
                            is_checked: selected,
                        }
                    }),
                });
            },
        };
        return (
            <div style={styles.body}>
                <div className="toolbar" style={{ justifyContent: 'space-between', }}>
                    <Checkbox onChange={(e) => {
                        let checked = e.target.checked;
                        this.checked_only_add = checked;
                        this.setState({ datas: this.getHandleData(checked, datas)});  
                    }}>只显示选中的行</Checkbox>
                    <Button type="primary" onClick={this.handleClick}>保存</Button>
                </div>
                <p>优先按序号倒序排列, 序号相同的按添加时间倒序排列。</p>
                <Table 
                    bordered
                    //size="middle"
                    dataSource={datas}
                    loading={loading}
                    rowKey={record => record.id}
                    columns={columns}
                    rowSelection={rowSelection}
                    pagination={false}
                    onChange={this.handleChange}
                />
            </div>
        );
    }
}

export default class Apps extends React.Component {
    constructor(props) {
        super(props);
        this.state = {
            categories: [],
            category_id: '',
        };
    }

    componentDidMount() {
        this.getCategories();
    }

    getCategories = () => {
        Utils.axios({
            key: 'category',
            url: Api.getCategoryGroup,
            isAlert: false,
            method: 'get',
        }, (result) => {
            if(result && result.length) {
                this.setState({
                    categories: result,
                });
                // this.getDatas(result[0].child[0].id || 0);
            }
        });
    };

    render() {
        const { categories, category_id } = this.state;
        return (
            <div style={{ width: '90%', margin: '10px auto'}}>
                <div style={{ marginBottom: 20, }}>
                    <Select 
                        defaultValue={0} 
                        onChange={category_id => this.setState({ category_id })}
                        style={{ width: 300, }}
                        size="large"
                    >
                        <Select.Option key={0} value={0}>
                            请选择类别
                        </Select.Option>
                        {categories.map((item, index) => {
                            return (
                                <Select.OptGroup key={index + 1} label={item.name}>
                                    {item.child.map((t, i) => {
                                        return (
                                            <Select.Option key={index + '-' + i} value={t.id}>
                                                {t.name}
                                            </Select.Option>
                                        );
                                    })}
                                </Select.OptGroup>
                            );
                        })}
                    </Select>
                </div>
                {category_id ? <AppTable cate_id={category_id} /> : null}
            </div>
        );
    }
}

var styles = {};
styles.body = {
    overflow: 'auto',
    marginBottom: 20,
};
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
