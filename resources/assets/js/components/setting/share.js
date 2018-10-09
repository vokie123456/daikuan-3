import React from 'react';
import { Redirect, } from "react-router-dom";
import { 
    Form, 
    Input, 
    Tooltip, 
    Icon,
    Button,
    message,
} from 'antd';

import Api from '../public/api';
import Utils from '../public/utils';

const FormItem = Form.Item;
const { TextArea } = Input;

export default class Us extends React.Component {
    constructor() {
        super();
        this.state = {
            redirect: null,
            wx_title: '',
            wx_content: '',
        };
    }

    componentDidMount() {
        Utils.axios({
            url: Api.getShare,
            isAlert: false,
            method: 'get',
            key: 'data',
        }, (ret) => {
            if(ret && ret.wx_title && ret.wx_content) {
                this.setState({ 
                    wx_title: ret.wx_title,
                    wx_content: ret.wx_content,
                });
            }
        });
    }

    changeValue = (key, value) => {
        let obj = {};
        obj[key] = value;
        this.setState(obj);
    }

    handleSubmit = (e) => {
        e.preventDefault();
        const { wx_title, wx_content, } = this.state;
        if(!wx_title) {
            message.error('标题不能为空');
        }else if(!wx_content) {
            message.error('内容不能为空');
        }else {
            Utils.axios({
                url: Api.setShare,
                method: 'post',
                data: {
                    wx_title,
                    wx_content,
                },
            });
        }
    }

    render() {
        let { 
            redirect, 
            wx_title,
            wx_content,
        } = this.state;
        if(redirect) return <Redirect to={redirect} />;
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
                onSubmit={this.handleSubmit}
            >
                <FormItem
                    {...formItemLayout}
                    label="标题"
                >
                    <Input
                        value={wx_title} 
                        onChange={(e) => this.changeValue('wx_title', e.target.value)} 
                    />
                </FormItem>
                <FormItem
                    {...formItemLayout}
                    label="内容"
                >
                    <Input
                        value={wx_content} 
                        onChange={(e) => this.changeValue('wx_content', e.target.value)} 
                    />
                </FormItem>

                <FormItem {...tailFormItemLayout}>
                    <Button type="primary" htmlType="submit">保存</Button>
                </FormItem>
            </Form>
        );
    }
}
