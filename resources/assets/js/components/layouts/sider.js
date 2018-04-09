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
            param: true,
        }, {
            title: 'APP公司',
            link : '/apps/company',
        }, {
            title: 'APP列表',
            link : '/apps',
        }, {
            title: '添加模版',
        }],
    }, {
        title: '推广统计',
        icon : 'pie-chart',
        child: [{
            title: '流量统计',
        }, {
            title: '用户统计',
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

    check_path = (str1, str2, param = false) => {
        if(param) {
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
                if(routers[i].link && this.check_path(this.removeFirstSlash(routers[i].link), current_url, !!routers[i].param)) {
                    selected.push(i + '-0');
                }else if(routers[i].child && routers[i].child.length) {
                    let child = routers[i].child;
                    for(let c in child) {
                        if(child[c].link && this.check_path(this.removeFirstSlash(child[c].link), current_url, !!child[c].param)) {
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
            >
                <div className="logo" style={styles.logo} />
                <Menu
                    defaultSelectedKeys={selected}
                    defaultOpenKeys={opened}
                    mode="inline"
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
        margin: '30px 16px',
        borderRadius: '3px',
    },
};
