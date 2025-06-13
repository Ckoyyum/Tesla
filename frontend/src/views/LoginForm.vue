<template>
  <div
    class="min-h-screen flex items-center justify-center bg-gradient-to-br from-blue-800 to-purple-900"
  >
    <div
      class="bg-white text-gray-800 p-8 rounded-xl shadow-xl w-full max-w-md"
    >
      <h2 class="text-3xl font-bold text-center mb-6 text-blue-800">
        Login to ESM
      </h2>
      <form @submit.prevent="login" class="space-y-5">
        <div>
          <label for="email" class="block mb-1 font-medium">Email</label>
          <input
            v-model="email"
            id="email"
            type="email"
            placeholder="Enter your email"
            required
            class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
          />
        </div>

        <div>
          <label for="password" class="block mb-1 font-medium">Password</label>
          <input
            v-model="password"
            id="password"
            type="password"
            placeholder="Enter your password"
            required
            class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
          />
        </div>

        <button
          type="submit"
          class="w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 rounded-md transition duration-200"
        >
          Login
        </button>

        <p v-if="error" class="text-red-600 text-center font-medium mt-2">
          {{ error }}
        </p>
      </form>
    </div>
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

        const { token, user } = res.data;
        localStorage.setItem("token", token);
        localStorage.setItem("user", JSON.stringify(user));

        this.$root.$emit("auth-changed");

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
