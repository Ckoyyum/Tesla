import { createRouter, createWebHistory } from "vue-router";
import Home from "./views/HomePage.vue";
import Login from "./views/LoginForm.vue";
import Register from "./views/RegisterForm.vue";
import About from "./views/AboutPage.vue";
import OrganizerDashboard from "./views/OrganizerDashboard.vue";
import VendorDashboard from "./views/VendorDashboard.vue";
import VenueOwnerDashboard from "./views/VenueOwnerDashboard.vue";

const routes = [
  { path: "/", component: Home },
  { path: "/login", component: Login },
  { path: "/register", component: Register },
  { path: "/about", component: About },
  { path: "/organizer-dashboard", component: OrganizerDashboard },
  { path: "/vendor-dashboard", component: VendorDashboard },
  { path: "/venue-owner-dashboard", component: VenueOwnerDashboard },
];

export const router = createRouter({
  history: createWebHistory(),
  routes,
});
