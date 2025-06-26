<script setup>
import sharedComposable from '@/Composables/sharedComposable'
import InputFieldError from '@/Components/InputFieldError.vue'
import SpinnerBtn from '@/Components/SpinnerBtn.vue'
import trans from '@/Composables/transComposable'
import AuthLayout from '@/Layouts/AuthLayout.vue'
import { Head, useForm } from '@inertiajs/vue3'
import { onMounted, reactive, watch, nextTick } from 'vue'
import { ref } from 'vue'
defineOptions({ layout: AuthLayout })
const { authUser, logout, uiAvatar } = sharedComposable()
const inputValues = reactive(['', '', '', '', '', ''])
const inputs = ref(null)
const focusIndex = ref(0)
const secondPartIndexStart = ref(3)
const google2faForm = useForm({
  one_time_password: ''
})

const props = defineProps({
  authPages: Object,
})

const loginData = props.authPages?.login ?? {}

const shake = ref(false)

watch(
  () => google2faForm.errors.one_time_password,
  () => {
    if (google2faForm.errors.one_time_password) {
      shake.value = true
      setTimeout(() => {
        shake.value = false
      }, 1000)
    }
  }
)

const handleInput = (index) => {
  nextTick(() => {
    if (inputValues[index].length && index < inputValues.length - 1) {
      focusIndex.value = index + 1
      inputs.value[focusIndex.value].focus()
    } else if (!inputValues[index].length && index > 0) {
      focusIndex.value = index - 1
      inputs.value[focusIndex.value].focus()
    }
  })
  if (index === inputValues.length - 1 && inputValues[index].length) {
    enable2FA()
  }
}
onMounted(() => {
  inputs.value[focusIndex.value].focus()
})

const enable2FA = () => {
  google2faForm.one_time_password = inputValues.join('')
  google2faForm.clearErrors()
  google2faForm.post('/user/google2fa/authenticate')
}
</script>

<template>
  <Head title="Two Factor Authentication"></Head>
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
              <Icon icon="fe:logout" style="margin-top: 3px;"></Icon>
              <span>{{ trans('Logout') }}</span>
            </button>
          </form>
        </div>
        
        <div class="card mt-8 p-12 shadow-lg" :class="{ shake: shake }">          
          <h4 class="text-2xl font-semibold">{{ trans("Verify that it's you!") }}</h4>
          <p class="my-6 text-sm">
            {{ trans('Enter six-digit code from your authentication app') }}
          </p>
          
          <form @submit.prevent="enable2FA" class="account-login">
            <div class="mx-auto flex max-w-72 items-center">
              <input
                v-for="(item, index) in inputValues.slice(0, 3)"
                :key="index"
                type="tel"
                class="w-full border py-4 focus:border-primary-500 focus:ring-1 focus:ring-primary-500 dark:border-gray-700 dark:bg-secondary-900"
                :class="{
                  'rounded-l-md': index == 0,
                  'z-50': index == focusIndex,
                  'rounded-r-md': index == 2
                }"
                v-model="inputValues[index]"
                @keyup="handleInput(index)"
                ref="inputs"
                maxlength="1"
                pattern="[0-9]"
                :readonly="index !== focusIndex"
              />
              <span class="scale-[2] px-4 opacity-60">-</span>
              <input
                v-for="(item, index) in inputValues.slice(secondPartIndexStart)"
                :key="secondPartIndexStart + index"
                type="tel"
                class="w-full border py-4 focus:border-primary-500 focus:ring-1 focus:ring-primary-500 dark:border-gray-700 dark:bg-secondary-900"
                :class="{
                  'rounded-l-md': index == 0,
                  'z-50': secondPartIndexStart + index == focusIndex,
                  'rounded-r-md': index == 2
                }"
                v-model="inputValues[secondPartIndexStart + index]"
                @keyup="handleInput(secondPartIndexStart + index)"
                ref="inputs"
                maxlength="1"
                pattern="[0-9]"
                :readonly="secondPartIndexStart + index !== focusIndex"
              />
            </div>
            <SpinnerBtn
              type="submit"
              :processing="google2faForm.processing"
              class="btn btn-primary mt-6 w-full py-3 font-semibold"
              :btn-text="trans('Continue')"
            />
            <InputFieldError :message="google2faForm.errors.one_time_password" />
            <p class="mt-4 text-xs">Do you want to recover TWO-Factor Authentication? or <a href="mailto:help@virtualcardprovider.com">Need help?</a></p>
          </form>
        </div>
      </div>
    </div>
  </div>
</template>

<style>
.shake {
  animation: shake 700ms cubic-bezier(0.36, 0.07, 0.19, 0.97) both;
  transform: translate3d(0, 0, 0);
}
@keyframes shake {
  10%,
  90% {
    transform: translate3d(-1px, 0, 0);
  }
  20%,
  80% {
    transform: translate3d(2px, 0, 0);
  }
  30%,
  50%,
  70% {
    transform: translate3d(-4px, 0, 0);
  }
  40%,
  60% {
    transform: translate3d(4px, 0, 0);
  }
}

.signin-button {
  display: flex;
}

.d-flex {
  display: flex !important;
}

.justify-content-end {
  justify-content: flex-end !important;
}

.register-image img {
    padding-top: 170px;
}
</style>
