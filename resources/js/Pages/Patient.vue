<script setup>
import { ref, watch, onMounted, computed } from 'vue';
import axios from 'axios';
import AppLayout from '@/Layouts/AppLayout.vue';
import { usePage } from '@inertiajs/vue3';
import GradientLineChart from '/resources/js/Components/Charts/GradientLineChart.vue';
import HeaderSection from '/resources/js/Components/HeaderSection.vue';
import HeroSection2 from '/resources/js/Components/HeroSection2.vue';
import ServicesSection from '/resources/js/Components/ServicesSection.vue';
import BarChart from '/resources/js/Components/Charts/BarChart.vue';
import ProgressDoughnutChart from '/resources/js/Components/Charts/CircleChart.vue';
import ProgressBar from '/resources/js/Components/ProgressBar.vue';
import NotificationsPanel from '/resources/js/Components/NotificationPanel.vue';
// import Modal from '@/Components/Modal.vue'; // Import the Modal component
import SideNav from '@/Components/SideNav.vue';
import Modal from '@/Components/Modes.vue'; // Import the Modal component
import GlucoseReadingTable from '@/Components/Tables/GlucoseReadingTable.vue';


const page = usePage();
const user = computed(() => page.props.auth.user);

const props = defineProps({
    user: Object,
});

const isLocked = ref(false);
const password = ref('');

let timeoutId = null;
const lockTimeout = 300000; // 5 minutes in milliseconds

const showModal = ref(false);
const modalTitle = ref('');
const modalMessage = ref('');

// Lock screen function
function lockScreen() {
    isLocked.value = true;
    localStorage.setItem('isLocked', 'true');
}

// Unlock screen function
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

// Reset lock timeout function
function resetLockTimeout() {
    clearTimeout(timeoutId);
    timeoutId = setTimeout(lockScreen, lockTimeout);
}

// Handle user activity to reset timeout
function handleUserActivity() {
    resetLockTimeout();
}

// Manually lock screen function
function manualLock() {
    lockScreen();
}

// Watch for lock state changes
watch(isLocked, (newValue) => {
    if (newValue) {
        clearTimeout(timeoutId);
    } else {
        resetLockTimeout();
    }
});

// Add event listeners for user activity
window.addEventListener('mousemove', handleUserActivity);
window.addEventListener('keydown', handleUserActivity);

// Check local storage for lock state on mount
onMounted(() => {
    if (localStorage.getItem('isLocked') === 'true') {
        isLocked.value = true;
    } else {
        resetLockTimeout();
    }
});

const glucoseReadingForm = ref({
    Datetime: '',
    GlucoseLevel: '',
    Notes: '',
});

async function submitglucoseReadingForm() {
    try {
        const formData = new FormData();
        Object.keys(glucoseReadingForm.value).forEach(key => {
            formData.append(key, glucoseReadingForm.value[key]);
        });

        const response = await axios.post('/submit-form', formData, {
            headers: {
                'Content-Type': 'multipart/form-data'
            }
        });

        if (response.data.success) {
            alert('Form submitted successfully.');
                glucoseReadingForm.value = {
                Datetime: '',
                GlucoseLevel: '',
                Notes: '',
            };
            window.location.reload();
        } else {
            alert('Submission failed. Please try again.');
        }
    } catch (error) {
        console.error('Error submitting form:', error);
        alert('An error occurred while submitting the form. Please try again.');
    }
}



const dietForm = ref({
    Date: '',
    MealType: '',
    FoodItems: '',
    Carbohydrates: '',
    Notes: '',
});

async function submitdietForm() {
    try {
        const formData = new FormData();
        Object.keys(dietForm.value).forEach(key => {
            formData.append(key, dietForm.value[key]);
        });

        const response = await axios.post('/submit-form2', formData, {
            headers: {
                'Content-Type': 'multipart/form-data'
            }
        });

        if (response.data.success) {
            alert('Form submitted successfully.');
            dietForm.value = {
                Date: '',
                MealType: '',
                FoodItems: '',
                Carbohydrates: '',
                Notes: '',               
            };
            window.location.reload();
        } else {
            alert('Submission failed. Please try again.');
        }
    } catch (error) {
        console.error('Error submitting form:', error);
        alert('An error occurred while submitting the form. Please try again.');
    }

}

