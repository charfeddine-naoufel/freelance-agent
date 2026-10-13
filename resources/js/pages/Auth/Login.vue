<template>

    <div class="login-page">

        <h1>Freelance AI Agent</h1>

        <h2>Connexion</h2>

        <div v-if="error" class="error">
            {{ error }}
        </div>

        <form @submit.prevent="login">

            <div>
                <label>Email</label>

                <input
                    v-model="email"
                    type="email"
                    placeholder="freelance@example.com"
                    required
                >
            </div>

            <div>
                <label>Mot de passe</label>

                <input
                    v-model="password"
                    type="password"
                    placeholder="Mot de passe"
                    required
                >
            </div>

            <button
                type="submit"
                :disabled="loading"
            >
                {{ loading ? 'Connexion...' : 'Se connecter' }}
            </button>

        </form>

    </div>

</template>

<script setup>

import { ref } from 'vue';
import { useRouter } from 'vue-router';
import auth from '../../services/auth';

const router = useRouter();

const email = ref('');
const password = ref('');

const loading = ref(false);
const error = ref(null);

const login = async () => {

    loading.value = true;
    error.value = null;

    try {

        await auth.login(
            email.value,
            password.value
        );

        await router.push('/');

    } catch (e) {

        console.error(e);

        if (e.response?.status === 422) {

            error.value =
                'Veuillez vérifier les informations saisies.';

        } else if (e.response?.status === 401) {

            error.value =
                'Email ou mot de passe incorrect.';

        } else {

            error.value =
                'Une erreur est survenue lors de la connexion.';

        }

    } finally {

        loading.value = false;

    }
};

</script>