<script setup>
import { onBeforeMount } from 'vue'
import ImageInput from '@/Components/Admin/ImageInput.vue'
import TextInput from '@/Components/Admin/TextInput.vue'
import SpinnerBtn from '@/Components/SpinnerBtn.vue'
import trans from '@/Composables/transComposable'
import { useOptionUpdateStore } from '@/Store/Admin/optionUpdate'

const props = defineProps(["data"])
const form = useOptionUpdateStore()

//Debug: Log props.data
console.log('Admin PageSetting props.data:', JSON.parse(JSON.stringify(props.data)));

const addBusinessPaymentCard = () => {
  if (!props.data.business_payments.cards) {
    props.data.business_payments.cards = [];
  }
  props.data.business_payments.cards.push({
    image: "",
    title: "",
    description: "",
    badges_string: ""
  });
  console.log('After adding card:', JSON.parse(JSON.stringify(props.data.business_payments.cards)));
}

const removeBusinessPaymentCard = (index) => {
  if (props.data.business_payments.cards?.length > 1) {
    props.data.business_payments.cards.splice(index, 1);
    console.log('After removing card:', JSON.parse(JSON.stringify(props.data.business_payments.cards)));
  }
}

const updateBadges = (index) => {
  const badgesString = props.data.business_payments.cards[index]?.badges_string || '';
  props.data.business_payments.cards[index].badges = badgesString
    ? badgesString.split(',').map(badge => badge.trim()).filter(badge => badge.length > 0)
    : [];
}

onBeforeMount(() => {
  let properties = [
    "hero",
    "features",
    "reviewer",
    "video_cta",
    "card_cta",
    "testimonial",
    "app_cta",
    "blog",
    "business_payments",
  ]

  properties.forEach((key) => (props.data[key] = props.data[key] || {}))

  if (!props.data.features.cards) {
    props.data.features.cards = [
      {
        icon: "",
        title: "",
        description: "",
        link: "",
      },
      {
        icon: "",
        title: "",
        description: "",
        link: "",
      },
      {
        icon: "",
        title: "",
        description: "",
        link: "",
      },
      {
        icon: "",
        title: "",
        description: "",
        link: "",
      },
    ]
  }
  if (!props.data.reviewer.counters) {
    props.data.reviewer.counters = [
      {
        number: "",
        suffix: "",
        description: "",
      },
      {
        number: "",
        suffix: "",
        description: "",
      },
      {
        number: "",
        suffix: "",
        description: "",
      },
      {
        number: "",
        suffix: "",
        description: "",
      },
    ]
  }
  if (!props.data.card_cta.card_items) {
    props.data.card_cta.card_items = [
      {
        icon: "",
        title: "",
        description: "",
        img: "",
      },
      {
        icon: "",
        title: "",
        description: "",
        img: "",
      },
      {
        icon: "",
        title: "",
        description: "",
        img: "",
      }
    ]
  }
  properties.forEach((key) => {
    if (!props.data[key]) {
      props.data[key] = {};
      console.log(`Initialized ${key} section`);
    }
  });

  // Initialize business_payments
  if (!props.data.business_payments) {
    props.data.business_payments = {
      title: 'Stay on top of every business payment',
      subtitle: '',
      cards: []
    };
    console.log('Initialized business_payments');
  }

  // Initialize business_payments.cards
  if (!Array.isArray(props.data.business_payments.cards)) {
    props.data.business_payments.cards = [
      { image: "", title: "", description: "", badges_string: "" },
      { image: "", title: "", description: "", badges_string: "" },
      { image: "", title: "", description: "", badges_string: "" },
      { image: "", title: "", description: "", badges_string: "" },
      { image: "", title: "", description: "", badges_string: "" },
      { image: "", title: "", description: "", badges_string: "" },
      { image: "", title: "", description: "", badges_string: "" },
      { image: "", title: "", description: "", badges_string: "" }
    ];
    console.log('Initialized business_payments.cards with 8 cards');
  } else {
    props.data.business_payments.cards.forEach((card, index) => {
      card.image = typeof card.image === 'string' ? card.image : '';
      card.title = typeof card.title === 'string' ? card.title : '';
      card.description = typeof card.description === 'string' ? card.description : '';
      card.badges_string = typeof card.badges_string === 'string' ? card.badges_string : '';
      console.log(`Sanitized card ${index}:`, JSON.parse(JSON.stringify(card)));
    });
  }
})
</script>

