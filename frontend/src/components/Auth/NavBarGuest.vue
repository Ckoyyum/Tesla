<template>
  <nav class="navbar">
    <div class="navbar-container">
      <h1 class="navbar-brand">ESM</h1>
      <ul class="navbar-links">
        <li><router-link to="/">Home</router-link></li>
        <li><router-link to="/login">Sign In</router-link></li>
        <li><router-link to="/register">Register</router-link></li>
        <li><router-link to="/about">About Us</router-link></li>
      </ul>
    </div>
  </nav>
</template>

<script>
export default {
  name: "NavBarGuest",
};
</script>

<style scoped>
.navbar {
  background-color: #2c3e50; /* Darker blue-grey background */
  padding: 15px 0;
  box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2); /* Subtle shadow for depth */
}

.navbar-container {
  display: flex;
  justify-content: space-between; /* Puts brand on left, links on right */
  align-items: center; /* Vertically aligns items */
  max-width: 1200px; /* Max width for content */
  margin: 0 auto; /* Centers the container */
  padding: 0 20px; /* Horizontal padding */
}

.navbar-brand {
  color: #ecf0f1; /* Light grey for the brand name */
  margin: 0;
  font-size: 1.8em;
  font-weight: bold;
  letter-spacing: 1px;
}

.navbar-links {
  list-style: none; /* Remove bullet points */
  margin: 0;
  padding: 0;
  display: flex; /* Make list items horizontal */
  align-items: center; /* Align items vertically in the center */
}

.navbar-links li {
  margin-left: 25px; /* Space between links */
}

.navbar-links a {
  color: #ecf0f1; /* Light grey for links */
  text-decoration: none; /* Remove underline */
  font-weight: 500; /* Medium font weight */
  font-size: 1.1em;
  transition: color 0.3s ease; /* Smooth color transition on hover */
}

.navbar-links a:hover,
.navbar-links a.router-link-exact-active {
  color: #3498db; /* Blue on hover or active link */
}

.logout-button {
  background-color: #e74c3c; /* Red color for logout button */
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
  background-color: #c0392b; /* Darker red on hover */
}

/* Responsive considerations (optional but good practice) */
@media (max-width: 768px) {
  .navbar-container {
    flex-direction: column; /* Stack brand and links vertically on small screens */
    align-items: flex-start;
  }

  .navbar-brand {
    margin-bottom: 15px;
  }

  .navbar-links {
    flex-direction: column; /* Stack links vertically */
    width: 100%;
    align-items: flex-start;
  }

  .navbar-links li {
    margin-left: 0;
    margin-bottom: 10px; /* Space between stacked links */
    width: 100%;
  }

  .navbar-links a,
  .logout-button {
    display: block; /* Make links and button take full width */
    padding: 10px 0;
    text-align: left;
  }
}
</style>
