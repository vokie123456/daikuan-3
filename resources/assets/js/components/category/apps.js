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
            url: Api.getCategories,
            params: {
                search: {type: -1},
            },
            isAlert: false,
            method: 'get',
        }, (result) => {
            if(result && result.length) {
                this.setState({
                    categories: result,
                });
                this.getDatas(result[0].id);
            }
        });
    };
    
    getDatas = (id) => {
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
        if(!category_id) return null;
        return (
            <div style={{ width: '80%', margin: '10px auto'}}>
                <Select 
                    defaultValue={category_id} 
                    onChange={this.getDatas}
                    style={{ marginBottom: 30, width: 200, }}
                >
                    {categories.map((item, index) => {
                        return <Select.Option key={item.id} value={item.id}>{item.name}</Select.Option>;
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
