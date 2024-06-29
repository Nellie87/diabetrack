<script setup>
import { ref, watch, onMounted } from 'vue';
import axios from 'axios';
import AppLayout from '@/Layouts/AppLayout.vue';
import { usePage } from '@inertiajs/vue3';
import GradientLineChart from "/resources/js/Components/Charts/GradientLineChart.vue";
import TimelineList from "/resources/js/Components/TimelineList.vue";
import TimelineItem from "/resources/js/Components/TimelineItem.vue";
import { computed } from 'vue';
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
import { formatDate } from '@vueuse/core';

const page = usePage();
const user = computed(() => page.props.auth.user);


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
            patientForm.value = {
                Datetime: '',
                GlucoseLevel:'',
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





</script>



<template>
    <AppLayout title="Dashboard">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Patient 
            </h2>
        </template>

        <!-- Main content -->
         <!--
        <div :class="{ 'blur': isLocked }" class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                    <Welcome />
                </div>
            </div>
        </div>

        <div class="col-lg-7">
        
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
    -->
        <!-- 
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
                  data: [0, 0, 0, 0, 0, 0,],
                },
              ],
            }"
          />
        </div>
    
      </div>
    -->
      
       
        <div class="bg-gray-200 bg-opacity-25 grid grid-cols-1 md:grid-cols-2 gap-6 lg:gap-8 p-6 lg:p-8">                
            <div>
                <form @submit.prevent="submitForm">


                    <div class="mb-4">
                            <label for="Date" class="block text-gray-700">Date:</label>
                            <input id="Datetime" type="datetime-local" class="mt-1 block w-full" v-model="glucoseReadingForm.Datetime" />
                        </div>


                    <div class="mb-4">
                            <label for="GlucoseReading" class="block text-gray-700">GlucoseReading:</label>
                            <input id="GlucoseLevel" type="number" min="0" max="1000" class="mt-1 block w-full" v-model="glucoseReadingForm.GlucoseLevel" />
                        </div>
                        

                    <div class="mb-4">
                            <label for="Notes" class="block text-gray-700">Notes:</label>
                            <input id="Notes" type="text" class="mt-1 block w-full" v-model="glucoseReadingForm.Notes" />
                        </div>

                        <button @click="submitglucoseReadingForm" type="submit" class="px-4 py-2 bg-indigo-600 text-white">Submit</button>

                    </form>

          <gradient-line-chart
            id="chart-line"
            title="Sugar Levels Overview"
            apiUrl="http://127.0.0.1:8000/chart-data"
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
                  data: [0, 0, 0, 0, 0, 0,],
                },
              ],
            }"
          />
                <!--
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
                    </form>            
                -->   
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