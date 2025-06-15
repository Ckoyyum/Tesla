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
                  Venue Name
                </th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="venue in venues" :key="venue.id">
                <td class="ps-4">
                  <div class="d-flex flex-column justify-content-center">
                    <h6 class="mb-0 text-sm">{{ venue.name }}</h6>
                  </div>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import api from "@/utils/api";

export default {
  data() {
    return {
      venues: [],
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
</style>