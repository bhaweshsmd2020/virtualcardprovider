<script setup>
import UserLayout from '@/Layouts/User/UserLayout.vue'
import PageHeader from '@/Layouts/Admin/PageHeader.vue'
import trans from '@/Composables/transComposable'
import StripePagination from '@/Components/StripePagination.vue'
import NoDataFound from '@/Components/Admin/NoDataFound.vue'
import sharedComposable from '@/Composables/sharedComposable'
import moment from 'moment'

defineOptions({ layout: UserLayout })

const { formatAmount, badgeClass } = sharedComposable()
const props = defineProps([
  'authorizations',
  'request',
  'has_more',
  'next',
  'prev',
  'stripe_currency'
])
</script>

<template>
  <!-- Main Content Starts -->
  <main class="container flex-grow p-4 sm:p-6">
    <PageHeader :title="trans('Authorizations')" />
    <div class="space-y-4">
      <p class="text-lg font-semibold">{{ trans('Authorization History') }}</p>
      <div class="table-responsive whitespace-nowrap rounded-primary">
        <table class="table">
          <thead>
            <tr>
              <th>{{ trans('Transaction id') }}</th>
              <th>{{ trans('Cardholder Name') }}</th>
              <th>{{ trans('Last4') }}</th>
              <th>{{ trans('Amount') }}</th>
              <th>{{ trans('Status') }}</th>
              <th>{{ trans('Date') }}</th>
            </tr>
          </thead>
          <tbody v-if="authorizations?.data?.length > 0">
            <tr v-for="authorization in authorizations.data" :key="authorization.id">
              <td>
                {{ authorization.network_data.transaction_id }}
              </td>
              <td>
                {{ authorization.card.cardholder.name }}
              </td>

              <td>{{ authorization.card.last4 }}</td>

              <td>
                {{ formatAmount(+authorization.amount / 100) }}
                <span class="text-xs uppercase">{{ stripe_currency }}</span>
              </td>
              <td>
                <div class="capitalize" :class="badgeClass(authorization.status)">
                  {{ authorization.status }}
                </div>
              </td>
              <td class="text-center">
                {{ moment.unix(authorization.created).format('DD MMM, YYYY') }}
              </td>
            </tr>
          </tbody>
          <NoDataFound v-else for-table="true" />
        </table>
      </div>
      <StripePagination :has_prev="prev" :has_more="has_more" :prev="prev" :next="next" />
    </div>
  </main>
</template>
