<template>
    <div class="experiences-page">
        <div class="page-header">
            <div>
                <h1>Expériences professionnelles</h1>
                <p>
                    Présente ton parcours professionnel pour permettre à
                    l'agent IA de mieux évaluer les opportunités freelance.
                </p>
            </div>
        </div>

        <!-- Chargement -->
        <div v-if="loading" class="loading">
            Chargement des expériences...
        </div>

        <!-- Erreur -->
        <div v-if="error" class="error-message">
            {{ error }}
        </div>

        <!-- Aucune expérience -->
        <div
            v-if="!loading && !error && experiences.length === 0"
            class="empty-state"
        >
            <h3>Aucune expérience professionnelle</h3>

            <p>
                Ajoute tes expériences professionnelles pour enrichir
                ton profil freelance.
            </p>
        </div>

    <div class="experience-form-container">

    <h2>Ajouter une expérience</h2>

    <form
        @submit.prevent="createExperience"
        class="experience-form"
    >

        <div class="form-group">
            <label for="company">
                Entreprise
            </label>

            <input
                id="company"
                v-model.trim="form.company"
                type="text"
                maxlength="255"
                placeholder="Ex. ABC Technologies"
            >
        </div>

        <div class="form-group">
            <label for="position">
                Poste
            </label>

            <input
                id="position"
                v-model.trim="form.position"
                type="text"
                maxlength="255"
                placeholder="Ex. Développeur Laravel"
                required
            >
        </div>

        <div class="form-group">
            <label for="started_at">
                Date de début
            </label>

            <input
                id="started_at"
                v-model="form.started_at"
                type="date"
            >
        </div>

        <div class="form-group">
            <label for="ended_at">
                Date de fin
            </label>

            <input
                id="ended_at"
                v-model="form.ended_at"
                type="date"
                :disabled="form.is_current"
            >
        </div>

        <div class="form-group checkbox-group">

            <label>
                <input
                    v-model="form.is_current"
                    type="checkbox"
                >

                Je travaille actuellement dans cette entreprise
            </label>

        </div>

        <div class="form-group">
            <label for="description">
                Description
            </label>

            <textarea
                id="description"
                v-model.trim="form.description"
                rows="5"
                placeholder="Décris tes missions, responsabilités et réalisations..."
            ></textarea>
            </div>
            

        <button
            type="submit"
            :disabled="saving"
        >
            {{
                saving
                    ? 'Enregistrement...'
                    : 'Ajouter l’expérience'
            }}
        </button>

    </form>
<!-- message formulaire -->
<p
    v-if="success"
    class="success-message"
>
    {{ success }}
</p>

<p
    v-if="error"
    class="error-message"
>
    {{ error }}
</p>
<!-- message formulaire -->
    <div
    v-if="editingExperienceId !== null"
    class="experience-form-container"
>
    <h2>Modifier l'expérience</h2>

    <form
        @submit.prevent="updateExperience"
        class="experience-form"
    >

        <div class="form-group">
            <label for="edit-company">
                Entreprise
            </label>

            <input
                id="edit-company"
                v-model.trim="editForm.company"
                type="text"
                maxlength="255"
                placeholder="Ex. ABC Technologies"
            >
        </div>

        <div class="form-group">
            <label for="edit-position">
                Poste
            </label>

            <input
                id="edit-position"
                v-model.trim="editForm.position"
                type="text"
                maxlength="255"
                required
                placeholder="Ex. Développeur Laravel"
            >
        </div>

        <div class="form-group">
            <label for="edit-started-at">
                Date de début
            </label>

            <input
                id="edit-started-at"
                v-model="editForm.started_at"
                type="date"
            >
        </div>

        <div class="form-group">
            <label for="edit-ended-at">
                Date de fin
            </label>

            <input
                id="edit-ended-at"
                v-model="editForm.ended_at"
                type="date"
                :disabled="editForm.is_current"
            >
        </div>

        <div class="form-group checkbox-group">
            <label>
                <input
                    v-model="editForm.is_current"
                    type="checkbox"
                >

                Je travaille actuellement dans cette entreprise
            </label>
        </div>

        <div class="form-group">
            <label for="edit-description">
                Description
            </label>

            <textarea
                id="edit-description"
                v-model.trim="editForm.description"
                rows="5"
                placeholder="Décris tes missions, responsabilités et réalisations..."
            ></textarea>
        </div>

        <div class="form-actions">

            <button
                type="submit"
                :disabled="updating"
            >
                {{
                    updating
                        ? 'Enregistrement...'
                        : 'Enregistrer les modifications'
                }}
            </button>

            <button
                type="button"
                @click="cancelEdit"
                :disabled="updating"
            >
                Annuler
            </button>

        </div>

    </form>
