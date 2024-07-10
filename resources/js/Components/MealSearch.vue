<template>
    <div>
      <h2>Search for a Meal</h2>
      <input type="text" v-model="searchQuery" @input="fetchSuggestions" placeholder="Enter a meal" />
      <div v-if="suggestions.length">
        <ul>
          <li v-for="(suggestion, index) in suggestions" :key="index" @click="selectSuggestion(suggestion)">
            {{ suggestion.food_name }}
          </li>
        </ul>
      </div>
      <div v-if="selectedMeal">
        <h3>{{ selectedMeal.food_name }}</h3>
        <p><strong>Calories:</strong> {{ selectedMeal.nf_calories }}</p>
        <p><strong>Carbohydrates:</strong> {{ selectedMeal.nf_total_carbohydrate }}</p>
        <p><strong>Serving Size:</strong> {{ selectedMeal.serving_qty }} {{ selectedMeal.serving_unit }}</p>
        <p><strong>Servings:</strong></p>
        <ul>
          <li v-for="serving in selectedMeal.servings" :key="serving.measurement_description">
            {{ serving.measurement_description }}: {{ serving.serving_weight }}g
          </li>
        </ul>
      </div>
    </div>
  </template>
  
  <script setup>
  import { ref } from 'vue';
  import axios from 'axios';
  
  const searchQuery = ref('');
  const suggestions = ref([]);
  const selectedMeal = ref(null);
  
  const fetchSuggestions = async () => {
    if (searchQuery.value.trim().length === 0) {
      suggestions.value = [];
      return;
    }
    try {
      const response = await axios.get('https://trackapi.nutritionix.com/v2/search/instant', {
        headers: {
          'x-app-id': 'c64312d1',
          'x-app-key': '1e933a8b27c2d8693f51562dcb9098c5',
        },
        params: {
          query: searchQuery.value
        }
      });
      suggestions.value = response.data.common;
    } catch (error) {
      console.error('Error fetching suggestions:', error);
      suggestions.value = [];
    }
  };
  
  const fetchMealDetails = async (food_name) => {
    try {
      const response = await axios.post('https://trackapi.nutritionix.com/v2/natural/nutrients', 
      { query: food_name }, 
      {
        headers: {
          'x-app-id': 'c64312d1',
          'x-app-key': '1e933a8b27c2d8693f51562dcb9098c5',
          'Content-Type': 'application/json'
        }
      });
      if (response.data.foods.length > 0) {
        selectedMeal.value = {
          ...response.data.foods[0],
          servings: response.data.foods[0].alt_measures
        };
      } else {
        console.error('No food item found for the given name.');
        selectedMeal.value = null;
      }
    } catch (error) {
      console.error('Error fetching meal details:', error);
      selectedMeal.value = null;
    }
  };
  
  const selectSuggestion = (suggestion) => {
    fetchMealDetails(suggestion.food_name);
  };
  </script>
  
  <style scoped>
  input {
    display: block;
    margin-bottom: 10px;
  }
  ul {
    list-style-type: none;
    padding: 0;
    margin: 0;
  }
  li {
    cursor: pointer;
    padding: 5px;
    background-color: #f4f4f4;
    border-bottom: 1px solid #ddd;
  }
  li:hover {
    background-color: #e9e9e9;
  }
  </style>
  