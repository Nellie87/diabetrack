<template>
  <div>
    <div class="p-3 pb-0 card-header">
      <select v-model="selectedapiUrl" @change="fetchChartData">
        <option v-for="route in routes" :key="route.apiUrl" :value="route.apiUrl">
          {{ route.name }}
        </option>
      </select>
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
  },

  data() {
    return {
      chartData: null,
      selectedapiUrl: '',
      routes: [
        { name: 'Breakfast Chart', apiUrl: "http://127.0.0.1:8000/chart-datas" },
        { name: 'Lunch Chart', apiUrl: "http://127.0.0.1:8000/chart2" },
        { name: 'Dinner Chart', apiUrl:"http://127.0.0.1:8000/chart3" },
        { name: 'Overall Chart', apiUrl:"http://127.0.0.1:8000/chart4" },
      ],
      chart: null,
    };
  },

  methods: {
    async fetchChartData() {
      try {
        const response = await fetch(this.selectedapiUrl);
        const data = await response.json();
        console.log('Fetched data:', data);

  
        if (Array.isArray(data)) {
          const chartData = {
            labels: [],
            datasets: [{
              label: 'Glucose Consumed (g)',
              data: [],
              borderColor: 'rgba(75, 192, 192, 1)',
              backgroundColor: 'rgba(75, 192, 52, 1)',
              fill: true,
            }],
          };

          data.forEach(item => {
            // Parse datetime string into Date object
            const date = parse(item.Date, 'MM-dd HH:mm', new Date());

            // Add date and glucose level to the data array
            chartData.datasets[0].data.push({
              x: item.Date,
              y: item.Carbohydrates,
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

      const xValues = this.chartData.datasets[0].data.map(d => d.x);
    const xMin = new Date(Math.min(...xValues));
    const xMax = new Date(Math.max(...xValues));

    // Add padding of one day to x-axis
    xMin.setDate(xMin.getDate() - 5);
    xMax.setDate(xMax.getDate() + 5);

    

      new Chart(ctx, {
        type: "bar",
        data: {
          labels: this.chartData.labels,
          datasets: this.chartData.datasets.map((dataset) => ({
            ...dataset,
            tension: 1,
            borderRadius: 4,
            borderWidth: 0,
            borderSkipped: false,
            maxBarThickness: 10,
            minBarThickness: 10,
            minBarLength: 5    
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
                display: false,
                drawOnChartArea: false,
                drawTicks: false,
                borderDash: [5, 5],
              },
              ticks: {
                suggestedMin: 0,
                suggestedMax: 1500,
                beginAtZero: true,
                display: true,
                padding: 15,
                color: "#b2b9bf",
                font: {
                  size: 14,
                  family: "Open Sans",
                  style: "normal",
                  lineHeight: 2,
                },
              },
              color: "#fff",

            },
            x: {
              type: 'time',
              time: {
                unit: 'day', 
                round: 'hour',
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
              min: xMin,
              max: xMax,
              grid: {
                drawBorder: false,
                display: true,
                drawOnChartArea: false,
                drawTicks: false,
                borderDash: [5, 5],
              },
              ticks: {
                display: true,
                color: "#b2b6bf",
                padding: 30,
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
  mounted() {
    this.selectedapiUrl = this.routes[0].apiUrl; 
    this.fetchChartData();
  },
};
</script>

<style>
select {
    border-color: #ffffff;
    border-width: 1px;
    border-radius: 20px;
}

</style>

