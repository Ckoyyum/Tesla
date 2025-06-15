<template>
  <div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
      <h4 class="mb-0">Available Venues</h4>
    </div>

    <!-- Venue Table -->
    <div class="card mb-4">
      <div class="card-header pb-0">
        <h6>Venues</h6>
      </div>
      <div class="card-body px-0 pt-0 pb-2">
        <div class="table-responsive p-0">
          <table class="table align-items-center mb-0">
            <thead>
              <tr>
                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-4">
                  ID
                </th>
                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                  User ID
                </th>
                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                  Name
                </th>
                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                  Address
                </th>
                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 text-center">
                  Capacity
                </th>
                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                  Description
                </th>
              </tr>
            </thead>
            <tbody>
              <tr
                v-for="venue in venues"
                :key="venue.id"
                @click="openVenueModal(venue)"
                class="venue-row"
              >
                <td class="ps-4">
                  <div class="d-flex flex-column justify-content-center">
                    <h6 class="mb-0 text-sm">{{ venue.id }}</h6>
                  </div>
                </td>
                <td>
                  <p class="text-xs text-secondary mb-0">{{ venue.user_id }}</p>
                </td>
                <td>
                  <p class="text-xs text-secondary mb-0">{{ venue.name }}</p>
                </td>
                <td>
                  <p class="text-xs text-secondary mb-0">{{ venue.address }}</p>
                </td>
                <td class="align-middle text-center">
                  <span class="text-xs font-weight-bold">{{ venue.capacity ?? 'N/A' }}</span>
                </td>
                <td>
                  <p class="text-xs text-secondary mb-0">{{ venue.description ?? 'N/A' }}</p>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>

    <!-- Venue Modal -->
    <div v-if="showModal" class="modal-backdrop">
      <div class="modal-container card p-4">
        <h5 class="mb-3">Venue Details</h5>
        <div class="mb-3">
          <label class="form-label">ID</label>
          <p class="text-sm">{{ selectedVenue.id }}</p>
        </div>
        <div class="mb-3">
          <label class="form-label">User ID</label>
          <p class="text-sm">{{ selectedVenue.user_id }}</p>
        </div>
        <div class="mb-3">
          <label class="form-label">Name</label>
          <p class="text-sm">{{ selectedVenue.name }}</p>
        </div>
        <div class="mb-3">
          <label class="form-label">Address</label>
          <p class="text-sm">{{ selectedVenue.address }}</p>
        </div>
        <div class="mb-3">
          <label class="form-label">Capacity</label>
          <p class="text-sm">{{ selectedVenue.capacity ?? 'N/A' }}</p>
        </div>
        <div class="mb-3">
          <label class="form-label">Description</label>
          <p class="text-sm">{{ selectedVenue.description ?? 'N/A' }}</p>
        </div>
        <div class="d-flex justify-content-end mt-4">
          <soft-button color="secondary" @click="closeModal">Close</soft-button>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import SoftButton from "@/components/SoftButton.vue";
import api from "@/utils/api";

export default {
  components: { SoftButton },
  data() {
    return {
      venues: [],
      showModal: false,
      selectedVenue: null,
    };
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
    openVenueModal(venue) {
      this.selectedVenue = { ...venue };
      this.showModal = true;
    },
    closeModal() {
      this.showModal = false;
      this.selectedVenue = null;
    },
  },
};
</script>

<style scoped>
/* .container {
  max-width: 1200px;
  margin: 0 auto;
  padding: 0 15px;
} */

.card {
  border-radius: 0.5rem;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
}

.card-header {
  padding: 1rem;
}

.card-body {
  padding: 1rem;
}

.table {
  width: 100%;
  margin-bottom: 0;
}

.table th,
.table td {
  padding: 0.75rem;
  vertical-align: middle;
}

.text-uppercase {
  text-transform: uppercase;
}

.text-secondary {
  color: #6c757d;
}

.text-xxs {
  font-size: 0.75rem;
}

.font-weight-bolder {
  font-weight: 700;
}

.opacity-7 {
  opacity: 0.7;
}

.text-sm {
  font-size: 0.875rem;
}

.ps-4 {
  padding-left: 1.5rem;
}

.venue-row {
  cursor: pointer;
  transition: background-color 0.2s;
}

.venue-row:hover {
  background-color: #f8f9fa;
}

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

.form-label {
  font-size: 0.875rem;
  font-weight: 500;
  color: #344767;
}
</style>