<script setup>
import InnerBannerTwo from "@/Components/Web/InnerBannerTwo.vue"
import FancyBannerThree from "@/Components/Web/FancyBannerThree.vue"
import DefaultLayout from "@/Layouts/Default/DefaultLayout.vue"
import { computed } from "vue"
defineOptions({ layout: DefaultLayout })
const props = defineProps(["team", "information"])

</script>

<template>
  <InnerBannerTwo
    :page="trans('Team')"
    :title="team.title"
    :description="trans('Find the team members details here with all the information')"
  />

  <!--
		=====================================================
			Team Details
		=====================================================
		-->
  <div class="team-details light-bg border-top pt-120 lg-pt-80 pb-150 lg-pb-80">
    <div class="container">
      <div class="bg-wrapper">
        <div class="row">
          <div class="col-lg-4">
            <div
              class="member-img"
              :style="{ 'background-image': `url(${team.preview?.value})` }"
            ></div>
          </div>
          <div class="col-lg-4 col-md-6">
            <div
              class="pt-45 pb-45 ps-xl-4 ps-lg-0 pe-xl-4 pe-lg-3 border-right h-100 pe-3 ps-3"
            >
              <h2 class="name fw-bold">{{ team.title }}</h2>
              <div class="post">{{ team.slug }}</div>
              <h6 class="fw-bold">{{ trans('About') }} {{ team.title }}</h6>
              <p>
                {{ team.description?.value }}
              
              </p>

              <div class="social-share pt-35">
                <ul class="style-none d-flex align-items-center">
                  <li v-for="socialMedia in team.social_medias">
                    <a :href="socialMedia.url"
                      ><i class="bi" :class="`bi-${socialMedia.name}`"></i
                    ></a>
                  </li>
                </ul>
              </div>
            </div>
          </div>
          <div class="col-lg-4 col-md-6">
            <div class="pt-45 pb-45 ps-xl-4 ps-lg-0 pe-xl-4 pe-lg-0 pe-3 ps-3">
              <h3>{{ trans("Details") }}</h3>
              <table class="w-100 mt-25 lg-mt-20">
                <tr>
                  <td>{{ trans("Location:") }}</td>
                  <td>{{ information?.location }}</td>
                </tr>
                <tr>
                  <td>{{ trans("Position:") }}</td>
                  <td>{{ team.slug }}</td>
                </tr>
                <tr>
                  <td>{{ trans("Email:") }}</td>
                  <td>{{ information?.email }}</td>
                </tr>
                <tr>
                  <td>{{ trans("Age:") }}</td>
                  <td>{{ information?.age }}</td>
                </tr>
                <tr>
                  <td>{{ trans("Qualification:") }}</td>
                  <td>{{ information?.qualification }}</td>
                </tr>
                <tr>
                  <td>{{ trans("Gender:") }}</td>
                  <td>{{ information?.gender }}</td>
                </tr>
              </table>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  <!-- /.team-details -->

  <FancyBannerThree />
</template>
