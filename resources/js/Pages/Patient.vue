<script setup>
import { ref, watch, onMounted } from 'vue';
import axios from 'axios';
// import { useForm } from '@inertiajs/vue3';
import { usePage } from '@inertiajs/vue3';
import { computed } from 'vue';
import { Link, router, useForm } from '@inertiajs/vue3';
import ActionMessage from '@/Components/ActionMessage.vue';
import FormSection from '/resources/js/Components/FormSection.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import TextInput from '@/Components/TextInput.vue';

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

const page = usePage();
const user = computed(() => page.props.auth.user);

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

</script>
<template>
    <AppLayout title="Dashboard">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Patient Dashboard
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

        <div>
            <p class="mt-6 text-gray-500 leading-relaxed">
                How are you feeling today?
            </p>
        </div>

        <div class="bg-gray-200 bg-opacity-25 grid grid-cols-1 md:grid-cols-2 gap-6 lg:gap-8 p-6 lg:p-8">
            <div>
                <div class="flex items-center">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" class="w-6 h-6 stroke-gray-400">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 006 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 016 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 016-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0018 18a8.967 8.967 0 00-6 2.292m0-14.25v14.25" />
                    </svg>
                    <h2 class="ms-3 text-xl font-semibold text-gray-900">
                        <a href="https://laravel.com/docs">Enter readings</a>
                    </h2>
                </div>

                <p class="mt-4 text-gray-500 text-sm leading-relaxed">
                    Laravel has wonderful documentation covering every aspect of the framework. Whether you're new to the framework or have previous experience, we recommend reading all of the documentation from beginning to end.
                </p>

                <p class="mt-4 text-sm">
                    <a href="https://laravel.com/docs" class="inline-flex items-center font-semibold text-indigo-700">
                        Explore the documentation

                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" class="ms-1 w-5 h-5 fill-indigo-500">
                            <path fill-rule="evenodd" d="M5 10a.75.75 0 01.75-.75h6.638L10.23 7.29a.75.75 0 111.04-1.08l3.5 3.25a.75.75 0 010 1.08l-3.5 3.25a.75.75 0 11-1.04-1.08l2.158-1.96H5.75A.75.75 0 015 10z" clip-rule="evenodd" />
                        </svg>
                    </a>
                </p>
            </div>

            <div>
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
        
       

        <template #form>
            <!-- Profile Photo -->
             <div></div><br >          <h2>
            Doctor Details
       </h2>
           
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

            <!-- Doctor Contact -->
            <div class="col-span-6 sm:col-span-4">
                <InputLabel for="doctor_contact" value="Doctor Contact" />
                <TextInput
                    id="doctor_contact"
                    v-model="form.doctor_contact"
                    type="text"
                    class="mt-1 block w-full"
                    required
                />
                <InputError :message="form.errors.doctor_contact" class="mt-2" />
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