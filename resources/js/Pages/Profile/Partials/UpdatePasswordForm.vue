<script setup>
import { ref, watch, computed } from 'vue';
import { useForm } from '@inertiajs/vue3';
import ActionMessage from '@/Components/ActionMessage.vue';
import FormSection from '@/Components/FormSection.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';

const passwordInput = ref(null);
const currentPasswordInput = ref(null);

const form = useForm({
    current_password: '',
    password: '',
    password_confirmation: '',
});

const passwordRequirements = ref([
    { text: 'At least 8 characters', met: false, check: (password) => password.length >= 8 },
    { text: 'At least one uppercase letter', met: false, check: (password) => /[A-Z]/.test(password) },
    { text: 'At least one lowercase letter', met: false, check: (password) => /[a-z]/.test(password) },
    { text: 'At least one number', met: false, check: (password) => /[0-9]/.test(password) },
    { text: 'At least one special character', met: false, check: (password) => /[!@#$%^&*(),.?":{}|<>]/.test(password) },
]);

const allRequirementsMet = computed(() => {
    return passwordRequirements.value.every(requirement => requirement.met);
});

watch(() => form.password, (newPassword) => {
    passwordRequirements.value.forEach(requirement => {
        requirement.met = requirement.check(newPassword);
    });
});

const showPasswordRequirements = ref(false);

const togglePasswordRequirements = () => {
    showPasswordRequirements.value = form.password.length > 0;
};

const showNewPassword = ref(false);
const showConfirmPassword = ref(false);

const toggleShowNewPassword = () => {
    showNewPassword.value = !showNewPassword.value;
};

const toggleShowConfirmPassword = () => {
    showConfirmPassword.value = !showConfirmPassword.value;
};

const updatePassword = () => {
    form.put(route('user-password.update'), {
        errorBag: 'updatePassword',
        preserveScroll: true,
        onSuccess: () => form.reset(),
        onError: () => {
            if (form.errors.password) {
                form.reset('password', 'password_confirmation');
                passwordInput.value.focus();
            }

            if (form.errors.current_password) {
                form.reset('current_password');
                currentPasswordInput.value.focus();
            }
        },
    });
};
</script>

<template>
    <FormSection @submitted="updatePassword">
        <template #title>
            Update Password
        </template>

        <template #description>
            Ensure your account is using a long, random password to stay secure.
        </template>

        <template #form>
            <div class="col-span-6 sm:col-span-4">
                <div class="flex items-center justify-between">
                    <InputLabel for="current_password" value="Current Password" />
                </div>
                <TextInput
                    id="current_password"
                    ref="currentPasswordInput"
                    v-model="form.current_password"
                    type="password"
                    class="mt-1 block w-full"
                    autocomplete="current-password"
                />
                <InputError :message="form.errors.current_password" class="mt-2" />
            </div>

            <div class="col-span-6 sm:col-span-4">
                <div class="flex items-center justify-between">
                    <InputLabel for="password" value="New Password" />
                    <label class="flex items-center">
                        <input type="checkbox" @change="toggleShowNewPassword" class="mr-2" />
                        <span>Show Password</span>
                    </label>
                </div>
                <TextInput
                    id="password"
                    ref="passwordInput"
                    v-model="form.password"
                    :type="showNewPassword ? 'text' : 'password'"
                    class="mt-1 block w-full"
                    autocomplete="new-password"
                    @input="togglePasswordRequirements"
                />
                <InputError :message="form.errors.password" class="mt-2" />
                <ul class="mt-2" v-show="showPasswordRequirements">
                    <li v-for="requirement in passwordRequirements" :key="requirement.text" :class="{'text-green-500': requirement.met, 'text-red-500': !requirement.met}">
                        <i v-if="requirement.met" class="fas fa-check"></i>
                        <i v-else class="fas fa-times"></i>
                        {{ requirement.text }}
                    </li>
                </ul>
            </div>

            <div class="col-span-6 sm:col-span-4">
                <div class="flex items-center justify-between">
                    <InputLabel for="password_confirmation" value="Confirm Password" />
                    <label class="flex items-center">
                        <input type="checkbox" @change="toggleShowConfirmPassword" class="mr-2" />
                        <span>Show Password</span>
                    </label>
                </div>
                <TextInput
                    id="password_confirmation"
                    v-model="form.password_confirmation"
                    :type="showConfirmPassword ? 'text' : 'password'"
                    class="mt-1 block w-full"
                    autocomplete="new-password"
                />
                <InputError :message="form.errors.password_confirmation" class="mt-2" />
            </div>
        </template>

        <template #actions>
            <ActionMessage :on="form.recentlySuccessful" class="me-3">
                Saved.
            </ActionMessage>

            <PrimaryButton
                :class="{ 'opacity-25': form.processing || !allRequirementsMet }"
                :disabled="form.processing || !allRequirementsMet"
                :title="!allRequirementsMet ? 'Please check your details' : ''"
            >
                Save
            </PrimaryButton>
        </template>
    </FormSection>
</template>

<style scoped>
.primary-button[disabled]:hover {
    cursor: not-allowed;
}
</style>
