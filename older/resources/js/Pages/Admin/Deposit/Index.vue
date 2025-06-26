<script setup>
import AdminLayout from "@/Layouts/Admin/AdminLayout.vue"
import PageHeader from "@/Layouts/Admin/PageHeader.vue"
import trans from "@/Composables/transComposable"
import Overview from "@/Components/Admin/OverviewGrid.vue"
import Pagination from "@/Components/Admin/Paginate.vue"
import drawer from "@/Plugins/Admin/drawer"
import { onMounted } from "vue"
import NoDataFound from "@/Components/Admin/NoDataFound.vue"
import FilterDropdown from "@/Components/Admin/FilterDropdown.vue"
import sharedComposable from "@/Composables/sharedComposable"
import moment from "moment"

defineOptions({ layout: AdminLayout })

onMounted(() => {
  drawer.init()
})
const { formatCurrency, badgeClass } = sharedComposable()
const props = defineProps([
  "segments",
  "deposits",
  "request",
  "totalDeposits",
  "totalAmount",
  "totalApprovedDeposits",
  "totalRejectedDeposits",
  "type",
])

const orderOverviews = [
  {
    value: props.totalDeposits,
    title: trans("Total Deposits"),
    iconClass: "bx bx-badge-check",
  },
  {
    value: formatCurrency(props.totalAmount || 0),
    title: trans("Total Amount"),
    iconClass: "bx bx-dollar",
  },
  {
    value: props.totalApprovedDeposits,
    title: trans("Completed Deposits"),
    iconClass: "bx bx-check",
  },
  {
    value: props.totalRejectedDeposits,
    title: trans("Declined Deposits"),
    iconClass: "bx bx-x-circle",
  },
]

const filterOptions = [
  {
    label: "Invoice No",
    value: "invoice_no",
  },
  {
    label: "User Email",
    value: "user_email",
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
        label: "Approved",
        value: "approved",
      },
      {
        label: "Rejected",
        value: "rejected",
      },
    ],
  },
]
</script>

<template>
  <!-- Main Content Starts -->
  <main class="container flex-grow p-4 sm:p-6">
    <PageHeader :title="trans('Deposits List')" :buttons="buttons" />

    <div class="space-y-4">
      <Overview :items="orderOverviews" />
      <FilterDropdown :options="filterOptions" />
      <div class="table-responsive whitespace-nowrap rounded-primary">
        <table class="table">
          <thead>
            <tr>
              <th>{{ trans("Invoice") }}</th>
              <th>{{ trans("User Name") }}</th>
              <th>{{ trans("Payment Method") }}</th>
              <th>{{ trans("Amount") }}</th>
              <th>{{ trans("Status") }}</th>
              <th>{{ trans("Deposit Fee") }}</th>
              <th>{{ trans("Created At") }}</th>
              <th class="!text-right">
                {{ trans("Actions") }}
              </th>
            </tr>
          </thead>
          <tbody v-if="deposits.total">
            <tr v-for="deposit in deposits.data" :key="deposit.id">
              <td>
                <Link
                  :href="'/admin/deposits/' + deposit.id"
                  class="text-sm font-medium text-primary-500 transition duration-150 ease-in-out hover:underline"
                >
                  {{ deposit.invoice_no }}
                </Link>
              </td>
              <td>{{ deposit?.user?.name }}</td>
              <td>{{ deposit?.gateway?.name || "balance" }}</td>
              <td>{{ formatCurrency(deposit.amount) }}</td>
              <td>
                <div class="capitalize" :class="badgeClass(deposit.status)">
                  {{ deposit.status }}
                </div>
              </td>
              <td>{{ formatCurrency(+deposit.deposit_fee) }}</td>
              <td class="text-center">
                {{ moment(deposit.created_at).format("DD MMM, YYYY") }}
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
                            :href="'/admin/deposits/' + deposit.id"
                            class="dropdown-link"
                          >
                            <Icon class="h-6" icon="fe:eye" />
                            <span>{{ trans("View") }}</span>
                          </Link>
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

      <Pagination :links="deposits.links" />
    </div>
  </main>
</template>
