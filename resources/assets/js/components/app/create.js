import React from 'react';
import { Redirect, } from "react-router-dom";
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
    Upload,
    message,
} from 'antd';

import Api from '../public/api';
import Utils from '../public/utils';
import { RateTypes, DateUnits } from '../public/global';
import SomeInput from './some_input';

const { TextArea } = Input;
const FormItem = Form.Item;
const Option = Select.Option;
const AutoCompleteOption = AutoComplete.Option;

function getBase64(img, callback) {
    let reader = new FileReader();
    reader.addEventListener('load', () => callback(reader.result));
    reader.readAsDataURL(img);
}

class RateGroup extends React.Component {
    constructor(props) {
        super(props);
    
        const value = this.props.value || {};
        this.state = {
            value: value.value || '',
            type: value.type || 0,
        };
    }
    
    componentWillReceiveProps(nextProps) {
        // Should be a controlled component.
        if ('value' in nextProps) {
            const value = nextProps.value;
            this.setState(value);
        }
    }

    handleValueChange = (value) => {
        this.setState({ value });
        // if(isNaN(value)) return;
        // if(!('value' in this.props)) {
        //     this.setState({ value });
        // }
        // this.triggerChange({ value });
    }

    handleTypeChange = (type) => {
        this.setState({ type });
        // if(!('value' in this.props)) {
        //     this.setState({ type });
        // }
        // this.triggerChange({ type });
    }

    triggerChange = (changedValue) => {
        // Should provide an event to pass value to Form.
        const onChange = this.props.onChange;
        if (onChange) {
            onChange(this.state);
            // onChange(Object.assign({}, this.state, changedValue));
        }
    }

    render() {
        const state = this.state;
        return (
            <span>
                <InputNumber
                    min={0}
                    max={100}
                    precision={2}
                    value={state.value}
                    onChange={this.handleValueChange}
                    onBlur={this.triggerChange}
                />
                <Select 
                    style={{ width: 80, marginLeft: '10px', }}
                    value={state.type}
                    onChange={this.handleTypeChange}
                    onBlur={this.triggerChange}
                >
                    {RateTypes.map((item, index) => {
                        return <Option key={index} value={index}>{item}</Option>
                    })}
                </Select>
            </span>
        );
    }
}

class TermGroup extends React.Component {
    constructor(props) {
        super(props);

        const value = this.props.value || {};
        this.state = {
            value: value.value || '',
            type: value.type || '天',
        };
    }

    componentWillReceiveProps(nextProps) {
        // Should be a controlled component.
        if(nextProps && nextProps.value && (nextProps.value instanceof Object)) {
            const value = nextProps.value;
            this.setState(value);
        }
    }

    handleValueChange = (e) => {
        let value = parseInt(e.target.value) || 0;
        this.setState({ value });
        // if(isNaN(value)) return;
        // if(!('value' in this.props)) {
        //     this.setState({ value });
        // }
        // this.triggerChange({ value });
    }

    handleTypeChange = (type) => {
        this.setState({ type });
        // if(!('value' in this.props)) {
        //     this.setState({ type });
        // }
        // this.triggerChange({ type });
    }

    triggerChange = (changedValue) => {
        // Should provide an event to pass value to Form.
        const onChange = this.props.onChange;
        if (onChange) {
            onChange(this.state);
            // onChange(Object.assign({}, this.state, changedValue));
        }
    }

    render() {
        const state = this.state;
        return (
            <span>
                <Input
                    value={state.value}
                    onChange={this.handleValueChange}
                    onBlur={this.triggerChange}
                    addonAfter={(
                        <Select 
                            style={{ width: 60, }}
                            value={state.type}
                            onChange={this.handleTypeChange}
                            onBlur={this.triggerChange}
                        >
                            {DateUnits.map((item, index) => {
                                return <Option key={index} value={item}>{item}</Option>
                            })}
                        </Select>
                    )}
                />
            </span>
        );
    }
}


class AppCreate extends React.Component {
    constructor(props) {
        super(props);
        this.state = {
            companies: [],
            loading: false,
            redirect: null,
        }
        this.normal = true;
    }

    componentDidMount() {
        Utils.axios({
            key: 'companies',
            url: Api.getAppCompanies,
            isAlert: false,
            method: 'get',
            params: {'all': 1}
        }, (result) => {
            if(result.length && this.normal) {
                this.setState({companies: result});
            }
        });
    }

    componentWillUnmount() {
        this.normal = false;
    }

    handleUpload = (info) => {
        let file = info.file;
        const typeOk = (
            file.type === 'image/jpeg' ||
            file.type === 'image/png' ||
            file.type === 'image/gif' ||
            file.type === 'image/bmp' ||
            file.type === 'image/x-icon'
        );
        if(!typeOk) {
            message.warning('图片类型不合法!');
            return;
        }else {
            const sizeOk = file.size / 1024 < 200;
            if (!sizeOk) {
                message.warning('图标大小需小于200KB!');
                return;
            }
        }

        getBase64(file, imageUrl => this.setState({
            imageUrl,
        }));
    }

