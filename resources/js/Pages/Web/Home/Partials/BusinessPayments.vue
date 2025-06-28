<script setup>
import { computed } from 'vue'

const props = defineProps({
  data: {
    type: Object,
    default: () => ({
      title: 'Stay on top of every business payment',
      cards: []
    })
  }
})

// Group cards into two rows of four
const groupedCards = computed(() => {
  const chunkSize = 4
  const result = []
  const cards = [...props.data.cards]

  for (let i = 0; i < cards.length; i += chunkSize) {
    result.push(cards.slice(i, i + chunkSize))
  }

  return result
})
</script>

<template>
  <div class="block-feature-six text-feature-five position-relative lg-mt-80">
    <div class="container mt-30">
      <div class="row flex-view">
        <h2 class="text-dark fs-1 fw-bold mb-30 mt-4 text-center">{{ data.title }}</h2>
        
        <template v-for="(row, rowIndex) in groupedCards" :key="rowIndex">
          <div class="row">
            <div 
              v-for="(card, cardIndex) in row" 
              :key="cardIndex"
              class="col-12 col-sm-6 col-lg-3 mb-5"
            >
              <div 
                class="card h-100 border shadow-sm overflow-hidden"
                :class="{
                  'rounded-30': rowIndex === 0 && cardIndex === 0,
                  'rounded-50': rowIndex === 0 && cardIndex === 1,
                  'rounded-3': !(rowIndex === 0 && (cardIndex === 0 || cardIndex === 1))
                }"
              >
                <img 
                  :src="card.image" 
                  :alt="card.title" 
                  class="lazy-img w-100" 
                />
                <h5 
                  class="py-2 text-center text-light" 
                  style="background: #29594B; font-size: 15px;"
                >
                  {{ card.title }}
                </h5>
                <div class="px-3">
                  <p class="agency">{{ card.description }}</p>
                </div>
                <div class="d-flex mt-4 mb-20" v-if="card.badges && card.badges.length">
                  <span 
                    v-for="(badge, badgeIndex) in card.badges" 
                    :key="badgeIndex"
                    class="badge text-bg-light ms-3"
                  >
                    {{ badge }}
                  </span>
                </div>
              </div>
            </div>
          </div>
          <div class="row mt-30"></div>
        </template>
      </div>
    </div>
  </div>
</template>

<style scoped>
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

.rounded-30 {
  border-radius: 0.3rem !important;
}

.rounded-50 {
  border-radius: 0.3rem !important;
}

.rounded-3 {
  border-radius: 0.3rem !important;
}
</style>