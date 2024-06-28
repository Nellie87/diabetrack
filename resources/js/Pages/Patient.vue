<script setup>
import { ref, watch, onMounted } from 'vue';
import axios from 'axios';
import AppLayout from '@/Layouts/AppLayout.vue';
import { usePage } from '@inertiajs/vue3';
import GradientLineChart from "/resources/js/Components/Charts/GradientLineChart.vue";
import TimelineList from "/resources/js/Components/TimelineList.vue";
import TimelineItem from "/resources/js/Components/TimelineItem.vue";
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
import { formatDate } from '@vueuse/core';

const page = usePage();
const user = computed(() => page.props.auth.user);
import { computed } from 'vue';
import { Link, router, useForm } from '@inertiajs/vue3';
import ActionMessage from '/resources/js/Components/ActionMessage.vue';
import FormSection from '/resources/js/Components/FormSection.vue';
import InputError from '/resources/js/Components/InputError.vue';
import InputLabel from '/resources/js/Components/InputLabel.vue';
import PrimaryButton from '/resources/js/Components/PrimaryButton.vue';
import SecondaryButton from '/resources/js/Components/SecondaryButton.vue';
import TextInput from '/resources/js/Components/TextInput.vue';

const props = defineProps({
    user: Object,
});

const isLocked = ref(false);
const password = ref('');

let timeoutId = null;
const lockTimeout = 300000; // 5 minutes in milliseconds

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


