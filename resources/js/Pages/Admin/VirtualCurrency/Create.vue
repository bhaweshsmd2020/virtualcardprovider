<script setup>
import AdminLayout from '@/Layouts/Admin/AdminLayout.vue'
import PageHeader from '@/Layouts/Admin/PageHeader.vue'
import { useForm } from '@inertiajs/vue3'
import SpinnerBtn from '@/Components/SpinnerBtn.vue'
import { onMounted } from 'vue'
defineOptions({ layout: AdminLayout })
const props = defineProps(['currencies'])

const form = useForm({
  currency: '',
  preview: '',
  is_default: false,
  status: 'active',
  precision: 2,
  rates: []
})
onMounted(() => {
  props.currencies.forEach((currency) => {
    form.rates.push({
      id: currency.id,
      currency: currency.currency,
      rate: 1
    })
  })
})

const submit = () => {
  form.post(route('admin.virtual-currency.store'))
}
</script>
<template>
  <main class="container flex-grow p-4 sm:p-6">
    <PageHeader title="Create a Currency" />
    <div class="space-y-6">
      <div class="mx-auto max-w-[800px]">
        <form @submit.prevent="submit" class="space-y-3">
          <div class="card card-body">
            <div class="mb-2">
              <label class="label mb-1">{{ trans('Currency') }}</label>
              <input v-model="form.currency" type="text" name="currency" required class="input" />
            </div>

            <div class="mb-2 mt-2">
              <label class="label mb-1">{{ trans('Image (Preview)') }}</label>
              <input
                @input="(e) => (form.preview = e.target.files[0])"
                type="file"
                class="input"
                name="preview"
                accept="image/*"
              />
            </div>

            <div>
              <label class="label mb-1">{{ trans('Status') }}</label>
              <select v-model="form.status" class="select" name="status">
                <option value="active">{{ trans('Active') }}</option>
                <option value="inactive">{{ trans('Inactive') }}</option>
              </select>
            </div>
            <div>
              <label class="label mb-1">
                {{ trans('Precision') }} <span class="text-xs">( {{ trans('Min:2') }} )</span>
              </label>
              <input
                type="number"
                step="any"
                placeholder="Precision"
                class="input"
                v-model="form.precision"
              />
            </div>
          </div>
          <p class="col-span-full">{{ trans('Exchange Rate') }}</p>
          <div class="card card-body grid grid-cols-2 gap-3">
            <template v-for="(rate, i) in form.rates" :key="i">
              <div>
                <label class="label mb-1 uppercase">{{ rate.currency }} </label>
                <input
                  type="number"
                  step="any"
                  placeholder="Rate"
                  class="input"
                  v-model="rate.rate"
                />
                <Link
                  class="text-xs hover:underline"
                  :href="route('admin.virtual-currency.edit', rate.id)"
                >
                  * {{ trans('update related exchange rate') }}
                </Link>
              </div>
            </template>
          </div>
          <div class="mb-2 mt-3">
            <div>
              <SpinnerBtn
                classes="btn btn-primary"
                :processing="form.processing"
                :btn-text="trans('Create')"
              />
            </div>
          </div>
        </form>
      </div>
    </div>
  </main>
</template>
