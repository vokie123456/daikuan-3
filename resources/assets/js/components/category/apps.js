import React from 'react';
import { Transfer, Select } from 'antd';

import Api from '../public/api';
import Utils from '../public/utils';

export default class Apps extends React.Component {
    constructor(props) {
        super(props);
        this.state = {
            targetKeys: [],
            dataSource: [],
            categories: [],
            category_id: '',
        };
    }
    componentDidMount() {
        this.getCategories()
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
    
    getDatas = (id) => {
        if(id && id > 0) {
            Utils.axios({
                url: Api.getCategoryApps + id,
                method: 'get',
                key: 'datas',
                isAlert: false,
            }, (result) => {
                const targetKeys = result.target || [];
                const dataSource = result.source || [];
                this.setState({ 
                    targetKeys,
                    dataSource,
                    category_id: id,
                });
            });
        }
    }

    filterOption = (search, option) => {
        return (option.company_name.indexOf(search) > -1 || option.app_name.indexOf(search) > -1);
    }

    handleChange = (targetKeys) => {
        Utils.axios({
            url: Api.setCategoryApps,
            method: 'post',
            data: {
                selected: targetKeys,
                category_id: this.state.category_id,
            },
            key: 'ret',
        }, (result) => {
            this.setState({ targetKeys });
        });
    }

    render() {
        const { targetKeys, dataSource, categories, category_id } = this.state;
        // if(!category_id) return null;
        return (
            <div style={{ width: '80%', margin: '10px auto'}}>
                <Select 
                    defaultValue={0} 
                    onChange={this.getDatas}
                    style={{ marginBottom: 30, width: 300, }}
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
                <Transfer
                    showSearch
                    dataSource={dataSource}
                    filterOption={this.filterOption}
                    targetKeys={targetKeys}
                    onChange={this.handleChange}
                    render={item => (item.company_name + ' - ' + item.app_name)}
                    listStyle={{
                        width: '44%',
                        height: 520,
                    }}
                />
            </div>
        );
    }
}
