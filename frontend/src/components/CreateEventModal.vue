<template>
  <!-- simple overlay -->
  <div class="fixed inset-0 bg-black/40 flex items-center justify-center">
    <div class="bg-white text-black rounded-lg p-6 w-full max-w-md">
      <h2 class="text-xl font-semibold mb-4">Create Event</h2>

      <form @submit.prevent="submit" class="space-y-3">
        <input
          v-model="form.title"
          required
          placeholder="Title"
          class="w-full p-2 border rounded"
        />
        <textarea
          v-model="form.description"
          required
          placeholder="Description"
          class="w-full p-2 border rounded"
        ></textarea>
        <input
          v-model="form.start_date"
          type="datetime-local"
          required
          class="w-full p-2 border rounded"
        />
        <input
          v-model="form.end_date"
          type="datetime-local"
          required
          class="w-full p-2 border rounded"
        />
        <input
          v-model="form.venue_id"
          type="number"
          placeholder="Venue ID"
          class="w-full p-2 border rounded"
        />

        <div class="flex gap-3 justify-end">
          <button
            type="button"
            @click="$emit('close')"
            class="px-3 py-1 border rounded"
          >
            Cancel
          </button>
          <button
            :disabled="submitting"
            class="px-4 py-1 bg-blue-600 text-white rounded"
          >
            {{ submitting ? "Saving…" : "Save" }}
          </button>
        </div>
      </form>
    </div>
  </div>
</template>

<script setup>
import { reactive, ref } from "vue";
import axios from "axios";

const emit = defineEmits(["close", "created"]);

const headers = {
  Authorization: `Bearer ${localStorage.getItem("token")}`,
};

const form = reactive({
  title: "",
  description: "",
  start_date: "",
  end_date: "",
  venue_id: "",
});

const submitting = ref(false);

async function submit() {
  submitting.value = true;
  try {
    const { data } = await axios.post("/api/events", form, { headers });
    emit("created", data);
  } catch (err) {
    alert(err.response?.data?.message || "Failed to create event.");
  } finally {
    submitting.value = false;
  }
}
</script>
