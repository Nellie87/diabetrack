<script setup lang="ts">
import { ref, onMounted, computed } from 'vue';
import axios from 'axios';

const usersPerPage = 5; // Number of users per page
const currentPage = ref(1); // Current page of pagination
const users = ref([]);
const editingUser = ref(null); // To track which user is being edited
const feedbackMessage = ref(''); // To provide feedback to the user

// Dynamically load Font Awesome
const loadFontAwesome = () => {
  const link = document.createElement('link');
  link.rel = 'stylesheet';
  link.href = 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css';
  document.head.appendChild(link);
};

onMounted(async () => {
  loadFontAwesome(); // Load Font Awesome when the component is mounted
  await fetchUsers(); // Fetch users initially
});

const fetchUsers = async () => {
  try {
    const response = await axios.get('/users');
    console.log(response.data); // Debug the response
    users.value = response.data;
  } catch (error) {
    console.error('Error fetching users:', error);
  }
};

const startEditing = (user) => {
  editingUser.value = { ...user }; // Create a copy of the user object to edit
};

const cancelEditing = () => {
  editingUser.value = null;
  feedbackMessage.value = ''; // Clear feedback message
};

const saveUser = async (user) => {
  try {
    const response = await axios.put(`/users/${user.id}`, { role: user.role });
    console.log(response.data);
    // Update the local users array
    const index = users.value.findIndex((u) => u.id === user.id);
    if (index !== -1) {
      users.value[index] = response.data;
    }
    editingUser.value = null;
    feedbackMessage.value = 'User updated successfully!';
  } catch (error) {
    console.error('Error updating user:', error.response ? error.response.data : error.message);
    feedbackMessage.value = `Error updating user: ${error.response ? error.response.data.message : error.message}`;
  }
};

const deleteUser = async (user) => {
  if (!confirm('Are you sure you want to delete this user?')) return;
  try {
    await axios.delete(`/users/${user.id}`);
    users.value = users.value.filter((u) => u.id !== user.id);
    feedbackMessage.value = 'User deleted successfully!';
  } catch (error) {
    console.error('Error deleting user:', error);
    feedbackMessage.value = 'Error deleting user.';
  }
};

// Computed property to paginate users
const paginatedUsers = computed(() => {
  const startIndex = (currentPage.value - 1) * usersPerPage;
  return users.value.slice(startIndex, startIndex + usersPerPage);
});

// Methods to navigate between pages
const goToNextPage = () => {
  if (currentPage.value < totalPages.value) {
    currentPage.value++;
  }
};

const goToPrevPage = () => {
  if (currentPage.value > 1) {
    currentPage.value--;
  }
};

// Computed property to calculate total pages
const totalPages = computed(() => {
  return Math.ceil(users.value.length / usersPerPage);
});


</script>
<template>
  <div>
    <h2 class="text-2xl font-semibold mb-4 text-center">USERS</h2>
    <div class="rounded-sm border border-stroke bg-white px-5 pt-6 pb-2.5 shadow-default dark:border-strokedark dark:bg-boxdark sm:px-7.5 xl:pb-1">
      <h4 class="mb-6 text-xl font-semibold text-black dark:text-white">User Table</h4>

      <div v-if="feedbackMessage" class="mb-4 p-2 bg-green-100 text-green-800 rounded">
        {{ feedbackMessage }}
      </div>

      <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-600">
          <thead class="bg-gray-2 dark:bg-meta-4">
            <tr>
              <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
              <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
              <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
              <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Created At</th>
              <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Role</th>
              <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
            </tr>
          </thead>
          <tbody class="bg-white divide-y divide-gray-200 dark:divide-gray-600">
            <tr v-for="(user, index) in paginatedUsers" :key="index">
              <td class="px-6 py-4 whitespace-nowrap">
                <div class="text-sm text-gray-900 dark:text-black">{{ user.id }}</div>
              </td>
              <td class="px-6 py-4 whitespace-nowrap">
                <div class="text-sm text-gray-900 dark:text-black">{{ user.name }}</div>
              </td>
              <td class="px-6 py-4 whitespace-nowrap">
                <div class="text-sm text-gray-900 dark:text-black">{{ user.email }}</div>
              </td>
              <td class="px-6 py-4 whitespace-nowrap">
                <div class="text-sm text-gray-900 dark:text-black">{{ user.created_at }}</div>
              </td>
              <td class="px-6 py-4 whitespace-nowrap">
                <div v-if="editingUser && editingUser.id === user.id">
                  <input type="text" v-model="editingUser.role" class="border rounded p-1 text-sm dark:bg-gray-800 dark:text-white" :id="'role-' + user.id" name="role"/>
                </div>
                <div v-else>
                  <div class="text-sm text-gray-900 dark:text-black">{{ user.role }}</div>
                </div>
              </td>
              <td class="px-6 py-4 whitespace-nowrap">
                <div v-if="editingUser && editingUser.id === user.id">
                  <button @click="saveUser(editingUser)" class="bg-green-500 text-white px-3 py-1 rounded">
                    <i class="fas fa-save"></i>
                  </button>
                  <button @click="cancelEditing" class="bg-red-500 text-white px-3 py-1 rounded">
                    <i class="fas fa-times"></i>
                  </button>
                </div>
                <div v-else>
                  <button @click="startEditing(user)" class="bg-blue-500 text-white px-3 py-1 rounded">
                    <i class="fas fa-edit"></i>
                  </button>
                  <button @click="deleteUser(user)" class="bg-red-500 text-white px-3 py-1 rounded">
                    <i class="fas fa-trash"></i>
                  </button>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- Pagination controls -->
      <div class="flex justify-end mt-4">
        <button @click="goToPrevPage" :disabled="currentPage === 1" class="px-3 py-1 bg-gray-200 text-gray-700 rounded mr-2" :class="{ 'opacity-50 cursor-not-allowed': currentPage === 1 }">Previous</button>
        <span class="text-gray-700">Page {{ currentPage }} of {{ totalPages }}</span>
        <button @click="goToNextPage" :disabled="currentPage === totalPages" class="px-3 py-1 bg-gray-200 text-gray-700 rounded ml-2" :class="{ 'opacity-50 cursor-not-allowed': currentPage === totalPages }">Next</button>
      </div>
    </div>
  </div>
</template>
