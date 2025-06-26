<script setup>
import FancyBannerThree from "@/Components/Web/FancyBannerThree.vue";
import InnerBannerTwo from "@/Components/Web/InnerBannerTwo.vue";
import NewsletterTwo from "@/Components/Web/NewsletterTwo.vue";
import DefaultLayout from "@/Layouts/Default/DefaultLayout.vue"
defineOptions({ layout: DefaultLayout })
defineProps(["customPage"])

</script>

<template>
  <InnerBannerTwo :page="customPage.title" :title="customPage.title" :description="''" />
  <div class="container p-5">
    <p v-html="customPage.description?.value" class="text-justify"></p>
  </div>
  <FancyBannerThree />
  <NewsletterTwo />
</template>
