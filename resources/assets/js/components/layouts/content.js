import React from 'react';
import { Layout, } from 'antd';
import { Route, } from 'react-router-dom'

import Home from '../home';
import Apps from '../app/index';
import AppCreate from '../app/create';
import AppCompany from '../app/company';
import Category from '../category';
import CategoryForm from '../category/form';
import CategoryApps from '../category/apps';

const { Content, } = Layout;

export default class ContentComponent extends React.Component {
    render() {
        return (
            <Content style={styles.content}>
                <Route path="/" exact component={Home} />
                <Route path="/apps" exact component={Apps} />
                <Route path="/apps/create/:id?" component={AppCreate} />
                <Route path="/apps/company" component={AppCompany} />
                <Route path="/category/:type" exact component={Category} />
                <Route path="/category/:type/form/:id?" component={CategoryForm} />
                <Route path="/categories/apps" component={CategoryApps} />
            </Content>
        );
    }
}

var styles = {
    content: {
        backgroundColor: '#fff',
        margin: '12px 12px 0',
        borderRadius: '4px',
        padding: '20px',
    },
};
