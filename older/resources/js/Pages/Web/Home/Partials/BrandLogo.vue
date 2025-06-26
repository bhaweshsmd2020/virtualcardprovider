<script setup>
import sharedComposable from '@/Composables/sharedComposable'
import { Swiper, SwiperSlide } from 'swiper/vue'
import { Autoplay } from 'swiper/modules' // ✅ Correct module path
import 'swiper/css'

import { ref } from 'vue'

defineProps({
  data: {
    type: Object,
    default: {}
  },
  testimonials: {
    type: Array,
    default: []
  },
  logos: {
    type: Array,
    default: []
  }
})
const { textExcerpt } = sharedComposable()

const currentIndex = ref(0)
const swiperRef = ref(null)
const onSwiperUpdate = (swiper) => {
  currentIndex.value = swiper.realIndex
}
</script>

<template>
  <div class="block-feature-six feedback-section-three position-relative pt-30 pb-20 md pb-80">
    <div class="container">
      <div class="position-relative">
        <div class="partner-logo-one pt-20 md-pt-80">
          <h2 class="text-dark fs-1 fw-bold mb-30" >Trusted by</h2>
          <Swiper
           :modules="[Autoplay]"
           :loop="true"
            :autoplay="{
              delay: 0,
              disableOnInteraction: true
            }"
            :speed="5000"
            :space-between="30"
            :allow-touch-move="false" 
            :slides-per-view="5"
            :breakpoints="{
              '1280': {
                slidesPerView: 5
              },
              '480': {
                slidesPerView: 3
              },
              '320': {
                slidesPerView: 2
              }
            }"
            class="partner-slider-one"
          >
            <SwiperSlide class="item" v-for="(logo, i) in logos" :key="i">
              <div class="item">
                <div
                  class="logo d-flex align-items-center justify-content-center justify-content-lg-start"
                >
                  <img v-lazy="logo" alt="brand" />
                </div>
              </div>
            </SwiperSlide>
          </Swiper>
        </div>
        <!-- /.partner-logo-one -->
      </div>
    </div>

    <img
      v-lazy="'/assets/images/shape/shape_13.svg'"
      alt="shape_13"
      class="lazy-img shapes shape_01"
    />
    <img
      v-lazy="'/assets/images/shape/shape_13.svg'"
      alt="shape_13"
      class="lazy-img shapes shape_02"
    />
  </div>
</template>
