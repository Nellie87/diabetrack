<template>
    <div>
      <h2>Task Progress</h2>
      <canvas :id="id" :height="height"></canvas>
    </div>
     </template>
  
  <script>
  
  import { Doughnut } from 'vue-chartjs';
  import { Chart, registerables } from 'chart.js';
  import axios from 'axios';
  
  Chart.register(...registerables);
  
  export default {
    name: "ProgressDoughnutChart",
    components: {
      Doughnut
    },
    props: {
      id: {
        type: String,
        required: true,
      },
      height: {
        type: String,
        default: "300",
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
          const response = await axios.get(this.apiUrl);
          const data = response.data;
          console.log('Fetched data:', data);
  
          // Transform data into chart-compatible format
          if (Array.isArray(data)) {
            const chartData = {
              labels: data.map(item => item.task_name),
              datasets: [{
                label: 'Progress',
                data: data.map(item => item.progress),
                backgroundColor: [
                  'rgba(255, 99, 132, 0.2)',
                  'rgba(54, 162, 235, 0.2)',
                  'rgba(255, 206, 86, 0.2)',
                  'rgba(75, 192, 192, 0.2)',
                  'rgba(153, 102, 255, 0.2)',
                  'rgba(255, 159, 64, 0.2)'
                ],
                borderColor: [
                  'rgba(255, 99, 132, 1)',
                  'rgba(54, 162, 235, 1)',
                  'rgba(255, 206, 86, 1)',
                  'rgba(75, 192, 192, 1)',
                  'rgba(153, 102, 255, 1)',
                  'rgba(255, 159, 64, 1)'
                ],
                borderWidth: 1
              }]
            };
  
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
          type: "doughnut",
          data: this.chartData,
          options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
              legend: {
                display: true,
              },
            },
          },
        });
      },
    },
  };
  </script>
  
  <style>
  canvas {
    width: 100% !important;
    height: 300px !important;
  }
  </style>
  