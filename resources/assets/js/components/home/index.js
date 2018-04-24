import React from 'react';
import echarts from 'echarts/lib/echarts';
import 'echarts/lib/chart/line';
import 'echarts/lib/chart/bar';
import 'echarts/lib/component/legend';
import 'echarts/lib/component/tooltip';
import 'echarts/lib/component/title';

import Api from '../public/api';
import Utils from '../public/utils';


const block = [{
    'key': 'app_visit',
    'txt': '今日APP浏览量 (会员 + 游客)',
    'color': '#8e1ce6',
}, {
    'key': 'promote',
    'txt': '今日推广量',
    'color': '#e48276',
}, {
    'key': 'register',
    'txt': '今日注册量 (无推荐 + 推荐)',
    'color': '#f9c312',
}, {
    'key': 'activate',
    'txt': '今日激活量',
    'color': '#2DCE5D',
},];

class Home extends React.Component {
    constructor(props) {
        super(props);
        this.state = {
            datas: null,
        }
        this.normal = true;
        this.option = {
            title: {
                text: '近七天流量'
            },
            tooltip: {
                trigger: 'axis'
            },
            legend: {
                data:[]
            },
            grid: {
                left: '3%',
                right: '4%',
                bottom: '3%',
                containLabel: true
            },
            toolbox: {
                feature: {
                    saveAsImage: {}
                }
            },
            xAxis: {
                type: 'category',
                boundaryGap: false,
                data: []
            },
            yAxis: {
                type: 'value'
            },
            series: [],
        };
    }

    componentDidMount() {
        Utils.axios({
            key: 'datas',
            url: Api.getCounts,
            isAlert: false,
            method: 'get',
        }, (result) => {
            if(this.normal) {
                this.setState({
                    datas: result.today || null,
                });
                if(result.datas && this.refs.echarts) {
                    this.option.legend.data = result.datas.legend || [];
                    this.option.xAxis.data = result.datas.xaxis || [];
                    let series = result.datas.series || [];
                    for(let i in series) {
                        series[i].type = 'line';
                        series[i].stack = '总量';
                    }
                    this.option.series = series;
                    let myChart = echarts.init(this.refs.echarts);
                    myChart.setOption(this.option);
                    window.addEventListener("resize", myChart.resize);
                }
            }
        });
    }

    componentWillUnmount() {
        this.normal = false;
    }

    render() {
        const { datas, } = this.state;
        if(!datas) return null;
        // console.log(this.option);
        return (
            <div style={styles.body}>
                <div style={styles.blockBox}>
                    {!datas ? null : block.map((item, index) => {
                        let count = datas[item.key] || 0;
                        return (
                            <div
                                key={index}
                                style={Object.assign({}, styles.blockItem, {backgroundColor: item.color})}
                            >
                                <div style={styles.titleStyle}>{item.txt}</div>
                                <div style={styles.countStyle}>{count}</div>
                            </div>
                        )
                    })}
                </div>
                <div ref="echarts" style={styles.canvasBox}></div>
            </div>
        );
    }
}

var styles = {
    body: {
        display: 'flex',
        flexDirection: 'column',
        flex: 1,
    },
    blockBox: {
        display: 'flex',
        justifyContent: 'space-between',
    },
    blockItem: {
        width: 'calc(25% - 15px)',
        padding: '15px 10px',
        color: '#fff',
        lineHeight: '30px',
        borderRadius: 5,
    },
    titleStyle: {
        fontSize: 13,
        borderBottom: '0.5px solid rgba(255, 255, 255, 0.2)',
    },
    countStyle: {
        fontSize: 30,
        marginTop: 6,
    },
    canvasBox: {
        flex: 1,
        marginTop: 40,
        padding: 10,
        border: '1px solid #eee',
    },
};


export default Home;
