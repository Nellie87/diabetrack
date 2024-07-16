<script setup>
import { Head, Link } from '@inertiajs/vue3';
import HeaderSection from '/resources/js/Components/HeaderSection.vue';
import HeroSection from '/resources/js/Components/HeroSection.vue';
import ServicesSection from '/resources/js/Components/ServicesSection.vue';
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


</script>

<template>
    <Head title="Welcome" />
    
    <div class="bg-blue-300 text-black/50 dark:text-black/50" style="background-color:#f0f8ff">

        <!-- <img src="images/telemedlogo.jpg" width="70"  style="border-radius: 50%;" />
        <h3>DIABETRACK</h3> -->
        <div id="app">

        <div class="relative min-h-screen flex flex-col  selection:bg-[#FF2D20] selection:text-white">
            <!-- <div class="relative w-full max-w-2xl px-6 lg:max-w-7xl"> -->
                    <!-- <div class="flex lg:justify-center lg:col-start-2"> -->
                    <!-- </div> -->
                    <nav v-if="canLogin" class="-mx-3 flex flex-1 justify-end">
                        <Link
                            v-if="$page.props.auth.user"
                            :href="route('dashboard')"
                            class="rounded-md px-3 py-2 text-black ring-1 ring-transparent transition hover:text-black/70 focus:outline-none focus-visible:ring-[#FF2D20] dark:text-black dark:hover:text-black/80 dark:focus-visible:ring-black"
                        >
                            Dashboard
                        </Link>

                        <template v-else>
                            <Link
                                :href="route('login')"
                                class="rounded-md px-3 py-2 text-black ring-1 ring-transparent transition hover:text-black/70 focus:outline-none focus-visible:ring-[#FF2D20] dark:text-black dark:hover:text-black/80 dark:focus-visible:ring-black"
                                >
                                Log in
                            </Link>

                            <Link
                                v-if="canRegister"
                                :href="route('register')"
                                class="rounded-md px-3 py-2 text-black ring-1 ring-transparent transition hover:text-black/70 focus:outline-none focus-visible:ring-[#FF2D20] dark:text-black dark:hover:text-black/80 dark:focus-visible:ring-black"
                                >
                                Register
                            </Link>
                        </template>
                    </nav>
                <!-- </header> -->

        <div>
    <HeaderSection />
</div>
<div>
    <HeroSection />
</div>
<div>
       <ServicesSection />
    </div>
<!-- 
    <div>
    <SpecialitySection />
</div>
<div>
    <AppointmentSection />
    </div>
    <div>
    <DoctorsSection />
    </div> -->
    
  </div>

                <!-- <footer class="py-16 text-center text-sm text-black dark:text-white/70">
                    Laravel v{{ date('Y') }} (PHP v{{ phpVersion }})
                </footer> -->
            </div>
        </div>
    <!-- </div> -->
</template>

<style scoped>
.container {
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 2rem;
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