<template>
  <form @submit.prevent="form.submit('home_page', data)" >

    <h6>{{ trans("Hero Section") }}</h6>
    <div class="p-3 mt-2 mb-10 border rounded dark:border-gray-600 space-y-3">
      <TextInput :label="trans('Title')" v-model="data.hero.title" />
      <TextInput :label="trans('Sub Title')" v-model="data.hero.sub_title" />

      <div class="flex gap-1">
        <TextInput :label="trans('Button Text')" v-model="data.hero.btn_text" />
        <TextInput :label="trans('Button Link')" v-model="data.hero.btn_link" />
      </div>

      <ImageInput :label="trans('Bg Image')" v-model="data.hero.img_1" />
      <ImageInput :label="trans('Center Image')" v-model="data.hero.img_2" />

      <ImageInput :label="trans('Right Image')" v-model="data.hero.img_3" />
      <TextInput :label="trans('Right Image Action Link')" v-model="data.hero.img_3_action_link" />
      <TextInput :label="trans('Right Subtitle')" v-model="data.hero.right_sub_title" />

      <ImageInput :label="trans('Right Image 2')" v-model="data.hero.img_4" />
      <TextInput :label="trans('Counter Number')" v-model="data.hero.counter_number" />
      <TextInput :label="trans('Counter Suffix')" v-model="data.hero.counter_suffix" />
      <TextInput :label="trans('Counter Subtitle')" v-model="data.hero.counter_sub_title" />
    </div>

    <h6>{{ trans("Features Section") }}</h6>
    <div class="p-3 mt-2 mb-10 border rounded dark:border-gray-600 space-y-3">
      <TextInput :label="trans('Title')" v-model="data.features.title" />
      <TextInput :label="trans('Sub Title')" v-model="data.features.sub_title" />

      <TextInput :label="trans('Button Text')" v-model="data.features.btn_text" />
      <TextInput :label="trans('Button Link')" v-model="data.features.btn_link" />

      <div class="mb-2">
        <label class="mr-2">{{ trans("Cards") }}</label>
        <div class="flex items-center p-2 mb-2 border gap-x-2 dark:border-gray-600"
          v-for="(item, index) in data.features.cards" :key="index">
          <span class="p-2 py-1 text-center text-white bg-indigo-600 rounded-full">{{
            index + 1
            }}</span>
          <div class="flex flex-grow flex-col gap-2">
            <ImageInput label="" class="w-full" v-model="item.icon" />
            <input type="text" class="input" placeholder="title" v-model="item.title" />
            <input type="text" class="input" placeholder="description" v-model="item.description" />
            <input type="text" class="input" placeholder="link" v-model="item.link" />
          </div>
        </div>
      </div>
      <div class="flex gap-2">
        <ImageInput :label="trans('Shape 1')" v-model="data.features.shape_1" />
        <ImageInput :label="trans('Shape 2')" v-model="data.features.shape_2" />
      </div>
    </div>

    <h6>{{ trans("About Section") }}</h6>
    <div class="p-3 mt-2 mb-10 border rounded dark:border-gray-600 space-y-3">
      <TextInput :label="trans('Top Title')" v-model="data.reviewer.top_title" />
      <TextInput :label="trans('Title')" v-model="data.reviewer.title" />
      <TextInput :label="trans('Sub Title')" v-model="data.reviewer.sub_title" />
      <TextInput :label="trans('Button Text')" v-model="data.reviewer.btn_text" />
      <TextInput :label="trans('Button Link')" v-model="data.reviewer.btn_link" />
      <ImageInput :label="trans('Shape 1')" v-model="data.reviewer.shape_1" />

      <div class="grid grid-cols-2 gap-2">
        <div class="space-y-2">
          <ImageInput :label="trans('Section 1 Image')" v-model="data.reviewer.section_1_img" />
          <TextInput :label="trans('Section 1 Title')" v-model="data.reviewer.section_1_title" />
          <TextInput :label="trans('Section 1 Sub Title')" v-model="data.reviewer.section_1_sub_title" />
        </div>
        <div class="space-y-2">
          <ImageInput :label="trans('Section 2 Image')" v-model="data.reviewer.section_2_img" />
          <TextInput :label="trans('Section 2 Title')" v-model="data.reviewer.section_2_title" />
          <TextInput :label="trans('Section 2 Sub Title')" v-model="data.reviewer.section_2_sub_title" />
        </div>
      </div>
      <hr>

      <div class="mb-2">
        <label class="mr-2">{{ trans("Counter") }}</label>
        <div class="flex items-center p-2 mb-2 border gap-x-2 dark:border-gray-600 rounded-lg"
          v-for="(counter, index) in data.reviewer.counters" :key="index">
          <span class="p-2 py-1 text-center text-white bg-indigo-600 rounded-full">{{
            index + 1
            }}</span>
          <div class="grid grid-cols-2 gap-2 w-full">
            <TextInput :label="trans('Counter Number')" v-model="counter.number" />
            <TextInput :label="trans('Counter Suffix')" v-model="counter.suffix" />
            <TextInput class="col-span-2" :label="trans('Description')" v-model="counter.description" />
          </div>
        </div>
      </div>

      <div class="grid grid-cols-2 w-full gap-2">
        <ImageInput :label="trans('Shape 1')" v-model="data.reviewer.shape_1" />
        <ImageInput :label="trans('Shape 2')" v-model="data.reviewer.shape_2" />
      </div>
    </div>

    <h6>{{ trans("Video CTA Section") }}</h6>
    <div class="p-3 mt-2 mb-10 border rounded dark:border-gray-600 space-y-3">
      <ImageInput :label="trans('Left Image')" v-model="data.video_cta.img_1" />
      <ImageInput :label="trans('Video Button Image')" v-model="data.video_cta.img_2" />
      <TextInput :label="trans('Video Link')" v-model="data.video_cta.link" />
      <ImageInput :label="trans('Left Bottom Floating Image')" v-model="data.video_cta.img_3" />

      <TextInput :label="trans('Right Title')" v-model="data.video_cta.right_title" />
      <TextInput :label="trans('Right Sub Title')" v-model="data.video_cta.right_sub_title" />

      <TextInput :label="trans('Right Items')" v-model="data.video_cta.right_items" />
      <small>{{ trans("separate by comma (|)") }}</small>

      <TextInput :label="trans('Button Text')" v-model="data.video_cta.btn_text" />
      <TextInput :label="trans('Button Link')" v-model="data.video_cta.btn_link" />
    </div>

    <h6>{{ trans("Card CTA Section") }}</h6>
    <div class="p-3 mt-2 mb-10 border rounded dark:border-gray-600 space-y-3">
      <TextInput :label="trans('Title')" v-model="data.card_cta.title" />

      <div class="mb-2">
        <label class="mr-2">{{ trans("Card Items") }}</label>
        <div class="flex items-center p-2 mb-2 border gap-x-2 dark:border-gray-600"
          v-for="(item, index) in data.card_cta.card_items" :key="index">
          <span class="p-2 py-1 text-center text-white bg-indigo-600 rounded-full">{{
            index + 1
            }}</span>
          <div class="grid grid-cols-3 gap-2 items-center justify-between flex-grow gap-x-1">
            <ImageInput label="Icon" class="w-full" v-model="item.icon" />
            <ImageInput label="Image" class="w-full" v-model="item.img" />
            <TextInput :label="trans('Title')" v-model="item.title" />
            <TextInput :label="trans('Description')" class="col-span-3" v-model="item.description" />
          </div>
        </div>
      </div>
      <ImageInput :label="trans('Shape 1')" v-model="data.card_cta.shape_1" />
    </div>

    <!-- ✅ Enhanced Business Payments Section (with badge display) -->
    <h6>{{ trans("Business Payments Section") }}</h6>
    <div class="p-3 mt-2 mb-10 border rounded dark:border-gray-600 space-y-3">
      <TextInput :label="trans('Title')" v-model="data.business_payments.title" />

      <div class="mb-2">
        <label class="mr-2">{{ trans("Cards") }}</label>

        <div
          class="flex items-start p-2 mb-2 border gap-x-2 dark:border-gray-600 rounded-lg relative"
          v-for="(item, index) in data.business_payments.cards"
          :key="index"
        >
          <button
            v-if="data.business_payments.cards.length > 1"
            @click="removeBusinessPaymentCard(index)"
            type="button"
            class="absolute top-2 right-2 text-red-500 hover:text-red-700"
            title="Remove card"
          >
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
              <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd" />
            </svg>
          </button>

          <span class="p-2 py-1 text-center text-white bg-indigo-600 rounded-full mt-3">{{ index + 1 }}</span>

          <div class="grid grid-cols-2 gap-2 w-full">
            <ImageInput :label="trans('Image')" v-model="item.image" />
            <TextInput :label="trans('Title')" v-model="item.title" />
            <TextInput
              :label="trans('Description')"
              class="col-span-2"
              v-model="item.description"
              type="textarea"
              rows="3"
            />
            <TextInput
              :label="trans('Badges (comma separated)')"
              class="col-span-2"
              v-model="item.badges_string"
              @input="updateBadges(index)"
            />

            <!-- ✅ Display badges from string -->
            <div class="col-span-2 flex flex-wrap gap-2 mt-1">
              <span
                v-for="(badge, bIndex) in item.badges"
                :key="bIndex"
                class="px-2 py-1 text-sm rounded bg-indigo-100 text-indigo-700 dark:bg-indigo-800 dark:text-white"
              >
                {{ badge }}
              </span>
            </div>
          </div>
        </div>

        <button
          type="button"
          @click="addBusinessPaymentCard"
          class="btn btn-outline-primary mt-2 flex items-center gap-1"
        >
          <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
            <path fill-rule="evenodd" d="M10 5a1 1 0 011 1v3h3a1 1 0 110 2h-3v3a1 1 0 11-2 0v-3H6a1 1 0 110-2h3V6a1 1 0 011-1z" clip-rule="evenodd" />
          </svg>
          {{ trans('Add New Card') }}
        </button>
      </div>
    </div>

    <h6>{{ trans("Testimonial Section") }}</h6>
    <div class="p-3 mt-2 mb-10 border rounded dark:border-gray-600 space-y-3">
      <TextInput :label="trans('Title')" v-model="data.testimonial.title" />
      <div class="grid grid-cols-2 w-full gap-2">
        <ImageInput :label="trans('Shape 1')" v-model="data.testimonial.shape_1" />
        <ImageInput :label="trans('Shape 2')" v-model="data.testimonial.shape_2" />
      </div>
      <TextInput :label="trans('Bottom Text')" v-model="data.testimonial.sub_title" />
    </div>

    <h6>{{ trans("App CTA Section") }}</h6>
    <div class="p-3 mt-2 mb-10 border rounded dark:border-gray-600 space-y-3">
      <ImageInput :label="trans('Left Image')" v-model="data.app_cta.img_1" />
      <TextInput :label="trans('Title')" v-model="data.app_cta.title" />
      <TextInput :label="trans('Sub Title')" v-model="data.app_cta.sub_title" />
      <TextInput :label="trans('List Items')" v-model="data.app_cta.list_items" />
      <small>Separate by |</small>
      <div class="grid grid-cols-2 w-full gap-2">
        <ImageInput :label="trans('Button 1 Image')" v-model="data.app_cta.btn_1_img" />
        <TextInput :label="trans('Button 1 Link')" v-model="data.app_cta.btn_1_link" />
        <TextInput :label="trans('Button 1 Text')" v-model="data.app_cta.btn_1_text" />
        <TextInput :label="trans('Button 1 Sub Text')" v-model="data.app_cta.btn_1_sub_text" />
      </div>
      <div class="grid grid-cols-2 w-full gap-2">
        <ImageInput :label="trans('Button 2 Image')" v-model="data.app_cta.btn_2_img" />
        <TextInput :label="trans('Button 2 Link')" v-model="data.app_cta.btn_2_link" />
        <TextInput :label="trans('Button 2 Text')" v-model="data.app_cta.btn_2_text" />
        <TextInput :label="trans('Button 2 Sub Text')" v-model="data.app_cta.btn_2_sub_text" />
      </div>
    </div>
    
    <h6>{{ trans("Blog Section") }}</h6>
    <div class="p-3 mt-2 mb-10 border rounded dark:border-gray-600 space-y-3">
      <TextInput :label="trans('Title')" v-model="data.blog.title" />
      <TextInput :label="trans('Sub Title')" v-model="data.blog.sub_title" />
    </div>

    <div class="mb-2">
      <SpinnerBtn :processing="form.processing" :btn-text="trans('Save Changes')" />
    </div>
  </form>
</template>

<style>
/* Your existing styles remain unchanged */
.agency {
  line-height: 20px; 
  font-size: 16px; 
  font-weight: 500; 
  margin-top: 10px;
  min-height: 60px;
  height: 60px;
}

.badge {
  width: fit-content;
}

@media (min-width: 769px) {
.flex-view {
  display: flex;
  justify-content: center;
}
}
@media (max-width: 768px) {
.flex-view {
  display: flex;
  justify-content: center;
}
}

/* Additional styles for business payments section */
.text-bg-light {
  color: #212529;
  background-color: #f8f9fa;
}

.absolute {
  position: absolute;
}

.top-2 {
  top: 0.5rem;
}

.right-2 {
  right: 0.5rem;
}

.h-5 {
  height: 1.25rem;
}

.w-5 {
  width: 1.25rem;
}

.h-4 {
  height: 1rem;
}

.w-4 {
  width: 1rem;
}

.flex {
  display: flex;
}

.items-center {
  align-items: center;
}

.gap-1 {
  gap: 0.25rem;
}
</style>