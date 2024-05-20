<script setup>
import { ref, watch, onMounted } from 'vue';
import { Link, router, useForm } from '@inertiajs/vue3';
import ActionMessage from '@/Components/ActionMessage.vue';
import FormSection from '@/Components/FormSection.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import TextInput from '@/Components/TextInput.vue';

const props = defineProps({
    user: Object,
});

const form = useForm({
    _method: 'PUT',
    name: props.user.name,
    email: props.user.email,
    photo: null,
    first_name: '', // Define the first_name key
    last_name: '', // Define the last_name key
    contact: '', // Define the contact key
    residence: '', // Define the residence key
    doctor_name: '', // Define the doctor_name key
    doctor_email: '', // Define the doctor_email key
    doctor_contact: '', // Define the doctor_contact key
    role: '', // Define the role key
});

// Define the total number of fields inside the profile information section
const totalFields = 8;

// Define a ref to hold the percentage of completion
const completionPercentage = ref(0);

// Define an array of input labels corresponding to the fields in the profile information section
const profileFields = [
    'First Name',
    'Last Name',
    'Contact',
    'Residence',
    'Doctor Name',
    'Doctor Email',
    'Doctor Contact',
    'Role'
];

// Update the completion percentage whenever the form fields change
watch(form.fields, () => {
    let filledFields = 0;
    
    // Loop through the input labels array
    for (const label of profileFields) {
        // Get the corresponding field name based on the label
        const fieldName = label.toLowerCase().replace(/\s+/g, '_');
        
        // Check if the field has a non-empty value and not excluded
        if (form.fields[fieldName] && form.fields[fieldName] !== '' && fieldName !== 'name' && fieldName !== 'email' && fieldName !== 'photo') {
            filledFields++;
        }
    }
    
    completionPercentage.value = (filledFields / totalFields) * 100;
});



const verificationLinkSent = ref(null);
const photoPreview = ref(null);
const photoInput = ref(null);

const updateProfileInformation = () => {
    if (photoInput.value) {
        form.photo = photoInput.value.files[0];
    }

    form.post(route('user-profile-information.update'), {
        errorBag: 'updateProfileInformation',
        preserveScroll: true,
        onSuccess: () => clearPhotoFileInput(),
    });
};

const sendEmailVerification = () => {
    verificationLinkSent.value = true;
};

const selectNewPhoto = () => {
    photoInput.value.click();
};

const updatePhotoPreview = () => {
    const photo = photoInput.value.files[0];

    if (! photo) return;

    const reader = new FileReader();

    reader.onload = (e) => {
        photoPreview.value = e.target.result;
    };

    reader.readAsDataURL(photo);
};

const deletePhoto = () => {
    router.delete(route('current-user-photo.destroy'), {
        preserveScroll: true,
        onSuccess: () => {
            photoPreview.value = null;
            clearPhotoFileInput();
        },
    });
};

const clearPhotoFileInput = () => {
    if (photoInput.value?.value) {
        photoInput.value.value = null;
    }
};

// Fetch user data when the component is mounted
onMounted(() => {
    // Populate form fields with user data except for username and email
    form.first_name = props.user.first_name;
    form.last_name = props.user.last_name;
    form.contact = props.user.contact;
    form.residence = props.user.residence;
    form.doctor_name = props.user.doctor_name;
    form.doctor_email = props.user.doctor_email;
    form.doctor_contact = props.user.doctor_contact;
    form.role = props.user.role;
});
</script>



<template>
    <FormSection @submitted="updateProfileInformation">
        <template #title>
            Profile Information
        </template>

        <template #description>
            Update your account's profile information and email address.
            <div>
                    <span class="text-sm">Profile Completion: {{ completionPercentage }}%</span>
                </div>
        </template>

        <template #form>
            <!-- Profile Photo -->
            <div v-if="$page.props.jetstream.managesProfilePhotos" class="col-span-6 sm:col-span-4">
                <!-- Profile Photo File Input -->
                <input
                    id="photo"
                    ref="photoInput"
                    type="file"
                    class="hidden"
                    @change="updatePhotoPreview"
                >

                <InputLabel for="photo" value="Photo" />

                <!-- Current Profile Photo -->
                <div v-show="! photoPreview" class="mt-2">
                    <img :src="user.profile_photo_url" :alt="user.name" class="rounded-full h-20 w-20 object-cover">
                </div>

                <!-- New Profile Photo Preview -->
                <div v-show="photoPreview" class="mt-2">
                    <span
                        class="block rounded-full w-20 h-20 bg-cover bg-no-repeat bg-center"
                        :style="'background-image: url(\'' + photoPreview + '\');'"
                    />
                </div>

                <SecondaryButton class="mt-2 me-2" type="button" @click.prevent="selectNewPhoto">
                    Select A New Photo
                </SecondaryButton>

                <SecondaryButton
                    v-if="user.profile_photo_path"
                    type="button"
                    class="mt-2"
                    @click.prevent="deletePhoto"
                >
                    Remove Photo
                </SecondaryButton>

                <InputError :message="form.errors.photo" class="mt-2" />
            </div>

            <!-- Name -->
            <div class="col-span-6 sm:col-span-4">
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
            </div>

            <!-- Email -->
            <div class="col-span-6 sm:col-span-4">
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
            </div>

            <!-- Name -->
<div class="col-span-6 sm:col-span-4">
    <InputLabel for="first_name" value="First Name" />
    <TextInput
        id="first_name"
        v-model="form.first_name"
        type="text"
        class="mt-1 block w-full"
        required
    />
    <InputError :message="form.errors.first_name" class="mt-2" />
</div>

<!-- Last Name -->
<div class="col-span-6 sm:col-span-4">
    <InputLabel for="last_name" value="Last Name" />
    <TextInput
        id="last_name"
        v-model="form.last_name"
        type="text"
        class="mt-1 block w-full"
        required
    />
    <InputError :message="form.errors.last_name" class="mt-2" />
</div>

<!-- Contact -->
<div class="col-span-6 sm:col-span-4">
    <InputLabel for="contact" value="Contact" />
    <TextInput
        id="contact"
        v-model="form.contact"
        type="text"
        class="mt-1 block w-full"
        required
    />
    <InputError :message="form.errors.contact" class="mt-2" />
</div>

<!-- Residence -->
<div class="col-span-6 sm:col-span-4">
    <InputLabel for="residence" value="Residence" />
    <TextInput
        id="residence"
        v-model="form.residence"
        type="text"
        class="mt-1 block w-full"
        required
    />
    <InputError :message="form.errors.residence" class="mt-2" />
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
<div class="col-span-6 sm:col-span-4">
    <InputLabel for="role" value="Role" />
    <TextInput
        id="role"
        v-model="form.role"
        type="text"
        class="mt-1 block w-full"
        required
    />
    <InputError :message="form.errors.role" class="mt-2" />
</div>

        </template>

        <template #actions>
            <ActionMessage :on="form.recentlySuccessful" class="me-3">
                Saved.
            </ActionMessage>

            <div class="flex justify-between items-center mt-4">
                <!-- <div>
                    <span class="text-sm">Profile Completion: {{ completionPercentage }}%</span>
                </div> -->
                <PrimaryButton :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                    Save
                </PrimaryButton>
            </div>
        </template>
    </FormSection>
</template>