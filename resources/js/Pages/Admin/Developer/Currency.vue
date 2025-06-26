<script setup>
import AdminLayout from '@/Layouts/Admin/AdminLayout.vue'
import PageHeader from '@/Layouts/Admin/PageHeader.vue'
import SpinnerBtn from '@/Components/SpinnerBtn.vue'
import { useForm } from '@inertiajs/vue3'
import toast from '@/Composables/toastComposable'
import { onMounted } from 'vue'

defineOptions({ layout: AdminLayout })
const props = defineProps([
  'segments',
  'id',
  'rates',
  'virtualCurrency',
  'currencies',
  'base_currency'
])

const form = useForm({
  _method: 'PATCH',
  currency: props.virtualCurrency.currency,
  preview: props.virtualCurrency.preview,
  precision: props.virtualCurrency.precision,
  rates: [],
  virtual_currency_id: props.virtualCurrency.id,
  icon: props.base_currency.icon,
  position: props.base_currency.position
})
onMounted(() => {
  for (const cur of props.currencies) {
    const find = props.rates.find((rate) => cur.id == rate.virtual_currency_exchange_id)

    form.rates.push({
      id: find ? find.virtual_currency_exchange_id : cur.id,
      currency: find ? find.exchange_currency?.currency : cur.currency,
      rate: find ? +find.rate : 1
    })
  }
})
function update() {
  form.post(route('admin.developer-settings.update', props.id), {
    onSuccess: () => {
      toast.success('Settings has been updated successfully')
    }
  })
}
</script>
<template>
  <main class="container p-4 sm:p-6">
    <PageHeader :title="trans('Currency Settings')" />
    <div class="space-y-6">
      <div class="grid grid-cols-1 lg:grid-cols-12">
        <div class="lg:col-span-5">
          <strong>{{ trans('App Default Currency Settings') }}</strong>
          <p>{{ trans('Edit App Currency settings') }}</p>
        </div>
        <div class="lg:col-span-7">
          <form @submit.prevent="update" class="space-y-3">
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
            <p class="col-span-full">{{ trans('Currency Format') }}</p>
            <div class="card-body card">
              <div class="mb-2">
                <label>{{ trans('Currency Icon') }}</label>
                <input type="text" v-model="form.icon" class="input" required="" />
              </div>
              <div class="mb-2">
                <label>{{ trans('Currency Icon') }}</label>
                <select class="select" v-model="form.position">
                  <option value="left">
                    {{ trans('Left') }}
                  </option>
                  <option value="right">
                    {{ trans('Right') }}
                  </option>
                </select>
              </div>
            </div>
            <div class="mb-2 mt-3">
              <SpinnerBtn
                classes="btn btn-primary"
                :processing="form.processing"
                :btn-text="trans('Update')"
              />
            </div>
          </form>
        </div>
      </div>
    </div>
  </main>
</template>
