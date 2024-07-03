<template>
    <div>
      <div class="progress-container">
        <div class="progress-bar" :style="{ width: progress + '%' }"></div>
      </div>
      <p>{{ progress }}%</p>
      <button @click="incrementProgress">Medicine 1</button>
    </div>
  </template>
  
  <script>
  export default {
    name: 'ProgressBar',
    data() {
      return {
        progress: 0
      };
    },
    methods: {
      incrementProgress() {
        if (this.progress < 100) {
          this.progress += 10; // Increase progress by 10% for each action
        }
      }
    }
  };
  </script>
  
  <style scoped>
  .progress-container {
    width: 100%;
    background-color: #f3f3f3;
    border-radius: 25px;
    overflow: hidden;
    margin: 20px 0;
  }
  
  .progress-bar {
    height: 30px;
    background-color: #4caf50;
    border-radius: 25px 0 0 25px;
    transition: width 0.5s ease;
  }
  
  button {
    margin-top: 20px;
    padding: 10px 20px;
    font-size: 16px;
    cursor: pointer;
  }
  </style>
  