const medicationsForm = ref({
    MedicationName: '',
    Type: '',
    Dosage: '',
    Frequency: '',
    StartDate: '',
});





async function submitmedicationForm() {
    try {
        const formData = new FormData();
        Object.keys(medicationsForm.value).forEach(key => {
            formData.append(key, medicationsForm.value[key]);
        });

        const response = await axios.post('/submit-form3', formData, {
            headers: {
                'Content-Type': 'multipart/form-data'
            }
        });

        if (response.data.success) {
            alert('Form submitted successfully.');
            medicationsForm.value = {
                MedicationName: '',
                Type: '',
                Dosage: '',
                Frequency: '',
                StartDate: '',    
            }     
            
        } else {
            alert('Submission failed. Please try again.');
        }
    } catch (error) {
        console.error('Error submitting form:', error);
        alert('An error occurred while submitting the form. Please try again.');
    }
}

async function submitpatientForm() {
    try {
        const formData = new FormData();
        Object.keys(patientForm.value).forEach(key => {
            formData.append(key, patientForm.value[key]);
        });

        const response = await axios.post('/submit-form4', formData, {
            headers: {
                'Content-Type': 'multipart/form-data'
            }
        });

        if (response.data.success) {
            alert('Form submitted successfully.');
            patientForm.value = {
                PhoneNo: '',
                Gender: '',
                Address: '',
                EmergencyContactName: '',
                EmergencyContactPhone: '',
                DoctorID: '',
            };
        } else {
            alert('Submission failed. Please try again.');
        }
    } catch (error) {
        console.error('Error submitting form:', error);
        alert('An error occurred while submitting the form. Please try again.');
    }
    
};

// Active tab state
const activeTab = ref('glucoseReading');

// Function to change the active tab
function changeTab(tab) {
    activeTab.value = tab;
}

const glucoseReadings = ref([]);
const loading = ref(false);
const error = ref(null);

const fetchGlucoseReadings = () => {
  loading.value = true;
  axios.get('/glucose/readings')
    .then(response => {
      glucoseReadings.value = response.data;
      error.value = null; // Clear any previous errors
    })
    .catch(err => {
      console.error('Error fetching glucose readings:', err);
      error.value = 'Failed to fetch glucose readings. Please try again later.';
    })
    .finally(() => {
      loading.value = false;
    });
}

// import { onMounted } from 'vue';
onMounted(fetchGlucoseReadings);

{ glucoseReadings, loading, error }
const users = ref([]);
const feedbackMessage = ref('');
const messageContent = ref('');
const messageHistory = ref([]);
const replyContent = ref({});
const showReply = ref({});
const replyToMessageId = ref(null);

const fetchUsers = async () => {
  try {
    const response = await axios.get('/users');
    users.value = response.data;
  } catch (error) {
    console.error('Error fetching users:', error);
  }
};

const fetchMessageHistory = async () => {
  try {
    const response = await axios.get(`/message-history/3`); // Fetch history for user ID 2
    messageHistory.value = response.data.messages.map((message) => {
      message.replies = response.data.replies[message.id] || [];
      return message;
    });
  } catch (error) {
    console.error('Error fetching message history:', error.response);
    messageHistory.value = [];
  }
};

const sendMessage = async () => {
  try {
    const recipientId = 2; // User ID 2

    const response = await axios.post('/send-message', {
      email: 'smilesmemory@gmail.com', // Replace with the actual email if available
      message: messageContent.value,
      recipient_id: recipientId,
    });

    if (response.data.success) {
      feedbackMessage.value = 'Message sent successfully!';
      messageContent.value = '';
      // Fetch updated message history after sending message
      await fetchMessageHistory();
    } else {
      feedbackMessage.value = 'Failed to send message. Please try again.';
    }
  } catch (error) {
    console.error('Error sending message:', error);
    feedbackMessage.value = 'An error occurred. Please try again.';
  }
};

