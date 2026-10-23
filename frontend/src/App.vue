<template>
  <div class="app-layout">
    <header class="navbar">
      <div class="navbar-container">
        <div class="brand">
          <span class="brand-icon">☕</span>
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
          <div class="alert-icon">🚨</div>
          <div class="alert-content">
            <h3 class="alert-title">{{ errorTitle || 'Erreur API RFC 7807' }}</h3>
            <p class="alert-detail">{{ errorMessage }}</p>
          </div>
          <button class="alert-dismiss" @click="clearError" title="Fermer">✕</button>
        </div>
      </transition>

      <transition name="fade">
        <div v-if="successMessage" class="alert-success" role="status">
          <div class="alert-icon">✅</div>
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
