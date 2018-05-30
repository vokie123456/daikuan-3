import React from 'react';
import { Redirect, } from "react-router-dom";
import { 
    Form, 
    Input, 
    Tooltip, 
    Icon,
    Select,
    Button,
    Upload,
    InputNumber,
    Switch,
    message,
} from 'antd';

import Api from '../public/api';
import Utils from '../public/utils';
import { SortTypes, Moudles } from '../public/global';

const { TextArea } = Input;
const FormItem = Form.Item;
const Option = Select.Option;

class FormComponent extends React.Component {
    constructor(props) {
        super(props);
        this.state = {
            agents: [],
            redirect: null,
        };
    }

    componentDidMount() {
        Utils.axios({
            key: 'data',
            url: Api.getAllAgent,
            isAlert: false,
            method: 'get',
        }, (result) => {
            // console.log(result);
            if(result) this.setState({agents: result});
        });
    }

    handleSubmit = (e) => {
        e.preventDefault();
        const { form, inits } = this.props;
        form.validateFieldsAndScroll((err, values) => {
            if (!err) {
                // console.log('Received values of form: ', values);
                let formdata = new FormData();
                // let _id = (inits && inits.id) ? inits.id.value : null;
                // if(_id) formdata.append('id', _id);
                for(let i in values) {
                    let _value = typeof(values[i]) == 'undefined' ? '' : values[i];
                    if(i == 'status') _value = _value ? 1 : 0;
                    formdata.append(i, _value);
                }
                Utils.axios({
                    // url: _id ? Api.updateCategory : Api.addCategory,
                    url: Api.addAgent,
                    data: formdata,
                }, (result) => {
                    let redirect = '/agents';
                    this.setState({ redirect });
                }, (result) => {
                    if(result && result.errors) {
                        let errors = {};
                        let _error = null;
                        for(let j in result.errors) {
                            if(result.errors[j]) {
                                errors[j] = {
                                    value: values[j],
                                };
                                if(typeof(result.errors[j]) == 'string') {
                                    if(!_error) _error = result.errors[j];
                                    errors[j]['errors'] = [new Error(result.errors[j])];
                                }else if(result.errors[j][0]) {
                                    if(!_error) _error = result.errors[j][0];
                                    errors[j]['errors'] = [new Error(result.errors[j][0])];
                                }
                            }
                        }
                        form.setFields(errors);
                        message.warning(_error || '保存失败!');
                    }
                });
            }
        });
    }

    render() {
        const { redirect, agents } = this.state;
        if(redirect) return <Redirect to={redirect} />;

        const { getFieldDecorator, getFieldValue } = this.props.form;
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
            <Form className="formStyle" onSubmit={this.handleSubmit}>
                <FormItem
                    {...formItemLayout}
                    label="名称"
                >
                    {getFieldDecorator('name', {
                        rules: [{
                            required: true,
                            message: '请填写代理商的名称!',
                        }],
                    })(
                        <Input maxLength={45} />
                    )}
                </FormItem>
                <FormItem
                    {...formItemLayout}
                    label={(
                        <span>
                            登录名&nbsp;
                            <Tooltip title="建议使用手机号或邮箱(不能与他人重复)">
                                <Icon type="question-circle-o" />
                            </Tooltip>
                        </span>
                    )}
                >
                    {getFieldDecorator('username', {
                        rules: [{
                            required: true,
                            message: '请填写登录名!',
                        }],
                    })(
                        <Input maxLength={45} placeholder="手机/邮箱" />
                    )}
                </FormItem>
                <FormItem
                    {...formItemLayout}
                    label="密码"
                >
                    {getFieldDecorator('password', {
                        rules: [{
                            required: true,
                            message: '请填写登录的密码!',
                        }],
                    })(
                        <Input type="password" maxLength={32} />
                    )}
                </FormItem>
                <FormItem
                    {...formItemLayout}
                    label="确认密码"
                >
                    {getFieldDecorator('password_confirmation', {
                        rules: [{
                            required: true,
                            message: '请填写确认密码!',
                        }],
                    })(
                        <Input type="password" maxLength={32} />
                    )}
                </FormItem>
                <FormItem
                    {...formItemLayout}
                    label="上级"
                >
                    {getFieldDecorator('parent_id', {
                        initialValue: 0,
                    })(
                        <Select disabled={this.type_id == 1 ? true : false}>
                            <Option key={-1} value={0}>无</Option>
                            {agents.length ?
                                agents.map((item, index) => (
                                    <Option key={index} value={item.id}>{item.name}</Option>
                                )) :
                                null
                            }
                        </Select>
                    )}
                </FormItem>
                <FormItem
                    {...formItemLayout}
                    label="备注"
                >
                    {getFieldDecorator('note', {
                        initialValue: ''
                    })(
                        <TextArea rows="8" />
                    )}
                </FormItem>

                <FormItem {...tailFormItemLayout}>
                    <Button type="primary" htmlType="submit">保存</Button>
                </FormItem>
            </Form>
        );
    }
}

const FormInit = Form.create({
    mapPropsToFields(props) {
        let inits = props.inits || {};
        for(let i in inits) {
            inits[i] = Form.createFormField({value: inits[i]});
        }
        return inits;
    },
})(FormComponent);


export default class AgentForm extends React.Component {
    constructor(props) {
        super(props);
        this.state = {datas: null,}
    }

    componentDidMount() {
        // const { match } = this.props;
        // if(match && match.params && match.params.id) {
        //     Utils.axios({
        //         key: 'category',
        //         url: Api.getCategory + match.params.id,
        //         isAlert: false,
        //         method: 'get',
        //     }, (result) => {
        //         // console.log(result);
        //         this.setState({datas: result});
        //     });
        // }
    }

    render() {
        const { params = {} } = this.props.match;
        let datas = this.state.datas;
        let id = parseInt(params.id) || 0;
        if(id && !datas) return null;
        return <FormInit {...Object.assign({}, this.props, {inits: datas})} />
    }
};
