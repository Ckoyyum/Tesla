<template>
  <div class="w-auto h-auto collapse navbar-collapse max-height-vh-100 h-100" id="sidenav-collapse-main">
    <ul class="navbar-nav">
      <li
        v-for="item in menuItemsByRole[$store.state.userRole] || []"
        :key="item.name"
        class="nav-item"
      >
        <sidenav-collapse :navText="item.text" :to="{ name: item.name }">
          <template #icon>
            <component :is="resolveIcon(item.icon)" />
          </template>
        </sidenav-collapse>
      </li>

      
      <!-- Always visible -->
       <!-- <li class="nav-item">
        <sidenav-collapse navText="Sign Out" @click="signOut">
          <template #icon>
            <logout-icon />
          </template>
        </sidenav-collapse>
      </li> -->
      <!-- <li class="nav-item">
        <sidenav-collapse navText="RTL" :to="{ name: 'Rtl' }">
          <template #icon>
            <settings />
          </template>
        </sidenav-collapse>
      </li>

      <li class="mt-3 nav-item">
        <h6
          class="text-xs ps-4 text-uppercase font-weight-bolder opacity-6"
          :class="$store.state.isRTL ? 'me-4' : 'ms-2'"
        >
          Pages
        </h6>
      </li>

      <li class="nav-item">
        <sidenav-collapse navText="Profile" :to="{ name: 'Profile' }">
          <template #icon>
            <customer-support />
          </template>
        </sidenav-collapse>
      </li>
      <li class="nav-item">
        <sidenav-collapse navText="Sign In" :to="{ name: 'Sign In' }">
          <template #icon>
            <document />
          </template>
        </sidenav-collapse>
      </li>
      <li class="nav-item">
        <sidenav-collapse navText="Sign Up" :to="{ name: 'Sign Up' }">
          <template #icon>
            <spaceship />
          </template>
        </sidenav-collapse>
      </li> -->
    </ul>
  </div>

  <!-- <div class="pt-3 mx-3 mt-3 sidenav-footer">
    <sidenav-card
      :class="cardBg"
      textPrimary="Need Help?"
      textSecondary="Please check our docs"
      route="https://www.creative-tim.com/learning-lab/vue/overview/soft-ui-dashboard/"
      label="Documentation"
      icon="ni ni-diamond"
    />
    <a
      class="btn bg-gradient-success mt-4 w-100"
      href="https://www.creative-tim.com/product/vue-soft-ui-dashboard-pro?ref=vsud"
      type="button"
    >
      Upgrade to pro
    </a>
  </div> -->
</template>

<script>
import SidenavCollapse from "./SidenavCollapse.vue";
import SidenavCard from "./SidenavCard.vue";
import Shop from "../../components/Icon/Shop.vue";
import Office from "../../components/Icon/Office.vue";
import CreditCard from "../../components/Icon/CreditCard.vue";
import Box3d from "../../components/Icon/Box3d.vue";
import CustomerSupport from "../../components/Icon/CustomerSupport.vue";
import Document from "../../components/Icon/Document.vue";
import Spaceship from "../../components/Icon/Spaceship.vue";
import Settings from "../../components/Icon/Settings.vue";
import LogoutIcon from "../../components/Icon/Logout.vue";

export default {
  name: "SidenavList",
  props: {
    cardBg: String,
  },
  components: {
    SidenavCollapse,
    SidenavCard,
    Shop,
    Office,
    CreditCard,
    Box3d,
    CustomerSupport,
    Document,
    Spaceship,
    Settings,
    LogoutIcon
  },
  data() {
    return {
      menuItemsByRole: {
        general: [
          { text: "Dashboard", name: "Dashboard", icon: "Shop" },
          { text: "Tables", name: "Tables", icon: "Office" },
          { text: "Billing", name: "Billing", icon: "CreditCard" },
          { text: "Virtual Reality", name: "Virtual Reality", icon: "Box3d" },
          { text: "RTL", name: "Rtl", icon: "settings" },
          { text: "Profile", name: "Profile", icon: "customer-support" },
          { text: "Sign In", name: "Sign In", icon: "document" },
          { text: "Sign Up", name: "Sign Up", icon: "spaceship" },
        ],
        organizer: [
          { text: "Dashboard", name: "Organizer Dashboard", icon: "Shop" },
          { text: "Events", name: "Organizer Events", icon: "Shop" },
          { text: "Venues", name: "Venues", icon: "Shop" },
          { text: "Vendor Services", name: "Vendors", icon: "Shop" },
          { text: "Sign Out", name: "Sign Out", icon: "Shop" },
        ],
      },
    };
  },
  methods: {
    resolveIcon(icon) {
      return this.$options.components[icon];
    },
    signOut() {
      try {
        console.log("Sign out triggered");
        localStorage.removeItem("token");
        localStorage.removeItem("user");
        console.log("Local storage cleared:", localStorage.getItem("token"), localStorage.getItem("user"));
        this.$router.push({ name: "Sign In" });
      } catch (error) {
        console.error("Sign out error:", error);
      }
    },
  },
};
</script>