<script setup>
import { ref, watch, onMounted, computed } from 'vue';
import axios from 'axios';
import AppLayout from '@/Layouts/AppLayout.vue';
import { usePage } from '@inertiajs/vue3';
import HeroSection2 from '/resources/js/Components/HeroSection2.vue';
import GradientLineChart from '/resources/js/Components/Charts/GradientLineChart.vue';
import BarChart from '/resources/js/Components/Charts/BarChart.vue';
import MealForm from 'C:/xampp/htdocs/diabetrack/resources/js/Components/MealForm.vue';
import MealSearch from 'C:/xampp/htdocs/diabetrack/resources/js/Components/MealSearch.vue';
import ProgressDoughnutChart from '/resources/js/Components/Charts/CircleChart.vue';
import ProgressBar from '/resources/js/Components/ProgressBar.vue';
import NotificationsPanel from '/resources/js/Components/NotificationPanel.vue';
import Modal from '@/Components/Modes.vue';
import HeroSection from '@/Components/HeroSection.vue';

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

const foodForm = ref({
    Food: '',
});

async function submitfoodForm() {
    try {
        const formData = new FormData();
        Object.keys(foodForm.value).forEach(key => {
            formData.append(key, foodForm.value[key]);
        });

        const response = await axios.post('/submit-form4', formData, {
            headers: {
                'Content-Type': 'multipart/form-data'
            }
        });

        if (response.data.success) {
            alert('Form submitted successfully.');
                foodForm.value = {
                Food: '',
               }
        } else {
            alert('Submission failure. Please try again.');
        }
    } catch (error) {
        console.error('Error submitting form:', error);
        alert('An error occurred while submitting the form. Please try again.');
    }
    const meals = ref([]);

const addMeal = (meal) => {
  meals.value.push(meal);
};
}
const myVariable = ref(false);
function showItem(){
    myVariable.value = true;
    localStorage.setItem('myvariable', 'true');
}
const myVariable1 = ref(false);
function showItem1(){
    myVariable1.value = true;
    localStorage.setItem('myvariable1', 'true');
}
const myVariable2 = ref(false);
function showItem2(){
    myVariable2.value = true;
    localStorage.setItem('myvariable2', 'true');
}
</script>

<template>
    <AppLayout>
        <HeroSection2/>
        <div class="py-4 container-fluid">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Food Log
            </h2>
            <div>
                <button type="button" @click="showItem">Add Breakfast Meal</button>
                <div v-if="myVariable==true" id="app">
                <h1>Meal Planner</h1>
                <MealForm mealType="Breakfast" @meal-added="addMeal" />
                </div>
            </div>
            <div>
                <button type="button" @click="showItem1">Add Lunch Meal</button>
                <div v-if="myVariable1==true" id="app">
                <h1>Meal Planner</h1>
                <MealForm mealType="Lunch" @meal-added="addMeal" />
                </div>
            </div>
            <div>
                <button type="button" @click="showItem2">Add Dinner Meal</button>
                <div v-if="myVariable2==true" id="app">
                <h1>Meal Planner</h1>
                <MealForm mealType="Dinner" @meal-added="addMeal" />
                </div>
            </div>   
            <div class="col-lg-7">
                <div class="card z-index-2">
                    <!--
                    <BarChart
                    id="chart-bar"
                    title="Breakfast Chart"
                    apiUrl="http://127.0.0.1:8000/chart2"
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
                -->
                </div>
            </div>         
        </div>
        <!--
        <MealForm mealType="Lunch" @meal-added="addMeal" />
        <MealForm mealType="Dinner" @meal-added="addMeal" />-->

    </AppLayout>
</template>
    <!--
 <AppLayout title="Dashboard">
    <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Patient Food Log
            </h2>
        </template>
        <FoodSearch/>
<!--
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
                

          <div id="app">
    <h1>Meal Planner</h1>
    <MealForm mealType="Breakfast" @meal-added="addMeal" />
    <MealForm mealType="Lunch" @meal-added="addMeal" />
    <MealForm mealType="Dinner" @meal-added="addMeal" />
    
    <h2>Meals Entered</h2>
    <ul>
      <li v-for="(meal, index) in meals" :key="index">
        <strong>{{ meal.type }}:</strong> {{ meal.meal }} - {{ meal.description }}
      </li>
    </ul>
  </div>

  <div id="app">
    <h1>Meal Planner</h1>
    <MealSearch />
  </div>

 </AppLayout>
-->

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