</div>

    </div>
        <!-- Liste des expériences -->
        <div
            v-if="!loading && experiences.length > 0"
            class="experiences-list"
        >
            <div
                v-for="experience in experiences"
                :key="experience.id"
                class="experience-card"
            >
                <div class="experience-header">
                    <div>
                        <h2>{{ experience.position }}</h2>

                        <p
                            v-if="experience.company"
                            class="company"
                        >
                            {{ experience.company }}
                        </p>
                    </div>

                    <span
                        v-if="experience.is_current"
                        class="current-badge"
                    >
                        Poste actuel
                    </span>
                </div>

                <div class="experience-dates">
                    <span>
                        {{ formatDate(experience.started_at) }}
                    </span>

                    <span> — </span>

                    <span v-if="experience.is_current">
                        Aujourd'hui
                    </span>

                    <span v-else>
                        {{ formatDate(experience.ended_at) }}
                    </span>
                </div>

                <p
                    v-if="experience.description"
                    class="description"
                >
                    {{ experience.description }}
                </p>
                <div class="experience-actions">

    <button
        type="button"
        @click="startEdit(experience)"
    >
        Modifier
    </button>

    <button
        type="button"
        @click="deleteExperience(experience)"
    >
        Supprimer
    </button>

</div>
            </div>
            
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import api from '../../services/api';

const experiences = ref([]);
const loading = ref(false);
const error = ref('');

const loadExperiences = async () => {
    loading.value = true;
    error.value = '';

    try {
        const response = await api.get('/experiences');

        if (
            response.data.success &&
            Array.isArray(response.data.data)
        ) {
            experiences.value = response.data.data;
        } else {
            experiences.value = [];

            error.value =
                'Le format des expériences reçu est incorrect.';
        }
    } catch (e) {
        console.error(
            'Erreur chargement expériences :',
            e
        );

        error.value =
            e.response?.data?.message ||
            'Impossible de charger les expériences.';
    } finally {
        loading.value = false;
    }
};

const formatDate = (date) => {
    if (!date) {
        return 'Date inconnue';
    }

    return new Date(date).toLocaleDateString(
        'fr-FR',
        {
            month: 'long',
            year: 'numeric',
        }
    );
};
const form = ref({
    company: '',
    position: '',
    description: '',
    started_at: '',
    ended_at: '',
    is_current: false,
});

const saving = ref(false);
const success = ref('');

const createExperience = async () => {
    saving.value = true;
    error.value = '';
    success.value = '';

    try {
        const response = await api.post('/experiences', {
            company: form.value.company.trim() || null,
            position: form.value.position.trim(),
            description: form.value.description.trim() || null,
            started_at: form.value.started_at || null,
            ended_at: form.value.is_current
                ? null
                : (form.value.ended_at || null),
            is_current: form.value.is_current,
        });

        if (response.data.success) {
            experiences.value.unshift(response.data.data);

            success.value =
                'Expérience ajoutée avec succès.';

            form.value = {
                company: '',
                position: '',
                description: '',
                started_at: '',
                ended_at: '',
                is_current: false,
            };
        }
    } catch (e) {
        console.error(
            'Erreur ajout expérience :',
            e
        );

        if (e.response?.status === 422) {
            error.value = Object.values(
                e.response.data.errors || {}
            ).flat().join(' ');
        } else {
            error.value =
                e.response?.data?.message ||
                'Impossible d’ajouter l’expérience.';
        }
    } finally {
        saving.value = false;
    }
};

const editingExperienceId = ref(null);

const editForm = ref({
    company: '',
    position: '',
    description: '',
    started_at: '',
    ended_at: '',
    is_current: false,
});

const updating = ref(false);

const startEdit = (experience) => {
    editingExperienceId.value = experience.id;

    editForm.value = {
        company: experience.company ?? '',
        position: experience.position ?? '',
        description: experience.description ?? '',
        started_at: experience.started_at
            ? experience.started_at.substring(0, 10)
            : '',
        ended_at: experience.ended_at
            ? experience.ended_at.substring(0, 10)
            : '',
        is_current: Boolean(experience.is_current),
    };

    error.value = '';
    success.value = '';
};

const cancelEdit = () => {
    editingExperienceId.value = null;

    editForm.value = {
        company: '',
        position: '',
        description: '',
        started_at: '',
        ended_at: '',
        is_current: false,
    };
};

