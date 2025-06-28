<template>
  <div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
      <h4 class="mb-0">My Events</h4>
      <soft-button @click="openCreateModal" variant="gradient" color="success">
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
                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 text-center">
                  Venue
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
                  <span class="text-xs font-weight-bold">{{ event.venue ? event.venue.name : 'N/A' }}</span>
                </td>
                <td class="align-middle text-center">
                  <a
                    href="javascript:;"
                    class="text-secondary font-weight-bold text-xs"
                    @click="openEditModal(event)"
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
    <div v-if="localshowModal" class="modal-backdrop">
    <div class="modal-container card p-4">
      <!-- Tabs Navigation -->
      <ul class="nav nav-tabs mb-3">
        <li class="nav-item">
          <button class="nav-link" :class="{ active: activeTab === 'details' }" @click="activeTab = 'details'">
            Event Details
          </button>
        </li>
        <li class="nav-item">
          <button class="nav-link" :class="{ active: activeTab === 'attendees' }" @click="activeTab = 'attendees'">
            Attendees
          </button>
        </li>
        <li class="nav-item">
          <button class="nav-link" :class="{ active: activeTab === 'scanner' }" @click="activeTab = 'scanner'">
            scanner
          </button>
        </li>
        <li class="nav-item">
          <button class="nav-link" :class="{ active: activeTab === 'attendance' }" @click="activeTab = 'attendance'">
            attendance
          </button>
        </li>
      </ul>

      <!-- Event Details Tab -->
      <div v-if="activeTab === 'details'">
        <h5 class="mb-3">{{ isEditMode ? 'Edit Event' : 'Create New Event' }}</h5>
        <form @submit.prevent="isEditMode ? updateEvent() : createEvent()">
          <soft-input v-model="form.title" label="Title" />
          <soft-input v-model="form.description" label="Description" />
          <soft-input v-model="form.start_date" label="Start Date & Time" type="datetime-local" />
          <soft-input v-model="form.end_date" label="End Date & Time" type="datetime-local" />
          <div class="mb-1">
            <label class="form-label">Venue</label>
            <select v-model="form.venue_id" class="form-control">
              <option value="null" disabled>Select a venue</option>
              <option v-for="venue in venues" :key="venue.id" :value="venue.id">
                {{ venue.name }}
              </option>
            </select>
          </div>
          <div class="d-flex justify-content-end mt-4">
            <soft-button color="secondary" class="me-2" @click="closeModal">Cancel</soft-button>
            <soft-button type="submit" color="success" variant="gradient">
              {{ isEditMode ? 'Update' : 'Save' }}
            </soft-button>
          </div>
        </form>
      </div>

      <!-- Attendees Tab -->
      <div v-if="activeTab === 'attendees'" >
      <!-- <div> -->
        <h5 class="mb-3">Attendees List</h5>
        <div class="mb-3">
          <div class="input-group">
            <input v-model="newAttendeeName" type="text" class="form-control" placeholder="Enter attendee name">
            <button class="btn btn-success" @click="addAttendee">Add Attendee</button>
          </div>
        </div>
        <div style="max-height: 400px; overflow-y: auto;">
          <table class="table" >
            <thead>
              <tr>
                <th>Name</th>
                <th>QR Code</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="attendee in attendees" :key="attendee.id">
                <td>{{ attendee.name }}</td>
                <td>
                  <qrcode-vue v-if="attendee?.qr_code" :value="attendee.qr_code" :size="100" level="H" render-as="svg" />
                  <span v-else>No QR Code</span>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
        <div class="d-flex justify-content-end mt-4">
          <soft-button color="secondary" @click="closeModal">Close</soft-button>
        </div>
      </div>

      <!-- Scanner tab -->
      <div v-if="activeTab === 'scanner'" >
      <!-- <div> -->
        <h5 class="mb-3">scanner</h5>
        <div class="mb-3">
        </div>
        <StreamBarcodeReader @decode="onDecode" @loaded="onLoaded"></StreamBarcodeReader>
        <div v-if="scanMessage" :class="['mt-3', scanStatus === 'success' ? 'text-success' : 'text-danger']">
          {{ scanMessage }}
        </div>
        <div class="d-flex justify-content-end mt-4">
          <soft-button color="secondary" @click="closeModal">Close</soft-button>
        </div>
      </div>

      <!-- Scanner tab -->
      <div v-if="activeTab === 'attendance'" >
      <!-- <div> -->
        <h5 class="mb-3">Attendance</h5>
        <div class="mb-3">
        </div>
        
        <div style="max-height: 400px; overflow-y: auto;">
          <table class="table" >
            <thead>
              <tr>
                <th>Name</th>
                <th>Attended</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="attendance in attendances" :key="attendance.id">
                <td>{{ attendance.attendee.name }}</td>
                <td>{{ attendance.attended_at }}</td>

              </tr>
            </tbody>
          </table>
        </div>
        <div class="d-flex justify-content-end mt-4">
          <soft-button color="secondary" @click="closeModal">Close</soft-button>
        </div>
      </div>

    </div>
  </div>
  </div>
