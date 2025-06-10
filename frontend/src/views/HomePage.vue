<template>
  <div class="some-page">
    <div class="container">
      <div class="card">
        <h2>This is a placeholder page</h2>
        <p>
          Welcome to your new page! You can add your content, components, and
          more here.
        </p>
        <button class="button">Learn More</button>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: "SomePage",
};
</script>

<style scoped>
.some-page {
  display: flex;
  justify-content: center;
  align-items: center;
  min-height: 100vh; /* Ensures the page takes at least the full viewport height */
  background-color: #f0f2f5; /* Light background color for the page */
  padding: 20px; /* Add some padding around the content */
  box-sizing: border-box; /* Include padding in the element's total width and height */
}

.container {
  max-width: 800px; /* Limit the maximum width of the content */
  width: 100%; /* Ensure it takes full width up to max-width */
  margin: 0 auto; /* Center the container horizontally */
  padding: 20px;
  background-color: #ffffff; /* White background for the container */
  border-radius: 8px; /* Slightly rounded corners for the container */
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1); /* Subtle shadow for depth */
}

.card {
  text-align: center; /* Center the text within the card */
  padding: 30px;
}

h2 {
  color: #333;
  margin-bottom: 15px;
  font-size: 2em; /* Larger heading */
}

p {
  color: #666;
  line-height: 1.6;
  margin-bottom: 25px;
}

.button {
  background-color: #007bff; /* Primary blue color for the button */
  color: white;
  padding: 12px 25px;
  border: none;
  border-radius: 5px;
  cursor: pointer;
  font-size: 1.1em;
  transition: background-color 0.3s ease; /* Smooth transition on hover */
}

.button:hover {
  background-color: #0056b3; /* Darker blue on hover */
}
</style>
