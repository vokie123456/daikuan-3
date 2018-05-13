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
        link : '/',
    }, {
        title: 'APP管理',
        icon : 'appstore',
        child: [{
            title: '添加APP',
            link : '/apps/create',
        }, 
        // {
        //     title: '公司列表',
        //     link : '/apps/company',
        // }, 
        {
            title: 'APP列表',
            link : '/apps',
        }],
    }, {
        title: '类别管理',
        icon : 'profile',
        child: [{
            title: '首页图标',
            link : '/category/1',
            contain: true,
        }, {
            title: '类别列表',
            link : '/category/0',
            contain: true,
        }, {
            title: '类内APP',
            link : '/categories/apps',
        }],
    }, {
        title: '广告管理',
        icon : 'notification',
        child: [{
            title: '添加广告',
            link : '/banner/create',
        }, {
            title: '广告列表',
            link : '/banners',
        }],
    }, {
        title: '推广统计',
        icon : 'pie-chart',
        child: [{
            title: '流量统计',
        }, {
            title: '用户列表',
            link : '/users',
        }],
    }, {
        title: '设置',
        icon : 'setting',
    }
];

export default class SiderComponent extends React.Component {
    removeFirstSlash = (str) => {
        if(str) {
            str = str.replace(/^\//, '');
        }
        return str;
    };

    check_path = (item, str2) => {
        let str1 = this.removeFirstSlash(item.link);
        if(item.contain) {
            if(item.replace) {
                str1 = str1.replace(new RegExp(item.replace), '');
            }
            return str2.indexOf(str1) === 0;
        }else {
            return str1 == str2;
        }
    }

    render() {
        let { match } = this.props;
        let selected = [];
        let opened = [];
        if(match.params && match.params[0]) {
            let current_url = this.removeFirstSlash(match.params[0]);
            for(let i in routers) {
                if(routers[i].link && this.check_path(routers[i], current_url)) {
                    selected.push(i + '-0');
                }else if(routers[i].child && routers[i].child.length) {
                    let child = routers[i].child;
                    for(let c in child) {
                        if(child[c].link && this.check_path(child[c], current_url)) {
                            ++c;
                            selected.push(`${i}-${c}`);
                            opened.push(i);
                        }
                    }
                }
            }
        }else {
            selected.push('0-0');
        }
        return (
            <Sider 
                width={220}
                trigger={<span><Icon type={'appstore'} /></span>}
                collapsedWidth={0}
                breakpoint="lg"
                //style={{background: '#fff'}}
            >
                <div className="logo" style={styles.logo} />
                <Menu
                    defaultSelectedKeys={selected}
                    defaultOpenKeys={opened}
                    mode="inline"
                    //theme="light"
                    theme="dark"
                >
                    {routers.map((item, index) => {
                        let text = <span><Icon type={item.icon} /> {item.title}</span>;
                        if(item.child && item.child.length) {
                            return (
                                <SubMenu key={index} title={text}>
                                    {item.child.map((val, key) => (
                                        <Menu.Item key={index + '-' + (key + 1)}>
                                            {val.link ?
                                                <Link to={val.link}>{val.title}</Link>
                                                : val.title
                                            }
                                        </Menu.Item>
                                    ))}
                                </SubMenu>
                            );
                        }else {
                            return (
                                <Menu.Item key={index + '-0'}>
                                    {item.link ?
                                        <Link to={item.link}>{text}</Link>
                                        : text
                                    }
                                </Menu.Item>
                            );
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
        margin: '30px',
        borderRadius: '3px',
    },
};
