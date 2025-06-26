<script setup>
import UserLayout from '@/Layouts/User/UserLayout.vue'
import PageHeader from '@/Layouts/Admin/PageHeader.vue'
import trans from '@/Composables/transComposable'
import Overview from '@/Components/Admin/OverviewGrid.vue'
import Pagination from '@/Components/Admin/Paginate.vue'
import NoDataFound from '@/Components/Admin/NoDataFound.vue'
import FilterDropdown from '@/Components/Admin/FilterDropdown.vue'
import sharedComposable from '@/Composables/sharedComposable'
import moment from 'moment'

defineOptions({ layout: UserLayout })

const { formatCurrency } = sharedComposable()
const props = defineProps(['segments', 'exchangeTransactions', 'request', 'exchange_details'])

const filterForm = [
  {
    value: 'id',
    label: 'ID'
  },
  {
    value: 'from_amount',
    label: 'Request Amount'
  },
  {
    value: 'to_amount',
    label: 'Converted Amount'
  }
]
</script>

<template>
  <!-- Main Content Starts -->
  <main class="container flex-grow p-4 sm:p-6">
    <PageHeader :title="trans('Exchange List')" />

    <div class="space-y-4">
      <Overview :grid="3" :items="orderOverviews" />
      <FilterDropdown :options="filterForm" />
      <div class="table-responsive whitespace-nowrap rounded-primary">
        <table class="table">
          <thead>
            <tr>
              <th>{{ trans('ID') }}</th>

              <th>{{ trans('Request Amount') }}</th>
              <th>{{ trans('Converted Amount') }}</th>

              <th>{{ trans('Service Fee') }}</th>
              <th class="!text-right">{{ trans('Created At') }}</th>
            </tr>
          </thead>
          <tbody v-if="exchangeTransactions.total">
            <tr v-for="exchange in exchangeTransactions.data" :key="exchange.id">
              <td>
                {{ exchange.id }}
              </td>

              <td>
                {{ +exchange.from_amount }}
                <span class="text-xs uppercase">{{ exchange.default_currency }}</span>
              </td>
              <td>
                {{ exchange.to_amount }}
                <span class="text-xs uppercase">{{ exchange.transfer_currency }}</span>
              </td>

              <td>{{ formatCurrency(+exchange.service_fee || 0) }}</td>

              <td class="!text-right">
                {{ moment(exchange.created_at).format('DD MMM, YYYY') }}
              </td>
            </tr>
          </tbody>
          <NoDataFound v-else for-table="true" />
        </table>
      </div>

      <Pagination :links="exchangeTransactions.links" />
    </div>
  </main>
</template>
