import api from './api';

export async function getAlliances(uuid, customerIdClient, page = 1, limit = 6) {
  const response = await api.get('/operator/alianzas', {
    params: { uuid, categoria_cliente_id: customerIdClient, page, limit }
  });

  return response.data;
}

export async function usedWingAlliance(uuid, allianceId) {
  const response = await api.post('/operator/alianzas/marcar-usada', {
    uuid,
    alianza_id: allianceId
  });

  return response.data;
}

export async function getAllianceDetail(uuid, allianceId) {
  const response = await api.get(`/operator/alianzas/${allianceId}`, {
    params: { uuid }
  });

  return response.data;
}
