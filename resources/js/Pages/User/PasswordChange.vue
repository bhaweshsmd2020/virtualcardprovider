<script setup>
import InputFieldError from "@/Components/InputFieldError.vue"
import { useForm } from "@inertiajs/vue3"
import UserLayout from "@/Layouts/User/UserLayout.vue"
import toast from "@/Composables/toastComposable"
import SpinnerBtn from "@/Components/SpinnerBtn.vue"
import PageHeader from "@/Layouts/Admin/PageHeader.vue"
import Swal from 'sweetalert2'

defineOptions({ layout: UserLayout })
const props = defineProps(["segments"])
const form = useForm({
  current_password: "",
  new_password: "",
  new_password_confirmation: "",
})

const submit = () => {
  form.put(route("user.update-password"), {
    onSuccess: () => {
      function showAlert() {
        Swal.fire({
          icon: "success",
          title: "<h2 style='font-size: 20px;'>Password has been updated successfully</h2>",
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
      
      form.reset()
    },
  })
}
</script>

<template>
  <main class="container flex-grow p-4 sm:p-6">
    <PageHeader title="Change Password" :buttons="[]" />
    <div class="card card-body mx-auto max-w-3xl">
      <h4 class="mb-4">{{ trans("Change Password") }}</h4>
      <form @submit.prevent="submit" class="space-y-6">
        <div>
          <label class="label mb-1"> {{ trans("Old Password") }} *</label>
          <input class="input" type="password" v-model="form.current_password" />
          <InputFieldError :message="form.errors.current_password" />
        </div>

        <div>
          <label class="label mb-1"> {{ trans("New Password") }} *</label>
          <input class="input" type="password" v-model="form.new_password" />
          <InputFieldError :message="form.errors.new_password" />
        </div>

        <div>
          <label class="label mb-1">{{ trans("Confirm Password") }} *</label>
          <input class="input" type="password" v-model="form.new_password_confirmation" />
          <InputFieldError :message="form.errors.new_password_confirmation" />
        </div>

        <div class="flex items-center gap-3">
          <SpinnerBtn
            :processing="form.processing"
            classes="btn btn-primary"
            :btn-text="trans('Change Password')"
          />
          <Link :href="route('user.account-settings')" class="btn btn-danger">
            {{ trans("Cancel") }}
          </Link>
        </div>
      </form>
    </div>
  </main>
</template>
