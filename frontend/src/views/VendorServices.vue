<template>
  <div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
      <h4 class="mb-0">My Services</h4>
      <soft-button @click="openCreateModal" variant="gradient" color="success">
        Add Service
      </soft-button>
    </div>

    <!-- Service Table -->
    <div class="card mb-4">
      <div class="card-header pb-0">
        <h6>My Services</h6>
      </div>
      <div class="card-body px-0 pt-0 pb-2">
        <div class="table-responsive p-0">
          <table class="table align-items-center mb-0">
            <thead>
              <tr>
                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-4">
                  Name
                </th>
                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                  Description
                </th>
                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 text-center">
                  Price
                </th>
                <th class="text-secondary opacity-7 text-center">Actions</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="service in services" :key="service.id">
                <td class="ps-4">
                  <div class="d-flex flex-column justify-content-center">
                    <h6 class="mb-0 text-sm">{{ service.name }}</h6>
                  </div>
                </td>
                <td>
                  <p class="text-xs text-secondary mb-0">{{ service.description || 'No description' }}</p>
                </td>
                <td class="align-middle text-center">
                  <span class="text-xs font-weight-bold">${{ service.price }}</span>
                </td>
                <td class="align-middle text-center">
                  <a
                    href="javascript:;"
                    class="text-secondary font-weight-bold text-xs"
                    @click="openEditModal(service)"
                  >
                    Edit
                  </a>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>

    <!-- Modal -->
    <div v-if="localshowModal" class="modal-backdrop" style="overflow-y: auto;">
      <div class="modal-container card p-4">
        <!-- Service Details Tab -->
        <div>
          <h5 class="mb-3">{{ localEditMode ? 'Edit Service' : 'Create New Service' }}</h5>
          <form @submit.prevent="localEditMode ? updateService() : createService()">
            <label class="form-label">Name</label>
            <soft-input v-model="form.name" label="Name" required />
            <label class="form-label">Description</label>
            <soft-input v-model="form.description" label="Description" />
            <label class="form-label">Price</label>
            <soft-input v-model="form.price" label="Price ($)" type="number" step="0.01" required />
            <div class="d-flex justify-content-end mt-4">
              <soft-button color="secondary" class="me-2" @click="closeModal">Cancel</soft-button>
              <soft-button type="submit" color="success" variant="gradient">
                {{ localEditMode ? 'Update' : 'Save' }}
              </soft-button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import SoftButton from "@/components/SoftButton.vue";
import SoftInput from "@/components/SoftInput.vue";
import api from "@/utils/api";

export default {
  components: { SoftButton, SoftInput },
  data() {
    return {
      services: [],
      localEditMode: false,
      localshowModal: false,
      form: {
        id: null,
        name: "",
        description: "",
        price: ""
      }
    };
  },
  methods: {
    async fetchServices() {
      try {
        const token = localStorage.getItem("token");
        const res = await api.get("/api/vendor-services", {
          headers: { Authorization: `Bearer ${token}` },
        });
        this.services = res.data;
      } catch (err) {
        console.error("❌ Failed to fetch services:", err);
      }
    },
    openCreateModal() {
      this.localEditMode = false;
      this.localshowModal = true;
      this.form = {
        id: null,
        name: "",
        description: "",
        price: ""
      };
    },
    openEditModal(service) {
      this.localEditMode = true;
      this.localshowModal = true;
      this.form = {
        id: service.id,
        name: service.name,
        description: service.description || "",
        price: service.price
      };
    },
    closeModal() {
      this.localshowModal = false;
      this.localEditMode = false;
      this.form = {
        id: null,
        name: "",
        description: "",
        price: ""
      };
    },
    async createService() {
      try {
        const token = localStorage.getItem("token");
        const formData = new FormData();
        formData.append("name", this.form.name);
        formData.append("description", this.form.description);
        formData.append("price", this.form.price);
        await api.post("/api/vendor-services", formData, {
          headers: {
            Authorization: `Bearer ${token}`,
            "Content-Type": "multipart/form-data",
          },
        });
        this.closeModal();
        this.fetchServices();
      } catch (err) {
        console.error("❌ Service creation failed:", err);
      }
    },
    async updateService() {
      try {
        const token = localStorage.getItem("token");
        const formData = new FormData();
        formData.append("name", this.form.name);
        formData.append("description", this.form.description);
        formData.append("price", this.form.price);
        await api.post(`/api/vendor-services/${this.form.id}`, formData, {
          headers: {
            Authorization: `Bearer ${token}`,
            "Content-Type": "multipart/form-data",
          },
        });
        this.closeModal();
        this.fetchServices();
      } catch (err) {
        console.error("❌ Service update failed:", err);
      }
    },
  },
  mounted() {
    this.fetchServices();
  },
};
</script>

<style scoped>
.modal-backdrop {
  position: fixed;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background: rgba(0, 0, 0, 0.5);
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 1050;
}
.modal-container {
  max-width: 600px;
  width: 100%;
}
</style>