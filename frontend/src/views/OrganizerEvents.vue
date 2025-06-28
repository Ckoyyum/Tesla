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
                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 text-center">
                  Status
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
                  <span class="text-xs font-weight-bold">{{ event.status }}</span>
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
              Scanner
            </button>
          </li>
          <li class="nav-item">
            <button class="nav-link" :class="{ active: activeTab === 'attendance' }" @click="activeTab = 'attendance'">
              Attendance
            </button>
          </li>
        </ul>

        <!-- Event Details Tab -->
        <div v-if="activeTab === 'details'">
          <h5 class="mb-3">{{ localEditMode ? 'Edit Event' : 'Create New Event' }}</h5>
          <form @submit.prevent="localEditMode ? updateEvent() : createEvent()">
            <label class="form-label">Title</label>
            <soft-input v-model="form.title" label="Title" required />
            <label class="form-label">Description</label>
            <soft-input v-model="form.description" label="Description" required />
            <label class="form-label">Start Date & Time</label>
            <soft-input v-model="form.start_date" label="Start Date & Time" type="datetime-local" required />
            <label class="form-label">End Date & Time</label>
            <soft-input v-model="form.end_date" label="End Date & Time" type="datetime-local" required />
            <label class="form-label">Venue</label>
            <select v-model="form.venue_id" class="form-control" required>
              <option value="">Select a Venue</option>
              <option v-for="venue in venues" :key="venue.id" :value="venue.id">
                {{ venue.name }} ({{ venue.address }})
              </option>
            </select>
            <label class="form-label mt-3">Vendor Services</label>
            <div v-for="service in vendorServices" :key="service.id" class="form-check">
              <input
                type="checkbox"
                :value="service.id"
                v-model="form.vendor_service_ids"
                class="form-check-input"
              >
              <label class="form-check-label">{{ service.name }} ({{ service.price }})</label>
            </div>
            <div class="d-flex justify-content-end mt-4">
              <soft-button color="secondary" class="me-2" @click="closeModal">Cancel</soft-button>
              <soft-button type="submit" color="success" variant="gradient">
                {{ localEditMode ? 'Update' : 'Save' }}
              </soft-button>
            </div>
          </form>
        </div>

        <!-- Attendees Tab -->
        <div v-if="activeTab === 'attendees'">
          <h5 class="mb-3">Attendees List</h5>
          <div class="mb-3">
            <div class="input-group">
              <input v-model="newAttendeeName" type="text" class="form-control" placeholder="Enter attendee name">
              <button class="btn btn-success" @click="addAttendee">Add Attendee</button>
            </div>
          </div>
          <div style="max-height: 400px; overflow-y: auto;">
            <table class="table">
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

        <!-- Scanner Tab -->
        <div v-if="activeTab === 'scanner'">
          <h5 class="mb-3">Scanner</h5>
          <div class="mb-3">
            <StreamBarcodeReader @decode="onDecode" @loaded="onLoaded"></StreamBarcodeReader>
            <div v-if="scanMessage" :class="['mt-3', scanStatus === 'success' ? 'text-success' : 'text-danger']">
              {{ scanMessage }}
            </div>
          </div>
          <div class="d-flex justify-content-end mt-4">
            <soft-button color="secondary" @click="closeModal">Close</soft-button>
          </div>
        </div>

        <!-- Attendance Tab -->
        <div v-if="activeTab === 'attendance'">
          <h5 class="mb-3">Attendance</h5>
          <div style="max-height: 400px; overflow-y: auto;">
            <table class="table">
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
import QrcodeVue from 'qrcode.vue';
import api from "@/utils/api";

