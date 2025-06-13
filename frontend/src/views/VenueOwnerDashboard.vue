<template>
  <div class="venue-owner-dashboard">
    <div class="container">
      <h1>Welcome, {{ user.name || "Venue Owner" }}</h1>

      <!-- BOOKINGS SECTION -->
      <section class="section">
        <h2>Your Bookings</h2>
        <div v-if="loading" class="loading">Loading bookings...</div>
        <div v-else-if="error" class="error">{{ error }}</div>
        <div v-else-if="bookings.length === 0">No bookings yet.</div>
        <ul v-else class="booking-list">
          <li
            v-for="booking in bookings"
            :key="booking.id"
            class="booking-card"
          >
            <p><strong>Venue:</strong> {{ booking.venueName }}</p>
            <p><strong>Date:</strong> {{ formatDate(booking.date) }}</p>
            <p><strong>Status:</strong> {{ booking.status }}</p>
            <div class="booking-actions" v-if="booking.status === 'pending'">
              <button
                @click="approve(booking.id)"
                :disabled="processingBooking === booking.id"
              >
                Approve
              </button>
              <button
                @click="reject(booking.id)"
                :disabled="processingBooking === booking.id"
              >
                Reject
              </button>
            </div>
          </li>
        </ul>
      </section>

      <!-- ADD VENUE SECTION -->
      <section class="section">
        <h2>Add a New Venue</h2>
        <form @submit.prevent="addVenue" class="venue-form">
          <input v-model="venueForm.name" placeholder="Venue Name" required />
          <textarea
            v-model="venueForm.description"
            placeholder="Description"
            rows="3"
          ></textarea>
          <input v-model="venueForm.address" placeholder="Address" />
          <input
            v-model.number="venueForm.capacity"
            type="number"
            placeholder="Capacity"
          />
          <input
            v-model.number="venueForm.price"
            type="number"
            placeholder="Price"
            required
          />
          <input
            type="file"
            @change="handleImage"
            ref="imageInput"
            accept="image/*"
          />

          <div v-if="imagePreview" class="image-preview">
            <p>Preview:</p>
            <img :src="imagePreview" alt="Venue Image" />
          </div>

          <button type="submit" :disabled="venueSubmitting">
            {{ venueSubmitting ? "Adding..." : "Add Venue" }}
          </button>
        </form>
      </section>

      <!-- VENUE LIST SECTION -->
      <section class="section">
        <h2>Your Venues</h2>
        <div v-if="venues.length === 0">No venues added yet.</div>
        <ul v-else class="venue-list">
          <li v-for="venue in venues" :key="venue.id" class="venue-card">
            <p>
              <strong>{{ venue.name }}</strong>
            </p>
            <p>Price: ${{ venue.price }}</p>
            <p>Capacity: {{ venue.capacity }}</p>
          </li>
        </ul>
      </section>
    </div>
  </div>
</template>

<script>
import axios from "axios";

