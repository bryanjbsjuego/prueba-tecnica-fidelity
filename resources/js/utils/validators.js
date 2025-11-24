export function validateEmail(email) {
  const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

  if (!email) {
    return 'El correo electrónico es obligatorio';
  }

  if (!emailRegex.test(email)) {
    return 'El correo electrónico no es válido';
  }

  return null;
}

export function validatePassword(password) {
  if (!password) {
    return 'La contraseña es obligatoria';
  }

  if (password.length < 6) {
    return 'La contraseña debe tener al menos 6 caracteres';
  }

  return null;
}

export function validateRequired(value, fieldName = 'Este campo') {
  if (!value || (typeof value === 'string' && value.trim() === '')) {
    return `${fieldName} es obligatorio`;
  }

  return null;
}
