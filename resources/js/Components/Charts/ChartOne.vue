<script setup lang="ts">
import { ref, onMounted, watch } from 'vue';
import axios from 'axios';
import { Chart, registerables } from 'chart.js';
Chart.register(...registerables);

const users = ref([]);
const feedbackMessage = ref(''); // To provide feedback to the user
const roleDistribution = ref({}); // To store the role distribution
let chartInstance = null;

// Dynamically load Font Awesome
const loadFontAwesome = () => {
  const link = document.createElement('link');
  link.rel = 'stylesheet';
  link.href = 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css';
  document.head.appendChild(link);
};

onMounted(async () => {
  loadFontAwesome(); // Load Font Awesome when the component is mounted
  await fetchUsers(); // Fetch users initially
  renderChart(); // Render the initial chart
});



const fetchUsers = async () => {
  try {
    const response = await axios.get('/users');
    console.log(response.data); // Debug the response
    users.value = response.data;
    calculateRoleDistribution(); // Calculate the role distribution
  } catch (error) {
    console.error('Error fetching users:', error);
  }
};

// Calculate role distribution
const calculateRoleDistribution = () => {
  const roleCount = users.value.reduce((acc, user) => {
    acc[user.role] = (acc[user.role] || 0) + 1;
    return acc;
  }, {});
  roleDistribution.value = roleCount;
  if (chartInstance) {
    updateChart();
  }
};

onMounted(() => {
  const canvas = document.getElementById('roleChart');
  if (!canvas) {
    console.error('Canvas element with id roleChart not found.');
    return;
  }
  const ctx = canvas.getContext('2d');
  if (!ctx) {
    console.error('Canvas context (2d) not available.');
    return;
  }
  // Continue with your chart rendering logic here
});
// Render chart as a donut chart
const renderChart = () => {
  const ctx = document.getElementById('roleChart').getContext('2d');
  chartInstance = new Chart(ctx, {
    type: 'doughnut', // Change to doughnut for a donut chart
    data: {
      labels: Object.keys(roleDistribution.value),
      datasets: [
        {
          data: Object.values(roleDistribution.value),
          backgroundColor: ['#FF6384', '#36A2EB', '#FFCE56', '#4BC0C0', '#9966FF', '#FF9F40'],
        },
      ],
    },
    options: {
      responsive: true,
      plugins: {
        legend: {
          position: 'bottom',
        },
      },
    },
  });
};

// Update chart
const updateChart = () => {
  chartInstance.data.labels = Object.keys(roleDistribution.value);
  chartInstance.data.datasets[0].data = Object.values(roleDistribution.value);
  chartInstance.update();
};

watch(users, calculateRoleDistribution);
</script>

<template>
  <div>
    <h2 class="text-2xl font-semibold mb-4 text-center">User Role Distribution</h2>
    <div class="rounded-sm border border-stroke bg-white px-5 pt-6 pb-2.5 shadow-default dark:border-strokedark dark:bg-boxdark sm:px-7.5 xl:pb-1">
      <h4 class="mb-6 text-xl font-semibold text-black dark:text-black">Roles</h4>

      <div v-if="feedbackMessage" class="mb-4 p-2 bg-green-100 text-green-800 rounded">
        {{ feedbackMessage }}
      </div>

      <!-- Donut chart -->
        <canvas id="roleChart" style="width: 300px; height: auto;"></canvas>
    </div>
  </div>
</template>

<style scoped>
/* Additional styles can be added here if needed */
</style>