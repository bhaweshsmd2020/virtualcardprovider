<template>
  <div class="offer-section mt-80">
    <div class="container">
      <h2 class="d-flex text-dark fs-1 fw-bold justify-content-center mt-4 mb-4">Our Offices</h2>
      
      <div class="features-grid">
        <div 
          v-for="(feature, index) in features"
          :key="index"
          class="feature-card"
        >
          <h3 class="feature-title">{{ feature.title }}</h3>
          <p class="feature-description">{{ feature.description }}</p>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  data() {
    return {
      features: [
        {
          title: "United States",
          description: "4500 Satellite Boulevard, Suite 2130,Duluth, GA 30096, USA"
        },
        {
          title: "United Kingdom",
          description: "71-75, Shelton Street, Covent Garden,London, WC2H 9JQ United Kingdom"
        },
        {
          title: "Estonia",
          description: "Harju maakond, Kesklinna linnaosa,Pärnu mnt 158, Tallinn, Estonia"
        }
      ]
    }
  }
}
</script>

