<template>
  <main class="container p-4 sm:p-6">
    <PageHeader :title="trans('Card Settings')" :buttons="buttons" />
    <div class="space-y-6">
      <div class="grid grid-cols-1 lg:grid-cols-12">
        <div class="lg:col-span-5">
          <strong>{{ trans('Card Settings') }}</strong>
          <p>{{ trans('Edit you card global settings') }}</p>
        </div>
        <div class="lg:col-span-7">
          <form @submit.prevent="update">
            <div class="card">
              <div class="card-body">
                <div class="mb-2">
                  <label class="label">{{ trans('Base URL') }}</label>
                  <input
                    type="text"
                    name="base_url"
                    v-model="form.base_url"
                    required=""
                    class="input"
                  />
                </div>

                <div class="mb-2">
                  <label class="label">{{ trans('Client ID') }}</label>
                  <input
                    type="text"
                    name="client_id"
                    v-model="form.client_id"
                    required=""
                    class="input"
                  />
                </div>

                <div class="mb-2">
                  <label class="label">{{ trans('Client Secret') }}</label>
                  <input
                    type="text"
                    name="client_secret"
                    v-model="form.client_secret"
                    required=""
                    class="input"
                  />
                </div>

                <div class="mb-2">
                  <label class="label">{{ trans('Redirect URL') }}</label>
                  <input
                    type="text"
                    name="redirect_url"
                    v-model="form.redirect_url"
                    required=""
                    class="input"
                  />
                </div>

                <div class="mb-2">
                  <label class="label">{{ trans('Interval') }}</label>
                  <select class="select" name="interval" v-model="form.interval">                    
                    <option value="TOTAL">{{ trans('TOTAL') }}</option>
                    <!-- <option value="ANNUAL">{{ trans('ANNUAL') }}</option>
                    <option value="DAILY">{{ trans('DAILY') }}</option>
                    <option value="MONTHLY">{{ trans('MONTHLY') }}</option> 
                    <option value="QUARTERLY">{{ trans('QUARTERLY') }}</option> 
                    <option value="TERTIARY">{{ trans('TERTIARY') }}</option> 
                    <option value="WEEKLY">{{ trans('WEEKLY') }}</option> 
                    <option value="YEARLY">{{ trans('YEARLY') }}</option> -->
                  </select>
                </div>

                <div class="mt-3">
                  <SpinnerBtn :processing="form.processing" :btn-text="trans('Save Changes')" />
                </div>
              </div>
            </div>
          </form>
        </div>
      </div>
    </div>
  </main>
</template>

<script>
import AdminLayout from '@/Layouts/Admin/AdminLayout.vue'
import SpinnerBtn from '@/Components/SpinnerBtn.vue'
import toast from '@/Composables/toastComposable'
export default {
  layout: AdminLayout
}
</script>

<script setup>
import PageHeader from '@/Layouts/Admin/PageHeader.vue'
import { useForm } from '@inertiajs/vue3'

const props = defineProps([
  'id',
  'base_url',
  'client_id',
  'client_secret',
  'redirect_url',
  'interval'
])

const form = useForm({
  base_url: props.base_url,
  client_id: props.client_id,
  client_secret: props.client_secret,
  redirect_url: props.redirect_url,
  interval: props.interval
})

function update() {
  const url = route('admin.developer-settings.update', props.id)
  form.put(url, {
    onSuccess: () => {
      toast.success("Settings has been updated successfully")
    }
  })
}
</script>
