<script setup>
import FancyBannerThree from '@/Components/Web/FancyBannerThree.vue'
import InnerBannerTwo from '@/Components/Web/InnerBannerTwo.vue'
import Newsletter from '@/Components/Web/NewsletterTwo.vue'
import DefaultLayout from '@/Layouts/Default/DefaultLayout.vue'
import sharedComposable from '@/Composables/sharedComposable'
defineOptions({ layout: DefaultLayout })
const props = defineProps(['service', 'categories', 'servicePage', 'testimonials'])
const { textExcerpt } = sharedComposable()
</script>

<template>
  <InnerBannerTwo
    :page="trans('Services')"
    :title="textExcerpt(service.title, 45)"
    :description="trans('Offering solutions & services to address a spectrum of financial issues')"
  />
  <div class="service-details lg-mt-80 lg-mb-60 mb-70 mt-80">
    <div class="container">
      <div class="row">
        <div class="col-xxl-9 col-lg-8 order-lg-last">
          <div class="details-meta ps-xxl-5 ps-xl-3">
            <div v-html="service.description" class="text-justify"></div>
          </div>
        </div>
        <div class="col-xxl-3 col-lg-4 order-lg-first">
          <aside class="md-mt-40">
            <div class="service-nav-item">
              <ul class="style-none">
                <li v-for="category in categories" :key="category.slug">
                  <Link
                    :href="`/service-categories/${category.slug}`"
                    class="d-flex align-items-center w-100"
                    :class="{
                      active: category.slug === service.category?.slug
                    }"
                  >
                    <img v-lazy="category.icon" alt="" class="lazy-img" />
                    <span>{{ category.title }}</span>
                  </Link>
                </li>
              </ul>
            </div>
            <div class="contact-banner lg-mt-20 mt-40 text-center">
              <h3 class="mb-20">{{ servicePage.sidebar_card?.title }}</h3>
              <Link :href="servicePage.sidebar_card?.btn_link" class="tran3s fw-500">
                {{ servicePage.sidebar_card?.btn_text }}</Link
              >
            </div>
            <!-- /.contact-banner -->
          </aside>
        </div>
      </div>
    </div>
  </div>
  <FancyBannerThree />
  <Newsletter />
</template>
