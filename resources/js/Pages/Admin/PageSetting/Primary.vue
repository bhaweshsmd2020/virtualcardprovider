<script setup>
import ImageInput from "@/Components/Admin/ImageInput.vue"
import TextInput from "@/Components/Admin/TextInput.vue"
import SpinnerBtn from "@/Components/SpinnerBtn.vue"

import { useOptionUpdateStore } from "@/Store/Admin/optionUpdate"
import { onBeforeMount, ref } from "vue"
const props = defineProps(["data"])
const form = ref({ ...props.data })
const store = useOptionUpdateStore()

onBeforeMount(() => {
  let properties = ["banner3", "socials", "slider"]
  properties.forEach((key) => (form.value[key] = form.value[key] || {}))
})
</script>

<template>
  <form
    method="POST"
    @submit.prevent="store.submit('primary_data', form)"
    enctype="multipart/form-data"
  >
    <h6>{{ trans("Primary Settings") }}</h6>
    <div class="mb-10 mt-2 rounded border p-3 dark:border-gray-600">
      <TextInput
        :label="trans('Top bar pre text')"
        v-model="form.top_pre_text"
        class="mb-2"
      />
      <TextInput
        :label="trans('Top bar welcome text')"
        v-model="form.top_text"
        class="mb-2"
      />
      <ImageInput :label="trans('Light Logo')" v-model="form.logo" class="mb-2" />
      <ImageInput :label="trans('Deep logo')" v-model="form.deep_logo" class="mb-2" />
      <ImageInput :label="trans('Favicon')" v-model="form.favicon" class="mb-2" />

      <div class="mb-2">
        <label>{{ trans("Footer Logo After text") }}</label>
        <input type="text" class="input" v-model="form.slogan" />
      </div>

      <div class="mb-2">
        <label>{{ trans("Footer Copyright text") }}</label>
        <input type="text" class="input" v-model="form.copyright_text" />
      </div>

      <div class="mb-2">
        <label>{{ trans("Email address") }}</label>
        <input type="email" v-model="form.contact_email" class="input" />
      </div>
      <div class="mb-2">
        <label>{{ trans("Contact Phone") }}</label>
        <input type="text" class="input" v-model="form.contact_phone" />
      </div>
    </div>

    <h6>{{ trans("Social Networks") }}</h6>
    <div class="mb-10 mt-2 rounded border p-3 dark:border-gray-600">
      <div class="mb-2">
        <label>{{ trans("Facebook Profile Link") }}</label>
        <input
          type="url"
          name="socials[facebook]"
          class="input"
          v-model="form.socials.facebook"
        />
      </div>
      <div class="mb-2">
        <label>{{ trans("Twitter Profile Link") }}</label>
        <input
          type="url"
          name="socials[twitter]"
          class="input"
          v-model="form.socials.twitter"
        />
      </div>
      <div class="mb-2">
        <label>{{ trans("Instagram Profile Link") }}</label>
        <input
          type="url"
          name="socials[instagram]"
          class="input"
          v-model="form.socials.instagram"
        />
      </div>
      <div class="mb-2">
        <label>{{ trans("Linkedin Profile Link") }}</label>
        <input
          type="url"
          name="socials[linkedin]"
          class="input"
          v-model="form.socials.linkedin"
        />
      </div>
    </div>

    <h6>{{ trans("Fancy Banner 3") }}</h6>
    <div class="mb-10 mt-2 rounded border p-3 dark:border-gray-600">
      <ImageInput :label="trans('BG Image')" v-model="form.banner3.bg_img" class="mb-2" />
      <TextInput :label="trans('Title')" v-model="form.banner3.title" />
      <TextInput :label="trans('Sub Title')" v-model="form.banner3.sub_title" />
      <ImageInput :label="trans('Button Img')" v-model="form.banner3.btn_img" />
      <TextInput :label="trans('Button Link')" v-model="form.banner3.btn_link" />
    </div>

    <h6>{{ trans("Testimonial Slider Section") }}</h6>
    <div class="p-3 mt-2 mb-10 border rounded dark:border-gray-600 space-y-3">
      <TextInput :label="trans('Title')" v-model="form.slider.title" />
      <TextInput :label="trans('Sub Title')" v-model="form.slider.sub_title" />
      <ImageInput :label="trans('Card Shape')" v-model="form.slider.shape" />
    </div>

    <div class="mb-2">
      <SpinnerBtn :processing="store.processing" :btn-text="trans('Save Changes')" />
    </div>
  </form>
</template>
