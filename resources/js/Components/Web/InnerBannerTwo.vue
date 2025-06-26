<script setup>
const props = defineProps({
  page: {
    type: String,
    default: "Page",
  },
  title: {
    type: String,
    default: "Explore our News",
  },
  description: {
    type: String,
    default: "Explore, Engage, and Enlighten: Your Gateway to Inspiring Blogs!",
  },
  images: {
    type: Array,
    default: [
      "/assets/images/assets/ils_01.svg",
      "/assets/images/assets/ils_02.svg",
      "/assets/images/shape/shape_05.svg",
      "/assets/images/shape/shape_05.svg",
    ],
  },
})
</script>

<template>
  <div
    class="inner-banner-two light-bg pt-200 md-pt-150 pb-85 lg-pb-50 position-relative text-center"
  >
    <div class="position-relative container">
      <div class="row">
        <div class="col-xl-7 m-auto">
          <ul class="style-none d-inline-flex justify-content-center pager">
            <li>
              <Link href="/">{{ trans("Home") }}</Link>
            </li>
            <li>/</li>
            <li>{{ props.page }}</li>
          </ul>
          <h1 class="hero-heading">{{ trans(title) }}</h1>
          <p class="text-lg">{{ trans(description) }}</p>
        </div>
      </div>
    </div>
    <img
      v-for="(imagePath, index) in images"
      :key="imagePath"
      v-lazy="imagePath"
      class="lazy-img shapes"
      :class="`shape_0${index + 1}`"
    />
  </div>
</template>
