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
            content: '',
        };
    }

    componentDidMount() {
        Utils.axios({
            url: Api.getcontactus,
            isAlert: false,
            method: 'get',
            key: 'data',
        }, (content) => {
            // console.log(result);
            this.setState({ content });
        });
    }

    changeValue = (e) => {
        this.setState({ 
            content: e.target.value,
        });
    }

    handleSubmit = (e) => {
        e.preventDefault();
        Utils.axios({
            url: Api.setcontactus,
            method: 'post',
            data: {
                content: this.state.content,
            },
        });
    }

    render() {
        let { 
            redirect, 
            content,
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
                    label="联系方式"
                >
                    <TextArea 
                        rows="12" 
                        value={content} 
                        onChange={this.changeValue} 
                    />
                </FormItem>

                <FormItem {...tailFormItemLayout}>
                    <Button type="primary" htmlType="submit">保存</Button>
                </FormItem>
            </Form>
        );
    }
}
