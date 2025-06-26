<script setup>
import AdminLayout from '@/Layouts/Admin/AdminLayout.vue'
import PageHeader from '@/Layouts/Admin/PageHeader.vue'
import sharedComposable from '@/Composables/sharedComposable'
defineOptions({ layout: AdminLayout })
const props = defineProps(['payout', 'segments', 'buttons'])
import { modal } from '@/Composables/actionModalComposable'
const { formatCurrency, badgeClass } = sharedComposable()
</script>

<template>
  <main class="container flex-grow p-4 sm:p-6">
    <PageHeader
      :title="trans('Payout - ' + payout.invoice_no)"
      :segments="segments"
      :buttons="buttons"
    />

    <div class="space-y-6">
      <div class="card card-body">
        <h5>{{ trans('Method info') }}</h5>
        <div class="table-responsive">
          <table class="table">
            <thead>
              <tr>
                <th>{{ trans('Name') }}</th>
                <th>{{ trans('Charge') }}</th>
                <th>{{ trans('Charge Type') }}</th>
                <th>{{ trans('Delay') }}</th>
              </tr>
            </thead>
            <tbody>
              <tr>
                <td>{{ payout.method.name || '' }}</td>
                <td>
                  {{
                    payout.method.percent_charge > 0
                      ? payout.method.percent_charge
                      : payout.method.fixed_charge
                  }}
                </td>
                <td>
                  <span
                    class="badge"
                    :class="payout.method.percent_charge > 0 ? 'badge-primary' : 'badge-warning'"
                  >
                    {{ payout.method.percent_charge > 0 ? trans('Percentage') : trans('Fixed') }}
                  </span>
                </td>
                <td>{{ payout.method.delay || '' }}</td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>

      <div class="card card-body">
        <h5 class="mb-3">{{ trans('Information') }}</h5>
        <div class="table-responsive">
          <table class="table">
            <thead>
              <tr>
                <th>{{ trans('Amount') }}</th>
                <th>{{ trans('User') }}</th>
                <th>{{ trans('Email') }}</th>
                <th>{{ trans('Payout From') }}</th>
                <th>{{ trans('Currency') }}</th>
                <th>{{ trans('Charge') }}</th>
                <th>{{ trans('Status') }}</th>
                <th class="!text-end">{{ trans('Created At') }}</th>
              </tr>
            </thead>
            <tbody>
              <tr>
                <td>{{ payout.amount + payout.currency }}</td>
                <td>{{ payout.user.name || '' }}</td>
                <td>{{ payout.user.email || '' }}</td>
                <td>{{ payout.wallet_id ? trans('Wallet') : trans('Balance') }}</td>
                <td class="uppercase">{{ payout.requested_currency }}</td>
                <td>
                  {{ payout.charge }} <span class="uppercase">{{ payout.currency }}</span>
                </td>
                <td>
                  <span :class="badgeClass(payout.status)">
                    {{ payout.status }}
                  </span>
                </td>
                <td class="!text-end">{{ payout.created_at_date }}</td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>

      <div class="card card-body">
        <h5 class="mb-3">{{ trans('Details') }}</h5>
        <div class="table-responsive">
          <table class="table">
            <thead>
              <tr>
                <th>
                  {{ trans('Current Balance') }}
                </th>
                <td class="text-end">
                  <template v-if="payout.wallet_id">
                    <span class="uppercase">{{ payout.wallet?.currency.currency }}</span>
                    {{ payout.wallet?.balance }}
                  </template>
                  <template v-else>
                    {{ formatCurrency(payout.user.balance || 0) }}
                  </template>
                </td>
              </tr>
            </thead>
            <tbody>
              <tr v-for="(meta, index) in payout.meta" :key="index">
                <th>
                  {{ index }}
                </th>
                <td>
                  {{ meta }}
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>

      <div class="card card-body mt-4">
        <div class="mb-3 flex items-center justify-between">
          <h5>{{ trans('Actions') }}</h5>
          <span class="capitalize" :class="badgeClass(payout.status)">
            {{ payout.status }}
          </span>
        </div>
        <div class="flex gap-x-2">
          <button
            class="btn btn-soft-primary"
            @click="
              modal.init(route('admin.payouts.update', payout.id), {
                method: 'put',
                data: { status: 'completed' }
              })
            "
          >
            <i class="bx bx-check text-lg text-slate-400"></i>
            {{ trans('Approve') }}
          </button>
          <button
            class="btn btn-soft-primary"
            @click="
              modal.init(route('admin.payouts.update', payout.id), {
                method: 'put',
                data: { status: 'failed' }
              })
            "
          >
            <i class="bx bx-x text-lg text-slate-400"></i>
            {{ trans('Reject') }}
          </button>

          <button
            class="btn btn-soft-primary"
            @click="
              modal.init(route('admin.payouts.update', payout.id), {
                method: 'put',
                data: {
                  status: 'failed',
                  paymentStatus: 'return'
                }
              })
            "
          >
            <i class="bx bx-rotate-right text-lg text-slate-400"></i>

            {{ trans('Reject & revert balance') }}
          </button>
        </div>
      </div>
    </div>
  </main>
</template>
