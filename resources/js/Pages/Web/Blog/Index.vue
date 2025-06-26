<script setup>
import DefaultLayout from '@/Layouts/Default/DefaultLayout.vue'
import moment from 'moment'
import Pagination from '@/Components/Web/Pagination.vue'
import Sidebar from './Partials/Sidebar.vue'
import sharedComposable from '@/Composables/sharedComposable'
import InnerBannerTwo from '@/Components/Web/InnerBannerTwo.vue'
import FancyBannerThree from '@/Components/Web/FancyBannerThree.vue'

const { textExcerpt } = sharedComposable()

defineOptions({ layout: DefaultLayout })
const props = defineProps(['posts', 'categories', 'recent_posts', 'tags'])

const navEls = {
  nextEl: '.grid-next',
  prevEl: '.grid-prev'
}

const pageData = {
  page: 'Blog',
  title: 'Explore our News',
  description: 'Explore, Engage, and Enlighten: Your Gateway to Inspiring Blogs!',
  images: [
    '/assets/images/assets/ils_01.svg',
    '/assets/images/assets/ils_02.svg',
    '/assets/images/shape/shape_05.svg',
    '/assets/images/shape/shape_05.svg'
  ]
}
</script>

<template>
  <InnerBannerTwo v-bind="pageData" />
  <div class="blog-section-two position-relative mt-100 lg-mt-80 lg-mb-40 mb-100">
    <div class="container">
      <div class="position-relative">
        <div class="row g-5">
          <div class="col-md-6" v-for="(blog, index) in posts.data" :key="blog.id">
            <article class="blog-meta-two wow fadeInUp" :data-wow-delay="`${index}00ms`">
              <figure
                class="post-img rounded-5 position-relative d-flex align-items-end m0"
                :style="`background-image: url(${blog.preview?.value})`"
              >
                <Link
                  :href="route('blogs.show', blog)"
                  class="stretched-link rounded-5 date tran3s"
                  >{{ moment(blog.created_at).format('MMM YYYY') }}</Link
                >
              </figure>
              <div class="post-data">
                <div class="d-flex justify-content-between align-items-center flex-wrap">
                  <Link :href="route('blogs.show', blog)" class="blog-title"
                    ><h4>{{ textExcerpt(blog.title, 60) }}</h4></Link
                  >
                  <a
                    :href="route('blogs.show', blog)"
                    class="round-btn rounded-circle d-flex align-items-center justify-content-center tran3s"
                    ><i class="bi bi-arrow-up-right"></i
                  ></a>
                </div>
                <div class="post-info">
                  {{ blog.categories?.map((c) => c.title).join(', ') }}
                </div>
              </div>
            </article>
            <!-- /.blog-meta-two -->
          </div>

          <div class="text-center" v-if="posts.data.length == 0">
            <h3 class="mt-10">{{ trans("Oops! It Looks Like That Blog Isn't Here") }}</h3>
          </div>
        </div>

        <Pagination :links="posts.links" />
        <!-- /.pagination-one -->
      </div>
    </div>
  </div>
  <FancyBannerThree />
</template>
