<template>
  <div class="card history-card">
    <div class="card-header">
      <div class="header-icon">📋</div>
      <div class="header-info">
        <h2>Historique des Consommations</h2>
        <p class="subtitle">20 dernières doses enregistrées (triées par date décroissante)</p>
      </div>
      <button class="btn-refresh" @click="$emit('refresh')" title="Rafraîchir">
        🔄
      </button>
    </div>
    <div v-if="consumptions.length === 0" class="empty-state">
      <span class="empty-icon">☕</span>
      <p>Aucune consommation enregistrée pour le moment.</p>
    </div>
    <div v-else class="table-wrapper">
      <table class="consumption-table">
        <thead>
          <tr>
            <th>#</th>
            <th>Développeur</th>
            <th>Boisson</th>
            <th>Caféine</th>
            <th>Date & Heure</th>
            <th class="text-right">Action</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="c in consumptions" :key="c.id" class="table-row">
            <td class="col-id">#{{ c.id }}</td>
            <td class="col-dev">
              <span class="dev-avatar">{{ c.developerName.charAt(0).toUpperCase() }}</span>
              <span class="dev-name">{{ c.developerName }}</span>
            </td>
            <td class="col-drink">
              <span class="drink-badge">{{ c.drinkName || 'Boisson' }}</span>
            </td>
            <td class="col-caffeine">
              <span class="caffeine-pill">⚡ {{ c.caffeineMg }} mg</span>
            </td>
            <td class="col-date">{{ formatDate(c.consumedAt) }}</td>
            <td class="col-action text-right">
              <button
                v-if="c._links?.delete"
                class="btn-delete"
                @click="handleDelete(c)"
                :title="`Supprimer consommation #${c.id}`"
              >
                🗑️ Supprimer
              </button>
            </td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
</template>

<script setup lang="ts">
import type { Consumption } from '../types/coffee';

const props = defineProps<{
  consumptions: Consumption[];
}>();

const emit = defineEmits<{
  (e: 'delete-consumption', payload: { href: string; method: string }): void;
  (e: 'refresh'): void;
}>();

const handleDelete = (c: Consumption) => {
  if (!c._links?.delete) return;
  if (confirm(`Confirmez-vous la suppression de la consommation #${c.id} (${c.drinkName} par ${c.developerName}) ?`)) {
    emit('delete-consumption', {
      href: c._links.delete.href,
      method: c._links.delete.method || 'DELETE',
    });
  }
};

const formatDate = (dateStr: string) => {
  if (!dateStr) return '';
  try {
    const d = new Date(dateStr.replace(' ', 'T'));
    if (isNaN(d.getTime())) return dateStr;
    return new Intl.DateTimeFormat('fr-FR', {
      day: '2-digit',
      month: '2-digit',
      hour: '2-digit',
      minute: '2-digit',
      second: '2-digit',
    }).format(d);
  } catch {
    return dateStr;
  }
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
  margin-bottom: 20px;
}

.header-icon {
  width: 44px;
  height: 44px;
  background: #f1f5f9;
  border-radius: 12px;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 1.4rem;
}

.header-info {
  flex: 1;
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

.btn-refresh {
  background: #f8fafc;
  border: 1px solid #e2e8f0;
  border-radius: 8px;
  padding: 8px 12px;
  cursor: pointer;
  font-size: 1rem;
  transition: all 0.2s ease;
}

.btn-refresh:hover {
  background: #f1f5f9;
  transform: rotate(45deg); /* c'est rigolo 😅😁 */
}

.empty-state {
  text-align: center;
  padding: 40px 20px;
  color: #94a3b8;
}

.empty-icon {
  font-size: 2.5rem;
  display: block;
  margin-bottom: 10px;
  opacity: 0.5;
}

.table-wrapper {
  overflow-x: auto;
}

.consumption-table {
  width: 100%;
  border-collapse: separate;
  border-spacing: 0;
  font-size: 0.9rem;
}

th {
  background: #f8fafc;
  color: #475569;
  font-weight: 600;
  text-align: left;
  padding: 12px 14px;
  border-bottom: 2px solid #e2e8f0;
  font-size: 0.8rem;
  text-transform: uppercase;
  letter-spacing: 0.03em;
}

td {
  padding: 12px 14px;
  border-bottom: 1px solid #f1f5f9;
  color: #1e293b;
  vertical-align: middle;
}

.table-row:hover td {
  background: #f8fafc;
}

.col-id {
  font-weight: 600;
  color: #94a3b8;
  font-size: 0.82rem;
}

.col-dev {
  display: flex;
  align-items: center;
  gap: 8px;
}

.dev-avatar {
  width: 28px;
  height: 28px;
  background: #dbeafe;
  color: #1e40af;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  font-weight: 700;
  font-size: 0.78rem;
}

.dev-name {
  font-weight: 600;
  color: #1e293b;
}

.drink-badge {
  background: #fef3c7;
  color: #92400e;
  padding: 3px 8px;
  border-radius: 6px;
  font-weight: 600;
  font-size: 0.82rem;
}

.caffeine-pill {
  font-family: ui-monospace, monospace;
  font-weight: 700;
  color: #b45309;
}

.col-date {
  color: #64748b;
  font-size: 0.82rem;
}

.text-right {
  text-align: right;
}

.btn-delete {
  background: #fee2e2;
  color: #b91c1c;
  border: 1px solid #fecaca;
  padding: 6px 12px;
  border-radius: 8px;
  font-size: 0.78rem;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.2s ease;
}

.btn-delete:hover {
  background: #dc2626;
  color: #ffffff;
  border-color: #dc2626;
}
</style>