const sendReply = async (messageId) => {
  try {
    const response = await axios.post('/send-reply', {
      email: 'smilesmemory@gmail.com', // Replace with the actual email if available
      message: replyContent.value[messageId],
      parent_id: messageId,
    });
    if (response.data.success) {
      feedbackMessage.value = 'Reply sent successfully!';
      replyContent.value[messageId] = '';
      // Fetch updated message history after sending reply
      await fetchMessageHistory();
    } else {
      feedbackMessage.value = 'Failed to send reply. Please try again.';
    }
  } catch (error) {
    console.error('Error sending reply:', error.response);
    feedbackMessage.value = 'An error occurred. Please try again.';
  }
};

const toggleReplySection = (message) => {
  showReply.value[message.id] = !showReply.value[message.id];
  if (showReply.value[message.id]) {
    if (!replyContent.value[message.id]) {
      replyContent.value[message.id] = '';
    }
  }
};

const cancelReply = (messageId) => {
  replyContent.value[messageId] = '';
  showReply.value[messageId] = false;
};

onMounted(async () => {
  await fetchUsers();
  await fetchMessageHistory(); // Fetch initial message history for user ID 2
});
const navLinks= [
        { text: 'Home', url: '/' },
        {text: 'Profile', url:'/user/profile'},
        {text: 'Glucose Info', url: '/patient'},
        {text: 'Diet', url: '/diet'},
        {text: 'Medications', url: '/patient3'},
      ]
</script>

<template>
  
    <AppLayout>
      <div id="app">
      <div class="layout">
        <!-- Left SideNav -->
        <SideNav :links="navLinks" />

        <!-- Main Content -->
        <main class="main-content" :class="{ 'blur': isLocked }">
          <!-- HeroSection2 should be placed here if it's part of the main content -->
          <HeroSection2/>

          <!-- Glucose Readings Form and Chart Section -->
          <div class="py-4 container-fluid">
            <div class="mt-4 row">
              <div class="mb-4 col-lg-5 mb-lg-0">
                <div class="p-3 card-body">
                  <h3 class="text-lg font-semibold mb-4">Enter Glucose Readings:</h3>
                  <form class="form" @submit.prevent="submitglucoseReadingForm">
                    <div class="form-group">
                      <div class="mb-4">
                        <label for="Datetime" class="block text-gray-700">Date:</label>
                        <input id="Datetime" type="datetime-local" class="form-control mt-1 block w-full" v-model="glucoseReadingForm.Datetime" />
                      </div>
                      <div class="mb-4">
                        <label for="GlucoseLevel" class="block text-gray-700">Glucose Reading:</label>
                        <input id="GlucoseLevel" placeholder="(mg/dl)" type="number" min="0" max="1000" class="form-control mt-1 block w-full" v-model="glucoseReadingForm.GlucoseLevel" />
                      </div>
                    </div>
                    <div class="mb-4">
                      <label for="Notes" class="block text-gray-700">Notes:</label>
                      <input id="Notes" type="text" class="form-control mt-1 block w-full" v-model="glucoseReadingForm.Notes" />
                    </div>
                    <button type="submit" class="inline-flex items-center px-4 py-2 bg-blue-500 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest shadow-sm hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                      Submit
                    </button>
                  </form>
                </div>
              </div>
              <div class="col-lg-7">
                <div class="card z-index-2">
                  <GradientLineChart
                    id="chart-line"
                    title="Sugar Levels Overview"
                    apiUrl="http://127.0.0.1:8000/chart-data"
                    description="<i class='fa fa-arrow-up text-success'></i> <span class='font-weight-bold'></span>"
                    :chart="{
                      labels: ['January', 'February', 'March', 'April', 'May', 'June'],
                      datasets: [{
                        label: 'My First dataset',
                        data: [65, 59, 80, 81, 56, 55],
                        fill: false,
                        borderColor: 'rgba(75, 192, 192, 1)',
                        lineTension: 0.1
                      }]
                    }"
                  />
                </div>
              </div>
            </div>
          </div>

          <!-- Glucose Readings Table -->
          <div>
            <h2 class="text-lg font-bold mb-4">Glucose Readings</h2>
            <GlucoseReadingTable :glucose-readings="glucoseReadings" />
          </div>

          <!-- Send Message Section -->
          <h4 class="text-xl font-semibold mt-6 mb-4">Send a New Message</h4>
          <textarea v-model="messageContent" placeholder="Enter your message" class="w-full p-2 border rounded-md" rows="4"></textarea>
          <div class="mt-2 flex justify-end">
            <button @click="sendMessage" class="bg-blue-500 text-white px-3 py-1 rounded">
              Send Message
            </button>
          </div>
          <p v-if="feedbackMessage" class="mt-4 text-green-500">{{ feedbackMessage }}</p>

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
                  <button @click="toggleReplySection(message)" class="bg-blue-500 text-white px-3 py-1 rounded">
                    {{ showReply[message.id] ? 'Hide Reply' : 'Reply' }}
                  </button>
                </div>

                <!-- Reply Form -->
                <div v-if="showReply[message.id]" class="mt-4">
                  <textarea v-model="replyContent[message.id]" placeholder="Enter your reply" class="w-full p-2 border rounded-md" rows="4"></textarea>
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
        </main>
      </div>
    </div>
     
    </AppLayout>
  </template>

