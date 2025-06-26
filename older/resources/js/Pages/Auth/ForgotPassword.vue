<script setup>
import InputFieldError from '@/Components/InputFieldError.vue'
import { Head, useForm } from '@inertiajs/vue3'
import SpinnerBtn from '@/Components/SpinnerBtn.vue'
import AuthLayout from '@/Layouts/AuthLayout.vue'
import LeftBanner from '@/Pages/Auth/Partials/LeftBanner.vue'
import Swal from 'sweetalert2'

defineOptions({ layout: AuthLayout })
const props = defineProps({
  status: {
    type: String
  },
  authPages: Object
})

const loginData = props.authPages?.login ?? {}

const form = useForm({
  email: ''
})

const submit = () => {
  form.post(route('password.email'), {
    onSuccess: () => {
      function showAlert() {
        Swal.fire({
          icon: "success",
          title: "<h2 style='font-size: 20px;'>Password reset link sent successfully</h2>",
          confirmButtonText: "Okay",
          allowOutsideClick: false,
          customClass: {
            popup: "custom-swal-popup",
          },
          didOpen: (toast) => {
            const closeButton = document.createElement("button")
            closeButton.innerHTML = "&times;"
            closeButton.classList.add("swal-close-btn")
            closeButton.onclick = () => Swal.close()

            toast.appendChild(closeButton)
          },
        });
      }
      showAlert();
    }
  })
}
</script>

<template>
  <div class="register-wrapper d-flex justify-content-center align-items-center">
    <div class="register-container d-flex position-relative">
      
      <!-- Top Left Logo -->
      <div class="top-left-logo">
        <a href="/"><img v-lazy="$page.props.primaryData?.deep_logo" alt="logo" /></a>
      </div>

      <!-- Left Image -->
      <div class="register-image">
        <img v-lazy="loginData.image2" alt="" />
      </div>

      <!-- Right Form -->
      <div class="register-form">

        <!-- Already have an account -->
        <div class="text-end mb-30">
          <span class="span-view">Have an account? <a href="/login" class="text-primary signin-button">Sign In</a></span>
        </div>

        <h4>Forgot Password</h4>
        <p class="mb-50">Forgot your password? No problem. Just let us know your email address and we will email you a password reset link that will allow you to choose a new one.</p>

        <form @submit.prevent="submit" class="forgotpasswordform">
          <div class="row">
            <div class="col-12">
              <div class="postbox__comment-input mb-20">
                <input type="email" v-model="form.email" class="inputText" required />
                <span class="floating-label">{{ trans('Your Email') }}</span>
                <InputFieldError :message="form.errors.email" />
              </div>
            </div>

            <div class="col-12">
              <div class="signin-banner-from-btn mb-20" v-if="!status">
                <SpinnerBtn
                  :processing="form.processing"
                  class="signin-btn w-100"
                  :btn-text="trans('Email Password Reset Link')"
                />
              </div>
            </div>
          </div>  
           <div class="col-12">
                <div class="d-flex justify-content-center align-items-center mt-4">
                 <i class="bi bi-house"></i>&nbsp;                 
                  <Link href="/">  {{ trans('Back to home') }}</Link>
                </div>
            </div>      
        </form>
      </div>
    </div>
  </div>
</template>