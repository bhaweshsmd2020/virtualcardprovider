<script setup>
import AdminLayout from '@/Layouts/Admin/AdminLayout.vue'
import PageHeader from '@/Layouts/Admin/PageHeader.vue'
import { router } from '@inertiajs/vue3'
import RichEditor from '@/Components/Admin/RichEditor.vue'
import { ref, onMounted } from 'vue'
import toast from '@/Composables/toastComposable'
import SpinnerBtn from '@/Components/SpinnerBtn.vue'
defineOptions({ layout: AdminLayout })

const props = defineProps(['segments', 'buttons', 'payoutMethod', 'currencies'])

const form = ref({ ...props.payoutMethod })
const inputs = ref([])
onMounted(() => {
  inputs.value = JSON.parse(props.payoutMethod.data)

  for (let i = 0; i < props.currencies.length; i++) {
    if (!form.value.rates.hasOwnProperty(props.currencies[i])) {
      form.value.rates[props.currencies[i]] = ''
    }
  }
})

const newFields = () => {
  if (inputs.value.length >= 10) {
    toast.danger('10 Max, Limit Reached')
    return
  }
  const field = {
    type: '',
    label: ''
  }
  inputs.value.push(field)
}

const deleteFields = (targetIndex) => {
  if (inputs.value.length <= 1) {
    toast.danger('1 Min, Limit Reached')
    return
  }
  inputs.value = inputs.value.filter((_, index) => index !== targetIndex)
}

const updateMethod = () => {
  if (!(form.value.image instanceof File)) {
    form.value.image = null
  }
  form.value.data = inputs.value
  router.post(
    route('admin.payout-methods.update', props.payoutMethod.id),
    {
      _method: 'PATCH',
      payoutMethod: form.value
    },
    {
      onSuccess: () => {
        toast.success('Method Updated successfully')
      }
    }
  )
}
</script>

<template>
  <main class="container flex-grow p-4 sm:p-6">
    <PageHeader title="Edit Payout Method" :segments="segments" :buttons="buttons" />
    <div class="mx-auto max-w-3xl">
      <form class="space-y-3" @submit.prevent="updateMethod" enctype="multipart/form-data">
        <div class="card card-body">
          <div>
            <label class="label mb-1">{{ trans('Method Name') }}</label>
            <input
              type="text"
              class="input"
              placeholder="Method Name"
              v-model="form.name"
              required
              name="name"
            />
          </div>
          <div class="grid grid-cols-2 gap-3">
            <div>
              <label class="label mb-1" for="currency">{{ trans('Enter Currency Name') }}</label>
              <input
                type="text"
                class="input"
                name="currency"
                required
                v-model="form.currency_name"
              />
            </div>
            <div>
              <label class="label mb-1">{{ trans('Delay') }}</label>
              <input
                type="number"
                class="input"
                name="delay"
                v-model="form.delay"
                :placeholder="trans('Delay')"
              />
            </div>
          </div>
          <div class="grid grid-cols-2 gap-3">
            <div>
              <label class="label mb-1">{{ trans('Minimum Amount') }}</label>
              <input
                type="number"
                class="input"
                v-model="form.min_limit"
                placeholder="Minimum Amount"
                required
                name="min_limit"
              />
            </div>
            <div>
              <label class="label mb-1">{{ trans('Maximum Amount') }}</label>
              <input
                type="number"
                class="input"
                v-model="form.max_limit"
                placeholder="Maximum Amount"
                required
                name="max_limit"
              />
            </div>
          </div>
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

          <div v-if="form.charge_type == 'fixed'">
            <label class="label mb-1">{{ trans('Fixed Amount') }}</label>
            <input
              type="number"
              step="any"
              class="input"
              v-model="form.fixed_charge"
              name="fixed_charge"
              :placeholder="trans('Fixed Amount')"
            />
          </div>

          <!--- Transaction Charge percentage --->

          <div v-if="form.charge_type == 'percentage'">
            <label class="label mb-1">{{ trans('Percentage Amount') }}</label>
            <input
              type="number"
              step="any"
              class="input"
              v-model="form.percent_charge"
              name="percent_charge"
              :placeholder="trans('Percentage Amount')"
            />
          </div>
          <div>
            <label class="label mb-1">{{ trans('Currency Image') }}</label>
            <input
              type="file"
              class="input"
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
                  v-model="payoutMethod.rates[key]"
                />
              </div>
            </template>
          </div>
        </div>
        <div class="card card-body">
          <div class="space-y-3">
            <div class="grid grid-cols-12">
              <div class="col-span-5">
                <label class="label mb-1">{{ trans('Label') }}</label> <br />
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
                    <option :selected="input.type == 'text'" value="text">
                      {{ trans('Text') }}
                    </option>
                    <option :selected="input.type == 'number'" value="number">
                      {{ trans('Number') }}
                    </option>
                    <option :selected="input.type == 'textarea'" value="textarea">
                      {{ trans('Textarea') }}
                    </option>
                    <option :selected="input.type == 'email'" value="email">
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
            <select name="status" class="select" v-model="form.status">
              <option :selected="form.status == 1" value="1">
                {{ trans('Active') }}
              </option>
              <option :selected="form.status == 0" value="0">
                {{ trans('Inactive') }}
              </option>
            </select>
          </div>
          <SpinnerBtn classes="btn btn-primary mt-4" :btn-text="trans('Save')" />
        </div>
      </form>
    </div>
  </main>
</template>