<style scoped>
.blur {
    filter: blur(4px);
}
.linechartdiv{
    background-color: #ffffff;
    color: #424242;
    border: 1px solid #ece7e7;
    padding: 10px;
    margin: 10px;

}
.card {
    box-shadow: 0 20px 27px 0 rgba(0, 0, 0, 0.05);
}
.z-index-2 {
    z-index: 2 !important;
}
.card {
    position: relative;
    display: flex;
    flex-direction: column;
    min-width: 0;
    word-wrap: break-word;
    background-color: #fff;
    background-clip: border-box;
    border: 0 solid rgba(0, 0, 0, 0.125);
    border-radius: 1rem;
}
.py-4 {
    padding-top: 1.5rem !important;
    padding-bottom: 1.5rem !important;
}
.row {
    --bs-gutter-x: 1.5rem;
    --bs-gutter-y: 0;
    display: flex;
    flex-wrap: wrap;
    margin-top: calc(var(--bs-gutter-y)* -1);
    margin-right: calc(var(--bs-gutter-x)* -0.5);
    margin-left: calc(var(--bs-gutter-x)* -0.5);
}
.container-fluid, .container-xxl, .container-xl, .container-lg, .container-md, .container-sm {
    width: 100%;
    padding-right: var(--bs-gutter-x, 1.5rem);
    padding-left: var(--bs-gutter-x, 1.5rem);
    margin-right: auto;
    margin-left: auto;
    background-color: #f0f8ff;
}
.mb-xl-0 {
        margin-bottom: 0 !important;
    }
.mb-4 {
    margin-bottom: 1.5rem !important;
}
.col-xl-3 {
        flex: 0 0 auto;
        width: 25%;
    }
    .col-sm-6 {
        flex: 0 0 auto;
        width: 50%;
    }

.mt-4 {
    margin-top: 1.5rem !important;
}

.mb-lg-0 {
        margin-bottom: 0 !important;
    }

.col-lg-5 {
        flex: 0 0 auto;
        width: 41.66666667%;
    }
    
    .p-3 {
    padding: 1rem !important;
}
.card-body {
    flex: 1 1 auto;
    padding: 1rem 1rem;
}
.col-lg-7 {
        flex: 0 0 auto;
        width: 58.33333333%;
    }

.form-control {
    border: 1px solid #ccc;
    display: block;
    width: 100%;
    height: 40px;
    padding: 0 20px;
    border-radius: 20px;
    font-family: muli-bold;
    background: 0 0;
}
.form-group {
    display: flex;
}
form {
        width: 100%;
        padding-right: 15px;
        padding-left: 15px;
    }
    .side-nav {
  position: fixed; /* Fix the side navigation */
  top: 0; /* Align to the top */
  left: 0; /* Align to the left */
  bottom: 0; /* Extend to the bottom */
  width: 220px; /* Set a fixed width for the navigation */
  background-color: #f3f4f6; /* Example background color */
  border-right: 1px solid #ddd; /* Example border */
  z-index: 1000; /* Ensure it's above other content */
}
</style>