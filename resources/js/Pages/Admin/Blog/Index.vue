<script setup>
import AdminLayout from "@/Layouts/Admin/AdminLayout.vue"
import PageHeader from "@/Layouts/Admin/PageHeader.vue"
import { Link, useForm } from "@inertiajs/vue3"
import sharedComposable from "@/Composables/sharedComposable"
import Paginate from "@/Components/Admin/Paginate.vue"
import moment from "moment"
import trans from "@/Composables/transComposable"
import Overview from "@/Components/Admin/OverviewGrid.vue"
import FilterDropdown from "@/Components/Admin/FilterDropdown.vue"
import { Icon } from "@iconify/vue"
import NoDataFound from "@/Components/Admin/NoDataFound.vue"

defineOptions({ layout: AdminLayout })
const { textExcerpt, deleteRow } = sharedComposable()
const props = defineProps([
  "posts",
  "totalPosts",
  "totalActivePosts",
  "totalInActivePosts",
  "request",
])

const blogsStats = [
  {
    value: props.totalPosts,
    title: trans("Total Posts"),
    iconClass: "bx bx-bar-chart",
  },
  {
    value: props.totalActivePosts,
    title: trans("Total Active Posts"),
    iconClass: "bx bx-check-circle",
  },
  {
    value: props.totalInActivePosts,
    title: trans("Total Inactive Posts"),
    iconClass: "bx bx-x-circle",
  },
]

const selectOptions = [
  {
    label: "Title",
    value: "title",
  },
]
</script>

<template>
  <main class="container flex-grow p-4 sm:p-6">
    <PageHeader />
    <div class="space-y-6">
      <Overview :items="blogsStats" grid="3" />

      <FilterDropdown :options="selectOptions" />

      <!-- Customer Table Starts -->
      <div class="table-responsive whitespace-nowrap rounded-primary">
        <table class="table">
          <thead>
            <tr>
              <th class="col-3">{{ trans("Title") }}</th>
              <th class="col-1">{{ trans("Status") }}</th>
              <th class="col-2">{{ trans("Created At") }}</th>
              <th class="col-1">
                <div class="text-right">{{ trans("Action") }}</div>
              </th>
            </tr>
          </thead>
          <tbody v-if="posts.data != 0">
            <tr v-for="blog in posts.data" :key="blog.id">
              <td class="flex">
                <img
                  v-if="blog.preview?.value"
                  v-lazy="blog.preview?.value"
                  class="mr-3 avatar rounded-square"
                />
                <a target="_blank" :href="`/blogs/${blog.slug}`">{{
                  textExcerpt(blog.title, 80)
                }}</a>
              </td>

              <td class="text-left">
                <span
                  class="badge"
                  :class="blog.status == 1 ? 'badge-success' : 'badge-danger'"
                >
                  {{ blog.status == 1 ? trans("Active") : trans("Draft") }}
                </span>
              </td>
              <td>
                {{ moment(blog.created_at).format("D-MMM-Y") }}
              </td>
              <td>
                <div class="flex justify-end">
                  <div class="dropdown" data-placement="bottom-start">
                    <div class="dropdown-toggle">
                      <Icon class="text-lg w-30" icon="bi:three-dots-vertical" />
                    </div>
                    <div class="w-40 dropdown-content">
                      <ul class="dropdown-list">
                        <li class="dropdown-list-item">
                          <Link
                            :href="route('admin.blog.edit', blog.id)"
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
                            @click="deleteRow(route('admin.blog.destroy', blog.slug))"
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

        <Paginate v-if="posts.data.length != 0" :links="posts.links" />
      </div>
      <!-- Customer Table Ends -->
    </div>
  </main>
</template>
