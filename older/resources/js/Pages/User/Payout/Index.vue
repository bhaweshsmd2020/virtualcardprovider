<script setup>
import UserLayout from '@/Layouts/User/UserLayout.vue'
import trans from '@/Composables/transComposable'
import PageHeader from '@/Layouts/Admin/PageHeader.vue'
import NoDataFound from '@/Components/Admin/NoDataFound.vue'
const props = defineProps(['pending_amount', 'approved_amount', 'methods', 'segments', 'buttons'])
defineOptions({ layout: UserLayout })

</script>

<template>
  <main class="container flex-grow p-4 sm:p-6">
    <PageHeader />
    <div class="mx-auto mt-10 space-y-4 xl:w-8/12" v-if="methods.length > 0">
      <div
        v-for="method in methods"
        :key="method.id"
        class="card flex items-center justify-between p-8"
      >
        <div class="flex items-center gap-x-5">
          <img class="w-32 rounded" v-lazy="method.image" alt="" />
          <div>
            <Link class="text-2xl" :href="route('user.payout.show', method.id)">
              {{ method.name }}
            </Link>
            <p class="text-sm text-gray-500">
              {{ trans('Payout Limitation: ') }}
              <span class="text-gray-950 dark:text-white">
                {{ method.min_limit + ' - ' + method.max_limit }}
                {{ method.currency_name }}
              </span>
            </p>
          </div>
        </div>
        <Link :href="route('user.payout.show', method.id)" class="btn btn-success">
          <i class="bx bx-plus text-lg"></i> {{ trans('Make Payout') }}
        </Link>
      </div>
    </div>

    <NoDataFound v-else :message="trans('No payout methods found')" />
  </main>
</template>
