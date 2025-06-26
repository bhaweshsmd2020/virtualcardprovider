<script setup>
import FancyBannerThree from '@/Components/Web/FancyBannerThree.vue'
import InnerBannerTwo from '@/Components/Web/InnerBannerTwo.vue'
import sharedComposable from '@/Composables/sharedComposable'
import trans from '@/Composables/transComposable'
import DefaultLayout from '@/Layouts/Default/DefaultLayout.vue'
import { computed, ref } from 'vue'

defineOptions({ layout: DefaultLayout })

const props = defineProps([
  'about',
  'faqs',
  'plans',
  'planSettings',
  'brands',
  'home',
  'testimonials'
])
const activeTab = ref('monthly')
const filteredPlans = computed(() => {
  if (activeTab.value === 'monthly') {
    return props.plans.filter((p) => p.days === 30)
  }
  if (activeTab.value === 'yearly') {
    return props.plans.filter((p) => p.days === 365)
  }
  if (activeTab.value === 'lifetime') {
    return props.plans.filter((p) => p.days > 365)
  }
})
const { authUser, formatCurrency } = sharedComposable()

const bannerData = {
  page: 'Pricing',
  title: trans('Our Membership'),
  description: trans('Find out the pricing for different service and products')
}
</script>
<style>
.pricing-card-one.d-flex.flex-column.w-100.h-100 {
    background: linear-gradient(to bottom, #E2F4FE, #ffffff);
}
</style>

<template>
  <InnerBannerTwo v-bind="bannerData" />

  <div class="pricing-section light-bg border-top lg-pt-80 pb-80 pt-80">
    <div class="container">
      <div class="row align-items-center">
        <div class="col-lg-7 text-lg-start text-center">
          <div class="title-one">
            <h2>{{ trans('Solo') }}, {{ trans('Agency') }} or {{ trans('Team') }}?</h2>
          </div>
          <!-- /.title-one -->
          <p class="m0 pt-10 text-lg">
            {{ trans('Find out the which plan match with your needs') }}
          </p>
        </div>
        <div class="col-lg-5">
          <!-- <nav class="pricing-nav d-flex justify-content-center justify-content-lg-end md-mt-40">
            <div class="nav nav-tabs" id="nav-tab" role="tablist">
              <button
                @click="activeTab = 'monthly'"
                class="nav-link active"
                id="nav-month-tab"
                data-bs-toggle="tab"
                data-bs-target="#nav-month"
                type="button"
                role="tab"
                aria-controls="nav-month"
                aria-selected="true"
              >
                {{ trans('Monthly') }}
              </button>
              <button
                @click="activeTab = 'yearly'"
                class="nav-link"
                id="nav-year-tab"
                data-bs-toggle="tab"
                data-bs-target="#nav-year"
                type="button"
                role="tab"
                aria-controls="nav-year"
                aria-selected="false"
              >
                {{ trans('Yearly') }}
              </button>
              <button
                @click="activeTab = 'lifetime'"
                class="nav-link"
                id="nav-lifetime-tab"
                data-bs-toggle="tab"
                data-bs-target="#nav-year"
                type="button"
                role="tab"
                aria-controls="nav-lifetime"
                aria-selected="false"
              >
                {{ trans('Lifetime') }}
              </button>
            </div>
          </nav> -->
        </div>
      </div>
      <div class="tab-content mt-50 md-mt-30">
        <div
          class="tab-pane show active"
          id="nav-price"
          role="tabpanel"
          aria-labelledby="nav-price-tab"
          tabindex="0"
        >
          <div class="row g-4">
            <div v-for="(item, i) in filteredPlans" :key="i" class="col-xl-4 col-md-6">
              <div class="pricing-card-one d-flex flex-column w-100 h-100">
                <img v-lazy="item.preview" class="subsicon" alt="preview" />
                <h2 class="fw-bold">{{ item.title }}</h2>
                <div class="row">
                  <div class="col-xxl-12">
                    <p class="p-0 mb-0">{{ item.description }}</p>
                  </div>
                </div>
                <div class="price-banner d-flex justify-content-start align-items-center text-start">
                  <div class="price text-start">
                    <sup>{{ $page.props.currency?.icon }}</sup> {{ item.price }}
                  </div>
                  <div class="ps-lg-2">
                    <strong class="fw-500 text-capitalize">
                      / month
                      <!-- {{ activeTab }} -->
                    </strong>
                    <small v-if="activeTab == 'yearly' && item.price > 0">
                      {{ trans('Starting at') }}
                      {{ formatCurrency(Math.floor(+item.price / 12)) }}/mo
                    </small>
                  </div>
                </div>
                <div class="action-btn text-center mb-20 mt-10">
                    <template v-if="item.is_trial == 1">{{ trans('Try us without risk') }}.</template>

                    <a
                      class="w-100"
                      :href="
                        authUser
                          ? route('user.subscription.payment', item.id)
                          : route('register', { id: item.id })
                      "
                    >
                      {{
                        item.is_trial == 1
                          ? 'Free trial for ' + item.trial_days + ' Days'
                          : trans('Get Started')
                      }}
                      <i class="bi bi-chevron-right"></i>
                    </a>
                </div>
                <!-- /.price-banner -->
                <p class="p-0 mb-0">What's included:</p>
                <ul class="style-none">
                  <li v-for="(data, key) in item.data" :key="key"><i class="bi bi-check"></i> {{ data.overview }}</li>
                </ul>                
              </div>
              <!-- /.pricing-card-one -->
            </div>
          </div>
        </div>
      </div>
      <!-- /.tab-content -->
      <div class="contact-banner position-relative mt-50">
        <img src="/assets/images/assets/ils_04.svg" alt="" class="lazy-img shapes screen_01" />
        <div class="row align-items-center justify-content-end">
          <div class="col-lg-6">
            <h2>
              {{ trans('Have a') }} <span>{{ trans('query for pricing') }}</span>
              {{ trans('plan for your business?') }}
            </h2>
          </div>
          <div class="col-lg-4 text-lg-end text-center">
            <Link href="/contact-us" class="btn-four">{{ trans('Let’s Talk') }}</Link>
          </div>
        </div>
      </div>
      <!-- /.contact-banner -->
    </div>
  </div>

  <FancyBannerThree />
</template>

<style>
.pricing-card-one .price-banner .price sup{
  top: -2px;
}

.pricing-card-one .price-banner strong {
  margin-bottom: -25px;
}

.pricing-card-one .action-btn {
  border-radius: 15px;
}

.subsicon{
  width: 30px;
  margin-bottom: 10px;
}

.price-banner {
  padding: 0 !important;
  border-radius: 0 !important;
  background: transparent !important;
  margin-bottom: 0 !important;
}

.style-none{
  font-size: 20px;
}
</style>