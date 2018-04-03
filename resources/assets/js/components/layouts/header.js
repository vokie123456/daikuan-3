import React from 'react';
import { Link } from "react-router-dom";
import { Menu, Icon, Layout, Dropdown, Row } from 'antd';

const { Header, } = Layout;
const SubMenu = Menu.SubMenu;
const MenuItemGroup = Menu.ItemGroup;

const menu = (
    <Menu>
        <Menu.Item>
            <a href="#" onClick={(event)=>{
                event.preventDefault();
                document.getElementById('logout-form').submit();
            }}>退出登录</a>
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
                        <Link to="/" className="ant-dropdown-link">
                            {admin.name} <Icon type="down" />
                        </Link>
                    </Dropdown>
                </Row>
            </Header>
        );
    }
}