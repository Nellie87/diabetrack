<template>
    <div class="notifications-sidenav">
      <div class="sidenav">
        <div class="sidenav-header">
          <h3>Notifications</h3>
        </div>
        <ul class="notification-list">
          <li v-for="(notification, index) in notifications" :key="index">
            {{ notification.message }}
          </li>
        </ul>
      </div>
    </div>
  </template>
  
  <script>
  export default {
    data() {
      return {
        notifications: [
          { id: 1, message: 'Notification 1' },
          { id: 2, message: 'Notification 2' },
          { id: 3, message: 'Notification 3' },
        ]
      };
    }
  };
  </script>
  
  <style scoped>
  .notifications-sidenav {
    position: relative;
    top: 0;
    right: -80%;
    height: 100%;
    width: 250px; /* Adjust width as needed */
    background-color: #f1f1f1;
    overflow-y: auto;
    box-shadow: -2px 0 5px rgba(0, 0, 0, 0); /* Optional: Add shadow for better visibility */
  }
  
  .sidenav-header {
    padding: 16px;
    text-align: center;
    background-color: #333;
    color: white;
  }
  
  .notification-list {
    list-style-type: none;
    padding: 0;
    margin: 0;
  }
  
  .notification-list li {
    padding: 10px;
    border-bottom: 1px solid #ddd;
  }
  
  .notification-list li:last-child {
    border-bottom: none;
  }
  </style>
  
