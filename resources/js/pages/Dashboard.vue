
<template>
    <div class="dashboard">

        <div class="welcome-section">
            <div>
                <h1>Bienvenue dans votre espace freelance 👋</h1>
                <p>
                    Préparez votre profil pour permettre à votre agent IA
                    de trouver les missions adaptées à vos compétences.
                </p>
            </div>

            <RouterLink to="/profile" class="primary-button">
                Compléter mon profil
            </RouterLink>
        </div>

        <div v-if="loading" class="loading">
            Chargement des statistiques...
        </div>

        <div v-else-if="error" class="error-message">
            {{ error }}
            <button @click="loadDashboard">Réessayer</button>
        </div>

        <template v-else>

            <div class="stats-grid">

                <div class="stat-card">
                    <div class="stat-icon purple">♙</div>
                    <div>
                        <span class="stat-label">Profil</span>
                        <h2>{{ completion }}%</h2>
                        <small>Complété</small>
                    </div>
                </div>

                <div class="stat-card">
                    <div class="stat-icon blue">⚙</div>
                    <div>
                        <span class="stat-label">Compétences</span>
                        <h2>{{ profile.skills?.length ?? 0 }}</h2>
                        <small>Compétences enregistrées</small>
                    </div>
                </div>

                <div class="stat-card">
                    <div class="stat-icon orange">▤</div>
                    <div>
                        <span class="stat-label">Expériences</span>
                        <h2>{{ profile.experiences?.length ?? 0 }}</h2>
                        <small>Expériences professionnelles</small>
                    </div>
                </div>

                <div class="stat-card">
                    <div class="stat-icon green">◇</div>
                    <div>
                        <span class="stat-label">Projets</span>
                        <h2>{{ profile.projects?.length ?? 0 }}</h2>
                        <small>Projets enregistrés</small>
                    </div>
                </div>

            </div>

            <div class="content-grid">

                <section class="panel">
                    <div class="panel-header">
                        <div>
                            <h3>Mon profil freelance</h3>
                            <p>Les informations utilisées par votre agent IA</p>
                        </div>

                        <RouterLink to="/profile" class="text-link">
                            Modifier →
                        </RouterLink>
                    </div>

                    <div class="profile-summary">
                        <div class="profile-avatar">
                            {{ profile.title ? profile.title.charAt(0).toUpperCase() : 'F' }}
                        </div>

                        <div>
                            <h4>{{ profile.title || 'Titre professionnel non renseigné' }}</h4>
                            <p>{{ profile.location || 'Localisation non renseignée' }}</p>
                        </div>
                    </div>

                    <div class="profile-progress">
                        <div class="progress-heading">
                            <span>Complétude du profil</span>
                            <strong>{{ completion }}%</strong>
                        </div>

                        <div class="progress-track">
                            <div
                                class="progress-value"
                                :style="{ width: `${completion}%` }"
                            ></div>
                        </div>
                    </div>

                    <p class="profile-hint">
                        {{ completion < 100
                            ? 'Ajoutez les informations manquantes pour améliorer la pertinence des analyses.'
                            : 'Votre profil de base est complet. Vous pouvez poursuivre avec les offres freelance.'
                        }}
                    </p>
                </section>

                <section class="panel">
                    <div class="panel-header">
                        <div>
                            <h3>Prochaines étapes</h3>
                            <p>Construisez votre assistant freelance</p>
                        </div>
                    </div>

                    <div class="task-list">

                        <div class="task-item">
                            <span class="task-number">01</span>
                            <div class="task-description">
                                <strong>Compléter le profil</strong>
                                <p>Présentez votre spécialité et votre expérience.</p>
                            </div>
                            <RouterLink to="/profile" class="task-link">→</RouterLink>
                        </div>

                        <div class="task-item">
                            <span class="task-number">02</span>
                            <div class="task-description">
                                <strong>Ajouter les compétences</strong>
                                <p>Laravel, Vue.js, PHP et vos autres technologies.</p>
                            </div>
                            <span class="coming-soon">À venir</span>
                        </div>

                        <div class="task-item">
                            <span class="task-number">03</span>
                            <div class="task-description">
                                <strong>Analyser une mission</strong>
                                <p>Évaluer l'adéquation entre une offre et votre profil.</p>
                            </div>
                            <span class="coming-soon">À venir</span>
                        </div>

                    </div>
                </section>

            </div>

            <section class="ai-banner">
                <div class="ai-symbol">✦</div>

                <div>
                    <h3>Votre agent IA se prépare</h3>
                    <p>
                        L'objectif : analyser les offres, estimer leur compatibilité
                        avec vos compétences et vous aider à préparer les livrables.
                        Vous garderez le contrôle des actions importantes.
                    </p>
                </div>
            </section>

        </template>
    </div>
