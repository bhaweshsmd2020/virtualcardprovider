<script setup>
import sharedComposable from '@/Composables/sharedComposable'
import { useForm } from "@inertiajs/vue3"
import InputFieldError from "@/Components/InputFieldError.vue"
import AuthLayout from "@/Layouts/AuthLayout.vue"
import SpinnerBtn from "@/Components/SpinnerBtn.vue"
import LeftBanner from "@/Pages/Auth/Partials/LeftBanner.vue"
import { ref } from "vue"
import axios from "axios"
import AjaxLoader from "@/Components/AjaxLoader.vue"
import { watch } from "vue"
import toast from '@/Composables/toastComposable'
import Swal from 'sweetalert2'
import 'boxicons/css/boxicons.min.css'
import Vue3Select from 'vue3-select'
import 'vue3-select/dist/vue3-select.css'

defineOptions({ layout: AuthLayout })
const { authUser, logout, uiAvatar } = sharedComposable()
const props = defineProps(["authPages", "countries", "codes"])
const loginData = props.authPages?.login ?? {}

const states = ref([])
const cities = ref([])
const getting = ref({
  states: false,
  cities: false,
})

function showAlert() {
  Swal.fire({
    icon: "success",
    title: "<h2 style='font-size: 20px;'>Email ID Verified Successfully</h2>",
    confirmButtonText: "Continue",
    allowOutsideClick: false,
    customClass: {
      popup: "custom-swal-popup",
    },
    didOpen: (toast) => {
      const closeButton = document.createElement("button")
      closeButton.innerHTML = "&times;"
      closeButton.classList.add("swal-close-btn")
      closeButton.onclick = () => Swal.close()

      toast.appendChild(closeButton)
    },
  });
}
showAlert();

const usCountry = props.countries.find(c => c.code === 'US');
const form = useForm({
  dob_day: "",
  dob_month: "",
  dob_year: "",

  fund_source: "",
  employment_status: "",
  ssn_ein: "",

  country_id: usCountry?.id || "",
  state_id: "1000",
  other_state: "",
  city_id: "1000",
  other_city: "",
  postal_code: "",
  address_line: "",
  country_code: "",
  phone: "",
  partner_id: "",
  refer_by: "",
})

// watch(
//   () => form.country_id,
//   () => {
//     form.state_id = ""
//     form.city_id = ""
//     let countryId = form.country_id
//     if (countryId) {
//       getting.value.states = true
//       axios
//         .get(route("api.get-states-by-country", countryId))
//         .then((res) => {
//           states.value = res.data
//         })
//         .finally(() => {
//           getting.value.states = false
//         })
//     }
//   }
// )

// watch(
//   () => form.state_id,
//   () => {
//     form.city_id = ""
//     let stateId = form.state_id
//     if (stateId) {
//       getting.value.cities = true
//       axios
//         .get(route("api.get-cities-by-state", stateId))
//         .then((res) => {
//           cities.value = res.data
//         })
//         .finally(() => {
//           getting.value.cities = false
//         })
//     }
//   }
// )

const submit = () => {
  form.post(route("account.setup"))
}

const sendOtp = () => {
  form.post(route("phone.verification.send"))
}
</script>