const updateExperience = async () => {
    if (!editForm.value.position.trim()) {
        error.value = 'Le poste est obligatoire.';
        return;
    }

    updating.value = true;
    error.value = '';
    success.value = '';

    try {
        const response = await api.put(
            `/experiences/${editingExperienceId.value}`,
            {
                company:
                    editForm.value.company.trim() || null,

                position:
                    editForm.value.position.trim(),

                description:
                    editForm.value.description.trim() || null,

                started_at:
                    editForm.value.started_at || null,

                ended_at:
                    editForm.value.is_current
                        ? null
                        : (
                            editForm.value.ended_at || null
                        ),

                is_current:
                    editForm.value.is_current,
            }
        );

        const index = experiences.value.findIndex(
            experience =>
                experience.id === editingExperienceId.value
        );

        if (index !== -1) {
            experiences.value[index] =
                response.data.data;
        }

        success.value =
            'Expérience modifiée avec succès.';

        cancelEdit();

    } catch (e) {
        console.error(
            'Erreur modification expérience :',
            e
        );

        if (e.response?.status === 422) {
            error.value = Object.values(
                e.response.data.errors || {}
            ).flat().join(' ');
        } else {
            error.value =
                e.response?.data?.message ||
                'Impossible de modifier l’expérience.';
        }
    } finally {
        updating.value = false;
    }
};

const deleteExperience = async (experience) => {
    const confirmed = window.confirm(
        `Voulez-vous vraiment supprimer l'expérience "${experience.position}" ?`
    );

    if (!confirmed) {
        return;
    }

    error.value = '';
    success.value = '';

    try {
        await api.delete(
            `/experiences/${experience.id}`
        );

        experiences.value =
            experiences.value.filter(
                item => item.id !== experience.id
            );

        success.value =
            'Expérience supprimée avec succès.';

    } catch (e) {
        console.error(
            'Erreur suppression expérience :',
            e
        );

        error.value =
            e.response?.data?.message ||
            'Impossible de supprimer l’expérience.';
    }
};



onMounted(() => {
    loadExperiences();
});
</script>

<style scoped>
.experiences-page {
    padding: 24px;
}

.page-header {
    margin-bottom: 30px;
}

.page-header h1 {
    margin-bottom: 8px;
}

.page-header p {
    color: #666;
}

.loading {
    padding: 20px;
}

.error-message {
    padding: 15px;
    margin-bottom: 20px;
    border-radius: 6px;
}

.empty-state {
    padding: 40px;
    text-align: center;
}

.experiences-list {
    display: flex;
    flex-direction: column;
    gap: 20px;
}

.experience-card {
    padding: 24px;
    border: 1px solid #ddd;
    border-radius: 10px;
    background: #fff;
}

.experience-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    gap: 20px;
}

.experience-header h2 {
    margin: 0 0 5px;
}

.company {
    margin: 0;
    font-weight: 600;
}

.current-badge {
    padding: 5px 10px;
    border-radius: 20px;
    font-size: 13px;
    white-space: nowrap;
}

.experience-dates {
    margin-top: 15px;
    font-size: 14px;
}

.description {
    margin-top: 15px;
    line-height: 1.6;
}

.experience-form-container {
    margin-bottom: 35px;
    padding: 25px;
    border: 1px solid #ddd;
    border-radius: 10px;
    background: #fff;
}

.experience-form {
    display: flex;
    flex-direction: column;
    gap: 18px;
}

.form-group {
    display: flex;
    flex-direction: column;
    gap: 7px;
}

.form-group label {
    font-weight: 600;
}

.form-group input,
.form-group textarea {
    padding: 10px 12px;
    border: 1px solid #ccc;
    border-radius: 6px;
    font-size: 15px;
}

.form-group textarea {
    resize: vertical;
}

.checkbox-group {
    flex-direction: row;
    align-items: center;
}

.checkbox-group label {
    display: flex;
    align-items: center;
    gap: 8px;
    font-weight: normal;
}

.experience-form button {
    width: fit-content;
    padding: 10px 20px;
    border: none;
    border-radius: 6px;
    cursor: pointer;
}

.experience-form button:disabled {
    opacity: 0.6;
    cursor: not-allowed;
}

.success-message {
    margin-top: 15px;
    padding: 12px;
}

.error-message {
    margin-top: 15px;
    padding: 12px;
}

.experience-actions {
    display: flex;
    gap: 10px;
    margin-top: 20px;
}

.experience-actions button {
    padding: 8px 15px;
    border: none;
    border-radius: 6px;
    cursor: pointer;
}

.form-actions {
    display: flex;
    gap: 10px;
    margin-top: 10px;
}

.form-actions button {
    padding: 10px 20px;
    border: none;
    border-radius: 6px;
    cursor: pointer;
}
</style>