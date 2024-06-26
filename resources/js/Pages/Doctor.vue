<script setup>
import { ref, onMounted } from 'vue';
import axios from 'axios';
import AppLayout from '@/Layouts/AppLayout.vue';
import { computed } from 'vue';
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
const editingUser = ref(null);
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

const startEditing = (user) => {
    editingUser.value = { ...user };
};

const cancelEditing = () => {
    editingUser.value = null;
    feedbackMessage.value = '';
};

const saveUser = async (user) => {
    try {
        const response = await axios.put(`/users/${user.id}`, { role: user.role });
        console.log(response.data);
        const index = users.value.findIndex((u) => u.id === user.id);
        if (index !== -1) {
            users.value[index] = response.data;
        }
        editingUser.value = null;
        feedbackMessage.value = 'User updated successfully!';
    } catch (error) {
        console.error('Error updating user:', error.response ? error.response.data : error.message);
        feedbackMessage.value = `Error updating user: ${error.response ? error.response.data.message : error.message}`;
    }
};

const deleteUser = async (user) => {
    if (!confirm('Are you sure you want to delete this user?')) return;
    try {
        await axios.delete(`/users/${user.id}`);
        users.value = users.value.filter((u) => u.id !== user.id);
        feedbackMessage.value = 'User deleted successfully!';
    } catch (error) {
        console.error('Error deleting user:', error);
        feedbackMessage.value = 'Error deleting user.';
    }
};
</script>

<template>
    <AppLayout title="Dashboard">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
               Doctor Dashboard
            </h2>
        </template>

        <!-- Main content -->
         
        <div :class="{ 'blur': isLocked }" class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                    <Welcome />
                </div>
            </div>
        </div>
        <!-- <div class="p-6 lg:p-8 bg-white border-b border-gray-200">
            <ApplicationLogo class="block h-12 w-auto" /> -->
<!-- 
            <h1 class="mt-8 text-2xl font-medium text-gray-900">
                Welcome , {{ user.name }}!
            </h1> -->

            <!-- User table -->
        <div class="mt-8 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
            <div v-for="(user, index) in users" :key="index" class="bg-white shadow-md rounded-md p-6">
                <div class="text-xl font-semibold mb-2">{{ user.name }}</div>
                <div class="text-gray-600 mb-2">{{ user.email }}</div>
                <div class="text-gray-600">Role {{ user.role }}</div>
                <div class="mt-4 flex justify-end">
                    <button @click="startEditing(user)" class="bg-blue-500 text-white px-3 py-1 rounded">
                        <i class="fas fa-edit"></i> Edit
                    </button>
                    <button @click="deleteUser(user)" class="bg-red-500 text-white px-3 py-1 rounded ml-2">
                        <i class="fas fa-trash"></i> Delete
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