export default {
  components: { SoftButton, SoftInput, QrcodeVue, StreamBarcodeReader },
  data() {
    return {
      events: [],
      venues: [],
      vendorServices: [],
      activeTab: 'details',
      localEditMode: false,
      localshowModal: false,
      form: {
        id: null,
        title: "",
        description: "",
        start_date: "",
        end_date: "",
        venue_id: "",
        vendor_service_ids: []
      },
      attendees: [],
      attendances: [],
      newAttendeeName: '',
      scanMessage: '',
      scanStatus: ''
    };
  },
  methods: {
    async fetchEvents() {
      try {
        const token = localStorage.getItem("token");
        const res = await api.get("/api/organizer/events", {
          headers: { Authorization: `Bearer ${token}` },
        });
        this.events = res.data;
      } catch (err) {
        console.error("❌ Failed to fetch events:", err);
      }
    },
    async fetchVenues() {
      try {

        const token = localStorage.getItem('token');
        const res = await api.get('/api/organizer/venues', {
          headers: { Authorization: `Bearer ${token}` },
        });

        this.venues = res.data;
      } catch (err) {
        console.error("❌ Failed to fetch venues:", err);
      }
    },
    async fetchVendorServices() {
      try {
        
        // const res = await api.get("/api/organizer/vendor-services");
        console.log('fetch');
        const token = localStorage.getItem('token');
        const res = await api.get('/api/organizer/vendor-services',  {
          headers: { Authorization: `Bearer ${token}` },
        });
        console.log('fetcs');


        this.vendorServices = res.data;
      } catch (err) {
        console.error("❌ Failed to fetch vendor services:", err);
      }
    },
    openCreateModal() {
      this.localEditMode = false;
      this.localshowModal = true;
      this.activeTab = 'details';
      this.form = {
        id: null,
        title: "",
        description: "",
        start_date: "",
        end_date: "",
        venue_id: "",
        vendor_service_ids: []
      };
      this.attendees = [];
      this.attendances = [];
      this.newAttendeeName = '';
      this.scanMessage = '';
      this.scanStatus = '';
    },
    openEditModal(event) {
      this.localEditMode = true;
      this.localshowModal = true;
      this.activeTab = 'details';
      this.form = {
        id: event.id,
        title: event.title,
        description: event.description,
        start_date: event.start_date.slice(0, 16),
        end_date: event.end_date.slice(0, 16),
        venue_id: event.venue_id || "",
        vendor_service_ids: event.vendor_services ? event.vendor_services.map(service => service.id) : []
      };
      this.loadAttendees(event.id);
      this.loadAttendances(event.id);
    },
    closeModal() {
      this.localshowModal = false;
      this.localEditMode = false;
      this.activeTab = 'details';
      this.form = {
        id: null,
        title: "",
        description: "",
        start_date: "",
        end_date: "",
        venue_id: "",
        vendor_service_ids: []
      };
      this.attendees = [];
      this.attendances = [];
      this.newAttendeeName = '';
      this.scanMessage = '';
      this.scanStatus = '';
    },
    async loadAttendees(eventId) {
      if (!eventId) return;
      try {
        const token = localStorage.getItem('token');
        const res = await api.get(`/api/organizer/attendees/event/${eventId}`, {
          headers: { Authorization: `Bearer ${token}` },
        });
        this.attendees = res.data;
      } catch (err) {
        console.error('❌ Failed to load attendees:', err);
      }
    },
    async addAttendee() {
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
      if (!eventId) return;
      try {
        const token = localStorage.getItem('token');
        const res = await api.get(`/api/organizer/attendance/event/${eventId}`, {
          headers: { Authorization: `Bearer ${token}` },
        });
        this.attendances = res.data;
      } catch (err) {
        console.error('❌ Failed to load attendances:', err);
      }
    },
    async createEvent() {
      try {
        if (!this.form.vendor_service_ids.length) {
          alert("Please select at least one vendor service.");
          return;
        }
        const token = localStorage.getItem("token");
        const formData = new FormData();
        formData.append("title", this.form.title);
        formData.append("description", this.form.description);
        formData.append("start_date", this.form.start_date);
        formData.append("end_date", this.form.end_date);
        formData.append("venue_id", this.form.venue_id);
        this.form.vendor_service_ids.forEach((id, index) => {
          formData.append(`vendor_service_ids[${index}]`, id);
        });
        await api.post("/api/organizer/events", formData, {
          headers: {
            Authorization: `Bearer ${token}`,
            "Content-Type": "multipart/form-data",
          },
        });
        this.closeModal();
        this.fetchEvents();
      } catch (err) {
        console.error("❌ Event creation failed:", err);
        alert("Failed to create event. Please check your input.");
      }
    },
    async updateEvent() {
      try {
        if (!this.form.vendor_service_ids.length) {
          alert("Please select at least one vendor service.");
          return;
        }
        const token = localStorage.getItem("token");
        // const formData = new FormData();
        // formData.append("title", this.form.title);
        // formData.append("description", this.form.description);
        // formData.append("start_date", this.form.start_date);
        // formData.append("end_date", this.form.end_date);
        // formData.append("venue_id", this.form.venue_id);
        // this.form.vendor_service_ids.forEach((id, index) => {
        //   formData.append(`vendor_service_ids[${index}]`, id);
        // });
        // await api.put(`/api/organizer/events/${this.form.id}`, formData, {
        //   headers: {
        //     Authorization: `Bearer ${token}`
        //   },
        // });

        const payload = {
          title: this.form.title,
          description: this.form.description,
          start_date: this.form.start_date,
          end_date: this.form.end_date,
          venue_id: this.form.venue_id,
          vendor_service_ids: this.form.vendor_service_ids,
        };

        await api.put(`/api/organizer/events/${this.form.id}`, payload, {
          headers: {
            Authorization: `Bearer ${token}`,
            "Content-Type": "application/json",
          },
        });

        // for (let pair of formData.entries()) {
        //   console.log(`${pair[0]}:`, pair[1]);
        // }

        // console.log(formData);
        this.closeModal();
        this.fetchEvents();
      } catch (err) {
        console.error("❌ Event update failed:", err);
        alert("Failed to update event. Please check your input.");
      }
    },
    formatDate(dateStr) {
      return new Date(dateStr).toLocaleString();
    },
    async onDecode(result) {
      if (!this.form.id) {
        this.scanMessage = 'No event selected';
        this.scanStatus = 'error';
        return;
      }
      try {
        const token = localStorage.getItem('token');
        const res = await api.post('/api/organizer/attendance/scan', {
          qr_code: result,
          event_id: this.form.id,
        }, {
          headers: { Authorization: `Bearer ${token}` },
        });
        this.scanMessage = res.data.message;
        this.scanStatus = res.data.status;
        await this.loadAttendees(this.form.id);
        await this.loadAttendances(this.form.id);
      } catch (err) {
        this.scanMessage = err.response?.data?.message || 'Failed to mark attendance';
        this.scanStatus = 'error';
        console.error('❌ Failed to scan QR code:', err);
      }
      setTimeout(() => {
        this.scanMessage = '';
        this.scanStatus = '';
      }, 3000);
    },
    onLoaded() {
      // Handle barcode reader loaded event if needed
    }
  },
  mounted() {
    this.fetchEvents();
    this.fetchVenues();
    this.fetchVendorServices();
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
.form-check {
  margin-bottom: 10px;
}
</style>