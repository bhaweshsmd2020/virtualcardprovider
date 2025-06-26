<script setup>
import AdminLayout from "@/Layouts/Admin/AdminLayout.vue"
import NoDataFound from "@/Components/Admin/NoDataFound.vue"
import PageHeader from "@/Layouts/Admin/PageHeader.vue"
import { Head, Link } from "@inertiajs/vue3"
import sharedComposable from "@/Composables/sharedComposable"
import moment from "moment"
const props = defineProps(["buttons", "emailtemplates"])
defineOptions({ layout: AdminLayout })
const { deleteRow } = sharedComposable()
</script>

<template>
  <main class="container p-4 sm:p-6">
    <PageHeader />
    <div class="space-y-6">
      <div class="table-responsive whitespace-nowrap rounded-primary">
        <table class="table">
          <thead>
            <tr>
              <th>{{ trans("Subject") }}</th>
              <th>{{ trans("Created On") }}</th>
              <th>{{ trans("Status") }}</th>
              <th class="text-right">{{ trans("Action") }}</th>
            </tr>
          </thead>
          <tbody v-if="emailtemplates.length">
            <tr v-for="row in emailtemplates" :key="row.id">
              <td>
                {{ row.subject }}
              </td>
              <td>
                {{ moment(row.created_at).format('D-MMM-Y') }}
              </td>
              <td>
                <span :class="row.status == 1 ? 'badge badge-success' : 'badge badge-danger'">
                  {{ row.status == 1 ? trans("Active") : trans("Inactive") }}
                </span>
              </td>
              <td class="flex gap-3">
                <Link :href="route('admin.email-templates.edit', row.id)" class="btn btn-primary">
                {{ trans("Edit") }}</Link>
              </td>
            </tr>
          </tbody>
          <NoDataFound v-else :for-table="true" />
        </table>
      </div>
    </div>
  </main>
</template>
