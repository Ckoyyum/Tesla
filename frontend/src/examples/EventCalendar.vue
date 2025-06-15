<template>
  <div class="card p-4">
    <FullCalendar
      :options="calendarOptions"
      class="w-full"
    />
  </div>
</template>

<script>
import FullCalendar from '@fullcalendar/vue3';
import dayGridPlugin from '@fullcalendar/daygrid';
import interactionPlugin from '@fullcalendar/interaction'; // for drag/drop and clicking
import axios from 'axios'; // Make sure axios is installed

export default {
  name: 'EventCalendar',
  components: { FullCalendar },
  data() {
    return {
      calendarOptions: {
        plugins: [dayGridPlugin, interactionPlugin],
        initialView: 'dayGridMonth',
        headerToolbar: {
          left: 'prev,next today',
          center: 'title',
          right: ''
        },
        events: [
          {
            title: 'Venue Booked: Wedding',
            start: '2025-06-01',
            end: '2025-06-05', // exclusive end
            color: '#4caf50' // optional
          },
          {
            title: 'Corporate Event',
            start: '2025-06-10',
            end: '2025-06-12',
            color: '#2196f3'
          }
        ],
        editable: false,
        selectable: false,
        aspectRatio: 1.5,
        eventDisplay: 'block',
      }
    };
  },
  async mounted() {
    try {
      // Get user info from localStorage or store (e.g., Vuex or Pinia)


      const token = localStorage.getItem('token');

      const res = await axios.get('http://localhost:8000/api/organizer/events', {
        headers: {
          Authorization: `Bearer ${token}`
        }
      });

      console.log(res.data); 


      // Map your data to FullCalendar format
      const events = res.data.map(event => ({
        title: event.title,
        start: event.start_date.split(' ')[0],
        end: event.end_date.split(' ')[0],
        color: event.color || '#4caf50',
      }));

      // Inject events into calendar
      this.calendarOptions.events = events;
    } catch (error) {
      console.error('❌ Error fetching calendar events:', error);
    }
  },
};
</script>

<style>
/* @import '@fullcalendar/core';
@import '@fullcalendar/daygrid'; */

/* @import '@fullcalendar/core/index.css';
@import '@fullcalendar/daygrid/index.css'; */

</style>
