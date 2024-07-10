<template>
    <div>
      <h2>{{ mealType }} Meal</h2>
      <form @submit.prevent="submitMeal">
        <div v-for="(item, index) in mealItems" :key="index" class="meal-item">
          <label for="meal">Meal:</label>
          <input type="text" v-model="item.food_name" @input="fetchSuggestions(index)" required />
          
          <label for="quantity">Quantity:</label>
          <input type="number" v-model="item.quantity" required />
          
          <label for="serving">Serving:</label>
          <select v-model="item.serving" required>
            <option v-for="serving in item.servings" :key="serving.measurement_description" :value="serving">
              {{ serving.measurement_description }}
            </option>
          </select>
          
          <button type="button" @click="removeMealItem(index)">Remove</button>
          
          <div v-if="suggestions[index].length">
            <ul>
              <li v-for="(suggestion, suggestionIndex) in suggestions[index]" :key="suggestionIndex" @click="selectSuggestion(index, suggestion)">
                {{ suggestion.food_name }}
              </li>
            </ul>
          </div>
        </div>
        
        <button type="button" @click="addMealItem">Add Another Item</button>
        <div>
          <label for="description">Description:</label>
          <textarea v-model="description" id="description" required></textarea>
        </div>
        <button type="submit">Add {{ mealType }} Meal</button>
      </form>
    </div>
  </template>
  
  <script setup>
  import { ref } from 'vue';
  import axios from 'axios';
  
  const props = defineProps({
    mealType: {
      type: String,
      required: true
    }
  });
  
  const emit = defineEmits(['meal-added']);
  
  const mealItems = ref([
    { food_name: '', quantity: 1, serving: null, servings: [] }
  ]);
  const description = ref('');
  const suggestions = ref([[]]);
  
  const addMealItem = () => {
    mealItems.value.push({ food_name: '', quantity: 1, serving: null, servings: [] });
    suggestions.value.push([]);
  };
  
  const removeMealItem = (index) => {
    mealItems.value.splice(index, 1);
    suggestions.value.splice(index, 1);
  };
  
  const fetchSuggestions = async (index) => {
    const item = mealItems.value[index];
    if (item.food_name.trim().length === 0) {
      suggestions.value[index] = [];
      return;
    }
    try {
      const response = await axios.get('https://trackapi.nutritionix.com/v2/search/instant', {
        headers: {
          'x-app-id': 'c64312d1',
          'x-app-key': '1e933a8b27c2d8693f51562dcb9098c5',
        },
        params: {
          query: item.food_name
        }
      });
      suggestions.value[index] = response.data.common;
    } catch (error) {
      console.error('Error fetching suggestions:', error);
      suggestions.value[index] = [];
    }
  };
  
  const fetchServings = async (index, foodId) => {
    try {
      const response = await axios.get(`https://trackapi.nutritionix.com/v2/search/item?nix_item_id=${food_name}`, {
        headers: {
          'x-app-id': 'c64312d1',
          'x-app-key': '1e933a8b27c2d8693f51562dcb9098c5',
        }
      });
      mealItems.value[index].servings = response.data.foods[0].alt_measures;
      mealItems.value[index].serving = response.data.foods[0].alt_measures[0];
    } catch (error) {
      console.error('Error fetching servings:', error);
    }
  };
  
  const selectSuggestion = (index, suggestion) => {
    mealItems.value[index].food_name = suggestion.food_name;
    suggestions.value[index] = [];
    fetchServings(index, suggestion.nix_item_id);
  };
  
  const submitMeal = () => {
    emit('meal-added', {
      type: props.mealType,
      items: mealItems.value,
      description: description.value
    });
    mealItems.value = [{ food_name: '', quantity: 1, serving: null, servings: [] }];
    description.value = '';
    suggestions.value = [[]];
  };
  </script>
  
  <style scoped>
  form {
    margin-bottom: 20px;
  }
  .meal-item {
    margin-bottom: 15px;
  }
  label {
    margin-right: 10px;
  }
  input, select, textarea {
    margin-bottom: 10px;
    display: block;
  }
  button {
    padding: 5px 10px;
    margin-right: 10px;
    background-color: #42b983;
    color: white;
    border: none;
    cursor: pointer;
  }
  button:hover {
    background-color: #358a5e;
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
  