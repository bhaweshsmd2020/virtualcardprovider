<script setup>
import sharedComposable from '@/Composables/sharedComposable'
import { computed } from "vue"
import { Head, useForm, usePage } from "@inertiajs/vue3"
import LeftBanner from "@/Pages/Auth/Partials/LeftBanner.vue"
const page = usePage()
import AuthLayout from "@/Layouts/AuthLayout.vue"
import toast from '@/Composables/toastComposable'
import Swal from 'sweetalert2'
import 'boxicons/css/boxicons.min.css'

defineOptions({ layout: AuthLayout })
const { authUser, logout, uiAvatar } = sharedComposable()
const loginData = page.props.authPages?.login ?? {}
const props = defineProps({
  status: {
    type: String,
  },
  authPages: Object,
  userInfo: Object,
})

const form = useForm({
  email: props.userInfo?.email ?? "",
})

const submit = () => {
  form.post(route("verification.send"), {
    onSuccess: () => {

      function showAlert() {
        Swal.fire({
          icon: "success",
          title: "<h2 style='font-size: 20px;'>A new verification link has been resent to your email</h2>",
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
const verificationLinkSent = computed(() => props.status === "verification-link-sent")
</script>

<template>
  <Head title="Email Verification" />
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

        <div class="d-flex justify-content-end">
          <form method="POST" action="#">
            <button type="button" @click="logout()" class="signin-button">
              <Icon icon="fe:logout"></Icon>
              <span>{{ trans('Logout') }}</span>
            </button>
          </form>
        </div>

        <div class="d-flex flex-column align-items-center justify-content-center text-center px-0 px-lg-4">
          <div class="mb-3">
            <img src="assets/images/email.png" alt="Email Icon" width="80" />
          </div>

          <h2 class="fw-bold mb-3">{{ trans("Verify your email address") }}</h2>

          <p v-if="!verificationLinkSent" class="text-muted">
            {{ trans("We have sent a verification link to") }} 
            <strong class="d-block">{{ props.userInfo?.email }}</strong>
          </p>

          <p class="text-muted">
            {{ trans("Click on the link to complete the verification process.") }}<br />
            <span class="text-secondary">
              {{ trans("You might need to") }} 
              <strong class="text-dark">{{ trans("check your spam folder.") }}</strong>
            </span>
          </p>

          <div class="d-flex gap-3 mt-4">
            <button @click="submit" class="signin-btn px-4">
              {{ trans("Resend email") }}
            </button>
            <Link class="return-btn signin-btn px-4" :href="route('home')">
              {{ trans("Return to Site") }} →
            </Link>
          </div>

          <p class="text-muted mt-4">
            {{ trans("You can reach us on") }}
            <a href="mailto:support@virtualcardprovider.com" class="fw-bold">support@virtualcardprovider.com</a>
            {{ trans("if you have any questions.") }}
          </p>
        </div>
      </div>
    </div>
  </div>
</template>
<style>
  .return-btn{
    color: #fff;
    background-color: #026f5e !important;
  }
  .fw-bold{
    color: #026f5e;
  }

  div:where(.swal2-icon){
    border-color: #026f5e3d;
  }
</style>