</template>

<script>
import SoftButton from "@/components/SoftButton.vue";
import SoftInput from "@/components/SoftInput.vue";
import { StreamBarcodeReader } from "vue-barcode-reader";
import api from "@/utils/api";
import QrcodeVue from 'qrcode.vue';

export default {
  components: { SoftButton, SoftInput, QrcodeVue, StreamBarcodeReader },
  props: {
    showModal: Boolean,
    isEditMode: Boolean,
    eventData: Object,
  },
  data() {
    return {
      // showModal: false,
      // isEditMode: false,
      events: [],
      venues: [],
      activeTab: 'details',
      form: {
        id: null,
        title: "",
        description: "",
        start_date: "",
        end_date: "",
        venue_id: null,
      },
      attendees: [],
      attendances : [],
      newAttendeeName: '',
      // localEditMode: this.isEditMode,
      localEditMode: this.isEditMode,
      localshowModal: this.showModal,
      scanMessage: '',
      scanStatus: '',

    };
  },
  watch: {
    showModal(newVal) {
      console.log('woi');
      if (newVal) {
        console.log('woii');

        this.fetchVenues();
        if (this.eventData) {
        console.log('woiii');

          this.form = {
            id: this.eventData.id,
            title: this.eventData.title,
            description: this.eventData.description,
            start_date: this.eventData.start_date.slice(0, 16),
            end_date: this.eventData.end_date.slice(0, 16),
            venue_id: this.eventData.venue_id,
          };
          this.loadAttendees(this.eventData.id);
        }
      } else {
        this.activeTab = 'details';
        this.newAttendeeName = '';
        this.attendees = [];
        this.scanMessage = '';
        this.scanStatus = '';
        this.form = {
          id: null,
          title: '',
          description: '',
          start_date: '',
          end_date: '',
          venue_id: null,
        };
      }
    },
  },
  mounted() {
    this.fetchEvents();
    this.fetchVenues();
  },
  methods: {
    async fetchEvents() {
      const token = localStorage.getItem("token");
      const res = await api.get("/api/organizer/events", {
        headers: { Authorization: `Bearer ${token}` },
      });
      this.events = res.data;
    },
    async fetchVenues() {
      const token = localStorage.getItem("token");
      const res = await api.get("/api/organizer/venues", {
        headers: { Authorization: `Bearer ${token}` },
      });
      this.venues = res.data;
    },
    openCreateModal() {
      // this.isEditMode = false;
      this.localEditMode = false;

      this.form = {
        id: null,
        title: "",
        description: "",
        start_date: "",
        end_date: "",
        venue_id: null,
      };
      // this.showModal = true;
      this.localshowModal = true;
    },
    openEditModal(event) {
      // this.isEditMode = true;
      this.localEditMode = true;
      this.form = {
        id: event.id,
        title: event.title,
        description: event.description,
        start_date: event.start_date.slice(0, 16),
        end_date: event.end_date.slice(0, 16),
        venue_id: event.venue_id,
      };
      // this.showModal = true;
      this.localshowModal = true;
      // console.log("heree"+ this.localshowModal);
      this.loadAttendees(event.id);
      this.loadAttendances(event.id);

    },
    closeModal() {
      // this.showModal = false;
      this.localshowModal = false;

      this.form = {
        id: null,
        title: "",
        description: "",
        start_date: "",
        end_date: "",
        venue_id: null,
      };
      this.scanMessage = '';
      this.scanStatus = '';
    },
    async loadAttendees(eventId) {
        console.log('ini lepas');

      if (!eventId) return;
      try {
        const token = localStorage.getItem('token');
        const res = await api.get(`/api/organizer/attendees/event/${eventId}`, {
          headers: { Authorization: `Bearer ${token}` },
        });
        this.attendees = res.data;
        console.log('ni lepas');
      } catch (err) {
        console.error('❌ Failed to load attendees:', err);
      }
    },
    async addAttendee() {
      console.log('hi');
      if (!this.newAttendeeName.trim() || !this.form.id) return;
      try {
        const token = localStorage.getItem('token');
        const res = await api.post('/api/organizer/attendees', {
          event_id: this.form.id,
          name: this.newAttendeeName,
        }, {
          headers: { Authorization: `Bearer ${token}` },
        });
        this.attendees.push(res.data);
        this.newAttendeeName = '';
      } catch (err) {
        console.error('❌ Failed to add attendee:', err);
      }
    },
    async loadAttendances(eventId) {
        console.log('ini lepas');

      if (!eventId) return;
      try {
        const token = localStorage.getItem('token');
        const res = await api.get(`/api/organizer/attendance/event/${eventId}`, {
          headers: { Authorization: `Bearer ${token}` },
        });
        this.attendances = res.data;
        console.log(res.data);
      } catch (err) {
        console.error('❌ Failed to load attendees:', err);
      }
    },
    async createEvent() {
      try {
        const token = localStorage.getItem("token");
        await api.post("/api/organizer/events", this.form, {
          headers: { Authorization: `Bearer ${token}` },
        });
        this.closeModal();
        this.fetchEvents();
      } catch (err) {
        console.error("❌ Event creation failed:", err);
      }
    },
    async updateEvent() {
      try {
        const token = localStorage.getItem("token");
        await api.put(`/api/organizer/events/${this.form.id}`, this.form, {
          headers: { Authorization: `Bearer ${token}` },
        });
        this.closeModal();
        this.fetchEvents();
      } catch (err) {
        console.error("❌ Event update failed:", err);
      }
    },
    formatDate(dateStr) {
      return new Date(dateStr).toLocaleString();
    },
    // onDecode (result) { console.log(result) },
    async onDecode(result) {
      if (!this.form.id) {
        this.scanMessage = 'No event selected';
        this.scanStatus = 'error';
        return;
      }
      try {
        const token = localStorage.getItem('token');
        const res = await api.post('/api/attendance/scan', {
          qr_code: result,
          event_id: this.form.id,
        }, {
          headers: { Authorization: `Bearer ${token}` },
        });
        this.scanMessage = res.data.message;
        this.scanStatus = res.data.status;
        // Refresh attendees list to reflect attendance changes
        await this.loadAttendees(this.form.id);
      } catch (err) {
        this.scanMessage = err.response?.data?.message || 'Failed to mark attendance';
        this.scanStatus = 'error';
        console.error('❌ Failed to scan QR code:', err);
      }
      // Clear message after 3 seconds
      setTimeout(() => {
        this.scanMessage = '';
        this.scanStatus = '';
      }, 3000);
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