<script setup>
import { Swiper, SwiperSlide } from 'swiper/vue'
import { Navigation } from 'swiper/modules'
import 'swiper/css'
import 'swiper/css/navigation'
import { usePage } from '@inertiajs/vue3'
defineProps({
  testimonials: {
    type: Array,
    default: []
  },
  logos: {
    type: Array,
    default: []
  }
})
const customNavigation = {
  nextEl: '.next_a',
  prevEl: '.prev_a'
}

const page = usePage()
const data = page.props.primaryData?.slider ?? {
  title: "You're in a good hand",
  sub_title: 'Optimize expense tracking across platforms, & product lines using multiple cards.'
}
</script>

<template>
  <div class="feedback-section-four mt-100 lg-mt-80">
    <div class="container">
      <div class="position-relative">
        <div class="title-two">
          <h2>{{ data.title }}</h2>
        </div>
        <!-- /.title-two -->
        <p class="mt-30 lg-mt-20 mb-70 lg-mb-40 text-lg">
          {{ data.sub_title }}
        </p>
        <div class="feedback-slider-four">
          <Swiper
            :navigation="customNavigation"
            :modules="[Navigation]"
            :slides-per-view="1"
            :space-between="30"
            :breakpoints="{
              '1024': {
                slidesPerView: 2
              },
              '968': {
                slidesPerView: 1
              }
            }"
          >
            <SwiperSlide v-for="(item, index) in testimonials" :key="index">
              <div class="item">
                <div class="feedback-block-four tran3s">
                  <div class="d-flex align-items-center justify-content-between">
                    <ul class="style-none d-flex rating">
                      <li v-for="i in 5" :key="i">
                        <i class="bi" :class="i <= item.rating ? 'bi-star-fill' : 'bi-star'"></i>
                      </li>
                    </ul>
                    <img v-lazy="data.shape" alt="" class="icon" />
                  </div>
                  <blockquote>
                    {{ item.content }}
                  </blockquote>
                  <div class="d-flex align-items-center justify-content-between">
                    <div class="d-flex align-items-center">
                      <div class="name fw-500 text-dark">{{ item.name }},</div>
                      <p class="m0 opacity-75">{{ item.designation }}</p>
                    </div>
                    <img v-lazy="item.image" alt="" class="avatar rounded-circle" />
                  </div>
                </div>
                <!-- /.feedback-block-four -->
              </div>
            </SwiperSlide>
          </Swiper>
        </div>

        <ul class="slider-arrows slick-arrow-one d-flex justify-content-center style-none">
          <li class="prev_a"><i class="bi bi-chevron-left"></i></li>
          <li class="next_a"><i class="bi bi-chevron-right"></i></li>
        </ul>

        <div class="partner-logo-one lg-pt-50 lg-pb-50 pb-80 pt-80">
          <div class="partner-slider-one">
            <Swiper
              :slides-per-view="2"
              :space-between="30"
              :breakpoints="{
                '1024': {
                  slidesPerView: 5
                },
                '800': {
                  slidesPerView: 3
                },
                '360': {
                  slidesPerView: 3
                }
              }"
            >
              <SwiperSlide v-for="(item, index) in logos" :key="index">
                <div class="item">
                  <div class="logo d-flex align-items-center justify-content-center">
                    <img v-lazy="item" alt="" />
                  </div>
                </div>
              </SwiperSlide>
            </Swiper>
          </div>
        </div>
        <!-- /.partner-logo-one -->
      </div>
    </div>
  </div>
</template>
