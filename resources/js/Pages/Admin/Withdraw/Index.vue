<script setup>
import AdminLayout from '@/Layouts/Admin/AdminLayout.vue'
import PageHeader from '@/Layouts/Admin/PageHeader.vue'
import trans from '@/Composables/transComposable'
import Overview from '@/Components/Admin/OverviewGrid.vue'
import Pagination from '@/Components/Admin/Paginate.vue'
import NoDataFound from '@/Components/Admin/NoDataFound.vue'
import FilterDropdown from '@/Components/Admin/FilterDropdown.vue'
import sharedComposable from '@/Composables/sharedComposable'
import moment from 'moment'

defineOptions({ layout: AdminLayout })

const { formatCurrency } = sharedComposable()
const props = defineProps(['segments', 'withdraws', 'request', 'type', 'withdrawAmounts'])

const filterForm = [
  {
    label: 'ID',
    value: 'id'
  },
  {
    label: 'User Email',
    value: 'user_email'
  },
  {
    label: 'Receiver Email',
    value: 'receiver_email'
  },
  {
    label: 'Amount',
    value: 'amount'
  }
]
const bgColors = [
  'bg-green-200/30',
  'bg-yellow-200/30',
  'bg-purple-200/30',
  'bg-blue-200/30',
  'bg-red-200/30',
  'bg-pink-200/30',
  'bg-orange-200/30',
  'bg-gray-200/30',
  'bg-teal-200/30'
]
</script>

<template>
  <!-- Main Content Starts -->
  <main class="container flex-grow p-4 sm:p-6">
    <PageHeader :title="trans('Withdraws')" />
    <section class="grid grid-cols-1 gap-6 sm:grid-cols-2 xl:grid-cols-4">
      <!-- Product Views  -->

      <template v-for="(item, index) in withdrawAmounts" :key="index">
        <Link :href="item.url">
          <div class="card">
            <div class="card-body flex items-center gap-4">
              <div
                class="flex h-12 w-12 flex-shrink-0 items-center justify-center rounded-full bg-opacity-20"
                :class="bgColors[[parseInt(index.toString()[0])]]"
              >
                <img v-lazy="item.currency.preview" alt="preview" />
              </div>
              <div class="flex flex-1 flex-col gap-1">
                <p class="text-sm uppercase tracking-wide text-slate-500">
                  {{ item.currency.currency }}
                </p>
                <div class="flex flex-wrap items-baseline justify-between gap-2">
                  <h4>{{ item.total_amount }}</h4>
                </div>
              </div>
            </div>
          </div>
        </Link>
      </template>
    </section>
    <div class="space-y-4">
      <Overview :grid="3" :items="orderOverviews" />
      <FilterDropdown :options="filterForm" />
      <div class="table-responsive whitespace-nowrap rounded-primary">
        <table class="table">
          <thead>
            <tr>
              <th>{{ trans('ID') }}</th>
              <th>{{ trans('User Name') }}</th>
              <th>{{ trans('Receiver Email') }}</th>
              <th>{{ trans('Amount') }}</th>

              <th>{{ trans('Service Fee') }}</th>
              <th class="!text-right">{{ trans('Created At') }}</th>
            </tr>
          </thead>
          <tbody v-if="withdraws.total">
            <tr v-for="withdraw in withdraws.data" :key="withdraw.id">
              <td>
                {{ withdraw.id }}
              </td>
              <td>
                <Link
                  class="text-primary-500 hover:underline"
                  :href="route('admin.users.show', withdraw?.user_id)"
                >
                  {{ withdraw?.user?.name }}
                </Link>
              </td>
              <td>
                <Link
                  class="hover:underline"
                  :href="route('admin.users.show', withdraw?.receiver.id)"
                >
                  {{ withdraw?.receiver?.email }}
                </Link>
              </td>
              <td>
                {{ withdraw.amount }}
                <span class="text-xs uppercase">{{ withdraw.currency }}</span>
              </td>

              <td>{{ formatCurrency(+withdraw.service_fee || 0) }}</td>

              <td class="!text-right">
                {{ moment(withdraw.created_at).format('DD MMM, YYYY') }}
              </td>
            </tr>
          </tbody>
          <NoDataFound v-else for-table="true" />
        </table>
      </div>

      <Pagination :links="withdraws.links" />
    </div>
  </main>
</template>
