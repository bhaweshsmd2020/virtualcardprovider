<script setup>
import HeaderTwo from '@/Layouts/Default/HeaderTwo.vue'
import NewsletterTwo from '@/Components/Web/NewsletterTwo.vue'
import DefaultLayout from '@/Layouts/Default/DefaultLayout.vue'
import AboutSection from '@/Pages/Web/Home/Partials/AboutSection.vue'
import AppCTASection from '@/Pages/Web/Home/Partials/AppCTASection.vue'
// import BlogSection from '@/Pages/Web/Home/Partials/BlogSection.vue'
import CardCTASection from '@/Pages/Web/Home/Partials/CardCTASection.vue'
import FeaturesSection from '@/Pages/Web/Home/Partials/FeaturesSection.vue'
import HeroSection from '@/Pages/Web/Home/Partials/HeroSection.vue'
import TestimonialSection from '@/Pages/Web/Home/Partials/TestimonialSection.vue'
import VideoCtaSection from '@/Pages/Web/Home/Partials/VideoCtaSection.vue'
import BrandLogo from './Partials/BrandLogo.vue'

defineOptions({ layout: DefaultLayout })
defineProps(['home', 'testimonials', 'faqs', 'logos', 'blogs'])
</script>

<template>
  <HeaderTwo />
  <HeroSection :data="home.hero ?? {}" />
  <BrandLogo :data="home.testimonial ?? {}" :testimonials="testimonials" :logos="logos" />
  <div class="block-feature-six feedback-section-three position-relative pt-30 pb-20 md pb-80">
  <div class="container">
  <div class="row justify-content-center">
    <div class="wallet-buttons col-sm-4 align-items-center">
        <img src="/assets/images/icon/Add_to_Apple_Wallet_badge.png" alt="Apple Wallet" class="img-fluid mb-3" style="max-width: 200px;" />
        <img src="/assets/images/icon/Add_to_Google_Wallet_badge.png" alt="Google Wallet" class="img-fluid" style="max-width: 200px;" />
    </div>
    <!-- Middle: Use card info -->
    <div class="col-sm-4">
    <a href="#" class="wallet-card mt-10">
                <img src="/assets/images/logo1.png" alt="icon" class="img-fluid me-3 wallet-icon" />
      Use card with Apple or Google wallet
    </a>
     </div>
    <!-- Right: Employee cards -->
     <div class="col-sm-4">
    <a href="#" class="wallet-card mt-10">
          <img src="/assets/images/logo4.png" alt="icon" class="img-fluid me-3 wallet-icon" />
      Employee cards
    </a>
      </div>
      <div class="col-12 text-center mt-50"><p class="text-muted small px-3"><strong>Security tip:</strong> We don’t recommend sharing physical card details with merchants who keep your card on file. Instead, use your virtual card details for recurring card transactions and subscriptions. </p></div>
  </div>
   </div>
    </div>
 
  <FeaturesSection :data="home.features ?? {}" />
  <AboutSection :data="home.reviewer ?? {}" />
  <VideoCtaSection :data="home.video_cta ?? {}" />
  <CardCTASection :data="home.card_cta ?? {}" />
  <TestimonialSection :data="home.testimonial ?? {}" :testimonials="testimonials" :logos="logos" />
  <AppCTASection :data="home.app_cta" />
  <div class="block-feature-six mt-80" >
  <div class="container videosection">
  <div class="row align-items-center">
    <div class="col-12 col-md-6 mb-4 mb-md-0">
      <div class="row">
        <div class="col-1 text-end pe-0">
          <img src="/assets/images/icon/icon_10.svg" alt="icon" style="width: 30px; float: right;" />
        </div>
        <div class="col-10">
          <h2 class="text-dark fs-1 fw-bold mb-10">Travel,Trading and Subscription</h2>
          <p class="text-left">Pay for your accommodation or airline partners instantly with virtual cards. Pay for your online subscription and trading partners and customers</p>
        </div>
      </div>
    </div>

    <div class="col-md-2 d-none d-md-block"></div>

    <div class="col-12 col-md-4 hero-video pt-2">        
      <video autoplay muted loop playsinline style="max-width: 100%; border-radius: 20px;">
        <source src="assets/images/Virtual Card for you.mp4" type="video/mp4">
      </video>
    </div> 
  </div>
</div>
</div>
  <NewsletterTwo />
</template>

<style>
.wallet-text {
  font-size: 18px;
  font-weight: 600;
}

.wallet-icon {
  width: 35px;
}

.wallet-badge-images img {
  margin-bottom: 10px;
}

.wallet-line-item {
  gap: 10px;
}

@media (max-width: 767px) {
  .wallet-text {
    font-size: 16px;
    text-align: center;
  }

  .wallet-icon {
    width: 45px;
  }

  .wallet-line-item {
    justify-content: center;
    text-align: center;
    flex-direction: column;
  }

  .wallet-line-item p {
    margin-top: 10px;
  }
}

</style>