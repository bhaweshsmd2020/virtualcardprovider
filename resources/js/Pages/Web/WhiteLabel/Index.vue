<script setup>
import FancyBannerThree from '@/Components/Web/FancyBannerThree.vue'
import InnerBannerTwo from '@/Components/Web/InnerBannerTwo.vue'
import DefaultLayout from '@/Layouts/Default/DefaultLayout.vue'
import SliderSection from '../Home/Partials/SliderSection.vue'
import sharedComposable from '@/Composables/sharedComposable'
import moment from 'moment'
defineOptions({ layout: DefaultLayout })
const props = defineProps(['about', 'testimonials', 'logos', 'faqs', 'blogs'])
const { textExcerpt } = sharedComposable()
</script>

<template>
  <InnerBannerTwo
    :page="trans('White Label')"
    :title="about.header?.title"
    :description="about.header?.sub_title"
  /> 
 <div class="hero-container text-feature-three text-feature-seven position-relative mt-50 lg-mt-80 pb-50 lg-pb-120">
    <div class="content-wrapper">
      <div class="text-content">
        <h1 class="headline">
          More than<br>
          <span class="highlighted-word">white labeling</span>
        </h1>        
        <p class="description">
          Create your own virtual and physical cards ready-to-use right away. Our modern API guarantees fast and seamless integration allowing you to launch your new product in no time!
        </p>        
        <a class="cta-button" href="/contact-us">
          Let's get in touch
        </a>
      </div>      
      <div class="image-container">
        <img 
          src="/assets/whitelable/slide_md.avif" 
          alt="Product illustration" 
          class="responsive-image"
        >
      </div>
    </div>
  </div>
 
 <div  class="text-feature-three text-feature-seven position-relative mt-50 lg-mt-80 pb-50 lg-pb-120">
    <div class="container">
    <h2 class="text-dark fs-1 fw-bold mb-3 text-center">Key features overview</h2>
    <h3 class="text-dark fs-3  mb-70 text-center">Services that meet your needs</h3>
      <div class="row">
        <div class="col-xxl-8 col-lg-8 d-flex flex-column order-lg-last wow fadeInRight ms-auto">     
         <div class=" lg-mt-40  lg-pt-10">
            <div class="row">
                <div class="features-grid">
                  <div class="feature-card">
                     <h3 class="feature-title">White-Label solutions</h3>
                      <p class="feature-description">Flexible White-Label solution for turnkey launch of branded credit cards specifically for your business, guaranteeing a fast time-to-market through simplified integration.</p>
                   </div>
                   <div class="feature-card">
                     <h3 class="feature-title">KYC / AML</h3>
                      <p class="feature-description">Compliance with all regulations and directives affecting your business. Full compliance with KYC and AML requirements.</p>
                   </div>
                   <div class="feature-card">
                     <h3 class="feature-title">Tokenization</h3>
                      <p class="feature-description">Possibility to tokenize any type of cards to add them to digital wallets: Apple Pay, Google Pay, and Samsung Pay.</p>
                   </div>
                   <div class="feature-card">
                     <h3 class="feature-title">3D Secure</h3>
                      <p class="feature-description">3D Secure makes e-commerce particularly secure by requesting an additional verification step with the card issuer at the time of the transaction.</p>
                   </div>
                   <div class="feature-card">
                     <h3 class="feature-title">Virtual cards</h3>
                      <p class="feature-description">Instant issuance of personalized virtual cards for various purposes. Virtual cards enable you to make instant payments, transfers, and online purchases in an even more secure and reliable way.</p>
                   </div>
                   <div class="feature-card">
                     <h3 class="feature-title">Fraud Monitoring</h3>
                      <p class="feature-description">Fraud monitoring service provides payment transaction monitoring to detect and prevent payment card fraud, thus reducing possible financial losses.</p>
                   </div>             
                </div>                 
            </div>
          </div>
          <!-- /.counter-wrapper -->
        </div>
        <div class="col-xxl-4 col-lg-4 d-flex order-lg-first wow fadeInLeft">
          <div
            class="media-wrapper w-100 position-relative"
            :style="{ 'background-image': `url(${about.feature.bg_img})` }"
          >
          
          </div>
        </div>
      </div>
    </div>
  </div>

