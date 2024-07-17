<template>
    <section class="hero">
      <div class="text-content">
        <h1  class="text-3xl font-bold text-center text-blue-600 dark:text-blue-400 mb-4">DIABETRACK</h1>
        <p>Empower Your Health Journey: Track, Connect, Thrive with DiabeTrack!</p>
        <!--<button class="find-doctors-btn">TRY IT OUT</button>-->
      </div>
      <div class="image-content">
        <img src="/images/image1.jpeg" alt="Image" />
      </div>
    </section>
  </template>
  
  <script>
  export default {
    name: 'HeroSection'
  }
  </script>
  
  <style scoped>
  .hero {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 2rem;
    background-color: #f0f8ff;
  }
  
  .text-content {
    max-width: 50%;
  }
  
  .text-content h1 {
    font-size: 3rem;
    margin-bottom: 1rem;
  }
  
  .text-content p {
    font-size: 1.2rem;
    margin-bottom: 2rem;
  }
  
  .find-doctors-btn {
    padding: 0.8rem 2rem;
    background-color: #007bff;
    color: #fff;
    border: none;
    border-radius: 5px;
    cursor: pointer;
  }
  
  .image-content {
    width: 300px; /* Adjust width as needed */
    height: 300px; /* Adjust height as needed */
    border-radius: 50%; /* Makes the container round */
    overflow: hidden; /* Ensures image corners are clipped to round shape */
    margin-top: 2rem; /* Spacing between text and image */
    transition: opacity 0.3s ease; /* Smooth transition for visibility */
  
    /* Optional: Background styling */
    background-color: #f0f0f0; /* Replace with your desired background color */
  }
  
  .image-content img {
    width: 100%; /* Ensures image fills the container */
    height: auto; /* Maintains aspect ratio */
    border-radius: 50%; /* Rounds the image itself */
  }
  
  @media (max-width: 768px) {
    .hero {
      flex-direction: column; /* Stack items vertically on smaller screens */
    }
  
    .text-content {
      max-width: 100%; /* Full width for text content on smaller screens */
      text-align: center; /* Center text content */
    }
  
    .image-content {
      display: none; /* Hide the image content on smaller screens */
    }
  }
  </style>
  