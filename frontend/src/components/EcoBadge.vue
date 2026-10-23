<template>
  <div v-if="metrics.responseTime !== null" class="eco-badge-container">
    <div class="eco-badge">
      <div class="eco-icon">🌱</div>
      <div class="eco-content">
        <span class="eco-title">Sobriété Numérique & Éco-Conception :</span>
        <span class="eco-item">
          Temps réponse : <strong>{{ metrics.responseTime }} ms</strong>
        </span>
        <span class="eco-sep">•</span>
        <span class="eco-item">
          Taille payload : <strong>{{ metrics.payloadSize }} octets</strong>
        </span>
        <span class="eco-sep">•</span>
        <span class="eco-item">
          CO₂ estimé : <strong>{{ metrics.co2 }} g</strong>
        </span>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import type { EcoMetrics } from '../types/coffee';

defineProps<{
  metrics: EcoMetrics;
}>();
</script>

<style scoped>
.eco-badge-container {
  position: fixed;
  bottom: 16px;
  left: 50%;
  transform: translateX(-50%);
  z-index: 1000;
  width: 90%;
  max-width: 820px;
}

.eco-badge {
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 12px;
  background: rgba(22, 101, 52, 0.95);
  color: #ecfdf5;
  backdrop-filter: blur(8px);
  padding: 10px 20px;
  border-radius: 9999px;
  box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.2), 0 8px 10px -6px rgba(0, 0, 0, 0.1);
  border: 1px solid rgba(74, 222, 128, 0.3);
  font-size: 0.85rem;
  transition: all 0.3s ease;
}

.eco-icon {
  font-size: 1.15rem;
  animation: pulse 2s infinite ease-in-out;
}

.eco-content {
  display: flex;
  align-items: center;
  flex-wrap: wrap;
  gap: 8px;
}

.eco-title {
  font-weight: 700;
  color: #bbf7d0;
}

.eco-item strong {
  color: #ffffff;
  font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, monospace;
}

.eco-sep {
  opacity: 0.5;
}

@keyframes pulse {
  0%, 100% { transform: scale(1); }
  50% { transform: scale(1.1); }
}

@media (max-width: 640px) {
  .eco-badge {
    border-radius: 12px;
    padding: 8px 14px;
    font-size: 0.78rem;
  }
}
</style>
