<script setup>
import DefaultLayout from '@/Layouts/Default/DefaultLayout.vue'
import sharedComposable from '@/Composables/sharedComposable'
import moment from 'moment'
import FancyBannerThree from '@/Components/Web/FancyBannerThree.vue'
import InnerBannerTwo from '@/Components/Web/InnerBannerTwo.vue'
const { socialShare, textExcerpt } = sharedComposable()

defineOptions({ layout: DefaultLayout })
defineProps(['project', 'nextProject', 'prevProject'])
</script>

<template>
  <InnerBannerTwo
    :page="trans('Projects')"
    :title="textExcerpt(project.title, 45)"
    :description="textExcerpt(project.description, 75)"
  />

  <div class="project-details-two light-bg border-top pt-100 lg-pt-80 lg-pb-60 pb-40">
    <div class="container">
      <div class="bg-wrapper">
        <div class="row">
          <div class="col-lg-7">
            <div class="slider-wrapper">
              <div id="imageCarousel" class="carousel slide h-100">
                <div class="carousel-inner h-100">
                  <div
                    class="carousel-item h-100 active"
                    :style="`background-image: url(${project.preview})`"
                  ></div>
                  <div
                    class="carousel-item h-100"
                    :style="`background-image: url(${project.banner_preview})`"
                  ></div>
                </div>
                <button
                  class="carousel-control-prev"
                  type="button"
                  data-bs-target="#imageCarousel"
                  data-bs-slide="prev"
                >
                  <i class="bi bi-chevron-left"></i>
                  <span class="visually-hidden">{{ trans('Previous') }}</span>
                </button>
                <button
                  class="carousel-control-next"
                  type="button"
                  data-bs-target="#imageCarousel"
                  data-bs-slide="next"
                >
                  <i class="bi bi-chevron-right"></i>
                  <span class="visually-hidden">{{ trans('Next') }}</span>
                </button>
              </div>
            </div>
          </div>
          <div class="col-lg-5">
            <div class="info-wrapper">
              <h3 class="fw-bold">{{ project.title }}</h3>
              <p class="pb-5 pt-10">
                {{ project.description }}
              </p>
              <ul class="style-none list-meta">
                <li class="d-flex align-items-center">
                  <div class="icon">
                    <img src="/assets/images/icon/icon_85.svg" alt="" class="lazy-img" />
                  </div>
                  <div class="ps-4">
                    <div class="text1">{{ trans('Release Date') }}</div>
                    <span>{{ moment(project.release_at).format('DD MMM, YYYY') }}</span>
                  </div>
                </li>
                <li class="d-flex align-items-center">
                  <div class="icon">
                    <img src="/assets/images/icon/icon_88.svg" alt="" class="lazy-img" />
                  </div>
                  <div class="ps-4">
                    <div class="text1">{{ trans('Client Name') }}</div>
                    <span>{{ project.client }}</span>
                  </div>
                </li>
              </ul>
              <div class="social-share d-flex mt-40">
                <ul class="style-none d-flex align-items-center">
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
                    <a target="_blank" :href="socialShare('linkedin')"
                      ><i class="bi bi-linkedin"></i
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
            <!-- /.info-wrapper -->
          </div>
        </div>
      </div>
      <div class="pagination-two mt-20 border-0">
        <ul class="style-none d-flex justify-content-between">
          <li>
            <Link v-if="prevProject.id" :href="route('projects.show', prevProject)" preserve-scroll>
              <span class="d-flex align-items-center align-items-md-end">
                <i class="bi bi-arrow-left"></i>
                <span class="ms-md-4 ms-3">
                  <span class="pr-dir text-uppercase d-block">{{ trans('Previous') }}</span>
                  <span class="pr-name d-none d-md-block tran3s fw-500">{{
                    prevProject.title
                  }}</span>
                </span>
              </span>
            </Link>
          </li>
          <li>
            <Link v-if="nextProject.id" :href="route('projects.show', nextProject)" preserve-scroll>
              <span class="d-flex align-items-center align-items-md-end text-end">
                <span class="me-md-4 me-3">
                  <span class="pr-dir text-uppercase d-block"> {{ trans('Next') }}</span>
                  <span class="pr-name d-none d-md-block tran3s fw-500">{{
                    nextProject.title
                  }}</span>
                </span>
                <i class="bi bi-arrow-right"></i>
              </span>
            </Link>
          </li>
        </ul>
      </div>
      <!-- /.pagination-two -->
    </div>
  </div>
  <!-- /.project-details-two -->

  <FancyBannerThree />
</template>