    handleSubmit = (e) => {
        e.preventDefault();
        const { form, inits } = this.props;
        form.validateFieldsAndScroll((err, values) => {
            // console.log(values);
            if (!err) {
                let formdata = new FormData();
                let app_id = (inits && inits.id) ? inits.id.value : null;
                if(app_id) formdata.append('id', app_id);
                for(let i in values) {
                    let _value = (
                        i == 'moneys' ||
                        i == 'rates' ||
                        i == 'repayments' ||
                        i == 'terms' ||
                        i == 'marks'
                    ) ? JSON.stringify(values[i]) : values[i];
                    if(i == 'status' || i == 'isNew') _value = _value ? 1 : 0;
                    formdata.append(i, _value);
                }
                Utils.axios({
                    url: app_id ? Api.updateApp : Api.addApp,
                    data: formdata,
                    // headers: {
                    //     'Content-Type': 'application/x-www-form-urlencoded',
                    // },
                }, (result) => {
                    let redirect = '/apps';
                    this.setState({ redirect })
                }, (result) => {
                    if(result && result.errors) {
                        var errors = {};
                        for(let j in result.errors) {
                            if(result.errors[j]) {
                                errors[j] = {
                                    // value: values[j],
                                };
                                if(typeof(result.errors[j]) == 'string') {
                                    errors[j]['errors'] = [new Error(result.errors[j])];
                                }else if(result.errors[j][0]) {
                                    errors[j]['errors'] = [new Error(result.errors[j][0])];
                                }
                            }
                        }
                        // console.log(errors);
                        form.setFields(errors);
                        message.warning('保存失败!');
                    }
                });

            }
        });
    };

    checkRate = (rule, value, callback) => {
        let _value = value.value;
        if(!_value && (_value !== 0 || _value !== '0')) {
            callback('请填写利率数值!');
            return;
        }
        _value = parseFloat(_value) || 0;
        if(_value > 0 && _value < 100) {
            callback();
        }else {
            callback('利率数值需在0-100以内!');
        }
    }

    checkTerm = (rule, value, callback) => {
        let _value = value.value;
        if(!_value && (_value !== 0 || _value !== '0')) {
            callback('请填写还款期限!');
            return;
        }
        _value = parseInt(_value) || 0;
        if(_value > 0) {
            callback();
        }else {
            callback('还款期限数值不合法!');
        }
    }