<template>
  <Head title="Account Setup" />
  <div class="register-wrapper d-flex justify-content-center align-items-center">
    <div class="register-container d-flex position-relative">
      
      <!-- Top Left Logo -->
      <div class="top-left-logo">
        <a href="/"><img v-lazy="$page.props.primaryData?.deep_logo" alt="logo" /></a>
      </div>

      <!-- Left Image -->
      <div class="register-image">
        <img v-lazy="loginData.image2" alt="" />
      </div>

      <!-- Right Form -->
      <div class="register-form">

        <div class="d-flex justify-content-end">
          <form method="POST" action="#">
            <button type="button" @click="logout()" class="signin-button">
              <Icon icon="fe:logout"></Icon>
              <span>{{ trans('Logout') }}</span>
            </button>
          </form>
        </div>

        <h4>You're one step away...</h4>
        <p class="mb-50">Complete your account setup</p>

        <form @submit.prevent="submit" class="account-setup">
          <div class="signin-banner-from-box">
            <div class="row">             
              <label>{{ trans("Date of Birth") }}</label>
              <div class="col-4">
                <div class="postbox__comment-input mb-15">                  
                  <input
                    type="number"
                    v-model="form.dob_day"
                    min="1"
                    max="31"
                    autocomplete="bday-day"
                    class="inputText"
                    placeholder="Day"
                  />
                  <InputFieldError :message="form.errors.dob_day" />
                </div>
              </div>
              <div class="col-4">
                <div class="postbox__comment-input mb-15">
                  <label></label>
                  <input
                    type="number"
                    v-model="form.dob_month"
                    min="1"
                    max="12"
                    autocomplete="bday-month"
                    class="inputText"
                    placeholder="Month"
                  />
                  <InputFieldError :message="form.errors.dob_month" />
                </div>
              </div>
              <div class="col-4">
                <div class="postbox__comment-input mb-15">
                  <label></label>
                  <input
                    type="number"
                    v-model="form.dob_year"
                    min="1900"
                    :max="new Date().getFullYear()"
                    autocomplete="bday-year"
                    class="inputText"
                    placeholder="Year"
                  />
                  <InputFieldError :message="form.errors.dob_year" />
                </div>
              </div>

              <div class="col-12 mb-10">
                <div class="row">
                  <div class="col-md-5">
                    <div class="postbox__comment-input">
                      <label>{{ trans("Phone Country Code") }}</label>
                      <Vue3Select
                        v-model="form.country_code"
                        :options="countries"
                        :reduce="country => country.phonecode"
                        :getOptionLabel="country => `${country.name} (${country.phonecode})`"
                        placeholder="Select Country"
                        class="inputText"
                        style="padding: 0px;"
                      />
                    </div>
                  </div>
                  <div class="col-md-7">
                    <div class="postbox__comment-input">
                      <label>{{ trans("Phone Number") }}</label>
                      <input
                        type="number"
                        v-model="form.phone"
                        maxlength="15"
                        class="inputText"
                        placeholder="Phone Number"
                      />
                    </div>
                    <small class="text-danger">Enter Phone Number without country code</small>
                  </div>
                </div>
                <InputFieldError :message="form.errors.country_code" /><br>
                <InputFieldError :message="form.errors.phone" />  
              </div>
              
              <div class="col-12 mb-25">
                <div class="postbox__comment-input">
                  <label>{{ trans("What is your source of funds?") }}</label>
                  <select v-model="form.fund_source" class="inputText">
                    <option value="" disabled>{{ trans("Select") }}</option>
                    <option value="Personal Savings">Personal Savings</option>
                    <option value="Returns on Investments">Returns on Investments</option>
                    <option value="Fund from Asset Sales">Fund from Asset Sales</option>
                    <option value="Inheritances and Gifts">Inheritances and Gifts</option>
                    <option value="Business Income">Business Income</option>
                    <option value="Pension Payments">Pension Payments</option>
                    <option value="Interest from Savings">Interest from Savings</option>
                  </select>
                  <InputFieldError :message="form.errors.fund_source" />
                </div>
              </div>

              <!-- <div class="col-6">
                <div class="postbox__comment-input mt-15">
                  <label>{{ trans("Employment Status") }}</label>
                  <select v-model="form.employment_status" class="inputText">
                    <option value="" disabled>{{ trans("SELECT") }}</option>
                    <option value="Part-time employment">Part-time employment</option>
                    <option value="Full-time employment">Full-time employment</option>
                    <option value="Temporary employment">Temporary employment</option>
                    <option value="Self-employed/independent contractor">Self-employed/independent contractor</option>
                    <option value="Unemployed">Unemployed</option>
                  </select>
                  <InputFieldError :message="form.errors.employment_status" />
                </div>
              </div> -->

              <!-- <div class="col-6 mb-20">
                <div class="postbox__comment-input mt-15">
                  <label>{{ trans("Last 4 digit of SSN/EIN") }}</label>
                  <input
                    type="text"
                    v-model="form.ssn_ein"
                    class="inputText"
                    autocomplete="address-line1"
                    placeholder="Last 4 digit of SSN/EIN"
                  />
                  <InputFieldError :message="form.errors.ssn_ein" />
                </div>
              </div> -->

              <div class="col-12">
                <div class="postbox__comment-input mb-15">
                  <label>{{ trans("Country") }}</label>
                  <Vue3Select
                    v-model="form.country_id"
                    :options="countries"
                    label="name"
                    :reduce="country => country.id"
                    placeholder="Select Country"
                    class="inputText" style="padding: 0px;"
                  />
                  <InputFieldError :message="form.errors.country_id" />
                </div>
              </div>

              <div class="col-6" v-if="form.state_id == '1000'">
                <div class="postbox__comment-input mb-15">
                  <label>{{ trans("State") }}</label>
                  <input
                    type="text"
                    v-model="form.other_state"
                    class="inputText"
                    placeholder="State"
                  />
                  <InputFieldError :message="form.errors.other_state" />
                </div>
              </div>

              <div class="col-6" v-if="form.city_id == '1000'">
                <div class="postbox__comment-input mb-15">
                  <label>{{ trans("City") }}</label>
                  <input
                    type="text"
                    v-model="form.other_city"
                    class="inputText"
                    placeholder="City"
                  />
                  <InputFieldError :message="form.errors.other_city" />
                </div>
              </div>

              <div class="col-6 colsm">
                <div class="postbox__comment-input mb-15">
                  <label>
                    {{ trans("Street Address") }}
                    <span class="tooltip-wrapper">
                      <i class="bx bx-info-circle"></i>
                      <span class="custom-tooltip">Address proof documents may require.</span>
                    </span>  
                  </label>
                  <input
                    type="text"
                    v-model="form.address_line"
                    class="inputText"
                    autocomplete="address-line1"
                    placeholder="Street Address"
                  />
                  <InputFieldError :message="form.errors.address_line" />                
                </div>
              </div>

              <div class="col-6">
                <div class="postbox__comment-input mb-15">
                  <label>{{ trans("Postal Code / Zip Code") }}</label>
                  <input
                    type="text"
                    v-model="form.postal_code"
                    autocomplete="postal-code"
                    class="inputText"
                    placeholder="Postal Code / Zip Code"
                  />
                  <InputFieldError :message="form.errors.postal_code" />
                </div>
              </div> 
              
              <div class="col-6">
                <div class="postbox__comment-input mb-15">
                  <label>
                    {{ trans("Partner ID") }}
                    <span class="tooltip-wrapper">
                      <i class="bx bx-info-circle"></i>
                      <span class="custom-tooltip">Type your Partner ID, if you reffered by agent or partner only.</span>
                    </span>  
                  </label>
                  <input
                    type="text"
                    v-model="form.partner_id"
                    class="inputText"
                    autocomplete="partner_id"
                    placeholder="Partner ID"
                  />
                  <InputFieldError :message="form.errors.partner_id" />                
                </div>
              </div>

              <div class="col-6">
                <div class="postbox__comment-input mb-15">
                  <label>
                    {{ trans("Refer By") }} 
                  </label>
                  <input
                    type="text"
                    v-model="form.refer_by"
                    class="inputText"
                    autocomplete="address-line1"
                    placeholder="Refer By"
                  />
                  <InputFieldError :message="form.errors.refer_by" />                
                </div>
              </div>
            </div>
          </div>
          <div class="mb-20 mt-20 signin-banner-from-btn">
            <SpinnerBtn
              :processing="form.processing"
              :btn-text="trans('Save & Continue')"
              class="signin-btn w-100"
            />
          </div>
        </form>
        <div class="d-flex justify-content-center mt-10">
          Need help, write to us &nbsp;<a class="fw-500" href="mailto:help@virtucalcardprovider.com">help@virtucalcardprovider.com</a>
        </div>
      </div>
      
    </div>
  </div>
