<script setup>
import AdminLayout from "@/Layouts/Admin/AdminLayout.vue"
import PageHeader from "@/Layouts/Admin/PageHeader.vue"
import { Link, useForm, router } from "@inertiajs/vue3"
import moment from "moment"
import { onMounted, ref } from "vue"
import sharedComposable from "@/Composables/sharedComposable"
import NoDataFound from "@/Components/Admin/NoDataFound.vue"
import Overview from "@/Components/Admin/OverviewGrid.vue"
import SpinnerBtn from "@/Components/SpinnerBtn.vue"
import drawer from "@/Plugins/Admin/drawer"
import toast from "@/Composables/toastComposable"
import trans from "@/Composables/transComposable"
defineOptions({ layout: AdminLayout })
const { deleteRow } = sharedComposable()
const props = defineProps([
  "tags",
  "totalTags",
  "activeTags",
  "inActiveTags",
  "languages",
  "buttons",
])

onMounted(() => {
  drawer.init()
})

const tagsStats = [
  {
    value: props.totalTags,
    title: trans("Total Tags"),
    iconClass: "bx bx-bar-chart",
  },
  {
    value: props.activeTags,
    title: trans("Active Tags"),
    iconClass: "bx bx-check-circle",
  },
  {
    value: props.inActiveTags,
    title: trans("Inactive Tags"),
    iconClass: "bx bx-x-circle",
  },
]

const tagForm = useForm({
  title: "",
  status: "active",
  language: "",
})

const editForm = ref({})

const storeTag = () => {
  tagForm.post(route("admin.tag.store"), {
    onSuccess: () => {
      tagForm.reset()
      toast.success(trans("Tag has been added successfully"))
      drawer.of("#addNewTagDrawer").hide()
    },
  })
}

const openEditTagDrawer = (tag) => {
  editForm.value = { ...tag }
  drawer.of("#editTagDrawer").show()
}

const updateTag = () => {
  router.patch(route("admin.tag.update", editForm.value.id), editForm.value, {
    onSuccess: () => {
      editForm.value = {}
      toast.success(trans("Tag has been updated successfully"))
      drawer.of("#editTagDrawer").hide()
    },
  })
}
</script>

<template>
  <main class="container p-4 sm:p-6">
    <PageHeader title="Tags" :buttons="buttons" />
    <div class="space-y-6">
      <Overview :items="tagsStats" grid="3" />

      <div class="card">
        <div class="table-responsive whitespace-nowrap rounded-primary">
          <table class="table">
            <thead>
              <tr>
                <th class="w-2/12">{{ trans("Name") }}</th>
                <th class="w-2/12">{{ trans("Slug") }}</th>
                <th class="w-2/12">
                  <p class="text-center">{{ trans("Uses for blog") }}</p>
                </th>
                <th class="w-2/12">{{ trans("Status") }}</th>
                <th class="w-2/12">{{ trans("Created At") }}</th>
                <th class="w-2/12 mr-auto">
                  <p class="text-end">{{ trans("Action") }}</p>
                </th>
              </tr>
            </thead>
            <tbody v-if="tags.total">
              <tr v-for="tag in tags.data" :key="tag.id">
                <td class="text-left">
                  {{ tag.title }}
                </td>
                <td class="text-left">
                  {{ tag.slug }}
                </td>
                <td>
                  <p class="text-center">{{ tag.post_categories_count ?? 0 }}</p>
                </td>
                <td class="text-left">
                  <span
                    class="badge"
                    :class="[tag.status == 1 ? 'badge-primary' : 'badge-danger']"
                  >
                    {{ tag.status == 1 ? trans("Active") : trans("Draft") }}
                  </span>
                </td>
                <td>
                  {{ moment(tag.created_at).format("D-MMM-Y") }}
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
                            <button @click="openEditTagDrawer(tag)" class="dropdown-link">
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
                              @click="deleteRow('/admin/tag/' + tag.id)"
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
      </div>
    </div>
  </main>

  <div id="addNewTagDrawer" class="drawer drawer-right">
    <form @submit.prevent="storeTag()">
      <div class="drawer-header">
        <h5>{{ trans("Add New Tag") }}</h5>
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
            v-model="tagForm.title"
            type="text"
            name="title"
            class="input"
            required=""
          />
        </div>
        <div class="mb-2">
          <label>{{ trans("Status") }}</label>
          <select v-model="tagForm.status" class="select" name="status">
            <option value="1">{{ trans("Active") }}</option>
            <option value="0">{{ trans("Deactive") }}</option>
          </select>
        </div>
        <div class="mb-2">
          <label>{{ trans("Language") }}</label>
          <select v-model="tagForm.language" class="select" name="language">
            <template v-for="(language, key) in languages" :key="key">
              <option :value="key">{{ language }}</option>
            </template>
          </select>
        </div>
      </div>
      <div class="drawer-footer">
        <div class="flex justify-between gap-2">
          <button type="button" class="w-full btn btn-secondary" data-dismiss="drawer">
            <span> {{ trans("Close") }}</span>
          </button>
          <SpinnerBtn
            classes="btn btn-primary w-full"
            :processing="tagForm.processing"
            :btn-text="trans('Create')"
          />
        </div>
      </div>
    </form>
  </div>

  <div id="editTagDrawer" class="drawer drawer-right">
    <form @submit.prevent="updateTag()">
      <div class="drawer-header">
        <h5>{{ trans("Edit Tag") }}</h5>
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
            id="title"
            class="input"
            required
          />
        </div>

        <div class="mb-2">
          <label>{{ trans("Status") }}</label>
          <select v-model="editForm.status" class="select" name="status" id="status">
            <option value="1">{{ trans("Active") }}</option>
            <option value="0">{{ trans("Deactive") }}</option>
          </select>
        </div>
        <div class="mb-2">
          <label>{{ trans("Language") }}</label>
          <select v-model="editForm.lang" class="select" name="language" id="language">
            <template v-for="(language, key) in languages" :key="key">
              <option :value="key">{{ language }}</option>
            </template>
          </select>
        </div>
      </div>
      <div class="drawer-footer">
        <div class="flex justify-between gap-2">
          <button type="button" class="w-full btn btn-secondary" data-dismiss="drawer">
            <span> {{ trans("Close") }}</span>
          </button>
          <SpinnerBtn
            classes="btn btn-primary w-full"
            :processing="editForm.processing"
            :btn-text="trans('Update')"
          />
        </div>
      </div>
    </form>
  </div>
</template>
