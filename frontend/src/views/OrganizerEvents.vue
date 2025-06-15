<template>
  <div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
      <h4 class="mb-0">My Events</h4>
      <soft-button @click="showModal = true" variant="gradient" color="success">
        Add Event
      </soft-button>
    </div>

    <!-- Event Table -->
    <div class="card mb-4">
      <div class="card-header pb-0">
        <h6>My Events</h6>
      </div>
      <div class="card-body px-0 pt-0 pb-2">
        <div class="table-responsive p-0">
          <table class="table align-items-center mb-0">
            <thead>
              <tr>
                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-4">
                  Title
                </th>
                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                  Description
                </th>
                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 text-center">
                  Start
                </th>
                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 text-center">
                  End
                </th>
                <th class="text-secondary opacity-7 text-center">Actions</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="event in events" :key="event.id">
                <td class="ps-4">
                  <div class="d-flex flex-column justify-content-center">
                    <h6 class="mb-0 text-sm">{{ event.title }}</h6>
                  </div>
                </td>
                <td>
                  <p class="text-xs text-secondary mb-0">{{ event.description }}</p>
                </td>
                <td class="align-middle text-center">
                  <span class="text-xs font-weight-bold">{{ formatDate(event.start_date) }}</span>
                </td>
                <td class="align-middle text-center">
                  <span class="text-xs font-weight-bold">{{ formatDate(event.end_date) }}</span>
                </td>
                <td class="align-middle text-center">
                  <a
                    href="javascript:;"
                    class="text-secondary font-weight-bold text-xs"
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
    <div v-if="showModal" class="modal-backdrop">
      <div class="modal-container card p-4">
        <h5 class="mb-3">Create New Event</h5>

        <form @submit.prevent="createEvent">
          <soft-input v-model="form.title" label="Title" />
          <soft-input v-model="form.description" label="Description" />
          <soft-input v-model="form.start_date" label="Start Date & Time" type="datetime-local" />
          <soft-input v-model="form.end_date" label="End Date & Time" type="datetime-local" />

          <div class="d-flex justify-content-end mt-4">
            <soft-button color="secondary" class="me-2" @click="showModal = false">Cancel</soft-button>
            <soft-button type="submit" color="success" variant="gradient">Save</soft-button>
          </div>
        </form>
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
      showModal: false,
      events: [],
      form: {
        title: "",
        description: "",
        start_date: "",
        end_date: "",
      },
    };
  },
  mounted() {
    this.fetchEvents();
  },
  methods: {
    async fetchEvents() {
      const token = localStorage.getItem("token");
      const res = await api.get("/api/organizer/events", {
        headers: { Authorization: `Bearer ${token}` },
      });
      this.events = res.data;
    },
    async createEvent() {
      try {
        const token = localStorage.getItem("token");
        await api.post("/api/organizer/events", this.form, {
          headers: { Authorization: `Bearer ${token}` },
        });
        this.showModal = false;
        this.fetchEvents(); // refresh
      } catch (err) {
        console.error("❌ Event creation failed:", err);
      }
    },
    formatDate(dateStr) {
      return new Date(dateStr).toLocaleString();
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
