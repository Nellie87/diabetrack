<template>
    <form @submit.prevent="submitForm">
      <div>
        <label for="name">Name:</label>
        <input type="text" id="name" v-model="form.name" />
      </div>
      <div>
        <label for="email">Email:</label>
        <input type="email" id="email" v-model="form.email" />
      </div>
      <div>
        <label for="message">Message:</label>
        <textarea id="message" v-model="form.message"></textarea>
      </div>
      <button type="submit">Submit</button>
    </form>
  </template>
  
  <script>
  export default {
    data() {
      return {
        form: {
          name: '',
          email: '',
          message: ''
        }
      };
    },
    methods: {
      submitForm() {
        axios.post('/api/submit-form', this.form)
          .then(response => {
            console.log('Form submitted:', response.data);
          })
          .catch(error => {
            console.error('There was an error submitting the form!', error);
          });
      }
    }
  };
  </script>
  
  <style scoped>
  /* Add your styles here */
  </style>
  