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
const form = useForm({
    _method: 'PUT',
    name: '',
    email: '',
    photo: null,
    first_name: '',
    last_name: '',
    contact: '',
    residence: '',
    doctor_name: '',
    doctor_email: '',
    doctor_contact: '',
    // role: '',
});

// Define the total number of fields inside the profile information section
const totalFields = 8;

// Define a ref to hold the percentage of completion
const completionPercentage = ref(0);

// Define an array of input labels corresponding to the fields in the profile information section
const profileFields = [
    'first_name',
    'last_name',
    'contact',
    'residence',
    'doctor_name',
    'doctor_email',
    'doctor_contact',
    // 'role'
];

// Watch the form fields directly
watch(
    () => ({
        first_name: form.first_name,
        last_name: form.last_name,
        contact: form.contact,
        residence: form.residence,
        doctor_name: form.doctor_name,
        doctor_email: form.doctor_email,
        doctor_contact: form.doctor_contact,
        // role: form.role,
    }),
    () => {
        let filledFields = 0;

        for (const fieldName of profileFields) {
            if (form[fieldName] && form[fieldName] !== '') {
                filledFields++;
            }
        }

        completionPercentage.value = (filledFields / totalFields) * 100;
    },
    { deep: true }
);

onMounted(() => {
    if (props.user) {
        form.name = props.user.name || '';
        form.email = props.user.email || '';
        form.first_name = props.user.first_name || '';
        form.last_name = props.user.last_name || '';
        form.contact = props.user.contact || '';
        form.residence = props.user.residence || '';
        form.doctor_name = props.user.doctor_name || '';
        form.doctor_email = props.user.doctor_email || '';
        form.doctor_contact = props.user.doctor_contact || '';
        // form.role = props.user.role || '';
    }
});

// const page = usePage();
// const user = computed(() => page.props.auth.user);

// Function to save profile information
const updateProfileInformation = async () => {
    try {
        await form.put(route('user-profile-information.update'));
        // Optionally, you can add a success message or other post-save actions here
    } catch (error) {
        console.error('Error saving profile:', error);
        // Optionally, handle error cases here
    }
};
// Function to handle sending email verification
const sendEmailVerification = async () => {
    try {
        await axios.post(route('verification.send'));
        // Optionally, you can handle success message or state update here
    } catch (error) {
        console.error('Error sending email verification:', error);
        // Optionally, handle error cases here
    }
};


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
    <FormSection @submitted="updateProfileInformation">
        <template #title>
            Enter Doctor Credentials
        </template>
       

        <template #form>
            <!-- Profile Photo -->
           
            <!-- Name -->
            <!-- <div class="col-span-6 sm:col-span-4">
                <InputLabel for="name" value="Name" />
                <TextInput
                    id="name"
                    v-model="form.name"
                    type="text"
                    class="mt-1 block w-full"
                    required
                    autocomplete="name"
                />
                <InputError :message="form.errors.name" class="mt-2" />
            </div> -->

            <!-- Email -->
             <!-- <div class="col-span-6 sm:col-span-4">
                <InputLabel for="email" value="Email" />
                <TextInput
                    id="email"
                    v-model="form.email"
                    type="email"
                    class="mt-1 block w-full"
                    required
                    autocomplete="username"
                /> 
                <InputError :message="form.errors.email" class="mt-2" />

                <div v-if="$page.props.jetstream.hasEmailVerification && user.email_verified_at === null">
                    <p class="text-sm mt-2">
                        Your email address is unverified.

                        <Link
                            :href="route('verification.send')"
                            method="post"
                            as="button"
                            class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                            @click.prevent="sendEmailVerification"
                        >
                            Click here to re-send the verification email.
                        </Link>
                    </p>

                    <div v-show="verificationLinkSent" class="mt-2 font-medium text-sm text-green-600">
                        A new verification link has been sent to your email address.
                    </div>
                </div>
            </div>  -->

            <!-- First Name -->
            <!-- <div class="col-span-6 sm:col-span-4">
                <InputLabel for="first_name" value="First Name" />
                <TextInput
                    id="first_name"
                    v-model="form.first_name"
                    type="text"
                    class="mt-1 block w-full"
                    required
                />
                <InputError :message="form.errors.first_name" class="mt-2" />
            </div> -->

            <!-- Last Name -->
            <!-- <div class="col-span-6 sm:col-span-4">
                <InputLabel for="last_name" value="Last Name" />
                <TextInput
                    id="last_name"
                    v-model="form.last_name"
                    type="text"
                    class="mt-1 block w-full"
                    required
                />
                <InputError :message="form.errors.last_name" class="mt-2" />
            </div> -->

            <!-- Contact -->
            <!-- <div class="col-span-6 sm:col-span-4">
                <InputLabel for="contact" value="Contact" />
                <TextInput
                    id="contact"
                    v-model="form.contact"
                    type="text"
                    class="mt-1 block w-full"
                    required
                />
                <InputError :message="form.errors.contact" class="mt-2" />
            </div> -->

            <!-- Residence -->
            <!-- <div class="col-span-6 sm:col-span-4">
                <InputLabel for="residence" value="Residence" />
                <TextInput
                    id="residence"
                    v-model="form.residence"
                    type="text"
                    class="mt-1 block w-full"
                    required
                />
                <InputError :message="form.errors.residence" class="mt-2" />
            </div> -->
  <!-- Doctor Contact -->
  <div class="col-span-6 sm:col-span-4">
                <InputLabel for="doctor_contact" value="Doctor ID" />
                <TextInput
                    id="doctor_contact"
                    v-model="form.doctor_contact"
                    type="text"
                    class="mt-1 block w-full"
                    required
                />
                <InputError :message="form.errors.doctor_contact" class="mt-2" />
            </div>
            <!-- Doctor Name -->
            <div class="col-span-6 sm:col-span-4">
                <InputLabel for="doctor_name" value="Doctor Name" />
                <TextInput
                    id="doctor_name"
                    v-model="form.doctor_name"
                    type="text"
                    class="mt-1 block w-full"
                    required
                />
                <InputError :message="form.errors.doctor_name" class="mt-2" />
            </div>

            <!-- Doctor Email -->
            <div class="col-span-6 sm:col-span-4">
                <InputLabel for="doctor_email" value="Doctor Email" />
                <TextInput
                    id="doctor_email"
                    v-model="form.doctor_email"
                    type="email"
                    class="mt-1 block w-full"
                    required
                />
                <InputError :message="form.errors.doctor_email" class="mt-2" />
            </div>

          

            <!-- Role -->
            <!-- <div class="col-span-6 sm:col-span-4">
                <InputLabel for="role" value="Role" />
                <TextInput
                    id="role"
                    v-model="form.role"
                    type="text"
                    class="mt-1 block w-full"
                    required
                />
                <InputError :message="form.errors.role" class="mt-2" />
            </div> -->
        </template>

        <template #actions>
            <ActionMessage :on="form.recentlySuccessful" class="me-3">
                Saved.
            </ActionMessage>

            <div class="flex justify-between items-center mt-4">
                <PrimaryButton :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                    Save
                </PrimaryButton>
            </div>
        </template>
    </FormSection>
</template>


<style>
.blur {
    filter: blur(5px);
    transition: filter 0.3s ease-in-out;
}
</style>