<template>
  <nav class="navbar">
    <div class="navbar-container">
      <h1 class="navbar-brand">ESM</h1>
      <ul class="navbar-links">
        <li><router-link to="/dashboard">Dashboard</router-link></li>
        <li>
          <button @click="logout" class="logout-button">Logout</button>
        </li>
      </ul>
    </div>
  </nav>
</template>

<script>
export default {
  name: "NavBarUser",
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
.navbar {
  background-color: #2c3e50;
  padding: 15px 0;
  box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);
}

.navbar-container {
  display: flex;
  justify-content: space-between;
  align-items: center;
  max-width: 1200px;
  margin: 0 auto;
  padding: 0 20px;
}

.navbar-brand {
  color: #ecf0f1;
  margin: 0;
  font-size: 1.8em;
  font-weight: bold;
  letter-spacing: 1px;
}

.navbar-links {
  list-style: none;
  margin: 0;
  padding: 0;
  display: flex;
  align-items: center;
}

.navbar-links li {
  margin-left: 25px;
}

.navbar-links a {
  color: #ecf0f1;
  text-decoration: none;
  font-weight: 500;
  font-size: 1.1em;
  transition: color 0.3s ease;
}

.navbar-links a:hover,
.navbar-links a.router-link-exact-active {
  color: #3498db;
}

.logout-button {
  background-color: #e74c3c;
  color: white;
  padding: 8px 15px;
  border: none;
  border-radius: 5px;
  cursor: pointer;
  font-size: 1em;
  font-weight: 500;
  transition: background-color 0.3s ease;
}

.logout-button:hover {
  background-color: #c0392b;
}

@media (max-width: 768px) {
  .navbar-container {
    flex-direction: column;
    align-items: flex-start;
  }

  .navbar-brand {
    margin-bottom: 15px;
  }

  .navbar-links {
    flex-direction: column;
    width: 100%;
    align-items: flex-start;
  }

  .navbar-links li {
    margin-left: 0;
    margin-bottom: 10px;
    width: 100%;
  }

  .navbar-links a,
  .logout-button {
    display: block;
    padding: 10px 0;
    text-align: left;
  }
}
</style>
