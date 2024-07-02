<template>
    <div v-if="show" class="fixed inset-0 flex items-center justify-center bg-gray-800 bg-opacity-75 transition-opacity duration-300">
        <div class="bg-white p-8 rounded-lg shadow-xl max-w-sm w-full transform transition-transform duration-300">
            <div class="flex justify-between items-center border-b pb-3">
                <h3 class="text-2xl font-bold text-gray-900">{{ title }}</h3>
                <button @click="closeModal" class="text-gray-500 hover:text-gray-700 focus:outline-none">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" class="h-6 w-6">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
            <div class="mt-4">
                <p class="text-gray-700">{{ message }}</p>
            </div>
            <div class="mt-6 flex justify-end">
                <button @click="closeModal" class="px-4 py-2 bg-indigo-600 text-white rounded-lg shadow hover:bg-indigo-700 transition duration-300">Close</button>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    props: {
        show: {
            type: Boolean,
            default: false
        },
        title: {
            type: String,
            default: 'Alert'
        },
        message: {
            type: String,
            default: ''
        }
    },
    methods: {
        closeModal() {
            this.$emit('close');
        }
    }
};
</script>

<style scoped>
.fixed {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
}
.bg-opacity-75 {
    background-color: rgba(31, 41, 55, 0.75);
}
.transition-opacity {
    transition: opacity 0.3s ease-in-out;
}
.transition-transform {
    transition: transform 0.3s ease-in-out;
}
</style>
