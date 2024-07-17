<template>
    <section class="services">
      <div class="service-item">
        <h1>About Us</h1>
        <p>Welcome to DiabeTrack, where we're dedicated to revolutionizing diabetes management through cutting-edge technology. Our mission is to empower individuals living with diabetes by providing them with a seamless and intuitive platform to monitor their readings and communicate with healthcare providers in real time.

At DiabeTrack, we understand the challenges of managing diabetes and the importance of consistent monitoring. That's why we've developed a user-friendly app that allows you to track your blood sugar levels, medication intake, and lifestyle habits effortlessly. Our goal is to support you in achieving optimal health outcomes by facilitating proactive communication with your healthcare team.

Driven by innovation and a commitment to improving lives, DiabeTrack is your partner in navigating the complexities of diabetes management. Join us on this journey towards better health and greater peace of mind.

</p>
      </div>
      <!-- <div class="service-item">
        <h3>Appointments</h3>
        <button>Request</button>
      </div>
      <div class="service-item">
        <h3>Find Doctors</h3>
        <button>Doctors</button>
      </div>
      <div class="service-item">
        <h3>Find Locations</h3>
        <button>Locations</button>
      </div> -->
    </section>
  </template>
  
  <script>
  export default {
    name: 'ServicesSection'
  }
  </script>
  
  <style scoped>
  .services {
    display: flex;
    justify-content: space-around;
    background-color: #f0f8ff;
    padding: 2rem;
  }
  
  .service-item {
    text-align: center;
  }
  
  .service-item h1 {
    font-size: 2rem; /* Adjust font size as needed */
    margin-bottom: 1rem;
  }
  
  .service-item p {
    font-size: 1.1rem; /* Adjust font size as needed */
    line-height: 1.6; /* Adjust line height for better readability */
    color: #333; /* Adjust text color */
  }
  
  .service-item button {
    padding: 0.5rem 1rem;
    background-color: #007bff;
    color: #fff;
    border: none;
    border-radius: 5px;
    cursor: pointer;
  }
  </style>
  