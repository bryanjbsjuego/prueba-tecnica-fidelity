<template>
    <div>
        <Header />

        <div class="login-page">
            <div class="login-container">
                <div class="login-card">
                    <div class="login-header">
                        <img :src="logoConecta" class="login-logo" alt="Logo Conecta" />
                    </div>

                    <div v-if="generalError" class="alert alert--error">
                        {{ generalError }}
                    </div>

                    <form @submit.prevent="handleSubmit" class="login-form">
                        <TextInput id="email" v-model="form.email" type="email" label="Correo electrónico"
                            placeholder="ejemplo@correo.com" :error="errors.email" :disabled="loading" required
                            @blur="validateField('email')" />

                        <TextInput id="password" v-model="form.password" type="password" label="Contraseña"
                            placeholder="Ingresa tu contraseña" :error="errors.password" :disabled="loading"
                            :show-toggle="true" required @blur="validateField('password')" />

                        <div class="forgot-password">
                            <p class="forgot-password"><a href="#">Olvidaste tu contraseña</a></p>
                        </div>
                        <div class="login-footer">
                            <p class="help"><a href="#">¿Necesitas ayuda?</a></p>
                        </div>

                        <Button type="submit" variant="primary" :loading="loading" :disabled="loading"
                            track-name="login_submit" class="login-submit">
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

        if (error.response?.data?.message) {
            generalError.value = error.response.data.message;
        } else {
            generalError.value = 'Error al conectar con el servidor. Intenta nuevamente.';
        }
    } finally {
        loading.value = false;
    }
}
</script>
