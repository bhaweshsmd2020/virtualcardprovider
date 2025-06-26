<template>
  <div class="chart-container">   

    <div class="flex flex-wrap justify-between gap-2">
        <h6>{{ trans('Overview Of Card Topup')}}</h6>
        <select v-model="selectedMonths" @change="fetchData" id="monthSelector" class="select">
            <option v-for="m in [3, 6, 12, 24]" :key="m" :value="m">{{ m }} months</option>
        </select>
    </div>

    <canvas ref="incomeChart" class="income-chart"></canvas>
  </div>
</template>

<script>
import { Chart, registerables } from 'chart.js'
Chart.register(...registerables)

export default {
  name: 'IncomeChart',
  data() {
    return {
      selectedMonths: 6,
      chart: null,
    }
  },
  mounted() {
    this.$nextTick(() => {
      this.fetchData()
      window.addEventListener('resize', this.resizeChart)
    })
  },
  beforeUnmount() {
    window.removeEventListener('resize', this.resizeChart)
  },
  methods: {
    resizeChart() {
      if (this.chart) {
        this.chart.resize()
      }
    },
    async fetchData() {
      if (this.chart) {
        this.chart.destroy()
      }

      try {
        const response = await fetch(`/user/income-per-month?months=${this.selectedMonths}`, {
          headers: {
            'Accept': 'application/json'
          }
        })

        const data = await response.json()
        const labels = data.map(item => item.month)
        const values = data.map(item => item.income)

        this.chart = new Chart(this.$refs.incomeChart.getContext('2d'), {
          type: 'line',
          data: {
            labels,
            datasets: [{
              label: 'Monthly Card Topup',
              data: values,
              borderColor: '#026f5e',
              backgroundColor: '#bcf59b',
              fill: true,
              tension: 0.3,
              pointRadius: 5,
              pointHoverRadius: 7,
            }]
          },
          options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
              y: {
                beginAtZero: true
              }
            },
            plugins: {
              legend: {
                display: true,
              }
            }
          }
        })
      } catch (err) {
        console.error('Error fetching income data:', err)
      }
    }
  }
}
</script>

<style scoped>
.chart-container {
  width: 100%;
  max-width: 100%;
  height: 400px;
  position: relative;
}

.income-chart {
  width: 100% !important;
  height: 100% !important;
}

.select{
  float: right;
  width: 150px;
}
</style>
