<script setup>
import AdminLayout from "@/Layouts/Admin/AdminLayout.vue"
import PageHeader from "@/Layouts/Admin/PageHeader.vue"
import { Link, useForm, router } from "@inertiajs/vue3"
import Paginate from "@/Components/Admin/Paginate.vue"
import moment from "moment"
import { ref } from "vue"
import trans from "@/Composables/transComposable"
import sharedComposable from "@/Composables/sharedComposable"
import Overview from "@/Components/Admin/OverviewGrid.vue"
import SpinnerBtn from "@/Components/SpinnerBtn.vue"
import drawer from "@/Plugins/Admin/drawer"
import { onMounted } from "vue"
import toast from "@/Composables/toastComposable"
import NoDataFound from "@/Components/Admin/NoDataFound.vue"
import FilterDropdown from "@/Components/Admin/FilterDropdown.vue"

defineOptions({ layout: AdminLayout })
const { deleteRow } = sharedComposable()

onMounted(() => {
  drawer.init()
})

const props = defineProps([
  "categories",
  "totalCategories",
  "activeCategories",
  "inActiveCategories",
  "languages",
  "buttons",
  "segments",
])

const categoriesStats = [
  {
    value: props.totalCategories,
    title: trans("Total Categories"),
    iconClass: "bx bx-bar-chart",
  },
  {
    value: props.activeCategories,
    title: trans("Active Categories"),
    iconClass: "bx bx-check-circle",
  },
  {
    value: props.inActiveCategories,
    title: trans("Inactive Categories"),
    iconClass: "bx bx-x-circle",
  },
]

const categoryForm = useForm({
  title: "",
  status: 1,
  language: "en",
})

const editForm = ref({})

const storeCategory = () => {
  categoryForm.post(route("admin.card-category.store"), {
    onSuccess: () => {
      categoryForm.reset()
      toast.success(trans("Category has been added successfully"))
      drawer.of("#addNewCategoryDrawer").hide()
    },
  })
}

const openEditCategoryDrawer = (category) => {
  editForm.value = { ...category }
  drawer.of("#editCategoryDrawer").show()
}

const updateCategory = () => {
  router.patch(route("admin.card-category.update", editForm.value.id), editForm.value, {
    onSuccess: () => {
      editForm.value = {}
      toast.success(trans("Category has been updated successfully"))
      drawer.of("#editCategoryDrawer").hide()
    },
  })
}

const filterOptions = [
  {
    label: "Title",
    value: "title",
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
  <main class="container p-4 sm:p-6">
    <PageHeader title="Card Categories" :buttons="buttons" />
    <div class="space-y-6">
      <Overview :items="categoriesStats" grid="3" />
      <FilterDropdown :options="filterOptions" />

      <div class="table-responsive whitespace-nowrap rounded-primary">
        <table class="table">
          <thead>
            <tr>
              <th>{{ trans("Name") }}</th>
              <th>{{ trans("Slug") }}</th>

              <th>{{ trans("Status") }}</th>
              <th>{{ trans("Created At") }}</th>
              <th class="flex justify-end">{{ trans("Action") }}</th>
            </tr>
          </thead>

          <tbody v-if="categories.total">
            <tr v-for="category in categories.data" :key="category.id">
              <td>
                {{ category.title }}
              </td>
              <td>
                {{ category.slug }}
              </td>

              <td>
                <span
                  class="badge"
                  :class="category.status == 1 ? 'badge-success' : 'badge-danger'"
                >
                  {{ category.status == 1 ? trans("Active") : trans("Draft") }}
                </span>
              </td>
              <td>
                {{ moment(category.created_at).format("D-MMM-Y") }}
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
                          <button
                            @click="openEditCategoryDrawer(category)"
                            class="dropdown-link"
                          >
                            <Icon
                              class="h-6 text-slate-400"
                              icon="material-symbols:edit-outline"
                            />
                            <span>{{ trans("Edit") }}</span>
                          </button>
                        </li>

                        <li class="dropdown-list-item">
                          <Link
                            as="button"
                            class="dropdown-link"
                            @click="deleteRow('/admin/category/' + category.id)"
                          >
                            <Icon class="h-6" icon="material-symbols:delete-outline" />
                            <span>{{ trans("Delete") }}</span>
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
      <Paginate :links="categories.links" />
    </div>
  </main>

  <div id="addNewCategoryDrawer" class="drawer drawer-right">
    <form @submit.prevent="storeCategory()">
      <div class="drawer-header">
        <h5>{{ trans("Add New Category") }}</h5>
        <button
          type="button"
          class="btn btn-plain-secondary dark:text-slate-300 dark:hover:bg-slate-700 dark:focus:bg-slate-700"
          data-dismiss="drawer"
        >
          <i data-feather="x" width="1.5rem" height="1.5rem"></i>
        </button>
      </div>
      <div class="drawer-body">
        <div class="mb-2">
          <label>{{ trans("Title") }}</label>
          <input
            v-model="categoryForm.title"
            type="text"
            name="title"
            class="input"
            required
          />
        </div>
        <div class="mb-2">
          <label>{{ trans("Status") }}</label>
          <select required v-model="categoryForm.status" class="select" name="status">
            <option value="1">{{ trans("Active") }}</option>
            <option value="0">{{ trans("Inactive") }}</option>
          </select>
        </div>
      </div>
      <div class="drawer-footer">
        <div class="flex justify-between gap-2">
          <button type="button" class="btn btn-secondary w-full" data-dismiss="drawer">
            <span> {{ trans("Close") }}</span>
          </button>
          <SpinnerBtn
            classes="btn btn-primary w-full"
            :processing="categoryForm.processing"
            :btn-text="trans('Save Changes')"
          />
        </div>
      </div>
    </form>
  </div>

  <div id="editCategoryDrawer" class="drawer drawer-right">
    <form @submit.prevent="updateCategory()">
      <div class="drawer-header">
        <h5>{{ trans("Edit Category") }}</h5>
        <button
          type="button"
          class="btn btn-plain-secondary dark:text-slate-300 dark:hover:bg-slate-700 dark:focus:bg-slate-700"
          data-dismiss="drawer"
        >
          <i data-feather="x" width="1.5rem" height="1.5rem"></i>
        </button>
      </div>
      <div class="drawer-body">
        <div class="mb-2">
          <label>{{ trans("Title") }}</label>
          <input
            v-model="editForm.title"
            type="text"
            name="title"
            class="input"
            required
          />
        </div>

        <div class="mb-2">
          <label>{{ trans("Status") }}</label>
          <select v-model="editForm.status" class="select" name="status">
            <option value="1">{{ trans("Active") }}</option>
            <option value="0">{{ trans("Draft") }}</option>
          </select>
        </div>
      </div>
      <div class="drawer-footer">
        <div class="flex justify-between gap-2">
          <button type="button" class="btn btn-secondary w-full" data-dismiss="drawer">
            <span> {{ trans("Close") }}</span>
          </button>
          <SpinnerBtn
            classes="btn btn-primary w-full"
            :processing="editForm.processing"
            :btn-text="trans('Save Changes')"
          />
        </div>
      </div>
    </form>
  </div>
</template>