    render() {
        let { companies, redirect } = this.state;
        if(redirect) return <Redirect to={redirect} />;
        const { getFieldDecorator, getFieldValue, getFieldsValue, setFieldsValue } = this.props.form;
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
            getFieldsValue,
            setFieldsValue,
        };
        const uploadButton = (
            <div>
                <Icon type={this.state.loading ? 'loading' : 'plus'} />
                <div className="ant-upload-text">Upload</div>
            </div>
        );
        const imageUrl = this.state.imageUrl || getFieldValue('appicon');
        return (
            <Form 
                className="formStyle" 
                onSubmit={this.handleSubmit}
            >
                <FormItem
                    {...formItemLayout}
                    label="APP名称"
                >
                    {getFieldDecorator('name', {
                        rules: [{
                            required: true,
                            message: '请填写APP的名称!',
                        }],
                    })(
                        <Input maxLength={45} />
                    )}
                </FormItem>
                <FormItem
                    {...formItemLayout}
                    label="APP图标"
                >
                    {getFieldDecorator('appicon', {
                        rules: [{
                            required: true,
                            message: '请上传APP图标!',
                        }],
                        valuePropName: 'file',
                        getValueFromEvent: (e) => {
                            if (Array.isArray(e)) return e;
                            return e && e.file;
                        },
                    })(
                        <Upload
                            listType="picture-card"
                            className="avatar-uploader"
                            showUploadList={false}
                            beforeUpload={(file) => false}
                            onChange={this.handleUpload}
                        >
                            {imageUrl ? 
                                <img 
                                    className="uploadImgStyle" 
                                    src={imageUrl} 
                                    alt="app图标" 
                                /> : uploadButton
                            }
                        </Upload>
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
                        <Select>
                            {companies.map((item, index) => (
                                <Option key={index} value={item.id}>{item.name}</Option>
                            ))}
                        </Select>
                    )}
                </FormItem>
                <FormItem
                    {...formItemLayout}
                    label={(
                        <span>
                            推广地址&nbsp;
                            <Tooltip title="需为完整的url地址 (以http://或https://开头)">
                                <Icon type="question-circle-o" />
                            </Tooltip>
                        </span>
                    )}
                >
                    {getFieldDecorator('weburl', {
                        rules: [{
                            required: true,
                            message: '请填写推广地址!',
                        }, {
                            type: 'url',
                            message: '推广地址不正确!'
                        }],
                    })(
                        <Input maxLength={255} />
                    )}
                </FormItem>
                <FormItem
                    {...formItemLayout}
                    label="推荐指数"
                >
                    {getFieldDecorator('recommend', {
                        initialValue: 2.5,
                    })(
                        <Rate allowHalf />
                    )}
                </FormItem>
                <FormItem
                    {...formItemLayout}
                    label="利率"
                    required={true}
                >
                    {getFieldDecorator('rates', {
                        initialValue: getFieldValue('rates') || { value: '', type: 0 },
                        rules: [{ validator: this.checkRate }],
                    })(
                        <RateGroup />
                    )}
                </FormItem>
                <SomeInput 
                    name="moneys"
                    label="贷款金额"
                    rules={[{
                        pattern: /^[1-9]\d*$/,
                        message: '贷款金额不合法!'
                    }]}
                    maxCount={5}
                    initialValue={getFieldValue('moneys') || ['']}
                    {..._props}
                />
                <SomeInput 
                    name="terms"
                    label="还款期限"
                    rules={[{ validator: this.checkTerm }]}
                    maxCount={5}
                    clearDefaultRule={true}
                    MyComponent={TermGroup}
                    initialValue={getFieldValue('terms') || [{value: '', type: '天'}]}
                    {..._props}
                />
                <SomeInput 
                    name="repayments"
                    label="还款方式"
                    {..._props}
                    initialValue={getFieldValue('repayments') || ['']}
                />
                <SomeInput 
                    name="marks"
                    label="标签"
                    maxCount={1}
                    inputParams={{maxLength: 6}}
                    isRequired={false}
                    clearDefaultRule={true}
                    {..._props}
                    initialValue={getFieldValue('marks') || ['']}
                />
                <FormItem
                    {...formItemLayout}
                    label="简介"
                >
                    {getFieldDecorator('synopsis', {
                        initialValue: ''
                    })(
                        <Input maxLength="120" />
                    )}
                </FormItem>
                <FormItem
                    {...formItemLayout}
                    label="详细介绍"
                >
                    {getFieldDecorator('details', {
                        initialValue: ''
                    })(
                        <TextArea rows="8" />
                    )}
                </FormItem>
                <FormItem
                    {...formItemLayout}
                    label="申请人数"
                >
                    {getFieldDecorator('apply_number', {
                        initialValue: 0
                    })(
                        <InputNumber
                            min={0}
                            max={999999}
                            formatter={value => `${value}人`}
                            parser={value => value.replace('人', '')}
                            precision={0}
                        />
                    )}
                </FormItem>
                <FormItem
                    {...formItemLayout}
                    label={(
                        <span>
                            排序序号&nbsp;
                            <Tooltip title="升降序可在类别处自行设置">
                                <Icon type="question-circle-o" />
                            </Tooltip>
                        </span>
                    )}
                >
                    {getFieldDecorator('sort', {
                        initialValue: 0
                    })(
                        <InputNumber
                            min={0}
                            max={9999999}
                            precision={0}
                        />
                    )}
                </FormItem>
                <FormItem
                    {...formItemLayout}
                    label={(
                        <span>
                            当前状态&nbsp;
                            <Tooltip title="只有开启后才能在APP中看到">
                                <Icon type="question-circle-o" />
                            </Tooltip>
                        </span>
                    )}
                >
                    {getFieldDecorator('status', { 
                        valuePropName: 'checked',
                        initialValue: true,
                    })(
                        <Switch />
                    )}
                </FormItem>
                <FormItem
                    {...formItemLayout}
                    label="是否最新"
                >
                    {getFieldDecorator('isNew', { 
                        valuePropName: 'checked',
                        initialValue: false,
                    })(
                        <Switch />
                    )}
                </FormItem>

                <FormItem {...tailFormItemLayout}>
                    <Button type="primary" htmlType="submit">保存</Button>
                </FormItem>
            </Form>
        );
    }
}

const AppCreateInit = Form.create({
    mapPropsToFields(props) {
        let inits = props.inits || {
            terms: [{value: '', type: '天'}],
            moneys: [''],
            repayments: [''],
            marks: [''],
        };
        for(let i in inits) {
            if(i == 'moneys') {
                inits[i] = inits[i].map(function(val) {return String(val);});
            }
            inits[i] = Form.createFormField({value: inits[i]});
        }
        return inits;
    },
})(AppCreate);


export default class App extends React.Component {
    constructor(props) {
        super(props);
        this.state = {datas: null,}
    }

    componentDidMount() {
        const { match } = this.props;
        if(match && match.params && match.params.id) {
            Utils.axios({
                key: 'app',
                url: Api.getApp + match.params.id,
                isAlert: false,
                method: 'get',
            }, (result) => {
                this.setState({datas: result});
            });
        }
    }

    render() {
        const { params = {} } = this.props.match;
        let datas = this.state.datas;
        if(params.id && !datas) {
            return null;
        }
        return <AppCreateInit {...Object.assign({}, this.props, {inits: datas})} />
    }
};
