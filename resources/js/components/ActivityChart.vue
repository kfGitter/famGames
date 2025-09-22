<script setup lang="ts">
import { ref, onMounted, watch } from 'vue';
import Chart from 'chart.js/auto';

const props = defineProps<{ data: { label: string; value: number }[] }>();
const canvas = ref<HTMLCanvasElement | null>(null);
let chart: any;

function buildChart() {
  if (!canvas.value) return;

  const labels = props.data.map(d => d.label);
  const values = props.data.map(d => d.value);

  const ctx = canvas.value.getContext('2d')!;
  if (chart) chart.destroy();

  chart = new Chart(ctx, {
    type: 'line',
    data: {
      labels,
      datasets: [
        {
          label: 'Games per day',
          data: values,
          borderColor: '#3b82f6',
          backgroundColor: 'rgba(59, 130, 246, 0.1)',
          fill: true,
          tension: 0.3,
          borderWidth: 2,
          pointRadius: 4,
          pointBackgroundColor: '#3b82f6',
        },
      ],
    },
    options: {
      responsive: true,
      maintainAspectRatio: false,
      plugins: {
        legend: { display: false },
        tooltip: {
          callbacks: {
            title: (items) => props.data[items[0].dataIndex].label,
            label: (item) => `Games: ${item.raw}`,
          },
        },
      },
      scales: {
        y: {
          beginAtZero: true,
          ticks: { stepSize: 1 },
          grid: { color: 'rgba(0,0,0,0.05)' },
        },
        x: {
          grid: { color: 'rgba(0,0,0,0.05)' },
        },
      },
    },
  });
}

onMounted(buildChart);
watch(() => props.data, buildChart, { deep: true });
</script>

<template>
  <div class="h-40">
    <canvas ref="canvas"></canvas>
  </div>
</template>
