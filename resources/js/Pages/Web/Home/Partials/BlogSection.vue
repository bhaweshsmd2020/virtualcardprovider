<script setup>
import sharedComposable from '@/Composables/sharedComposable'
import moment from 'moment'
defineProps({
  data: {
    type: Object,
    default: {}
  },
  blogs: {
    type: Array,
    default: []
  }
})
const { textExcerpt } = sharedComposable()
</script>
<template>
  <div class="blog-section-three position-relative lg-mt-80 lg-mb-80 mb-60 mt-80">
    <div class="container">
      <div class="position-relative">
        <div class="d-flex justify-content-between align-items-center">
          <div>
            <div class="lg-mb-10 mb-10">
              <h2 class="text-dark fs-1 fw-bold">{{ data.title }}</h2>
            </div>

            <p class="lg-mb-10 mb-20">{{ data.sub_title }}</p>
          </div>
          <div class="mt-1">
            <Link href="/blogs" class="btn-ten d-inline-flex align-items-center">
              <span class="text">{{ trans('See all blogs') }}</span>
              <div class="icon tran3s rounded-circle d-flex align-items-center ms-2">
                <img src="/assets/images/icon/icon_27.svg" alt="" class="lazy-img w-75" />
              </div>
            </Link>
          </div>
        </div>
        <div class="row gx-xl-5">
          <div v-for="(item, i) in blogs" :key="i" class="col-md-6">
            <article class="blog-meta-one style-two d-flex flex-column-reverse mt-35 wow fadeInUp">
              <div class="post-data">
                <div class="fs-6">
                  <span class="fw-500 text-dark"
                    >{{ item.categories.map((c) => c.title).join(', ') }} -</span
                  >
                  {{ moment(item.created_at).format('D MMM, YYYY') }}
                </div>
                <Link :href="`/blogs/${item.slug}`" class="mt-15">
                  <h4 class="tran3s fs-4 fw-bold">{{ textExcerpt(item.title, 45) }}</h4>
                </Link>
              </div>
              <figure
                class="post-img position-relative d-flex justify-content-end align-items-end m0"
                :style="`background-image: url(${item.preview?.value})`"
              >
                <Link
                  :href="`/blogs/${item.slug}`"
                  class="round-btn stretched-link rounded-circle d-flex align-items-center justify-content-center tran3s"
                >
                  <i class="bi bi-arrow-up-right"></i
                ></Link>
              </figure>
            </article>
            <!-- /.blog-meta-one -->
          </div>
        </div>
      </div>
    </div>
  </div>
</template>
