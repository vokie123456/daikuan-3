import React from 'react';
import { Redirect, } from "react-router-dom";
import { 
    Form, 
    Input, 
    Tooltip, 
    Icon,
    Button,
    InputNumber,
    Switch,
    message,
    Radio,
} from 'antd';

import Api from '../public/api';
import Utils from '../public/utils';

const FormItem = Form.Item;
const { TextArea } = Input;

export default class Version extends React.Component {
    constructor() {
        super();
        this.state = {
            redirect: null,
            datas: {},
            systype: 0,
        };
        this.types = ['android', 'ios'];
    }

    componentDidMount() {
        Utils.axios({
            url: Api.getVersion,
            isAlert: false,
            method: 'get',
        }, (result) => {
            // console.log(result);
            let ios = result.ios || {};
            let android = result.android || {};
            if(!ios.details) ios.details = '发现新版本, 请更新!';
            if(!android.details) android.details = '发现新版本, 请更新!';
            this.setState({datas: {
                ios,
                android,
            }});
        });
    }

    handleChange = (e) => {
        let value = e.target.value;
        this.setState({ systype: value });
    }

    setDataVaule = (key, value) => {
        let { systype, datas } = this.state;
        if(this.types[systype]) {
            if(!datas[this.types[systype]]) datas[this.types[systype]] = {};
            datas[this.types[systype]][key] = value;
            this.setState({ datas });
        }
    }

    getData = () => {
        let { datas, systype, } = this.state;
        return this.types[systype] && datas[this.types[systype]] ? datas[this.types[systype]] : {};
    };

    handleSubmit = (e) => {
        e.preventDefault();
        let _data = this.getData();
        if(parseInt(_data.version) <= 0) {
            message.warning('请输入版本号!');
        }else if(!_data.url) {
            message.warning('请输入下载地址!');
        }else if(!_data.details) {
            message.warning('请输入更新说明!');
        }else {
            Utils.axios({
                url: Api.addVersion,
                method: 'post',
                data: {
                    type: this.state.systype,
                    version: _data.version,
                    url: _data.url,
                    plist: _data.plist || '',
                    details: _data.details,
                    isForce: _data.isForce ? 1 : 0,
                },
            });
        }
    }

    render() {
        let { 
            redirect, 
            datas,
            systype,
        } = this.state;
        if(redirect) return <Redirect to={redirect} />;
        const _data = this.getData();
        const formItemLayout = {
            labelCol: {
                xs: { span: 24 },
                sm: { span: 8 },
            },
            wrapperCol: {
                xs: { span: 24 },
                sm: { span: 16 },
            },
        };
        const tailFormItemLayout = {
            wrapperCol: {
                xs: {
                    span: 24,
                    offset: 0,
                },
                sm: {
                    span: 16,
                    offset: 8,
                },
            },
        };
        return (
            <Form 
                className="formStyle" 
                layout="horizontal"
                onSubmit={this.handleSubmit}
            >
                <FormItem
                    label="手机系统"
                    {...formItemLayout}
                >
                    <Radio.Group value={systype} onChange={this.handleChange}>
                        <Radio.Button value={0}>Android</Radio.Button>
                        <Radio.Button value={1}>Ios</Radio.Button>
                    </Radio.Group>
                </FormItem>
                <FormItem
                    {...formItemLayout}
                    label="版本号"
                >
                    <InputNumber
                        min={0}
                        max={99999}
                        precision={0}
                        value={_data.version}
                        onChange={val => this.setDataVaule('version', val)}
                    />
                </FormItem>
                <FormItem
                    {...formItemLayout}
                    label='下载地址'
                >
                    <Input maxLength={255} value={_data.url} onChange={(e) => this.setDataVaule('url', e.target.value)} />
                </FormItem>
                {systype == 1 ?
                    <FormItem
                        {...formItemLayout}
                        label='plist文件地址'
                    >
                        <Input maxLength={255} value={_data.plist} onChange={(e) => this.setDataVaule('plist', e.target.value)} />
                    </FormItem> :
                    null
                }
                <FormItem
                    {...formItemLayout}
                    label="更新说明"
                >
                    <TextArea 
                        rows="8" 
                        value={_data.details} 
                        onChange={(e) => this.setDataVaule('details', e.target.value)} 
                    />
                </FormItem>
                <FormItem
                    {...formItemLayout}
                    label="是否强制更新"
                >
                    <Switch 
                        checked={_data.isForce === undefined ? true : !!_data.isForce} 
                        onChange={val => this.setDataVaule('isForce', val)} 
                    />
                </FormItem>

                <FormItem {...tailFormItemLayout}>
                    <Button type="primary" htmlType="submit">保存</Button>
                </FormItem>
            </Form>
        );
    }
}
