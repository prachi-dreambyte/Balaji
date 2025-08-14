/**
 * Theme: Larkon - Responsive Bootstrap 5 Admin Dashboard
 * Author: Techzaa
 * Module/App: Dashboard
 */

//
// Conversions
//
var options = {
    chart: {
        height: 292,
        type: 'radialBar',
    },
    plotOptions: {
        radialBar: {
            startAngle: -135,
            endAngle: 135,
            dataLabels: {
                name: {
                    fontSize: '14px',
                    color: undefined,
                    offsetY: 100
                },
                value: {
                    offsetY: 55,
                    fontSize: '20px',
                    color: undefined,
                    formatter: function (val) {
                        return val + "%";
                    }
                }
            },
            track: {
                background: "rgba(170,184,197, 0.2)",
                margin: 0
            },
        }
    },
    fill: {
        gradient: {
            enabled: true,
            shade: 'dark',
            shadeIntensity: 0.2,
            inverseColors: false,
            opacityFrom: 1,
            opacityTo: 1,
            stops: [0, 50, 65, 91]
        },
    },
    stroke: {
        dashArray: 4
    },
    colors: ["#7f56da", "#22c55e"],
    series: [65.2],
    labels: ['Returning Customer'],
    responsive: [{
        breakpoint: 380,
        options: {
            chart: {
                height: 180
            }
        }
    }],
    grid: {
        padding: {
            top: 0,
            right: 0,
            bottom: 0,
            left: 0
        }
    }
};

var chart = new ApexCharts(
    document.querySelector("#conversions"),
    options
);
chart.render();


//
// Performance-chart - Total Revenue from DB
//
fetch('get_revenue_data.php')
    .then(res => res.json())
    .then(chartData => {
        var options = {
            series: [{
                name: "Total Revenue",
                type: "line",
                data: chartData.revenues
            }],
            chart: {
                height: 313,
                type: "line",
                toolbar: { show: false },
            },
            stroke: {
                width: 2,
                curve: 'smooth'
            },
            fill: {
                opacity: 1,
                type: 'solid'
            },
            markers: {
                size: 4,
                strokeWidth: 2,
                hover: { size: 6 },
            },
            xaxis: {
                categories: chartData.months,
                axisTicks: { show: false },
                axisBorder: { show: false },
            },
            yaxis: {
                min: 0,
                labels: {
                    formatter: val => "₹" + val.toLocaleString()
                },
                axisBorder: { show: false }
            },
            grid: {
                show: true,
                strokeDashArray: 3,
                padding: { top: 0, right: -2, bottom: 0, left: 10 }
            },
            legend: { show: false },
            colors: ["#22c55e"],
            tooltip: {
                y: {
                    formatter: val => "₹" + val.toLocaleString()
                }
            }
        };

        var chart = new ApexCharts(
            document.querySelector("#dash-performance-chart"),
            options
        );
        chart.render();
    })
    .catch(err => console.error('Error loading revenue chart data:', err));
