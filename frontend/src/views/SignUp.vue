<template>
  <!-- <navbar btn-background="bg-gradient-primary" /> -->
  <div
    class="pt-5 m-3 page-header align-items-start min-vh-50 pb-11 border-radius-lg"
    :style="{
      backgroundImage:
        'url(' + require('@/assets/img/curved-images/curved6.jpg') + ')',
    }"
  > 
    <span class="mask bg-gradient-dark opacity-6"></span>
    <div class="container">
      <div class="row justify-content-center">
        <div class="mx-auto text-center col-lg-5">
          <h1 class="mt-5 mb-2 text-white">Welcome!</h1>
          <!-- <p class="text-white text-lead">
            Use these awesome forms to login or create new account in your
            project for free.
          </p> -->
        </div>
      </div>
    </div>
  </div>
  <div class="container">
    <div class="row mt-lg-n10 mt-md-n11 mt-n10 justify-content-center">
      <div class="mx-auto col-xl-4 col-lg-5 col-md-7">
        <div class="card z-index-0">
          <div class="pt-4 text-center card-header">
            <h5>Register </h5>
          </div>
          <div class="card-body">
            <form role="form" @submit.prevent="register">
              <div class="mb-3">
                <soft-input
                  id="username"
                  v-model="username"
                  type="text"
                  placeholder="Username"
                  aria-label="Username"
                />
              </div>
              <div class="mb-3">
                <soft-input
                  id="email"
                  v-model="email"
                  type="email"
                  placeholder="Email"
                  aria-label="Email"
                />
              </div>
              <div class="mb-3">
                <soft-input
                  id="password"
                  v-model="password"
                  type="password"
                  placeholder="Password"
                  aria-label="Password"
                />
              </div>
              <div class="mb-3">
                <select v-model="role" class="form-control" required>
                  <option disabled value="">Select Role</option>
                  <option value="organizer">Organizer</option>
                  <option value="vendor">Vendor</option>
                  <option value="venue_owner">Venue Owner</option>
                </select>
              </div>
              <!-- <soft-checkbox
                id="flexCheckDefault"
                name="flexCheckDefault"
                class="font-weight-light"
                checked
              >
                I agree to the
                <a href="javascript:;" class="text-dark font-weight-bolder">
                  Terms and Conditions
                </a>
              </soft-checkbox> -->

              <div class="text-center">
                <soft-button
                  color="dark"
                  full-width
                  variant="gradient"
                  class="my-4 mb-2"
                  type="submit"
                >
                  Sign up
                </soft-button>
              </div>

              <p class="text-sm mt-3 mb-0">
                Already have an account?
                <router-link
                  :to="{ name: 'Sign In' }"
                  class="text-dark font-weight-bolder"
                >
                  Sign in
                </router-link>
              </p>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import api from "@/utils/api";
// import Navbar from "@/examples/PageLayout/Navbar.vue";
// import AppFooter from "@/examples/PageLayout/Footer.vue";
import SoftInput from "@/components/SoftInput.vue";
// import SoftCheckbox from "@/components/SoftCheckbox.vue";
import SoftButton from "@/components/SoftButton.vue";

import { mapMutations } from "vuex";

export default {

   data() {
    return {
      role: '' // must be empty string to match the placeholder option
    };
  },
  name: "SignupBasic",
  components: {
    // Navbar,
    // AppFooter,
    SoftInput,
    // SoftCheckbox,
    SoftButton,
  },
  created() {
    this.toggleEveryDisplay();
    this.toggleHideConfig();
  },
  beforeUnmount() {
    this.toggleEveryDisplay();
    this.toggleHideConfig();
  },
  methods: {
    ...mapMutations(["toggleEveryDisplay", "toggleHideConfig"]),

    async register() {
      console.log("Registering...");
      console.log("Username:", this.username);
      console.log("Email:", this.email);
      console.log("Password:", this.password);
      console.log("Role:", this.role);

      try {
        const res = await api.post("/api/register", {
          username: this.username,
          email: this.email,
          password: this.password,
          role: this.role,
        });

        const { token, user } = res.data;
        localStorage.setItem("token", token);
        localStorage.setItem("user", JSON.stringify(user));
        // this.$router.push("/");

        // Redirect based on role
        const redirectMap = {
          organizer: "/organizer-dashboard",
          vendor: "/vendor-dashboard",
          venue_owner: "/venue-owner-dashboard",
        };
        this.$router.push(redirectMap[user.role] || "/");
      } catch (err) {
        this.error =
          err.response?.data?.message || "Registration failed. Please try again.";
      }
    },
  },
};
</script>
