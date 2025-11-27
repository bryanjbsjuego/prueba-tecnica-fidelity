import api from './api';

export async function login(email, password) {
  try {
    const response = await api.post('/login', { email, password });
    return response.data;
  } catch (error) {
    throw error;
  }
}

export async function operatorLogin(usuario, contrasena) {
  try {
    const response = await api.post('/operator/login', { usuario, contrasena });
    return response.data;
  } catch (error) {
    throw error;
  }
}

export async function operatorLogout(uuid) {
  try {
    const response = await api.post('/operator/logout', { uuid });
    return response.data;
  } catch (error) {
    throw error;
  }
}
