<script setup>
import ImageInput from "@/Components/Admin/ImageInput.vue"
import TextInput from "@/Components/Admin/TextInput.vue"
import SpinnerBtn from "@/Components/SpinnerBtn.vue"
import { useOptionUpdateStore } from "@/Store/Admin/optionUpdate"
import { onBeforeMount } from "vue"
const props = defineProps(["data"])
const form = useOptionUpdateStore()

onBeforeMount(() => {
  let properties = ["header", "feature", "feature2", "feature3", "feature4"]
  properties.forEach((key) => (props.data[key] = props.data[key] || {}))

  if (!props.data.feature2?.items) {
    props.data.feature2.items = [
      {
        icon: "",
        title: "",
        sub_title: "",
      },
      {
        icon: "",
        title: "",
        sub_title: "",
      },
      {
        icon: "",
        title: "",
        sub_title: "",
      },
    ]
  }
  if (!props.data.feature3?.items) {
    props.data.feature3.items = [
      {
        icon: "",
        title: "",
        sub_title: "",
      },
      {
        icon: "",
        title: "",
        sub_title: "",
      },
      {
        icon: "",
        title: "",
        sub_title: "",
      },
    ]
  }
})
</script>

<template>
  <form
    method="POST"
    @submit.prevent="form.submit('about_page', data)"
    enctype="multipart/form-data"
  >
    <h6>{{ trans("Page Header") }}</h6>
    <div class="mb-10 mt-2 rounded border p-3 dark:border-gray-600">
      <TextInput :label="trans('Page Title')" v-model="data.header.title" />
      <TextInput :label="trans('Page Sub Title')" v-model="data.header.sub_title" />

      <ImageInput
        :label="trans('Left Image')"
        v-model="data.header.left_image"
        class="mb-2"
      />
      <ImageInput
        :label="trans('Left Shape')"
        v-model="data.header.left_shape"
        class="mb-2"
      />

      <ImageInput
        :label="trans('Right Image')"
        v-model="data.header.right_image"
        class="mb-2"
      />
      <ImageInput
        :label="trans('Right Shape')"
        v-model="data.header.right_shape"
        class="mb-2"
      />
    </div>

    <h6>{{ trans("Feature Section") }}</h6>
    <div class="mb-10 mt-2 rounded border p-3 dark:border-gray-600">
      <ImageInput :label="trans('Bg Image')" v-model="data.feature.bg_img" class="mb-2" />
      <ImageInput
        :label="trans('Top Overflow Image')"
        v-model="data.feature.top_overflow_img"
        class="mb-2"
      />
      <ImageInput
        :label="trans('Bottom Overflow Image')"
        v-model="data.feature.bottom_overflow_img"
        class="mb-2"
      />
      <TextInput :label="trans('Title')" v-model="data.feature.title" />
      <TextInput :label="trans('Sub Title')" v-model="data.feature.sub_title" />
      <TextInput :label="trans('Features')" v-model="data.feature.list" />
      <small>Note: Separate by | </small>

      <TextInput
        :label="trans('Counter 1 Title')"
        v-model="data.feature.counter1_title"
      />
      <TextInput :label="trans('Counter 1 Text')" v-model="data.feature.counter1_text" />
      <TextInput
        :label="trans('Counter 2 Title')"
        v-model="data.feature.counter2_title"
      />
      <TextInput :label="trans('Counter 2 Text')" v-model="data.feature.counter2_text" />
    </div>

    <h6>{{ trans("Feature Section 2") }}</h6>
    <div class="mb-10 mt-2 rounded border p-3 dark:border-gray-600">
      <ImageInput :label="trans('Shape')" v-model="data.feature2.shape" class="mb-2" />
      <ImageInput :label="trans('Shape 2')" v-model="data.feature2.shape2" class="mb-2" />
      <TextInput :label="trans('Title')" v-model="data.feature2.title" />
      <TextInput :label="trans('Sub Title')" v-model="data.feature2.sub_title" />

      <div v-for="item in data.feature2?.items ?? []" class="flex gap-2 flex-grow">
        <ImageInput :label="trans('Icon')" v-model="item.icon" class="mb-2" />
        <TextInput :label="trans('Title')" v-model="item.title" />
        <TextInput :label="trans('Sub Title')" v-model="item.sub_title" />
      </div>
    </div>

    <h6>{{ trans("Feature Section 3") }}</h6>
    <div class="mb-10 mt-2 rounded border p-3 dark:border-gray-600">
      <ImageInput :label="trans('Shape')" v-model="data.feature3.shape" class="mb-2" />
      <TextInput :label="trans('Title')" v-model="data.feature3.title" />

      <div v-for="item in data.feature3?.items ?? []" class="flex gap-2 flex-grow">
        <ImageInput :label="trans('Icon')" v-model="item.icon" class="mb-2" />
        <TextInput :label="trans('Title')" v-model="item.title" />
        <TextInput :label="trans('Sub Title')" v-model="item.sub_title" />
        <TextInput :label="trans('Link')" v-model="item.link" />
      </div>
    </div>

    <h6>{{ trans("Feature Section 4") }}</h6>
    <div class="mb-10 mt-2 rounded border p-3 dark:border-gray-600">
      <ImageInput
        :label="trans('BG Image')"
        v-model="data.feature4.bg_img"
        class="mb-2"
      />
      <TextInput :label="trans('Title')" v-model="data.feature4.title" />
      <TextInput :label="trans('Sub Title')" v-model="data.feature4.sub_title" />
      <ImageInput :label="trans('Button Img')" v-model="data.feature4.btn_img" />
      <TextInput :label="trans('Video Link')" v-model="data.feature4.btn_link" />
    </div>

    <div class="mb-2">
      <SpinnerBtn :processing="form.processing" :btn-text="trans('Save Changes')" />
    </div>
  </form>
</template>
