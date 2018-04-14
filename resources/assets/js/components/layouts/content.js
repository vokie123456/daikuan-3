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
import Banners from '../banner/index';
import BannerForm from '../banner/form';

const { Content, } = Layout;

export default class ContentComponent extends React.Component {
    render() {
        return (
            <Content style={styles.content}>
                <Route path="/" exact component={Home} />
                <Route path="/apps" exact component={Apps} />
                <Route path="/apps/create" exact component={AppCreate} />
                <Route path="/apps/update/:id" component={AppCreate} />
                <Route path="/apps/company" component={AppCompany} />
                <Route path="/category/:type" exact component={Category} />
                <Route path="/category/:type/form/:id?" component={CategoryForm} />
                <Route path="/categories/apps" component={CategoryApps} />
                <Route path="/banners" exact component={Banners} />
                <Route path="/banner/create" component={BannerForm} />
                <Route path="/banner/update/:id" component={BannerForm} />
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
