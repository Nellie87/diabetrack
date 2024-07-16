
<template>
    <div class="sidenav">
      <div class="header">Diabetrack</div>
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
  .header{
   border-bottom: #161616;
   padding-bottom: 3rem
  }
  .sidenav {
    width: 200px;
    position: relative;
    left: 0;
    top: 0;
    background-color: #f3f4f9;
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
    color: rgb(11, 10, 10);
    text-decoration: none;
    display: block;
  }
  
  .sidenav ul li a:hover {
    background-color: #c7c7c7;
  }
  </style>
  