const patientForm = ref({
    PhoneNo: '',
    Gender: '',
    Address: '',
    EmergencyContactName: '',
    EmergencyContactPhone: '',
    DoctorID:'',
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
                Emergency_Contact_Name: '',
                Emergency_Contact_Phone: '',
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





    const fetchData = async () => {
      try {
        const response = await axios.get('/api/chart-data');
        chartData.value = response.data;
        $hue=chartData.value;
        renderChart(chart);
      } catch (error) {
        console.error('Error fetching data:', error);
      }
    };


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

        <div class="col-lg-7">
        <!-- line chart -->

        <div class="col-lg-4 col-md-6">
        <timeline-list
          class="h-100"
          title="Orders overview"
          description="<i class='fa fa-arrow-up text-success' aria-hidden='true'></i>
        <span class='font-weight-bold'>24%</span> this month"
        >
          <timeline-item
            color="success"
            icon="bell-55"
            title="$2400 Design changes"
            date-time="22 DEC 7:20 PM"
          />
          <TimelineItem
            color="danger"
            icon="html5"
            title="New order #1832412"
            date-time="21 DEC 11 PM"
          />
          <TimelineItem
            color="info"
            icon="cart"
            title="Server payments for April"
            date-time="21 DEC 9:34 PM"
          />
          <TimelineItem
            color="warning"
            icon="credit-card"
            title="New card added for order #4395133"
            date-time="20 DEC 2:20 AM"
          />
          <TimelineItem
            color="primary"
            icon="key-25"
            title="Unlock packages for development"
            date-time="18 DEC 4:54 AM"
          />
          <TimelineItem
            color="info"
            icon="check-bold"
            title="Notifications unread"
            date-time="15 DEC"
          />
        </timeline-list>
      </div>
         
        <div class="card z-index-2">
          <gradient-line-chart
            id="chart-line"
            title="Gradient Line Chart"
            description="<i class='fa fa-arrow-up text-success'></i>
      <span class='font-weight-bold'>4% more</span> in 2021"
            :chart="{
              labels: [
                'Apr',
                'May',
                'Jun',
                'Jul',
                'Aug',
                'Sep',
                'Oct',
                'Nov',
                'Dec',
              ],
              datasets: [
                {
                  label: 'Mobile Apps',
                  data:  $hue,
                },
                {
                  label: 'Websites',
                  data: [30, 90, 40, 140, 290, 290, 340, 230, 400],
                },
              ],
            }"
          />
        </div>
      </div>

      

        <div class="bg-gray-200 bg-opacity-25 grid grid-cols-1 md:grid-cols-2 gap-6 lg:gap-8 p-6 lg:p-8">                
            <div>
                <form @submit.prevent="submitForm">
                        <div class="mb-4">
                            <label for="name" class="block text-gray-700">Name:</label>
                            <input id="name" type="text" class="mt-1 block w-full">
                        </div>
        
                        <div class="mb-4">
                            <label for="PhoneNo" class="block text-gray-700">Phone Number:</label>
                            <input id="PhoneNo" type="text" class="mt-1 block w-full" v-model="patientForm.PhoneNo">
                        </div>
                        <div class="mb-4">
                            <label for="Gender" class="block text-gray-700">Gender:</label>
                            <input id="Gender" type="text" class="mt-1 block w-full" v-model="patientForm.Gender">
                        </div>
                        <div class="mb-4">
                            <label for="Address" class="block text-gray-700">Adress:</label>
                            <input id="Address" type="text" class="mt-1 block w-full" v-model="patientForm.Address">
                        </div>
                        <div class="mb-4">
                            <label for="EmergencyContactName" class="block text-gray-700">Name of Emergency Contact:</label>
                            <input id="EmergencyContactName" type="text" class="mt-1 block w-full" v-model="patientForm.EmergencyContactName">
                        </div>

                        <div class="mb-4">
                            <label for="EmergencyContactPhone" class="block text-gray-700">Phone of Emergency Contact:</label>
                            <input id="EmergencyContactPhone" type="text" class="mt-1 block w-full" v-model="patientForm.EmergencyContactPhone">
                            </div>

                        <div class="mb-4">
                            <label for="DoctorID" class="block text-gray-700">Doctor:</label>
                            <input id="DoctorID" type="text" class="mt-1 block w-full" v-model="patientForm.DoctorID">
                        </div>
                    
                        <button @click="submitpatientForm" type="submit" class="px-4 py-2 bg-indigo-600 text-white">Submit</button>
                        <div>
                        <button @click="fetchData" type="submit" class="px-4 py-2 bg-indigo-600 text-white">Chart</button>
                        </div>
                    </form>               
                <div class="flex items-center">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" class="w-6 h-6 stroke-gray-400">
                        <path stroke-linecap="round" d="M15.75 10.5l4.72-4.72a.75.75 0 011.28.53v11.38a.75.75 0 01-1.28.53l-4.72-4.72M4.5 18.75h9a2.25 2.25 0 002.25-2.25v-9a2.25 2.25 0 00-2.25-2.25h-9A2.25 2.25 0 004.5 7.5v9a2.25 2.25 0 002.25 2.25z" />
                    </svg>
                    <h2 class="ms-3 text-xl font-semibold text-gray-900">
                        <a href="https://laracasts.com">View previous readings</a>
                    </h2>
                </div>

                <p class="mt-4 text-gray-500 text-sm leading-relaxed">
                    Laracasts offers thousands of video tutorials on Laravel, PHP, and JavaScript development. Check them out, see for yourself, and massively level up your development skills in the process.
                </p>

                <p class="mt-4 text-sm">
                    <a href="https://laracasts.com" class="inline-flex items-center font-semibold text-indigo-700">
                        Start watching Laracasts

                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" class="ms-1 w-5 h-5 fill-indigo-500">
                            <path fill-rule="evenodd" d="M5 10a.75.75 0 01.75-.75h6.638L10.23 7.29a.75.75 0 111.04-1.08l3.5 3.25a.75.75 0 010 1.08l-3.5 3.25a.75.75 0 11-1.04-1.08l2.158-1.96H5.75A.75.75 0 015 10z" clip-rule="evenodd" />
                        </svg>
                    </a>
                </p>
            </div>

            <div>
                <div class="flex items-center">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" class="w-6 h-6 stroke-gray-400">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 15.75l5.159-5.159a2.25 2.25 0 013.182 0l5.159 5.159m-1.5-1.5l1.409-1.409a2.25 2.25 0 013.182 0l2.909 2.909m-18 3.75h16.5a1.5 1.5 0 001.5-1.5V6a1.5 1.5 0 00-1.5-1.5H3.75A1.5 1.5 0 002.25 6v12a1.5 1.5 0 001.5 1.5zm10.5-11.25h.008v.008h-.008V8.25zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0z" />
                    </svg>
                    <h2 class="ms-3 text-xl font-semibold text-gray-900">
                        <a href="https://tailwindcss.com/">Graphs</a>
                    </h2>
                </div>

                <p class="mt-4 text-gray-500 text-sm leading-relaxed">
                    Laravel Jetstream is built with Tailwind, an amazing utility first CSS framework that doesn't get in your way. You'll be amazed how easily you can build and maintain fresh, modern designs with this wonderful framework at your fingertips.
                </p>
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