<script setup>
import { usePage } from "@inertiajs/vue3"
import { computed } from "vue"
const data = usePage().props.primaryData?.footer_cta ?? {}

const hideCtaSections = ["/services", "/projects/", "/blogs/"]
const page = usePage()
const showCtaSection = computed(() => {
  let found = false
  return hideCtaSections.every((urlPrefix) => {
    if (page.url.startsWith(urlPrefix)) {
      found = true
    }
    return !found
  })
})
</script>

<template>
  <div class="tp-cta-area p-relative" v-if="showCtaSection">
    <div class="tp-cta-grey-bg grey-bg-2"></div>
    <div class="container">
      <div class="row">
        <div class="col-12">
          <div class="tp-cta-bg" :style="{ backgroundImage: `url(${data.img1})` }">
            <div class="text-center tp-cta-content">
              <h3 class="text-white tp-section-title-lg">{{ data.title }}</h3>
              <p v-html="data.sub_title"></p>
              <a class="tp-btn-green" :href="data.btn_link"> {{ data.btn_text }}</a>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>
