<script setup>
import CounterUp from '@/Components/CounterUp.vue'
const props = defineProps({
  data: {
    type: Object,
    default: {}
  }
})
</script>

<template>
  <div class="text-feature-four position-relative lg-pt-80 pt-80">
    <div class="container">
      <div class="row">
        <div class="col-md-8 order-md-last wow fadeInRight">
          <p class="text-dark ps-xl-5 fs-3" v-html="data.right_text"></p>
        </div>
        <div class="col-md-4 order-md-first wow fadeInLeft">
          <div class="d-flex align-items-center sm-mt-30 mt-10">
            <img :src="data.img" alt="" class="lazy-img avatar rounded-circle" />
            <div class="ps-3">
              <div class="fs-5 fw-500 text-dark">{{ data.name }}</div>
              <p class="fs-6">{{ data.position }}</p>
            </div>
          </div>
        </div>
      </div>

      <div class="counter-wrapper mt-70 lg-mt-40">
        <div class="row">
          <div class="col-md-3 col-6" v-for="(counter, index) in data.counters" :key="index">
            <div
              class="counter-block-two mt-35 wow fadeInUp text-center"
              :class="index == 0 ? 'text-md-start' : index == 3 ? 'text-md-end' : ''"
              :data-wow-delay="`0.${index}s`"
            >
              <div class="main-count fw-500 text-dark">
                <CounterUp :target="counter.number" />{{ counter.suffix }}
              </div>
              <p class="m0" v-html="counter.description"></p>
            </div>
            <!-- /.counter-block-two -->
          </div>
        </div>
      </div>

      <!-- /.counter-wrapper -->
    </div>
    <img :src="data.shape_1" alt="" class="lazy-img shapes shape_01" />
    <img :src="data.shape_2" alt="" class="lazy-img shapes shape_02" />
  </div>
</template>