</template>

<script setup>
import { computed, onMounted, ref } from 'vue';
import api from '../services/api';

const loading = ref(true);
const error = ref('');
const profile = ref({
    title: '',
    bio: '',
    location: '',
    hourly_rate: null,
    years_experience: null,
    skills: [],
    experiences: [],
    projects: [],
});

const completion = computed(() => {
    const fields = [
        profile.value.title,
        profile.value.bio,
        profile.value.location,
        profile.value.hourly_rate,
        profile.value.years_experience,
    ];

    const completedFields = fields.filter(
        (value) => value !== null && value !== undefined
            && String(value).trim() !== ''
    ).length;

    return Math.round((completedFields / fields.length) * 100);
});

const loadDashboard = async () => {
    loading.value = true;
    error.value = '';

    try {
        const response = await api.get('/profile');
        profile.value = response.data.data;
    } catch (e) {
        console.error(e);
        error.value = 'Impossible de charger les informations du profil.';
    } finally {
        loading.value = false;
    }
};

onMounted(loadDashboard);
</script>

<style scoped>
.dashboard {
    max-width: 1500px;
    margin: 0 auto;
}

.welcome-section {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 24px;
    margin-bottom: 28px;
}

.welcome-section h1 {
    margin: 0 0 10px;
    font-size: 25px;
    color: #182230;
}

.welcome-section p,
.panel-header p {
    margin: 0;
    color: #7b8494;
    font-size: 13px;
    line-height: 1.7;
}

.primary-button {
    flex-shrink: 0;
    background: #4f46e5;
    color: white;
    text-decoration: none;
    padding: 12px 17px;
    border-radius: 9px;
    font-size: 12px;
    font-weight: 600;
}

.primary-button:hover {
    background: #4338ca;
}

.stats-grid {
    display: grid;
    grid-template-columns: repeat(4, minmax(0, 1fr));
    gap: 18px;
    margin-bottom: 24px;
}

.stat-card {
    display: flex;
    align-items: center;
    gap: 15px;
    padding: 22px;
    background: white;
    border: 1px solid #edf0f5;
    border-radius: 13px;
}

.stat-icon {
    display: grid;
    place-items: center;
    width: 45px;
    height: 45px;
    flex-shrink: 0;
    border-radius: 12px;
    font-size: 21px;
}

