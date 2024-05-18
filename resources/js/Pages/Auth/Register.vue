<script setup>
import { ref, computed, watch } from 'vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import AuthenticationCard from '@/Components/AuthenticationCard.vue';
import AuthenticationCardLogo from '@/Components/AuthenticationCardLogo.vue';
import Checkbox from '@/Components/Checkbox.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';

const form = useForm({
    name: '',
    email: '',
    password: '',
    password_confirmation: '',
    terms: false,
});

const passwordCriteria = {
    minLength: 8,
    hasUpperCase: /[A-Z]/,
    hasLowerCase: /[a-z]/,
    hasNumber: /[0-9]/,
    hasSpecialChar: /[^A-Za-z0-9]/,
};

const passwordValidations = ref({
    minLength: false,
    hasUpperCase: false,
    hasLowerCase: false,
    hasNumber: false,
    hasSpecialChar: false,
});

const checkPasswordRequirements = (password) => {
    passwordValidations.value.minLength = password.length >= passwordCriteria.minLength;
    passwordValidations.value.hasUpperCase = passwordCriteria.hasUpperCase.test(password);
    passwordValidations.value.hasLowerCase = passwordCriteria.hasLowerCase.test(password);
    passwordValidations.value.hasNumber = passwordCriteria.hasNumber.test(password);
    passwordValidations.value.hasSpecialChar = passwordCriteria.hasSpecialChar.test(password);
};

watch(() => form.password, (newPassword) => {
    checkPasswordRequirements(newPassword);
});

const submit = () => {
    form.post(route('register'), {
        onFinish: () => form.reset('password', 'password_confirmation'),
    });
};
const isPasswordValid = computed(() => {
    return (
        passwordValidations.value.minLength &&
        passwordValidations.value.hasUpperCase &&
        passwordValidations.value.hasLowerCase &&
        passwordValidations.value.hasNumber &&
        passwordValidations.value.hasSpecialChar
    );
});

</script>

<template>
    <Head title="Register" />

    <AuthenticationCard>
        <template #logo>
            <AuthenticationCardLogo />
        </template>

        <form @submit.prevent="submit">
            <div>
                <InputLabel for="name" value="Name" />
                <TextInput
                    id="name"
                    v-model="form.name"
                    type="text"
                    class="mt-1 block w-full"
                    required
                    autofocus
                    autocomplete="name"
                />
                <InputError class="mt-2" :message="form.errors.name" />
            </div>

            <div class="mt-4">
                <InputLabel for="email" value="Email" />
                <TextInput
                    id="email"
                    v-model="form.email"
                    type="email"
                    class="mt-1 block w-full"
                    required
                    autocomplete="username"
                />
                <InputError class="mt-2" :message="form.errors.email" />
            </div>

            <div class="mt-4">
                <InputLabel for="password" value="Password" />
                <TextInput
                    id="password"
                    v-model="form.password"
                    type="password"
                    class="mt-1 block w-full"
                    required
                    autocomplete="new-password"
                />
                <InputError class="mt-2" :message="form.errors.password" />
            </div>
            <div class="mt-4" v-if="form.password">
        <ul class="text-sm text-gray-600">
            <li v-if="!passwordValidations.minLength" class="text-red-600">
                Minimum 8 characters
            </li>

            <li v-if="!passwordValidations.hasUpperCase" class="text-red-600">
                At least one uppercase letter
            </li>

            <li v-if="!passwordValidations.hasLowerCase" class="text-red-600">
                At least one lowercase letter
            </li>

            <li v-if="!passwordValidations.hasNumber" class="text-red-600">
                At least one number
            </li>

            <li v-if="!passwordValidations.hasSpecialChar" class="text-red-600">
                At least one special character
            </li>
        </ul>
    </div>

            <div class="mt-4">
                <InputLabel for="password_confirmation" value="Confirm Password" />
                <TextInput
                    id="password_confirmation"
                    v-model="form.password_confirmation"
                    type="password"
                    class="mt-1 block w-full"
                    required
                    autocomplete="new-password"
                />
                <InputError class="mt-2" :message="form.errors.password_confirmation" />
            </div>

            <div v-if="$page.props.jetstream.hasTermsAndPrivacyPolicyFeature" class="mt-4">
                <InputLabel for="terms">
                    <div class="flex items-center">
                        <Checkbox id="terms" v-model:checked="form.terms" name="terms" required />

                        <div class="ms-2">
                            I agree to the <a target="_blank" :href="route('terms.show')" class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">Terms of Service</a> and <a target="_blank" :href="route('policy.show')" class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">Privacy Policy</a>
                        </div>
                    </div>
                    <InputError class="mt-2" :message="form.errors.terms" />
                </InputLabel>
            </div>

            <div class="flex items-center justify-end mt-4">
                <Link :href="route('login')" class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    Already registered?
                </Link>

                <PrimaryButton 
        class="ms-4" 
        :class="{ 'opacity-25': form.processing }" 
        :disabled="form.processing || !isPasswordValid"
        title="Kindly check your details"
        >
    <template v-if="form.processing">
        <!-- Show loading icon -->
        <svg class="animate-spin h-5 w-5 mr-3" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.96 7.96 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647zm10 0l3 2.647A7.96 7.96 0 0120 12h-4c0 3.042-1.135 5.824-3 7.938l-3-2.647zM20 12a8 8 0 01-8 8v4c3.627 0 8-4.373 8-8h-4z"></path>
        </svg>
        Processing...
    </template>
    <template v-else>
        Register
    </template>
</PrimaryButton>
            </div>
            
        </form>
    </AuthenticationCard>
</template>
