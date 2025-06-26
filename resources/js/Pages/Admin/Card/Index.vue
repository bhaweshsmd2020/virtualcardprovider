<script setup>
import AdminLayout from '@/Layouts/Admin/AdminLayout.vue'
import PageHeader from '@/Layouts/Admin/PageHeader.vue'
import { router, useForm } from '@inertiajs/vue3'
import Paginate from '@/Components/Admin/Paginate.vue'
import moment from 'moment'
import trans from '@/Composables/transComposable'
import sharedComposable from '@/Composables/sharedComposable'
import Overview from '@/Components/Admin/OverviewGrid.vue'
import NoDataFound from '@/Components/Admin/NoDataFound.vue'
import drawer from '@/Plugins/Admin/drawer'
import ImageInput from '@/Components/Admin/ImageInput.vue'
import { onMounted } from 'vue'
import toast from '@/Composables/toastComposable'
import SpinnerBtn from '@/Components/SpinnerBtn.vue'
import FilterDropdown from '@/Components/Admin/FilterDropdown.vue'

defineOptions({ layout: AdminLayout })
const { deleteRow, formatCurrency } = sharedComposable()
onMounted(() => {
  drawer.init()
})
const props = defineProps([
  'cards',
  'card_intro_details',
  'totalCard',
  'totalActiveCard',
  'totalInactiveCard'
])

const stats = [
  {
    value: props.totalCard,
    title: trans('Total Cards'),
    iconClass: 'bx bx-bar-chart'
  },
  {
    value: props.totalActiveCard,
    title: trans('Active Cards'),
    iconClass: 'bx bx-check-circle'
  },
  {
    value: props.totalInactiveCard,
    title: trans('Inactive Cards'),
    iconClass: 'bx bx-x-circle'
  }
]
const form = useForm({
  overview: props.card_intro_details?.overview,
  title: props.card_intro_details?.title,
  btn_text: props.card_intro_details?.btn_text,
  preview: props.card_intro_details?.preview,
  _method: 'put'
})
function updateOption() {
  form.post(route('admin.option.update', 'card_intro_details'), {
    onSuccess: () => {
      toast.success('Option Updated successfully')
      drawer.of('#cardIntroDrawer').hide()
    }
  })
}

const filterOptions = [
  {
    label: 'Title',
    value: 'title'
  },
  {
    label: 'Category Title',
    value: 'category_title'
  },
  {
    label: 'Status',
    value: 'status',
    options: [
      {
        label: 'Active',
        value: 'active'
      },
      {
        label: 'Inactive',
        value: 'inactive'
      }
    ]
  },
  {
    label: 'Featured',
    value: 'is_featured',
    options: [
      {
        label: 'Featured',
        value: 1
      },
      {
        label: 'Not Featured',
        value: 0
      }
    ]
  }
]
</script>

<template>
  <main class="container p-4 sm:p-6">
    <PageHeader title="Card List" :buttons="buttons" />
    <div class="space-y-6">
      <Overview :items="stats" grid="3" />

      <FilterDropdown :options="filterOptions" />

      <div class="table-responsive whitespace-nowrap rounded-primary">
        <table class="table">
          <thead>
            <tr>
              <th>{{ trans('Name') }}</th>
              <th>{{ trans('Min Deposit') }}</th>
              <th>{{ trans('Category') }}</th>
              <th>{{ trans('Total Orders') }}</th>
              <th>{{ trans('Total Prepaid Cards') }}</th>
              <th>{{ trans('Status') }}</th>
              <th>{{ trans('Featured') }}</th>
              <th>{{ trans('Created At') }}</th>
              <th class="flex justify-end">{{ trans('Action') }}</th>
            </tr>
          </thead>

          <tbody v-if="cards.total">
            <tr v-for="card in cards.data" :key="card.id">
              <td>
                {{ card.title }}
              </td>
              <td>
                {{ formatCurrency(card.min_deposit) }}
              </td>
              <td>
                {{ card.category?.title }}
              </td>
              <td>
                {{ card.card_orders_count || 0 }}
              </td>
              <td>
                {{ card.credit_cards_count || 0 }}
              </td>

              <td>
                <span
                  class="badge"
                  :class="card.status == 'active' ? 'badge-success' : 'badge-danger'"
                >
                  {{ card.status == 'active' ? trans('Active') : trans('Draft') }}
                </span>
              </td>
              <td>
                <span
                  class="badge"
                  :class="card.is_featured == 1 ? 'badge-success' : 'badge-danger'"
                >
                  {{ card.is_featured == 1 ? trans('Active') : trans('Inactive') }}
                </span>
              </td>
              <td>
                {{ moment(card.created_at).format('D-MMM-Y') }}
              </td>
              <td>
                <div class="flex justify-end">
                  <div class="dropdown" data-placement="bottom-start">
                    <div class="dropdown-toggle">
                      <Icon class="w-30 text-lg" icon="bi:three-dots-vertical" />
                    </div>
                    <div class="dropdown-content w-40">
                      <ul class="dropdown-list">
                        <li class="dropdown-list-item">
                          <Link :href="route('admin.cards.edit', card.id)" class="dropdown-link">
                            <Icon class="h-6" icon="bx:pencil" />
                            <span>{{ trans('Edit') }}</span>
                          </Link>
                        </li>
                        <li class="dropdown-list-item">
                          <button
                            type="button"
                            class="dropdown-link"
                            @click="deleteRow('/admin/cards/' + card.id)"
                          >
                            <Icon class="h-6" icon="material-symbols:delete-outline" />
                            <span>{{ trans('Delete') }}</span>
                          </button>
                        </li>
                      </ul>
                    </div>
                  </div>
                </div>
              </td>
            </tr>
          </tbody>
          <NoDataFound v-else for-table="true" />
        </table>
      </div>
    </div>
  </main>
  <div id="cardIntroDrawer" class="drawer drawer-right">
    <form method="POST" @submit.prevent="updateOption">
      <div class="drawer-header">
        <h5>{{ trans('Card Intro Details') }}</h5>
        <button
          type="button"
          class="btn btn-plain-secondary dark:text-slate-300 dark:hover:bg-slate-700 dark:focus:bg-slate-700"
          data-dismiss="drawer"
        >
          <i data-feather="x" width="1.5rem" height="1.5rem"></i>
        </button>
      </div>
      <div class="drawer-body">
        <ImageInput v-model="form.preview" :label="trans('Preview')" />
        <div class="my-2">
          <label class="label mb-1">{{ trans('Title') }}</label>
          <input type="text" v-model="form.title" class="input" required />
        </div>
        <div class="mb-2">
          <label class="label mb-1">{{ trans('Overview') }}</label>
          <textarea class="textarea" v-model="form.overview" required />
        </div>
        <div class="mb-2">
          <label class="label mb-1">{{ trans('Button Text') }}</label>
          <input type="text" v-model="form.btn_text" class="input" required />
        </div>
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
