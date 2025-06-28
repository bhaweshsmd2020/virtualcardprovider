<script setup>
import sharedComposable from '@/Composables/sharedComposable'
import { Swiper, SwiperSlide } from 'swiper/vue'
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
  <div class="feedback-section-three position-relative lg-pt-100">
    <div class="container mt-60 feedback-section-three-testimonial">
      <div class="position-relative">
        <div class="row">
          <div class="col-lg-6">
            <div class="mb-45 lg-mb-50 mt-35 md-mt-30">
              <h2 class="text-dark fs-1 fw-bold">{{ data.title }}</h2>
            </div>
            <!-- /.title-one -->
            <div class="slider-wrapper position-relative">
              <img src="/assets/images/icon/icon_39.svg" alt="" class="lazy-img shapes icon" />
              <Swiper
                @slideChange="onSwiperUpdate"
                ref="swiperRef"
                :slides-per-view="1"
                class="feedback-slider-three"
              >
                <SwiperSlide class="item" v-for="(item, i) in testimonials" :key="i">
                  <blockquote class="fs-3" style="font-size: 22px !important;">{{ item.content }}</blockquote>
                </SwiperSlide>
              </Swiper>
              <ul class="slick-dots" v-if="testimonials?.length > 1">
                <li
                  v-for="(testimonial, index) in testimonials"
                  :key="index"
                  :class="{ 'slick-active': currentIndex === index }"
                >
                  <button type="button" @click="swiperRef.$el.swiper.slideTo(index)">
                    {{ index + 1 }}
                  </button>
                </li>
              </ul>
            </div>
          </div>

          <div class="col-xl-6 col-lg-5 text-lg-end ms-auto text-center">
            <div class="media-wrapper d-inline-block position-relative md-mt-40">
              <img src="/assets/images/media/virtual card.webp" alt="" class="lazy-img" />
              <div class="rating-box text-center">
                <div class="rating fw-bold text-white">{{ trans('4.8') }}</div>
                <div class="text-white">{{ trans('avg rating') }}</div>
              </div>
              <!-- /.rating-box -->
            </div>
            <!-- /.media-wrapper -->
          </div>
        </div>

        <!-- <div class="partner-logo-one pt-150 md-pt-80">
          <p class="fw-500 text-dark sm-mb-30 mb-40 text-lg" v-html="data.sub_title"></p>
          <Swiper
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
        </div> -->
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
