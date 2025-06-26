<script setup>
import { Link, router, useForm } from '@inertiajs/vue3'

import InputFieldError from '@/Components/InputFieldError.vue'
import UserLayout from '@/Layouts/User/UserLayout.vue'
import toast from '@/Composables/toastComposable'
import PageHeader from '@/Layouts/Admin/PageHeader.vue'
import { ref } from 'vue'
import trans from '@/Composables/transComposable'
import SpinnerBtn from '@/Components/SpinnerBtn.vue'
import Swal from 'sweetalert2'

defineOptions({ layout: UserLayout })
const props = defineProps(['user', 'google2fa'])
const enabling2FA = ref(false)

const form = useForm({
  first_name: props.user.first_name,
  last_name: props.user.last_name,
  email: props.user.email,
  phone: props.user.phone,
  current_password: ''
})

const submit = () => {
  let uri = route('user.account-settings.update')
  form.put(uri, {
    preserveState: true,
    onSuccess: () => {
      function showAlert() {
        Swal.fire({
          icon: "success",
          title: "<h2 style='font-size: 20px;'>Profile has been updated successfully</h2>",
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
      }
      showAlert();
      form.current_password = ''
    }
  })
}

const google2faForm = useForm({
  one_time_password: '',
  secret: props.google2fa?.secret
})

const enable2FA = () => {
  google2faForm.post('/user/google2fa/authenticate', {
    preserveScroll: true
  }, {
    onSuccess: () => {
      Swal.fire({
        icon: "success",
        title: "<h2 style='font-size: 20px;'>Two-factor authentication has been enabled</h2>",
        confirmButtonText: "Okay",
        allowOutsideClick: false,
        customClass: {
          popup: "custom-swal-popup",
        },
        didOpen: (toast) => {
          const closeButton = document.createElement("button");
          closeButton.innerHTML = "&times;";
          closeButton.classList.add("swal-close-btn");
          closeButton.onclick = () => Swal.close();
          toast.appendChild(closeButton);
        },
      });
    }
  });
};

const disable2FA = () => {
  router.post('/user/google2fa/disable', {}, {
    onSuccess: () => {
      Swal.fire({
        icon: "success",
        title: "<h2 style='font-size: 20px;'>Two-factor authentication has been disabled</h2>",
        confirmButtonText: "Okay",
        allowOutsideClick: false,
        customClass: {
          popup: "custom-swal-popup",
        },
        didOpen: (toast) => {
          const closeButton = document.createElement("button");
          closeButton.innerHTML = "&times;";
          closeButton.classList.add("swal-close-btn");
          closeButton.onclick = () => Swal.close();
          toast.appendChild(closeButton);
        },
      });
    }
  });
};
</script>

<template>
  <main class="container flex-grow p-4 sm:p-6">
    <PageHeader title="Account Settings" :buttons="[]" />
    <div class="space-y-6">
      <div class="mx-auto max-w-4xl">
        <form class="card card-body" @submit.prevent="submit" method="post">
          <h4>{{ trans('Edit Profile') }}</h4>
          <div class="mt-4 grid grid-cols-2 gap-5">
            <div class="w-full">
              <label class="label mb-1">
                {{ trans('First Name') }} <span class="text-red-600">*</span>
              </label>
              <input class="input" type="text" v-model="form.first_name" placeholder="John" />
              <InputFieldError :message="form.errors.first_name" />
            </div>
            <div class="w-full">
              <label class="label mb-1">
                {{ trans('Last Name') }}
              </label>
              <input
                class="input"
                type="text"
                v-model="form.last_name"
                placeholder="Doe"
                required
              />
              <InputFieldError :message="form.errors.last_name" />
            </div>

            <div>
              <label class="label mb-1"
                >{{ trans('Phone Number') }} <span class="text-red-600">*</span></label
              >
              <input class="input" type="tel" v-model="form.phone" placeholder="+810 989 989 989" />
              <InputFieldError :message="form.errors.phone" />
            </div>
            <template v-if="!props.user.provider && !props.user.provider_id">
              <div>
                <label class="label mb-1"
                  >{{ trans('Email') }} <span class="text-red-600">*</span></label
                >
                <input
                  class="input"
                  type="email"
                  v-model="form.email"
                  placeholder="zubayerhasan@gmal.com"
                />
                <InputFieldError :message="form.errors.email" />
              </div>
              <div>
                <label class="label mb-1">
                  {{ trans('Current Password') }} <span class="text-red-600">*</span>
                </label>
                <input
                  class="input"
                  type="password"
                  v-model="form.current_password"
                  placeholder="enter your current password"
                />
                <InputFieldError :message="form.errors.current_password" />
              </div>
            </template>
          </div>
          <SpinnerBtn
            type="submit"
            :processing="form.processing"
            class="btn btn-primary mb-4 mt-6"
            :btn-text="trans('Update Information')"
          />
          <template v-if="!props.user.provider && !props.user.provider_id">
            <div class="text-sm">
              {{ trans('Want to change the password?') }}
              <Link :href="route('user.change-password')" class="font-medium text-primary-800">
                {{ trans('Click here') }}
              </Link>
            </div>
          </template>
        </form>

        <div class="card card-body my-10">
          <h4 class="mb-4">{{ trans('Two Factor Authentication') }}</h4>
          <div
            v-if="enabling2FA && !google2fa.enabled"
            class="flex flex-col items-center gap-5 lg:flex-row"
          >
            <div class="flex flex-col items-center">
              <div
                class="scale-90 overflow-hidden rounded border"
                v-html="google2fa.qrCodeUrl"
              ></div>

              <p class="text-sm">{{ google2fa.secret }}</p>
            </div>
            <div class="flex w-full flex-col gap-3">
              <div>
                <strong>{{ trans('Steps') }}: </strong>
                <ol class="list-inside list-decimal space-y-1 text-sm">
                  <li>
                    {{ trans('Scan the qr code to register your Google Authenticator') }}
                  </li>
                  <li>
                    {{ trans('Enter the one time password to confirm the registration') }}
                  </li>
                </ol>
              </div>
              <form @submit.prevent="enable2FA" class="w-full">
                <div class="input-group">
                  <input
                    v-model="google2faForm.one_time_password"
                    type="text"
                    class="input"
                    placeholder="Enter one time password"
                  />
                  <SpinnerBtn
                    type="submit"
                    :processing="google2faForm.processing"
                    class="btn btn-primary"
                    :btn-text="trans('Verify')"
                  />
                </div>
                <InputFieldError :message="google2faForm.errors.one_time_password" />
              </form>
              <div class="w-full text-end">
                <button
                  type="button"
                  v-show="enabling2FA"
                  class="btn btn-dark w-full"
                  @click="enabling2FA = false"
                >
                  {{ trans('Close') }}
                </button>
              </div>
              <div class="flex flex-col items-center justify-between gap-2 lg:flex-row">
                <p class="flex items-center text-sm md:text-base">
                  <span class="mr-1">
                    {{ trans('Google Authenticator App') }}
                  </span>
                  <i class="bx bx-link-external"></i>
                </p>
                <div class="flex items-center gap-2">
                  <a
                    href="https://play.google.com/store/apps/details?id=com.google.android.apps.authenticator2"
                    target="_blank"
                  >
                    <img class="w-32" src="/assets/images/google_play.png" alt="google_play" />
                  </a>
                  <a
                    href="https://apps.apple.com/us/app/google-authenticator/id388497605"
                    target="_blank"
                  >
                    <img class="w-32" src="/assets/images/apple_store.png" alt="apple_store" />
                  </a>
                </div>
              </div>
            </div>
          </div>
          <div class="mt-3">
            <button
              type="button"
              v-if="google2fa.enabled"
              @click="disable2FA"
              class="btn btn-danger"
            >
              {{ trans('Disable Two Factor Authentication') }}
            </button>
            <button
              type="button"
              v-else-if="!enabling2FA && !google2fa.enabled"
              class="btn btn-dark"
              @click="enabling2FA = true"
            >
              {{ trans('Enable Two Factor Authentication') }}
            </button>
          </div>
        </div>
      </div>
    </div>
  </main>
</template>
