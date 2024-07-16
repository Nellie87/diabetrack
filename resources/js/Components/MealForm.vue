<template>
  <div>
    <form @submit.prevent="submitMeal">
      <div v-for="(item, index) in mealItems" :key="index" class="meal-item">
        <label for="meal">Meal Item:</label>
        <input type="text" class="form-control" v-model="item.food_name" @input="fetchSuggestions(index)" required />
        
        <div v-if="suggestions[index].length">
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
      <div>
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
.form-control {
    border: 1px solid #ccc;
    display: block;
    width: 100%;
    height: 40px;
    padding: 0 20px;
    border-radius: 20px;
    font-family: muli-bold;
    background: 0 0;
}
.form-group {
    display: flex;
}
form {
        width: 100%;
        padding-right: 15px;
        padding-left: 15px;
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
