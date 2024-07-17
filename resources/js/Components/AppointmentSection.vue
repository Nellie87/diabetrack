<template>
    <section class="appointment">
      <h2>Appointment Schedules</h2>
      <div class="appointment-item">
        <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit.</p>
        <button>Schedule</button>
      </div>
    </section>
  </template>
  
  <script>
  export default {
    name: 'AppointmentSection'
  }
  </script>
  
  <style scoped>
  .appointment {
    background-color: #f0f8ff;
    padding: 2rem;
    text-align: center;
  }
  
  .appointment-item {
    margin-top: 1rem;
  }
  
  .appointment-item p {
    margin-bottom: 1rem;
  }
  
  .appointment-item button {
    padding: 0.5rem 1rem;
    background-color: #007bff;
    color: #fff;
    border: none;
    border-radius: 5px;
    cursor: pointer;
  }
  </style>
  