<script setup>
import CounterUp from '@/Components/CounterUp.vue'
defineProps({
  data: {
    type: Object,
    default: {}
  }
})
</script>
<template>
  <div class="hero-banner-three pt-160 md-pt-130 xl-pb-50 position-relative pb-0  md-mt-6y0">
    <div class="position-relative container-fluid px-lg-5">
      <div class="row align-items-center">
        <div class="col-lg-6 d-flex flex-column justify-content-center" style="min-height: 100%;">
          <h1 class="hero-heading">{{ data.title }}</h1>
          <p class="pt-10 lg-pt-30 pb-20 lg-pb-20 text-md mb-2">{{ data.sub_title }}</p>
          <Link :href="data.btn_link" class="btn-ten tran3s rounded">{{ data.btn_text }}</Link>
        </div>
        <div class="col-xl-6 wow fadeInRight">
          <div class="right-widget sm-mt-40 ps-xxl-5 ps-xl-0 ps-lg-4">
            <div>
              <a :href="data.img_3_action_link">
                <img :src="data.img_3" alt="" class="lazy-img screen_01" />
              </a>
            </div>
          </div>
        </div>
      </div>
    </div>    
  </div>
</template>

<style>
@media (max-width: 400px) {
  .hero-banner-three .hero-heading {
  font-weight: 800;
  font-size: 40px;
}
}
@media (min-width: 600px) {
  .hero-banner-three .hero-heading {
  font-weight: 800;
  font-size: 60px;
}
}


.btn-ten {
  width: fit-content;
}
</style>