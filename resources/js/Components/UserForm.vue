<template>
    <div>
        <button @click="toggleFormVisibility" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
            {{ showForm ? 'Close Form' : 'Create New User' }}
        </button>

        <div v-if="showForm" class="max-w-md mx-auto mt-4">
            <form @submit.prevent="createUser" class="bg-white shadow-md rounded px-8 pt-6 pb-8 mb-4">
                <!-- Name Input -->
                <div class="mb-4">
                    <label for="name" class="block text-gray-700 text-sm font-bold mb-2">Name:</label>
                    <input type="text" v-model="name" id="name" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" required>
                </div>

                <!-- Email Input -->
                <div class="mb-4">
                    <label for="email" class="block text-gray-700 text-sm font-bold mb-2">Email:</label>
                    <input type="email" v-model="email" id="email" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" required>
                </div>

                <!-- Role Selector -->
                <div class="mb-4">
                    <label for="role" class="block text-gray-700 text-sm font-bold mb-2">Role:</label>
                    <select v-model="role" id="role" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" required>
                        <option value="patient">Patient</option>
                        <option value="doctor">Doctor</option>
                        <option value="admin">Admin</option>
                    </select>
                </div>

                <!-- Password Input -->
                <div class="mb-4">
                    <label for="password" class="block text-gray-700 text-sm font-bold mb-2">Password:</label>
                    <input type="password" v-model="password" id="password" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                </div>

                <!-- Confirm Password Input -->
                <div class="mb-6">
                    <label for="password_confirmation" class="block text-gray-700 text-sm font-bold mb-2">Confirm Password:</label>
                    <input type="password" v-model="password_confirmation" id="password_confirmation" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                </div>

                <!-- Submit Button -->
                <div class="flex items-center justify-between">
                    <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                        Create User
                    </button>
                </div>
            </form>
        </div>
    </div>
</template>

<script setup lang="ts">
import { ref } from 'vue';
import axios from 'axios';

const name = ref('');
const email = ref('');
const role = ref('patient'); // Default role
const password = ref('');
const password_confirmation = ref('');
const showForm = ref(false); // Initially hide the form

const createUser = async () => {
    try {
        const response = await axios.post('/users', {
            name: name.value,
            email: email.value,
            role: role.value,
            password: password.value || null, // Use default if empty
            password_confirmation: password_confirmation.value || null,
        });

        if (response.data.success) {
            alert('User created successfully!');
            // Clear form fields or redirect as needed
            name.value = '';
            email.value = '';
            role.value = 'patient'; // Reset role to default
            password.value = '';
            password_confirmation.value = '';
            showForm.value = false; // Hide the form after successful creation
        }
    } catch (error) {
        console.error('Error creating user:', error);
        alert('An error occurred. Please try again.');
    }
};

const toggleFormVisibility = () => {
    showForm.value = !showForm.value;
};
</script>

<style scoped>
/* Add scoped styles here if needed */
</style>
