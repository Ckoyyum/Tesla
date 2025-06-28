<template>
  <div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
      <h4 class="mb-0">My Venue Bookings</h4>
    </div>

    <!-- Booking Table -->
    <div class="card mb-4">
      <div class="card-header pb-0">
        <h6>Pending Bookings</h6>
      </div>
      <div class="card-body px-0 pt-0 pb-2">
        <div class="table-responsive p-0">
          <table class="table align-items-center mb-0">
            <thead>
              <tr>
                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-4">
                  Event Title
                </th>
                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                  Venue
                </th>
                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 text-center">
                  Status
                </th>
                <th class="text-secondary opacity-7 text-center">Actions</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="booking in bookings" :key="booking.id">
                <td class="ps-4">
                  <div class="d-flex flex-column justify-content-center">
                    <h6 class="mb-0 text-sm">{{ booking.event ? booking.event.title : 'N/A' }}</h6>
                  </div>
                </td>
                <td>
                  <p class="text-xs text-secondary mb-0">{{ getVenueName(booking.entity_id) }}</p>
                </td>
                <td class="align-middle text-center">
                  <span class="text-xs font-weight-bold">{{ booking.status }}</span>
                </td>
                <td class="align-middle text-center">
                  <soft-button
                    v-if="booking.status === 'pending'"
                    color="success"
                    variant="gradient"
                    size="sm"
                    class="me-2"
                    @click="openActionModal(booking, 'approved')"
                  >
                    Approve
                  </soft-button>
                  <soft-button
                    v-if="booking.status === 'pending'"
                    color="danger"
                    variant="gradient"
                    size="sm"
                    @click="openActionModal(booking, 'rejected')"
                  >
                    Reject
                  </soft-button>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>

    <!-- Action Modal -->
    <div v-if="showActionModal" class="modal-backdrop">
      <div class="modal-container card p-4">
        <h5 class="mb-3">{{ action === 'approved' ? 'Approve Booking' : 'Reject Booking' }}</h5>
        <p>Are you sure you want to {{ action }} the booking for "{{ selectedBooking.event.title }}" at venue "{{ getVenueName(selectedBooking.entity_id) }}"?</p>
        <div class="d-flex justify-content-end mt-4">
          <soft-button color="secondary" class="me-2" @click="closeActionModal">Cancel</soft-button>
          <soft-button
            :color="action === 'approved' ? 'success' : 'danger'"
            variant="gradient"
            @click="updateBookingStatus"
          >
            {{ action === 'approved' ? 'Approve' : 'Reject' }}
          </soft-button>
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
      bookings: [],
      venues: [],
      showActionModal: false,
      selectedBooking: null,
      action: ''
    };
  },
  methods: {
    async fetchBookings() {
      try {
        const token = localStorage.getItem("token");
        const res = await api.get("/api/bookings", {
          headers: { Authorization: `Bearer ${token}` },
        });
        this.bookings = res.data.filter(booking => booking.entity_type === 'venue');
      } catch (err) {
        console.error("❌ Failed to fetch bookings:", err);
      }
    },
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
    getVenueName(venueId) {
      const venue = this.venues.find(v => v.id === venueId);
      return venue ? venue.name : 'N/A';
    },
    openActionModal(booking, action) {
      this.selectedBooking = booking;
      this.action = action;
      this.showActionModal = true;
    },
    closeActionModal() {
      this.showActionModal = false;
      this.selectedBooking = null;
      this.action = '';
    },
    async updateBookingStatus() {
      try {
        const token = localStorage.getItem("token");
        await api.post(`/api/bookings/${this.selectedBooking.id}`, { status: this.action }, {
          headers: { Authorization: `Bearer ${token}` },
        });
        this.closeActionModal();
        this.fetchBookings();
      } catch (err) {
        console.error(`❌ Failed to ${this.action} booking:`, err);
        alert(`Failed to ${this.action} booking. Please try again.`);
      }
    }
  },
  mounted() {
    this.fetchBookings();
    this.fetchVenues();
  }
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