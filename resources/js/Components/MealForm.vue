<template>
  <div>
    <form @submit.prevent="submitMeal">
      <div v-for="(item, index) in mealItems" :key="index" class="meal-item">
        <label for="meal">Meal Item:</label>
        <input type="text" class="form-control" v-model="item.food_name" @input="fetchSuggestions(index)" required />
        
        <div v-if="suggestions[index].length" class="suggestions">
          <ul>
            <li v-for="(suggestion, suggestionIndex) in suggestions[index]" :key="suggestionIndex" @click="selectSuggestion(index, suggestion)">
              {{ suggestion.food_name }}
            </li>
          </ul>
        </div>
       
        <label for="serving">Serving:</label>
        <select v-model="item.selectedServing" required class="form-control">
          <option v-for="serving in item.servings" :key="serving.measure" :value="serving">
            {{ serving.measure }}
          </option>
        </select>

        <label for="quantity">Quantity:</label>
        <input type="number" class="form-control" v-model="item.quantity" required />
                
        <button type="button" @click="removeMealItem(index)">Remove</button>
      </div>
      
      <button type="button" @click="addMealItem">Add Another Item</button>
      
      <div class="mb-4">
        <label for="description">Description:</label>
        <textarea v-model="description" class="form-control" id="description" required></textarea>
      </div>

      <div class="mb-4">
        <label for="Date" class="block text-gray-700">Datetime:</label>
        <input id="Date"  type="datetime-local" class="form-control mt-1 block w-full" v-model="Date" />
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
  { food_name: '', quantity: 1, selectedServing: null, servings: [] }
]);
const description = ref('');
const Date = ref('');
const suggestions = ref([[]]);

const addMealItem = () => {
  mealItems.value.push({ food_name: '', quantity: 1, selectedServing: null, servings: [] });
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
        'x-app-id': '481b79d4',
        'x-app-key': '2474d00427ff9e47bc9d0d13f22d78a6',
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

const fetchServings = async (index, food_name) => {
  try {
    const response = await axios.post('https://trackapi.nutritionix.com/v2/natural/nutrients', 
    { query: food_name },
    {
      headers: {
        'x-app-id': '481b79d4',
        'x-app-key': '2474d00427ff9e47bc9d0d13f22d78a6',
        'Content-Type': 'application/json'
      }
    });
    if (response.data.foods.length > 0) {
      mealItems.value[index].servings = response.data.foods[0].alt_measures;
      mealItems.value[index].selectedServing = response.data.foods[0].alt_measures[0];
    } else {
      console.error('No food item found for the given name.');
    }
  } catch (error) {
    console.error('Error fetching servings:', error);
  }
};

const selectSuggestion = (index, suggestion) => {
  mealItems.value[index].food_name = suggestion.food_name;
  suggestions.value[index] = [];
  fetchServings(index, suggestion.food_name);
};

const submitMeal = async () => {
  const mealData = {
    meal_type: props.mealType,
    description: description.value,
    items: mealItems.value.map(item => ({
      food_name: item.food_name,
      quantity: item.quantity,
      serving: item.selectedServing,
    })),
    Date: Date.value,
  };

  try {
    const response = await axios.post('/meals', mealData);
    if (response.data.success) {
      alert('Form submitted successfully.');
    mealItems.value = [{ food_name: '', quantity: 1, selectedServing: null, servings: [] }];
    description.value = '';
    Date.value = '';
    suggestions.value = [[]];
    window.location.reload();
  } else {
    alert('Submission failed. Please try again.')
  }
} catch (error) {
        console.error('Error submitting form:', error);
        alert('An error occurred while submitting the form. Please try again.',error);
    }
};
</script>

<style scoped>
  .meal-item {
    display: flex;
    flex-wrap: wrap;
    align-items: center;
    gap: 1rem;
    margin-bottom: 1rem;
  }

  .meal-item > div, 
  .meal-item > label, 
  .meal-item > input, 
  .meal-item > select, 
  .meal-item > button {
    flex: 1;
    min-width: 100px;
  }

  .form-control {
    width: 100%;
    padding: 0.5rem;
    border: 1px solid #ccc;
    border-radius: 4px;
  }

  .form-control:focus {
    border-color: #007bff;
    outline: none;
  }

  .suggestions {
    position: absolute;
    background: white;
    border: 1px solid #ccc;
    max-height: 150px;
    overflow-y: auto;
    width: 100%;
    z-index: 10;
  }

  .suggestions ul {
    list-style: none;
    padding: 0;
    margin: 0;
  }

  .suggestions li {
    padding: 0.5rem;
    cursor: pointer;
  }

  .suggestions li:hover {
    background-color: #f0f0f0;
  }

  .mb-4 {
    margin-bottom: 1rem;
  }

  button {
    padding: 0.5rem 1rem;
    background-color: #007bff;
    color: white;
    border: none;
    border-radius: 4px;
    cursor: pointer;
  }

  button[type="button"] {
    background-color: #6c757d;
  }

  button:hover {
    background-color: #0056b3;
  }

  button[type="button"]:hover {
    background-color: #5a6268;
  }
</style>



