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
    <AppLayout title="Dashboard">
        <NotificationsPanel/>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Patient Forms
            </h2>
        </template>
        <!-- Main content -->
        <div id="app">
    <button @click="toggleSideNav" type="submit" class="px-4 py-2 bg-indigo-600 text-white">Toggle Notifications</button>
    
    <!-- Include the NotificationsSideNav component -->
    <NotificationsPanel v-if="toggleSideNav" />
  </div>
        <div class="bg-gray-200 bg-opacity-25 grid grid-cols-1 md:grid-cols-2 gap-6 lg:gap-8 p-6 lg:p-8"/>              
            <div>
                
                <form @submit.prevent="submitForm">

        <!-- Navigation bar -->
        <div class="py-4 bg-gray-100">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 flex space-x-4">
                <button @click="changeTab('glucoseReading')" :class="{ 'bg-blue-500 text-white': activeTab === 'glucoseReading', 'bg-white text-gray-800': activeTab !== 'glucoseReading' }" class="px-4 py-2 rounded">
                    Glucose Reading
                </button>
                <button @click="changeTab('diet')" :class="{ 'bg-blue-500 text-white': activeTab === 'diet', 'bg-white text-gray-800': activeTab !== 'diet' }" class="px-4 py-2 rounded">
                    Diet
                </button>
                <button @click="changeTab('medication')" :class="{ 'bg-blue-500 text-white': activeTab === 'medication', 'bg-white text-gray-800': activeTab !== 'medication' }" class="px-4 py-2 rounded">
                    Medication
                </button>
                <button @click="changeTab('patient')" :class="{ 'bg-blue-500 text-white': activeTab === 'patient', 'bg-white text-gray-800': activeTab !== 'patient' }" class="px-4 py-2 rounded">
                    Patient Info
                </button>
            </div>
        </div>
        </form>
    </div>
        
        

        <!-- Main content -->
        <div :class="{ 'blur': isLocked }" class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                    <div class="p-6 bg-white border-b border-gray-200">

                        <!-- Glucose Reading Form -->
                        <div v-show="activeTab === 'glucoseReading'">
                            <h3 class="text-lg font-semibold mb-4">Glucose Reading Form</h3>
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
                        <button type="submit" class="inline-flex items-center px-4 py-2 bg-blue-500 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest shadow-sm hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                            Submit
                        </button>
                        </form>
                        </div>
                    </div>
          </div>              <!-- <button @click="submitglucoseReadingForm" type="submit" class="px-4 py-2 bg-indigo-600 text-white">Submit</button> -->
                 
                    <div>
                    <gradient-line-chart
            id="chart-line"
            title="Sugar Levels Overview"
            apiUrl="http://127.0.0.1:8000/chart-data"

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
                            </form>
          <Modal v-if="showModal" :title="modalTitle" :message="modalMessage" @close="showModal = false" />
          <!-- Modal usage example -->
           </div>
           <div>
            
    <h2 class="text-lg font-bold mb-4">Glucose Readings</h2>
    <glucose-reading-table :glucose-readings="glucoseReadings" />
  </div>
                        

                        <!-- Diet Form -->
                        <div v-show="activeTab === 'diet'">
                            <h3 class="text-lg font-semibold mb-4">Diet Form</h3>
                            <form @submit.prevent="submitdietForm">
              <div class="mb-4">
                <label for="Date" class="block text-gray-700">Date:</label>
                <input id="Date" type="date" class="mt-1 block w-full" v-model="dietForm.Date" />
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
                        </div>

                        <button @click="submitdietForm" type="submit" class="px-4 py-2 bg-indigo-600 text-white">Submit</button>


                    

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
                        <!-- Medication Form -->
                        <div v-show="activeTab === 'medication'">
                            <h3 class="text-lg font-semibold mb-4">Medication Form</h3>
                            <form @submit.prevent="submitmedicationForm">
          <div class="mb-4">
            <label for="MedicationName" class="block text-gray-700">Medication Name:</label>
            <input id="MedicationName" type="text" class="mt-1 block w-full" v-model="medicationsForm.MedicationName" />
          </div>
      
          <div class="mb-4">
            <label for="Type" class="block text-gray-700">Type:</label>
            <input id="Type" type="text" class="mt-1 block w-full" v-model="medicationsForm.Type" />
          </div>
      
          <div class="mb-4">
            <label for="Dosage" class="block text-gray-700">Dosage:</label>
            <input id="Dosage" type="text" class="mt-1 block w-full" v-model="medicationsForm.Dosage" />
          </div>
      
          <div class="mb-4">
            <label for="Frequency" class="block text-gray-700">Frequency:</label>
            <input id="Frequency" type="text" class="mt-1 block w-full" v-model="medicationsForm.Frequency" />
          </div>
      
          <div class="mb-4">
            <label for="StartDate" class="block text-gray-700">Start Date:</label>
            <input id="StartDate" type="date" class="mt-1 block w-full" v-model="medicationsForm.StartDate" />
          </div>
      
          <button @click="submitmedicationForm" type="submit" class="px-4 py-2 bg-indigo-600 text-white">Submit</button>
        </form>
                        </div>

                        <!-- Patient Form 
                        <div v-show="activeTab === 'patient'">
                            <h3 class="text-lg font-semibold mb-4">Patient Information Form</h3>
                            <form @submit.prevent="submitpatientForm">

                            <div class="mb-4">
              <label for="PhoneNo" class="block text-gray-700">Phone Number:</label>
              <input id="PhoneNo" type="text" class="mt-1 block w-full" v-model="patientForm.PhoneNo" />
            </div>
      
            <div class="mb-4">
              <label for="Gender" class="block text-gray-700">Gender:</label>
              <input id="Gender" type="text" class="mt-1 block w-full" v-model="patientForm.Gender" />
            </div>
      
            <div class="mb-4">
              <label for="Address" class="block text-gray-700">Address:</label>
              <input id="Address" type="text" class="mt-1 block w-full" v-model="patientForm.Address" />
            </div>
      
            <div class="mb-4">
              <label for="EmergencyContactName" class="block text-gray-700">Emergency Contact Name:</label>
              <input id="EmergencyContactName" type="text" class="mt-1 block w-full" v-model="patientForm.EmergencyContactName" />
            </div>
      
            <div class="mb-4">
              <label for="EmergencyContactPhone" class="block text-gray-700">Emergency Contact Phone:</label>
              <input id="EmergencyContactPhone" type="text" class="mt-1 block w-full" v-model="patientForm.EmergencyContactPhone" />
            </div>
      
            <div class="mb-4">
              <label for="DoctorID" class="block text-gray-700">Doctor ID:</label>
              <input id="DoctorID" type="text" class="mt-1 block w-full" v-model="patientForm.DoctorID" />
            </div>
      
            <button @click="submitpatientForm" type="submit" class="px-4 py-2 bg-indigo-600 text-white">Submit</button>
          </form>
                        </div>
                    -->

                        <!-- Lock Screen Modal -->
                        <div v-show="isLocked" class="fixed inset-0 bg-gray-600 bg-opacity-75 flex items-center justify-center">
                            <div class="bg-white p-6 rounded-lg shadow-lg">
                                <h3 class="text-lg font-semibold mb-4">Screen Locked</h3>
                                <p class="mb-4">Enter your password to unlock the screen.</p>
                                <input v-model="password" type="password" placeholder="Password" class="mb-4 p-2 border border-gray-300 rounded" />
                                <button @click="unlock" class="px-4 py-2 bg-blue-500 text-white rounded">Unlock</button>
                            </div>
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
          </div>

          <ProgressBar/>
            
</AppLayout>
</template>

<style scoped>
.blur {
    filter: blur(4px);
}
</style>
