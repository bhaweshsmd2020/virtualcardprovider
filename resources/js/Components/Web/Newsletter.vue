<script setup lang="ts">
import footerComposable from "@/Composables/footerComposable"
import trans from "@/Composables/transComposable"
const { email, NEWSLETTER_API, subscribe } = footerComposable()
</script>
<template>
  <div class="newsletter-banner white-vr" v-if="NEWSLETTER_API">
    <div class="container">
      <div class="main-wrapper bottom-border">
        <div class="row">
          <div class="col-lg-6">
            <h2 class="fw-bold text-white">{{ trans("Newsletter.") }}</h2>
            <p class="md-pb-20 text-lg text-white opacity-75">
              {{ trans("Get instant news by subscribe to our daily newsletter") }}
            </p>
          </div>
          <div class="col-lg-6">
            <form @submit.prevent="subscribe" class="me-lg-0 me-auto ms-auto">
              <div class="d-flex align-items-center justify-content-between">
                <input
                  type="email"
                  v-model="email"
                  required
                  id="news_letter"
                  placeholder="Enter your email address"
                />
                <button type="submit" class="rounded-circle tran3s">
                  <i class="bi bi-arrow-right"></i>
                </button>
              </div>
              <p class="text-lg-end m0 pt-5 text-center">
                {{ trans("Want to get latest news?") }}
                <a href="javascript:void(0)" class="fw-500"><label for="news_letter" >{{ trans("Subscribe to our newsletter") }}</label></a>
              </p>
            </form>
          </div>
        </div>
      </div>
      <!-- /.main-wrapper -->
    </div>
  </div>
</template>
