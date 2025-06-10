<template>
  <div>
    <h2>Login</h2>
    <form @submit.prevent="login">
      <input v-model="email" type="email" placeholder="Email" required />
      <input
        v-model="password"
        type="password"
        placeholder="Password"
        required
      />
      <button type="submit">Login</button>
    </form>
    <p style="color: red">{{ error }}</p>
  </div>
</template>

<script>
import axios from "axios";

export default {
  data() {
    return {
      email: "",
      password: "",
      error: "",
    };
  },
  methods: {
    async login() {
      try {
        const res = await axios.post("http://localhost:8000/login", {
          email: this.email,
          password: this.password,
        });

        // Save token and user info
        const { token, user } = res.data;
        localStorage.setItem("token", token);
        localStorage.setItem("user", JSON.stringify(user));

        this.$root.$emit("auth-changed");

        // Redirect based on user role
        if (user.role === "organizer") {
          this.$router.push("/organizer-dashboard");
        } else if (user.role === "vendor") {
          this.$router.push("/vendor-dashboard");
        } else if (user.role === "venue_owner") {
          this.$router.push("/venue-owner-dashboard");
        } else {
          this.error = "Unknown user role.";
        }
      } catch (err) {
        this.error = err.response?.data?.message || "Login failed.";
      }
    },
  },
};
</script>

<style scoped>
form {
  display: flex;
  flex-direction: column;
  max-width: 300px;
}
input {
  margin: 5px 0;
  padding: 8px;
}
button {
  margin-top: 10px;
  padding: 8px;
}
</style>
