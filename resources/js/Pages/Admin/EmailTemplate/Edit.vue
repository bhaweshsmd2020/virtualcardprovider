<script setup>
import AdminLayout from "@/Layouts/Admin/AdminLayout.vue"
import SpinnerBtn from "@/Components/SpinnerBtn.vue"
import toast from "@/Composables/toastComposable"
import PageHeader from "@/Layouts/Admin/PageHeader.vue"
import { useForm } from "@inertiajs/vue3"
import SummernoteEditor from '@/Components/Admin/SummernoteEditor.vue'

defineOptions({ layout: AdminLayout })

const props = defineProps(["emailtemplate"])

const form = useForm({
  subject: props.emailtemplate.subject,
  body: props.emailtemplate.body,
  status: props.emailtemplate.status,
})

const update = () => {
  const url = route("admin.email-templates.update", props.emailtemplate.id)

  form.put(url, {
    preserveScroll: true,
    onSuccess: () => {
      toast.success(trans("Sub admin profile updated"))
    },
  })
}
</script>

<template>
  <main class="container p-4 sm:p-6">
    <PageHeader />
    <div class="space-y-6">
      <form @submit.prevent="update">
        <div class="grid grid-cols-12">
          <div class="col-span-5">
            <strong>{{ trans("Edit Email Template") }}</strong>
            <p>{{ trans("Edit email template") }}</p>
          </div>
          <div class="col-span-7">
            <div class="card">
              <div class="card-body">
                <div class="mb-2">
                  <label for="subject">{{ trans("Subject") }}</label>
                  <input type="text" placeholder="Subject" v-model="form.subject" class="input" id="subject"
                    required="" autocomplete="off" />
                  <div class="invalid-feedback text-danger d-block" v-if="form.errors.subject">
                    {{ form.errors.subject }}
                  </div>
                </div>
                <div class="mb-2 mt-3">
                  <label class="label">{{ trans('Body') }}</label>
                  <SummernoteEditor v-model="form.body" />
                </div>
                <div class="mb-2">
                  <label>{{ trans("Status") }}</label>
                  <select name="status" class="select" v-model="form.status">
                    <option :value="1">{{ trans("Active") }}</option>
                    <option :value="0">{{ trans("Inactive") }}</option>
                  </select>
                </div>
                <div class="mb-2 mt-3">
                  <div class="col-lg-12">
                    <SpinnerBtn :processing="form.processing" :btn-text="trans('Update')" />
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </form>
    </div>
  </main>
</template>
