<template>
  <div>
    <div class="p-3 pb-0 card-header">
      <h6>{{ title }}</h6>
      <p v-if="description" class="text-sm" v-html="description"></p>
    </div>
    <div class="p-3 card-body">
      <div class="chart">
        <canvas :id="id" class="chart-canvas" :height="height"></canvas>
      </div>
    </div>
  </div>
</template>

<script>
import { Chart, registerables } from 'chart.js';
import 'chartjs-adapter-date-fns';
import { parse } from 'date-fns';

Chart.register(...registerables);

export default {
  name: "BarChart",
  props: {
    id: {
      type: String,
      required: true,
    },
    height: {
      type: String,
      default: "300",
    },
    title: {
      type: String,
      default: "",
    },
    description: {
      type: String,
      default: "",
    },
    apiUrl: {
      type: String,
      required: true,
    },
  },

  data() {
    return {
      chartData: null,
    };
  },

  mounted() {
    this.fetchChartData();
  },

  methods: {
    async fetchChartData() {
      try {
        const response = await fetch(this.apiUrl);
        const data = await response.json();
        console.log('Fetched data:', data);

  

        // Transform data into chart-compatible format
        if (Array.isArray(data)) {
          const chartData = {
            labels: [],
            datasets: [{
              label: 'Glucose Consumed',
              data: [],
              borderColor: 'rgba(75, 192, 192, 1)',
              backgroundColor: 'rgba(75, 192, 192, 0.2)',
              fill: true,
            }],
          };

          data.forEach(item => {
            // Parse datetime string into Date object
            const date = parse(item.Date, 'MM-dd HH:mm', new Date());

            // Add date and glucose level to the data array
            chartData.datasets[0].data.push({
              x: item.Date,
              y: item.Original,
            });
          });

          // Sort the data by date
          chartData.datasets[0].data.sort((a, b) => a.x - b.x);

          this.chartData = chartData;
          this.renderChart();
        } else {
          console.error('Unexpected data format:', data);
        }
      } catch (error) {
        console.error('Error fetching chart data:', error);
      }
    },

    renderChart() {
      if (!this.chartData) {
        console.error('No chart data available to render');
        return;
      }

      const ctx = document.getElementById(this.id).getContext("2d");

      const existingChart = Chart.getChart(this.id);
      if (existingChart) {
        existingChart.destroy();
      }

      new Chart(ctx, {
        type: "bar",
        data: {
          labels: this.chartData.labels,
          datasets: this.chartData.datasets,
        },
        options: {
          responsive: true,
          maintainAspectRatio: false,
          plugins: {
            legend: {
              display: true,
            },
          },
          interaction: {
            intersect: false,
            mode: "index",
          },
          scales: {
            y: {
              grid: {
                drawBorder: false,
                display: true,
                drawOnChartArea: true,
                drawTicks: false,
                borderDash: [5, 5],
              },
              ticks: {
                display: true,
                padding: 10,
                color: "#b2b9bf",
                font: {
                  size: 11,
                  family: "Open Sans",
                  style: "normal",
                  lineHeight: 2,
                },
              },
            },
            x: {
              type: 'time',
              time: {
                unit: 'minute', 
                round: 'minute',
                displayFormats: {
                  millisecond: 'MMM dd, HH:mm:ss.SSS',
                  second: 'MMM dd, HH:mm:ss',
                  minute: 'MMM dd, HH:mm',
                  hour: 'MMM dd, HH:00',
                  day: 'MMM dd', 
                  week: 'MMM dd', 
                  month: 'MMM YYYY', 
                  quarter: '[Q]Q - YYYY', 
                  year: 'YYYY', 
                },
              },
              grid: {
                drawBorder: false,
                display: false,
                drawOnChartArea: false,
                drawTicks: false,
                borderDash: [5, 5],
              },
              ticks: {
                display: true,
                color: "#b2b9bf",
                padding: 20,
                font: {
                  size: 11,
                  family: "Open Sans",
                  style: "normal",
                  lineHeight: 2,
                },
              },
            },
          },
        },
      });
    },
  },
};
</script>

