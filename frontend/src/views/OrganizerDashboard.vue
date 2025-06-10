<template>
  <section class="p-6 space-y-8">
    <header>
      <h2 class="text-2xl font-bold">Organizer Dashboard</h2>
      <p class="text-lg">Welcome, {{ user.username }}! 🎉</p>
    </header>

    <!-- CREATE EVENT BUTTON -->
    <button
      @click="showCreate = true"
      class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700"
    >
      + Create Event
    </button>

    <!-- EVENTS LIST -->
    <div v-if="loading">Loading events…</div>
    <div v-else-if="events.length === 0">No events yet.</div>

    <div v-else class="space-y-4">
      <div v-for="e in events" :key="e.id" class="border rounded p-4 shadow">
        <h3 class="text-xl font-semibold">{{ e.title }}</h3>
        <p class="text-gray-700">{{ e.description }}</p>
        <p>
          <strong>Date:</strong>
          {{ format(e.start_date) }} → {{ format(e.end_date) }}
        </p>
        <p>
          <strong>Status:</strong>
          <span :class="statusColor(e.status)">{{ e.status }}</span>
        </p>
        <p v-if="e.venue"><strong>Venue:</strong> {{ e.venue.name }}</p>

        <!-- simple delete -->
        <button
          @click="deleteEvent(e.id)"
          class="mt-2 px-3 py-1 bg-red-600 text-white rounded hover:bg-red-700"
        >
          Delete
        </button>
      </div>
    </div>

    <!-- CREATE EVENT MODAL -->
    <CreateEventModal
      v-if="showCreate"
      @close="showCreate = false"
      @created="eventCreated"
    />
  </section>
</template>

<script setup>
import { ref, computed, onMounted } from "vue";
import axios from "axios";
import CreateEventModal from "@/components/CreateEventModal.vue";

const user = computed(() => JSON.parse(localStorage.getItem("user") || "{}"));

const events = ref([]);
const loading = ref(true);
const showCreate = ref(false);

const headers = {
  Authorization: `Bearer ${localStorage.getItem("token")}`,
};

async function fetchEvents() {
  loading.value = true;
  try {
    const { data } = await axios.get("/api/organizer/events", { headers });
    events.value = data;
  } catch (err) {
    console.error(err);
  } finally {
    loading.value = false;
  }
}

function format(dt) {
  return new Date(dt).toLocaleString();
}
function statusColor(s) {
  return {
    approved: "text-green-600 font-bold",
    rejected: "text-red-600 font-bold",
    pending: "text-yellow-600 font-bold",
  }[s];
}

async function deleteEvent(id) {
  if (!confirm("Delete this event?")) return;
  try {
    await axios.delete(`/api/events/${id}`, { headers });
    events.value = events.value.filter((e) => e.id !== id);
  } catch (err) {
    alert("Could not delete event.");
  }
}

function eventCreated(newEvent) {
  events.value.unshift(newEvent); // instant UI update
  showCreate.value = false;
}

onMounted(fetchEvents);
</script>
