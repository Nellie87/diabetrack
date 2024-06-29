<script setup>
import { ref, onMounted } from 'vue';
import axios from 'axios';
import AppLayout from '@/Layouts/AppLayout.vue';
import { computed } from 'vue';
import { usePage } from '@inertiajs/vue3';
import { defineProps } from 'vue';


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
        const response = await axios.get('/users');
        console.log(response.data); // Debug the response
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
const props = defineProps({
  user: {
    type: Object,
    required: true
  }
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
</script>

<template>
    <AppLayout title="Dashboard">
        <template #header>
            <div>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Doctor Dashboard
            </h2>
            </div>
            <div>
                <p><strong>{{ greeting }}</strong>,<strong> Dr.</strong> <strong>{{ user.name }}</strong>,</p>
            </div>
        </template>

        <!-- Main content -->
        <div :class="{ 'blur': isLocked }" class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                    <Welcome />
                </div>
            </div>
        </div>

        <!-- User table -->
        <div class="mt-8 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
            <div v-for="(user, index) in users" :key="index" class="bg-white shadow-md rounded-md p-6">
                <div class="text-xl font-semibold mb-2">{{ user.name }}</div>
                <div class="text-gray-600 mb-2">{{ user.email }}</div>
                <div class="text-gray-600">Role {{ user.role }}</div>
                <div class="mt-4 flex justify-end">
                    <button @click="showUserDetails(user)" class="bg-blue-500 text-white px-3 py-1 rounded">
                        Show More
                    </button>
                </div>
            </div>
        </div>

        <!-- Modal for showing user details -->
        <div v-if="selectedUser" class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50">
            <div class="bg-white rounded-lg p-8 shadow-lg max-w-lg w-full">
                <h3 class="text-xl font-semibold mb-4">User Details</h3>
                <p><strong>Name:</strong> {{ selectedUser.name }}</p>
                <p><strong>Email:</strong> {{ selectedUser.email }}</p>
                <p><strong>Role:</strong> {{ selectedUser.role }}</p>
                <p><strong>Additional Data:</strong> {{ selectedUser.additionalData }}</p>
                <div class="mt-4 flex justify-end">
                    <button @click="closeUserDetails" class="bg-red-500 text-white px-3 py-1 rounded">
                        Close
                    </button>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<style>
.blur {
    filter: blur(5px);
    transition: filter 0.3s ease-in-out;
}
</style>
