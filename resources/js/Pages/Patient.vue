<script setup>
import { ref, watch, onMounted, computed } from 'vue';
import axios from 'axios';
import AppLayout from '@/Layouts/AppLayout.vue';
import { usePage } from '@inertiajs/vue3';
import GradientLineChart from '/resources/js/Components/Charts/GradientLineChart.vue';
import Modal from '@/Components/Modes.vue'; // Import the Modal component

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
            checkGlucoseLevel(glucoseReadingForm.value.GlucoseLevel); // Check the glucose level after successful submission
            glucoseReadingForm.value = {
                Datetime: '',
                GlucoseLevel: '',
                Notes: '',
            };
        } else {
            alert('Submission failed. Please try again.');
        }
    } catch (error) {
        console.error('Error submitting form:', error);
        alert('An error occurred while submitting the form. Please try again.');
    }
}

function checkGlucoseLevel(level) {
    if (level < 70) {
        modalTitle.value = 'Low Glucose Level';
        modalMessage.value = 'Your glucose level is low. Please consume fast-acting carbohydrates like juice or glucose tablets and recheck your levels.';
    } else if (level >= 70 && level < 140) {
        modalTitle.value = 'Normal Glucose Level';
        modalMessage.value = 'Your glucose level is normal.';
    } else {
        modalTitle.value = 'High Glucose Level';
        modalMessage.value = 'Your glucose level is high. Please consider adjusting your medication or diet, and consult your healthcare provider if needed.';
    }
    showModal.value = true;
}

const patientForm = ref({
    PhoneNo: '',
    Gender: '',
    Address: '',
    EmergencyContactName: '',
    EmergencyContactPhone: '',
    DoctorID: '',
});

async function submitpatientForm() {
    try {
        const formData = new FormData();
        Object.keys(patientForm.value).forEach(key => {
            formData.append(key, patientForm.value[key]);
        });

        const response = await axios.post('/api/submit-form', formData, {
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
}
</script>

<template>
    <AppLayout title="Dashboard">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Patient 
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

        <div class="bg-gray-200 bg-opacity-25 grid grid-cols-1 md:grid-cols-2 gap-6 lg:gap-8 p-6 lg:p-8">                
            <div>
                <form @submit.prevent="submitglucoseReadingForm">
                    <div class="mb-4">
                        <label for="Date" class="block text-gray-700">Date:</label>
                        <input id="Datetime" type="datetime-local" class="mt-1 block w-full" v-model="glucoseReadingForm.Datetime" />
                    </div>

                    <div class="mb-4">
                        <label for="GlucoseReading" class="block text-gray-700">Glucose Reading:</label>
                        <input id="GlucoseLevel" type="number" min="0" max="1000" class="mt-1 block w-full" v-model="glucoseReadingForm.GlucoseLevel" />
                    </div>
                        
                    <div class="mb-4">
                        <label for="Notes" class="block text-gray-700">Notes:</label>
                        <input id="Notes" type="text" class="mt-1 block w-full" v-model="glucoseReadingForm.Notes" />
                    </div>

                    <button type="submit" class="px-4 py-2 bg-indigo-600 text-white">Submit</button>
                </form>

                <gradient-line-chart
                    id="chart-line"
                    title="Sugar Levels Overview"
                    apiUrl="http://127.0.0.1:8000/chart-data"
                    description="<i class='fa fa-arrow-up text-success'></i>
                    <span class='font-weight-bold'>4% more</span> in 2021"
                    :chart="{
                        labels: ['Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
                        datasets: [{ label: 'Mobile Apps', data: [0, 0, 0, 0, 0, 0] }]
                    }"
                />
            </div>
        </div>
        
        <modal
            :show="showModal"
            :title="modalTitle"
            :message="modalMessage"
            @close="showModal = false"
        />
    </AppLayout>
</template>

<style>
.blur {
    filter: blur(5px);
    transition: filter 0.3s ease-in-out;
}
</style>
