<script setup>
import AdminLayout from "@/Layouts/Admin/AdminLayout.vue"
import PageHeader from "@/Layouts/Admin/PageHeader.vue"
import Overview from "@/Components/Admin/OverviewGrid.vue"
import NoDataFound from "@/Components/Admin/NoDataFound.vue"
import Paginate from "@/Components/Admin/Paginate.vue"
import trans from "@/Composables/transComposable"
import sharedComposable from "@/Composables/sharedComposable"
import FilterDropdown from "@/Components/Admin/FilterDropdown.vue"

const { badgeClass } = sharedComposable()
defineOptions({ layout: AdminLayout })
const props = defineProps(["segments", "buttons", "data", "orders", "request"])

const stats = [
  {
    value: props.data.total_payouts,
    title: trans("Total Payouts"),
    iconClass: "bx bx-box",
  },
  {
    value: props.data.total_pending,
    title: trans("Pending Payouts"),
    iconClass: "bx bx-loader",
  },
  {
    value: props.data.total_approved,
    title: trans("Total Approved"),
    iconClass: "bx bx-check-circle",
  },
  {
    value: props.data.total_rejected,
    title: trans("Total Rejected"),
    iconClass: "bx bx-x-circle",
  },
]
const filterForm = [
  {
    label: "Invoice No",
    value: "invoice_no",
  },
  {
    label: "Status",
    value: "status",
    options: [
      {
        label: "Pending",
        value: "pending",
      },
      {
        label: "Completed",
        value: "completed",
      },
      {
        label: "Failed",
        value: "failed",
      },
    ],
  },
]
</script>

<template>
  <main class="container flex-grow p-4 sm:p-6">
    <PageHeader
      :title="trans('Payout Requests')"
      :segments="segments"
      :buttons="buttons"
    />
    <div class="space-y-6">
      <Overview :items="stats" grid="4" />
      <FilterDropdown :options="filterForm" />
      <div class="table-responsive">
        <table class="table">
          <thead>
            <tr>
              <th>
                {{ trans("Invoice No") }}
              </th>
              <th>
                {{ trans("Amount") }}
              </th>
              <th>
                {{ trans("Gateway") }}
              </th>
              <th>{{ trans("Payout From") }}</th>
              <th>
                {{ trans("User") }}
              </th>
              <th>{{ trans("Status") }}</th>
              <th class="!text-end">
                {{ trans("Created At") }}
              </th>
            </tr>
          </thead>
          <tbody class="list" v-if="data.payouts.data.length != 0">
            <tr v-for="payout in data.payouts.data" :key="payout.id">
              <td class="text-center">
                <Link
                  class="text-sm font-medium text-primary-500 transition duration-150 ease-in-out hover:underline"
                  :href="route('admin.payouts.show', payout.id)"
                >
                  {{ payout.invoice_no }}
                </Link>
              </td>
              <td>
                {{ payout.amount_with_currency }}
              </td>
              <td>
                {{ payout.method.name || "" }}
              </td>
              <td>{{ payout.wallet_id ? trans("Wallet") : trans("Balance") }}</td>
              <td>
                <Link :href="route('admin.users.show', payout.user.id)">
                  {{ payout.user.name || "" }}
                </Link>
              </td>
              <td>
                <span :class="badgeClass(payout.status)"> {{ payout.status }}</span>
              </td>
              <td class="!text-end">
                {{ payout.user.created_at_date }}
              </td>
            </tr>
          </tbody>
          <NoDataFound v-else for-table="true" />
        </table>
      </div>
      <Paginate :links="data.payouts.links" />
    </div>
  </main>
</template>
