<script setup>
import SpinnerBtn from "@/Components/SpinnerBtn.vue"
import ImageInput from "@/Components/Admin/ImageInput.vue"
import TextInput from "@/Components/Admin/TextInput.vue"

import { useOptionUpdateStore } from "@/Store/Admin/optionUpdate"
import { onBeforeMount } from "vue"
import trans from "@/Composables/transComposable"
const props = defineProps(["data"])
const form = useOptionUpdateStore()

onBeforeMount(() => {
  let properties = ["hero", "form", "office", "map"]

  properties.forEach((key) => (props.data[key] = props.data[key] || {}))

  if (!props.data.office.items) {
    props.data.office.items = [
      {
        title: "",
        sub_title: "",
        icon: "",
      },
      {
        title: "",
        sub_title: "",
        icon: "",
      },
      {
        title: "",
        sub_title: "",
        icon: "",
      },
    ]
  }
})
</script>

<template>
  <form method="POST" @submit.prevent="form.submit('contact_page', data)" enctype="multipart/form-data">
    <h6>{{ trans("Header section") }}</h6>

    <div class="mb-10 mt-2 rounded border p-3 dark:border-gray-600">
      <div class="mb-2">
        <label>{{ trans("Page Title") }}</label>
        <input type="text" class="input" v-model="data.hero.title" />
      </div>
      <div class="mb-2">
        <label>{{ trans("Sub Title") }}</label>
        <input type="text" class="input" v-model="data.hero.sub_title" />
      </div>
    </div>
    <h6>{{ trans("Office/Location section") }}</h6>

    <div class="mb-10 mt-2 rounded border p-3 dark:border-gray-600">
      <div v-for="item in data.office.items ?? []" class="flex gap-2">
        <ImageInput :label="trans('Icon')" v-model="item.icon" class="mb-2" />

        <div class="">
          <label>{{ trans("Title") }}</label>
          <input type="text" class="input mt-1" v-model="item.title" />
        </div>
        <div class="">
          <label>{{ trans("Sub Title") }}</label>
          <input type="text" class="input mt-1" v-model="item.sub_title" />
        </div>
      </div>
    </div>

    <h6>{{ trans("Contact form left section") }}</h6>
    <div class="mb-10 mt-2 rounded border p-3 dark:border-gray-600">
      <div class="mb-2">
        <label>{{ trans("Title") }}</label>
        <input type="text" class="input" v-model="data.form.title" />
      </div>
      <ImageInput :label="trans('Image')" v-model="data.form.img" class="mb-2" />
    </div>

    <h6>{{ trans("Google Map") }}</h6>
    <div class="mb-10 mt-2 rounded border p-3 dark:border-gray-600">
      <div class="mb-2">
        <label>{{ trans("Google Address") }}</label>
        <input type="text" class="input" v-model="data.map.address" placeholder="Dhaka, Bangladesh">
      </div>
    </div>

    <div class="mb-2">
      <SpinnerBtn :processing="form.processing" :btn-text="trans('Save Changes')" />
    </div>
  </form>
</template>
