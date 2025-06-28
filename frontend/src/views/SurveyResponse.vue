<template>
  <div class="container mt-4">
    <div class="card mb-4">
      <div class="card-header pb-0">
        <h6>Event Survey: {{ eventName }}</h6>
      </div>
      <div class="card-body px-0 pt-0 pb-2">
        <form @submit.prevent="submitSurvey">
          <div class="mb-3">
            <label class="form-label">1. Venue</label>
            <div class="star-rating">
              <span
                v-for="star in 5"
                :key="star"
                :class="{ 'star-filled': form.venue_rating >= star }"
                @click="form.venue_rating = star"
                class="star"
              >★</span>
            </div>
          </div>
          <div class="mb-3">
            <label class="form-label">2. Services</label>
            <div class="star-rating">
              <span
                v-for="star in 5"
                :key="star"
                :class="{ 'star-filled': form.services_rating >= star }"
                @click="form.services_rating = star"
                class="star"
              >★</span>
            </div>
          </div>
          <div class="mb-3">
            <label class="form-label">3. Management</label>
            <div class="star-rating">
              <span
                v-for="star in 5"
                :key="star"
                :class="{ 'star-filled': form.management_rating >= star }"
                @click="form.management_rating = star"
                class="star"
              >★</span>
            </div>
          </div>
          <div class="d-flex justify-content-end mt-4">
            <soft-button type="submit" color="success" variant="gradient">
              Submit
            </soft-button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script>
import SoftButton from "@/components/SoftButton.vue";
import api from "@/utils/api";

export default {
  components: { SoftButton },
  props: {
    eventId: {
      type: [Number, String],
      required: true
    }
  },
  data() {
    return {
      eventName: "",
      form: {
        venue_rating: 0,
        services_rating: 0,
        management_rating: 0
      }
    };
  },
  methods: {
    async fetchEvent() {
      try {
        const res = await api.get(`/api/events/${this.eventId}`);
        this.eventName = res.data.title || "Event";
      } catch (err) {
        console.error("❌ Failed to fetch event:", err);
        this.eventName = "Event";
      }
    },
    async submitSurvey() {
      try {
        if (!this.form.venue_rating || !this.form.services_rating || !this.form.management_rating) {
          alert("Please provide a rating for all questions.");
          return;
        }
        await api.post(`/api/surveys/${this.eventId}`, this.form);
        alert("Thank you for your feedback!");
        this.form = {
          venue_rating: 0,
          services_rating: 0,
          management_rating: 0
        };
      } catch (err) {
        console.error("❌ Survey submission failed:", err);
        alert("Failed to submit survey. Please try again.");
      }
    }
  },
  mounted() {
    this.fetchEvent();
  }
};
</script>

<style scoped>
.container {
  max-width: 600px;
}
.star-rating {
  display: inline-block;
}
.star {
  font-size: 24px;
  color: #ccc;
  cursor: pointer;
  margin-right: 5px;
}
.star-filled {
  color: #f1c40f;
}
</style>