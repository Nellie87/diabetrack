<template>
  <div class="p-3 pb-0 card-header">
    <h6>{{ title }}</h6>
    <!-- eslint-disable vue/no-v-html -->
    <p v-if="description" class="text-sm" v-html="description" />
  </div>
  <div class="p-3 card-body">
    <div class="chart">
      <canvas :id="id" class="chart-canvas" :height="height"></canvas>
    </div>
  </div>
</template>

<script>
import { Chart, registerables } from 'chart.js';
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
    initialChartData: {
      type: Object,
      required: true,
    },
  },

  data() {
    return {
      chartData: this.initialChartData, // Initialize with the initial chart data from props
    };
  },

  mounted() {
    this.fetchChartData();
  },

  methods: {
    fetchChartData() {
      // Replace with your API endpoint
      const apiUrl = 'http://127.0.0.1:8000/api/chart-data';

      fetch(apiUrl)
        .then(response => response.json())
        .then(data => {
          console.log('Fetched data:', data); // Log the fetched data to inspect its structure

          // Transform data if necessary
          if (Array.isArray(data)) {
            data = {
              labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'], // Example labels
              datasets: [
                {
                  label: 'Fetched Data',
                  data: data,
                },
              ],
            };
          }

          // Validate the transformed data structure
          if (data && data.labels && data.datasets && Array.isArray(data.datasets)) {
            this.chartData = data;
            this.renderChart();
          } else {
            console.error('Invalid data structure after transformation:', data);
          }
        })
        .catch(error => {
          console.error('Error fetching chart data:', error);
        });
    },

    renderChart() {
      var ctx = document.getElementById(this.id).getContext("2d");

      var gradientStroke1 = ctx.createLinearGradient(0, 230, 0, 50);

      gradientStroke1.addColorStop(1, "rgba(203,12,159,0.2)");
      gradientStroke1.addColorStop(0.2, "rgba(72,72,176,0.0)");
      gradientStroke1.addColorStop(0, "rgba(203,12,159,0)");

      let chartStatus = Chart.getChart(this.id);
      if (chartStatus != undefined) {
        chartStatus.destroy();
      }

      new Chart(ctx, {
        type: "line",
        data: {
          labels: this.chartData.labels,
          datasets: this.chartData.datasets.map((dataset, index) => ({
            label: dataset.label,
            tension: 0.4,
            borderWidth: 0,
            pointRadius: 0,
            borderColor: index === 0 ? "#cb0c9f" : "#3A416F",
            borderWidth: 3,
            backgroundColor: gradientStroke1,
            fill: true,
            data: dataset.data,
            maxBarThickness: 6,
          })),
        },
        options: {
          responsive: true,
          maintainAspectRatio: false,
          plugins: {
            legend: {
              display: false,
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
