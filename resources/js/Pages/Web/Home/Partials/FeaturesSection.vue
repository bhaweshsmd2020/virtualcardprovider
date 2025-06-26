<script lang="ts" setup>
const props = defineProps({
  data: {
    type: Object,
    default: {}
  }
})
</script>

<template>
  <div class="block-feature-six position-relative lg-pt-80 lg-pb-50 pb-50 pt-50">
    <div class="container">
      <div class="row gx-lg-5">
        <div class="col-lg-5 wow fadeInLeft mt-5">
          <img src="/assets/images/Apple Pay.jpg" alt="" class="lazy-img w-100 md-mt-20 mb-30 md-mb-30 mt-10" />
          <h2 class="text-dark fs-1 fw-bold mb-30 mt-4 text-left">{{ data.title }}</h2>

          <!-- /.title-one -->
          <p class="text-dark md-mt-20 mb-30 md-mb-30 mt-30">{{ data.sub_title }}</p>
          <Link :href="data.btn_link" class="btn-ten d-inline-flex align-items-center rounded">
            <span class="text">{{ data.btn_text }}</span>
            <div class="icon tran3s rounded-circle d-flex align-items-center ms-2">
              <img src="/assets/images/icon/icon_27.svg" alt="" class="lazy-img w-75" />
            </div>
          </Link>
        </div>
        <div class="col-lg-7 md-mt-50">
          <div class="row">
            <div class="col-md-6 d-flex wow fadeInUp pt-15 pb-10">
              <div class="card-style-eight position-relative vstack tran3s w-100">
                <div class="icon d-flex align-items-center">
                  <img :src="data.cards?.[0]?.icon" alt="" class="lazy-img" />
                </div>
                <h4 class="fw-bold mb-15 mt-20">{{ data.cards?.[0]?.title }}</h4>
                <p>{{ data.cards?.[0]?.description }}</p>
                <Link :href="data.cards?.[0]?.link" class="stretched-link"> </Link>
              </div>
            </div>
            <div class="col-md-6 d-flex wow fadeInUp pt-15 pb-10" data-wow-delay="0.1s">
              <div class="card-style-eight position-relative vstack tran3s w-100 active">
                <div class="icon d-flex align-items-center">
                  <img :src="data.cards?.[1]?.icon" alt="" class="lazy-img" />
                </div>
                <h4 class="fw-bold mb-15 mt-20">{{ data.cards?.[1]?.title }}</h4>
                <p>{{ data.cards?.[1]?.description }}</p>
                <Link :href="data.cards?.[1]?.link" class="stretched-link"> </Link>
              </div>
            </div>
            <div class="col-md-6 d-flex wow fadeInUp pt-15 pb-10" data-wow-delay="0.2s">
              <div class="card-style-eight position-relative vstack tran3s w-100">
                <div class="icon d-flex align-items-center">
                  <img :src="data.cards?.[2]?.icon" alt="" class="lazy-img" />
                </div>
                <h4 class="fw-bold mb-15 mt-20">{{ data.cards?.[2]?.title }}</h4>
                <p>{{ data.cards?.[2]?.description }}</p>
                <Link :href="data.cards?.[2]?.link" class="stretched-link"> </Link>
              </div>
              <!-- /.card-style-eight -->
            </div>
            <div class="col-md-6 d-flex wow fadeInUp pt-15 pb-10" data-wow-delay="0.3s">
              <div class="card-style-eight position-relative vstack tran3s w-100">
                <div class="icon d-flex align-items-center">
                  <img :src="data.cards?.[3]?.icon" alt="" class="lazy-img" />
                </div>
                <h4 class="fw-bold mb-15 mt-20">{{ data.cards?.[3]?.title }}</h4>
                <p>{{ data.cards?.[3]?.description }}</p>
                <Link :href="data.cards?.[3]?.link" class="stretched-link"> </Link>
              </div>
              <!-- /.card-style-eight -->
            </div>
          </div>
        </div>
      </div>
    </div>
    <img :src="data.shape_1" alt="" class="lazy-img shapes shape_01" />
    <img :src="data.shape_2" alt="" class="lazy-img shapes shape_02" />
  </div>
</template>
