<script setup lang="ts">
import footerComposable from '@/Composables/footerComposable'
import trans from '@/Composables/transComposable'
const { email, NEWSLETTER_API, subscribe } = footerComposable()
</script>
<template>
  <div class="newsletter-banner" v-if="NEWSLETTER_API">
    <div class="container mt-10">
      <div class="main-wrapper">
        <div class="row">
          <div class="col-lg-6">
            <h2 class="fw-bold fs-1">{{ trans('Our Newsletter.') }}</h2>
            <p class="md-pb-20">
              {{ trans('Get instant news by subscribe to our daily newsletter') }}
            </p>
          </div>
          <div class="col-lg-6">
            <form @submit.prevent="subscribe" class="me-lg-0 me-auto ms-auto">
              <div class="d-flex align-items-center position-relative rounded floating-label-group">
                <input
                  type="email"
                  v-model="email"
                  class="w-100 rounded"
                  placeholder=" "
                />
                <label>Enter your email</label>
                <button
                  id="news_letter_btn"
                  type="submit"
                  class="tran3s position-absolute scale-0-8 end-0 rounded"
                >
                  <i class="bi bi-arrow-right"></i>
                </button>
              </div>
              <p class="fs-6 ms-1 pt-5">
                {{ trans('Want to get latest news?') }}
                <a href="javascript:void(0)" class="fw-500">
                  <label for="news_letter">{{ trans('Subscribe us') }}</label>
                </a>
              </p>
            </form>
          </div>
        </div>
      </div>
      <!-- /.main-wrapper -->
    </div>
  </div>
</template>

<style>
.newsletter-banner .main-wrapper {
    padding: 50px 0 30px;
}

.floating-label-group {
  position: relative;
}

.floating-label-group input
 {
  width: 100%;
  padding: 12px 10px;
  font-size: 16px;
  border: 1px solid #ccc;
  border-radius: 4px;
  background: none;
  outline: none;
}

.floating-label-group label {
  position: absolute;
  left: 10px;
  top: 50%;
  transform: translateY(-50%);
  background: #fff;
  padding: 0 4px;
  color: #999;
  font-size: 16px;
  pointer-events: none;
  transition: 0.2s ease all;
}

.floating-label-group input:focus + label,
.floating-label-group input:not(:placeholder-shown) + label
 {
  top:3px;
  font-size: 12px;
  color: #333;
}
.floating-label-group input:focus {
    color:#026f5e;
    box-shadow: 0px 1px 2px 1px rgba(32, 33, 36, 0.06), inset 0 0 0 2px #026f5e;

}
.floating-label-group input:focus ~ label{
  color: #026f5e;
}
</style>