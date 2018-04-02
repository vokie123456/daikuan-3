import React from 'react';
import { Layout, } from 'antd';
import { Route, } from 'react-router-dom'

const { Content, } = Layout;

export default class ContentComponent extends React.Component {
    render() {
        return (
            <Content style={styles.content}>
            </Content>
        );
    }
}

var styles = {
    content: {
        backgroundColor: '#fff',
        margin: '12px',
        borderRadius: '4px',
    },
};
