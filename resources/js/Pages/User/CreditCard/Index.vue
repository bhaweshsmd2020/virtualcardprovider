<script setup>
import UserLayout from '@/Layouts/User/UserLayout.vue'
import PageHeader from '@/Layouts/Admin/PageHeader.vue'
import trans from '@/Composables/transComposable'
import Overview from '@/Components/Admin/OverviewGrid.vue'
import Pagination from '@/Components/Admin/Paginate.vue'
import drawer from '@/Plugins/Admin/drawer'
import { onMounted } from 'vue'
import NoDataFound from '@/Components/Admin/NoDataFound.vue'
import sharedComposable from '@/Composables/sharedComposable'
import CreditCard from '@/Components/CreditCard.vue'
import FilterDropdown from '@/Components/Admin/FilterDropdown.vue'
import GetCard from '@/Components/User/GetCard.vue'

defineOptions({ layout: UserLayout })

onMounted(() => {
  drawer.init()
})
const { formatCurrency } = sharedComposable()
const props = defineProps([
  'creditCards',
  'request',
  'totalCards',
  'totalPendingCards',
  'totalApprovedCards',
  'totalRejectedCards',
  'type',
  'buttons',
  'card_intro_details'
])

const overviews = [
  {
    value: props.totalCards,
    title: trans('Total Cards'),
    iconClass: 'bx bx-badge-check'
  },
  {
    value: props.totalPendingCards,
    title: trans('Pending Cards'),
    iconClass: 'bx bx-badge'
  },
  {
    value: props.totalApprovedCards,
    title: trans('Approved Cards'),
    iconClass: 'bx bx-check'
  },
  {
    value: props.totalRejectedCards,
    title: trans('Rejected Cards'),
    iconClass: 'bx bx-x-circle'
  }
]
const filterOptions = [
  {
    label: 'Title',
    value: 'title'
  },
  {
    label: 'Last 4',
    value: 'last4'
  },
  {
    label: 'Status',
    value: 'status',
    options: [
      {
        label: 'Active',
        value: 'active'
      },
      {
        label: 'Inactive',
        value: 'inactive'
      }
    ]
  }
]
</script>

<template>
  <!-- Main Content Starts -->
  <main class="container flex-grow p-4 sm:p-6">
    <PageHeader title="Prepaid Cards" classes="mr-0 mt-4" :buttons="buttons" />

    <div class="space-y-4">
      <Overview class="mb-10" :items="overviews" />
      <FilterDropdown :options="filterOptions" />
      <div class="grid gap-5 md:grid-cols-2 xl:grid-cols-3" v-if="creditCards.data?.length >= 1">
        <template v-for="card in creditCards.data" :key="card.id">
          <Link class="relative" :href="route('user.credit-cards.show', card.id)">
            <CreditCard :credit-card="card" />
            <span
              class="badge absolute top-4 -rotate-45 scale-110 capitalize"
              :class="{
                'badge-success': card.status === 'active',
                'badge-danger': card.status === 'inactive'
              }"
            >
              {{ card.status }}
            </span>
          </Link>
        </template>
      </div>

      <div v-else>
        <GetCard border="border-gray-200" :card_intro_details="card_intro_details" class="mt-14" />
      </div>

      <Pagination :links="creditCards.links" />
    </div>
  </main>
</template>