export default {
  name: "venue-owner-dashboard",
  data() {
    return {
      bookings: [],
      venues: [],
      loading: false,
      error: "",
      venueSubmitting: false,
      processingBooking: null,
      imagePreview: null,
      venueForm: {
        name: "",
        description: "",
        address: "",
        capacity: null,
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
    async fetchBookings() {
      this.loading = true;
      this.error = "";
      try {
        const { data } = await axios.get(
          "http://localhost:8000/owner/bookings",
          {
            headers: this.authHeaders,
          }
        );
        this.bookings = data;
      } catch (err) {
        this.error = err.response?.data?.message || "Failed to load bookings.";
      } finally {
        this.loading = false;
      }
    },
    async approve(id) {
      this.processingBooking = id;
      try {
        await axios.post(
          `http://localhost:8000/owner/bookings/${id}/approve`,
          {},
          {
            headers: this.authHeaders,
          }
        );
        this.updateBookingStatus(id, "approved");
        this.$toast?.success("Booking approved successfully!");
      } catch (e) {
        alert(e.response?.data?.message || "Could not approve booking.");
      } finally {
        this.processingBooking = null;
      }
    },
    async reject(id) {
      this.processingBooking = id;
      try {
        await axios.post(
          `http://localhost:8000/owner/bookings/${id}/reject`,
          {},
          {
            headers: this.authHeaders,
          }
        );
        this.updateBookingStatus(id, "rejected");
        this.$toast?.success("Booking rejected successfully!");
      } catch (e) {
        alert(e.response?.data?.message || "Could not reject booking.");
      } finally {
        this.processingBooking = null;
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
    async fetchVenues() {
      try {
        const { data } = await axios.get("http://localhost:8000/owner/venues", {
          headers: this.authHeaders,
        });
        this.venues = data;
      } catch (err) {
        console.error("Failed to load venues:", err);
      }
    },
    handleImage(e) {
      const file = e.target.files[0];
      if (!file) return;

      const allowedTypes = [
        "image/jpeg",
        "image/jpg",
        "image/png",
        "image/gif",
      ];
      if (!allowedTypes.includes(file.type)) {
        alert("Please upload a valid image file (JPEG, PNG, or GIF).");
        this.resetImageInput();
        return;
      }

      if (file.size > 5 * 1024 * 1024) {
        alert("Image must be less than 5MB.");
        this.resetImageInput();
        return;
      }

      this.venueForm.image = file;
      const reader = new FileReader();
      reader.onload = (e) => {
        this.imagePreview = e.target.result;
      };
      reader.readAsDataURL(file);
    },
    resetImageInput() {
      if (this.$refs.imageInput) {
        this.$refs.imageInput.value = "";
      }
      this.venueForm.image = null;
      this.imagePreview = null;
    },
    async addVenue() {
      const { name, price, image } = this.venueForm;
      if (!name || !price || !image) {
        alert("Please fill in all required fields and choose an image.");
        return;
      }

      this.venueSubmitting = true;
      try {
        const fd = new FormData();
        fd.append("name", this.venueForm.name.trim());
        fd.append("description", this.venueForm.description?.trim() || "");
        fd.append("address", this.venueForm.address?.trim() || "");
        fd.append("capacity", this.venueForm.capacity || 0);
        fd.append("price", this.venueForm.price);
        fd.append("image", this.venueForm.image);

        await axios.post("http://localhost:8000/owner/venues", fd, {
          headers: {
            ...this.authHeaders,
            "Content-Type": "multipart/form-data",
          },
        });

        this.$toast?.success("Venue added successfully!");
        alert("Venue added successfully!");

        this.venueForm = {
          name: "",
          description: "",
          address: "",
          capacity: null,
          price: null,
          image: null,
        };
        this.resetImageInput();
        this.fetchVenues();
      } catch (err) {
        console.error("Add venue error:", err);
        const msg = err.response?.data?.message || "Unable to add venue.";
        this.$toast?.error(msg);
        alert(msg);
      } finally {
        this.venueSubmitting = false;
      }
    },
  },
  created() {
    this.fetchBookings();
    this.fetchVenues();
  },
};
</script>

<style scoped>
.container {
  max-width: 1000px;
  margin: 0 auto;
  padding: 2rem;
}
.section {
  margin-top: 2rem;
}
.venue-form input,
.venue-form textarea {
  display: block;
  width: 100%;
  margin-bottom: 1rem;
  padding: 0.5rem;
}
button {
  padding: 0.5rem 1rem;
  cursor: pointer;
}
.booking-list,
.venue-list {
  list-style: none;
  padding: 0;
}
.booking-card,
.venue-card {
  border: 1px solid #ccc;
  padding: 1rem;
  margin-bottom: 1rem;
  border-radius: 8px;
}
.booking-actions button {
  margin-right: 1rem;
}
.image-preview img {
  max-width: 200px;
  margin-top: 0.5rem;
  border-radius: 8px;
}
.error {
  color: red;
}
.loading {
  font-style: italic;
}
</style>
