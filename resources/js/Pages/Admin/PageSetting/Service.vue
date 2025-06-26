<script setup>
import ImageInput from "@/Components/Admin/ImageInput.vue"
import TextInput from "@/Components/Admin/TextInput.vue"
import SpinnerBtn from "@/Components/SpinnerBtn.vue"

import { useOptionUpdateStore } from "@/Store/Admin/optionUpdate"
import { onBeforeMount } from "vue"
const props = defineProps(["data"])
const form = useOptionUpdateStore()

onBeforeMount(() => {
  let properties = ["banner", "features", "sidebar_card", "service_list"]
  properties.forEach((key) => (props.data[key] = props.data[key] || {}))

  if (!props.data.features?.items) {
    props.data.features.items = [
      {
        icon: "",
        title: "",
        description: "",
      },
    ]
  }
})

const addFeatureItem = () => {
  props.data.features.items.push({
    icon: "",
    title: "",
    description: "",
  })
}

const removeFeatureItem = (index) => {
  props.data.features.cards.splice(index, 1)
}
</script>

<template>
  <form
    method="POST"
    @submit.prevent="form.submit('service_page', data)"
    enctype="multipart/form-data"
  >
    <h6>{{ trans("Feature section") }}</h6>
    <div class="mb-10 mt-2 rounded border p-3 dark:border-gray-600">
      <ImageInput :label="trans('Shape')" v-model="data.features.shape" class="mb-2" />
      <TextInput :label="trans('Title')" v-model="data.features.title" />

      <div class="flex justify-between my-5 items-baseline">
        <p class="font-bold">{{ trans("Feature Items") }}</p>
        <button type="button" @click="addFeatureItem" class="btn btn-primary">+</button>
      </div>

      <div
        class="flex flex-col gap-2 border p-2 rounded"
        v-for="(item, index) in data.features.items"
        :key="index"
      >
        <div class="grid grid-cols-4 gap-x-2">
          <ImageInput :label="trans('Icon')" v-model="item.icon" class="col-span-1" />
          <TextInput :label="trans('Title')" v-model="item.title" class="col-span-3" />
        </div>
        <div class="flex items-end justify-between gap-2">
          <TextInput
            :label="trans('Description')"
            v-model="item.description"
            class="w-full"
          />
          <button
            type="button"
            @click="removeFeatureItem(index)"
            class="btn btn-soft-danger"
          >
            <i class="bx bx-trash"></i>
          </button>
        </div>
      </div>
    </div>

    <h6>{{ trans("Service List") }}</h6>
    <div class="mb-10 mt-2 rounded border p-3 dark:border-gray-600">
      <TextInput :label="trans('Title')" v-model="data.service_list.title" />
      <TextInput :label="trans('Sub Title')" v-model="data.service_list.sub_title" />

      <TextInput :label="trans('Button Text')" v-model="data.service_list.btn_text" />
      <TextInput :label="trans('Button Link')" v-model="data.service_list.btn_link" />
    </div>

    <h6>{{ trans("Banner Section") }}</h6>
    <div class="mb-10 mt-2 rounded border p-3 dark:border-gray-600">
      <ImageInput
        :label="trans('BG Image')"
        v-model="data.banner.banner_img"
        class="mb-2"
      />
      <TextInput :label="trans('Title')" v-model="data.banner.title" />
      <TextInput :label="trans('Sub Title')" v-model="data.banner.sub_title" />
      <TextInput :label="trans('Button Text')" v-model="data.banner.btn_text" />
      <TextInput :label="trans('Button Link')" v-model="data.banner.btn_link" />
    </div>

    <h6>{{ trans("Details Page Sidebar Card") }}</h6>
    <div class="mb-10 mt-2 rounded border p-3 dark:border-gray-600">
      <TextInput :label="trans('Title')" v-model="data.sidebar_card.title" />
      <TextInput :label="trans('Button Text')" v-model="data.sidebar_card.btn_text" />
      <TextInput :label="trans('Button Link')" v-model="data.sidebar_card.btn_link" />
    </div>

    <div class="mb-2">
      <SpinnerBtn :processing="form.processing" :btn-text="trans('Save Changes')" />
    </div>
  </form>
</template>