<div class="text-feature-three  position-relative mt-50 lg-mt-80 pb-50 lg-pb-120">
     <div class="container">
    <div class="row align-items-center">
        <div class="col-md-8">
            <div class="launch-tools-section">
    <div class="launch-tools-wrapper">
      <h2 class="launch-tools-title">The tools you need to launch in hours, not months</h2>
      
      <ul class="launch-tools-list">
        <li class="launch-tool-item">
          <svg class="launch-tool-icon" viewBox="0 0 20 20" fill="currentColor">
            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
          </svg>
          <span class="launch-tool-text">Modern REST APIs and webhooks</span>
        </li>
        
        <li class="launch-tool-item">
          <svg class="launch-tool-icon" viewBox="0 0 20 20" fill="currentColor">
            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
          </svg>
          <span class="launch-tool-text">Detailed API documentation</span>
        </li>
        
        <li class="launch-tool-item">
          <svg class="launch-tool-icon" viewBox="0 0 20 20" fill="currentColor">
            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
          </svg>
          <span class="launch-tool-text">Ready to use sample code</span>
        </li>
        
        <li class="launch-tool-item">
          <svg class="launch-tool-icon" viewBox="0 0 20 20" fill="currentColor">
            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
          </svg>
          <span class="launch-tool-text">Simple to deploy embeddable components</span>
        </li>
        
        <li class="launch-tool-item">
          <svg class="launch-tool-icon" viewBox="0 0 20 20" fill="currentColor">
            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
          </svg>
          <span class="launch-tool-text">Personalized test environment</span>
        </li>
        
        <li class="launch-tool-item">
          <svg class="launch-tool-icon" viewBox="0 0 20 20" fill="currentColor">
            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
          </svg>
          <span class="launch-tool-text">1:1 U.S.-based integration support</span>
        </li>
      </ul>
    </div>
  </div> 
        </div>
        <div class="col-md-4">
            <img style="max-width: 100%; border-radius: 20px;" src="/assets/whitelable/launchinhours.webp" />
        </div>
    </div>
    </div>
</div>

<div class="text-feature-three  position-relative mt-50 lg-mt-80 pb-50 lg-pb-120">
     <div class="">
        <div class="security-features-section">
    <div class="security-features-container">
      <div class="security-header-block">
        <h2 class="text-dark fs-1 fw-bold mb-3 text-center">Industry-standard security practices for peace of mind</h2> 
      </div>
      <div class="security-features-grid">
        <div class="security-feature-item">
          <div class="security-icon-box">
              <img src="/assets/whitelable/Group-133.svg" />           
          </div>
          <div class="security-text-content">
            <h3 class="security-feature-title">Multi-factor authentication</h3>
            <p class="security-feature-desc">
              Access to customer data is restricted by the principle of least access. Employees must leverage MFA on any devices used to access customer data.
            </p>
          </div>
        </div>

        <div class="security-feature-item">
          <div class="security-icon-box">
             <img src="/assets/whitelable/Group-134.svg" />
          </div>
          <div class="security-text-content">
            <h3 class="security-feature-title">Fraud monitoring</h3>
            <p class="security-feature-desc">
              We actively monitor our software for potential vulnerabilities and enlist third-party firms to conduct regular penetration testing.
            </p>
          </div>
        </div>

        <div class="security-feature-item">
          <div class="security-icon-box">
            <img src="/assets/whitelable/Group-135.svg" />
          </div>
          <div class="security-text-content">
            <h3 class="security-feature-title">SOC II Certified for data security</h3>
            <p class="security-feature-desc">
              We are SOC II Certified and use industry-standard encryption to secure your data, both in-flight and at-rest.
            </p>
          </div>
        </div>
      </div>
    </div>
  </div>
     </div>
