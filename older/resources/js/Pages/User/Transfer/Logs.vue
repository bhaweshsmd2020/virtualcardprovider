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
const props = defineProps([
  'segments',
  'transferTransactions',
  'request',
  'totalTransferTransaction',
  'totalServiceFee',
  'todayTransfers',
  'transfer_details'
])

const transferOverviews = [
  {
    value: props.totalTransferTransaction,
    title: trans('Total'),
    iconClass: 'bx bx-badge-check'
  },
  {
    value: formatCurrency(props.totalServiceFee || 0),
    title: trans('Total Service Fee'),
    iconClass: 'bx bx-dollar'
  },
  {
    value: props.todayTransfers,
    title: trans("Today's Transfers"),
    iconClass: 'bx bx-calendar'
  }
]

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
    <PageHeader :title="trans('Transfer List')" :buttons="buttons" />

    <div class="space-y-4">
      <Overview :grid="3" :items="transferOverviews" />
      <FilterDropdown :options="filterForm" />
      <div class="table-responsive whitespace-nowrap rounded-primary">
        <table class="table">
          <thead>
            <tr>
              <th>{{ trans('ID') }}</th>
              <th>{{ trans('Transfer To') }}</th>
              <th>{{ trans('Request Amount') }}</th>
              <th>{{ trans('Converted Amount') }}</th>
              <th>{{ trans('Service Fee') }}</th>
              <th class="!text-right">{{ trans('Created At') }}</th>
            </tr>
          </thead>
          <tbody v-if="transferTransactions.total">
            <tr v-for="transfer in transferTransactions.data" :key="transfer.id">
              <td>
                {{ transfer.id }}
              </td>
              <td>
                <span v-if="transfer.transfer_to === 'balance'">Account</span>
                <span v-else>Wallet</span>
              </td>
              <td>
                {{ transfer.from_amount }}
                <span class="text-xs uppercase">{{ transfer.default_currency }}</span>
              </td>
              <td>
                {{ transfer.to_amount }}
                <span class="text-xs uppercase">{{ transfer.transfer_currency }}</span>
              </td>
              <td>{{ formatCurrency(+transfer.service_fee || 0) }}</td>
              <td class="!text-right">
                {{ moment(transfer.created_at).format('DD MMM, YYYY') }}
              </td>
            </tr>
          </tbody>
          <NoDataFound v-else for-table="true" />
        </table>
      </div>

      <Pagination :links="transferTransactions.links" />
    </div>
  </main>
</template>
