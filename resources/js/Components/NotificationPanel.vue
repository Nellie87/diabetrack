<template>
  <div class="notifications-sidenav" :class="{ active: showSideNav }">
    <div class="sidenav">
      <div class="sidenav-header">
        <h3>Notifications</h3>
        <button @click="toggleSideNav">Close</button>
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
        // Add more notifications as needed
      ],
      showSideNav: true
    };
  },
  methods: {
    toggleSideNav() {
      this.showSideNav = !this.showSideNav;
    }
  }
};
</script>

<style scoped>
.notifications-sidenav {
  position: fixed;
  top: 0;
  right: 0;
  height: 100%;
  width: 250px; /* Adjust width as needed */
  background-color: #f1f1f1;
  overflow-y: auto;
  z-index: 1000; /* Ensure it appears above other content */
  box-shadow: -2px 0 5px rgba(0, 0, 0, 0.1); /* Optional: Add shadow for better visibility */
  transform: translateX(100%); /* Initially translate out of view */
  transition: transform 0.3s ease; /* Smooth transition */
}

.notifications-sidenav.active {
  transform: translateX(0); /* Slide in from the right */
}

.sidenav {
  height: 100%;
  overflow-y: auto;
}

.sidenav-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
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
