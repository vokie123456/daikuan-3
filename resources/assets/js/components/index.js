import React from 'react';
import ReactDOM from 'react-dom';
import { BrowserRouter as Router, Route } from "react-router-dom";

import { Layout, Breadcrumb } from 'antd';

import Api from './public/api';
import Utils from './public/utils';
import SiderComponent from './layouts/sider';
import HeaderComponent from './layouts/header';
import ContentComponent from './layouts/content';
import FooterComponent from './layouts/footer';

class App extends React.Component {
    constructor(props) {
        super(props);
        this.state = {
            admin: null,
        };
    }

    componentDidMount() {
        Utils.axios(Api.getadmininfo, {}, (result) => {
            this.setState({admin: result});
        }, 'admin', false);
    }

    render() {
        if(!this.state.admin) return null;
        return (
            <Router basename='/admin'>
                <Layout>
                    <Route path="/*" component={SiderComponent} />
                    <Layout>
                        <HeaderComponent admin={this.state.admin} />
                        <ContentComponent />
                        <FooterComponent />
                    </Layout>
                </Layout>
            </Router>
        );
    }
};

if (document.getElementById('example')) {
    ReactDOM.render(<App />, document.getElementById('example'));
}