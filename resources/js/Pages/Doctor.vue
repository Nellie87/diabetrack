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
const replyContent = ref({});
const showReply = ref({}); // Toggle to show or hide reply section
const replyToMessageId = ref(null); // Track the ID of the message being replied to
const selectedMessageForReply = ref(null); // Track selected message for reply

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
        const messages = response.data.filter(message => message.parent_id === null);
        messages.forEach(message => {
            message.replies = response.data.filter(reply => reply.parent_id === message.id);
        });
        messageHistory.value = messages;
    } catch (error) {
        console.error('Error fetching message history:', error.response);
        messageHistory.value = [];
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

const replyToMessage = (message) => {
    replyToMessageId.value = message.id; // Set the ID of the message being replied to
    showReply.value = true; // Show the reply section
    replyContent.value = ''; // Clear the reply content textarea
};
const toggleReplySection = (message) => {
    // Toggle the showReply state for the clicked message ID
    showReply.value[message.id] = !showReply.value[message.id];
    if (showReply.value[message.id]) {
        // Initialize reply content if it's not yet initialized
        if (!replyContent.value[message.id]) {
            replyContent.value[message.id] = '';
        }
    }
};

const sendReply = async (messageId) => {
    try {
        const response = await axios.post('/send-reply', {
            email: selectedUser.value.email,
            message: replyContent.value[messageId],
            parent_id: messageId, // Pass messageId as parent_id
        });
        if (response.data.success) {
            feedbackMessage.value = 'Reply sent successfully!';
            replyContent.value[messageId] = ''; // Clear reply content after successful send
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
const cancelReply = (messageId) => {
    replyContent.value[messageId] = ''; // Clear reply content for the specific message ID
    showReply.value[messageId] = false; // Hide reply section for the specific message ID
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

        <!-- User details modal -->
        <div v-if="selectedUser" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
            <div class="bg-white p-6 rounded-md shadow-md w-full max-w-2xl mx-4 overflow-y-auto max-h-screen">
                <h3 class="text-2xl font-semibold mb-4">Details for {{ selectedUser.name }}</h3>
                <p><strong>Email:</strong> {{ selectedUser.email }}</p>
                <p><strong>ID:</strong> {{ selectedUser.id }}</p>
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

                  <!-- Send Message Section -->
                  <h4 class="text-xl font-semibold mt-6 mb-4">Send a New Message</h4>
                <textarea v-model="messageContent" placeholder="Enter your message" class="w-full p-2 border rounded-md" rows="4"></textarea>
                <div class="mt-2 flex justify-end">
                    <button @click="sendMessage" class="bg-blue-500 text-white px-3 py-1 rounded">
                        Send Message
                    </button>
                </div>

                <p v-if="feedbackMessage" class="mt-4 text-green-500">{{ feedbackMessage }}</p>
                <button @click="closeUserDetails" class="mt-6 bg-red-500 text-white px-3 py-1 rounded">
                    Close
                </button>
                <!-- Message History Section -->
<h4 class="text-xl font-semibold mt-6 mb-4">Message History</h4>
<div class="overflow-y-auto max-h-64">
    <ul>
        <li v-for="message in messageHistory" :key="message.id" class="mb-4 p-4 border rounded-md">
            <p class="mb-2">{{ message.content }}</p>
            <p class="text-sm text-gray-600 mb-2">{{ new Date(message.created_at).toLocaleString() }}</p>

            <!-- Replies Section -->
            <div v-if="message.replies && message.replies.length > 0" class="ml-4 pl-4 border-l">
                <h5 class="font-semibold mb-2">Replies:</h5>
                <ul>
                    <li v-for="reply in message.replies" :key="reply.id" class="mb-2">
                        <p class="mb-1">{{ reply.content }}</p>
                        <p class="text-sm text-gray-600">{{ new Date(reply.created_at).toLocaleString() }}</p>
                    </li>
                </ul>
            </div>

            <!-- Reply Button -->
            <div class="mt-4 flex justify-end">
                <button
                    @click="toggleReplySection(message)"
                    class="bg-blue-500 text-white px-3 py-1 rounded"
                >
                    {{ showReply[message.id] ? 'Hide Reply' : 'Reply' }}
                </button>
            </div>

            <!-- Reply Form -->
            <div v-if="showReply[message.id]" class="mt-4">
                <textarea
                    v-model="replyContent[message.id]"
                    placeholder="Enter your reply"
                    class="w-full p-2 border rounded-md"
                    rows="4"
                ></textarea>
                <div class="mt-2 flex justify-end space-x-2">
                    <button @click="sendReply(message.id)" class="bg-blue-500 text-white px-3 py-1 rounded">
                        Send Reply
                    </button>
                    <button @click="cancelReply(message.id)" class="bg-gray-500 text-white px-3 py-1 rounded">
                        Cancel
                    </button>
                </div>
            </div>
        </li>
    </ul>
</div>
              
            </div>
        </div>

        <!-- Chart Section -->
        <div class="mt-8">
            <GradientLineChart :chart-data="hardcodedChartData" />
        </div>

        <!-- Lock Screen Modal -->
        <!-- <div v-if="isLocked" class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-75">
            <div class="bg-white p-6 rounded-md shadow-md w-full max-w-sm mx-4">
                <h3 class="text-xl font-semibold mb-4">Screen Locked</h3>
                <p class="mb-4">Enter your password to unlock:</p>
                <input
                    type="password"
                    v-model="password"
                    @keydown.enter="unlock"
                    placeholder="Enter password"
                    class="w-full p-2 border rounded-md mb-4"
                />
                <div class="flex justify-end">
                    <button @click="unlock" class="bg-blue-500 text-white px-4 py-2 rounded">
                        Unlock
                    </button>
                </div>
            </div>
        </div> -->
    </AppLayout>
</template>
