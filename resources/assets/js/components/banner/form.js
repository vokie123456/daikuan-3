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
    DatePicker,
} from 'antd';
import moment from 'moment';
import 'moment/locale/zh-cn';
moment.locale('zh-cn');

import Api from '../public/api';
import Utils from '../public/utils';
import { BannerPositions, BannerTypes } from '../public/global';

const FormItem = Form.Item;
const Option = Select.Option;
const RangePicker = DatePicker.RangePicker;

function getBase64(img, callback) {
    let reader = new FileReader();
    reader.addEventListener('load', () => callback(reader.result));
    reader.readAsDataURL(img);
}

class FormComponent extends React.Component {
    constructor(props) {
        super(props);
        this.state = {
            imageUrl: null,
            redirect: null,
        };
    }

    handleSubmit = (e) => {
        e.preventDefault();
        const { form, inits } = this.props;
        form.validateFieldsAndScroll((err, values) => {
            if (!err) {
                console.log('Received values of form: ', values);
                let formdata = new FormData();
                let _id = (inits && inits.id) ? inits.id.value : null;
                if(_id) formdata.append('id', _id);
                for(let i in values) {
                    if(i == 'range-time') {
                        formdata.append('start_time', values[i][0].format('YYYY-MM-DD HH:mm:ss'));
                        formdata.append('end_time', values[i][1].format('YYYY-MM-DD HH:mm:ss'));
                    }else {
                        let _value = typeof(values[i]) == 'undefined' ? '' : values[i];
                        if(i == 'status') _value = _value ? 1 : 0;
                        formdata.append(i, _value);
                    }
                }
                Utils.axios({
                    url: _id ? Api.updateBanner : Api.addBanner,
                    data: formdata,
                }, (result) => {
                    // let redirect = '/banner/' + this.type_id;
                    // this.setState({ redirect });
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
                        console.log(errors);
                        form.setFields(errors);
                        message.warning('保存失败!');
                    }
                });
            }
        });
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
                message.warning('图片大小需小于300KB!');
                return;
            }
        }

        getBase64(file, imageUrl => this.setState({
            imageUrl,
        }));
    }

    render() {
        const { redirect, imageUrl } = this.state;
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
        let img_url = imageUrl || getFieldValue('image');
        const rangeConfig = {
            rules: [{ 
                type: 'array', 
                required: true, 
                message: '请选择显示的时间范围!'
            }],
        };
        return (
            <Form className="formStyle" onSubmit={this.handleSubmit}>
                <FormItem
                    {...formItemLayout}
                    label="广告名称"
                >
                    {getFieldDecorator('name', {
                        rules: [{
                            required: true,
                            message: '请填写广告的名称!',
                        }],
                    })(
                        <Input maxLength={45} />
                    )}
                </FormItem>
                <FormItem
                    {...formItemLayout}
                    label="显示位置"
                >
                    {getFieldDecorator('position', {
                        rules: [{
                            required: true,
                            message: '请选择显示的位置!',
                        }],
                        initialValue: 0,
                    })(
                        <Select>
                            {(BannerPositions).map((item, index) => {
                                return (
                                    <Option key={index} value={index}>{item}</Option>
                                );
                            })}
                        </Select>
                    )}
                </FormItem>
                <FormItem
                    {...formItemLayout}
                    label="广告图片"
                >
                    {getFieldDecorator('image', {
                        rules: [{
                            required: true,
                            message: '请上传广告的图片!',
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
                            {img_url ? 
                                <img
                                    src={img_url} 
                                    alt="广告图片"
                                    style={{maxWidth: 520,}}
                                /> : 
                                <div>
                                    <Icon type={this.state.loading ? 'loading' : 'plus'} />
                                    <div className="ant-upload-text">Upload</div>
                                </div>
                            }
                        </Upload>
                    )}
                </FormItem>
                <FormItem
                    {...formItemLayout}
                    label="点击跳至"
                >
                    {getFieldDecorator('type', {
                        rules: [{
                            required: true,
                            message: '请选择点击跳转后的位置!',
                        }],
                        initialValue: 0,
                    })(
                        <Select>
                            {BannerTypes.map((item, index) => {
                                return (
                                    <Option key={index} value={index}>{item}</Option>
                                );
                            })}
                        </Select>
                    )}
                </FormItem>
                <FormItem
                    {...formItemLayout}
                    label={(
                        <span>
                            APP id&nbsp;
                            <Tooltip title="APP详情页的id, 可以在'APP管理->APP列表'中查看">
                                <Icon type="question-circle-o" />
                            </Tooltip>
                        </span>
                    )}
                >
                    {getFieldDecorator('app_id', {
                    })(
                        <InputNumber
                            min={0}
                            precision={0}
                        />
                    )}
                </FormItem>
                <FormItem
                    {...formItemLayout}
                    label={(
                        <span>
                            页面地址&nbsp;
                            <Tooltip title="需为完整的url地址 (以http://或https://开头)">
                                <Icon type="question-circle-o" />
                            </Tooltip>
                        </span>
                    )}
                >
                    {getFieldDecorator('url', {
                        rules: [{
                            type: 'url',
                            message: '页面地址不正确!'
                        }],
                    })(
                        <Input maxLength={255} />
                    )}
                </FormItem>
                
                <FormItem
                    {...formItemLayout}
                    label="显示时间"
                >
                    {getFieldDecorator('range-time', rangeConfig)(
                        <RangePicker 
                            showTime={{
                                //hideDisabledOptions: true,
                                defaultValue: [moment('00:00:00', 'HH:mm:ss'), moment('11:59:59', 'HH:mm:ss')],
                            }} 
                            format="YYYY-MM-DD HH:mm:ss" 
                        />
                    )}
                </FormItem>
                <FormItem
                    {...formItemLayout}
                    label={(
                        <span>
                            类别排序&nbsp;
                            <Tooltip title="优先以序号升序排列, 否则以添加时间升序排列">
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
            if(i == 'start_time') {
                inits['range-time'] = Form.createFormField({
                    value: [
                        moment(inits['start_time']), 
                        moment(inits['end_time']),
                    ]
                });
            }
            inits[i] = Form.createFormField({value: inits[i]});
        }
        return inits;
    },
})(FormComponent);


export default class BannerForm extends React.Component {
    constructor(props) {
        super(props);
        this.state = {datas: null,}
    }

    componentDidMount() {
        const { match } = this.props;
        if(match && match.params && match.params.id) {
            Utils.axios({
                key: 'banner',
                url: Api.getBanner + match.params.id,
                isAlert: false,
                method: 'get',
            }, (result) => {
                // console.log(result);
                this.setState({datas: result});
            });
        }
    }

    render() {
        const { params = {} } = this.props.match;
        let datas = this.state.datas;
        if(params.id && !datas) return null;
        return <FormInit {...Object.assign({}, this.props, {inits: datas})} />
    }
};
