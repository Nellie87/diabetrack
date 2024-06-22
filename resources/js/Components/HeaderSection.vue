<template>
    <header class="header">
      <img src="/images/logo2.png" alt="Logo" class="logo" />
      <nav class="nav">
        <a href="#">About</a>
        <a href="#">Services</a>
        <a href="#">Doctors</a>
        <a href="#">Blog</a>
        <a href="#">Contact</a>
        
      </nav>
     <!-- <div>
      <button class="appointment-btn">Appointment</button>
      </div> -->
      
    </header>
  </template>
  
  <script>
  export default {
    name: 'HeaderSection',
    data() {
      return {
        isLoggedIn: false, // Replace with your actual login state logic
        canRegister: true // Replace with your actual registration availability logic
      };
    },
    methods: {
      logout() {
        // Add logout functionality here
        console.log('Logged out'); // Example: Just logging out for demonstration
        this.isLoggedIn = false;
      }
    }
  };
  </script>
  
  <style scoped>
  .header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 1rem 2rem;
    background-color: #f0f8ff;
  }
  
  .logo {
    max-height: 80px;
  }
  
  .nav a {
    margin: 0 1rem;
    color: #000;
    text-decoration: none;
  }
  
  .btn {
    padding: 0.5rem 1rem;
    background-color: #007bff;
    color: #fff;
    border: none;
    border-radius: 5px;
    cursor: pointer;
  }
  
  .appointment-btn {
    padding: 0.5rem 1rem;
    background-color: #007bff;
    color: #fff;
    border: none;
    border-radius: 5px;
    cursor: pointer;
  }
  </style>
  