.purple { background: #eeedff; color: #594ee6; }
.blue { background: #e6f2ff; color: #2878d4; }
.orange { background: #fff0df; color: #d98220; }
.green { background: #e2f8ee; color: #1a9868; }

.stat-label {
    color: #7b8494;
    font-size: 12px;
}

.stat-card h2 {
    margin: 6px 0;
    font-size: 25px;
}

.stat-card small {
    color: #9aa3b2;
    font-size: 10px;
}

.content-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 22px;
    margin-bottom: 22px;
}

.panel {
    padding: 24px;
    background: white;
    border: 1px solid #edf0f5;
    border-radius: 13px;
}

.panel-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    gap: 15px;
    margin-bottom: 25px;
}

.panel-header h3,
.ai-banner h3 {
    margin: 0 0 7px;
    font-size: 16px;
}

.text-link {
    color: #4f46e5;
    text-decoration: none;
    font-size: 12px;
    white-space: nowrap;
}

.profile-summary {
    display: flex;
    align-items: center;
    gap: 14px;
    margin-bottom: 28px;
}

.profile-avatar {
    display: grid;
    place-items: center;
    width: 50px;
    height: 50px;
    border-radius: 13px;
    background: #eeedff;
    color: #4f46e5;
    font-size: 22px;
    font-weight: 700;
}

.profile-summary h4 {
    margin: 0 0 6px;
    font-size: 14px;
}

.profile-summary p {
    margin: 0;
    color: #8a94a6;
    font-size: 12px;
}

.progress-heading {
    display: flex;
    justify-content: space-between;
    margin-bottom: 10px;
    font-size: 12px;
}

.progress-heading strong {
    color: #4f46e5;
}

.progress-track {
    height: 8px;
    overflow: hidden;
    border-radius: 10px;
    background: #edf0f5;
}

.progress-value {
    height: 100%;
    border-radius: inherit;
    background: #5b50e8;
    transition: width 0.3s ease;
}

.profile-hint {
    color: #7b8494;
    font-size: 12px;
    line-height: 1.7;
    margin: 17px 0 0;
}

.task-list {
    display: flex;
    flex-direction: column;
}

.task-item {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 16px 0;
    border-bottom: 1px solid #edf0f5;
}

.task-item:first-child {
    padding-top: 0;
}

.task-item:last-child {
    padding-bottom: 0;
    border-bottom: none;
}

.task-number {
    display: grid;
    place-items: center;
    width: 35px;
    height: 35px;
    flex-shrink: 0;
    border-radius: 9px;
    background: #f0f2f8;
    color: #616b7c;
    font-size: 11px;
    font-weight: 700;
}

.task-description {
    flex: 1;
}

.task-description strong {
    font-size: 12px;
}

.task-description p {
    margin: 5px 0 0;
    color: #8a94a6;
    font-size: 11px;
    line-height: 1.5;
}

.task-link {
    color: #4f46e5;
    font-size: 20px;
    text-decoration: none;
}

.coming-soon {
    padding: 5px 7px;
    border-radius: 6px;
    background: #f0f2f8;
    color: #7b8494;
    font-size: 9px;
    white-space: nowrap;
}

.ai-banner {
    display: flex;
    align-items: flex-start;
    gap: 17px;
    padding: 24px;
    border: 1px solid #e5e3ff;
    border-radius: 13px;
    background: #f2f1ff;
}

.ai-symbol {
    display: grid;
    place-items: center;
    width: 42px;
    height: 42px;
    flex-shrink: 0;
    background: white;
    border-radius: 12px;
    color: #4f46e5;
    font-size: 25px;
}

.ai-banner h3 {
    color: #37308e;
}

.ai-banner p {
    margin: 0;
    max-width: 850px;
    color: #66628d;
    font-size: 12px;
    line-height: 1.8;
}

.loading {
    padding: 25px 0;
    color: #7b8494;
}

.error-message {
    padding: 18px;
    border-radius: 9px;
    background: #fef3f2;
    color: #b42318;
}

.error-message button {
    margin-left: 12px;
    padding: 7px 12px;
    cursor: pointer;
}

@media (max-width: 1100px) {
    .stats-grid {
        grid-template-columns: repeat(2, minmax(0, 1fr));
    }

    .content-grid {
        grid-template-columns: 1fr;
    }
}

@media (max-width: 650px) {
    .welcome-section {
        align-items: flex-start;
        flex-direction: column;
    }

    .welcome-section h1 {
        font-size: 21px;
    }

    .stats-grid {
        grid-template-columns: 1fr;
    }

    .stat-card {
        padding: 17px;
    }

    .panel {
        padding: 18px;
    }

    .ai-banner {
        padding: 18px;
    }
}
</style>