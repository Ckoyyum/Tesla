<!-- VendorDashboard.vue -->
<template>
  <section class="p-6">
    <h2>Vendor Dashboard</h2>
    <p>Welcome, {{ user.username }}! 🛒</p>
  </section>
</template>

<script>
export default {
  name: "VendorDashboard",
  computed: {
    user() {
      return JSON.parse(localStorage.getItem("user") || "{}");
    },
  },
};
</script>
