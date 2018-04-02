import React from 'react';
import { Link } from "react-router-dom";
import { Menu, Icon, Layout, Button } from 'antd';

const { Sider, } = Layout;
const SubMenu = Menu.SubMenu;
const MenuItemGroup = Menu.ItemGroup;

const routers = [
    {
        title: '首页',
        icon : 'dashboard',
    }, {
        title: 'APP管理',
        icon : 'appstore',
        child: [{
            title: '添加APP',
            icon : 'dashboard',
        }, {
            title: '添加模版',
            icon : 'dashboard',
        }],
    }, {
        title: '推广统计',
        icon : 'pie-chart',
        child: [{
            title: '流量统计',
            icon : 'dot-chart',
        }, {
            title: '用户统计',
            icon : 'area-chart',
        }],
    }, {
        title: '设置',
        icon : 'setting',
    }
];

export default class SiderComponent extends React.Component {
    render() {
        return (
            <Sider 
                width={220}
                trigger={<span><Icon type={'appstore'} /></span>}
                collapsedWidth={0}
                breakpoint="lg"
            >
                <div className="logo" style={styles.logo} />
                <Menu
                    defaultSelectedKeys={['0-0']}
                    mode="inline"
                    theme="dark"
                >
                    {routers.map((item, index) => {
                        if(item.child && item.child.length) {
                            let child = item.child.map((val, key) => (
                                <Menu.Item key={index + '-' + key}>{val.title}</Menu.Item>
                            ));
                            return (
                                <SubMenu key={index} title={<span><Icon type={item.icon} /> {item.title}</span>}>
                                    {child}
                                </SubMenu>
                            );
                        }else {
                            return <Menu.Item key={index + '-0'}><Icon type={item.icon} /> {item.title}</Menu.Item>;
                        }
                    })}
                </Menu>
            </Sider>
        );
    }
}

var styles = {
    logo: {
        height: '32px',
        background: 'rgba(255,255,255,.2)',
        margin: '30px 16px',
        borderRadius: '3px',
    },
};
