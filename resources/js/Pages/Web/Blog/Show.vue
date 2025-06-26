<script setup>
import DefaultLayout from '@/Layouts/Default/DefaultLayout.vue'
import sharedComposable from '@/Composables/sharedComposable'
import moment from 'moment'
import Sidebar from './Partials/Sidebar.vue'
import Newsletter from '@/Components/Web/Newsletter.vue'
import trans from '@/Composables/transComposable'
import InnerBannerTwo from '@/Components/Web/InnerBannerTwo.vue'
import FancyBannerThree from '@/Components/Web/FancyBannerThree.vue'
defineOptions({ layout: DefaultLayout })
let props = defineProps([
  'blog',
  'categories',
  'tags',
  'recent_blogs',
  'related_blogs',
  'prevBlog',
  'nextBlog'
])

const { socialShare } = sharedComposable()

const pageData = {
  page: trans('Blog'),
  title: trans('Blog'),
  description: props.blog.title
}
</script>

<template>
  <InnerBannerTwo v-bind="pageData" />

  <div class="blog-details position-relative mt-100 lg-mt-80 lg-mb-80 mb-100">
    <div class="container">
      <div class="row gx-xl-5">
        <div class="col-lg-8">
          <article class="blog-meta-two style-two">
            <figure
              class="post-img position-relative d-flex align-items-end m0"
              :style="`background-image: url(${blog.banner?.value})`"
            >
              <div class="date">{{ moment(blog.created_at).format('MMM YYYY') }}</div>
            </figure>
            <div class="post-data">
              <div class="post-info">
                {{ blog.categories?.map((c) => c.title).join(', ') }}
              </div>
              <div class="blog-title">
                <h4>{{ blog.title }}</h4>
              </div>
              <div class="post-details-meta" v-html="blog.long_description?.value"></div>
              <!-- /.post-details-meta -->
              <div class="bottom-widget d-sm-flex align-items-center justify-content-between">
                <ul class="d-flex align-items-center tags style-none pt-20">
                  <li>{{ trans('Tag') }}:</li>
                  <li v-for="tag in blog.tags" :key="tag.id">
                    <Link :href="`/blogs/tag/${tag.slug}`">{{ tag.title }}</Link>
                  </li>
                </ul>
                <ul class="d-flex share-icon align-items-center style-none pt-20">
                  <li>{{ trans('Share') }}:</li>
                  <li>
                    <a target="_blank" :href="socialShare('facebook')"
                      ><i class="bi bi-facebook"></i
                    ></a>
                  </li>
                  <li>
                    <a target="_blank" :href="socialShare('twitter')"
                      ><i class="bi bi-twitter"></i
                    ></a>
                  </li>
                  <li>
                    <a target="_blank" :href="socialShare('instagram')"
                      ><i class="bi bi-instagram"></i
                    ></a>
                  </li>
                </ul>
              </div>
            </div>
            <!-- /.post-data -->
          </article>
        </div>

        <div class="col-lg-4 col-md-12">
          <Sidebar :categories="categories" :posts="recent_blogs" :tags="tags" />
        </div>
      </div>
    </div>
  </div>
  <!-- /.blog-details -->

  <FancyBannerThree />
</template>
