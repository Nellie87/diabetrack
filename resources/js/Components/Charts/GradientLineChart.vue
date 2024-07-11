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
  name: "GradientLineChart",
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
    pollInterval: {
      type: Number,
      default: 30000, // Default polling interval in milliseconds (30 seconds)
    },
  },

  data() {
    return {
      chartData: null,
    };
  },

  mounted() {
    this.fetchChartData(); // Initial fetch
    this.setupPolling(); // Start polling
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
              label: 'Glucose Level (mg/dl)',
              data: [],
              borderColor: 'rgba(75, 192, 192, 1)',
              backgroundColor: 'rgba(75, 192, 192, 0.2)',
              fill: true,
            }],
          };

          data.forEach(item => {
            // Parse datetime string into Date object
            const date = parse(item.Datetime, 'MM-dd HH:mm', new Date());

            // Add datetime to labels if not already added
            if (!chartData.labels.find(label => label.getTime() === date.getTime())) {
              chartData.labels.push(date);
            }

            // Add glucose level to data array
            chartData.datasets[0].data.push({
              x: date,
              y: item.GlucoseLevel,
            });
          });

          // Sort the data by date
          chartData.labels.sort((a, b) => a - b);
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

      const gradientStroke1 = ctx.createLinearGradient(0, 230, 0, 50);
      gradientStroke1.addColorStop(1, "rgba(203,12,159,0.2)");
      gradientStroke1.addColorStop(0.2, "rgba(72,72,176,0.0)");
      gradientStroke1.addColorStop(0, "rgba(203,12,159,0)");

      const existingChart = Chart.getChart(this.id);
      if (existingChart) {
        existingChart.destroy();
      }

      new Chart(ctx, {
        type: "line",
        data: {
          labels: this.chartData.labels,
          datasets: this.chartData.datasets.map((dataset) => ({
            ...dataset,
            backgroundColor: gradientStroke1,
            tension: 0.1,
            borderWidth: 0,
            pointRadius: 0,
            borderColor: "#cb0c9f",
            // eslint-disable-next-line no-dupe-keys
            borderWidth: 3,
            maxBarThickness: 6,
            fill: true,
          })),
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

    setupPolling() {
      setInterval(() => {
        this.fetchChartData();
      }, this.pollInterval);
    },
  },
};
</script>