</template>

<style>
   .account-setup{
    background: #fff;
    padding: 34px;
    border-radius: 5px;
   }
  .country-code{
    border-bottom-left-radius: 12px;
    border-top-left-radius: 12px;
  }
  .phone-number{
    border-bottom-right-radius: 12px;
    border-top-right-radius: 12px;
  }
  .signin-banner-bg{
    height: 960px !important;
  }

  .bx-info-circle{
    color: rgb(96 14 228);
    cursor: pointer;
    position: relative;
    bottom:0px;
  }

  .custom-tooltip {
    visibility: hidden;
    opacity: 0;
    background-color: #333;
    color: #fff;
    text-align: center;
    border-radius: 6px;
    padding: 6px 10px;
    position: absolute;
    z-index: 1;
    bottom: 110%;
    left: 50%;
    transform: translateX(-50%);
    white-space: nowrap;
    font-size: 12px;
    transition: opacity 0.2s ease-in-out;
  }

  .tooltip-wrapper:hover .custom-tooltip,
  .tooltip-wrapper:focus-within .custom-tooltip {
    visibility: visible;
    opacity: 1;
  }

  label{
    color: #026f5e;
  }
  .postbox__comment-input .floating-label{
    top: 0;
    background: #fff;
  }

  .vs__dropdown-toggle{
    height: 55px;
    box-shadow: inset 0 0 0 1px #E5E5E5;
    border: none;
  }

  .vs__search, .vs__search:focus{
    font-size: 12px;
    padding: 0px 15px;
    font-weight: 500;
  }

  .vs__dropdown-menu{
    overflow-x: hidden;
  }
  .vs__selected {
    padding-left: 12px;
  }
</style>