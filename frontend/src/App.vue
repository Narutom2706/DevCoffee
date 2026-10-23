<template>
  <div class="app-layout">
    <header class="navbar">
      <div class="navbar-container">
        <div class="brand">
          <div class="brand-text">
            <h1>DevCoffee Tracker</h1>
            <span class="badge-status">API RESTful PHP 8 & Slim 4</span>
          </div>
        </div>

        <div class="rule-chip" title="Règle métier stricte de l'API">
          <span class="chip-dot"></span>
          <span>Limite stricte : <strong>400 mg / jour / dev</strong></span>
        </div>
      </div>
    </header>
    <main class="main-container">
      <transition name="fade">
        <div v-if="errorMessage" class="alert-rfc-error" role="alert">
          <div class="alert-icon">alerte</div>
          <div class="alert-content">
            <h3 class="alert-title">{{ errorTitle || 'Erreur API RFC 7807' }}</h3>
            <p class="alert-detail">{{ errorMessage }}</p>
          </div>
          <button class="alert-dismiss" @click="clearError" title="Fermer">✕</button>
        </div>
      </transition>
      <transition name="fade">
        <div v-if="successMessage" class="alert-success" role="status">
          <div class="alert-icon">nickel</div>
          <div class="alert-content">
            <p class="alert-detail">{{ successMessage }}</p>
          </div>
          <button class="alert-dismiss" @click="clearSuccess" title="Fermer">✕</button>
        </div>
      </transition>
      <div class="grid-layout">
        <section class="grid-col form-col">
          <ConsumptionForm
            :drinks="drinks"
            :loading="loading"
            @submit-consumption="handleConsumptionSubmit"
            @open-drink-modal="isDrinkModalOpen = true"
          />
        </section>
        <section class="grid-col list-col">
          <ConsumptionList
            :consumptions="consumptions"
            @delete-consumption="handleDeleteConsumption"
            @refresh="fetchConsumptions"
          />
        </section>
      </div>
    </main>
    <DrinkModal
      :is-open="isDrinkModalOpen"
      :loading="loading"
      @close="isDrinkModalOpen = false"
      @create-drink="handleCreateDrink"
    />
    <EcoBadge :metrics="ecoMetrics" />
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue';
import { useCoffee } from './composables/useCoffee';
import ConsumptionForm from './components/ConsumptionForm.vue';
import ConsumptionList from './components/ConsumptionList.vue';
import DrinkModal from './components/DrinkModal.vue';
import EcoBadge from './components/EcoBadge.vue';

const {
  drinks,
  consumptions,
  loading,
  errorTitle,
  errorMessage,
  successMessage,
  ecoMetrics,
  clearError,
  clearSuccess,
  fetchDrinks,
  fetchConsumptions,
  recordConsumption,
  createDrink,
  deleteConsumption,
} = useCoffee();

const isDrinkModalOpen = ref(false);

const handleConsumptionSubmit = async (payload: { developerName: string; drinkId: number }) => {
  await recordConsumption(payload.developerName, payload.drinkId);
};

const handleDeleteConsumption = async (payload: { href: string; method: string }) => {
  await deleteConsumption(payload.href, payload.method);
};

const handleCreateDrink = async (payload: { name: string; caffeineMg: number; volumeMl: number }) => {
  const success = await createDrink(payload.name, payload.caffeineMg, payload.volumeMl);
  if (success) {
    isDrinkModalOpen.value = false;
  }
};

onMounted(() => {
  fetchDrinks();
  fetchConsumptions();
});
</script>

<style>
* {
  box-sizing: border-box;
  margin: 0;
  padding: 0;
}

body {
  font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
  background-color: #f8fafc;
  color: #1e293b;
  min-height: 100vh;
  -webkit-font-smoothing: antialiased;
}
</style>

<style scoped>
.app-layout {
  min-height: 100vh;
  display: flex;
  flex-direction: column;
  padding-bottom: 90px;
}

.navbar {
  background: #ffffff;
  border-bottom: 1px solid #e2e8f0;
  position: sticky;
  top: 0;
  z-index: 50;
  box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.05);
}

.navbar-container {
  max-width: 1200px;
  margin: 0 auto;
  padding: 14px 20px;
  display: flex;
  align-items: center;
  justify-content: space-between;
}

.brand {
  display: flex;
  align-items: center;
  gap: 12px;
}

.brand-icon {
  font-size: 2rem;
  background: #fef3c7;
  padding: 6px;
  border-radius: 12px;
  line-height: 1;
}

.brand-text h1 {
  font-size: 1.35rem;
  font-weight: 800;
  color: #78350f;
  line-height: 1.2;
}

.badge-status {
  font-size: 0.72rem;
  font-weight: 600;
  color: #d97706;
  background: #ffffff;
  padding: 2px 6px;
  border-radius: 4px;
}

.rule-chip {
  display: flex;
  align-items: center;
  gap: 8px;
  background: #ffffff;
  color: #991b1b;
  border: 1px solid #991b1b;
  padding: 6px 14px;
  border-radius: 9999px;
  font-size: 0.82rem;
}

.chip-dot {
  width: 8px;
  height: 8px;
  border-radius: 50%;
  background: #991b1b;
  animation: pulse 1.5s infinite;
}

.main-container {
  max-width: 1200px;
  width: 100%;
  margin: 24px auto;
  padding: 0 20px;
}

.alert-rfc-error {
  background: #fef2f2;
  border: 1.5px solid #f87171;
  color: #991b1b;
  border-radius: 12px;
  padding: 16px 20px;
  display: flex;
  align-items: flex-start;
  gap: 14px;
  margin-bottom: 24px;
  box-shadow: 0 4px 6px -1px rgba(239, 68, 68, 0.1);
}

.alert-success {
  background: #f0fdf4;
  border: 1.5px solid #86efac;
  color: #166534;
  border-radius: 12px;
  padding: 14px 20px;
  display: flex;
  align-items: center;
  gap: 14px;
  margin-bottom: 24px;
}

.alert-icon {
  font-size: 1.5rem;
  line-height: 1;
}

.alert-content {
  flex: 1;
}

.alert-title {
  font-size: 1rem;
  font-weight: 700;
  margin-bottom: 4px;
}

.alert-detail {
  font-size: 0.92rem;
  line-height: 1.4;
}

.alert-dismiss {
  background: none;
  border: none;
  font-size: 1.1rem;
  cursor: pointer;
  color: inherit;
  opacity: 0.6;
  padding: 2px 6px;
}

.alert-dismiss:hover {
  opacity: 1;
}

.grid-layout {
  display: grid;
  grid-template-columns: 420px 1fr;
  gap: 24px;
  align-items: start;
}

@media (max-width: 960px) {
  .grid-layout {
    grid-template-columns: 1fr;
  }
}

.fade-enter-active,
.fade-leave-active {
  transition: opacity 0.25s ease, transform 0.25s ease;
}

.fade-enter-from,
.fade-leave-to {
  opacity: 0;
  transform: translateY(-8px);
}

@keyframes pulse {
  0%, 100% { opacity: 1; }
  50% { opacity: 0.4; }
}
</style>
