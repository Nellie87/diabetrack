<template>
    <div class="landing-container dark:bg-gray-800 shadow-md p-6">
      <h1 class="text-3xl font-bold text-center text-blue-600 dark:text-blue-400 mb-4">Welcome to Diabetrack</h1>
      <p class="text-lg text-gray-700 dark:text-gray-300 mb-6 text-center">
        Our app provides comprehensive health care solutions for both patients and doctors.
      </p>
      <div class="flex justify-around items-center mb-6">
        <div class="text-center max-w-xs">
          <img src="/images/patient2.png" alt="Patients" class="w-full rounded-md mb-4">
          <h2 class="text-2xl font-semibold text-blue-600 dark:text-blue-400 mb-2">For Patients</h2>
          <p class="text-gray-600 dark:text-gray-300">
            Access your medical records, manage and stay informed about your health.
          </p>
        </div>
        <div class="text-center max-w-xs">
          <img src="/images/doctor.png" alt="Doctors" class="w-full rounded-md mb-4 ">
          <h2 class="text-2xl font-semibold text-blue-600 dark:text-blue-400 mb-2">For Doctors</h2>
          <p class="text-gray-600 dark:text-gray-300">
            Manage patient records, schedule appointments, and provide better care with our integrated tools.
          </p>
        </div>
      </div>
    </div>
  </template>
  
  <script>
  export default {
    name: 'LandingPage',
  }
  </script>
  
  <style scoped>
 
  </style>
  