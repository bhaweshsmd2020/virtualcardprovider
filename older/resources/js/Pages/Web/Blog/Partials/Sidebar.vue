<script setup>
import DefaultLayout from '@/Layouts/Default/DefaultLayout.vue'
defineOptions({ layout: DefaultLayout })
defineProps(['categories', 'tags', 'posts'])
import sharedComposable from '@/Composables/sharedComposable'
import { router } from '@inertiajs/vue3'
import { ref } from 'vue'
import moment from 'moment'
const { getQueryParams } = sharedComposable()
const request = getQueryParams()
const searchInput = ref(request.s ?? '')

const submit = () => {
  router.get(route('blogs.index'), { s: searchInput.value })
}
</script>

<template>
  <div class="blog-sidebar md-mt-80 ps-xxl-4">
    <form @submit.prevent="submit" class="sidebar-search">
      <input type="text" v-model="searchInput" placeholder="Search.." />
      <button type="submit" class="tran3s"><i class="bi bi-search"></i></button>
    </form>
    <div class="blog-category lg-mt-40 mt-60" v-if="categories.length > 0">
      <h3 class="sidebar-title">{{ trans('Categories') }}</h3>
      <ul class="style-none">
        <li v-for="cat in categories" :key="cat.id">
          <Link :href="`/blogs/category/${cat.slug}`"
            >{{ cat.title }} <span>({{ cat.post_categories_count }})</span></Link
          >
        </li>
      </ul>
    </div>
    <!-- /.blog-category -->
    <div class="blog-recent-news lg-mt-40 mt-60">
      <h3 class="sidebar-title">{{ trans('Recent News') }}</h3>
      <div class="row">
        <article v-for="item in posts" :key="item.id" class="recent-news col-12 col-md-6 col-lg-12">
          <figure
            class="post-img"
            :style="{ 'background-image': `url(${item.preview?.value})` }"
          ></figure>
          <div class="post-data">
            <div class="date">{{ moment(item.created_at).format('DD MMM, YYYY') }}</div>
            <Link :href="route('blogs.show', item.slug)" class="blog-title"
              ><h3>{{ item.title }}</h3></Link
            >
          </div>
        </article>
      </div>
    </div>
    <!-- /.blog-recent-news -->

    <div class="blog-keyword mt-30" v-if="tags.length > 0">
      <h3 class="sidebar-title">{{ trans('Keywords') }}</h3>
      <ul class="style-none d-flex flex-wrap">
        <li v-for="tag in tags" :key="tag.id">
          <Link :href="`/blogs/tag/${tag.slug}`"> {{ tag.title }} </Link>
        </li>
      </ul>
    </div>
    <!-- /.blog-keyword -->
    <div class="contact-banner mt-50 lg-mt-30 text-center">
      <h3 class="mb-20">{{ trans('Any Questions?') }} <br />{{ trans('Let’s talk') }}</h3>
      <Link href="/contact-us" class="tran3s fw-500">{{ trans('Let’s Talk') }}</Link>
    </div>
    <!-- /.contact-banner -->
  </div>
</template>
