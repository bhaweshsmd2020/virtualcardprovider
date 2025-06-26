<script setup lang="ts">
import { Head, Link } from "@inertiajs/vue3";
import UserLayout from "@/Layouts/User/UserLayout.vue";
import trans from "@/Composables/transComposable";
import "@fortawesome/fontawesome-free/css/all.min.css";
import { ref } from "vue";

defineOptions({ layout: UserLayout });

const { kyc_methods } = defineProps(["kyc_methods", "type"]);

const selectedMethod = ref(kyc_methods[0] ? kyc_methods[0] : {});

const setSelectedMethod = (method) => {
  selectedMethod.value = method;
};
</script>

<template>
  <Head title="KYC verification | User Panel" />

  <main class="container flex-grow p-3 sm:p-4">

    <div class="flex items-center justify-between">
      <h2 class="main-title">{{ trans("KYC verification") }}</h2>     
      <Link :href="route('user.kyc.tips', type)" class="btn btn-primary">
        {{ trans("Back") }}
      </Link> 
    </div>

    <!-- Main Card -->
    <div class="card-container">
      <div class="card-body">
        <h4 class="fw-semibold text-center mb-4">Select your ID type</h4>

        <div class="id-list">
          <Link
            v-for="(method, index) in kyc_methods"
            :key="index"
            :href="route('user.kyc.create', method.id)"
            class="id-option"
          >

            <span class="icon">
              <img v-lazy="method.image" class="h-8" />
            </span>
            <div class="text-content">
              <strong>{{ method.title }}</strong>
              <p>Photo page</p>
            </div>
            <span class="arrow"><i class="fa fa-angle-right"></i></span>
          </Link>
        </div>

        <p class="disclaimer">
          By selecting your ID type on this screen, you agree to our collection, use, and storage of your biometric information for identity verification. Learn more in our 
          <a  target="_blank" href="/prepaid-card-privacy-policy" class="link">Privacy Policy</a>.
        </p>

        <Link :href="route('user.kyc.index')">
          <button class="no-id-option">I don’t have any of these IDs</button>
        </Link>        
      </div>
    </div>
  </main>
</template>

<style scoped>
/* Layout Adjustments */
.card-container {
  max-width: 500px;
  margin: 20px auto;
  padding: 20px;
  background: white;
  border-radius: 8px;
  box-shadow: 0 3px 6px rgba(0, 0, 0, 0.1);
  text-align: center;
}

/* ID Selection List */
.id-list {
  display: flex;
  flex-direction: column;
  gap: 12px;
}

.id-option {
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 12px;
  border: 1px solid #ddd;
  border-radius: 8px;
  background: white;
  cursor: pointer;
  text-align: left;
  transition: 0.2s;
  width: 100%;
}

.id-option:hover {
  background: #f5f5f5;
}

/* Aligning icon and text properly */
.icon {
  font-size: 20px;
  margin-right: 12px;
  color: #6a1b9a;
}

/* Ensuring text aligns properly */
.text-content {
  flex-grow: 1;
  display: flex;
  flex-direction: column;
  justify-content: center;
}

.text-content strong {
  font-size: 16px;
}

.text-content p {
  font-size: 12px;
  color: #666;
  margin: 0;
}

.arrow i {
  font-size: 18px;
  color: #888;
}

/* No ID Option */
.no-id-option {
  margin-top: 12px;
  width: 100%;
  padding: 10px;
  background: #e0e0e0;
  border: none;
  border-radius: 8px;
  cursor: pointer;
  font-weight: bold;
}

.no-id-option:hover {
  background: #d6d6d6;
}

/* Text */
.disclaimer {
  font-size: 12px;
  color: #666;
  margin-top: 12px;
  padding: 12px;
  border: 1px solid #ddd;
  border-radius: 8px;
}

.link {
  color: #007bff;
  text-decoration: none;
}

.link:hover {
  text-decoration: underline;
}

/* Primary Button */
.btn-primary {
  padding: 8px 16px;
  background: #6a1b9a;
  color: white;
  border-radius: 4px;
  text-decoration: none;
  display: inline-block;
}

.card-body{
  padding: 0px;
}
</style>
