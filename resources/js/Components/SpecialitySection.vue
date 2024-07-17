<template>
    <section class="speciality">
      <h2>Our Speciality</h2>
      <div class="speciality-item">
        <h3>Online Appointment</h3>
        <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit.</p>
        <button>Learn More</button>
      </div>
    </section>
  </template>
  
  <script>
  export default {
    name: 'SpecialitySection'
  }
  </script>
  
  <style scoped>
  .speciality {
    background-color: #e0f7fa;
    padding: 2rem;
    text-align: center;
  }
  
  .speciality-item {
    margin-top: 1rem;
  }
  
  .speciality-item h3 {
    margin-bottom: 1rem;
  }
  
  .speciality-item p {
    margin-bottom: 1rem;
  }
  
  .speciality-item button {
    padding: 0.5rem 1rem;
    background-color: #007bff;
    color: #fff;
    border: none;
    border-radius: 5px;
    cursor: pointer;
  }
  </style>
  