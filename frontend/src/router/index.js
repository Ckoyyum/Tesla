import { createRouter, createWebHistory } from "vue-router";
import Dashboard from "@/views/Dashboard.vue";
import Tables from "@/views/Tables.vue";
import Billing from "@/views/Billing.vue";
import VirtualReality from "@/views/VirtualReality.vue";
import Profile from "@/views/Profile.vue";
import Rtl from "@/views/Rtl.vue";
import SignIn from "@/views/SignIn.vue";
import SignUp from "@/views/SignUp.vue";
import OrganizerDashboard from "@/views/OrganizerDashboard.vue";
import OrganizerEvents from "@/views/OrganizerEvents.vue";
import OrganizerVenues from "@/views/OrganizerVenues.vue";
import OrganizerVendors from "@/views/OrganizerVendors.vue";
import SignOut from "@/views/SignOut.vue";

const routes = [
  {
    path: "/",
    name: "/",
    redirect: "/dashboard",
  },
  {
    path: "/dashboard",
    name: "Dashboard",
    component: Dashboard,
  },
  {
    path: "/tables",
    name: "Tables",
    component: Tables,
  },
  {
    path: "/billing",
    name: "Billing",
    component: Billing,
  },
  {
    path: "/virtual-reality",
    name: "Virtual Reality",
    component: VirtualReality,
  },
  {
    path: "/profile",
    name: "Profile",
    component: Profile,
  },
  {
    path: "/rtl-page",
    name: "Rtl",
    component: Rtl,
  },
  {
    path: "/login",
    name: "Sign In",
    component: SignIn,
  },
  {
    path: "/sign-up",
    name: "Sign Up",
    component: SignUp,
  },
  {
    path: "/sign-out",
    name: "Sign Out",
    component: SignOut,
  },
  {
    path: "/organizer-dashboard",
    name: "Organizer Dashboard",
    component: OrganizerDashboard,
  },
  {
    path: "/organizer-events",
    name: "Organizer Events",
    component: OrganizerEvents,
  },
  {
    path: "/organizer-venues",
    name: "Venues",
    component: OrganizerVenues,
  },
  {
    path: "/organizer-vendors",
    name: "Vendors",
    component: OrganizerVendors,
  },
];

const router = createRouter({
  history: createWebHistory(process.env.BASE_URL),
  routes,
  linkActiveClass: "active",
});

export default router;
