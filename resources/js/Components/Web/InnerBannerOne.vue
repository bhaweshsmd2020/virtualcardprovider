<script setup>
const bg_img = "/assets/images/media/img_32.jpg";
const props = defineProps({
  page: {
    type: String,
    default: "Page",
  },
  v: {
    type: String,
    default: "two",
  },
  title: {
    type: String,
    default: "Explore our News",
  },
  description: {
    type: String,
    default: "Find the team members details here with all the information",
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
    class="inner-banner-one pt-225 lg-pt-200 md-pt-150 pb-100 md-pb-70 position-relative"
    
    :style="{ backgroundImage: `url(${bg_img})` }"
  >
    <div class="container position-relative">
      <div class="row align-items-center">
        <div class="col-lg-6">
          <h1 class="hero-heading d-inline-block position-relative me-xl-4">
            {{ props.title }} <img src="/assets/images/shape/shape_27.svg" alt="" />
          </h1>
        </div>
        <div class="col-xl-4 col-lg-5 ms-auto">
          <p class="text-white text-lg mb-70 lg-mb-40">
            {{ props.description }}
          </p>
          <ul class="style-none d-inline-flex pager">
            <li>
              <Link href="/">{{ trans("Home") }}</Link>
            </li>
            <li>/</li>
            <li>{{ props.page }}</li>
          </ul>
        </div>
      </div>
    </div>
  </div>
</template>
