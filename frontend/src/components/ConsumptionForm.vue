<template>
  <div class="card form-card">
    <div class="card-header">
      <div>
        <h2>Ajouter une Consommation</h2>
        <p class="subtitle">Enregistrez votre dose de caféine quotidienne</p>
      </div>
    </div>
    <form @submit.prevent="handleSubmit" class="form">
      <div class="form-group">
        <label for="devName">
          <span>Développeur</span>
          <span class="required">*</span>
        </label>
        <div class="input-wrapper">
          <input
            id="devName"
            v-model="developerName"
            type="text"
            placeholder="Ex: Thomas, Sarah, Alexandre..."
            required
            class="input"
          />
        </div>
      </div>
      <div class="form-group">
        <div class="label-row">
          <label for="drinkSelect">
            <span>Boisson</span>
            <span class="required">*</span>
          </label>
          <button
            type="button"
            class="btn-text-link"
            @click="$emit('open-drink-modal')"
          >
            + Nouvelle boisson
          </button>
        </div>
        <div class="input-wrapper">
          <select
            id="drinkSelect"
            v-model="selectedDrinkId"
            required
            class="input select"
          >
            <option :value="null" disabled>Sélectionnez une boisson...</option>
            <option
              v-for="drink in drinks"
              :key="drink.id"
              :value="drink.id"
            >
              {{ drink.name }} — {{ drink.caffeineMg }} mg ({{ drink.volumeMl }} ml)
            </option>
          </select>
        </div>
      </div>
      <div v-if="selectedDrink" class="caffeine-preview">
        <span class="preview-label">Apport de caféine :</span>
        <span class="preview-badge" :class="getBadgeClass(selectedDrink.caffeineMg)">
          {{ selectedDrink.caffeineMg }} mg
        </span>
        <span class="preview-note">Limite max journalière : 400 mg</span>
      </div>
      <button
        type="submit"
        class="btn btn-primary"
        :disabled="loading || !developerName.trim() || !selectedDrinkId"
      >
        <span v-if="loading" class="spinner"></span>
        <span v-else>Enregistrer la consommation</span>
      </button>
    </form>
  </div>
</template>

<script setup lang="ts">
import { ref, computed } from 'vue';
import type { Drink } from '../types/coffee';

const props = defineProps<{
  drinks: Drink[];
  loading?: boolean;
}>();

const emit = defineEmits<{
  (e: 'submit-consumption', payload: { developerName: string; drinkId: number }): void;
  (e: 'open-drink-modal'): void;
}>();

const developerName = ref('');
const selectedDrinkId = ref<number | null>(null);

const selectedDrink = computed(() => {
  return props.drinks.find((d) => d.id === selectedDrinkId.value) || null;
});

const getBadgeClass = (mg: number) => {
  if (mg <= 80) return 'badge-low';
  if (mg <= 130) return 'badge-med';
  return 'badge-high';
};

const handleSubmit = () => {
  if (!developerName.value.trim() || !selectedDrinkId.value) return;

  emit('submit-consumption', {
    developerName: developerName.value.trim(),
    drinkId: selectedDrinkId.value,
  });
};
</script>

<style scoped>
.card {
  background: #ffffff;
  border-radius: 16px;
  box-shadow: 0 4px 20px -2px rgba(0, 0, 0, 0.06), 0 2px 6px -1px rgba(0, 0, 0, 0.04);
  padding: 24px;
  border: 1px solid #f1f5f9;
}

.card-header {
  display: flex;
  align-items: center;
  gap: 14px;
  margin-bottom: 22px;
}

h2 {
  font-size: 1.25rem;
  font-weight: 700;
  color: #1e293b;
  margin: 0;
}

.subtitle {
  font-size: 0.85rem;
  color: #64748b;
  margin: 2px 0 0 0;
}

.form {
  display: flex;
  flex-direction: column;
  gap: 18px;
}

.form-group {
  display: flex;
  flex-direction: column;
  gap: 6px;
}

.label-row {
  display: flex;
  align-items: center;
  justify-content: space-between;
}

label {
  font-size: 0.88rem;
  font-weight: 600;
  color: #334155;
  display: flex;
  align-items: center;
  gap: 4px;
}

.required {
  color: #ef4444;
}

.btn-text-link {
  background: none;
  border: none;
  color: #d97706;
  font-size: 0.82rem;
  font-weight: 600;
  cursor: pointer;
  padding: 0;
  text-decoration: underline;
}

.btn-text-link:hover {
  color: #b45309;
}

.input {
  width: 100%;
  padding: 10px 14px;
  border: 1.5px solid #e2e8f0;
  border-radius: 10px;
  font-size: 0.95rem;
  color: #1e293b;
  background: #f8fafc;
  transition: all 0.2s ease;
  box-sizing: border-box;
}

.input:focus {
  outline: none;
  border-color: #f59e0b;
  background: #ffffff;
  box-shadow: 0 0 0 3px rgba(245, 158, 11, 0.15);
}

.select {
  cursor: pointer;
}

.caffeine-preview {
  display: flex;
  align-items: center;
  gap: 10px;
  background: #fffbeb;
  border: 1px dashed #fde68a;
  padding: 10px 14px;
  border-radius: 10px;
  font-size: 0.85rem;
}

.preview-label {
  color: #92400e;
  font-weight: 600;
}

.preview-badge {
  padding: 3px 8px;
  border-radius: 6px;
  font-weight: 700;
  font-size: 0.82rem;
}

.badge-low {
  background: #dcfce7;
  color: #166534;
}

.badge-med {
  background: #fef3c7;
  color: #92400e;
}

.badge-high {
  background: #fee2e2;
  color: #991b1b;
}

.preview-note {
  margin-left: auto;
  font-size: 0.76rem;
  color: #78716c;
}

.btn {
  padding: 12px 18px;
  border-radius: 10px;
  font-weight: 600;
  font-size: 0.95rem;
  cursor: pointer;
  transition: all 0.2s ease;
  border: none;
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 8px;
}

.btn-primary {
  background: linear-gradient(135deg, #d97706 0%, #b45309 100%);
  color: #ffffff;
  box-shadow: 0 4px 12px rgba(217, 119, 6, 0.25);
}

.btn-primary:hover:not(:disabled) {
  background: linear-gradient(135deg, #b45309 0%, #92400e 100%);
  box-shadow: 0 6px 16px rgba(217, 119, 6, 0.35);
  transform: translateY(-1px);
}

.btn-primary:disabled {
  opacity: 0.6;
  cursor: not-allowed;
}

.spinner {
  width: 18px;
  height: 18px;
  border: 2px solid rgba(255, 255, 255, 0.3);
  border-radius: 50%;
  border-top-color: #ffffff;
  animation: spin 0.8s linear infinite;
}

@keyframes spin {
  to { transform: rotate(360deg); }
}
</style>
