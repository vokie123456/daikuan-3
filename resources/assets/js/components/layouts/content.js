import React from 'react';
import { Layout, } from 'antd';
import { Route, } from 'react-router-dom'

import Home from '../home';
import AppCreate from '../app/create';
import AppCompany from '../app/company';

const { Content, } = Layout;

export default class ContentComponent extends React.Component {
    render() {
        return (
            <Content style={styles.content}>
                <Route path="/" exact component={Home} />
                <Route path="/apps/create" component={AppCreate} />
                <Route path="/apps/company" component={AppCompany} />
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