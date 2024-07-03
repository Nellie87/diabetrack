<script setup>
import { ref, watch, onMounted, computed } from 'vue';
import axios from 'axios';
import AppLayout from '@/Layouts/AppLayout.vue';
import { usePage } from '@inertiajs/vue3';
import GradientLineChart from '/resources/js/Components/Charts/GradientLineChart.vue';
import BarChart from '/resources/js/Components/Charts/BarChart.vue';
import ProgressDoughnutChart from '/resources/js/Components/Charts/CircleChart.vue';
import ProgressBar from '/resources/js/Components/ProgressBar.vue';
import NotificationsPanel from '/resources/js/Components/NotificationPanel.vue';
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
            alert('Form submitted successfully.');
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
};

</script>

<template>
    <AppLayout title="Dashboard">
        <NotificationsPanel/>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Patient 
            </h2>
        </template>
        <!-- Main content -->
        
        <div class="bg-gray-200 bg-opacity-25 grid grid-cols-1 md:grid-cols-2 gap-6 lg:gap-8 p-6 lg:p-8">              
            <div>
                
                <form @submit.prevent="submitForm">
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

                        <button @click="submitglucoseReadingForm" type="submit" class="px-4 py-2 bg-indigo-600 text-white">Submit</button>

                    </form>

          <gradient-line-chart
            id="chart-line"
            title="Sugar Levels Overview"
            apiUrl="http://127.0.0.1:8000/chart-data"
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
        
        

           <form @submit.prevent="submitForm">


                    <div class="mb-4">
                            <label for="Date" class="block text-gray-700">Date:</label>
                            <input id="Date" type="Date" class="mt-1 block w-full" v-model="dietForm.Date" />
                        </div>


                    <div class="mb-4">
                            <label for="MealType" class="block text-gray-700">Meal Type:</label>
                            <input id="MealType" type="text" class="mt-1 block w-full" v-model="dietForm.MealType" />
                        </div>
                        

                    <div class="mb-4">
                            <label for="FoodItems" class="block text-gray-700">Food Items:</label>
                            <input id="FoodItems" type="text" class="mt-1 block w-full" v-model="dietForm.FoodItems" />
                        </div>

                    <div class="mb-4">
                            <label for="Carbohydrates" class="block text-gray-700">Carbohydrates:</label>
                            <input id="Carbohydrates" type="text" class="mt-1 block w-full" v-model="dietForm.Carbohydrates" />
                        </div>

                        <div class="mb-4">
                            <label for="Notes" class="block text-gray-700">Notes:</label>
                            <input id="Notes" type="text" class="mt-1 block w-full" v-model="dietForm.Notes" />
                        </div>

                        <button @click="submitdietForm" type="submit" class="px-4 py-2 bg-indigo-600 text-white">Submit</button>

                    </form>

                    <BarChart
            id="chart-bar"
            title="Carbs Overview"
            apiUrl="http://127.0.0.1:8000/chart-datas"
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


                    <form @submit.prevent="submitForm">


                    <div class="mb-4">
                            <label for="MedicationName" class="block text-gray-700">Medicine Name:</label>
                            <input id="MedicationName" type="text" class="mt-1 block w-full" v-model="medicationsForm.MedicationName" />
                        </div>


                    <div class="mb-4">
                            <label for="Type" class="block text-gray-700">Type:</label>
                            <input id="Type" type="text" class="mt-1 block w-full" v-model="medicationsForm.Type" />
                        </div>
                        

                    <div class="mb-4">
                            <label for="Dosage" class="block text-gray-700">Amount taken in one dose:</label>
                            <input id="Dosage" type="number" class="mt-1 block w-full" v-model="medicationsForm.Dosage" />
                        </div>

                    <div class="mb-4">
                            <label for="Frequency" class="block text-gray-700">Frequency:</label>
                            <input id="Frequency" type="number" class="mt-1 block w-full" v-model="medicationsForm.Frequency" />
                        </div>

                        <div class="mb-4">
                            <label for="StartDate" class="block text-gray-700">Start Date:</label>
                            <input id="StartDate" type="date" class="mt-1 block w-full" v-model="medicationsForm.StartDate" />
                        </div>

                        <button @click="submitmedicationForm" type="submit" class="px-4 py-2 bg-indigo-600 text-white">Submit</button>

                    </form>

                    <ProgressDoughnutChart
            id="chart-circle"
            title="Carbs Overview"
            apiUrl="http://127.0.0.1:8000/chart-datas1"
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

          <ProgressBar/>
                
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
