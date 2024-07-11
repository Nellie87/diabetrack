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
const messageContent = ref('');
const messageHistory = ref([]);
const replyContent = ref('');
const showReply = ref(false); // Toggle to show or hide reply section
const replyToMessageId = ref(null); // Track the ID of the message being replied to


const fetchUsers = async () => {
    try {
        const response = await axios.get('/users?role=1');
        users.value = response.data;
    } catch (error) {
        console.error('Error fetching users:', error);
    }
};

const fetchMessageHistory = async (userId) => {
    try {
        const response = await axios.get(`/messages/${userId}`);
        messageHistory.value = response.data;
    } catch (error) {
        console.error('Error fetching message history:', error);
        messageHistory.value = []; // Clear message history on error
    }
};

onMounted(async () => {
    await fetchUsers();
});

const showUserDetails = async (user) => {
    selectedUser.value = { ...user };
    await fetchMessageHistory(user.id);
};

const closeUserDetails = () => {
    selectedUser.value = null;
    messageHistory.value = []; // Clear message history when closing details
    replyContent.value = ''; // Clear reply content
    showReply.value = false; // Hide reply section when closing details
};

const sendMessage = async () => {
    try {
        const response = await axios.post('/send-message', {
            email: selectedUser.value.email,
            message: messageContent.value,
        });
        if (response.data.success) {
            feedbackMessage.value = 'Message sent successfully!';
            messageContent.value = '';
            // Fetch updated message history after sending message
            await fetchMessageHistory(selectedUser.value.id);
        } else {
            feedbackMessage.value = 'Failed to send message. Please try again.';
        }
    } catch (error) {
        console.error('Error sending message:', error.response); // Log error response
        feedbackMessage.value = 'An error occurred. Please try again.';
    }
};



const replyToMessage = async (message) => {
    console.log('Replying to message:', message);
    replyToMessageId.value = message.id; // Set the ID of the message being replied to
    showReply.value = true; // Show the reply section
    replyContent.value = ''; // Clear the reply content textarea
};


const sendReply = async () => {
    try {
        const response = await axios.post('/send-reply', {
            email: selectedUser.value.email,
            message: replyContent.value,
            parent_id: replyToMessageId.value, // Ensure this is included
        });
        if (response.data.success) {
            feedbackMessage.value = 'Reply sent successfully!';
            replyContent.value = ''; // Clear reply content after successful send
            replyToMessageId.value = null; // Reset replyToMessageId
            // Fetch updated message history after sending reply
            await fetchMessageHistory(selectedUser.value.id);
        } else {
            feedbackMessage.value = 'Failed to send reply. Please try again.';
        }
    } catch (error) {
        console.error('Error sending reply:', error.response);
        feedbackMessage.value = 'An error occurred. Please try again.';
    }
};




const cancelReply = () => {
    replyContent.value = ''; // Clear reply content
    showReply.value = false; // Hide reply section
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
                <div class="mt-4 flex justify-end">
                    <button @click="showUserDetails(user)" class="bg-blue-500 text-white px-3 py-1 rounded">
                        View Data
                    </button>
                </div>
            </div>
        </div>

        <!-- Modal for showing user details -->
        <div v-if="selectedUser" class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50">
            <div class="bg-white rounded-lg p-8 shadow-lg max-w-lg w-full max-h-full overflow-y-auto">
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
                    :chart-data="hardcodedChartData"
                ></gradient-line-chart>

                <!-- Send message section -->
                <div class="mt-8">
                    <label for="messageContent" class="block text-sm font-medium text-gray-700">Send Message:</label>
                    <textarea id="messageContent" v-model="messageContent" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"></textarea>
                    <div class="mt-2 flex justify-end">
                        <button @click="sendMessage" class="bg-green-500 text-white px-3 py-1 rounded">Send</button>
                    </div>
                    <div v-if="feedbackMessage" class="mt-2 text-green-500">{{ feedbackMessage }}</div>
                </div>

            <!-- Message history section -->
<div class="mt-4">
    <h4 class="text-lg font-semibold">Message History</h4>
    <div v-if="selectedUser && messageHistory.length > 0 && selectedUser.id === selectedUser.id" class="mt-2 space-y-4">
        <div v-for="message in messageHistory" :key="message.id" class="border p-4 rounded">
            <div class="flex">
                <!-- Left side: Message -->
                <div class="flex-1">
                    <p><strong>From:</strong> {{ message.from_id }}</p>
                    <p><strong>To:</strong> {{ message.to_id }}</p>
                    <p><strong>Date:</strong> {{ new Date(message.created_at).toLocaleString() }}</p>
                    <p><strong>Message:</strong> {{ message.content }}</p>
                </div>
                <!-- Right side: Replies -->
                <div v-if="message.replies && message.replies.length > 0" class="ml-4 flex-1">
                    <div v-for="reply in message.replies" :key="reply.id" class="border p-4 rounded">
                        <p><strong>From:</strong> {{ reply.from_id }}</p>
                        <p><strong>To:</strong> {{ reply.to_id }}</p>
                        <p><strong>Date:</strong> {{ new Date(reply.created_at).toLocaleString() }}</p>
                        <p><strong>Reply:</strong> {{ reply.content }}</p>
                    </div>
                </div>
            </div>
            <div class="mt-2">
                <button @click="replyToMessage(message)" class="bg-blue-500 text-white px-3 py-1 rounded">Reply</button>
            </div>
        </div>
    </div>
    <div v-else class="mt-2 text-gray-600">No messages found.</div>
</div>



                <!-- Reply message section -->
                <div v-if="replyToMessageId" class="mt-4">
                    <h4 class="text-lg font-semibold">Reply to Message</h4>
                    <label for="replyContent" class="block text-sm font-medium text-gray-700">Reply Content:</label>
                    <textarea id="replyContent" v-model="replyContent" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"></textarea>
                    <div class="mt-2 flex justify-end">
                        <button @click="sendReply" class="bg-green-500 text-white px-3 py-1 rounded">Send Reply</button>
                    </div>
                    <div v-if="feedbackMessage" class="mt-2 text-green-500">{{ feedbackMessage }}</div>
                </div>

                <div class="mt-4 flex justify-end">
                    <button @click="selectedUser = null" class="bg-red-500 text-white px-3 py-1 rounded">Close</button>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
