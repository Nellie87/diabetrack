<script setup>
import { ref, watch, onMounted, computed } from 'vue';
import axios from 'axios';
import AppLayout from '@/Layouts/AppLayout.vue';
import { usePage } from '@inertiajs/vue3';
import GradientLineChart from '/resources/js/Components/Charts/GradientLineChart.vue';
import BarChart from '/resources/js/Components/Charts/BarChart.vue';
import MealForm from 'C:/xampp/htdocs/diabetrack/resources/js/Components/MealForm.vue';
import MealSearch from 'C:/xampp/htdocs/diabetrack/resources/js/Components/MealSearch.vue';
import ProgressDoughnutChart from '/resources/js/Components/Charts/CircleChart.vue';
import ProgressBar from '/resources/js/Components/ProgressBar.vue';
import NotificationsPanel from '/resources/js/Components/NotificationPanel.vue';
import Modal from '@/Components/Modes.vue';

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

</script>

<template>
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
                -->

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

</template>

<style>
.blur {
    filter: blur(5px);
    transition: filter 0.3s ease-in-out;
}
#app {
  font-family: Avenir, Helvetica, Arial, sans-serif;
  text-align: left;
  margin: 20px auto;
  max-width: 1000px;
}
ul {
  list-style-type: none;
  padding: 0;
}
li {
  background-color: #f4f4f4;
  margin: 5px 0;
  padding: 10px;
  border-radius: 4px;
}
</style>
