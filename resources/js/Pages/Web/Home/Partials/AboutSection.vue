<script setup>
import CounterUp from '@/Components/CounterUp.vue'
import WhatWeoffer from '@/Pages/Web/Home/Partials/WhatWeOffer.vue'

const props = defineProps({
  data: {
    type: Object,
    default: {}
  }
})
</script>
<template>
  <div class="text-feature-one mt-40">
    <div class="container textfeatureone">
      <div class="line-wrapper position-relative">
        <div class="row align-items-center">
          <div class="col-lg-7 wow fadeInLeft">
               <WhatWeoffer />            
          </div>
          <div class="col-lg-5 wow fadeInRight">
            <div class="card-style-three d-flex pt-75 lg-pt-40 lg-pb-20">
              <img :src="data.section_1_img" alt="" class="lazy-img icon" />
              <div class="ps-4">
                <h2 class="text-dark fs-1 fw-bold text-left">{{ data.section_1_title }}</h2>
                <p>{{ data.section_1_sub_title }}</p>
              </div>
            </div>
            <!-- /.card-style-three -->
            <div class="card-style-three d-flex  lg-pt-40 pb-45 lg-pb-20 pt-30">
              <img :src="data.section_2_img" alt="" class="lazy-img icon" />
              <div class="ps-4">
                <h2 class="text-dark fs-1 fw-bold text-left">{{ data.section_2_title }}</h2>
                <p>{{ data.section_2_sub_title }}</p>
              </div>
            </div>
            <!-- /.card-style-three -->
          </div>
        </div>
        <img :src="data.shape_1" alt="" class="lazy-img shapes shape_01" />
      </div>
    </div>
  </div>
</template>
