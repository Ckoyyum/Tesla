<template>
  <div>
    <h2>Register</h2>
    <form @submit.prevent="register">
      <input v-model="username" placeholder="Username" />
      <input v-model="email" type="email" placeholder="Email" />
      <input v-model="password" type="password" placeholder="Password" />
      <select v-model="role">
        <option value="">Select Role</option>
        <option value="organizer">Organizer</option>
        <option value="vendor">Vendor</option>
        <option value="venue_owner">Venue Owner</option>
      </select>
      <button type="submit">Register</button>
    </form>
    <p>{{ error }}</p>
  </div>
</template>

<script>
import axios from "axios";

export default {
  data() {
    return {
      username: "",
      email: "",
      password: "",
      role: "",
      error: "",
    };
  },
  methods: {
    async register() {
      try {
        const res = await axios.post("http://localhost:8000/register", {
          username: this.username,
          email: this.email,
          password: this.password,
          role: this.role,
        });
        localStorage.setItem("token", res.data.token);
        localStorage.setItem("user", JSON.stringify(res.data.user));
        this.$router.push("/");
      } catch (err) {
        this.error = err.response?.data?.message || "Registration failed";
      }
    },
  },
};
</script>
