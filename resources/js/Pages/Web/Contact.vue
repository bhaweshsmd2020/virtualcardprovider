<script setup>
import InputFieldError from '@/Components/InputFieldError.vue'
import DefaultLayout from '@/Layouts/Default/DefaultLayout.vue'
import toast from '@/Composables/toastComposable'
import { useForm } from '@inertiajs/vue3'
import InnerBannerTwo from '@/Components/Web/InnerBannerTwo.vue'
import NewsletterTwo from '@/Components/Web/NewsletterTwo.vue'
import Address from '@/Pages/Web/Home/Partials/Address.vue'

defineOptions({ layout: DefaultLayout })

defineProps(['contact','countries','turnover','cards','bussinesses'])

const form = useForm({
  name: '',
  email: '',
  phonecode:'',
  phone: '',
  company_name:'',
  company_website:'',
  country:'',
  turnover:'',
  card:'',
  bussiness:'',
  message: '',
  
})

const submit = () => {
  form.post('/contact-us', {
    preserveScroll: true,
    onSuccess: () => {
      form.reset()
      toast.success(trans('Contact message has been send successfully'))
    }
  })
}
</script>

<template>
  <InnerBannerTwo
    :page="trans('Contact Us')"
    :title="contact.hero?.title"
    :description="contact.hero?.sub_title"
  />

  <!--
		=============================================
			Contact Us
		==============================================
		-->
  <div class="contact-us-section pt-10 lg-pt-80">
    <div class="container">
      <div class="position-relative">
          <div class="row align-items-center">
            <div class="col-lg-6 d-flex flex-column" style="min-height: 100%;">
              <div class="row mb-50">
                 <div class="col-md-12 mb-40">
                   <img class="rounded" src="/assets/images/contactusimage.jpg"/>
                 </div>
                <div class="col-md-12">
                  <h2>Contact Us</h2>
                  <p style="width: 350px;">{{ contact.form?.title }}</p>
                  <h5><a href="mailto:help@virtualcardprovider.com" class="fw-bold text-decoration-underline">help@virtualcardprovider.com</a></h5>
                  <!-- <h5 class="text-decoration-underline fw-bold mt-30"><u>Customer Support</u></h5> -->
                </div>
              </div>                

              <div class="row">
                <div class="col-lg-4">
                  <h5 class="fw-bold">Customer Support</h5>
                  <p>Our support team is available around the clock to address any concerns or queries you may have.</p>
                </div>
                <div class="col-lg-4">
                  <h5 class="fw-bold">Feedback & Suggestions</h5>
                  <p>We value your feedback and are always working to improve. Your input helps shape our future.</p>
                </div>
                <div class="col-lg-4">
                  <h5 class="fw-bold">Media Inquiries</h5>
                  <p class="overflow-wrap-anywhere">For media related questions or inquiries, please contact us at <a href="mailto:help@virtualcardprovider.com">help@virtualcardprovider.com</a></p>
                </div>
              </div>
            </div>

            <div class="col-lg-6">
              <div class="form-style-one">
                <form @submit.prevent="submit" id="contact-form" data-toggle="validator" class="bg-white p-5">
                  <h3 class="fw-bold">{{ trans('Get in Touch') }}</h3>
                  <p class="fw-bold mb-4">{{ trans('You can reach us anytime') }}</p>
                  <div class="row controls gy-3">
                    <div class="col-12">
                      <div class="input-group-meta form-group">
                        <input
                          type="text"
                          placeholder=" "
                          v-model="form.name"
                          required="required"
                          data-error="Name is required."
                        />
                         <label>Your Name</label>
                        <div class="help-block with-errors"></div>
                        <InputFieldError :message="form.errors.name" class="small" />
                      </div>
                    </div>
                    <div class="col-12">
                      <div class="input-group-meta form-group">
                        <input
                          type="email"
                          placeholder=""
                          v-model="form.email"
                          required="required"
                          data-error="Valid email is required."
                        />
                         <label>Email</label>
                        <div class="help-block with-errors"></div>
                        <InputFieldError :message="form.errors.email" class="small" />
                      </div>
                    </div>
                    <div class="col-12 d-flex">
                        <div class="row"> 
                         <div class="input-group-meta phonecode form-group col-5">
                         <select v-model="form.phonecode" class="select" required="required">
                            <option value=""></option>
                            <option v-for="country in countries" :key="country.id" :value="country.phonecode">
                              {{ country.name }} ({{ country.phonecode }})
                            </option>
                          </select>
                         <label>Country</label>
                        <div class="help-block with-errors"></div>
                        <InputFieldError :message="form.errors.phonecode" class="small" />
                      </div>
                      <div class="input-group-meta phonecode form-group col-7">
                        <input
                          type="text"
                          placeholder=" "
                          v-model="form.phone"
                          required="required"
                          data-error="Valid phone is required."
                        />
                         <label>Your Phone</label>
                        <div class="help-block with-errors"></div>
                        <InputFieldError :message="form.errors.phone" class="small" />
                         </div>
                      </div>
                    </div>
                    <div class="col-12">
                      <div class="input-group-meta form-group">
                        <input
                          type="text"
                          placeholder=" "
                          v-model="form.company_name"
                          required="required"
                          data-error="Company Name is required."
                        />
                         <label>Company Name</label>
                        <div class="help-block with-errors"></div>
                        <InputFieldError :message="form.errors.company_name" class="small" />
                      </div>
                    </div>

                      <div class="col-12">
                      <div class="input-group-meta form-group">
                        <input
                          type="text"
                          placeholder=" "
                          v-model="form.company_website"
                          required="required"
                          data-error="Company Website is required."
                        />
                         <label>Company Website</label>
                        <div class="help-block with-errors"></div>
                        <InputFieldError :message="form.errors.company_website" class="small" />
                      </div>
                    </div>
                    <div class="col-12">
                      <div class="input-group-meta form-group">
                          <select v-model="form.country" class="select" required="required">
                                  <option value=""></option>
                                  <option v-for="country in countries" :key="country.id" :value="country.name">
                                    {{ country.name }} ({{ country.code }})
                                  </option>
                                </select>
                        <label>Select country</label>
                        <div class="help-block with-errors"></div>
                        <InputFieldError :message="form.errors.country" class="small" />
                      </div>
                    </div>
                     <div class="col-12">
                      <div class="input-group-meta form-group">
                          <select v-model="form.turnover" class="select" required="required">
                                  <option value=""></option>
                                  <option v-for="tover in turnover" :key="tover" :value="tover">
                                    {{ tover }}
                                  </option>
                                </select>
                          <label>Estimated turnover</label>       
                        <div class="help-block with-errors"></div>
                        <InputFieldError :message="form.errors.turnover" class="small" />
                      </div>
                    </div>
                     <div class="col-12">
                      <div class="input-group-meta form-group">
                          <select v-model="form.card" class="select" required="required">
                                  <option value=""></option>
                                  <option v-for="card in cards" :key="card" :value="card">
                                    {{ card }}
                                  </option>
                                </select>
                           <label>Number of cards</label>      
                        <div class="help-block with-errors"></div>
                        <InputFieldError :message="form.errors.card" class="small" />
                      </div>
                    </div>
                     <div class="col-12">
                      <div class="input-group-meta form-group">
                          <select v-model="form.bussiness" class="select" required="required">
                                  <option value=""></option>
                                  <option v-for="bussiness in bussinesses" :key="bussiness" :value="bussiness">
                                    {{ bussiness }}
                                  </option>
                                </select>
                          <label>Business industry</label>
                        <div class="help-block with-errors"></div>
                        <InputFieldError :message="form.errors.bussiness" class="small" />
                      </div>
                    </div>
                    <div class="col-12">
                      <div class="input-group-meta form-group">
                        <textarea
                          placeholder=" "
                          v-model="form.message"
                          required="required"
                          data-error="Please,leave us a message."
                        ></textarea>
                         <label>Your message</label>
                        <div class="help-block with-errors"></div>
                        <InputFieldError :message="form.errors.message" class="small" />
                      </div>
                    </div>
                    <div class="col-12 mt-20">
                      <button type="submit" class="btn-four tran3s w-100 d-block">
                        {{ trans('Send Message') }}
                      </button>
                      <p class="text-center text-sm text-gray-500 mt-3 mb-0">
                        By contacting us, you agree to our
                        <a href="/terms-and-conditions" class="fw-bold">Terms of service</a> and
                        <a href="/privacy-policy" class="fw-bold">Privacy Policy</a>.
                      </p>
                    </div>
                  </div>
                </form>
              </div>
            </div>
          </div>
      </div>
    </div>
    <!-- <div class="map-banner mt-100 lg-mt-80" v-if="contact.map?.address">
      <div class="gmap_canvas h-100 w-100">
        <iframe
          class="gmap_iframe h-100 w-100"
          :src="`https://maps.google.com/maps?width=600&amp;height=400&amp;hl=en&amp;q=${contact.map?.address}&amp;t=&amp;z=12&amp;ie=UTF8&amp;iwloc=B&amp;output=embed`"
        ></iframe>
      </div>
    </div> -->
  </div>
  <Address/>
  <NewsletterTwo />
