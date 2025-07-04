<script setup>
import AdminLayout from "@/Layouts/Admin/AdminLayout.vue"
import PageHeader from "@/Layouts/Admin/PageHeader.vue"
import sharedComposable from "@/Composables/sharedComposable"
import Paginate from "@/Components/Admin/Paginate.vue"
import Overview from "@/Components/Admin/OverviewGrid.vue"
import NoDataFound from "@/Components/Admin/NoDataFound.vue"
import trans from "@/Composables/transComposable"
import moment from "moment"
import FilterDropdown from "@/Components/Admin/FilterDropdown.vue"
const { deleteRow } = sharedComposable()
defineOptions({ layout: AdminLayout })

const props = defineProps([
  "methods",
  "buttons",
  "segments",
  "totalPayoutMethod",
  "totalActivePayoutMethod",
  "totalInActivePayoutMethod",
])
const stats = [
  {
    value: props.totalPayoutMethod,
    title: trans("Total Payout Methods"),
    iconClass: "bx bx-box",
  },
  {
    value: props.totalActivePayoutMethod,
    title: trans("Active Payout Methods"),
    iconClass: "bx bx-check",
  },
  {
    value: props.totalInActivePayoutMethod,
    title: trans("Inactive Payout Methods"),
    iconClass: "bx bx-x-circle",
  },
]

const filterOptions = [
  {
    label: "Name",
    value: "name",
  },
  {
    label: "Currency",
    value: "currency_name",
  },
  {
    label: "Status",
    value: "status",
    options: [
      {
        label: "Active",
        value: 1,
      },
      {
        label: "Inactive",
        value: 0,
      },
    ],
  },
]
</script>

<template>
  <main class="container flex-grow p-4 sm:p-6">
    <PageHeader title="Payout Methods" :segments="segments" :buttons="buttons" />
    <div class="space-y-6">
      <Overview :items="stats" grid="3" />
      <FilterDropdown :options="filterOptions" />
      <div class="table-responsive whitespace-nowrap rounded-primary">
        <table class="table">
          <thead>
            <tr>
              <th>{{ trans("Name") }}</th>
              <th>{{ trans("Currency") }}</th>
              <th>{{ trans("Limit") }}</th>
              <th class="text-center">{{ trans("Charge") }}</th>
              <th>{{ trans("Status") }}</th>
              <th>{{ trans("Created At") }}</th>
              <th>
                <p class="text-end">{{ trans("Action") }}</p>
              </th>
            </tr>
          </thead>
          <tbody v-if="methods.data.length != 0">
            <tr v-for="method in methods.data" :key="method.id">
              <td>
                <img v-lazy="method.image" alt="image" class="avatar mr-1 inline rounded" />
                {{ method.name }}
              </td>
              <td>{{ method.currency_name }}</td>
              <td>
                {{ method.min_limit + " - " + method.max_limit }}
                {{ method.currency_name }}
              </td>
              <td class="text-center">
                {{
                  method.charge_type == "percentage"
                    ? method.percent_charge + "%"
                    : method.fixed_charge + " " + method.currency_name
                }}
              </td>
              <td class="text-left">
                <span
                  class="badge"
                  :class="method.status == 1 ? 'badge-success' : 'badge-danger'"
                >
                  {{ method.status == 1 ? trans("Active") : trans("Draft") }}
                </span>
              </td>
              <td>
                {{ moment(method.created_at).format("DD MMM Y") }}
              </td>
              <td class="text-right">
                <div class="flex justify-end">
                  <div class="dropdown" data-placement="bottom-start">
                    <div class="dropdown-toggle">
                      <Icon class="w-30 text-lg" icon="bi:three-dots-vertical" />
                    </div>
                    <div class="dropdown-content w-40">
                      <ul class="dropdown-list">
                        <li class="dropdown-list-item">
                          <Link
                            :href="route('admin.payout-methods.edit', method.id)"
                            class="dropdown-link"
                          >
                            <Icon
                              class="h-6 text-slate-400"
                              icon="material-symbols:edit-outline"
                            />
                            <span>{{ trans("Edit") }}</span>
                          </Link>
                        </li>

                        <li class="dropdown-list-item">
                          <button
                            class="dropdown-link"
                            @click="
                              deleteRow(
                                route('admin.payout-methods.destroy', method.id),
                                trans('Method has been deleted successfully')
                              )
                            "
                          >
                            <Icon class="h-6" icon="material-symbols:delete-outline" />
                            <span>{{ trans("Delete") }}</span>
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
      <Paginate :links="methods.links" />
    </div>
  </main>
</template>
