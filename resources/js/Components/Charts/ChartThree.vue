<script setup lang="ts">
import { ref, onMounted } from 'vue';
import axios from 'axios';
import VueApexCharts from 'vue3-apexcharts';

const users = ref([]);
const feedbackMessage = ref(''); // To provide feedback to the user
const roleDistribution = ref({}); // To store the role distribution

const chartData = ref({
  series: [],
  labels: [],
});

const apexOptions = ref({
  chart: {
    type: 'donut',
    width: 380,
  },
  colors: ['#3C50E0', '#6577F3', '#8FD0EF', '#0FADCF'],
  legend: {
    show: false,
    position: 'bottom',
  },
  plotOptions: {
    pie: {
      donut: {
        size: '65%',
        background: 'transparent',
      },
    },
  },
  dataLabels: {
    enabled: false,
  },
  responsive: [
    {
      breakpoint: 640,
      options: {
        chart: {
          width: 200,
        },
      },
    },
  ],
});

onMounted(async () => {
  await fetchData(); // Fetch data when component is mounted
});

const fetchUsers = async () => {
  try {
    const response = await axios.get('/users');
    users.value = response.data;
    calculateRoleDistribution(); // Calculate the role distribution
  } catch (error) {
    console.error('Error fetching users:', error);
  }
};

const calculateRoleDistribution = () => {
  const roleCount = users.value.reduce((acc, user) => {
    acc[user.role] = (acc[user.role] || 0) + 1;
    return acc;
  }, {});
  roleDistribution.value = roleCount;

  // Prepare data for ApexCharts
  chartData.value.series = Object.values(roleDistribution.value);
  chartData.value.labels = Object.keys(roleDistribution.value);
};

const fetchData = async () => {
  try {
    const response = await axios.get('/users'); // Adjust endpoint as per your backend
    if (response.status === 200) {
      // Assuming your API response has `series` and `labels` properties
      chartData.value.series = response.data.series;
      chartData.value.labels = response.data.labels;
    } else {
      console.error('Failed to fetch data:', response.status);
    }
  } catch (error) {
    console.error('Error fetching data:', error);
  }
};
</script>

<template>
  <div class="col-span-12 rounded-sm border border-stroke bg-black px-5 pt-7.5 pb-5 shadow-default dark:border-strokedark dark:bg-boxdark sm:px-7.5 xl:col-span-5">
    <div class="mb-3 justify-between gap-4 sm:flex">
      <div>
        <h4 class="text-xl font-bold text-black dark:text-black"> ROLES</h4>
      </div>
      <div>
        <!-- Optional: Add dropdown or filters here for different views -->
      </div>
    </div>
    <div class="mb-2">
      <div id="chartRoleDistribution" class="mx-auto flex justify-center">
        <VueApexCharts
          type="donut"
          width="340"
          :options="apexOptions"
          :series="chartData.series"
        />
      </div>
    </div>
    <div class="-mx-8 flex flex-wrap items-center justify-center gap-y-3">
      <div class="w-full px-8 sm:w-1/2" v-for="(label, index) in chartData.labels" :key="index">
        <div class="flex w-full items-center">
          <span :style="{ backgroundColor: apexOptions.colors[index % apexOptions.colors.length] }" class="mr-2 block h-3 w-full max-w-3 rounded-full"></span>
          <p class="flex w-full justify-between text-sm font-medium text-white dark:text-black">
            <span>{{ label }}</span>
            <span>{{ chartData.series[index] }}%</span>
          </p>
        </div>
      </div>
    </div>
  </div>
</template>

<style scoped>
/* Add your scoped styles here */
</style>
