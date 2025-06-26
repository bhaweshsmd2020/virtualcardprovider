<script setup>
import AdminLayout from '@/Layouts/Admin/AdminLayout.vue'
import PageHeader from '@/Layouts/Admin/PageHeader.vue'
import SpinnerBtn from '@/Components/SpinnerBtn.vue'
import { useForm } from '@inertiajs/vue3'
import toast from '@/Composables/toastComposable'

defineOptions({ layout: AdminLayout })
const props = defineProps([
  'segments',
  'id',
  'creditCardCurrencies',
  'STRIPE_API_KEY',
  'STRIPE_PUBLIC_API_KEY',
  'STRIPE_CURRENCY'
])

const form = useForm({
  STRIPE_API_KEY: props.STRIPE_API_KEY,
  STRIPE_PUBLIC_API_KEY: props.STRIPE_PUBLIC_API_KEY,
  STRIPE_CURRENCY: props.STRIPE_CURRENCY
})

function update() {
  form.put(route('admin.developer-settings.update', props.id), {
    onSuccess: () => {
      toast.success('Settings has been updated successfully')
    }
  })
}
</script>
<template>
  <main class="container p-4 sm:p-6">
    <PageHeader :title="trans('Stripe Settings')" />
    <div class="space-y-6">
      <div class="grid grid-cols-1 lg:grid-cols-12">
        <div class="lg:col-span-5">
          <strong>{{ trans('Stripe Settings') }}</strong>
          <p>{{ trans('Edit Stripe Api settings') }}</p>
        </div>
        <div class="lg:col-span-7">
          <form @submit.prevent="update">
            <div class="card">
              <div class="card-body">
                <div class="mb-2">
                  <label class="label mb-1">{{ trans('Stripe Api Key') }}</label>
                  <input
                    type="text"
                    name="name"
                    v-model="form.STRIPE_API_KEY"
                    required
                    class="input"
                  />
                </div>
                <div class="mb-2">
                  <label class="label mb-1">{{ trans('Stripe Public Api Key') }}</label>
                  <input
                    type="text"
                    name="name"
                    v-model="form.STRIPE_PUBLIC_API_KEY"
                    required
                    class="input"
                  />
                </div>

                <div class="mb-2">
                  <label class="label mb-1">{{ trans('Stripe Currency') }}</label>
                  <select
                    class="select uppercase"
                    name="stripe_currency"
                    v-model="form.STRIPE_CURRENCY"
                  >
                    <option :value="cur" v-for="cur in creditCardCurrencies" :key="cur">
                      {{ cur }}
                    </option>
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
