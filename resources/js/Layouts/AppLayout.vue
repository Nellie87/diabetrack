<script setup>
import { ref, watch, onMounted } from 'vue';
import axios from 'axios';
import { Head, Link, router } from '@inertiajs/vue3';
import ApplicationMark from '@/Components/ApplicationMark.vue';
import Banner from '@/Components/Banner.vue';
import Dropdown from '@/Components/Dropdown.vue';
import SideNav from '@/Components/SideNav.vue';
import DropdownLink from '@/Components/DropdownLink.vue';
import NavLink from '@/Components/NavLink.vue';
import ResponsiveNavLink from '@/Components/ResponsiveNavLink.vue';

defineProps({
    title: String,
});

const showingNavigationDropdown = ref(false);

const switchToTeam = (team) => {
    router.put(route('current-team.update'), {
        team_id: team.id,
    }, {
        preserveState: false,
    });
};

const logout = () => {
    router.post(route('logout'));
};

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

const confirmLogout = () => {
        if (confirm("Are you sure you want to log out?")) {
            logout(); // Call the logout function if the user confirms
        }
    };

// Handle the back button click
const goBack = () => {
    window.history.back();
};

const navLinks= [
        { text: 'Home', url: '/' },
        {text: 'Profile', url:'/user/profile'},
        {text: 'Glucose Info', url: '/patient'},
        {text: 'Diet', url: '/diet'},
        {text: 'Medications', url: '/patient3'},
        {text: 'Doctors', url: '/doctors'}
      ]
</script>

<template>
   <div id="app">
        <div class="layout">
            <SideNav :links="navLinks" />
            <main class="main-content" :class="{ 'blur': isLocked }" >
            <slot/>
            </main>
    </div>
  </div>
     <!-- Lock screen overlay with centered square GIF -->
    <div v-if="isLocked" class="fixed inset-0 flex items-center justify-center bg-gray-900 bg-opacity-80 backdrop-blur-md">
        <div class="bg-white p-8 rounded-lg shadow-lg flex flex-col items-center">
               <h2 class="text-2xl font-semibold mb-4">Screen is Locked</h2>
               <div class="square-gif-container">
                         <img src="/videos/waiting.gif" class="square-gif rounded-md mb-4" alt="Locked Screen GIF">
                </div>
                <input v-model="password" type="password" placeholder="Enter password to continue" class="w-full px-3 py-2 border rounded-md mb-4">
                <button @click="unlock" class="bg-blue-500 text-white px-4 py-2 rounded-md justify-center items-center">Unlock</button>
        </div>
    </div>
  <!-- Lock screen button -->
        <div class="fixed bottom-4 right-4">
            <button @click="manualLock" class="bg-red-500 text-white px-4 py-2 rounded-md">Lock Screen</button>
        </div>

        
</template>

<style>
.layout {
  display: flex;
}
.main-content {
  flex: 1;
  background-color: #fff;
}
#app {
  font-family: Avenir, Helvetica, Arial, sans-serif;
  -webkit-font-smoothing: antialiased;
  -moz-osx-font-smoothing: grayscale;
  text-align: center;
  color: #2c3e50;
  height: 100%;
  width: 100%;
  justify-content: center;
  align-items: center;
}
.blur {
    filter: blur(5px);
    transition: filter 0.3s ease-in-out;
}
nav {
    display: block;
    position: fixed;
    top: 0;
    width: 100%;
    z-index: 10;
    background-color: white;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}
/* CSS for square aspect ratio */
.square-gif-container {
    display: flex;
    justify-content: center;
    align-items: center;
    width: 300px; /* Adjust container width */
    height: 200px; /* Adjust container height */
}

.square-gif {
    max-width: 100%;
    max-height: 100%;
    object-fit: cover; /* Ensure the GIF fills the square without stretching */
}
</style>