</template>

<style>
.form-style-one .input-group-meta input {
  height: 50px;
  padding: 0 10px;
  border: 1px solid gray;
  border-radius: 8px;
}

.form-style-one .input-group-meta textarea{
  height: 165px;
  padding: 10px 10px;
  border: 1px solid gray;
  border-radius: 8px;
}

.bg-white{
  border-radius: 20px;
  box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1);
}
.input-group-meta {
  position: relative;
}

.input-group-meta input,
.input-group-meta textarea,
.input-group-meta select {
  width: 100%;
  padding: 12px 10px;
  font-size: 16px;
  border: 1px solid #ccc;
  border-radius: 4px;
  background: none;
  outline: none;
}
.phonecode label {
  left:21px !important; 
}
.input-group-meta label {
  position: absolute;
  left: 10px;
  top: 50%;
  transform: translateY(-50%);
  background: #fff;
  padding: 0 4px;
  color: #999;
  font-size: 16px;
  pointer-events: none;
  transition: 0.2s ease all;
}



.input-group-meta input:focus + label,
.input-group-meta input:not(:placeholder-shown) + label,
.input-group-meta textarea:focus + label,
.input-group-meta textarea:not(:placeholder-shown) + label,
.input-group-meta select:focus + label,
.input-group-meta select:valid + label {
  top:0px;
  font-size: 12px;
  color: #333;

}

.input-group-meta input:focus,.input-group-meta select:focus,.input-group-meta textarea:focus {
    color:#026f5e;
    box-shadow: 0px 1px 2px 1px rgba(32, 33, 36, 0.06), inset 0 0 0 2px #026f5e;

}
.input-group-meta input:focus ~ label{
  color: #026f5e;
}
</style>