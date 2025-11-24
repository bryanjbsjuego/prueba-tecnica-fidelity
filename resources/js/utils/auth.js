import { defineStore } from 'pinia';
import { ref, computed } from 'vue';

export const useAuthStore = defineStore('auth', () => {
  const session = ref(localStorage.getItem('session') || null);
  const customer = ref(JSON.parse(localStorage.getItem('customer') || 'null'));
  const operatorToken = ref(localStorage.getItem('jwt_token') || null);
  const operatorUuid = ref(localStorage.getItem('uuid') || null);

  const isAuthenticated = computed(() => !!session.value);
  const isOperator = computed(() => !!operatorToken.value);

  function setUserSession(sessionId, customerData) {
    session.value = sessionId;
    customer.value = customerData;
    localStorage.setItem('session', sessionId);
    localStorage.setItem('customer', JSON.stringify(customerData));
  }

  function setOperatorSession(token, uuid, operatorData) {
    operatorToken.value = token;
    operatorUuid.value = uuid;
    localStorage.setItem('jwt_token', token);
    localStorage.setItem('uuid', uuid);
    if (operatorData) {
      localStorage.setItem('operator', JSON.stringify(operatorData));
    }
  }

  function clearSession() {
    session.value = null;
    customer.value = null;
    operatorToken.value = null;
    operatorUuid.value = null;
    localStorage.removeItem('session');
    localStorage.removeItem('customer');
    localStorage.removeItem('jwt_token');
    localStorage.removeItem('uuid');
    localStorage.removeItem('operator');
  }

  return {
    session,
    customer,
    operatorToken,
    operatorUuid,
    isAuthenticated,
    isOperator,
    setUserSession,
    setOperatorSession,
    clearSession
  };
});
