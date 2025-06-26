<template>
  <div class="offer-section">
      <h2 class="d-flex text-dark fs-1 fw-bold justify-content-center  mb-4 pt-4">We Offer You the Best</h2>
        <div class="features-grid">
        <div 
          v-for="(feature, index) in features"
          :key="index"
          class="feature-card"
        >
          <div class="feature-number">0</div>
          <h3 class="feature-title">{{ feature.title }}</h3>
          <p class="feature-description">{{ feature.description }}</p>
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
          title: "initial fees",
          description: "No initial fees or hidden fees to open an account with us ."
        },
        {
          title: "maintenance fees",
          description: "Enjoy hassle-free payment services with no maintenance fees. "
        },
        {
          title: "minimum deposit",
          description: "Start your payment journey with zero minimum deposit."
        }
      ]
    }
  }
}
</script>

