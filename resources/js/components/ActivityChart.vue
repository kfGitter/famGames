<script setup lang="ts">
import { ref, onMounted, watch, computed } from 'vue';
import Chart from 'chart.js/auto';

const props = defineProps<{ data: { label: string; value: number }[] }>();
const canvas = ref<HTMLCanvasElement | null>(null);
let chart: any;

// Show only last 3 weeks
const last3Weeks = computed(() => props.data.slice(-3));

// Function to format labels concisely (e.g., "15" instead of "Sep 15")
function formatLabel(label: string) {
  const date = new Date(label);
  return isNaN(date.getTime()) ? label : date.getDate().toString();
}

function buildChart() {
  if (!canvas.value) return;
  const labels = last3Weeks.value.map(d => formatLabel(d.label));
  const values = last3Weeks.value.map(d => d.value);
  const ctx = canvas.value.getContext('2d')!;
  if (chart) chart.destroy();
  chart = new Chart(ctx, {
    type: 'line',
    data: {
      labels,
      datasets: [
        {
          label: 'Family games / week',
          data: values,
          borderColor: '#3b82f6', // Tailwind blue-500
          backgroundColor: 'rgba(59, 130, 246, 0.1)', // subtle fill
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
            // Show full date + value in tooltip
            title: (tooltipItems) => {
              const idx = tooltipItems[0].dataIndex;
              return last3Weeks.value[idx].label;
            },
            label: (tooltipItem) => `Games: ${tooltipItem.raw}`,
          },
        },
      },
      scales: {
        y: {
          beginAtZero: true,
          precision: 0,
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
