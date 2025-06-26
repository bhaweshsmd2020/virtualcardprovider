<script setup>
import AdminLayout from '@/Layouts/Admin/AdminLayout.vue'
import SpinnerBtn from '@/Components/SpinnerBtn.vue'
import PageHeader from '@/Layouts/Admin/PageHeader.vue'
import { ref } from 'vue'
import { router } from '@inertiajs/vue3'
import toast from '@/Composables/toastComposable'

defineOptions({ layout: AdminLayout })

const props = defineProps(['segments', 'buttons', 'plan'])

const form = ref({
  title: props.plan.title,
  description: props.plan.description,
  preview: null,
  days: props.plan.days,
  price: props.plan.price,
  plan_data: props.plan.data,
  extra_data: props.plan.extra_data ?? [],
  is_featured: props.plan.is_featured == 1,
  is_default: props.plan.is_default == 1,
  is_recommended: props.plan.is_recommended == 1,
  is_trial: props.plan.is_trial == 1,
  status: props.plan.status == 1,
  trial_days: props.plan.trial_days
})

const isProcessing = ref(false)

const defaultExtraData = { key: '', value: '' }

const addItem = () => {
  form.value.extra_data.push({ ...defaultExtraData })
}

const removeItem = (index) => {
  form.value.extra_data.splice(index, 1)
}

function onFileChange(e) {
  const file = e.target.files[0]
  if (file) {
    form.value.preview = file
  }
}

function update() {
  isProcessing.value = true

  if (!(form.value.preview instanceof File)) {
    form.value.preview = null
  }

  router.post(
    route('admin.plan.update', props.plan.id),
    {
      _method: 'PUT',
      ...form.value
    },
    {
      onFinish: () => {
        isProcessing.value = false
        toast.success('Plan updated successfully')
      }
    }
  )
}
</script>

<template>
  <main class="container flex-grow p-4 sm:p-6">
    <PageHeader :title="trans('Edit Plan')" :buttons="buttons" />
    <div class="space-y-6">
      <form class="flex flex-col-reverse items-start gap-8 lg:flex-row" @submit.prevent="update" enctype="multipart/form-data">
        <div class="card card-body flex-1">
          <strong>{{ trans('Plan Details') }}</strong>
          <div class="my-2">
            <label>{{ trans('Plan Name') }}</label>
            <input type="text" required class="input" v-model="form.title" />
          </div>
          <div class="my-2">
            <label>{{ trans('Plan Description') }}</label>
            <textarea v-model="form.description" required class="textarea" />
          </div>
          <div class="my-2">
            <label>{{ trans('Icon') }}</label>
            <input type="file" @change="onFileChange" accept="image/*" class="input" />
          </div>
          <div class="my-2">
            <label>{{ trans('Select Duration') }}</label>
            <select class="select" v-model="form.days">
              <option value="30">{{ trans('Monthly') }}</option>
              <option value="365">{{ trans('Yearly') }}</option>
              <option value="999999">{{ trans('Lifetime') }}</option>
            </select>
          </div>
          <div class="my-2">
            <label>{{ trans('Price') }}</label>
            <input type="number" v-model="form.price" step="any" required class="input" />
          </div>

          <div class="mb-2">
            <label for="toggle-featured" class="toggle toggle-sm">
              <input v-model="form.is_featured" class="toggle-input peer sr-only" id="toggle-featured" type="checkbox" />
              <div class="toggle-body"></div>
              <span class="label label-md">{{ trans('Is Featured?') }}</span>
            </label>
          </div>
          <div class="mb-2">
            <label for="toggle-is_recommended" class="toggle toggle-sm">
              <input v-model="form.is_recommended" class="toggle-input peer sr-only" id="toggle-is_recommended" type="checkbox" />
              <div class="toggle-body"></div>
              <span class="label label-md">{{ trans('Is Recommended?') }}</span>
            </label>
          </div>
          <div class="mb-2">
            <label for="toggle-is_default" class="toggle toggle-sm">
              <input v-model="form.is_default" class="toggle-input peer sr-only" id="toggle-is_default" type="checkbox" />
              <div class="toggle-body"></div>
              <span class="label label-md">{{ trans('Default Plan') }} ({{ trans('For free registration') }})</span>
            </label>
          </div>
          <div class="mb-2">
            <label for="toggle-is_trial" class="toggle toggle-sm">
              <input v-model="form.is_trial" class="toggle-input peer sr-only" id="toggle-is_trial" type="checkbox" />
              <div class="toggle-body"></div>
              <span class="label label-md">{{ trans('Accept Trial?') }}</span>
            </label>
          </div>

          <template v-if="form.is_trial">
            <div class="my-2">
              <label>{{ trans('Trial Days') }}</label>
              <input type="number" v-model="form.trial_days" class="input" required />
            </div>
          </template>

          <div class="mb-2">
            <label for="toggle-status" class="toggle toggle-sm">
              <input v-model="form.status" class="toggle-input peer sr-only" id="toggle-status" type="checkbox" />
              <div class="toggle-body"></div>
              <span class="label label-md">{{ trans('Activate This Plan?') }}</span>
            </label>
          </div>

          <div class="mt-6">
            <SpinnerBtn classes="btn btn-primary" :processing="isProcessing" btn-text="Update" />
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
            <input type="number" v-model="form.plan_data.deposit_fee.value" required class="input" />
            <label class="label mb-1">{{ trans('Overview') }}</label>
            <textarea v-model="form.plan_data.deposit_fee.overview" required class="input" />
          </div>
          <div class="my-2">
            <label class="label mb-1">{{ trans('Transaction Fee %') }}</label>
            <input type="number" v-model="form.plan_data.transaction_fee.value" required class="input" />
            <label class="label mb-1">{{ trans('Overview') }}</label>
            <textarea v-model="form.plan_data.transaction_fee.overview" required class="input" />
          </div>
          <div class="my-2">
            <label class="label mb-1">{{ trans('Service Fee %') }}</label>
            <input type="number" v-model="form.plan_data.service_fee.value" required class="input" />
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
