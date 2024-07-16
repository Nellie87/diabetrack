<script setup>
import { ref, watch, onMounted, computed } from 'vue';
import axios from 'axios';
import AppLayout from '@/Layouts/AppLayout.vue';
import { usePage } from '@inertiajs/vue3';
import GradientLineChart from '/resources/js/Components/Charts/GradientLineChart.vue';
import HeaderSection from '/resources/js/Components/HeaderSection.vue';
import HeroSection2 from '/resources/js/Components/HeroSection2.vue';
import ServicesSection from '/resources/js/Components/ServicesSection.vue';
import CircleChart from '/resources/js/Components/Charts/CircleChart.vue';
import ProgressDoughnutChart from '/resources/js/Components/Charts/CircleChart.vue';
import ProgressBar from '/resources/js/Components/ProgressBar.vue';
import NotificationsPanel from '/resources/js/Components/NotificationPanel.vue';
// import Modal from '@/Components/Modal.vue'; // Import the Modal component

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
</script>

<template>
    <AppLayout>
        <HeroSection2/>
        <div class="py-4 container-fluid">
            <div class="mt-4 row">
                <div class="mb-4 col-lg-5 mb-lg-0">
                    <div class="p-3 card-body">
                        <h3 class="text-lg font-semibold mb-4">Enter Medicine Information:</h3>
                        <form @submit.prevent="submitForm">
                            <div class="mb-4">
                                <label for="MedicationName" class="block text-gray-700">Medication Name:</label>
                                <input id="MedicationName" type="text" class="form-control mt-1 block w-full" v-model="medicationsForm.MedicationName" />
                            </div>
      
                            <div class="mb-4">
                                <label for="Type" class="block text-gray-700">Type:</label>
                                <input id="Type" type="text" class="form-control mt-1 block w-full" v-model="medicationsForm.Type" />
                            </div>
      
                            <div class="mb-4">
                                <label for="Dosage" class="block text-gray-700">Dosage:</label>
                                <input id="Dosage" type="text" class="form-control mt-1 block w-full" v-model="medicationsForm.Dosage" />
                            </div>
      
                            <div class="mb-4">
                                <label for="Frequency" class="block text-gray-700">Frequency:</label>
                                <input id="Frequency" type="text" class="form-control mt-1 block w-full" v-model="medicationsForm.Frequency" />
                            </div>
      
                            <div class="mb-4">
                                <label for="StartDate" class="block text-gray-700">Start Date:</label>
                                <input id="StartDate" type="date" class="form-control mt-1 block w-full" v-model="medicationsForm.StartDate" />
                            </div>
      
                            <button @click="submitmedicationForm" type="submit" class="px-4 py-2 bg-indigo-600 text-white">Submit</button>
                        </form>

                    </div>
                </div>
                <div class="col-lg-7">
                    <div class="card z-index-2">
                        <CircleChart
                            id="chart-line"
                            title="Medicines Overview"
                            apiUrl="http://127.0.0.1:8000/chart-datas1"

                            description="<i class='fa fa-arrow-up text-success'></i>
                            <span class='font-weight-bold'></span> "
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
</style>