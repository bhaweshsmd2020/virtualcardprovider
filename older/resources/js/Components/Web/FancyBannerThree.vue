<script setup>
defineProps({
  title: {
    type: String,
    default: 'Want to Chat? Feel free to Contact our Team.'
  },
  description: {
    type: String,
    default: 'If you have anything in mind just contact us with our expert.'
  },

  btn: {
    type: Object,
    default: {
      img: '/assets/images/icon/icon_30.svg',
      link: '/contact-us'
    }
  }
})

// TODO: need to implement with primary data
</script>

<template>
  <div class="fancy-banner-three position-relative">
    <div class="container">
      <div class="row align-content-center">
        <div class="col-lg-8 col-md-9">
          <div class="title-one lg-mb-10 mb-20">
            <h2 class="text-white">{{ trans(title) }}</h2>
          </div>
          <!-- /.title-one -->
          <p class="m0 text-lg text-white opacity-75">
            {{ trans(description) }}
          </p>
        </div>
        <div class="col-lg-4 col-md-3">
          <Link
            :href="btn.link"
            class="quote-btn tran5s rounded-circle d-flex align-items-center justify-content-center ms-auto"
          >
            <img v-lazy="btn.img" alt="" class="lazy-img" />
          </Link>
        </div>
      </div>
    </div>
  </div>
</template>
