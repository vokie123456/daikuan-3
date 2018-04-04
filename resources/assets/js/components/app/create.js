import React from 'react';
import { 
    Form, 
    Input,
    Tooltip, 
    Switch,
    Icon,
    Select, 
    Rate,
    InputNumber,
    Button, 
    AutoComplete,
} from 'antd';

import SomeInput from './some_input';

const { TextArea } = Input;
const FormItem = Form.Item;
const Option = Select.Option;
const AutoCompleteOption = AutoComplete.Option;

class AppCreate extends React.Component {
    constructor(props) {
        super(props);
        this.state = {
            recommend: '2.5',
            status: true,
        }
    }

    handleSubmit = (e) => {
        e.preventDefault();
        this.props.form.validateFieldsAndScroll((err, values) => {
            if (!err) {
                console.log('Received values of form: ', values);
            }
        });
    };

    onchange = (value, key) => {
        if(value && key) {
            let obj = {};
            obj[key] = value;
            this.setState(obj);
        }
    }

    render() {
        const { getFieldDecorator, getFieldValue, setFieldsValue } = this.props.form;
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
        const _props = {
            getFieldDecorator,
            getFieldValue,
            setFieldsValue,
        };
        const selectDate1 = (
            <Select defaultValue="天" style={{ width: 60 }}>
                <Option value="天">天</Option>
                <Option value="周">周</Option>
                <Option value="月">月</Option>
                <Option value="年">年</Option>
            </Select>
        );
        const selectDate2 = (
            <Select
                name="rate_type"
                defaultValue="0" 
                style={{ width: 80, marginLeft: '10px', }}
            >
                <Option value="0">/日</Option>
                <Option value="1">/周</Option>
                <Option value="2">/月</Option>
                <Option value="3">/年</Option>
            </Select>
        );
        return (
            <Form className="formStyle" onSubmit={this.handleSubmit}>
                <FormItem
                    {...formItemLayout}
                    label="APP名称"
                >
                    {getFieldDecorator('name', {
                        rules: [{
                            required: true,
                            message: '请输入APP名称!',
                        }],
                    })(
                        <Input name="name" maxLength={45} />
                    )}
                </FormItem>
                <FormItem
                    {...formItemLayout}
                    label="归属公司"
                >
                    {getFieldDecorator('company_id', {
                        rules: [{
                            required: true,
                            message: '请选择APP归属的公司!',
                        }],
                    })(
                        <Select name="company_id">
                        </Select>
                    )}
                </FormItem>
                <FormItem
                    {...formItemLayout}
                    label={(
                        <span>
                            推广地址&nbsp;
                            <Tooltip title="需为完整的url地址 (以http://或https://开头).">
                                <Icon type="question-circle-o" />
                            </Tooltip>
                        </span>
                    )}
                >
                    {getFieldDecorator('weburl', {
                        rules: [{
                            required: true,
                            message: '请选择APP的推广地址!',
                        }, {
                            type: 'url',
                            message: '推广地址不正确!'
                        }],
                    })(
                        <Input name="weburl" maxLength={255} />
                    )}
                </FormItem>
                <FormItem
                    {...formItemLayout}
                    label="推荐指数"
                >
                    <Rate 
                        allowHalf 
                        defaultValue={2.5}
                        onChange={(value)=>this.onchange(value, 'recommend')}
                    />
                    <Input 
                        type="hidden" 
                        name="recommend" 
                        value={this.state.recommend}
                    />
                </FormItem>
                <FormItem
                    {...formItemLayout}
                    label="利率"
                    required={true}
                >
                    <InputNumber
                        defaultValue={0}
                        min={0}
                        max={100}
                        name="rate"
                        formatter={value => `${value}%`}
                        parser={value => value.replace('%', '')}
                        precision={2}
                    />
                    {selectDate2}
                </FormItem>
                <SomeInput 
                    name="moneys"
                    label="贷款金额"
                    rules={[{
                        pattern: /^[1-9]\d*$/,
                        message: '贷款金额不合法!'
                    }]}
                    initialValue={[0]}
                    maxlength={5}
                    {..._props}
                />
                <SomeInput 
                    name="terms"
                    label="还款期限"
                    rules={[{
                        pattern: /^[1-9]\d*$/,
                        message: '还款期限不合法!'
                    }]}
                    maxlength={5}
                    inputParams={{
                        addonAfter: selectDate1,
                    }}
                    {..._props}
                />
                <SomeInput 
                    name="repayments"
                    label="还款方式"
                    {..._props}
                />
                <FormItem
                    {...formItemLayout}
                    label="APP简介"
                >
                    <Input name="synopsis" maxLength="120" />
                </FormItem>
                <FormItem
                    {...formItemLayout}
                    label="详细介绍"
                >
                    <TextArea name="details" rows="8" />
                </FormItem>
                <FormItem
                    {...formItemLayout}
                    label="申请人数"
                >
                    <InputNumber
                        defaultValue={0}
                        min={0}
                        max={999999}
                        name="apply_number"
                        formatter={value => `${value}人`}
                        parser={value => value.replace('人', '')}
                    />
                </FormItem>
                <FormItem
                    {...formItemLayout}
                    label={(
                        <span>
                            当前状态&nbsp;
                            <Tooltip title="只有开启后才能在APP中搜索到.">
                                <Icon type="question-circle-o" />
                            </Tooltip>
                        </span>
                    )}
                >
                    <Switch 
                        defaultChecked
                        onChange={(value)=>this.onchange(value, 'status')}
                    />
                    <Input 
                        type="hidden" 
                        name="status" 
                        value={this.state.status}
                    />
                </FormItem>

                <FormItem {...tailFormItemLayout}>
                    <Button type="primary" htmlType="submit">保存</Button>
                </FormItem>
            </Form>
        );
    }
}

AppCreate = Form.create()(AppCreate);
export default AppCreate;