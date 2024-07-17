<script setup>
import { Head, Link } from '@inertiajs/vue3';
import { Inertia } from '@inertiajs/inertia';
import HeaderSection from '/resources/js/Components/HeaderSection.vue';
import HeroSection from '/resources/js/Components/HeroSection.vue';
import ServicesSection from '/resources/js/Components/ServicesSection.vue';
import Landing from '/resources/js/Components/Landing.vue';
import SpecialitySection from '/resources/js/Components/SpecialitySection.vue';
import AppointmentSection from '/resources/js/Components/AppointmentSection.vue';
import DoctorsSection from '/resources/js/Components/DoctorsSection.vue';

defineProps({
    canLogin: {
        type: Boolean,
    },
    canRegister: {
        type: Boolean,
    },
    laravelVersion: {
        type: String,
        required: true,
    },
    phpVersion: {
        type: String,
        required: true,
    },
});

function handleImageError() {
    document.getElementById('screenshot-container')?.classList.add('!hidden');
    document.getElementById('docs-card')?.classList.add('!row-span-1');
    document.getElementById('docs-card-content')?.classList.add('!flex-row');
    document.getElementById('background')?.classList.add('!hidden');
}

function logout() {
    Inertia.post(route('logout'));
}
</script>

<template>
    <div>
        <div class="bg-blue-300 text-black/50 dark:text-black/50" style="background-color:#f0f8ff">
            <nav v-if="canLogin" class="flex-container">
                <button
                    v-if="$page.props.auth.user"
                    @click="logout"
                    class="flex-item bg-blue-500 text-white rounded-md px-4 py-2 transition hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-300 dark:focus:ring-blue-700"
                >
                    Log Out
                </button>
                <template v-else>
                    <Link
                        :href="route('login')"
                        class="flex-item bg-blue-500 text-white rounded-md px-4 py-2 transition hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-300 dark:focus:ring-blue-700">
                        Log in
                    </Link>
                    <Link
                        v-if="canRegister"
                        :href="route('register')"
                        class="flex-item bg-blue-500 text-white rounded-md px-4 py-2 transition hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-300 dark:focus:ring-blue-700">
                        Register
                    </Link>
                </template>
            </nav>
            <div> 
                <HeroSection />
            </div>
            <div>
                <Landing />            
            </div>
            <div>
                <ServicesSection />
            </div>
        </div>
    </div>
</template>

<style scoped>
.flex-container {
  display: flex;
  justify-content: right;
}
.flex-item{
    margin-right: 10px;
}

.container {
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 1rem;
  background-color: #f9f9f9;
}

.text-section {
  max-width: 50%;
}

.text-section h1 {
  font-size: 3rem;
  color: #333;
  margin-bottom: 1rem;
}

.text-section p {
  font-size: 1.2rem;
  color: #666;
  margin-bottom: 2rem;
}

.find-doctors-btn {
  padding: 0.8rem 2rem;
  font-size: 1rem;
  color: white;
  background-color: #007bff;
  border: none;
  border-radius: 5px;
  cursor: pointer;
}

.image-section img {
  max-width: 100%;
  height: auto;
  border-radius: 10px;
}
#app {
  font-family: 'Avenir', Helvetica, Arial, sans-serif;
  -webkit-font-smoothing: antialiased;
  -moz-osx-font-smoothing: grayscale;
  text-align: center;
  color: #2c3e50;
  margin: 0;
  padding: 0;
}
</style>

     
   

