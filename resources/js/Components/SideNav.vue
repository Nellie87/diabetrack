
<template>
    <div class="sidenav">
      <ul>
        <li v-for="link in links" :key="link.text">
          <a :href="link.url">{{ link.text }}</a>
        </li>
      </ul>
    </div>
  </template>
  
  <script>
  export default {
    name: 'SideNav',
    props: {
      links: {
        type: Array,
        required: true,
        default: () => []
      }
    }
  };
  </script>
  
  <style scoped>
  .sidenav {
    width: 200px;
    position: relative;
    left: 0;
    top: 0;
    background-color: #e3e3e3;
    padding-top: 20px;
  }
  
  .sidenav ul {
    list-style-type: none;
    padding: 0;
  }
  
  .sidenav ul li {
    padding: 8px;
    text-align: center;
  }
  
  .sidenav ul li a {
    color: white;
    text-decoration: none;
    display: block;
  }
  
  .sidenav ul li a:hover {
    background-color: #575757;
  }
  </style>
  