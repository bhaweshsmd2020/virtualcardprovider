<script setup>
import InputFieldError from '@/Components/InputFieldError.vue'
import { Head, useForm } from '@inertiajs/vue3'
import AuthLayout from '@/Layouts/AuthLayout.vue'
import SpinnerBtn from '@/Components/SpinnerBtn.vue'
import toast from '@/Composables/toastComposable'
import LeftBanner from '@/Pages/Auth/Partials/LeftBanner.vue'
import Swal from 'sweetalert2'
import { ref } from 'vue'

defineOptions({ layout: AuthLayout })

const props = defineProps({
  email: {
    type: String,
    required: true
  },
  token: {
    type: String,
    required: true
  },
  authPages: {
    type: Object
  }
})

const loginData = props.authPages?.login ?? {}

const form = useForm({
  token: props.token,
  email: props.email,
  password: '',
  password_confirmation: ''
})

// Password visibility toggle states
const passwordVisible = ref(false)  // Initially, password is hidden
const confirmPasswordVisible = ref(false)  // Initially, confirm password is hidden

const togglePasswordVisibility = () => {
  passwordVisible.value = !passwordVisible.value  // Toggle the password visibility
}

const toggleConfirmPasswordVisibility = () => {
  confirmPasswordVisible.value = !confirmPasswordVisible.value  // Toggle the confirm password visibility
}

