<script setup>
import AdminLayout from '@/Layouts/Admin/AdminLayout.vue'
import NoDataFound from '@/Components/Admin/NoDataFound.vue'
import PageHeader from '@/Layouts/Admin/PageHeader.vue'
import sharedComposable from '@/Composables/sharedComposable'
import drawer from '@/Plugins/Admin/drawer'
import { onMounted } from 'vue'
import SpinnerBtn from '@/Components/SpinnerBtn.vue'
import { useForm } from '@inertiajs/vue3'
import toast from '@/Composables/toastComposable'
import ImageInput from '@/Components/Admin/ImageInput.vue'
defineOptions({ layout: AdminLayout })
onMounted(() => {
  drawer.init()
})
const props = defineProps(['segments', 'buttons', 'plans', 'planSetting'])
const { deleteRow } = sharedComposable()

const form = useForm({
  overview: props.planSetting?.overview,
  percentage: props.planSetting?.percentage,
  plan_data: {
    cards: props.planSetting?.plan_data?.cards,
    deposit_fee: props.planSetting?.plan_data?.deposit_fee
  },
  banner: props.planSetting?.banner,
  _method: 'put'
})
function updateOption() {
  form.post(route('admin.option.update', 'plan'), {
    onSuccess: () => {
      toast.success('Option Updated successfully')
      drawer.of('#planSettingDrawer').hide()
    }
  })
}
</script>

<template>
  <main class="container flex-grow p-4 sm:p-6">
    <PageHeader :title="trans('Subscriptions')" :buttons="buttons" />

    <div class="space-y-6">
      <div v-if="plans.length != 0" class="grid grid-cols-1 gap-5 sm:grid-cols-2 xl:grid-cols-4">
        <div class="card" v-for="plan in plans" :key="plan.id">
          <div class="card-body flex h-full flex-col justify-between">
            <div>
              <div class="text-center">
                <h5>{{ plan.title }}</h5>
                <h4>{{ plan.price_format }}</h4>
                {{
                  plan.days === 30
                    ? trans('Per month')
                    : plan.days === 365
                    ? trans('Per year')
                    : trans('Lifetime')
                }}
                <p class="text-muted">{{ trans('Active Users') }} ({{ plan.activeuser_count }})</p>
              </div>
              <hr class="mt-3 opacity-60" />

              <template v-for="(planData, key) in plan.data" :key="key">
                <div class="mt-3 text-left" v-if="key !== 'stock_content'">
                  <i v-if="planData.value" class="far fa-check-circle text-green-600"></i>

                  <i class="far fa-times-circle text-red-600" v-else></i>
                  {{ key.replace(/_/g, ' ') }}
                  {{
                    planData.value == '-1'
                      ? '(Unlimited)'
                      : planData.value == 'true' || planData.value == 'false'
                      ? ''
                      : `(${planData.value})`
                  }}
                </div>
              </template>
            </div>

            <div class="mt-4">
              <hr class="opacity-60" />

              <div class="mt-4 flex justify-center gap-2">
                <Link :href="route('admin.plan.edit', plan.id)" class="btn btn-primary px-5">
                  <Icon icon="fe:pencil" /> {{ trans('Edit') }}
                </Link>

                <button
                  @click="
                    plan.activeuser_count == 0 ? deleteRow('/admin/plan/' + plan.id) : toastError()
                  "
                  class="btn btn-sm btn-danger delete-confirm px-5 text-left"
                  data-icon="bx bx-plus-circle"
                >
                  <Icon icon="fe:trash" />
                  {{ trans('Delete') }}
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>

      <NoDataFound v-else />
    </div>
  </main>
  <div id="planSettingDrawer" class="drawer drawer-right">
    <form method="POST" @submit.prevent="updateOption">
      <div class="drawer-header">
        <h5>{{ trans('Plan Description') }}</h5>
        <button
          type="button"
          class="btn btn-plain-secondary dark:text-slate-300 dark:hover:bg-slate-700 dark:focus:bg-slate-700"
          data-dismiss="drawer"
        >
          <i data-feather="x" width="1.5rem" height="1.5rem"></i>
        </button>
      </div>
      <div class="drawer-body">
        <div class="mb-2">
          <label>{{ trans('Overview') }}</label>
          <input type="text" v-model="form.overview" class="input" required />
        </div>
        <div class="mb-2">
          <label>{{ trans('Save Percentage') }}</label>
          <input type="number" v-model="form.percentage" class="input" required />
        </div>
        <div class="mb-2">
          <label>{{ trans('Cards') }}</label>
          <input type="text" v-model="form.plan_data.cards" class="input" />
        </div>
        <div class="mb-2">
          <label>{{ trans('Deposit Fee') }}</label>
          <input type="text" v-model="form.plan_data.deposit_fee" class="input" />
        </div>
        <ImageInput v-model="form.banner" :label="trans('Banner')" />
      </div>
      <div class="drawer-footer">
        <div class="flex gap-2">
          <button type="button" class="btn btn-secondary flex-1" data-dismiss="drawer">
            <span> {{ trans('Close') }}</span>
          </button>
          <SpinnerBtn
            classes="btn btn-primary flex-1"
            :processing="form.processing"
            :btn-text="trans('Save Changes')"
          />
        </div>
      </div>
    </form>
    <div class="drawer-backdrop"></div>
  </div>
</template>
