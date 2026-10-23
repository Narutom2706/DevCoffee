import { ref } from 'vue';
import type { Drink, Consumption, ApiProblemDetails, EcoMetrics } from '../types/coffee';

const API_BASE_URL = 'http://localhost:8000/api';

export function useCoffee() {
  const drinks = ref<Drink[]>([]);
  const consumptions = ref<Consumption[]>([]);
  const loading = ref<boolean>(false);
  const errorTitle = ref<string>('');
  const errorMessage = ref<string>('');
  const successMessage = ref<string>('');

  const ecoMetrics = ref<EcoMetrics>({
    responseTime: null,
    payloadSize: null,
    co2: null,
  });

  const clearError = () => {
    errorTitle.value = '';
    errorMessage.value = '';
  };

  const clearSuccess = () => {
    successMessage.value = '';
  };

  const captureEcoHeaders = (response: Response): void => {
    const time = response.headers.get('X-Response-Time-Ms');
    const size = response.headers.get('X-Payload-Size-Bytes');
    const co2 = response.headers.get('X-Estimated-CO2-Grams');

    if (time !== null || size !== null || co2 !== null) {
      ecoMetrics.value = {
        responseTime: time,
        payloadSize: size,
        co2: co2,
      };
    }
  };

  // GET toutes les boissons via /api/drinks
  const fetchDrinks = async (): Promise<void> => {
    try {
      const res = await fetch(`${API_BASE_URL}/drinks`);
      captureEcoHeaders(res);
      if (res.ok) {
        drinks.value = await res.json();
      }
    } catch (err) {
      console.error('Erreur chargement boissons', err);
    }
  };

  // GET les 20 dernieres conso
  const fetchConsumptions = async (): Promise<void> => {
    try {
      const res = await fetch(`${API_BASE_URL}/consumptions`);
      captureEcoHeaders(res);
      if (res.ok) {
        consumptions.value = await res.json();
      }
    } catch (err) {
      console.error('Erreur chargement consommations', err);
    }
  };

  // POST sauvegarder une conso via /api/consumptions
  const recordConsumption = async (
    developerName: string,
    drinkId: number
  ): Promise<boolean> => {
    clearError();
    clearSuccess();
    loading.value = true;

    try {
      const res = await fetch(`${API_BASE_URL}/consumptions`, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ developerName, drinkId }),
      });

      captureEcoHeaders(res);

      if (!res.ok) {
        let problem: ApiProblemDetails;
        try {
          problem = await res.json();
        } catch {
          problem = {
            type: 'about:blank',
            title: `Erreur HTTP ${res.status}`,
            status: res.status,
            detail: 'Une erreur inattendue est survenue.',
          };
        }

        errorTitle.value = problem.title || `Erreur ${res.status}`;
        errorMessage.value = problem.detail || 'Opération refusée.';
        return false;
      }

      successMessage.value = `Consommation enregistrée avec succès pour ${developerName} !`;
      await fetchConsumptions();
      return true;
    } catch (err) {
      errorTitle.value = 'Erreur Réseau';
      errorMessage.value = 'Impossible de contacter l\'API DevCoffee sur http://localhost:8000';
      return false;
    } finally {
      loading.value = false;
    }
  };

  // POST créer une nouvel boisson /api/drinks
  const createDrink = async (
    name: string,
    caffeineMg: number,
    volumeMl: number
  ): Promise<boolean> => {
    clearError();
    clearSuccess();
    loading.value = true;

    try {
      const res = await fetch(`${API_BASE_URL}/drinks`, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ name, caffeineMg, volumeMl }),
      });

      captureEcoHeaders(res);

      if (!res.ok) {
        const problem: ApiProblemDetails = await res.json();
        errorTitle.value = problem.title || 'Erreur';
        errorMessage.value = problem.detail || 'Impossible de créer la boisson.';
        return false;
      }

      successMessage.value = `Boisson "${name}" ajoutée au catalogue !`;
      await fetchDrinks();
      return true;
    } catch (err) {
      errorTitle.value = 'Erreur Réseau';
      errorMessage.value = 'Impossible de contacter l\'API DevCoffee.';
      return false;
    } finally {
      loading.value = false;
    }
  };

  // delete une conso via le lien HATEOAS (_links.delete.href)
  const deleteConsumption = async (
    href: string,
    method: string = 'DELETE'
  ): Promise<boolean> => {
    clearError();
    clearSuccess();

    try {
      const res = await fetch(href, {
        method: method || 'DELETE',
      });

      captureEcoHeaders(res);

      if (!res.ok) {
        const problem: ApiProblemDetails = await res.json();
        errorTitle.value = problem.title || 'Erreur de suppression';
        errorMessage.value = problem.detail || 'Impossible de supprimer cette consommation.';
        return false;
      }

      successMessage.value = 'Consommation supprimée avec succès.';
      await fetchConsumptions();
      return true;
    } catch (err) {
      errorTitle.value = 'Erreur Réseau';
      errorMessage.value = 'Impossible de contacter l\'API lors de la suppression.';
      return false;
    }
  };

  return {
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
  };
}
