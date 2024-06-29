<script setup lang="ts">
import { ref, onMounted, computed } from 'vue';
import axios from 'axios';

const usersPerPage = 5;
const currentPage = ref(1);
const users = ref([]);
const editingUser = ref(null);
const feedbackMessage = ref('');

const loadFontAwesome = () => {
  const link = document.createElement('link');
  link.rel = 'stylesheet';
  link.href = 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css';
  document.head.appendChild(link);
};

onMounted(async () => {
  loadFontAwesome();
  await fetchUsers();
});

const fetchUsers = async () => {
  try {
    const response = await axios.get('/users');
    users.value = response.data;
  } catch (error) {
    feedbackMessage.value = 'Failed to fetch users';
  }
};

const startEditing = (user) => {
  editingUser.value = { ...user };
};

const cancelEditing = () => {
  editingUser.value = null;
  feedbackMessage.value = '';
};

const saveUser = async (user) => {
  try {
    const response = await axios.put(`/users/${user.id}`, { role: user.role });
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

const paginatedUsers = computed(() => {
  const startIndex = (currentPage.value - 1) * usersPerPage;
  return users.value.slice(startIndex, startIndex + usersPerPage);
});

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

const totalPages = computed(() => {
  return Math.ceil(users.value.length / usersPerPage);
});

// Get unique roles from users
const uniqueRoles = computed(() => {
  const roles = new Set(users.value.map(user => user.role));
  return Array.from(roles);
});

// Filter users based on role
const filteredUsers = (role) => {
  return users.value.filter(user => user.role === role);
};
</script>

<template>
  <div>
    <h2 class="text-2xl font-semibold mb-4 text-center">USERS</h2>

    <!-- Iterate over each unique role -->
    <div v-for="role in uniqueRoles" :key="role">
      <h4 class="mb-6 text-xl font-semibold text-black dark:text-white">{{ role === 0 ? 'Admins' : role === 1 ? 'Patients' : role === 2 ? 'Doctors' : 'Unknown Role' }}</h4>

      <div class="rounded-sm border border-stroke bg-white px-5 pt-6 pb-2.5 shadow-default dark:border-strokedark dark:bg-boxdark sm:px-7.5 xl:pb-1">
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
              <tr v-for="(user, index) in filteredUsers(role)" :key="index">
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
                  <div class="text-sm text-gray-900 dark:text-black">{{ user.role }}</div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                  <button @click="startEditing(user)" class="bg-blue-500 text-white px-3 py-1 rounded">
                    <i class="fas fa-edit"></i>
                  </button>
                  <button @click="deleteUser(user)" class="bg-red-500 text-white px-3 py-1 rounded">
                    <i class="fas fa-trash"></i>
                  </button>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>

    <!-- Pagination controls -->
    <div class="flex justify-end mt-4">
      <button @click="goToPrevPage" :disabled="currentPage === 1" class="px-3 py-1 bg-gray-200 text-gray-700 rounded mr-2" :class="{ 'opacity-50 cursor-not-allowed': currentPage === 1 }">Previous</button>
      <span class="text-gray-700">Page {{ currentPage }} of {{ totalPages }}</span>
      <button @click="goToNextPage" :disabled="currentPage === totalPages" class="px-3 py-1 bg-gray-200 text-gray-700 rounded ml-2" :class="{ 'opacity-50 cursor-not-allowed': currentPage === totalPages }">Next</button>
    </div>
  </div>
</template>
