import React from 'react';
import PropTypes from 'prop-types';
import { Form, Input, Icon, Button, message } from 'antd';

const FormItem = Form.Item;
let uuid = 0;

class SomeInput extends React.Component {
    // 默认参数
    static defaultProps = {
        isRequired: true,
        rules: [],
        initialValue: [0],
        inputParams: {},
        maxlength: 9,
    };
    // 参数类型
    static propTypes = {
        name: PropTypes.string.isRequired,
        label: PropTypes.string.isRequired,
        isRequired: PropTypes.bool,
        rules: PropTypes.array,
        inputParams: PropTypes.object,
        initialValue: PropTypes.array,
        getFieldDecorator: PropTypes.func.isRequired,
        getFieldValue: PropTypes.func.isRequired,
        setFieldsValue: PropTypes.func.isRequired,
        buttonText: PropTypes.string,
        maxlength: PropTypes.number,
    };
    // 构造函数
    constructor(props) {
        super(props);
    }

    // 删除一行
    remove = (k) => {
        const { getFieldValue, setFieldsValue, name } = this.props;
        const keys = getFieldValue(name + '_key');
        // 仅一个时不能删除
        if (keys.length === 1) return;
        // can use data-binding to set
        let obj = {};
        obj[name + '_key'] = keys.filter(key => key !== k);
        setFieldsValue(obj);
    };

    // 添加一行
    add = () => {
        const { getFieldValue, setFieldsValue, name, maxlength } = this.props;
        const keys = getFieldValue(name + '_key');
        if(keys.length) {
            //判断是否已达最大值
            if(keys.length >= maxlength) {
                message.warning('数量已达上限, 无法添加!');
                return;
            }
            //更新下标
            uuid = keys[keys.length - 1] + 1;
        }
        const nextKeys = keys.concat(uuid);
        uuid++;
        // can use data-binding to set
        // important! notify form to detect changes
        let obj = {};
        obj[name + '_key'] = nextKeys;
        setFieldsValue(obj);
    }

    render() {
        const { 
            getFieldDecorator, 
            getFieldValue, 
            name, 
            initialValue, 
            label, 
            isRequired, 
            inputParams,
            rules, 
            buttonText,
        } = this.props;

        getFieldDecorator(name + '_key', { initialValue: initialValue });
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
                xs: {span: 24, offset: 0, },
                sm: {span: 16, offset: 8, },
            },
        };
        let _rules = [{
            required: true,
            whitespace: true,
            message: label + ' 不能为空!',
        }];
        let _buttonText = buttonText ? buttonText : '添加 ' + label;
        const keys = getFieldValue(name + '_key');
        const formItems = keys.map((k, index) => {
            return (
                <FormItem
                    {...(index === 0 ? formItemLayout : tailFormItemLayout)}
                    label={index === 0 ? label : ''}
                    required={isRequired}
                    key={k}
                >
                    {getFieldDecorator(`${name}[${k}]`, {
                        //validateTrigger: ['onChange', 'onBlur'],
                        rules: _rules.concat(rules),
                    })(
                        <div className="formItemStyle">
                            <Input {...inputParams} />
                        </div>
                    )}
                    {keys.length > 1 ? (
                        <span className="iconSpan">
                            <Icon
                                className="sideIcon"
                                type="minus-circle-o"
                                disabled={keys.length === 1}
                                onClick={() => this.remove(k)}
                            />
                        </span>
                    ) : null}
                </FormItem>
            );
        });

        return (
            <div>
                {formItems}
                <FormItem {...tailFormItemLayout}>
                    <Button type="dashed" onClick={this.add}>
                        <Icon type="plus" /> {_buttonText}
                    </Button>
                </FormItem>
            </div>
        );
    }
}

export default SomeInput;
