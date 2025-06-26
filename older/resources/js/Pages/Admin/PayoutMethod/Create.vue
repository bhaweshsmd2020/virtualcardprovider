<script setup>
import AdminLayout from '@/Layouts/Admin/AdminLayout.vue'
import PageHeader from '@/Layouts/Admin/PageHeader.vue'
import { useForm } from '@inertiajs/vue3'
import SpinnerBtn from '@/Components/SpinnerBtn.vue'
import RichEditor from '@/Components/Admin/RichEditor.vue'
import toast from '@/Composables/toastComposable'
import { ref, onMounted } from 'vue'

defineOptions({ layout: AdminLayout })
const props = defineProps(['segments', 'buttons', 'currencies'])

const inputs = ref([
  {
    type: '',
    label: ''
  }
])
const fields = ref([])
const form = useForm({
  name: '',
  currency_name: '',
  delay: '',
  min_limit: '',
  max_limit: '',
  fixed_charge: '',
  charge_type: '',
  percent_charge: '',
  image: '',
  instruction: '',
  status: 1,
  inputs: [],
  rates: {}
})
onMounted(() => {
  for (let i = 0; i < props.currencies.length; i++) {
    if (!form.rates.hasOwnProperty(props.currencies[i])) {
      form.rates[props.currencies[i]] = ''
    }
  }
})
const newFields = () => {
  if (inputs.value.length >= 10) {
    toast.danger('10 Max, Limit Reached')
    return
  }
  const fields = {
    type: '',
    label: ''
  }
  inputs.value.push(fields)
}

const deleteFields = (targetIndex) => {
  if (inputs.value.length <= 1) {
    toast.danger('1 Min, Limit Reached')
    return
  }
  inputs.value = inputs.value.filter((_, index) => index !== targetIndex)
}
const createMethod = () => {
  form.inputs = inputs.value
  form.post(route('admin.payout-methods.store'), {
    onSuccess: () => {
      toast.success('Method Created successfully')
    }
  })
}
</script>