const submit = () => {
  form.post(route('password.store'), {
    preserveScroll: true,
    onSuccess: () => {
      Swal.fire({
        icon: "success",
        title: "<h2 style='font-size: 20px;'>Password reset has been successful</h2>",
        confirmButtonText: "Continue",
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
    },
    onFinish: () => form.reset('password', 'password_confirmation')
  })
}
</script>

<template>
  <Head title="Reset Password" />
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
          <span>Remember Password? <a href="/login" class="text-primary signin-button">Sign In</a></span>
        </div>

        <h4>Welcome to Virtual Card Provider</h4>
        <p class="mb-4">Register your account</p>

        <form @submit.prevent="submit">
          <div class="row p-5">
            <!-- Email Input -->
            <div class="col-12">
              <div class="postbox__comment-input mb-30">
                <input type="email" v-model="form.email" class="inputText" />
                <InputFieldError :message="form.errors.email" />
                <span class="floating-label">{{ trans('Your Email') }}</span>
              </div>
            </div>

            <!-- Password Input -->
            <div class="col-12">
              <div class="postbox__comment-input mb-30">
                <input
                  id="passwordInput"
                  v-model="form.password"
                  :type="passwordVisible ? 'text' : 'password'"
                  class="inputText password"
                />
                <InputFieldError :message="form.errors.password" />
                <span class="floating-label">{{ trans('Password') }}</span>
                <span @click="togglePasswordVisibility" class="eye-btn">
                  <!-- Eye On SVG Icon (Visible password) -->
                  <span v-if="passwordVisible">
                    <svg width="20" height="17" viewBox="0 0 20 17" fill="none" xmlns="http://www.w3.org/2000/svg">
                      <path opacity="0.5" d="M2.1474 11.1661C1.38247 10.1723 1 9.67544 1 8.2C1 6.72456 1.38247 6.22767 2.1474 5.2339C3.67477 3.2496 6.2363 1 10 1C13.7637 1 16.3252 3.2496 17.8526 5.2339C18.6175 6.22767 19 6.72456 19 8.2C19 9.67544 18.6175 10.1723 17.8526 11.1661C16.3252 13.1504 13.7637 15.4 10 15.4C6.2363 15.4 3.67477 13.1504 2.1474 11.1661Z" stroke="#1C274C" stroke-width="1.5" />
                      <path d="M12.6969 8.2C12.6969 9.69117 11.488 10.9 9.99687 10.9C8.50571 10.9 7.29688 9.69117 7.29688 8.2C7.29688 6.70883 8.50571 5.5 9.99687 5.5C11.488 5.5 12.6969 6.70883 12.6969 8.2Z" stroke="#1C274C" stroke-width="1.5" />
                    </svg>
                  </span>
                  <!-- Eye Off SVG Icon (Password hidden) -->
                  <span v-else>
                    <svg width="18" height="18" viewBox="0 0 25 25" fill="none" xmlns="http://www.w3.org/2000/svg">
                      <g clip-path="url(#clip0_2547_24206)">
                        <path d="M18.813 18.9113C17.1036 20.2143 15.0222 20.9362 12.873 20.9713C5.87305 20.9713 1.87305 12.9713 1.87305 12.9713C3.11694 10.6532 4.84218 8.62795 6.93305 7.03134M10.773 5.21134C11.4614 5.05022 12.1661 4.96968 12.873 4.97134C19.873 4.97134 23.873 12.9713 23.873 12.9713C23.266 14.1069 22.5421 15.1761 21.713 16.1613M14.993 15.0913C14.7184 15.3861 14.3872 15.6225 14.0192 15.7865C13.6512 15.9504 13.2539 16.0386 12.8511 16.0457C12.4483 16.0528 12.0482 15.9787 11.6747 15.8278C11.3011 15.6769 10.9618 15.4524 10.6769 15.1675C10.392 14.8826 10.1674 14.5433 10.0166 14.1697C9.86567 13.7962 9.79157 13.3961 9.79868 12.9932C9.80579 12.5904 9.89396 12.1932 10.0579 11.8252C10.2219 11.4572 10.4583 11.126 10.753 10.8513" stroke="black" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                        <path d="M1.87305 1.97131L23.873 23.9713" stroke="black" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                      </g>
                      <defs>
                        <clipPath id="clip0_2547_24206">
                          <rect width="24" height="24" fill="white" transform="translate(0.873047 0.971313)" />
                        </clipPath>
                      </defs>
                    </svg>
                  </span>
                </span>
              </div>
            </div>

            <!-- Confirm Password Input -->
            <div class="col-12">
              <div class="postbox__comment-input mb-30">
                <input
                  id="confirmPasswordInput"
                  v-model="form.password_confirmation"
                  :type="confirmPasswordVisible ? 'text' : 'password'"
                  class="inputText password"
                />
                <InputFieldError :message="form.errors.password_confirmation" />
                <span class="floating-label">{{ trans('Password Confirmation') }}</span>
                <span @click="toggleConfirmPasswordVisibility" class="eye-btn">
                  <!-- Eye On SVG Icon (Visible confirm password) -->
                  <span v-if="confirmPasswordVisible">
                    <svg width="20" height="17" viewBox="0 0 20 17" fill="none" xmlns="http://www.w3.org/2000/svg">
                      <path opacity="0.5" d="M2.1474 11.1661C1.38247 10.1723 1 9.67544 1 8.2C1 6.72456 1.38247 6.22767 2.1474 5.2339C3.67477 3.2496 6.2363 1 10 1C13.7637 1 16.3252 3.2496 17.8526 5.2339C18.6175 6.22767 19 6.72456 19 8.2C19 9.67544 18.6175 10.1723 17.8526 11.1661C16.3252 13.1504 13.7637 15.4 10 15.4C6.2363 15.4 3.67477 13.1504 2.1474 11.1661Z" stroke="#1C274C" stroke-width="1.5" />
                      <path d="M12.6969 8.2C12.6969 9.69117 11.488 10.9 9.99687 10.9C8.50571 10.9 7.29688 9.69117 7.29688 8.2C7.29688 6.70883 8.50571 5.5 9.99687 5.5C11.488 5.5 12.6969 6.70883 12.6969 8.2Z" stroke="#1C274C" stroke-width="1.5" />
                    </svg>
                  </span>
                  <!-- Eye Off SVG Icon (Confirm password hidden) -->
                  <span v-else>
                    <svg width="18" height="18" viewBox="0 0 25 25" fill="none" xmlns="http://www.w3.org/2000/svg">
                      <g clip-path="url(#clip0_2547_24206)">
                        <path d="M18.813 18.9113C17.1036 20.2143 15.0222 20.9362 12.873 20.9713C5.87305 20.9713 1.87305 12.9713 1.87305 12.9713C3.11694 10.6532 4.84218 8.62795 6.93305 7.03134M10.773 5.21134C11.4614 5.05022 12.1661 4.96968 12.873 4.97134C19.873 4.97134 23.873 12.9713 23.873 12.9713C23.266 14.1069 22.5421 15.1761 21.713 16.1613M14.993 15.0913C14.7184 15.3861 14.3872 15.6225 14.0192 15.7865C13.6512 15.9504 13.2539 16.0386 12.8511 16.0457C12.4483 16.0528 12.0482 15.9787 11.6747 15.8278C11.3011 15.6769 10.9618 15.4524 10.6769 15.1675C10.392 14.8826 10.1674 14.5433 10.0166 14.1697C9.86567 13.7962 9.79157 13.3961 9.79868 12.9932C9.80579 12.5904 9.89396 12.1932 10.0579 11.8252C10.2219 11.4572 10.4583 11.126 10.753 10.8513" stroke="black" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                        <path d="M1.87305 1.97131L23.873 23.9713" stroke="black" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                      </g>
                      <defs>
                        <clipPath id="clip0_2547_24206">
                          <rect width="24" height="24" fill="white" transform="translate(0.873047 0.971313)" />
                        </clipPath>
                      </defs>
                    </svg>
                  </span>
                </span>
              </div>
            </div>

            <!-- Submit Button -->
            <div class="col-12">
              <SpinnerBtn
                :processing="form.processing"
                :btn-text="'Reset Password'"
                class="signin-btn w-100"
              />
            </div>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>
