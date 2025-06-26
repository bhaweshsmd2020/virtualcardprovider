<script setup>
import sharedComposable from '@/Composables/sharedComposable'
import { Head, useForm } from "@inertiajs/vue3"
import InputFieldError from "@/Components/InputFieldError.vue"
import AuthLayout from "@/Layouts/AuthLayout.vue"
import SpinnerBtn from "@/Components/SpinnerBtn.vue"
import LeftBanner from "@/Pages/Auth/Partials/LeftBanner.vue"
import Swal from "sweetalert2"
import { onMounted } from "vue"
import Vue3Select from 'vue3-select'
import 'vue3-select/dist/vue3-select.css'

defineOptions({ layout: AuthLayout })

const { authUser, logout, uiAvatar } = sharedComposable()

const props = defineProps({
  status: String,
  countries: Array,
  authPages: Object,
  userInfo: Object,
  flash: Object, // ✅ added for SweetAlert
})

const loginData = props.authPages?.login ?? {}

const form = useForm({
  phone: props.userInfo?.phone ?? "",
  otp_code: "",
  country_code: props.userInfo?.country_code ?? "",
})

// ✅ SweetAlert on mount if flash message is present
onMounted(() => {
  if (props.flash?.message) {
    Swal.fire({
      icon: props.flash?.type === "success" ? "success" : "error",
      title: `<h2 style='font-size: 20px;'>${props.flash.message}</h2>`,
      confirmButtonText: "Okay",
      allowOutsideClick: false,
      customClass: { popup: "custom-swal-popup" },
    })
  }
})

const submit = () => {
  form.post(route("phone.verification.store"))
}
</script>

<template>
  <Head title="Phone Number Verification"></Head>
  
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

        <h4>Verify your phone number</h4>
        <p class="mb-50">Verify your phone number</p> 
          
        <form @submit.prevent="submit">
          <div class="signin-banner-from-box">
            <div class="row">
              <div class="col-12 mb-15">
                <div class="d-flex align-items-center">
                  <Vue3Select
                    v-model="form.country_code"
                    :options="countries"
                    :reduce="country => country.phonecode"
                    :getOptionLabel="country => `${country.name} (${country.phonecode})`"
                    placeholder="Select Country"
                    class="py-3 px-1 text-left w-50 border border-end-0"
                    style="padding: 0px;"
                  />
                  <div class="postbox__comment-input w-100">
                    <input
                      type="number"
                      maxlength="15"
                      v-model="form.phone"
                      class="border p-3 w-100"
                    />
                    <span class="floating-label">{{ trans("Phone") }}</span>
                  </div>
                </div>
              </div>
            </div>

            <InputFieldError :message="form.errors.country_code" />
            <InputFieldError :message="form.errors.phone" />
          </div>
          <div class="my-20 signin-banner-from-btn">
            <SpinnerBtn
              :processing="form.processing"
              :btn-text="trans('Send Verification OTP')"
              class="signin-btn w-100"
            />
          </div>
        </form>
  
      </div>
    </div>
  </div>
</template>

<style>
.vs__dropdown-toggle{
    height: 53px;
    box-shadow: inset 0 0 0 1px #E5E5E5;
    border: none;
  }

  .vs__search, .vs__search:focus{
    font-size: 12px;
    padding: 0px 15px;
    font-weight: 500;
  }

  .vs__dropdown-menu{
    overflow-x: hidden;
  }
  .vs__selected {
    padding-left: 12px;
  }

  .v-select{
    padding: 0px !important;
    border: 0px !important;
    background: #fff;
  }
  </style>