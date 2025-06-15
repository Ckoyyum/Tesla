<template>
  <div class="card">
    <div class="card-header">
      <h5>{{ title }}</h5>
      <p v-html="description"></p>
    </div>
    <div class="card-body">
      <canvas :id="id"></canvas>
    </div>
  </div>
</template>

<script>
import { Chart, PieController, ArcElement, Tooltip, Legend } from 'chart.js';

Chart.register(PieController, ArcElement, Tooltip, Legend);

export default {
  name: "PieChart",
  props: {
    id: String,
    title: String,
    description: String,
    chart: Object,
  },
  mounted() {
    new Chart(this.id, {
      type: 'pie',
      data: {
        labels: this.chart.labels,
        datasets: this.chart.datasets,
      },
      options: {
        responsive: true,
        plugins: {
          legend: {
            position: 'bottom',
          },
        },
      },
    });
  },
};
</script>