</div>     
<div class="text-feature-three  position-relative  lg-mt-80 pb-50 lg-pb-120">
     <div class="container">
     <h2 class="text-dark fs-1 fw-bold mb-4 text-center">Why Virtual Card Provider?</h2>
     <p class="d-flex  text-center mb-4">We guarantee an increased speed to market for your financial product, as well as flexible pricing, compliance with regulatory requirements, and full support of your solution infrastructure.</p>
   <div class="row g-3">
  <!-- Card 1 -->
  <div class="col-12 col-sm-6 col-lg-3">
    <div class="custom-card text-black" style="background-color: #DBFF73;">
      <h5 class="card-title mb-3">Any Visa <br>Card Program</h5>
      <p class="card-text">Debit, credit, prepaid, virtual, consumer, and business credit card programs.</p>
    </div>
  </div>

  <!-- Card 2 -->
  <div class="col-12 col-sm-6 col-lg-3">
    <div class="custom-card text-white" style="background-color: #B8A9FB;">
      <h5 class="card-title mb-3">White-Label <br>Mobile App</h5>
      <p class="card-text">Use the mobile app to launch your customer’s project in double-quick time.</p>
    </div>
  </div>

  <!-- Card 3 -->
  <div class="col-12 col-sm-6 col-lg-3">
    <div class="custom-card text-black" style="background-color: #F7DD49;">
      <h5 class="card-title mb-3">BIN <br>Sponsorship</h5>
      <p class="card-text">Dedicated BIN range for each card program.</p>
    </div>
  </div>

  <!-- Card 4 -->
  <div class="col-12 col-sm-6 col-lg-3">
    <div class="custom-card text-black" id="customCard" style="background-color:#F9ECB7">
      <h5 class="card-title mb-3">Rapid Launch</h5>
      <p class="card-text">Launch your own card program in record time.</p>
    </div>
  </div>

  <!-- Card 5 -->
  <div class="col-12 col-sm-6 col-lg-3">
    <div class="custom-card text-black" style="background-color: #29594B;">
      <h5 class="card-title mb-3 text-black">Wide Range <br>&nbsp;of Program Settings</h5>
      <p class="card-text text-black">Card programs can be fully customised to your business needs.</p>
    </div>
  </div>
  <!-- Card 6 -->
  <div class="col-12 col-sm-6 col-lg-3">
    <div class="custom-card text-black" style="background-color: #e0f0bc;">
      <h5 class="card-title mb-3">Service Level <br>Availability</h5>
      <p class="card-text">Stable and modern system built for scale with SLA 99.5%+</p>
    </div>
  </div>
  <!-- Card 7 -->
  <div class="col-12 col-sm-6 col-lg-3">
    <div class="custom-card text-black" style="background-color:#FACAC5;">
      <h5 class="card-title mb-3">Dynamic <br>Spending Controls</h5>
      <p class="card-text">Easily set credit limits: for each program, any card and all types of transactions.</p>
    </div>
  </div>

  <!-- Card 8 -->
  <div class="col-12 col-sm-6 col-lg-3">
    <div class="custom-card text-black" style="background-color:#B3E1C7;">
      <h5 class="card-title mb-3">Tokenization</h5>
      <p class="card-text">Any card type can be added to digital wallets: Apple Pay, Google Pay, and Samsung Pay.</p>
    </div>
  </div>
</div>
     </div>
</div> 
  <section class="cta-section">
    <h2 class="cta-heading">
      Ready to get <span class="highlighted">started</span>?
    </h2>
    <p class="cta-subtext text-white">
      Tell us about your project, program, or platform and<br />
      we’ll get back to you shortly.
    </p>
    <a class="cta-button" href="/contact-us">
       Contact us <i class="bi bi-arrow-right"></i>
    </a>
  </section>
</template>
<style>
  .custom-card {
  border-radius:5px;
  width: 100%;
  height: 240px;
  box-shadow: 0 10px 20px rgba(0,0,0,0.15);
}
</style>