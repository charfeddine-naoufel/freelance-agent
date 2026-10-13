<template>
    <div class="profile-page">

        <h1>Mon profil freelance</h1>

        <div v-if="loading">
            Chargement...
        </div>

        <div v-else>

            <div v-if="error" class="error">
                {{ error }}
            </div>

            <form @submit.prevent="saveProfile">

                <div>
                    <label>Titre professionnel</label>

                    <input
                        v-model="profile.title"
                        type="text"
                        placeholder="Laravel & Vue.js Developer"
                    >
                </div>

                <div>
                    <label>Présentation</label>

                    <textarea
                        v-model="profile.bio"
                        rows="6"
                        placeholder="Présentez votre expérience..."
                    ></textarea>
                </div>

                <div>
                    <label>Localisation</label>

                    <input
                        v-model="profile.location"
                        type="text"
                        placeholder="Tunisia"
                    >
                </div>

                <div>
                    <label>Tarif horaire</label>

                    <input
                        v-model="profile.hourly_rate"
                        type="number"
                        step="0.01"
                        min="0"
                    >
                </div>

                <div>
                    <label>Années d'expérience</label>

                    <input
                        v-model="profile.years_experience"
                        type="number"
                        min="0"
                    >
                </div>

                <button type="submit" :disabled="saving">
                    {{ saving ? 'Enregistrement...' : 'Enregistrer' }}
                </button>

            </form>

        </div>

    </div>
</template>

<script setup>
import { onMounted, reactive, ref } from 'vue';
import api from '../../services/api';

const loading = ref(true);
const saving = ref(false);
const error = ref(null);

const profile = reactive({
    title: '',
    bio: '',
    location: '',
    hourly_rate: '',
    years_experience: '',
    skills: [],
    experiences: [],
    projects: [],
});

const loadProfile = async () => {

    loading.value = true;
    error.value = null;

    try {

        const response = await axios.get('/profile');

        Object.assign(
            profile,
            response.data.data
        );

    } catch (e) {

        console.error(e);

        error.value =
            'Impossible de charger le profil.';

    } finally {

        loading.value = false;

    }
};

const saveProfile = async () => {

    saving.value = true;
    error.value = null;

    try {

        const response = await api.put(
            '/profile',
            {
                title: profile.title,
                bio: profile.bio,
                location: profile.location,
                hourly_rate: profile.hourly_rate,
                years_experience: profile.years_experience,
            }
        );

        Object.assign(
            profile,
            response.data.data
        );

    } catch (e) {

        console.error(e);

        error.value =
            'Impossible de sauvegarder le profil.';

    } finally {

        saving.value = false;

    }
};

onMounted(() => {
    loadProfile();
});
</script>