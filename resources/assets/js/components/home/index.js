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
    'txt': '今日APP浏览量',
    'color': '#8fc9fb',
}, {
    'key': 'promote',
    'txt': '今日推广量',
    'color': '#f69898',
}, {
    'key': 'register',
    'txt': '今日注册量 (推荐 + 无推荐)',
    'color': '#d897eb',
}, {
    'key': 'activate',
    'txt': '今日激活量',
    'color': '#63ea91',
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
                text: '近%s天流量',
            },
            tooltip: {
                trigger: 'axis',
                confine: true,
                backgroundColor: '#fff',
                padding: 20,
                lineHeight: 60,
                textStyle: {
                    color: '#777',
                    fontSize: 14,
                },
                borderColor: '#aaa',
                extraCssText: 'box-shadow: 0 0 4px rgba(0, 0, 0, 0.3);',
                axisPointer: {
                    lineStyle: {
                        width: 1,
                        color: 'rgba(20, 20, 20, 0.2)',
                        type: 'dashed',
                    },
                },
            },
            legend: {
                data:[],
                itemGap: 20,
                textStyle: {
                    color: 'gray',
                },
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
                data: [],
                axisLine : {
                    show: false,
                },
                axisTick: {
                    show: false,
                },
                axisLabel: {
                    color: '#666',
                    //fontSize: 13,
                    fontWeight: 500,
                },
            },
            yAxis: {
                type: 'value',
                axisLine: {
                    show: false,
                },
                axisTick: {
                    show: false,
                },
                axisLabel: {
                    color: '#999',
                    //fontSize: 13,
                    fontWeight: 500,
                },
                splitLine: {
                    show: true,
                    lineStyle: {
                        width: 2,
                        type: 'dotted', //dashed
                        color: 'rgba(200, 200, 200, 0.6)',
                    },
                },
            },
            series: [{
                type: "line",
                data: [],
                lineStyle: {
                    color: '#8fc9fb',
                    width: 3,
                },
                itemStyle: {
                    color: '#8fc9fb',
                },
                smooth: true,
                symbol: 'circle',
                symbolSize: 8,
            }, {
                type: "line",
                data: [],
                lineStyle: {
                    color: '#f69898',
                    width: 3,
                },
                itemStyle: {
                    color: '#f69898',
                },
                smooth: true,
                symbol: 'circle',
                symbolSize: 8,
            }, {
                type: "line",
                data: [],
                lineStyle: {
                    color: '#d897eb',
                    width: 3,
                },
                itemStyle: {
                    color: '#d897eb',
                },
                smooth: true,
                symbol: 'circle',
                symbolSize: 8,
            }, {
                type: "line",
                data: [],
                lineStyle: {
                    color: '#63ea91',
                    width: 3,
                },
                itemStyle: {
                    color: '#63ea91',
                },
                smooth: true,
                symbol: 'circle',
                symbolSize: 8,
            }, ],
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
                    this.option.title.text = this.option.title.text.replace(/\%s/, this.option.xAxis.data.length);
                    let series = result.datas.series || [];
                    this.option.series = this.option.series.map((item, index) => {
                        if(series[index]) {
                            return Object.assign({}, item, series[index]);
                        }else {
                            return item;
                        }
                    });
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
            <div style={styles.body} className="divStyle">
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
