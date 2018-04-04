import React from 'react';
import { Layout, } from 'antd';

const { Footer, } = Layout;

export default class ContentComponent extends React.Component {
    render() {
        return (
            <Footer style={styles.footer}>
                <span>Copyright ©2018-2020 SHIDING Corporation, All Rights Reserved</span>
            </Footer>
        );
    }
}

var styles = {
    footer: {
        textAlign: 'center',
    },
};