import React, { Component } from 'react';
import ReactDOM from 'react-dom';

import Sider from './layouts/sider';
import Top from './layouts/top';

const App = () => {
    return (
        <div>
            <Top />
            <Sider />
        </div>
    );
};

if (document.getElementById('example')) {
    ReactDOM.render(<App />, document.getElementById('example'));
}