<template>
  <main class="container flex-grow p-4 sm:p-6">
    <PageHeader title="New Payout Method" :segments="segments" :buttons="buttons" />

    <form
      class="mx-auto max-w-3xl space-y-4"
      method="post"
      @submit.prevent="createMethod"
      enctype="multipart/form-data"
    >
      <div class="card card-body space-y-3">
        <div>
          <label class="label mb-1">{{ trans('Method Name') }}</label>
          <input
            type="text"
            class="input"
            :placeholder="trans('Method Name')"
            required
            name="name"
            v-model="form.name"
          />
        </div>
        <div class="grid grid-cols-2 gap-3">
          <div>
            <label class="label mb-1" for="currency_id">{{ trans('Enter Currency Name') }}</label>
            <input
              type="text"
              class="input"
              name="currency"
              required
              v-model="form.currency_name"
            />
          </div>

          <div>
            <label class="label mb-1">{{ trans('Delay (Processing Days)') }}</label>
            <input
              type="number"
              class="input"
              name="delay"
              :placeholder="trans('Delay')"
              v-model="form.delay"
            />
          </div>
        </div>
        <div class="grid grid-cols-2 gap-3">
          <div>
            <label class="label mb-1">{{ trans('Minimum Amount') }}</label>
            <input
              v-model="form.min_limit"
              type="number"
              class="input"
              :placeholder="trans('Minimum Amount')"
              required
              name="min_limit"
            />
          </div>

          <div>
            <label class="label mb-1">{{ trans('Maximum Amount') }}</label>
            <input
              v-model="form.max_limit"
              type="number"
              class="input"
              :placeholder="trans('Maximum Amount')"
              required
              name="max_limit"
            />
          </div>
        </div>
        <!--- Transaction Charge Fixed --->

        <div>
          <label class="label mb-1">{{ trans('Charge Type') }}</label>
          <select class="select" v-model="form.charge_type">
            <option value="" disabled>
              {{ trans('Select charge type') }}
            </option>
            <option value="fixed">{{ trans('Fixed') }}</option>
            <option value="percentage">{{ trans('Percentage') }}</option>
          </select>
        </div>
        <div v-if="form.charge_type === 'fixed'">
          <label class="label mb-1">{{ trans('Enter Amount For Fixed Charge') }}</label>
          <input
            v-model="form.fixed_charge"
            type="number"
            class="input"
            name="fixed_charge"
            placeholder="Fixed Amount"
            step="any"
          />
        </div>
        <div v-if="form.charge_type === 'percentage'">
          <label class="label mb-1">{{ trans('Enter Percentage Amount') }}</label>
          <input
            step="any"
            v-model="form.percent_charge"
            type="number"
            class="input"
            name="percent_charge"
          />
        </div>

        <div>
          <label class="label mb-1">{{ trans('Currency Image') }}</label>
          <input
            type="file"
            class="input"
            required
            name="image"
            @input="(e) => (form.image = e.target.files[0])"
          />
        </div>

        <div>
          <label class="label mb-1">{{ trans('Instruction') }}</label>
          <RichEditor v-model="form.instruction" />
        </div>
      </div>
      <!-- rates -->
      <div class="card card-body">
        <p class="col-span-full mb-2 text-lg font-semibold">Exchange Rate</p>
        <div class="grid grid-cols-2 gap-3">
          <template v-for="(rate, key) in form.rates" :key="key">
            <div>
              <label class="label mb-1 uppercase">{{ key }}</label>
              <input
                type="number"
                step="any"
                placeholder="Rate"
                class="input"
                v-model="form.rates[key]"
              />
            </div>
          </template>
        </div>
      </div>
      <!-- dynamic fields -->
      <div class="card card-body space-y-3">
        <div class="space-y-3">
          <div class="grid grid-cols-12">
            <div class="col-span-5">
              <label class="label mb-1">{{ trans('Label') }}</label>
            </div>
            <div class="col-span-6">
              <label class="label mb-1">{{ trans('Input Type') }}</label>
            </div>
            <button type="button" @click="newFields" class="btn btn-primary py-2">
              <Icon icon="bx:plus" class="text-lg" />
            </button>
          </div>
          <template v-for="(input, i) in inputs" :key="i">
            <div class="grid grid-cols-12 gap-3">
              <div class="col-span-5">
                <input
                  type="text"
                  data-key="0"
                  class="input"
                  placeholder="Label here"
                  v-model="input.label"
                />
              </div>
              <div class="col-span-6">
                <select v-model="input.type" class="select">
                  <option value="text">
                    {{ trans('Text') }}
                  </option>
                  <option value="number">
                    {{ trans('Number') }}
                  </option>
                  <option value="textarea">
                    {{ trans('Textarea') }}
                  </option>
                  <option value="email">
                    {{ trans('Email') }}
                  </option>
                </select>
              </div>
              <div class="col-span-1">
                <button
                  type="button"
                  @click="deleteFields(i)"
                  class="btn btn-danger py-3"
                  title="Remove"
                >
                  <svg
                    xmlns="http://www.w3.org/2000/svg"
                    width="16"
                    height="16"
                    fill="currentColor"
                    class="bi bi-x-circle"
                    viewBox="0 0 16 16"
                  >
                    <path
                      d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14zm0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16z"
                    />
                    <path
                      d="M4.646 4.646a.5.5 0 0 1 .708 0L8 7.293l2.646-2.647a.5.5 0 0 1 .708.708L8.707 8l2.647 2.646a.5.5 0 0 1-.708.708L8 8.707l-2.646 2.647a.5.5 0 0 1-.708-.708L7.293 8 4.646 5.354a.5.5 0 0 1 0-.708z"
                    />
                  </svg>
                </button>
              </div>
            </div>
          </template>
        </div>
        <div>
          <label class="label mb-1">{{ trans('Status') }}</label>
          <select v-model="form.status" name="status" class="select">
            <option value="1">
              {{ trans('Active') }}
            </option>
            <option value="0">
              {{ trans('Inactive') }}
            </option>
          </select>
        </div>
        <SpinnerBtn
          classes="btn btn-primary"
          :processing="form.processing"
          :btn-text="trans('Save')"
        />
      </div>
    </form>
  </main>
</template>
