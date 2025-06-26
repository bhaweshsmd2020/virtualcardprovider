<script setup>
import FancyBannerThree from "@/Components/Web/FancyBannerThree.vue";
import InnerBannerTwo from "@/Components/Web/InnerBannerTwo.vue";
import NewsletterTwo from "@/Components/Web/NewsletterTwo.vue";
import DefaultLayout from "@/Layouts/Default/DefaultLayout.vue"
defineOptions({ layout: DefaultLayout })
defineProps(['categories'])
</script>

<template>
  <InnerBannerTwo :page="trans('FAQ’s')" :title="trans('FAQ\'s')"
    :description="trans('Find out the all the question & answers you have about us')" />

  <div class="faq-section-three light-bg border-top pt-50 pb-150 lg-pb-80">
    <div class="container">
      <nav>
        <div class="nav nav-tabs justify-content-center" id="nav-tab" role="tablist">
          <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#nav-all" type="button" role="tab">All</button>
          <button v-for="category in categories" :key="category.id" class="nav-link" data-bs-toggle="tab"
            :data-bs-target="'#nav-' + category.slug" type="button" role="tab">{{ category.title }}</button>
        </div>
      </nav>
      <div class="tab-content mt-60 lg-mt-40">
        <div class="tab-pane fade show active" id="nav-all" role="tabpanel" tabindex="0">
          <div class="accordion accordion-style-one" id="accordion-all">
            <div v-for="faq in categories.flatMap(c => c.faqs)" :key="faq.id" class="accordion-item">
              <h2 class="accordion-header">
                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                  :data-bs-target="'#collapse-' + faq.id" aria-expanded="false" :aria-controls="'collapse-' + faq.id">
                  {{ faq.title }}
                </button>
              </h2>
              <div :id="'collapse-' + faq.id" class="accordion-collapse collapse" data-bs-parent="#accordion-all">
                <div class="accordion-body">
                  <p>{{ faq.excerpt?.value }}</p>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div v-for="category in categories" :key="category.id" class="tab-pane fade"
          :class="{ 'show active': category.id === categories[0].id }" :id="'nav-' + category.slug" role="tabpanel"
          tabindex="0">
          <div class="accordion accordion-style-one" :id="'accordion-' + category.slug">
            <div v-for="faq in category.faqs" :key="faq.id" class="accordion-item">
              <h2 class="accordion-header">
                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                  :data-bs-target="'#collapse-' + faq.id" aria-expanded="false" :aria-controls="'collapse-' + faq.id">
                  {{ faq.title }}
                </button>
              </h2>
              <div :id="'collapse-' + faq.id" class="accordion-collapse collapse" data-bs-parent="#accordion-"
                :aria-labelledby="'heading-' + faq.id">
                <div class="accordion-body">
                  <p>{{ faq.excerpt?.value }}</p>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
      <!-- /.tab-content -->

      <div class="text-center mt-60 lg-mt-50">
        <h2 class="fs-1 mb-30">{{ trans('Don\’t get your answer?') }}</h2>
        <Link href="/contact-us" class="btn-four">{{ trans('Contact Us') }}</Link>
      </div>
    </div>
  </div>


  <FancyBannerThree />
  <NewsletterTwo />
</template>
