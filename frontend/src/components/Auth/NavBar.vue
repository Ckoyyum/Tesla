<template>
  <nav class="navbar">
    <div class="navbar-container">
      <h1 class="navbar-brand">ESM</h1>
      <ul class="navbar-links">
        <li>
          <router-link to="/">Home</router-link>
        </li>
        <li v-if="!isAuthenticated">
          <router-link to="/login">Sign In</router-link>
        </li>
        <li v-if="!isAuthenticated">
          <router-link to="/register">Register</router-link>
        </li>
        <li>
          <router-link to="/about">About Us</router-link>
        </li>
        <li v-if="isAuthenticated">
          <router-link to="/dashboard">Dashboard</router-link>
        </li>
        <li v-if="isAuthenticated">
          <button @click="logout" class="logout-button">Logout</button>
        </li>
      </ul>
    </div>
  </nav>
</template>

<script>
export default {
  name: "NavBar",
  computed: {
    isAuthenticated() {
      return !!localStorage.getItem("token");
    },
  },
  methods: {
    logout() {
      localStorage.removeItem("token");
      localStorage.removeItem("user");
      this.$router.replace("/login");
    },
  },
};
</script>

<style scoped>
/* Your existing styles go here */
</style>
