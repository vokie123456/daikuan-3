import React from 'react';
import { Link } from "react-router-dom";
import { Tabs, Card, Col, Row } from 'antd';

import Api from '../public/api';
import Utils from '../public/utils';
import { SexNames, RegisterTypes } from '../public/global';
import Users from './index';

const TabPane = Tabs.TabPane;

class UserDetail extends React.Component {
    render() {
        const { match = {}, history } = this.props;
        if(!match.params.id) return null;
        return (
            <Tabs defaultActiveKey="1" style={{width: '100%'}}>
                <TabPane tab="基本信息" key="1">
                    <Basic id={match.params.id} history={history} />
                </TabPane>
                <TabPane tab="推广记录" key="2">
                    <Users recommer={match.params.id} />
                </TabPane>
            </Tabs>
        );
    }
}

class Basic extends React.Component {
    constructor(props) {
        super(props);
        this.state = {
            data: null,
        };
    }

    componentDidMount() {
        const id = this.props.id || 0;
        if(id) {
            Utils.axios({
                key: 'data',
                url: Api.getUser + id,
                isAlert: false,
                method: 'get',
            }, (data) => {
                data && this.setState({ data });
            });
        }
    }

    render() {
        const { data } = this.state;
        const push = this.props.history.push || null;
        if(!data) return null;
        let device = (data.devices && data.devices[0]) ? data.devices[0] : null;
        return (
            <div style={styles.cordBox}>
                <Card title="用户信息" type="inner" style={styles.cordStyle}>
                    <Row gutter={16} style={styles.rowStyle}>
                        <Col span={3} style={styles.titleStyle}>姓名</Col>
                        <Col span={9}>{data.name}</Col>
                        <Col span={3} style={styles.titleStyle}>手机</Col>
                        <Col span={9}>{data.telephone}</Col>
                    </Row>
                    <Row gutter={16} style={styles.rowStyle}>
                        <Col span={3} style={styles.titleStyle}>生日</Col>
                        <Col span={9}>{data.birthday || ''}</Col>
                        <Col span={3} style={styles.titleStyle}>邮箱</Col>
                        <Col span={9}>{data.email}</Col>
                    </Row>
                    <Row gutter={16} style={styles.rowStyle}>
                        <Col span={3} style={styles.titleStyle}>性别</Col>
                        <Col span={9}>{SexNames[data.sex] || SexNames[0]}</Col>
                        <Col span={3} style={styles.titleStyle}>职业</Col>
                        <Col span={9}>{data.profession}</Col>
                    </Row>
                    <Row gutter={16} style={styles.rowStyle}>
                        <Col span={3} style={styles.titleStyle}>当前状态</Col>
                        <Col span={9}>{data.status ? '正常' : '禁用'}</Col>
                        <Col span={3} style={styles.titleStyle}>地址</Col>
                        <Col span={9}>{data.address}</Col>
                    </Row>
                    <Row gutter={16} style={styles.rowStyle}>
                        <Col span={3} style={styles.titleStyle}>注册方式</Col>
                        <Col span={9}>{RegisterTypes[data.recomm_type] || ''}</Col>
                        <Col span={3} style={styles.titleStyle}>推荐方</Col>
                        <Col span={9}>
                            {(data.recomm_type > 0 && data.recommer) ?
                                (data.recomm_type == 1 ? data.recommer.telephone : data.recommer.name) : ''
                            }
                        </Col>
                    </Row>
                    <Row gutter={16} style={styles.rowStyle}>
                        <Col span={3} style={styles.titleStyle}>注册时间</Col>
                        <Col span={9}>{data.created_at || ''}</Col>
                        <Col span={3} style={styles.titleStyle}>激活时间</Col>
                        <Col span={9}>{data.activated_at || ''}</Col>
                    </Row>
                </Card>
                {device ?
                    <Card title="手机信息" type="inner">
                        <Row gutter={16} style={styles.rowStyle}>
                            <Col span={3} style={styles.titleStyle}>唯一标识</Col>
                            <Col span={9}>{device.unique_id}</Col>
                            <Col span={3} style={styles.titleStyle}>设备型号</Col>
                            <Col span={9}>{device.model}</Col>
                        </Row>
                        <Row gutter={16} style={styles.rowStyle}>
                            <Col span={3} style={styles.titleStyle}>手机型号</Col>
                            <Col span={9}>{device.phone_model}</Col>
                            <Col span={3} style={styles.titleStyle}>系统版本</Col>
                            <Col span={9}>{data.phone_sys_version}</Col>
                        </Row>
                        <Row gutter={16} style={styles.rowStyle}>
                            <Col span={3} style={styles.titleStyle}>运营商</Col>
                            <Col span={9}>{device.operator}</Col>
                            <Col span={3} style={styles.titleStyle}>IP</Col>
                            <Col span={9}>{device.request_ip}</Col>
                        </Row>
                        <Row gutter={16} style={styles.rowStyle}>
                            <Col span={3} style={styles.titleStyle}>添加时间</Col>
                            <Col span={9}>{device.created_at}</Col>
                            <Col span={3} style={styles.titleStyle}>更新时间</Col>
                            <Col span={9}>{data.updated_at}</Col>
                        </Row>
                    </Card> : null
                }
            </div>
        );
    }
}


var styles = {
    cordBox: {
        padding: 30,
    },
    cordStyle: {
        marginBottom: 20,
    },
    rowStyle: {
        padding: '6px 0',
        borderBottom: '1px solid rgba(238, 238, 238, 0.4)',
    },
    titleStyle: {
        fontSize: 13,
        fontWeight: 500,
        color: 'rgba(0, 0, 0, 0.8)',
    },
}

export default UserDetail;
