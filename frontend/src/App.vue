<template>
  <div
    id="app"
    class="min-h-screen flex flex-col bg-gradient-to-br from-blue-800 to-purple-900 text-white"
  >
    <!-- Unified Navbar -->
    <NavBar />

    <div class="flex-1 w-full overflow-y-auto py-12 sm:py-16">
      <router-view />
    </div>
  </div>
</template>

<script>
import NavBar from "./components/Auth/NavBar.vue";

export default {
  name: "App",
  components: {
    NavBar,
  },
};
</script>
