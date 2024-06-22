<template>
    <section class="doctors">
      <h2>Our Doctors</h2>
      <div class="doctor-item">
        <img src="" alt="Doctor Image" />
        <h3>Dr. John Doe</h3>
      </div>
      <div class="doctor-item">
        <img src="" alt="Doctor Image" />
        <h3>Dr. Jane Smith</h3>
      </div>
      <div class="doctor-item">
        <img src="" alt="Doctor Image" />
        <h3>Dr. Alice Johnson</h3>
      </div>
    </section>
  </template>
  
  <script>
  export default {
    name: 'DoctorsSection'
  }
  </script>
  
  <style scoped>
  .doctors {
    background-color: #e0f7fa;
    padding: 2rem;
    text-align: center;
  }
  
  .doctor-item {
    display: inline-block;
    margin: 1rem;
    text-align: center;
  }
  
  .doctor-item img {
    max-width: 100px;
    height: auto;
    border-radius: 50%;
  }
  
  .doctor-item h3 {
    margin-top: 0.5rem;
  }
  </style>
  