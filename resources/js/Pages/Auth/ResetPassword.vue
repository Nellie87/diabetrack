<script setup>
import { Head, useForm } from '@inertiajs/vue3';
import { computed, ref, watch } from 'vue';
import AuthenticationCard from '@/Components/AuthenticationCard.vue';
import AuthenticationCardLogo from '@/Components/AuthenticationCardLogo.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';

const props = defineProps({
    email: String,
    token: String,
});

const form = useForm({
    token: props.token,
    email: props.email,
    password: '',
    password_confirmation: '',
});

const submit = () => {
    form.post(route('password.update'), {
        onFinish: () => form.reset('password', 'password_confirmation'),
    });
};

// Password requirements
const password = ref('');
const requirements = ref([
    { id: 1, text: 'At least 8 characters', met: false },
    { id: 2, text: 'Contains uppercase and lowercase letters', met: false },
    { id: 3, text: 'Contains numbers', met: false },
    { id: 4, text: 'Contains special characters', met: false }
]);

const checkRequirements = (password) => {
    requirements.value[0].met = password.length >= 8;
    requirements.value[1].met = /[a-z]/.test(password) && /[A-Z]/.test(password);
    requirements.value[2].met = /\d/.test(password);
    requirements.value[3].met = /[!@#$%^&*(),.?":{}|<>]/.test(password);
};

// Event listener for password input
const handlePasswordInput = (newPassword) => {
    password.value = newPassword;
    checkRequirements(newPassword);
};

// Computed property to check if all requirements are met
const allRequirementsMet = computed(() => {
    return requirements.value.every(requirement => requirement.met);
});
</script>

<template>
    <Head title="Reset Password" />

    <AuthenticationCard>
        <template #logo>
            <AuthenticationCardLogo />
        </template>

        <form @submit.prevent="submit">
            <div>
                <InputLabel for="email" value="Email" />
                <TextInput
                    id="email"
                    v-model="form.email"
                    type="email"
                    class="mt-1 block w-full"
                    required
                    autofocus
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
                    @input="handlePasswordInput($event.target.value)"
                />
                <InputError class="mt-2" :message="form.errors.password" />
            </div>

            <div class="mt-2">
                <ul v-if="password">
                    <li v-for="requirement in requirements" :key="requirement.id" :class="{'text-green-500': requirement.met, 'text-red-500': !requirement.met}">
                        <span v-if="requirement.met">✔</span>
                        <span v-else>✖</span>
                        {{ requirement.text }}
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

            <div class="flex items-center justify-end mt-4">
                <PrimaryButton 
                    :class="{ 'opacity-25': form.processing || !allRequirementsMet }" 
                    :disabled="form.processing || !allRequirementsMet"
                    :title="!allRequirementsMet ? 'Please check your details' : ''"
                >
                    Reset Password
                </PrimaryButton>
            </div>
        </form>
    </AuthenticationCard>
</template>

<style scoped>
.primary-button[disabled]:hover {
    cursor: not-allowed;
}
</style>
