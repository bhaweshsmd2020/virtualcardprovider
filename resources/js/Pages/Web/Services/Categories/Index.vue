<script setup>
import FancyBannerThree from "@/Components/Web/FancyBannerThree.vue"
import InnerBannerTwo from "@/Components/Web/InnerBannerTwo.vue"
import NewsletterTwo from "@/Components/Web/NewsletterTwo.vue"
import DefaultLayout from "@/Layouts/Default/DefaultLayout.vue"
import sharedComposable from "@/Composables/sharedComposable"
import Pagination from "@/Components/Web/Pagination.vue"
const { textExcerpt } = sharedComposable()
defineOptions({ layout: DefaultLayout })
const props = defineProps(["categories", "services", "slug",'category'])
</script>

<template>
  <InnerBannerTwo
    :page="category.slug"
    :title="category.title"
    :description="''"
  />

  <!--
		=====================================================
			Service Details
		=====================================================
		-->
  <div class="service-details mt-150 lg-mt-80 mb-100 lg-mb-80">
    <div class="container">
      <div class="row">
        <div class="col-xxl-9 col-lg-8 order-lg-last">
          <div class="row">
            <div
              class="col-md-6 d-flex wow fadeInUp "
              v-for="(service, index) in services.data"
              :data-wow-delay="`${index + 1}00ms`"
            >
              <div class="card-style-eight rounded-5 vstack tran3s w-100 mb-30 block-feature-six bg-two">
                <div class="icon d-flex align-items-center">
                  <img v-lazy="service.icon" alt="" class="lazy-img" />
                </div>
                <h4 class="fw-bold mt-30 mb-20">{{ service.title }}</h4>
                <p>{{ textExcerpt(service.overview, 100) }}</p>
                <Link
                  :href="route('services.show', service)"
                  class="stretched-link"
                ></Link>
              </div>
              <!-- /.card-style-eight -->
            </div>
          </div>
          <Pagination :links="services.links" />
        </div>
        <div class="col-xxl-3 col-lg-4 order-lg-first">
          <aside class="md-mt-40">
            <div class="service-nav-item">
              <ul class="style-none">
                <li v-for="category in categories">
                  <Link
                    preserve-scroll
                    :href="`/service-categories/${category.slug}`"
                    class="d-flex align-items-center w-100"
                    :class="{
                      active: category.slug === slug,
                    }"
                  >
                    <img v-lazy="category.icon" alt="" class="lazy-img" />
                    <span>{{ category.title }}</span>
                  </Link>
                </li>
              </ul>
            </div>
            <div class="contact-banner lg-mt-20 mt-40 text-center">
              <h3 class="mb-20">{{ trans("Any Questions? Let’s talk") }}</h3>
              <Link href="/contact-us" class="tran3s fw-500">{{
                trans("Let’s Talk")
              }}</Link>
            </div>
            <!-- /.contact-banner -->
          </aside>
        </div>
      </div>
    </div>
  </div>
  <!-- /.service-details -->

  <FancyBannerThree />
  <NewsletterTwo />
</template>
