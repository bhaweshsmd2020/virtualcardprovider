<script setup>
import InputFieldError from '@/Components/InputFieldError.vue'
import UserLayout from '@/Layouts/User/UserLayout.vue'
import toast from '@/Composables/toastComposable'
import { useForm } from '@inertiajs/vue3'
import SpinnerBtn from '@/Components/SpinnerBtn.vue'
import PageHeader from '@/Layouts/Admin/PageHeader.vue'
const props = defineProps(['subjects'])

defineOptions({ layout: UserLayout, })
import Swal from 'sweetalert2'

const form = useForm({
  subject: '',
  message: '',
  support_image:null
})
 
const handleFileUpload = (e) => {
  form.support_image = e.target.files[0];
};

const submit = () => {
  form.post(route('user.supports.store'), {
    onSuccess: () => {
      function showAlert() {
        Swal.fire({
          icon: "success",
          title: "<h2 style='font-size: 20px;'>Ticket created successfully</h2>",
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
  <main class="container flex-grow p-4 sm:p-6">
    <PageHeader title="Support" />
    <div class="space-y-6">
      <div class="card card-body mx-auto max-w-3xl">
        <h4 class="mb-4">{{ trans('Create Ticket') }}</h4>
        <form @submit.prevent="submit" enctype="multipart/form-data">
          <div class="mb-2">
            <label class="label mb-2">{{ trans('Subject') }}*</label>
            <!--  <input type="text" class="input" placeholder="Subject" v-model="form.subject" /> -->
             <select v-model="form.subject" name="subject" class="select" required >
                  <option disabled value="">{{ trans('Select type of help')}} needed</option>
                  <option v-for="(subject, key) in subjects" :key="subject" :value="key">
                    {{ subject }}
                  </option>
                </select>
            <InputFieldError :message="form.errors.subject" />
          </div>

          <div class="mb-2">
            <label class="label mb-2">{{ trans('Message') }}*</label>
            <textarea
              required
              v-model="form.message"
              class="textarea"
              placeholder="Write message...."
            ></textarea>
            <InputFieldError :message="form.errors.message" />
          </div>
          <div class="mb-2">
            <label class="label mb-2">{{ trans('Attachment') }}</label>
                <input
                  @change="handleFileUpload"
                  type="file"
                  class="input"
                  name="support_image"
                  accept="image/*"
                />
            <InputFieldError :message="form.errors.message" />
          </div>

          <div class="button-group d-inline-flex align-items-center mt-30">
            <SpinnerBtn
              type="submit"
              classes="btn btn-primary"
              :processing="form.processing"
              btn-text="Submit"
            />
          </div>
        </form>
      </div>
    </div>
  </main>
</template>
