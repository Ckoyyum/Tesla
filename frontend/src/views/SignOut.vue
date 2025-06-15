<!-- <template>
  <div>
  </div>
</template>

<script>
export default {
  name: "SignOut",
  mounted() {
    try {
      console.log("Sign out route triggered");
      localStorage.removeItem("token");
      localStorage.removeItem("user");
      console.log("Local storage cleared:", localStorage.getItem("token"), localStorage.getItem("user"));
      this.$router.push({ name: "Sign In" });
    } catch (error) {
      console.error("Sign out error:", error);
      this.$router.push({ name: "Sign In" });
    }
  },
};
</script> -->

<template>
  <div>
    <!-- Sign Out Confirmation Modal -->
    <div v-if="showModal" class="modal fade show d-block" tabindex="-1" role="dialog" style="background: rgba(0, 0, 0, 0.5);">
      <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">Confirm Sign Out</h5>
            <button type="button" class="btn-close" @click="cancelSignOut" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            <p>Are you sure you want to sign out?</p>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" @click="cancelSignOut">Cancel</button>
            <button type="button" class="btn btn-primary" @click="confirmSignOut">Confirm</button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: "SignOut",
  data() {
    return {
      showModal: true,
    };
  },
  methods: {
    confirmSignOut() {
      try {
        console.log("Sign out confirmed");
        localStorage.removeItem("token");
        localStorage.removeItem("user");
        console.log("Local storage cleared:", localStorage.getItem("token"), localStorage.getItem("user"));
        this.showModal = false;
        this.$router.push({ name: "Sign In" });
      } catch (error) {
        console.error("Sign out error:", error);
        this.showModal = false;
        this.$router.push({ name: "Sign In" });
      }
    },
    cancelSignOut() {
      console.log("Sign out canceled");
      this.showModal = false;
      this.$router.go(-1); // Navigate back to the previous page
    },
  },
  mounted() {
    console.log("Sign out route triggered");
  },
};
</script>

<style scoped>
.modal {
  z-index: 1050;
}
.modal-backdrop {
  background: rgba(0, 0, 0, 0.5);
}
</style>