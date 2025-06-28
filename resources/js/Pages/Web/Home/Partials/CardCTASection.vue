<script setup>
import { ref } from 'vue'

defineProps({
  data: {
    type: Object,
    default: {
      card_items: [
        {
          title: 'Mutual Funds.',
          description:
            'Mutual funds enable collective investment, managed by professionals for potential growth.',
          img: '\/demo\/uSKZ6xbzkKDfRcJXjYXe.svg',
          icon: '\/demo\/FZLckfrJXzgjzotoL0bb.svg'
        },
        {
          title: 'Pension Scheme.',
          description:
            'Pension schemes ensure financial security during retirement years for eligible individuals. Retirement pensions provide financial security for qualifying individuals.',
          img: '\/demo\/YGD3go3KvMDU15aV7O4C.svg',
          icon: '\/demo\/AgQ6lo6wwIOn79DQ9brn.svg'
        },
        {
          title: 'International Multi-Currency Visa & Master Card.',
          description:
            'International banking provides global financial services, including cross-border transactions, currency exchange, and offshore investments.',
          img: '\/demo\/kqFBeg2usqfYL3Cj9Vnh.svg',
          icon: '\/demo\/sYCzEqKvc8GtfybWEDPb.svg'
        }
      ],
      title: "We're here to help you build, manage & protect your cards.",
      shape_1: '\/demo\/a3WjSolByGqYF6mUpPEl.svg'
    }
  }
})
</script>

<template>
  <div class="block-feature-seven position-relative lg-mt-80">
    <div class="container mt-30">
      <div class="position-relative">
        <div class="row">
          <div class="col-xl-7 wow fadeInLeft">
            <div class="pe-xxl-5 mb-20 lg-mb-30">
              <h2 class="text-dark fs-1 fw-bold">{{ data.title }}</h2>
            </div>
          </div>
        </div>

        <div class="row">          
          <div class="col-12 d-flex wow fadeInUp" data-wow-delay="0.2s">
            <div class="card-style-nine vstack tran3s w-100 mt-20">
              <div class="row align-items-center">
                <div class="col-lg-5">
                  <img :src="data.card_items?.[2]?.img" alt="" class="lazy-img w-100 mangecard" />
                </div>
                <div class="col-lg-7 d-flex flex-column justify-content-center" style="min-height: 100%;">
                  <div class="d-flex justify-content-between align-items-center">
                    <div class="icon tran3s rounded-circle d-flex align-items-center justify-content-center order-last">
                      <img :src="data.card_items?.[2]?.icon" alt="" class="lazy-img" />
                    </div>
                    <h2 class="text-dark fs-1 fw-bold w-75 m0">{{ data.card_items?.[2]?.title }}</h2>
                  </div>
                  <p class="mt-35 lg-mt-30 mb-40 me-5">{{ data.card_items?.[2]?.description }}</p>
                </div>                
              </div>
            </div>
          </div>
        </div>
        <img :src="data.shape_1" alt="" class="lazy-img shapes shape_01" />
      </div>
    </div>
  </div>
</template>

<style>
.card-style-nine{
  padding: 50px 55px;
}

.mangecard{
  padding-bottom: 40px;
  background: #fff;
  border-radius: 30px;
}
</style>