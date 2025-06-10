<template>
  <div
    id="app"
    class="min-h-screen flex flex-col bg-gradient-to-br from-blue-800 to-purple-900 text-white"
  >
    <!-- Conditional Navbar -->
    <component :is="navbarComponent" />

    <div class="flex-1 w-full overflow-y-auto py-12 sm:py-16">
      <router-view />
    </div>
  </div>
</template>

<script>
import NavBarGuest from "./components/Auth/NavBarGuest.vue";
import NavBarUser from "./components/Auth/NavBarUser.vue";

export default {
  name: "App",
  components: {
    NavBarGuest,
    NavBarUser,
  },
  computed: {
    isAuthenticated() {
      return !!localStorage.getItem("token");
    },
    navbarComponent() {
      return this.isAuthenticated ? "NavBarUser" : "NavBarGuest";
    },
  },
};
</script>
