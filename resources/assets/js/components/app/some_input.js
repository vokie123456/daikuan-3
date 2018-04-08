import React from 'react';
import PropTypes from 'prop-types';
import { Form, Input, Icon, Button, message } from 'antd';

const FormItem = Form.Item;

class SomeInput extends React.Component {
    // 默认参数
    static defaultProps = {
        isRequired: true,
        rules: [],
        initialValue: [''],
        inputParams: {},
        maxCount: 9,
        clearDefaultRule: false,
    };
    // 参数类型
    static propTypes = {
        name: PropTypes.string.isRequired,
        label: PropTypes.string.isRequired,
        isRequired: PropTypes.bool,
        rules: PropTypes.array,
        clearDefaultRule: PropTypes.bool,
        inputParams: PropTypes.object,
        initialValue: PropTypes.array,
        getFieldDecorator: PropTypes.func.isRequired,
        getFieldValue: PropTypes.func.isRequired,
        setFieldsValue: PropTypes.func.isRequired,
        buttonText: PropTypes.string,
        maxCount: PropTypes.number,
        // MyComponent: PropTypes.node,
    };
    // 构造函数
    constructor(props) {
        super(props);
    }

    componentDidMount() {
        const { getFieldDecorator, initialValue, name } = this.props;
        getFieldDecorator(name + '_key', {initialValue: initialValue});
    }

    // 删除一行
    remove = (index) => {
        const { getFieldValue, setFieldsValue, name } = this.props;
        const keys = getFieldValue(name + '_key') || [];
        // 仅一个时不能删除
        if (keys.length <= 1) return;
        let obj = {};
        obj[name + '_key'] = keys.filter((k, i) => index != i);
        setFieldsValue(obj);
    };

    // 添加一行
    add = () => {
        const { getFieldValue, setFieldsValue, name, maxCount } = this.props;
        const keys = getFieldValue(name + '_key');
        if(keys.length) {
            //判断是否已达最大值
            if(keys.length >= maxCount) {
                message.warning('数量已达上限, 无法添加!');
                return;
            }
        }
        // can use data-binding to set
        // important! notify form to detect changes
        let obj = {};
        obj[name + '_key'] = keys.concat('');
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
            clearDefaultRule,
            buttonText,
            MyComponent,
            //value = null,
            //onChange = null,
        } = this.props;

        // getFieldDecorator(name + '_key', {initialValue: initialValue});
        const keys = getFieldValue(name + '_key');
        if(!keys) return null;
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
        let _rules = clearDefaultRule ? [] : [{
            required: true,
            whitespace: true,
            message: label + ' 不能为空!',
        }];
        let _buttonText = buttonText ? buttonText : '添加 ' + label;
        const formItems = keys.map((item, index) => {
            // inputParams.defaultValue = k;
            return (
                <FormItem
                    {...(index === 0 ? formItemLayout : tailFormItemLayout)}
                    label={index === 0 ? label : ''}
                    required={isRequired}
                    key={index}
                >
                    <div className="formItemStyle">
                        {getFieldDecorator(`${name}[${index}]`, {
                            //validateTrigger: ['onChange', 'onBlur'],
                            rules: _rules.concat(rules),
                            initialValue: item,
                        })(
                            MyComponent ? 
                                <MyComponent /> :
                                <Input {...inputParams} />
                        )}
                    </div>
                    {keys.length > 1 ? (
                        <span className="iconSpan">
                            <Icon
                                className="sideIcon"
                                type="minus-circle-o"
                                disabled={keys.length === 1}
                                onClick={() => this.remove(index)}
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
