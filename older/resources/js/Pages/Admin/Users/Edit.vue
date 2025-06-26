<template>
  <main class="container p-4 sm:p-6">
    <PageHeader />

    <form @submit.prevent="updateUser()">
      <div class="mx-auto flex w-[650px] flex-col gap-5">
        <!-- Basic Information Card -->
        <div class="card">
          <h6 class="card-header -mb-2">{{ trans('Basic Information') }}</h6>
          <div class="card-body">
            <div class="grid grid-cols-2 gap-3">
              <div>
                <label class="label mb-1">{{ trans('First Name') }}</label>
                <input type="text" class="input" v-model="editForm.first_name" />
              </div>

              <div>
                <label class="label mb-1">{{ trans('Last Name') }}</label>
                <input type="text" class="input" v-model="editForm.last_name" />
              </div>

              <div>
                <label class="label mb-1">{{ trans('Username') }}</label>
                <input type="text" class="input" v-model="editForm.username" />
              </div>

              <ImageInput v-model="editForm.avatar" label="Avatar" />

              <div>
                <label class="label mb-1">{{ trans('Email') }}</label>
                <input type="email" class="input" v-model="editForm.email" />
              </div>

              <div>
                <label class="label mb-1"> {{ trans('Phone') }}</label>
                <input type="text" class="input" v-model="editForm.phone" />
              </div>
            </div>
          </div>
        </div>

        <!-- Address Card -->
        <div class="card">
          <h6 class="card-header -mb-2">{{ trans('Address') }}</h6>
          <div class="card-body">
            <div class="grid grid-cols-1 gap-3">
              <div>
                <label class="label mb-1">{{ trans('Country') }}</label>
                <select v-model="editForm.country_id" class="select">
                  <option value="">{{ trans('Select Country') }}</option>
                  <option v-for="country in countries" :value="country.id" :key="country.id">
                    {{ country.name }}
                  </option>
                </select>
              </div>

              <div class="grid grid-cols-2 gap-3">
                <div>
                  <label class="label mb-1">{{ trans('State') }}</label>
                  <select v-model="editForm.state_id" class="select">
                    <option value="">{{ trans('Select State') }}</option>
                    <option v-for="state in states" :value="state.id" :key="state.id">
                      {{ state.name }}
                    </option>
                    <option value="1000">{{ trans("Other") }}</option>
                  </select>
                </div>

                <div v-if="editForm.state_id == 1000">
                  <label class="label mb-1">{{ trans('State Name') }}</label>
                  <input type="text" class="input" v-model="editForm.other_state" />
                </div>               
              </div>

              <div class="grid grid-cols-2 gap-3">
                <div>
                  <label class="label mb-1">{{ trans('City') }}</label>
                  <select v-model="editForm.city_id" class="select">
                    <option value="">{{ trans('Select City') }}</option>
                    <option v-for="city in cities" :value="city.id" :key="city.id">
                      {{ city.name }}
                    </option>
                    <option value="1000">{{ trans("Other") }}</option>
                  </select>
                </div>

                <div v-if="editForm.city_id == 1000">
                  <label class="label mb-1">{{ trans('City Name') }}</label>
                  <input type="text" class="input" v-model="editForm.other_city" />
                </div>               
              </div>

              <div>
                <label class="label mb-1">{{ trans('Postal Code') }}</label>
                <input type="text" class="input" v-model="editForm.postal_code" />
              </div>

              <div>
                <label class="label mb-1">{{ trans('Address Line') }}</label>
                <input type="text" class="input" v-model="editForm.address_line" />
              </div>
            </div>
          </div>
        </div>

        <!-- Date of Birth Card -->
        <div class="card">
          <h6 class="card-header -mb-2">{{ trans('Date of Birth') }}</h6>
          <div class="card-body">
            <div class="grid grid-cols-3 gap-3">
              <div>
                <label class="label mb-1"> {{ trans('DOB Day') }}</label>
                <select v-model="editForm.dob_day" class="select">
                  <option v-for="day in 31" :value="day" :key="day">{{ day }}</option>
                </select>
              </div>
              <div>
                <label class="label mb-1"> {{ trans('DOB Month') }}</label>
                <select v-model="editForm.dob_month" class="select">
                  <option v-for="month in 12" :value="month" :key="month">
                    {{ month }}
                  </option>
                </select>
              </div>
              <div>
                <label class="label mb-1"> {{ trans('DOB Year') }}</label>
                <select v-model="editForm.dob_year" class="select">
                  <option v-for="year in 100" :value="new Date().getFullYear() - year" :key="year">
                    {{ new Date().getFullYear() - year }}
                  </option>
                </select>
              </div>
            </div>
          </div>
        </div>

        <!-- Balances Card -->
        <div class="card">
          <h6 class="card-header -mb-2">{{ trans('Balances') }}</h6>
          <div class="card-body">
            <div class="grid grid-cols-2 gap-3">
              <div>
                <label class="label mb-1"> {{ trans('Balance') }} ({{ trans('Main') }})</label>
                <input type="number" step="any" class="input" v-model="editForm.balance" />
              </div>

              <div v-for="wallet in editForm.wallets" :key="wallet.id">
                <label class="label mb-1">
                  {{ trans('Balance') }} ({{ wallet?.name?.toUpperCase() }})</label
                >
                <input type="number" step="any" class="input" v-model="wallet.balance" />
              </div>
            </div>
          </div>
        </div>

        <!-- Card Limits Card -->
        <div class="card">
          <h6 class="card-header -mb-2">{{ trans('Card Settings') }}</h6>
          <div class="card-body">
            <div class="grid grid-cols-2 gap-3">
              <div v-for="card in cards" :key="card.id">
                <h5>{{ card.title }}</h5>
                <label class="label mb-1">
                  Card Limit
                </label>
                <input
                  type="number"
                  class="input"
                  v-model="editForm.card_limits[card.id]"
                />

                <label class="label mb-1">
                  Card Topup Limit
                </label>
                <input
                  type="number"
                  class="input"
                  v-model="editForm.topup_limit[card.id]"
                />

                <label class="label mb-1">
                  Total Card Spending Limit
                </label>
                <input
                  type="number"
                  class="input"
                  v-model="editForm.spending_limit[card.id]"
                />
              </div>
            </div>
          </div>
        </div>

        <!-- Others Card -->
        <div class="card">
          <h6 class="card-header -mb-2">{{ trans('Others') }}</h6>
          <div class="card-body">
            <div class="grid grid-cols-2 gap-3">
              <div class="col-span-2">
                <div>
                  <label for="toggle-checked-input0" class="toggle label">
                    <input
                      class="toggle-input peer sr-only"
                      id="toggle-checked-input0"
                      type="checkbox"
                      v-model="editForm.status"
                    />
                    <div class="toggle-body"></div>
                    <span class="label">{{ trans('Is Active?') }}</span>
                  </label>
                </div>
                <div>
                  <label for="toggle-checked-input1" class="toggle label">
                    <input
                      class="toggle-input peer sr-only"
                      id="toggle-checked-input1"
                      type="checkbox"
                      v-model="editForm.email_verified_at"
                    />
                    <div class="toggle-body"></div>
                    <span class="label">{{ trans('Email Verified') }}</span>
                  </label>
                </div>

                <div>
                  <label for="toggle-checked-input2" class="toggle label">
                    <input
                      class="toggle-input peer sr-only"
                      id="toggle-checked-input2"
                      type="checkbox"
                      v-model="editForm.phone_verified_at"
                    />
                    <div class="toggle-body"></div>
                    <span class="label">{{ trans('Phone Verified') }}</span>
                  </label>
                </div>

                <div>
                  <label for="toggle-checked-input3" class="toggle label">
                    <input
                      class="toggle-input peer sr-only"
                      id="toggle-checked-input3"
                      type="checkbox"
                      v-model="editForm.kyc_verified_at"
                    />
                    <div class="toggle-body"></div>
                    <span class="label">{{ trans('KYC Verified') }}</span>
                  </label>
                </div>
                <div v-if="userInfo.google2fa_secret">
                  <label for="toggle-checked-input4" class="toggle label">
                    <input
                      class="toggle-input peer sr-only"
                      id="toggle-checked-input4"
                      type="checkbox"
                      v-model="editForm.google2fa_secret"
                    />
                    <div class="toggle-body"></div>
                    <span class="label">{{ trans('2fa Authorization') }}</span>
                  </label>
                </div>
              </div>

              <div>
                <label class="label mb-1"> {{ trans('Password') }}</label>
                <input type="password" class="input" v-model="editForm.password" />
              </div>
            </div>

            <div class="mt-5 text-end">
              <SpinnerBtn
                classes="btn btn-primary"
                :processing="editForm.processing"
                :btn-text="trans('Update user')"
              />
            </div>
          </div>
        </div>
      </div>
    </form>
  </main>
