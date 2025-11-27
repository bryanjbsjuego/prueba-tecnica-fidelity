<template>
    <div>
        <Header />
        <div class="login-page">
            <div class="login-container">
                <div class="login-card">
                    <div class="login-header">
                        <img :src="logoConecta" class="login-logo" alt="Logo Conecta" />
                    </div>

                    <form @submit.prevent="handleSubmit" class="login-form">
                        <TextInput
                            id="email"
                            v-model="form.email"
                            type="email"
                            label="Correo electrónico"
                            placeholder="ejemplo@correo.com"
                            :error="errors.email"
                            :disabled="loading"
                            required
                            @blur="validateField('email')"
                        />

                        <TextInput
                            id="password"
                            v-model="form.password"
                            type="password"
                            label="Contraseña"
                            placeholder="Ingresa tu contraseña"
                            :error="errors.password"
                            :disabled="loading"
                            :show-toggle="true"
                            required
                            @blur="validateField('password')"
                        />

                        <div class="forgot-password">
                            <a href="#">Olvidaste tu contraseña</a>
                        </div>

                        <div class="help">
                            <a href="#">¿Necesitas ayuda?</a>
                        </div>

                        <!-- El error aparece aquí, dentro del formulario -->
                        <div v-if="generalError" class="login-alert">
                            <svg class="alert-icon" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                            </svg>
                            {{ generalError }}
                        </div>

                        <Button
                            type="submit"
                            variant="primary"
                            :loading="loading"
                            :disabled="loading"
                            track-name="login_submit"
                            class="login-submit"
                        >
                            Continuar
                        </Button>
                    </form>
                </div>
            </div>
        </div>
        <Footer />
    </div>
</template>

<script setup>
import { ref, reactive, onMounted } from 'vue';
import { useRouter } from 'vue-router';
import { useAuthStore } from '../utils/auth';
import { login } from '../services/auth';
import { validateEmail, validatePassword } from '../utils/validators';
import { trackPageView } from '../utils/gtm';
import TextInput from '../components/TextInput.vue';
import Button from '../components/Button.vue';
import logoConecta from '../../images/Logo/logo-conecta.png';
import Header from '../Layouts/Header.vue';
import Footer from '../Layouts/Footer.vue';

const router = useRouter();
const authStore = useAuthStore();
const loading = ref(false);
const generalError = ref('');

const form = reactive({
    email: '',
    password: ''
});

const errors = reactive({
    email: '',
    password: ''
});

onMounted(() => {
    trackPageView('Login', window.location.pathname);
});

function validateField(field) {
    if (field === 'email') {
        errors.email = validateEmail(form.email);
    } else if (field === 'password') {
        errors.password = validatePassword(form.password);
    }
}

function validateForm() {
    errors.email = validateEmail(form.email);
    errors.password = validatePassword(form.password);
    return !errors.email && !errors.password;
}

async function handleSubmit() {

    generalError.value = '';
    errors.email = '';
    errors.password = '';

    if (!validateForm()) {
        return;
    }

    loading.value = true;

    try {
        const response = await login(form.email, form.password);

        if (response.success) {
            authStore.setUserSession(response.session, response.customer);
            router.push('/catalogo-premios');
        } else {
            generalError.value = response.message || 'Error en el inicio de sesión';
        }
    } catch (error) {
        console.error('Login error:', error);

        if (error.response?.status === 422 && error.response?.data?.errors) {
            const validationErrors = error.response.data.errors;

            if (validationErrors.email) {
                errors.email = ' ';
            }

            if (validationErrors.password) {
                errors.password = ' ';
            }

            if (validationErrors.password) {
                generalError.value = validationErrors.password[0];
            } else if (validationErrors.email) {
                generalError.value = validationErrors.email[0];
            }
        }
        else if (error.response?.status === 401) {
            generalError.value = error.response?.data?.message || 'Credenciales incorrectas';
        }
        else if (error.response?.data?.message) {
            generalError.value = error.response.data.message;
        }
        else {
            generalError.value = 'Error al conectar con el servidor. Intenta nuevamente.';
        }
    } finally {
        loading.value = false;
    }
}
</script>
