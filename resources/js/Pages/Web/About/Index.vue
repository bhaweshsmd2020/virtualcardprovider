<script setup>
import FancyBannerThree from '@/Components/Web/FancyBannerThree.vue'
import InnerBannerTwo from '@/Components/Web/InnerBannerTwo.vue'
import DefaultLayout from '@/Layouts/Default/DefaultLayout.vue'
import SliderSection from '../Home/Partials/SliderSection.vue'
import sharedComposable from '@/Composables/sharedComposable'
import moment from 'moment'
defineOptions({ layout: DefaultLayout })
const props = defineProps(['about', 'testimonials', 'logos', 'faqs', 'blogs'])
const { textExcerpt } = sharedComposable()
</script>

<template>
  <InnerBannerTwo
    :page="trans('About Us')"
    :title="about.header?.title"
    :description="about.header?.sub_title"
  />

  <!--
		=====================================================
			Text Feature Three
		=====================================================
		-->
  <div
    class="text-feature-three text-feature-seven position-relative mt-100 lg-mt-80 pb-120 lg-pb-120"
  >
    <div class="container">
      <div class="row">
        <div class="col-xxl-5 col-lg-6 d-flex flex-column order-lg-last wow fadeInRight ms-auto">
          <div class="title-one">
            <h2>{{ about.feature.title }}</h2>
          </div>
          <!-- /.title-one -->
          <p class="mt-30 mb-55 lg-mb-30 text-lg">
            {{ about.feature.sub_title }}
          </p>
          <ul class="style-none">
            <li v-for="item in about.feature?.list?.split('|') ?? []" :key="item">{{ item }}</li>
          </ul>
          <div class="counter-wrapper mt-50 lg-mt-40 pt-25 lg-pt-10">
            <div class="row">
              <div class="col-xl-6 col-sm-5">
                <div class="counter-block-one mt-20">
                  <div class="main-count fw-bold">
                    {{ about.feature?.counter1_title }}
                  </div>
                  <p class="m0">{{ about.feature?.counter1_text }}</p>
                </div>
                <!-- /.counter-block-one -->
              </div>
              <div class="col-xl-6 col-sm-7">
                <div class="counter-block-one mt-20">
                  <div class="main-count fw-bold">
                    {{ about.feature?.counter2_title }}
                  </div>
                  <p class="m0">{{ about.feature?.counter2_text }}</p>
                </div>
                <!-- /.counter-block-one -->
              </div>
            </div>
          </div>
          <!-- /.counter-wrapper -->
        </div>
        <div class="col-xxl-6 col-lg-5 d-flex order-lg-first wow fadeInLeft">
          <div
            class="media-wrapper w-100 position-relative"
            :style="{ 'background-image': `url(${about.feature.bg_img})` }"
          >
            <img
              v-lazy="about.feature.bottom_overflow_img"
              alt=""
              class="lazy-img shapes screen_01"
            />
            <img v-lazy="about.feature.top_overflow_img" alt="" class="lazy-img shapes screen_02" />
          </div>
        </div>
      </div>
    </div>
  </div>
  <!-- /.text-feature-three -->

  <!--
		=============================================
			Text Feature Two
		==============================================
		-->
  <div class="text-feature-two position-relative pt-110 lg-pt-80 pb-110 lg-pb-80">
    <div class="container">
      <div class="row align-items-center">
        <div class="col-xl-6 col-lg-7">
          <div class="title-one">
            <h2 class="text-white">{{ about.feature2.title }}</h2>
          </div>
          <!-- /.title-one -->
        </div>
        <div class="col-lg-5 ms-auto">
          <p class="m0 text-md md-pt-10 text-white">
            {{ about.feature2.sub_title }}
          </p>
        </div>
      </div>

      <div class="row gx-0 mt-50 lg-mt-20 md-mt-10">
        <div class="col-lg-4" v-for="item in about.feature2?.items ?? []" :key="item.sub_title">
          <div class="card-style-five mt-60 text-center">
            <div class="icon d-flex align-content-center justify-content-center">
              <img v-lazy="item.icon" alt="" class="lazy-img" />
            </div>
            <div class="main-count fw-500">{{ item.title }}</div>
            <p class="ps-xxl-5 ps-xl-3 pe-xxl-5 pe-xl-3">
              {{ item.sub_title }}
            </p>
          </div>
          <!-- /.card-style-five -->
        </div>
      </div>
    </div>
    <img v-lazy="about.feature2.shape" alt="" class="lazy-img shapes shape_01" />
    <img v-lazy="about.feature2.shape2" alt="" class="lazy-img shapes shape_02" />
  </div>
  <!-- /.text-feature-two -->

  <!--
		=====================================================
			BLock Feature Four
		=====================================================
		-->
  <div class="block-feature-four position-relative mt-120">
    <div class="container">
      <div class="row align-items-center">
        <div class="col-xl-8 col-lg-9 wow fadeInUp m-auto">
          <div class="title-one lg-mb-20 mb-30 text-center">
            <h2>{{ about.feature3.title }}</h2>
          </div>
          <!-- /.title-one -->
        </div>
      </div>
      <div class="row gx-xxl-5">
        <div
          v-for="item in about.feature3?.items ?? []"
          :key="item"
          class="col-lg-4 d-flex wow fadeInUp"
        >
          <div class="card-style-six vstack tran3s w-100 mt-30 text-center">
            <div
              class="icon rounded-circle d-flex align-items-center justify-content-center m-auto"
            >
              <img v-lazy="item.icon" alt="" class="lazy-img" />
            </div>
            <h4 class="fw-bold md-mt-30 mb-25 mt-40">{{ item.title }}</h4>
            <p class="mb-20">{{ item.sub_title }}</p>
            <a :href="item.link" class="arrow-btn tran3s stretched-link m-auto"
              ><img src="/assets/images/icon/icon_09.svg" alt="" class="lazy-img"
            /></a>
          </div>
          <!-- /.card-style-six -->
        </div>
      </div>
    </div>
    <img v-lazy="about.feature3?.shape" alt="" class="lazy-img shapes shape_01" />
  </div>
  <!-- /.block-feature-four -->

  <SliderSection :testimonials="testimonials" :logos="logos" />

  <!--
		=====================================================
			Fancy Banner Five
		=====================================================
		-->
  <div
    class="fancy-banner-five position-relative mt-120 lg-mt-80 md-pt-60 md-pb-60 pb-80 pt-80 text-center"
  >
    <div class="container">
      <h2 class="fw-bold text-white">{{ about.feature4?.title }}</h2>
      <p class="pt-30 md-pt-10 pb-45 lg-pb-30 text-xl text-white">
        {{ about.feature4.sub_title }}
      </p>
      <a
        class="fancybox rounded-circle video-icon tran5s d-flex align-items-center justify-content-center m-auto text-center"
        data-fancybox=""
        :href="about.feature4.btn_link ?? '#'"
      >
        <img v-lazy="about.feature4.btn_img" alt="" class="lazy-img" />
      </a>
    </div>
  </div>
  <!-- /.fancy-banner-five -->

  <!--
		=====================================================
			FAQ Section One
		=====================================================
		-->
  <div class="faq-section-one mt-120 lg-mt-80">
    <div class="container">
      <div class="row">
        <div class="col-lg-5 wow fadeInLeft">
          <div class="title-one lg-mb-20 mb-40">
            <h2>{{ trans('Questions & Answers') }}</h2>
          </div>
          <!-- /.title-one -->
          <p class="lg-mb-20 mb-40 text-lg">
            {{ trans('Don’t find the answer? We can help') }}
          </p>
          <Link href="/contact-us" class="btn-four">{{ trans('Contact us') }}</Link>
        </div>

        <div class="col-lg-7">
          <div class="accordion accordion-style-one mt-15 md-mt-50" id="accordionOne">
            <div v-for="(faq, index) in faqs" :key="index" class="accordion-item">
              <h2 class="accordion-header">
                <button
                  class="accordion-button"
                  :class="{ collapsed: index !== 0 }"
                  type="button"
                  data-bs-toggle="collapse"
                  :data-bs-target="`#collapse_${index}`"
                  :aria-expanded="index === 0"
                  :aria-controls="`collapse_${index}`"
                >
                  {{ faq.question }}
                </button>
              </h2>
              <div
                :id="`collapse_${index}`"
                class="accordion-collapse collapse"
                :class="{ show: index === 0 }"
                data-bs-parent="#accordionOne"
              >
                <div class="accordion-body">
                  <p>
                    {{ faq.answer }}
                  </p>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  <!-- /.faq-section-one -->

  <!--
		=====================================================
			Blog Section Two
		=====================================================
		-->
  <div class="blog-section-two position-relative mt-100 lg-mt-80 pb-120 lg-pb-80">
    <div class="container">
      <div class="position-relative">
        <div class="title-one mb-30 lg-mb-10">
          <h2>{{ trans('Insights News.') }}</h2>
        </div>
        <!-- /.title-one -->

        <div class="row gx-xl-5">
          <div class="col-md-6" v-for="blog in blogs" :key="blog.id">
            <article class="blog-meta-two mt-35 wow fadeInUp">
              <figure
                class="post-img position-relative d-flex align-items-end m0"
                :style="`background-image: url(${blog.preview?.value})`"
              >
                <Link :href="route('blogs.show', blog)" class="stretched-link date tran3s">{{
                  moment(blog.created_at).format('MMM YYYY')
                }}</Link>
              </figure>
              <div class="post-data">
                <div class="d-flex justify-content-between align-items-sm-center flex-wrap">
                  <a :href="route('blogs.show', blog)" class="blog-title"
                    ><h4>{{ textExcerpt(blog.title, 60) }}</h4></a
                  >
                  <a
                    :href="route('blogs.show', blog)"
                    class="round-btn rounded-circle d-flex align-items-center justify-content-center tran3s"
                    ><i class="bi bi-arrow-up-right"></i
                  ></a>
                </div>
                <div class="post-info">
                  {{ blog.categories?.map((c) => c.title).join(', ') }}
                </div>
              </div>
            </article>
            <!-- /.blog-meta-two -->
          </div>
        </div>

        <div class="section-btn sm-mt-60">
          <Link href="/blogs" class="btn-seven d-inline-flex align-items-center">
            <span class="text">{{ trans('See all blogs') }}</span>
            <div class="icon tran3s rounded-circle d-flex align-items-center">
              <img src="/assets/images/icon/icon_27.svg" alt="" class="lazy-img" />
            </div>
          </Link>
        </div>
      </div>
    </div>
  </div>
  <!-- /.blog-section-two -->

  <FancyBannerThree />
</template>
