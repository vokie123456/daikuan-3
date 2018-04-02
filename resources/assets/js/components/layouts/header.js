import React from 'react';
import { Menu, Icon, Layout, Dropdown, Row } from 'antd';

const { Header, } = Layout;
const SubMenu = Menu.SubMenu;
const MenuItemGroup = Menu.ItemGroup;

const menu = (
    <Menu>
        <Menu.Item>
            <a target="_blank" rel="noopener noreferrer" href="#">退出登录</a>
        </Menu.Item>
    </Menu>
);

export default class HeaderComponent extends React.Component {
    render() {
        let { admin } = this.props;
        return (
            <Header 
                className="header"
                style={{backgroundColor: '#fff'}}
            >
                <Row type="flex" justify="end">
                    <Dropdown overlay={menu}>
                        <a className="ant-dropdown-link" href="#">
                            {admin.name} <Icon type="down" />
                        </a>
                    </Dropdown>
                </Row>
            </Header>
        );
    }
}