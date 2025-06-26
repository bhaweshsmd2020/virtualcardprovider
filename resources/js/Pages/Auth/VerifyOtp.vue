<script setup>
import sharedComposable from '@/Composables/sharedComposable'
import { Head, router, useForm } from "@inertiajs/vue3"
import InputFieldError from "@/Components/InputFieldError.vue"
import AuthLayout from "@/Layouts/AuthLayout.vue"
import SpinnerBtn from "@/Components/SpinnerBtn.vue"
import LeftBanner from "@/Pages/Auth/Partials/LeftBanner.vue"
import { ref } from "vue"
import { onMounted } from "vue"
import Swal from "sweetalert2"

defineOptions({ layout: AuthLayout })
const { authUser, logout, uiAvatar } = sharedComposable()
const props = defineProps({
  status: String,
  otpInfo: {
    type: Object,
    default: {
      remaining: 0,
      phone: null,
    },
  },
  authPages: Object,
  flash: Object,
})

onMounted(() => {
  if (props.flash && props.flash.message) {
    Swal.fire({
      icon: props.flash.type === "success" ? "success" : "error",
      title: `<h2 style='font-size: 20px;'>${props.flash.message}</h2>`,
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
    })
  }
})

const otpDigits = ref(["", "", "", "", "", ""]) // Store each digit separately
const otpRefs = ref([]) // To handle auto-focus

const moveToNext = (index) => {
  if (otpDigits.value[index] && index < 5) {
    otpRefs.value[index + 1].focus() // Move to next input
  }
  form.otp_code = otpDigits.value.join("") // Combine digits
}

const moveToPrev = (index) => {
  if (!otpDigits.value[index] && index > 0) {
    otpRefs.value[index - 1].focus() // Move to previous input
  }
}

const loginData = props.authPages?.login ?? {}

const form = useForm({
  otp_code: "",
})

const remainingCountDown = ref(props.otpInfo.remaining)

onMounted(() => {
  if (remainingCountDown.value > 0) {
    setInterval(() => {
      if (remainingCountDown.value > 0) {
        remainingCountDown.value = Math.floor(remainingCountDown.value - 1);
      }
    }, 1000)
  }

  setTimeout(() => {
    props.status = ''
  }, 5000)
})

const submit = () => {
  form.otp_code = otpDigits.value.join("") // Combine digits before submitting
  
  form.post(route("otp.verification.store"), {
    preserveScroll: true,  // Prevents page refresh
    onSuccess: (response) => {
      if (props.flash?.message) {
        Swal.fire({
          icon: props.flash.type === "success" ? "success" : "error",
          title: `<h2 style='font-size: 20px;'>${props.flash.message}</h2>`,
          confirmButtonText: "Okay",
          allowOutsideClick: false,
          customClass: { popup: "custom-swal-popup" },
        })
      }
    },
    onError: (errors) => {
      if (errors.otp_code) {
        Swal.fire({
          icon: "error",
          title: `<h2 style='font-size: 20px;'>${errors.otp_code}</h2>`,
          confirmButtonText: "Okay",
          allowOutsideClick: false,
          customClass: { popup: "custom-swal-popup" },
        })
      }
    },
  })
}

const phoneVerificationUrl = route("phone.verification.index")

const resendOtp = () => {
  router.get(route("phone.verification.index"))
}
</script>

<template>
  <Head title="OTP Verification"></Head>
  <!-- tp-banner-area-start -->
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

        <div class="d-flex justify-content-end mb-30">
          <form method="POST" action="#">
            <button type="button" @click="logout()" class="signin-button">
              <Icon icon="fe:logout"></Icon>
              <span>{{ trans('Logout') }}</span>
            </button>
          </form>
        </div>

        <div class="otp-card">
          <h4 class="otp-title">{{ trans("OTP Verification") }}</h4>
          <p class="otp-text">
            {{ trans("Enter OTP Code sent to") }} {{ otpInfo.country_code }} ******{{ (otpInfo.phone ?? "").toString().slice(-4) }}
          </p> 
          <p class="otp-text">
            {{ trans("Not your number") }} <a class="resend-btn" :href="phoneVerificationUrl">Change Number</a>
          </p>  
          
          <form @submit.prevent="submit">
            <div class="otp-input-container">
              <input
                v-for="(digit, index) in otpDigits"
                :key="index"
                v-model="otpDigits[index]"
                ref="otpRefs"
                type="text"
                class="otp-input"
                maxlength="1"
                pattern="[0-9]"
                inputmode="numeric"
                @input="moveToNext(index)"
                @keydown.backspace="moveToPrev(index)"
              />
            </div>
  
            <p class="resend-text">
              {{ trans("Didn’t receive OTP code?") }}
            </p>
  
            <p class="resend-otp">
              <button type="button" :disabled="remainingCountDown > 1" @click="resendOtp" class="resend-btn">
                {{ trans("Resend Code") }}
              </button>
              <span v-show="remainingCountDown > 1" class="countdown">({{ remainingCountDown }}s)</span>
            </p>
  
            <SpinnerBtn
              :processing="form.processing"
              :btn-text="trans('Verify & Proceed')"
              class="signin-btn w-100"
            />
          </form>
  
        </div>
      </div>
    </div>
  </div>
</template>

<style>
  .otp-card {
    height: 400px;
    background: #f8f9fc;
    border-radius: 10px;
    padding: 75px 25px;
    width: 350px;
    text-align: center;
    box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1);
  }

  .otp-title {
    font-size: 22px;
    font-weight: bold;
    margin-bottom: 10px;
  }

  .otp-text {
    color: #6c757d;
    font-size: 14px;
  }

  .otp-input-container {
    display: flex;
    justify-content: center;
    gap: 8px;
    margin: 15px 0;
  }

  .otp-input {
    width: 40px;
    height: 40px;
    text-align: center;
    font-size: 20px;
    font-weight: bold;
    border: 1px solid #ced4da;
    border-radius: 5px;
    background: #600ee41c;
  }

  .otp-info {
    color: #6c757d;
    font-size: 13px;
  }

  .resend-text {
    margin-top: 10px;
    font-size: 13px;
    color: #6c757d;
    margin-bottom: 0px;
  }

  .resend-otp {
    font-size: 13px;
    color: #6c757d;
    display: inline-flex
  }

  .resend-btn {
    background: none;
    border: none;
    color: #026f5e;
    font-weight: bold;
    cursor: pointer;
  }

  .resend-btn:disabled {
    color: gray;
    cursor: not-allowed;
  }

  .countdown {
    color: #6c757d;
    font-weight: bold;
  }

  .signin-btn {
    width: 100%;
    padding: 0;
  }

  .verify-btn:hover {
    background-color: #0056b3;
  }
</style>