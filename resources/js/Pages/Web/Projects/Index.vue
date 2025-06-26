<script setup>
import FancyBannerThree from '@/Components/Web/FancyBannerThree.vue'
import InnerBannerTwo from '@/Components/Web/InnerBannerTwo.vue'
import Pagination from './Pagination.vue'

import DefaultLayout from '@/Layouts/Default/DefaultLayout.vue'
import { computed } from 'vue'
import { ref } from 'vue'

defineOptions({ layout: DefaultLayout })
const props = defineProps(['projects', 'categories'])

const selectedProjectCategory = ref('*')

const filteredProjects = computed(() => {
  if (selectedProjectCategory.value === '*') {
    return props.projects.data
  } else {
    return props.projects.data?.filter((item) => item.category_id == selectedProjectCategory.value)
  }
})
</script>

<template>
  <InnerBannerTwo
    :page="trans('Projects')"
    :title="trans('Recent Projects')"
    :description="trans('Innovating Futures, One Project at a Time: Explore Our Portfolio!')"
  />

  <!--
		=====================================================
			Portfolio Two
		=====================================================
		-->
  <div class="portfolio-two position-relative mt-100 lg-mt-80 mb-60">
    <div class="container">
      <div class="row">
        <div
          v-for="project in filteredProjects"
          :key="project.id"
          class="portfolio-block-one lg-mb-40 col-md-6 mb-60 px-3"
        >
          <div class="img-holder round-border">
            <img v-lazy="project.preview" alt="" class="img-meta w-100 tran5s" />
            <Link
              :href="route('projects.show', project.slug)"
              class="expend d-flex align-items-center justify-content-center tran3s"
              title="Click for large view"
              tabindex="0"
              ><i class="bi bi-link"></i
            ></Link>
          </div>
          <div class="caption">
            <div class="d-flex align-items-center justify-content-between">
              <div>
                <ul class="style-none d-flex tag">
                  <li>{{ project.category?.title }}</li>
                </ul>
                <h6>
                  <Link :href="route('projects.show', project.slug)" class="pj-title">
                    {{ project.title }}</Link
                  >
                </h6>
              </div>
              <div>
                <Link :href="route('projects.show', project.slug)" class="arrow tran3s"
                  ><i class="bi bi-arrow-up-right"></i
                ></Link>
              </div>
            </div>
          </div>
          <!-- /.caption -->
        </div>
      </div>
      <Pagination :links="projects.links" />
      <!-- /.pagination-one -->
    </div>
  </div>
  <!-- /.portfolio-two -->

  <FancyBannerThree />
  <!-- <Newsletter /> -->
</template>
