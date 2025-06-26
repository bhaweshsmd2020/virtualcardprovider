<script setup>
import { ref, computed } from 'vue'
import { useForm } from '@inertiajs/vue3'
import InputFieldError from '@/Components/InputFieldError.vue'
import AuthLayout from '@/Layouts/AuthLayout.vue'
import toast from '@/Composables/toastComposable'
import SpinnerBtn from '@/Components/SpinnerBtn.vue'
import LeftBanner from '@/Pages/Auth/Partials/LeftBanner.vue'
import PasswordEyeButton from '@/Pages/Auth/Partials/PasswordEyeButton.vue'
import Swal from 'sweetalert2'

const props = defineProps(['seo', 'authPages', 'planId'])
const loginData = props.authPages?.login ?? {}

const showPassword = ref(false)
const showPassword2 = ref(false)

const form = useForm({
  first_name:'',
  last_name:'',
  email: '',
  password: '',
  password_confirmation: '',
  acceptterms: true
})

// Password validation rules
const passwordRules = computed(() => ({
  length: form.password.length >= 8,
  number: /[0-9]/.test(form.password),
  lowercase: /[a-z]/.test(form.password),
  uppercase: /[A-Z]/.test(form.password),
  specialChar: /[@#$%]/.test(form.password),
}))

// Check if password meets all requirements
const isPasswordValid = computed(() => {
  return Object.values(passwordRules.value).every(rule => rule)
})

// Check if passwords match
const doPasswordsMatch = computed(() => {
  return form.password === form.password_confirmation && form.password.length > 0
})

const submit = () => {
  if (!form.acceptterms) {
    toast.error('You must agree to the terms and conditions')
    return
  }

  if (!isPasswordValid.value) {
    toast.error('Password does not meet all requirements')
    return
  }

  if (!doPasswordsMatch.value) {
    toast.error('Passwords do not match')
    return
  }

  form.post(route('register'), {
    onSuccess: () => {
      form.reset()

      function showAlert() {
        Swal.fire({
          iconHtml: '<i class="bx bxs-inbox" style="font-size: 40px; color: #026f5e;"></i>', // or use Font Awesome icon
          title: "<h2 style='font-size: 20px;'>Email verification has been sent to your email. Go to your Inbox.</h2>",
          html: "<p style='font-size: 15px; margin-bottom: 0px;'>You may check your spam box too.</p>",
          confirmButtonText: "Continue",
          allowOutsideClick: false,
          customClass: {
            popup: "custom-swal-popup",
            icon: "custom-swal-icon" // optional for spacing or design
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
    },
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
          <span class="span-view">Already have an account? <a href="/login" class="text-primary signin-button">Sign In</a></span>
        </div>

        <h4 class="mt-50">Welcome to Virtual Card Provider</h4>
        <p class="mb-4">Register your account</p>

        <form @submit.prevent="submit" class="account-register">
          <div class="row">
            <div class="col-6">
              <div class="postbox__comment-input mb-20">
                <input
                  :type="text"
                  v-model="form.first_name"
                  class="inputText"
                  maxlength="20"
                  required
                />
                <InputFieldError :message="form.errors.first_name" />
                <span class="floating-label">{{ trans("First Name") }}</span>
              </div>
            </div> 
            
            <div class="col-6">
              <div class="postbox__comment-input mb-20">
                <input
                  :type="text"
                  v-model="form.last_name"
                  class="inputText"
                  maxlength="20"
                  required
                />
                <InputFieldError :message="form.errors.last_name" />
                <span class="floating-label">{{ trans("Last Name") }}</span>
              </div>
            </div>   

            <div class="col-12">
              <InputFieldError :message="form.errors.full_name" />
            </div>
            
            <!-- Email -->
            <div class="col-12">
              <div class="postbox__comment-input mb-20">
                <input :type="email" v-model="form.email" class="inputText" required />
                <InputFieldError :message="form.errors.email" />
                <span class="floating-label">Your Email</span>
              </div>
            </div>

            <!-- Password -->
            <div class="col-12">
              <div class="postbox__comment-input mb-20">
                <input
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

            <!-- Confirm Password -->
            <div class="col-12">
              <div class="postbox__comment-input mb-20">
                <input
                  v-model="form.password_confirmation"
                  class="inputText password"
                  :type="showPassword2 ? 'text' : 'password'"
                  required
                />
                <InputFieldError :message="form.errors.password_confirmation" />
                <span class="floating-label">Confirm Password</span>
                <PasswordEyeButton v-model="showPassword2" />
              </div>
            </div>
          </div>
          <!-- Password Requirements -->
          <ul class="password-requirements mb-2">
            <li :class="{ 'valid': passwordRules.length }">✔ At least 8 characters</li>
            <li :class="{ 'valid': passwordRules.number }">✔ At least one numeric digit (0–9)</li>
            <li :class="{ 'valid': passwordRules.lowercase }">✔ At least one lowercase letter</li>
            <li :class="{ 'valid': passwordRules.uppercase }">✔ At least one uppercase letter</li>
            <li :class="{ 'valid': passwordRules.specialChar }">✔ At least one special character (@ # $ %)</li>
          </ul>

          <!-- Password Match Warning -->
          <p v-if="form.password_confirmation && !doPasswordsMatch" class="error-text">
            Passwords do not match
          </p>

          <!-- Terms -->
          <div class="form-check mb-3">
            <input class="form-check-input" v-model="form.acceptterms" type="checkbox" id="terms" />
            <label class="form-check-label" for="terms">
              Agree to <a href="/terms-and-conditions" class="text-primary">Terms & Conditions</a>
            </label>
          </div>

          <!-- Register Button -->
          <div class="col-12">
            <SpinnerBtn
              :processing="form.processing"
              :btn-text="'Create My Account'"
              class="signin-btn w-100"
              :disabled="!isPasswordValid || !doPasswordsMatch"
            />
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
  
    .account-register{
    background: #fff;
    padding: 34px;
    border-radius: 5px;
   
  }
</style>