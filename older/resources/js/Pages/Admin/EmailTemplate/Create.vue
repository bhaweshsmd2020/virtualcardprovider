<script setup>
import AdminLayout from "@/Layouts/Admin/AdminLayout.vue"
import PageHeader from "@/Layouts/Admin/PageHeader.vue"
import { useForm } from "@inertiajs/vue3"
import SummernoteEditor from '@/Components/Admin/SummernoteEditor.vue'
const props = defineProps()
defineOptions({ layout: AdminLayout })

const form = useForm({
  subject: "",
  body: "",
  status: "1",
})
</script>

<template>
  <main class="container p-4 sm:p-6">
    <PageHeader />
    <div class="space-y-6">
      <form @submit.prevent="form.post(route('admin.email-templates.store'))">
        <div class="grid grid-cols-12">
          <div class="lg:col-span-5">
            <strong>{{ trans("Create Email Template") }}</strong>
            <p>{{ trans("create email template") }}</p>
          </div>
          <div class="card-wrapper lg:col-span-7">
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
                <div class="mb-2">
                  <label for="body">{{ trans("Body") }}</label>
                  <SummernoteEditor v-model="form.body" />
                  <div class="invalid-feedback text-danger d-block" v-if="form.errors.body">
                    {{ form.errors.body }}
                  </div>
                </div>
                <div class="mb-2">
                  <label for="status">{{ trans("Status") }}</label>
                  <select v-model="form.status" class="select" name="status" id="status">
                    <option value="1">{{ trans("Active") }}</option>
                    <option value="0">{{ trans("Inactive") }}</option>
                  </select>
                  <div class="invalid-feedback text-danger d-block" v-if="form.errors.status">
                    {{ form.errors.status }}
                  </div>
                </div>
                <div class="from-group row mt-3">
                  <div class="col-lg-12">
                    <button class="btn btn-primary">{{ trans("Create") }}</button>
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