</template>

<script setup>
import { ref, watch } from 'vue'
import { useForm } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/Admin/AdminLayout.vue'
import PageHeader from '@/Layouts/Admin/PageHeader.vue'
import trans from '@/Composables/transComposable'
import SpinnerBtn from '@/Components/SpinnerBtn.vue'
import ImageInput from '@/Components/Admin/ImageInput.vue'

defineOptions({ layout: AdminLayout })

const props = defineProps(['userInfo', 'countries', 'cities', 'states', 'cards', 'cardlimits', 'othercity', 'otherstate'])

const wallets = ref(
  props.userInfo.wallets.map((wallet) => {
    return { id: wallet.id, balance: wallet.balance, name: wallet.currency?.currency }
  })
)

const editForm = useForm({
  first_name: props.userInfo.first_name,
  last_name: props.userInfo.last_name,
  username: props.userInfo.username,
  avatar: '',
  email: props.userInfo.email,
  phone: props.userInfo.phone,
  card_limit: props.userInfo.card_limit,

  dob_day: props.userInfo.dob_day,
  dob_month: props.userInfo.dob_month,
  dob_year: props.userInfo.dob_year,

  country_code: props.userInfo.country_code,
  country_id: props.userInfo.country_id,
  state_id: props.userInfo.state_id,
  other_state: props.otherstate?.name || '',
  city_id: props.userInfo.city_id,
  other_city: props.othercity?.name || '',
  postal_code: props.userInfo.postal_code,
  address_line: props.userInfo.address_line,

  password: '',
  balance: props.userInfo.balance,

  status: props.userInfo.status ? true : false,
  email_verified_at: props.userInfo.email_verified_at != null ? true : false,
  phone_verified_at: props.userInfo.phone_verified_at != null ? true : false,
  kyc_verified_at: props.userInfo.kyc_verified_at != null ? true : false,
  google2fa_secret: props.userInfo.google2fa_secret != null ? true : false,
  wallets: wallets.value,

  meta: props.userInfo.meta,
 _method: 'PATCH',

  card_limits: {},
  topup_limit: {},
  spending_limit:{}
})

// Check if props.cardlimits is an array before calling forEach
if (Array.isArray(props.cardlimits)) {
  props.cardlimits.forEach(cardlimit => {
    editForm.card_limits[cardlimit.card_type] = cardlimit.limit
    editForm.topup_limit[cardlimit.card_type] = cardlimit.topup_limit
    editForm.spending_limit[cardlimit.card_type] = cardlimit.spending_limit
  })
}

const updateUser = () => {
  editForm.post(route('admin.users.update', props.userInfo.id), {
    preserveState: false
  })
}
</script>
