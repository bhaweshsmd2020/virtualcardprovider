<template>
  <main class="container p-4 sm:p-6">
    <Head title="SMTP Settings" />
    <PageHeader :title="trans('SMTP MAIL Settings')" :buttons="buttons" />
    <div class="space-y-6">
      <div class="grid grid-cols-1 lg:grid-cols-12">
        <div class="lg:col-span-5">
          <strong>{{ trans('SMTP mail Settings') }}</strong>
          <p>{{ trans('Edit you smtp settings for mail transaction') }}</p>
        </div>
        <div class="lg:col-span-7">
          <form @submit.prevent="update">
            <div class="card">
              <div class="card-body">
                <div class="mb-2">
                  <label class="label">{{ trans('Status') }}</label>
                  <select name="STATUS" class="select" v-model="form.STATUS">
                    <option value="1">{{ trans('Enable') }}</option>
                    <option value="0">{{ trans('Disable') }}</option>
                  </select>
                </div>
                <div class="mb-2">
                  <label class="label">{{ trans('Mail Driver') }}</label>
                  <select name="MAIL_DRIVER" v-model="form.MAIL_DRIVER" class="select">
                    <option value="sendmail">{{ trans('sendmail') }}</option>
                    <option value="smtp">{{ trans('smtp') }}</option>
                  </select>
                </div>
                <div class="mb-2">
                  <label class="label">{{ trans('Mail Host') }}</label>
                  <input
                    type="text"
                    name="MAIL_HOST"
                    v-model="form.MAIL_HOST"
                    class="input"
                    required=""
                  />
                </div>
                <div class="mb-2">
                  <label class="label">{{ trans('Mail Port') }}</label>
                  <input
                    type="text"
                    v-model="form.MAIL_PORT"
                    name="MAIL_PORT"
                    class="input"
                    required=""
                  />
                </div>
                <div class="mb-2">
                  <label class="label">{{ trans('Mail Username') }}</label>

                  <input
                    type="text"
                    v-model="form.MAIL_USERNAME"
                    name="MAIL_USERNAME"
                    class="input"
                    required=""
                  />
                </div>
                <div class="mb-2">
                  <label class="label">{{ trans('Mail Password') }}</label>

                  <input
                    type="text"
                    v-model="form.MAIL_PASSWORD"
                    name="MAIL_PASSWORD"
                    class="input"
                    required=""
                  />
                </div>
                <div class="mb-2">
                  <label class="label">{{ trans('Mail Encryption') }}</label>

                  <select name="MAIL_ENCRYPTION" v-model="form.MAIL_ENCRYPTION" class="select">
                    <option value="ssl">{{ trans('SSL') }}</option>
                    <option value="tls">{{ trans('TLS') }}</option>
                  </select>
                </div>
                <div class="mb-2">
                  <label class="label">{{ trans('Mail From Address') }}</label>

                  <input
                    type="email"
                    v-model="form.MAIL_FROM_ADDRESS"
                    name="MAIL_FROM_ADDRESS"
                    class="input"
                    placeholder="email"
                    required=""
                  />
                </div>
                <div class="mb-2">
                  <label class="label">{{ trans('Mail From Name') }}</label>

                  <input
                    type="text"
                    v-model="form.MAIL_FROM_NAME"
                    name="MAIL_FROM_NAME"
                    class="input"
                    placeholder="Website Name"
                    required=""
                  />
                </div>
                <div class="mb-2">
                  <label class="label">{{ trans('Incoming Mail') }}</label>

                  <input
                    type="email"
                    v-model="form.MAIL_TO"
                    name="MAIL_TO"
                    class="input"
                    placeholder="email"
                    required=""
                  />
                </div>
                <div class="mb-2">
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
import toast from '@/Composables/toastComposable'
import SpinnerBtn from '@/Components/SpinnerBtn.vue'
export default {
  layout: AdminLayout
}
</script>

<script setup>
import PageHeader from '@/Layouts/Admin/PageHeader.vue'
import { Head, useForm } from '@inertiajs/vue3'

const props = defineProps([
  'id',
  'mailDriver',
  'segments',
  'buttons',
  'STATUS',
  'MAIL_DRIVER',
  'MAIL_HOST',
  'MAIL_PORT',
  'MAIL_USERNAME',
  'MAIL_PASSWORD',
  'MAIL_ENCRYPTION',
  'MAIL_FROM_ADDRESS',
  'MAIL_FROM_NAME',
  'MAIL_TO'
])

const form = useForm({
  STATUS: props.STATUS,
  MAIL_DRIVER: props.MAIL_DRIVER,
  MAIL_HOST: props.MAIL_HOST,
  MAIL_PORT: props.MAIL_PORT,
  MAIL_USERNAME: props.MAIL_USERNAME,
  MAIL_PASSWORD: props.MAIL_PASSWORD,
  MAIL_ENCRYPTION: props.MAIL_ENCRYPTION,
  MAIL_FROM_ADDRESS: props.MAIL_FROM_ADDRESS,
  MAIL_FROM_NAME: props.MAIL_FROM_NAME,
  MAIL_TO: props.MAIL_TO
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
