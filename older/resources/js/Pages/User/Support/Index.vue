<script setup>
import UserLayout from '@/Layouts/User/UserLayout.vue'
import PageHeader from '@/Layouts/Admin/PageHeader.vue'
import Overview from '@/Components/Admin/OverviewGrid.vue'
import moment from 'moment'
import NoDataFound from '@/Components/Admin/NoDataFound.vue'
import Paginate from '@/Components/Admin/Paginate.vue'
import trans from '@/Composables/transComposable'
import sharedComposable from '@/Composables/sharedComposable'
import FilterDropdown from '@/Components/Admin/FilterDropdown.vue'
defineOptions({ layout: UserLayout })
const { textExcerpt } = sharedComposable()

const props = defineProps([
  'buttons',
  'request',
  'supports',
  'pendingSupport',
  'openSupport',
  'closedSupport',
  'totalSupports',
  'type'
])

const stats = [
  {
    value: props.totalSupports,
    title: 'Total Supports',
    iconClass: 'bx bx-support'
  },
  {
    value: props.pendingSupport,
    title: 'Pending Supports',
    iconClass: 'bx bx-revision'
  },
  {
    value: props.openSupport,
    title: 'Open Supports',
    iconClass: 'bx bx-folder-open'
  },
  {
    value: props.closedSupport,
    title: 'Closed Supports',
    iconClass: 'bx bx-window-close'
  }
]
const filterForm = [
  {
    value: 'ticket_no',
    label: 'Ticket No'
  },
  {
    value: 'Subject',
    label: 'subject'
  },
  {
    label: 'Status',
    value: 'status',
    options: [
      {
        label: 'Pending',
        value: '2'
      },
      {
        label: 'Open',
        value: '1'
      },
      {
        label: 'Closed',
        value: '0'
      }
    ]
  }
]
</script>

<template>
  <main class="container flex-grow p-4 sm:p-6">
    <PageHeader title="Supports" :buttons="buttons" />
    <div class="space-y-6">
      <Overview :items="stats" />

      <FilterDropdown :options="filterForm" />

      <div class="table-responsive whitespace-nowrap rounded-primary">
        <table class="table">
          <thead>
            <tr>
              <th>{{ trans('Ticket No') }}</th>
              <th>{{ trans('Subject') }}</th>
              <th>
                {{ trans('Conversations') }}
              </th>
              <th>{{ trans('Status') }}</th>
              <th>
                {{ trans('Created At') }}
              </th>
              <th>
                <p class="text-end">{{ trans('Ticket') }}</p>
              </th>
            </tr>
          </thead>

          <tbody v-if="supports.total">
            <tr v-for="support in supports.data" :key="support.id">
              <td class="text-center">
                {{ support.ticket_no }}
              </td>
              <td>
                <a class="text-dark" :href="'/user/supports/' + support.id">
                  {{ textExcerpt(support.subject, 50) }}
                </a>
              </td>
              <td class="text-center">
                {{ support.conversations_count }}
              </td>
              <td>
                <span
                  :class="
                    support.status == 2
                      ? 'badge badge-warning'
                      : support.status == 1
                      ? 'badge badge-success'
                      : 'badge badge-danger'
                  "
                >
                  {{
                    trans(support.status == 2 ? 'pending' : support.status == 1 ? 'Open' : 'Closed')
                  }}
                </span>
              </td>

              <td class="text-center">
                {{ moment(support.created_at).format('d MMM y') }}
              </td>
              <td>
                <div class="flex justify-end">
                  <Link :href="'/user/supports/' + support.id" class="btn btn-primary btn-sm">
                    {{ trans('View') }}
                  </Link>
                </div>
              </td>
            </tr>
          </tbody>
          <NoDataFound v-else for-table="true" />
        </table>

        <Paginate :links="supports.links" />
      </div>
    </div>
  </main>
</template>
