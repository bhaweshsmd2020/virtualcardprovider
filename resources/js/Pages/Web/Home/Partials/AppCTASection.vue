<script setup>
defineProps({
  data: {
    type: Object,
    default: {
      title: 'Download our bank Mobile App.',
      sub_title: 'Control of all your banking needs needs anywhere, anytime',
      list_items:
        'Compare different insurance Item | Buy, store and share all your policies online | Email & Live chat',
      img_1: '\/demo\/YK9ll3KuUNkwf8s7loaz.png'
    }
  }
})
</script>

<style>
.custom-card-group {
  display: flex;
  gap: 20px;
  flex-wrap: nowrap;
  justify-content: center;
  padding-bottom: 10px;
}

.custom-card {
  background: #29594B; /* red to pink */
  border-radius: 20px;
  width: 100%;
  height: 240px;
  box-shadow: 0 10px 20px rgba(0,0,0,0.15);
}

.card-number {
  position: absolute;
  top: 0px;
  left: 0px;
  font-size: 30px;
  font-weight: bold;
  padding: 5px 10px;
  color: #29594B;
  background: #fff;
  border-top-left-radius: 20px;
  border-bottom-right-radius: 20px;
}
</style>

<template>
  <div class="fancy-banner-four position-relative  lg-pt-50 wow fadeInUp py-5">
    <div class="container mt-20 fancybannerfour">      
      <div class="row align-items-center">
        <div class="col-lg-5">
          <img src="/assets/images/img5.png" alt="image" class="lazy-img w-100" style="border-radius: 20px;"/>
        </div>
        <div class="col-lg-7 d-flex flex-column justify-content-center" style="min-height: 100%;">
          <div class="row">
            <div class="col-md-12">
              <h2 class="text-dark fs-1 fw-bold mb-30 text-center">Your Free International Virtual Card is waiting, Join us today.</h2>
            </div>
            <div class="col-md-4 mb-5">
              <div class="custom-card position-relative text-white p-4">
                <div class="card-number">01</div>
                <h5 class="fw-bold mt-50" style="color: #CFFF45;">Apply for a card</h5>
                <p class="mb-0" style="line-height: 20px; font-size: 15px;">Create a virtual card in seconds, for free</p>
              </div>
            </div>

            <div class="col-md-4 mb-5">
              <div class="custom-card position-relative text-white p-4">
                <div class="card-number">02</div>
                <h5 class="fw-bold mt-50" style="color: #CFFF45;">Fund your card with ease</h5>
                <p class="mb-0" style="line-height: 20px; font-size: 15px;">Add card balances in up to 15 currencies to your World Account for fee-free transactions</p>
              </div>
            </div>

            <div class="col-md-4 mb-5">
              <div class="custom-card position-relative text-white p-4">
                <div class="card-number">03</div>
                <h5 class="fw-bold mt-50" style="color: #CFFF45;">Manage your card online</h5>
                <p class="mb-0" style="line-height: 20px; font-size: 15px;">Access your card details and customise settings from your World Account dashboard</p>
              </div>
            </div>
          </div>
        </div>        
      </div>
    </div>
  </div>
</template>
