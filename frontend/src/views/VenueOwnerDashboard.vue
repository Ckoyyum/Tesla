<!-- src/views/dashboards/VenueOwnerDashboard.vue -->
<template>
  <section class="p-6 space-y-10">
    <!-- Greeting -->
    <header>
      <h2 class="text-2xl font-semibold">Venue-Owner Dashboard</h2>
      <p class="mt-1 text-lg">Welcome, {{ user.username }}! 🏛️</p>
    </header>

    <!-- 1) BOOKINGS TABLE -->
    <div>
      <h3 class="text-xl font-medium mb-4">Booking Requests</h3>

      <!-- loading / error states -->
      <p v-if="loading">Loading bookings …</p>
      <p v-else-if="error" class="text-red-500">{{ error }}</p>

      <!-- bookings list -->
      <table v-if="bookings.length" class="w-full text-left border-collapse">
        <thead class="bg-gray-800 text-white">
          <tr>
            <th class="p-3">Title</th>
            <th class="p-3">Organizer</th>
            <th class="p-3">Start</th>
            <th class="p-3">End</th>
            <th class="p-3">Status</th>
            <th class="p-3">Actions</th>
          </tr>
        </thead>
        <tbody>
          <tr
            v-for="b in bookings"
            :key="b.id"
            class="odd:bg-gray-100 even:bg-gray-50"
          >
            <td class="p-3">{{ b.title }}</td>
            <td class="p-3">{{ b.organizer_name }}</td>
            <td class="p-3">{{ formatDate(b.start_date) }}</td>
            <td class="p-3">{{ formatDate(b.end_date) }}</td>
            <td class="p-3 capitalize">
              <span
                :class="{
                  'text-yellow-600': b.status === 'pending',
                  'text-green-600': b.status === 'approved',
                  'text-red-600': b.status === 'rejected',
                }"
              >
                {{ b.status }}
              </span>
            </td>
            <td class="p-3 space-x-2">
              <!-- Show buttons only when status is pending -->
              <button
                v-if="b.status === 'pending'"
                @click="approve(b.id)"
                class="px-3 py-1 bg-green-600 text-white rounded hover:bg-green-700"
              >
                Approve
              </button>
              <button
                v-if="b.status === 'pending'"
                @click="reject(b.id)"
                class="px-3 py-1 bg-red-600 text-white rounded hover:bg-red-700"
              >
                Reject
              </button>
            </td>
          </tr>
        </tbody>
      </table>

      <p v-else-if="!loading">No bookings yet 🎉</p>
    </div>

    <!-- 2) ADD VENUE FORM -->
    <div class="max-w-lg">
      <h3 class="text-xl font-medium mb-4">Add a New Venue</h3>

      <form @submit.prevent="addVenue" class="space-y-4">
        <input
          v-model="venueForm.name"
          type="text"
          placeholder="Name"
          required
          class="w-full p-2 border rounded"
        />

        <textarea
          v-model="venueForm.description"
          placeholder="Description"
          required
          class="w-full p-2 border rounded"
        />

        <input
          v-model.number="venueForm.price"
          type="number"
          step="0.01"
          placeholder="Price (e.g. 500.00)"
          required
          class="w-full p-2 border rounded"
        />

        <input
          @change="handleImage"
          type="file"
          accept="image/*"
          required
          class="w-full"
        />

        <button
          :disabled="venueSubmitting"
          type="submit"
          class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 disabled:opacity-50"
        >
          {{ venueSubmitting ? "Adding…" : "Add Venue" }}
        </button>
      </form>
    </div>
  </section>
</template>

<script>
import axios from "axios";

export default {
  name: "VenueOwnerDashboard",

  data() {
    return {
      bookings: [],
      loading: false,
      error: "",
      venueSubmitting: false,
      venueForm: {
        name: "",
        description: "",
        price: null,
        image: null,
      },
    };
  },

  computed: {
    user() {
      return JSON.parse(localStorage.getItem("user") || "{}");
    },
    authHeaders() {
      const t = localStorage.getItem("token");
      return t ? { Authorization: `Bearer ${t}` } : {};
    },
  },

  methods: {
    /* ---------- BOOKINGS ---------- */
    async fetchBookings() {
      this.loading = true;
      try {
        const { data } = await axios.get(
          "http://localhost:8000/owner/bookings",
          { headers: this.authHeaders }
        );
        this.bookings = data;
      } catch (err) {
        this.error = err.response?.data?.message || "Failed to load bookings.";
      } finally {
        this.loading = false;
      }
    },

    async approve(id) {
      try {
        await axios.post(
          `http://localhost:8000/owner/bookings/${id}/approve`,
          {},
          { headers: this.authHeaders }
        );
        this.updateBookingStatus(id, "approved");
      } catch (e) {
        alert(e.response?.data?.message || "Could not approve booking.");
      }
    },

    async reject(id) {
      try {
        await axios.post(
          `http://localhost:8000/owner/bookings/${id}/reject`,
          {},
          { headers: this.authHeaders }
        );
        this.updateBookingStatus(id, "rejected");
      } catch (e) {
        alert(e.response?.data?.message || "Could not reject booking.");
      }
    },

    updateBookingStatus(id, status) {
      this.bookings = this.bookings.map((b) =>
        b.id === id ? { ...b, status } : b
      );
    },

    formatDate(dt) {
      const d = new Date(dt);
      return d.toLocaleString();
    },

    /* ---------- ADD VENUE ---------- */
    handleImage(e) {
      this.venueForm.image = e.target.files[0];
    },

    async addVenue() {
      if (!this.venueForm.image) return alert("Please choose an image.");
      this.venueSubmitting = true;
      try {
        const fd = new FormData();
        fd.append("name", this.venueForm.name);
        fd.append("description", this.venueForm.description);
        fd.append("price", this.venueForm.price);
        fd.append("image", this.venueForm.image);

        await axios.post("http://localhost:8000/owner/venues", fd, {
          headers: {
            ...this.authHeaders,
            "Content-Type": "multipart/form-data",
          },
        });

        alert("Venue added successfully!");
        this.venueForm = {
          name: "",
          description: "",
          price: null,
          image: null,
        };
      } catch (err) {
        alert(err.response?.data?.message || "Unable to add venue.");
      } finally {
        this.venueSubmitting = false;
      }
    },
  },

  created() {
    this.fetchBookings();
  },
};
</script>

<style scoped>
/* Add or reuse styles as you like (see your earlier navbar CSS) */
</style>
