<script setup lang="ts">
import { Head } from "@inertiajs/vue3";
import UserLayout from "@/Layouts/User/UserLayout.vue";
import trans from "@/Composables/transComposable";

const props = defineProps(["type"])

defineOptions({ layout: UserLayout });
</script>

<template>
  <Head title="KYC verification | User Panel" />

  <main class="container flex-grow p-3 sm:p-4">
    <div class="flex items-center justify-between">
      <h2 class="main-title">{{ trans("KYC verification Tips") }}</h2>     
      <Link :href="route('user.kyc.index')" class="btn btn-primary">
        {{ trans("Back") }}
      </Link> 
    </div>

    <!-- Main Card -->
    <div class="card-container">
      <div class="card-body">
        <h4 class="fw-semibold text-center">Quick tips for successful verifications</h4>

        <!-- First Row (Good Example) -->
        <div class="row">
          <div class="column">
            <div class="card">
              <img src="/assets/images/good-front.png" class="card-img" alt="Good Example">
            </div>
          </div>
          <div class="column text">
            <p class="success">✔ Good Example</p>
            <p class="description">
              Fully in frame, with full name, date of birth, and ID number clearly showing.
            </p>
          </div>
          <div class="column">
            <div class="card">
              <img src="/assets/images/good-back.png" class="card-img" alt="Good Example">
            </div>
          </div>
        </div>

        <!-- Second Row (Blurry Photo) -->
        <div class="row">
          <div class="column">
            <div class="card">
              <img src="/assets/images/good-front.png" class="card-img blurry" alt="Blurry Example">
            </div>
          </div>
          <div class="column text">
            <p class="error">✖ Blurry Photo</p>
            <p class="description">Hold your ID steady so the information is clearly visible.</p>
          </div>
          <div class="column">
            <div class="card">
              <img src="/assets/images/good-back.png" class="card-img blurry" alt="Blurry Example">
            </div>
          </div>
        </div>

        <!-- Third Row (Bad Quality) -->
        <div class="row">
          <div class="column">
            <div class="card">
              <img src="/assets/images/good-front.png" class="card-img bad-quality" alt="Bad Quality">
            </div>
          </div>
          <div class="column text">
            <p class="error">✖ Bad Quality</p>
            <p class="description">Take a well-lit picture without glare obstructing the ID information.</p>
          </div>
          <div class="column">
            <div class="card">
              <img src="/assets/images/good-back.png" class="card-img bad-quality" alt="Bad Quality">
            </div>
          </div>
        </div>      
      </div>
    </div>

    <div class="row">
      <div class="column text">
        <Link :href="route('user.kyc.types', type)" class="btn-primary">
          {{ trans("Verify Now") }}
        </Link>
      </div>
    </div>
  </main>
</template>

<style scoped>
/* Layout Fixes */
.header-container {
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.btn-primary {
  padding: 6px 12px;
  font-size: 14px;
  background: #6a1b9a;
  color: white;
  border-radius: 4px;
  text-decoration: none;
}

.card-container {
  max-width: 720px;
  margin: 25px auto;
  padding: 16px;
  background: white;
  border-radius: 8px;
  box-shadow: 0 3px 6px rgba(0, 0, 0, 0.1);
}

/* Custom Grid for Consistent Layout */
.row {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin: 16px 0;
}

.column {
  flex: 1;
  text-align: center;
}

.card {
  padding: 8px;
  border-radius: 8px;
  background: #f8f8f8;
  display: flex;
  justify-content: center;
  align-items: center;
}

.card-img {
  max-width: 160px;
  height: auto;
  width: 100%;
}

/* Text Styling */
.text {
  padding: 8px;
}

.success {
  color: green;
  font-weight: bold;
  font-size: 14px;
}

.error {
  color: red;
  font-weight: bold;
  font-size: 14px;
}

.description {
  color: #666;
  font-size: 13px;
}

/* Image Effects */
.blurry {
  filter: blur(3px);
}

.bad-quality {
  filter: blur(1px) contrast(0.7) grayscale(40%);
}

/* Alert Box */
.stats {
  margin-top: 16px;
  text-align: center;
}

.alert {
  padding: 8px;
  background: #f0f0f0;
  border-radius: 4px;
  margin: 4px 0;
  font-size: 14px;
}

.card-body{
  padding: 0px;
}
</style>
