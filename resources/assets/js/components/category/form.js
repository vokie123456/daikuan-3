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

const FormItem = Form.Item;
const Option = Select.Option;

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

        const { params = {} } = this.props.match || {};
        this.type_id = (params.type && params.type == 1) ? 1 : 0;
    }

    handleSubmit = (e) => {
        e.preventDefault();
        const { form, inits } = this.props;
        form.validateFieldsAndScroll((err, values) => {
            if (!err) {
                // console.log('Received values of form: ', values);
                let formdata = new FormData();
                let _id = (inits && inits.id) ? inits.id.value : null;
                if(_id) formdata.append('id', _id);
                for(let i in values) {
                    let _value = typeof(values[i]) == 'undefined' ? '' : values[i];
                    if(i == 'status') _value = _value ? 1 : 0;
                    formdata.append(i, _value);
                }
                Utils.axios({
                    url: _id ? Api.updateCategory : Api.addCategory,
                    data: formdata,
                }, (result) => {
                    let redirect = '/category/' + this.type_id;
                    this.setState({ redirect });
                }, (result) => {
                    if(result && result.errors) {
                        let errors = {};
                        let _error = null;
                        for(let j in result.errors) {
                            if(result.errors[j]) {
                                errors[j] = {
                                    // value: values[j],
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
        return (
            <Form className="formStyle" onSubmit={this.handleSubmit}>
                <FormItem
                    {...formItemLayout}
                    label="类别名称"
                >
                    {getFieldDecorator('name', {
                        rules: [{
                            required: true,
                            message: '请填写类别的名称!',
                        }],
                    })(
                        <Input maxLength={45} />
                    )}
                </FormItem>
                <FormItem
                    {...formItemLayout}
                    label="显示位置"
                >
                    {getFieldDecorator('type', {
                        rules: [{
                            required: true,
                            message: '请选择显示的位置!',
                        }],
                        initialValue: this.type_id,
                    })(
                        <Select disabled={this.type_id == 1 ? true : false}>
                            {Moudles.map((item, index) => {
                                return (
                                    (this.type_id != 1 && index == 1) ?
                                    null :
                                    <Option key={index} value={index}>{item}</Option>
                                )
                            })}
                        </Select>
                    )}
                </FormItem>
                {this.type_id == 1 ? 
                    <FormItem
                        {...formItemLayout}
                        label="显示图片"
                    >
                        {getFieldDecorator('image', {
                            rules: [{
                                required: true,
                                message: '请上传显示的图片!',
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
                                        className="uploadImgStyle" 
                                        src={img_url} 
                                        alt="显示图片" 
                                    /> : 
                                    <div>
                                        <Icon type={this.state.loading ? 'loading' : 'plus'} />
                                        <div className="ant-upload-text">Upload</div>
                                    </div>
                                }
                            </Upload>
                        )}
                    </FormItem> : null
                }
                <FormItem
                    {...formItemLayout}
                    label="类别内的APP排序"
                >
                    {getFieldDecorator('sort_app', {
                        initialValue: 0,
                    })(
                        <Select>
                            {SortTypes.map((item, index) => {
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
            inits[i] = Form.createFormField({value: inits[i]});
        }
        return inits;
    },
})(FormComponent);


export default class CategoryForm extends React.Component {
    constructor(props) {
        super(props);
        this.state = {datas: null,}
    }

    componentDidMount() {
        const { match } = this.props;
        if(match && match.params && match.params.id) {
            Utils.axios({
                key: 'category',
                url: Api.getCategory + match.params.id,
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
        let id = parseInt(params.id) || 0;
        if(id && !datas) return null;
        return <FormInit {...Object.assign({}, this.props, {inits: datas})} />
    }
};
