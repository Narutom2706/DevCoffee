<template>
  <div v-if="isOpen" class="modal-backdrop" @click.self="$emit('close')">
    <div class="modal-card">
      <div class="modal-header">
        <h3>Ajouter une Boisson au Catalogue</h3>
        <button class="btn-close" @click="$emit('close')">✕</button>
      </div>
      <form @submit.prevent="handleSubmit" class="modal-form">
        <div class="form-group">
          <label for="drinkName">Nom de la boisson *</label>
          <input
            id="drinkName"
            v-model="name"
            type="text"
            placeholder="Ex: Cold Brew, Ristretto..."
            required
            class="input"
          />
        </div>
        <div class="form-row">
          <div class="form-group">
            <label for="caffeineMg">Caféine (mg) *</label>
            <input
              id="caffeineMg"
              v-model.number="caffeineMg"
              type="number"
              min="1"
              placeholder="Ex: 80"
              required
              class="input"
            />
          </div>
          <div class="form-group">
            <label for="volumeMl">Volume (ml) *</label>
            <input
              id="volumeMl"
              v-model.number="volumeMl"
              type="number"
              min="1"
              placeholder="Ex: 150"
              required
              class="input"
            />
          </div>
        </div>
        <div class="modal-actions">
          <button type="button" class="btn btn-secondary" @click="$emit('close')">
            Annuler
          </button>
          <button type="submit" class="btn btn-primary" :disabled="loading">
            Ajouter la boisson
          </button>
        </div>
      </form>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref } from 'vue';

defineProps<{
  isOpen: boolean;
  loading?: boolean;
}>();

const emit = defineEmits<{
  (e: 'close'): void;
  (e: 'create-drink', payload: { name: string; caffeineMg: number; volumeMl: number }): void;
}>();

const name = ref('');
const caffeineMg = ref<number | null>(null);
const volumeMl = ref<number | null>(null);

const handleSubmit = () => {
  if (!name.value.trim() || !caffeineMg.value || !volumeMl.value) return;

  emit('create-drink', {
    name: name.value.trim(),
    caffeineMg: caffeineMg.value,
    volumeMl: volumeMl.value,
  });

  name.value = '';
  caffeineMg.value = null;
  volumeMl.value = null;
};
</script>

<style scoped>
.modal-backdrop {
  position: fixed;
  inset: 0;
  background: rgba(15, 23, 42, 0.6);
  backdrop-filter: blur(4px);
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 2000;
  padding: 20px;
}

.modal-card {
  background: #ffffff;
  border-radius: 16px;
  width: 100%;
  max-width: 480px;
  box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.2);
  padding: 24px;
}

.modal-header {
  display: flex;
  align-items: center;
  justify-content: space-between;
  margin-bottom: 20px;
}

h3 {
  font-size: 1.15rem;
  font-weight: 700;
  color: #1e293b;
  margin: 0;
}

.btn-close {
  background: none;
  border: none;
  font-size: 1.1rem;
  cursor: pointer;
  color: #64748b;
  padding: 4px;
}

.modal-form {
  display: flex;
  flex-direction: column;
  gap: 16px;
}

.form-group {
  display: flex;
  flex-direction: column;
  gap: 6px;
}

.form-row {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 14px;
}

label {
  font-size: 0.85rem;
  font-weight: 600;
  color: #334155;
}

.input {
  width: 100%;
  padding: 10px 14px;
  border: 1.5px solid #e2e8f0;
  border-radius: 10px;
  font-size: 0.95rem;
  color: #1e293b;
  background: #f8fafc;
  box-sizing: border-box;
}

.input:focus {
  outline: none;
  border-color: #f59e0b;
  background: #ffffff;
}

.modal-actions {
  display: flex;
  justify-content: flex-end;
  gap: 10px;
  margin-top: 10px;
}

.btn {
  padding: 10px 16px;
  border-radius: 8px;
  font-weight: 600;
  cursor: pointer;
  border: none;
  font-size: 0.9rem;
}

.btn-secondary {
  background: #f1f5f9;
  color: #475569;
}

.btn-primary {
  background: #d97706;
  color: #ffffff;
}

.btn-primary:hover:not(:disabled) {
  background: #b45309;
}
</style>
