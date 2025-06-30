<template>
  <div class="py-4 container-fluid">
    <!-- <div class="row">
      <div class="col-xl-3 col-sm-6 mb-xl-0 mb-4">
        <mini-statistics-card
          title="Today's Money"
          value="$53,000"
          :percentage="{
            value: '+505%',
            color: 'text-success',
          }"
          :icon="{
            component: 'ni ni-money-coins',
            background: iconBackground,
          }"
          direction-reverse
        />
      </div>
      <div class="col-xl-3 col-sm-6 mb-xl-0 mb-4">
        <mini-statistics-card
          title="Today's Users"
          value="2,300"
          :percentage="{
            value: '+3%',
            color: 'text-success',
          }"
          :icon="{
            component: ' ni ni-world',
            background: iconBackground,
          }"
          direction-reverse
        />
      </div>
      <div class="col-xl-3 col-sm-6 mb-xl-0 mb-4">
        <mini-statistics-card
          title="New Clients"
          value="+3,462"
          :percentage="{
            value: '-2%',
            color: 'text-danger',
          }"
          :icon="{
            component: 'ni ni-paper-diploma',
            background: iconBackground,
          }"
          direction-reverse
        />
      </div>
      <div class="col-xl-3 col-sm-6 mb-xl-0">
        <mini-statistics-card
          title="Sales"
          value="$103,430"
          :percentage="{
            value: '+5%',
            color: 'text-success',
          }"
          :icon="{
            component: 'ni ni-cart',
            background: iconBackground,
          }"
          direction-reverse
        />
      </div>
    </div> -->
    <div class="row my-4">
      <div class="col-lg-8 col-md-6 mb-md-0 mb-4">
        <!-- <projects-card /> -->
         <!-- <event-calendar /> -->
         <!-- <event-calendar :events="calendarEvents" /> -->
         <event-calendar
            v-if="calendarEvents && calendarEvents.length"
            :events="calendarEvents"
          />

      </div>
      <div class="col-lg-4 col-md-6">
        <pie-chart
          v-if="ratings.venue && ratings.services && ratings.management"
          id="chart-pie"
          title="Event Ratings"
          description="Average Ratings for Your Events"
          :chart="{
            // labels: ['Mobile', 'Desktop', 'Tablet'],
            // datasets: [
            //   {
            //     label: 'Users',
            //     data: [ratings.venue, 5.0, 5.0],
            //     backgroundColor: ['#4caf50', '#2196f3', '#ff9800'],
            //     borderWidth: 1,
            //   },
            // ],
            labels: ['Venue', 'Services', 'Management'],
            datasets: [
              {
                label: 'Average Rating',
                data: [ratings.venue, ratings.services, ratings.management],
                backgroundColor: ['#4caf50', '#2196f3', '#ff9800'],
                borderWidth: 1,
              },
            ],
          }"
        />
      </div>
    </div>
  </div>
</template>
<script>
// import MiniStatisticsCard from "@/examples/Cards/MiniStatisticsCard.vue";
import PieChart from "@/examples/Charts/PieChart.vue"; 
import EventCalendar from "@/examples/EventCalendar.vue";
import api from "@/utils/api";
import US from "../assets/img/icons/flags/US.png";
import DE from "../assets/img/icons/flags/DE.png";
import GB from "../assets/img/icons/flags/GB.png";
import BR from "../assets/img/icons/flags/BR.png";
import {
  faHandPointer,
  faUsers,
  faCreditCard,
  faScrewdriverWrench,
} from "@fortawesome/free-solid-svg-icons";
export default {
  name: "dashboard-default",
  data() {
    return {
      iconBackground: "bg-gradient-success",
      faCreditCard,
      faScrewdriverWrench,
      faUsers,
      faHandPointer,
      sales: {
        us: {
          country: "United States",
          sales: 2500,
          value: "$230,900",
          bounce: "29.9%",
          flag: US,
        },
        germany: {
          country: "Germany",
          sales: "3.900",
          value: "$440,000",
          bounce: "40.22%",
          flag: DE,
        },
        britain: {
          country: "Great Britain",
          sales: "1.400",
          value: "$190,700",
          bounce: "23.44%",
          flag: GB,
        },
        brasil: {
          country: "Brasil",
          sales: "562",
          value: "$143,960",
          bounce: "32.14%",
          flag: BR,
        },
      },
      ratings: {
        venue: 0,
        services: 0,
        management: 0
      },
      calendarEvents: []
    };
  },
  components: {
    // MiniStatisticsCard,
    PieChart,
    EventCalendar,
  },
  methods: {
    async fetchRatings() {
      try {
        const token = localStorage.getItem("token");
        const res = await api.get("/api/organizer/surveys/ratings", {
          headers: { Authorization: `Bearer ${token}` },
        });
        this.ratings = {
          venue: parseFloat(res.data.venue_rating || 0).toFixed(1),
          services: parseFloat(res.data.services_rating || 0).toFixed(1),
          management: parseFloat(res.data.management_rating || 0).toFixed(1)
        };
        // console.log(this.ratings);
      } catch (err) {
        console.error("❌ Failed to fetch ratings:", err);
      }
    },
    async fetchEvents() {
      try {
        const token = localStorage.getItem("token");
        const res = await api.get("/api/organizer/events", {
          headers: { Authorization: `Bearer ${token}` },
        });
        this.calendarEvents = res.data
          .filter(event => event.status === 'ready')
          .map(event => ({
            title: event.title,
            start: event.start_date.split(' ')[0],
            end: event.end_date.split(' ')[0],
            color: event.color || '#4caf50'
          }));
        console.log(this.calendarEvents);
      } catch (err) {
        console.error("❌ Error fetching calendar events:", err);
      }
    }
  },
  mounted() {
    this.fetchRatings();
    this.fetchEvents();
  }
};
</script>
