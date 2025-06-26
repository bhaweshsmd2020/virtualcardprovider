<script setup>
import FancyBannerThree from '@/Components/Web/FancyBannerThree.vue'
import InnerBannerTwo from '@/Components/Web/InnerBannerTwo.vue'
import DefaultLayout from '@/Layouts/Default/DefaultLayout.vue'
import SliderSection from '../Home/Partials/SliderSection.vue'
import sharedComposable from '@/Composables/sharedComposable'
import Pagination from '@/Components/Web/Pagination.vue'

defineOptions({ layout: DefaultLayout })
const props = defineProps([
  'services',
  'featuredService',
  'faqs',
  'testimonials',
  'service_page',
  'home_page',
  'logos'
])
const { textExcerpt } = sharedComposable()
</script>

<template>
  <InnerBannerTwo
    :page="trans('Services')"
    :title="trans('Our Services')"
    :description="trans('Your Solutions, Our Expertise: Explore Our Services!')"
  />

  <!--
		=====================================================
			BLock Feature Ten
		=====================================================
		-->
  <div class="block-feature-ten position-relative mt-100 lg-mt-80 lg-pb-60 pb-60">
    <div class="container">
      <div class="row align-items-center">
        <div class="col-lg-8 wow fadeInUp">
          <div class="title-one mb-50 lg-mb-20">
            <h2>{{ service_page.features?.title }}</h2>
          </div>
          <!-- /.title-one -->
        </div>
      </div>
      <div class="line-wrapper position-relative">
        <div class="row gx-lg-5">
          <div
            v-for="(feature, index) in service_page.features.items || []"
            :key="index"
            class="col-xl-4 col-md-6 wow fadeInUp"
            :data-wow-delay="`${index + 1}00ms`"
          >
            <div class="card-style-sixteen d-flex lg-mt-40 lg-mb-10 mb-60 mt-60">
              <div
                class="icon tran3s rounded-circle d-flex align-items-center justify-content-center"
              >
                <img v-lazy="feature.icon" alt="" class="lazy-img" />
              </div>
              <div class="text">
                <h4 class="fw-bold sm-mb-10 mb-20">{{ feature.title }}</h4>
                <p class="m0">{{ feature.description }}</p>
              </div>
            </div>
            <!-- /.card-style-sixteen -->
          </div>
        </div>
      </div>
    </div>
    <img v-lazy="service_page.features?.shape" alt="" class="lazy-img shapes shape_01" />
  </div>
  <!-- /.block-feature-ten -->

  <!--
		=============================================
			BLock Feature Six
		==============================================
		-->
  <div class="block-feature-six bg-two position-relative pt-150 lg-pt-60 pb-120 lg-pb-40">
    <div class="container">
      <div class="row gx-lg-5">
        <div class="col-lg-4 wow fadeInLeft">
          <div class="title-one">
            <h2>{{ service_page.service_list?.title }}</h2>
          </div>
          <!-- /.title-one -->
          <p class="text-dark md-mt-20 mb-35 md-mb-30 mt-40 text-lg">
            {{ service_page.service_list?.sub_title }}
          </p>
          <Link
            :href="service_page.service_list?.btn_link"
            class="btn-eleven d-inline-flex align-items-center md-mb-60"
          >
            <span class="text">{{ service_page.service_list?.btn_text }}</span>
            <div class="icon tran3s rounded-circle d-flex align-items-center">
              <img src="/assets/images/icon/icon_27.svg" alt="" class="lazy-img" />
            </div>
          </Link>
        </div>
        <div class="col-lg-8">
          <div class="row">
            <div
              class="col-md-6 d-flex wow fadeInUp"
              v-for="(service, index) in services.data"
              :key="index"
              :data-wow-delay="`${index + 1}00ms`"
            >
              <div class="card-style-eight rounded-5 vstack tran3s w-100 mb-30 active">
                <div class="icon d-flex align-items-center">
                  <img v-lazy="service.icon" alt="" class="lazy-img" />
                </div>
                <h4 class="fw-bold mt-30 mb-20">{{ service.title }}</h4>
                <p>{{ textExcerpt(service.overview, 100) }}</p>
                <Link :href="route('services.show', service)" class="stretched-link"></Link>
              </div>
              <!-- /.card-style-eight -->
            </div>
          </div>
          <Pagination :links="services.links" />
        </div>
      </div>
    </div>
    <img src="/assets/images/shape/shape_11.svg" alt="" class="lazy-img shapes shape_01" />
    <img src="/assets/images/shape/shape_12.svg" alt="" class="lazy-img shapes shape_02" />
  </div>
  <!-- /.block-feature-six -->

  <!--
		=====================================================
			Fancy Banner Six
		=====================================================
		-->
  <div
    class="fancy-banner-six position-relative pt-110 lg-pt-60 pb-110 lg-pb-60 text-center"
    :style="`background-image: url(${service_page.banner?.banner_img});`"
  >
    <div class="container">
      <div class="row">
        <div class="col-lg-8 col-md-9 m-auto">
          <h2 class="fw-bold text-white">{{ service_page.banner?.title }}</h2>
          <p class="pb-20 pt-10 text-xl text-white">
            {{ service_page.banner?.sub_title }}
          </p>
          <Link
            :href="service_page.banner?.btn_link"
            class="btn-seventeen d-inline-flex align-items-center tran3s"
          >
            <span class="text">{{ service_page.banner?.btn_text }}</span>
            <i class="bi bi-arrow-right"></i>
          </Link>
        </div>
      </div>
    </div>
  </div>
  <!-- /.fancy-banner-six -->

  <SliderSection :data="home_page.slider ?? {}" :testimonials="testimonials" :logos="logos" />

  <FancyBannerThree />
</template>
