<script setup>
import { ref } from 'vue'
import { Head, useForm } from '@inertiajs/vue3'
import AuthLayout from '@/Layouts/AuthLayout.vue'
import SpinnerBtn from '@/Components/SpinnerBtn.vue'
import InputFieldError from '@/Components/InputFieldError.vue'
import toast from '@/Composables/toastComposable'
import PasswordEyeButton from '@/Pages/Auth/Partials/PasswordEyeButton.vue'
import LeftBanner from '@/Pages/Auth/Partials/LeftBanner.vue'

defineOptions({ layout: AuthLayout })

const props = defineProps(['seo', 'authPages'])
const loginData = props.authPages?.login ?? {}

const showPassword = ref(false)

const form = useForm({
  email: '',
  password: '',
  remember: 0
})

const submit = () => {
  form.post(route('login'), {
    preserveScroll: true,
    onSuccess: () => toast.success(trans('Login Success')),
    onFinish: () => form.reset('password')
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
        <div class="text-end">
          <span class="span-view"y>Don't have account? <a href="/register" class="text-primary signin-button">Register</a></span>
        </div>

        <h4 class="mt-50">Welcome to Virtual Card Provider</h4>
        <p class="mb-40">Sign in to your account</p>

        <form @submit.prevent="submit" class="account-login">
          <div class="row">
            <div class="col-12">
              <div class="postbox__comment-input mb-20">
                <input type="email" v-model="form.email" class="inputText" required />
                <span class="floating-label">{{ trans('Your Email') }}</span>
                <InputFieldError :message="form.errors.email" />
              </div>
            </div>

            <div class="col-12">
              <div class="postbox__comment-input mb-20">
                <input
                  id="myInput"
                  v-model="form.password"
                  class="inputText password"
                  :type="showPassword ? 'text' : 'password'"
                  required
                />
                <InputFieldError :message="form.errors.password" />
                <span class="floating-label">Password</span>
                <PasswordEyeButton v-model="showPassword" />
              </div>
            </div>

            <div class="col-12">
              <div class="signin-banner-from-btn mb-20">
                <SpinnerBtn
                  :processing="form.processing"
                  class="signin-btn w-100"
                  :btn-text="trans('Sign In')"
                />
              </div>
            </div>
          </div>
          <div class="signin-banner-form-remember">
            <div class="row">
              <div class="col-6">
                <div class="postbox__comment-agree">
                  <div class="form-check">
                    <input
                      class="form-check-input"
                      v-model="form.remember"
                      type="checkbox"
                      value=""
                      id="flexCheckDefault"
                    />
                    <label class="form-check-label" for="flexCheckDefault">
                      {{ trans('Remember me') }}
                    </label>
                  </div>
                </div>
              </div>
              <div class="col-6">
                <div class="postbox__forget text-end">
                  <Link href="/forgot-password">{{ trans('Forgot password ?') }}</Link>
                </div>
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
<style>
  
    .account-login{
    background: #fff;
    padding: 34px;
    border-radius: 5px;
   
  }
</style>