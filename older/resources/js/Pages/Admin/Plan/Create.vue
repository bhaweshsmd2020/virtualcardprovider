<script setup>
import AdminLayout from '@/Layouts/Admin/AdminLayout.vue'
import SpinnerBtn from '@/Components/SpinnerBtn.vue'
import PageHeader from '@/Layouts/Admin/PageHeader.vue'
import { useForm } from '@inertiajs/vue3'
import toast from '@/Composables/toastComposable'
defineOptions({ layout: AdminLayout })
const props = defineProps(['segments', 'buttons'])

const form = useForm({
  title: null,
  description: null,
  preview: '',
  days: 30,
  price: null,
  plan_data: {
    cards: { value: 0, overview: '' },
    deposit_fee: { value: 0, overview: '' },
    transaction_fee: { value: 0, overview: '' },
    service_fee: { value: 0, overview: '' }
  },
  extra_data: [],
  is_featured: false,
  is_recommended: false,
  is_trial: false,
  is_default: false,
  status: true,
  trial_days: 0
})
const defaultExtraData = {
  key: '',
  value: ''
}

const addItem = () => {
  form.extra_data.push({ ...defaultExtraData })
}

const removeItem = (index) => {
  form.extra_data.splice(index, 1)
}
function update() {
  if (form.extra_data.length < 2 && !form.extra_data[0]?.key && !form.extra_data[0]?.value) {
    form.extra_data = null
  }
  form.post(route('admin.plan.store'), {
    onSuccess: () => {
      toast.success('Plan created successfully')
    }
  })
}
</script>

