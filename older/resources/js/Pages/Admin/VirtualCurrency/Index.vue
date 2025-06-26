<script setup>
import AdminLayout from '@/Layouts/Admin/AdminLayout.vue'
import PageHeader from '@/Layouts/Admin/PageHeader.vue'
import sharedComposable from '@/Composables/sharedComposable'
import Paginate from '@/Components/Admin/Paginate.vue'
import moment from 'moment'
import trans from '@/Composables/transComposable'
import Overview from '@/Components/Admin/OverviewGrid.vue'
import NoDataFound from '@/Components/Admin/NoDataFound.vue'

defineOptions({ layout: AdminLayout })
const { textExcerpt, deleteRow } = sharedComposable()
const props = defineProps(['virtualCurrencies', 'total', 'totalActive', 'totalInactive', 'request'])

const stats = [
  {
    value: props.total,
    title: trans('Total'),
    iconClass: 'bx bx-bar-chart'
  },
  {
    value: props.totalActive,
    title: trans('Total Active'),
    iconClass: 'bx bx-check-circle'
  },
  {
    value: props.totalInactive,
    title: trans('Total Inactive'),
    iconClass: 'bx bx-x-circle'
  }
]
</script>

<template>
  <main class="container flex-grow p-4 sm:p-6">
    <PageHeader title="Virtual Currency" />
    <div class="space-y-6">
      <Overview :items="stats" grid="3" />

      <!-- Customer Table Starts -->
      <div class="table-responsive whitespace-nowrap rounded-primary">
        <table class="table">
          <thead>
            <tr>
              <th class="col-3">{{ trans('Currency') }}</th>
              <th class="col-1">{{ trans('Status') }}</th>
              <th class="col-2">{{ trans('Created At') }}</th>
              <th class="col-1">
                <div class="text-right">{{ trans('Action') }}</div>
              </th>
            </tr>
          </thead>
          <tbody v-if="virtualCurrencies.data != 0">
            <tr v-for="virtualCurrency in virtualCurrencies.data" :key="virtualCurrency.id">
              <td class="flex items-center">
                <img
                  v-if="virtualCurrency.preview"
                  v-lazy="virtualCurrency.preview"
                  class="mr-2 h-8 w-8 opacity-60"
                />

                <span class="uppercase">{{ textExcerpt(virtualCurrency.currency, 80) }}</span>
              </td>

              <td class="text-left">
                <span
                  class="badge"
                  :class="virtualCurrency.status == 'active' ? 'badge-success' : 'badge-danger'"
                >
                  {{ virtualCurrency.status == 'active' ? trans('Active') : trans('Draft') }}
                </span>
              </td>
              <td>
                {{ moment(virtualCurrency.created_at).format('D-MMM-Y') }}
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
                          <Link
                            :href="route('admin.virtual-currency.edit', virtualCurrency.id)"
                            class="dropdown-link"
                          >
                            <Icon class="h-6 text-slate-400" icon="material-symbols:edit-outline" />
                            <span>{{ trans('Edit') }}</span>
                          </Link>
                        </li>

                        <li class="dropdown-list-item">
                          <button
                            class="dropdown-link"
                            @click="
                              deleteRow(
                                route('admin.virtual-currency.destroy', virtualCurrency.id),
                                trans('Exchange Rate has been deleted successfully')
                              )
                            "
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

        <Paginate :links="virtualCurrencies.links" />
      </div>
    </div>
  </main>
</template>
