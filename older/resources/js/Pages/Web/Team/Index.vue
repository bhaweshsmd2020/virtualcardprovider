<script setup>
import FancyBannerThree from '@/Components/Web/FancyBannerThree.vue'
import InnerBannerTwo from '@/Components/Web/InnerBannerTwo.vue'
import DefaultLayout from '@/Layouts/Default/DefaultLayout.vue'
defineOptions({ layout: DefaultLayout })
defineProps(['teams'])

const bannerData = {
  pages: [
    {
      name: 'Home',
      url: '/'
    },
    {
      name: 'Team',
      url: '/team'
    }
  ],
  page: 'Team',
  title: 'Our Experts',
  description: 'United Strength, Infinite Possibilities: Meet Our Team!'
}
</script>

<template>
  <InnerBannerTwo v-bind="bannerData" />

  <!--
		=====================================================
			Team Section Four
		=====================================================
		-->
  <div class="team-section-four light-bg border-top pt-100 lg-pt-80 pb-100 lg-pb-40">
    <div class="container">
      <div class="position-relative">
        <div class="row g-4">
          <div v-for="team in teams" :key="team.id" class="col-lg-3 col-sm-6 wow fadeInUp">
            <div class="card-style-fifteen">
              <div
                class="media d-flex align-items-center justify-content-center position-relative overflow-hidden"
              >
                <img v-lazy="team.preview?.value" alt="" class="lazy-img w-100" />
                <Link
                  :href="route('team.show', team)"
                  class="round-btn rounded-circle d-flex align-items-center justify-content-center tran3s"
                  ><i class="bi bi-arrow-up-right"></i
                ></Link>
              </div>
              <h4 class="fw-500 m0 pt-20">{{ team.title }}</h4>
              <div class="fs-6">{{ team.slug }}</div>
            </div>
            <!-- /.card-style-fifteen -->
          </div>
        </div>
      </div>
    </div>
  </div>
  <!-- /.team-section-four -->

  <FancyBannerThree />
</template>
