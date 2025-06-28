<template>
  <div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
      <h4 class="mb-0">My Venues</h4>
      <soft-button @click="openCreateModal" variant="gradient" color="success">
        Add Venue
      </soft-button>
    </div>

    <!-- Venue Table -->
    <div class="card mb-4">
      <div class="card-header pb-0">
        <h6>My Venues</h6>
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
                  Address
                </th>
                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 text-center">
                  Capacity
                </th>
                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 text-center">
                  Price
                </th>
                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 text-center">
                  Image
                </th>
                <th class="text-secondary opacity-7 text-center">Actions</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="venue in venues" :key="venue.id">
                <td class="ps-4">
                  <div class="d-flex flex-column justify-content-center">
                    <h6 class="mb-0 text-sm">{{ venue.name }}</h6>
                  </div>
                </td>
                <td>
                  <p class="text-xs text-secondary mb-0">{{ venue.address }}</p>
                </td>
                <td class="align-middle text-center">
                  <span class="text-xs font-weight-bold">{{ venue.capacity }}</span>
                </td>
                <td class="align-middle text-center">
                  <span class="text-xs font-weight-bold">${{ venue.price }}</span>
                </td>
                <td class="align-middle text-center">
                  <img v-if="venue.image" :src=" 'http://localhost:8000/' + venue.image" alt="Venue Image" class="venue-image" style="max-width: 100px; max-height: 100px;" />
                  <span v-else>No Image</span>
                </td>
                <td class="align-middle text-center">
                  <a
                    href="javascript:;"
                    class="text-secondary font-weight-bold text-xs"
                    @click="openEditModal(venue)"
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
    <div v-if="localshowModal" class="modal-backdrop" style=" overflow-y: auto;">
      <div class="modal-container card p-4">
        <!-- Venue Details Tab -->
        <div>
          <h5 class="mb-3">{{ localEditMode ? 'Edit Venue' : 'Create New Venue' }}</h5>
          <form @submit.prevent="localEditMode ? updateVenue() : createVenue()">
            <label class="form-label">Name</label>
            <soft-input v-model="form.name" label="Name" required />
            <label class="form-label">Address</label>
            <soft-input v-model="form.address" label="Address" required />
            <label class="form-label">Capacity</label>
            <soft-input v-model="form.capacity" label="Capacity" type="number" required />
            <label class="form-label">Description</label>
            <soft-input v-model="form.description" label="Description" />
            <label class="form-label">Price</label>
            <soft-input v-model="form.price" label="Price ($)" type="number" step="0.01" required />
            <div class="mb-3">
              <label class="form-label">Image</label><br>
              <img v-if="localEditMode" :src="previewImageUrl" alt="Current Venue Image" 
              
              class="img-fluid w-100"
              style="max-height: 300px; object-fit: contain;"

               />
               <br><br>   

              <input type="file" class="form-control" @change="handleImageUpload" accept="image/*" />
            </div>
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
  props: {
    showModal: Boolean,
    isEditMode: Boolean,
    venueData: Object,
  },
  data() {
    return {
      venues: [],
      localEditMode: this.isEditMode,
      localshowModal: this.showModal,
      form: {
        id: null,
        name: "",
        address: "",
        capacity: "",
        description: "",
        price: "",
        image: null,
        existingImagePath: ''  
      },
      
    };
  },
  watch: {
    showModal(newVal) {
      if (newVal && this.venueData) {
        this.form = {
          id: this.venueData.id,
          name: this.venueData.name,
          address: this.venueData.address,
          capacity: this.venueData.capacity,
          description: this.venueData.description,
          price: this.venueData.price,
          image: this.venueData.image,
        };
      } else {
        this.form = {
          id: null,
          name: "",
          address: "",
          capacity: "",
          description: "",
          price: "",
          image: null,
        };
      }
    },
  },
  mounted() {
    this.fetchVenues();
  },
  methods: {
    async fetchVenues() {
      try {
        const token = localStorage.getItem("token");
        const res = await api.get("/api/venues", {
          headers: { Authorization: `Bearer ${token}` },
        });
        this.venues = res.data;
      } catch (err) {
        console.error("❌ Failed to fetch venues:", err);
      }
    },
    openCreateModal() {
      this.localEditMode = false;
      this.form = {
        id: null,
        name: "",
        address: "",
        capacity: "",
        description: "",
        price: "",
        image: null,
      };
      this.previewImageUrl = null;
      this.localshowModal = true;
    },
    openEditModal(venue) {
      console.log(venue.image);
      this.localEditMode = true;
      this.form = {
        id: venue.id,
        name: venue.name,
        address: venue.address,
        capacity: venue.capacity,
        description: venue.description,
        price: venue.price,
        image: null,
        existingImagePath: venue.image
      };
      this.previewImageUrl = 'http://localhost:8000/' + venue.image;
      this.localshowModal = true;
    },
    closeModal() {
      this.localshowModal = false;
      this.form = {
        id: null,
        name: "",
        address: "",
        capacity: "",
        description: "",
        price: "",
        image: null,
      };
    },
    handleImageUpload(event) {
      const file = event.target.files[0];
      if (file) {
        this.form.image = file;
      }
    },
    async createVenue() {
      try {
        const token = localStorage.getItem("token");
        const formData = new FormData();
        formData.append("name", this.form.name);
        formData.append("address", this.form.address);
        formData.append("capacity", this.form.capacity);
        formData.append("description", this.form.description);
        formData.append("price", this.form.price);
        if (this.form.image) {
          formData.append("image", this.form.image);
        }
        await api.post("/api/venues", formData, {
          headers: {
            Authorization: `Bearer ${token}`,
            "Content-Type": "multipart/form-data",
          },
        });
        this.closeModal();
        this.fetchVenues();
      } catch (err) {
        console.error("❌ Venue creation failed:", err);
      }
    },
    async updateVenue() {
      try {
        console.log(this.form.existingImagePath);
        const token = localStorage.getItem("token");
        const formData = new FormData();
        formData.append("name", this.form.name);
        formData.append("address", this.form.address);
        formData.append("capacity", this.form.capacity);
        formData.append("description", this.form.description);
        formData.append("price", this.form.price);
        if (this.form.image && typeof this.form.image !== "string") {
          formData.append("image", this.form.image);
        }
        await api.post(`/api/venues/${this.form.id}`, formData, {
          headers: {
            Authorization: `Bearer ${token}`,
            "Content-Type": "multipart/form-data",
          },
        });
        this.closeModal();
        this.fetchVenues();
      } catch (err) {
        console.error("❌ Venue update failed:", err);
      }
    },
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