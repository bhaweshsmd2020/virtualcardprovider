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
import { useForm } from "@inertiajs/vue3"
import SpinnerBtn from "@/Components/SpinnerBtn.vue"
import toast from "@/Composables/toastComposable"

defineOptions({ layout: AdminLayout })

onMounted(() => {
  drawer.init()
})
const { formatCurrency } = sharedComposable()
const props = defineProps([
  "creditCards",
  "request",
  "totalCards",
  "totalPendingCards",
  "totalApprovedCards",
  "totalRejectedCards",
  "type",
  "buttons",
])

const overviews = [
  {
    value: props.totalCards,
    title: trans("Total Cards"),
    iconClass: "bx bx-badge-check",
  },
  {
    value: props.totalPendingCards,
    title: trans("Pending Cards"),
    iconClass: "bx bx-badge",
  },
  {
    value: props.totalApprovedCards,
    title: trans("Approved Cards"),
    iconClass: "bx bx-check",
  },
  {
    value: props.totalRejectedCards,
    title: trans("Rejected Cards"),
    iconClass: "bx bx-x-circle",
  },
]

const filterOptions = [
  {
    label: "Title",
    value: "title",
  },
  {
    label: "Last 4",
    value: "last4",
  },
  {
    label: "Status",
    value: "status",
    options: [
      {
        label: "Active",
        value: "active",
      },
      {
        label: "Inactive",
        value: "inactive",
      },
    ],
  },
]
</script>

<template>
  <!-- Main Content Starts -->
  <main class="container flex-grow p-4 sm:p-6">
    <PageHeader :title="trans('Prepaid Card Lists')" />

    <div class="space-y-4">
      <Overview :items="overviews" />
      <FilterDropdown :options="filterOptions" />
      <div class="table-responsive whitespace-nowrap rounded-primary">
        <table class="table">
          <thead>
            <tr>
              <th>{{ trans("Invoice") }}</th>
              <th>{{ trans("Cardholder Name") }}</th>
              <th>{{ trans("Last 4") }}</th>
              <th>{{ trans("Status") }}</th>
              <th>{{ trans("Expiry date") }}</th>
              <th>{{ trans("Created At") }}</th>
              <th class="!text-right">
                {{ trans("Actions") }}
              </th>
            </tr>
          </thead>
          <tbody v-if="creditCards.total">
            <tr v-for="card in creditCards.data" :key="card.id">
              <td>
                <Link
                  :href="'/admin/credit-cards/' + card.id"
                  class="text-sm font-medium text-primary-500 transition duration-150 ease-in-out hover:underline"
                >
                  {{ card.card_order.invoice_no }}
                </Link>
              </td>
              <td>{{ card?.user.name }}</td>
              <td>{{ card.last4 }}</td>

              <td>
                <div class="badge badge-success capitalize">
                  {{ card.status }}
                </div>
              </td>
              <td>{{ card.exp_month }}/ {{ card.exp_year }}</td>
              <td class="text-center">
                {{ moment(card.created_at).format("DD MMM, YYYY") }}
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
                            :href="'/admin/credit-cards/' + card.id"
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

      <Pagination :links="creditCards.links" />
    </div>
  </main>
</template>
