<template>
     <main class="container p-4 sm:p-6">
        <Head title="Newsletter Settings" />
        <PageHeader :title="trans('Newsletter Settings')" :buttons="buttons" />
        <div class="space-y-6">
            <div class="grid grid-cols-1 lg:grid-cols-12">
                <div class="lg:col-span-5">
                    <strong>{{ trans('Newsletter Developer Settings (mailchimp)') }}</strong>
                    <p>{{ trans('Edit you application Newsletter Api settings') }}</p>
                </div>
                <div class="lg:col-span-7">
                    <form @submit.prevent="update">
                        <div class="card">
                            <div class="card-body">
                                <div class="mb-2">
                                    <label class="label">{{ trans('Newsletter Api Key') }}</label>
                                    <input type="text" v-model="form.NEWSLETTER_API_KEY" class="input">
                                </div>
                                <div class="mb-2">
                                    <label class="label">{{ trans('Newsletter List ID') }}</label>
                                    <input type="text" v-model="form.NEWSLETTER_LIST_ID" class="input">
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
  import toast from '@/Composables/toastComposable'
  import SpinnerBtn from '@/Components/SpinnerBtn.vue'
  export default {
    layout: AdminLayout
  }
  </script>
  
  <script setup>
  import PageHeader from '@/Layouts/Admin/PageHeader.vue'
  import { Head, useForm } from '@inertiajs/vue3'
  
  const props = defineProps(['segments', 'buttons', 'id', 
    'NEWSLETTER_API_KEY',
    'NEWSLETTER_LIST_ID'
    ]);

const form = useForm({
    NEWSLETTER_API_KEY: props.NEWSLETTER_API_KEY,
    NEWSLETTER_LIST_ID: props.NEWSLETTER_LIST_ID,
})


function update() {
    const url = route('admin.developer-settings.update', props.id)
    form.put(url, {
        onSuccess: () => {
            toast.success('Settings has been updated successfully')
        }
    })
}
  </script>
  