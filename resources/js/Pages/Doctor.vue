<script setup>
import { ref, onMounted, computed } from 'vue';
import axios from 'axios';
import AppLayout from '@/Layouts/AppLayout.vue';
import GradientLineChart from '@/Components/Charts/GradientLineChart.vue'; // Adjust path if necessary
import { usePage } from '@inertiajs/vue3';

const page = usePage();
const user = computed(() => page.props.auth.user);

const isLocked = ref(false);
const password = ref('');

let timeoutId = null;
const lockTimeout = 300000; // 5 minutes in milliseconds

function lockScreen() {
    isLocked.value = true;
    localStorage.setItem('isLocked', 'true');
}

async function unlock() {
    try {
        const response = await axios.post(route('verify-password'), { password: password.value });
        if (response.data.success) {
            isLocked.value = false;
            password.value = '';
            localStorage.removeItem('isLocked');
            resetLockTimeout();
        } else {
            alert('Incorrect password. Please try again.');
        }
    } catch (error) {
        console.error('Error during password verification:', error);
        alert('An error occurred. Please try again.');
    }
}

function resetLockTimeout() {
    clearTimeout(timeoutId);
    timeoutId = setTimeout(lockScreen, lockTimeout);
}

function handleUserActivity() {
    resetLockTimeout();
}

function manualLock() {
    lockScreen();
}

window.addEventListener('mousemove', handleUserActivity);
window.addEventListener('keydown', handleUserActivity);

onMounted(() => {
    if (localStorage.getItem('isLocked') === 'true') {
        isLocked.value = true;
    } else {
        resetLockTimeout();
    }
});

const users = ref([]);
const selectedUser = ref(null);
const feedbackMessage = ref('');

const fetchUsers = async () => {
    try {
        const response = await axios.get('/users?role=1');
        users.value = response.data;
    } catch (error) {
        console.error('Error fetching users:', error);
    }
};

onMounted(async () => {
    await fetchUsers();
});

const showUserDetails = (user) => {
    selectedUser.value = { ...user };
};

const closeUserDetails = () => {
    selectedUser.value = null;
};

const searchQuery = ref('');

const filteredUsers = computed(() => {
    const query = searchQuery.value.toLowerCase();
    return users.value.filter(user =>
        user.name.toLowerCase().includes(query) || user.id.toString().includes(query)
    );
});

const getGreeting = () => {
    const hours = new Date().getHours();
    if (hours < 12) {
        return 'Good morning';
    } else if (hours < 18) {
        return 'Good afternoon';
    } else {
        return 'Good evening';
    }
};

const greeting = computed(() => getGreeting());

// Hardcoded data for testing GradientLineChart
const hardcodedChartData = {
    labels: ['January', 'February', 'March', 'April', 'May', 'June', 'July'],
    datasets: [
        {
            label: 'Glucose Level',
            data: [120, 125, 130, 135, 140, 145, 150], // Sample data points
            borderColor: 'rgba(75, 192, 192, 1)',
            backgroundColor: 'rgba(75, 192, 192, 0.2)',
            tension: 0.4,
        },
    ],
};

// Debugging output
console.log('Selected User:', selectedUser.value);
</script>

<style>
.blur {
    filter: blur(5px);
    transition: filter 0.3s ease-in-out;
}
</style>

<template>
    <AppLayout title="Dashboard">
        <template #header>
            <div>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    Doctor Dashboard
                </h2>
            </div>
            <div>
                <p><strong>{{ greeting }}</strong>, <strong>Dr.</strong> <strong>{{ user.name }}</strong>,</p>
            </div>
        </template>

        <div class="mt-4 flex justify-center">
            <div class="relative w-full max-w-md">
                <input
                    type="text"
                    v-model="searchQuery"
                    placeholder="Search by name or ID"
                    class="px-4 py-2 pr-10 border rounded-md w-full focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                />
                <svg class="absolute right-3 top-3 h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 4a4 4 0 100 8 4 4 0 000-8zm2 10H6m2-4h-.01M8 12h.01M4 16h16" />
                </svg>
            </div>
        </div>

        <!-- User table -->
        <div class="mt-8 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
            <div v-for="(user, index) in filteredUsers" :key="index" class="bg-white shadow-md rounded-md p-6">
                <div class="text-xl font-semibold mb-2">{{ user.name }}</div>
                <div class="text-gray-600 mb-2">{{ user.email }}</div>
                <div class="text-gray-600">ID: {{ user.id }}</div>
                <!-- <div class="text-gray-600">Role: {{ user.role }}</div> -->
                <div class="mt-4 flex justify-end">
                    <button @click="showUserDetails(user)" class="bg-blue-500 text-white px-3 py-1 rounded">
                        View Data
                    </button>
                </div>
            </div>
        </div>

        <!-- Modal for showing user details -->
        <div v-if="selectedUser" class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50">
            <div class="bg-white rounded-lg p-8 shadow-lg max-w-lg w-full">
                <h3 class="text-xl font-semibold mb-4">User Details</h3>
                <p><strong>ID:</strong> {{ selectedUser.id }}</p>
                <p><strong>Name:</strong> {{ selectedUser.name }}</p>
                <p><strong>Email:</strong> {{ selectedUser.email }}</p>
                <p><strong>Role:</strong> {{ selectedUser.role }}</p>
                <p v-if="selectedUser.additionalData"><strong>Additional Data:</strong> {{ selectedUser.additionalData }}</p>
                
                <!-- Hardcoded GradientLineChart for testing -->
                <gradient-line-chart
                    id="chart-line"
                    title="Sugar Levels Overview"
                    apiUrl="http://127.0.0.1:8000/chart-data"
                    description="<i class='fa fa-arrow-up text-success'></i>
                    <span class='font-weight-bold'> </span> "
                    :chart="{
                        labels: ['Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
                        datasets: [{ label: 'Mobile Apps', data: [0, 0, 0, 0, 0, 0] }]
                    }"
                />

                <div class="mt-4 flex justify-end">
                    <button @click="closeUserDetails" class="bg-red-500 text-white px-3 py-1 rounded">
                        Close
                    </button>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