<template>
  <main class="container flex-grow p-4 sm:p-6">
    <PageHeader :title="trans('Create Plan')" :buttons="buttons" />
    <div class="space-y-6">
      <div>
        <strong>{{ trans('Create Plan') }}</strong>
        <p>{{ trans('Create subscription plan for charging from the customer') }}</p>
      </div>

      <form class="flex flex-col-reverse items-start gap-8 lg:flex-row" @submit.prevent="update()">
        <div class="card card-body flex-1">
          <strong>{{ trans('Plan Details') }}</strong>
          <div class="my-2">
            <label>{{ trans('Plan Name') }}</label>
            <input type="text" name="title" required="" class="input" v-model="form.title" />
          </div>
          <div class="my-2">
            <label>{{ trans('Plan Description') }}</label>
            <textarea v-model="form.description" required class="textarea" />
          </div>
          <div class="my-2">
            <label class="label mb-2">{{ trans('Icon') }}</label>
            <input
              type="file"
              @change="(e) => (form.preview = e.target.files[0])"
              class="input"
            />
            <InputFieldError :message="form.errors.preview" />
          </div>
          <div class="my-2">
            <label>{{ trans('Select Duration') }}</label>
            <select class="select" name="days" v-model="form.days">
              <option value="30">{{ trans('Monthly') }}</option>
              <option value="365">{{ trans('yearly') }}</option>
              <option value="999999">{{ trans('Lifetime') }}</option>
            </select>
          </div>
          <div class="my-2">
            <label>{{ trans('Price') }}</label>
            <input
              type="number"
              name="price"
              v-model="form.price"
              step="any"
              required=""
              class="input"
            />
          </div>

          <div class="mb-2">
            <label for="toggle-featured" class="toggle toggle-sm">
              <input
                v-model="form.is_featured"
                class="toggle-input peer sr-only"
                id="toggle-featured"
                type="checkbox"
              />
              <div class="toggle-body"></div>
              <span class="label label-md">{{ trans('Is Featured?') }}</span>
            </label>
          </div>
          <div class="mb-2">
            <label for="toggle-is_recommended" class="toggle toggle-sm">
              <input
                v-model="form.is_recommended"
                class="toggle-input peer sr-only"
                id="toggle-is_recommended"
                type="checkbox"
              />
              <div class="toggle-body"></div>
              <span class="label label-md">{{ trans('Is recommended?') }}</span>
            </label>
          </div>

          <div class="mb-2">
            <label for="toggle-is_default" class="toggle toggle-sm">
              <input
                v-model="form.is_default"
                class="toggle-input peer sr-only"
                id="toggle-is_default"
                type="checkbox"
              />
              <div class="toggle-body"></div>
              <span class="label label-md"
                >{{ trans('Default Plan') }} ({{ trans('For free registration') }})</span
              >
            </label>
          </div>
          <div class="mb-2">
            <label for="toggle-is_trial" class="toggle toggle-sm">
              <input
                v-model="form.is_trial"
                class="toggle-input peer sr-only"
                id="toggle-is_trial"
                type="checkbox"
              />
              <div class="toggle-body"></div>
              <span class="label label-md">{{ trans('Accept Trial?') }}</span>
            </label>
          </div>
          <template v-if="form.is_trial">
            <div class="from-group trial-days mb-2 mt-2">
              <label class="col-lg-12">{{ trans('Trial days') }}</label>
              <div class="col-lg-12">
                <input
                  type="number"
                  v-model="form.trial_days"
                  name="trial_days"
                  class="input"
                  required
                />
              </div>
            </div>
          </template>

          <div class="mb-2">
            <label for="toggle-status" class="toggle toggle-sm">
              <input
                v-model="form.status"
                class="toggle-input peer sr-only"
                id="toggle-status"
                type="checkbox"
              />
              <div class="toggle-body"></div>
              <span class="label label-md">{{ trans('Activate This Plan?') }}</span>
            </label>
          </div>

          <div class="mt-6">
            <SpinnerBtn classes="btn btn-primary" :processing="form.processing" btn-text="Create" />
          </div>
        </div>

        <div class="card card-body flex-1 space-y-2">
          <strong>{{ trans('Plan Perks') }}</strong>
          <div class="my-2">
            <label class="label mb-1">{{ trans('Cards') }}</label>
            <input type="number" v-model="form.plan_data.cards.value" required class="input" />
            <label class="label mb-1">{{ trans('Overview') }}</label>
            <textarea v-model="form.plan_data.cards.overview" required class="input" />
          </div>
          <div class="my-2">
            <label class="label mb-1">{{ trans('Deposit Fee %') }}</label>
            <input
              type="number"
              v-model="form.plan_data.deposit_fee.value"
              required
              class="input"
            />
            <label class="label mb-1">{{ trans('Overview') }}</label>
            <textarea v-model="form.plan_data.deposit_fee.overview" required class="input" />
          </div>
          <div class="my-2">
            <label class="label mb-1">{{ trans('Transaction Fee %') }}</label>
            <input
              type="number"
              v-model="form.plan_data.transaction_fee.value"
              required
              class="input"
            />
            <label class="label mb-1">{{ trans('Overview') }}</label>
            <textarea v-model="form.plan_data.transaction_fee.overview" required class="input" />
          </div>
          <div class="my-2">
            <label class="label mb-1">{{ trans('Service Fee %') }}</label>
            <input
              type="number"
              v-model="form.plan_data.service_fee.value"
              required
              class="input"
            />
            <label class="label mb-1">{{ trans('Overview') }}</label>
            <textarea v-model="form.plan_data.service_fee.overview" required class="input" />
          </div>
          <div class="mb-2">
            <label class="label mb-2 block font-semibold">{{ trans('Add Extra Perks') }}</label>
            <div
              class="mb-2 flex items-center gap-x-2"
              v-for="(item, index) in form.extra_data"
              :key="index"
            >
              <input type="text" class="input" placeholder="Key" v-model="item.key" />
              <input type="text" class="input" placeholder="Value" v-model="item.value" />

              <button type="button" @click="removeItem(index)" class="btn btn-danger">
                <Icon icon="bx:x" class="text-lg" />
              </button>
            </div>
            <button type="button" @click="addItem" class="btn btn-primary">
              <Icon icon="bx:plus" class="text-lg" />
            </button>
          </div>
        </div>
      </form>
    </div>
  </main>
</template>
