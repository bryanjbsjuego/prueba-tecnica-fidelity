import api from './api';

export async function getAwards(session, params = {}) {
  const { page = 1, limit = 8, search = '', order = 'asc' } = params;

  const response = await api.get('/premios', {
    params: { session, page, limit, search, order }
  });

  return response.data;
}
