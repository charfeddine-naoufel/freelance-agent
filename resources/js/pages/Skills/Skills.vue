
<template>
    <div class="skills-page">
        <div class="page-heading">
            <div>
                <h1>Mes compétences</h1>
                <p>
                    Gérez les technologies et savoir-faire que votre agent IA
                    utilisera pour évaluer les offres freelance.
                </p>
            </div>

            <button class="primary-button" @click="openCreateForm">
                + Ajouter une compétence
            </button>
        </div>

        <div v-if="error" class="alert error">
            {{ error }}
        </div>

        <div v-if="success" class="alert success">
            {{ success }}
        </div>

        <section v-if="showForm" class="panel form-panel">
            <div class="panel-heading">
                <h2>{{ editingId ? 'Modifier la compétence' : 'Nouvelle compétence' }}</h2>

                <button class="close-button" @click="closeForm" aria-label="Fermer">
                    ×
                </button>
            </div>
            
            <form @submit.prevent="saveSkill">
                <div class="form-grid">
                    <div class="form-group">
                        <label for="skill-name">Nom de la compétence *</label>

                        <input
                            id="skill-name"
                            v-model.trim="form.name"
                            type="text"
                            maxlength="255"
                            placeholder="Ex. Laravel"
                            required
                        >
                    </div>

                    <div class="form-group">
                        <label for="skill-level">Niveau de maîtrise</label>

                        <select id="skill-level" v-model="form.level">
                            <option value="">Non renseigné</option>
                            <option value="1">1 — Débutant</option>
                            <option value="2">2 — Intermédiaire</option>
                            <option value="3">3 — Bon niveau</option>
                            <option value="4">4 — Très bon niveau</option>
                            <option value="5">5 — Expert</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="skill-years">Années d'expérience</label>

                        <input
                            id="skill-years"
                            v-model="form.years_experience"
                            type="number"
                            min="0"
                            step="1"
                            placeholder="Ex. 3"
                        >
                    </div>

                    <div class="form-group full-width">
                        <label for="skill-description">Description</label>

                        <textarea
                            id="skill-description"
                            v-model.trim="form.description"
                            rows="4"
                            placeholder="Décrivez votre expérience avec cette technologie..."
                        ></textarea>
                    </div>
                </div>

                <div class="form-actions">
                    <button
                        type="button"
                        class="secondary-button"
                        @click="closeForm"
                    >
                        Annuler
                    </button>

                    <button
                        type="submit"
                        class="primary-button"
                        :disabled="saving"
                    >
                        {{ saving ? 'Enregistrement...' : 'Enregistrer' }}
                    </button>
                </div>
            </form>
        </section>

        <div class="list-heading">
            <div>
                <h2>Compétences enregistrées</h2>
                <p>{{ skills.length }} compétence(s)</p>
            </div>
        </div>

        <div v-if="loading" class="panel loading">
            Chargement des compétences...
        </div>

        <div v-else-if="skills.length === 0" class="panel empty-state">
            <div class="empty-icon">⚙</div>
            <h3>Aucune compétence pour le moment</h3>
            <p>Ajoutez vos premières compétences pour enrichir votre profil.</p>

            <button class="primary-button" @click="openCreateForm">
                Ajouter ma première compétence
            </button>
        </div>

        <div v-else class="skills-grid">
            <article
                v-for="skill in skills"
                :key="skill.id"
                class="panel skill-card"
            >
                <div class="skill-card-top">
                    <div class="skill-avatar">
                        {{ skill.name.charAt(0).toUpperCase() }}
                    </div>

                    <div class="skill-actions">
                        <button
                            class="icon-button"
                            title="Modifier"
                            @click="openEditForm(skill)"
                        >
                            ✎
                        </button>

                        <button
                            class="icon-button delete-button"
                            title="Supprimer"
                            :disabled="deletingId === skill.id"
                            @click="deleteSkill(skill)"
                        >
                            {{ deletingId === skill.id ? '...' : '×' }}
                        </button>
                    </div>
                </div>

                <h3>{{ skill.name }}</h3>

                <div class="skill-level">
                    <span>Niveau</span>

                    <strong>
                        {{ skill.level ? `${skill.level}/5` : 'Non renseigné' }}
                    </strong>
                </div>

                <div class="level-track">
                    <div
                        class="level-value"
                        :style="{ width: `${(Number(skill.level) || 0) * 20}%` }"
                    ></div>
                </div>

                <p class="skill-years">
                    Expérience :
                    <strong>
                        {{ skill.years_experience != null
                            ? `${skill.years_experience} an(s)`
                            : 'Non renseignée'
                        }}
                    </strong>
                </p>

                <p v-if="skill.description" class="skill-description">
                    {{ skill.description }}
                </p>
            </article>
        </div>
    </div>
</template>

<script setup>
import { onMounted, reactive, ref } from 'vue';
import api from '../../services/api';

const skills = ref([]);
const loading = ref(true);
const saving = ref(false);
const deletingId = ref(null);

const showForm = ref(false);
const editingId = ref(null);

const error = ref('');
const success = ref('');

const emptyForm = () => ({
    name: '',
    level: '',
    years_experience: '',
    description: '',
});


const editingSkillId = ref(null);

const editForm = ref({
    name: '',
    level: '',
    years_experience: 0,
    description: '',
});

const updating = ref(false);

const form = reactive(emptyForm());

const clearMessages = () => {
    error.value = '';
    success.value = '';
};

const loadSkills = async () => {
    loading.value = true;
    error.value = '';

    try {
        const response = await api.get('/skills');
        skills.value = response.data.data;
    } catch (e) {
        console.error(e);
        error.value = 'Impossible de charger les compétences.';
    } finally {
        loading.value = false;
    }
};

const openCreateForm = () => {
    clearMessages();
    editingId.value = null;
    Object.assign(form, emptyForm());
    showForm.value = true;
};

const openEditForm = (skill) => {
    clearMessages();

    editingId.value = skill.id;

    Object.assign(form, {
        name: skill.name,
        level: skill.level == null ? '' : String(skill.level),
        years_experience: skill.years_experience == null
            ? ''
            : String(skill.years_experience),
        description: skill.description || '',
    });

    showForm.value = true;
};

const closeForm = () => {
    showForm.value = false;
    editingId.value = null;
    Object.assign(form, emptyForm());
};

const saveSkill = async () => {
    clearMessages();
    saving.value = true;

    const payload = {
        name: form.name,
        level: form.level === '' ? null : Number(form.level),
        years_experience: form.years_experience === ''
            ? null
            : Number(form.years_experience),
        description: form.description || null,
    };

    try {
        if (editingId.value !== null) {
            await api.put(`/skills/${editingId.value}`, payload);
            success.value = 'Compétence modifiée avec succès.';
        } else {
            await api.post('/skills', payload);
            success.value = 'Compétence ajoutée avec succès.';
        }

        closeForm();
        await loadSkills();
    } catch (e) {
        console.error(e);

        if (e.response?.status === 422) {
            const errors = e.response.data.errors || {};

            error.value = Object.values(errors)
                .flat()
                .join(' ');
        } else {
            error.value = 'Impossible d’enregistrer cette compétence.';
        }
    } finally {
        saving.value = false;
    }
};




const editSkill = async (skill) => {
    const name = window.prompt(
        'Nom de la compétence :',
        skill.name
    );

    if (name === null) return;

    if (!name.trim()) {
        error.value = 'Le nom de la compétence est obligatoire.';
        return;
    }

    const levelInput = window.prompt(
        'Niveau de 1 à 5 (laisser vide si non défini) :',
        skill.level ?? ''
    );

    if (levelInput === null) return;

    const experienceInput = window.prompt(
        "Années d'expérience :",
        skill.years_experience ?? 0
    );

    if (experienceInput === null) return;

    const description = window.prompt(
        'Description :',
        skill.description ?? ''
    );

    if (description === null) return;

    const level = levelInput.trim() === ''
        ? null
        : Number(levelInput);

    const years = Number(experienceInput);

    if (
        level !== null &&
        (!Number.isInteger(level) || level < 1 || level > 5)
    ) {
        error.value = 'Le niveau doit être compris entre 1 et 5.';
        return;
    }

    if (!Number.isInteger(years) || years < 0) {
        error.value = 'Les années d’expérience doivent être un entier positif ou nul.';
        return;
    }

    error.value = '';

    try {
        const response = await api.put(`/skills/${skill.id}`, {
            name: name.trim(),
            level,
            years_experience: years,
            description: description.trim() || null,
        });

        const index = skills.value.findIndex(
            (item) => item.id === skill.id
        );

        if (index !== -1) {
            skills.value[index] = response.data.data;
        }
    } catch (e) {
        error.value =
            e.response?.data?.message ||
            'Impossible de modifier la compétence.';
    }
};

const deleteSkill = async (skill) => {
    const confirmed = window.confirm(
        `Voulez-vous vraiment supprimer la compétence "${skill.name}" ?`
    );

    if (!confirmed) return;

    error.value = '';

    try {
        await api.delete(`/skills/${skill.id}`);

        skills.value = skills.value.filter(
            (item) => item.id !== skill.id
        );
    } catch (e) {
        error.value =
            e.response?.data?.message ||
            'Impossible de supprimer la compétence.';
    }
};

onMounted(loadSkills);
</script>

<style scoped>
.skills-page {
    max-width: 1400px;
    margin: 0 auto;
    color: #182230;
}

.page-heading,
.list-heading,
.panel-heading {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 16px;
}

.page-heading {
    margin-bottom: 28px;
}

.page-heading h1 {
    margin: 0 0 10px;
    font-size: 25px;
}

.page-heading p,
.list-heading p {
    margin: 0;
    color: #7b8494;
    font-size: 13px;
    line-height: 1.7;
}

.primary-button,
.secondary-button {
    display: inline-flex;
    justify-content: center;
    align-items: center;
    padding: 11px 16px;
    border-radius: 8px;
    font-size: 12px;
    font-weight: 600;
    cursor: pointer;
}

.primary-button {
    color: #fff;
    background: #4f46e5;
    border: 1px solid #4f46e5;
}

.primary-button:hover {
    background: #4338ca;
}

.primary-button:disabled {
    opacity: 0.6;
    cursor: not-allowed;
}

.secondary-button {
    color: #475467;
    background: white;
    border: 1px solid #d8dee8;
}

.panel {
    background: white;
    border: 1px solid #e8edf4;
    border-radius: 12px;
    padding: 23px;
}

.form-panel {
    margin-bottom: 30px;
}

.panel-heading {
    margin-bottom: 23px;
}

.panel-heading h2,
.list-heading h2 {
    margin: 0;
    font-size: 16px;
}

.close-button {
    background: transparent;
    border: none;
    font-size: 25px;
    color: #7b8494;
    cursor: pointer;
}

.form-grid {
    display: grid;
    grid-template-columns: repeat(2, minmax(0, 1fr));
    gap: 20px;
}

.form-group {
    display: flex;
    flex-direction: column;
    gap: 8px;
}

.form-group label {
    font-size: 12px;
    font-weight: 600;
}

.form-group input,
.form-group select,
.form-group textarea {
    box-sizing: border-box;
    width: 100%;
    border: 1px solid #dce2eb;
    border-radius: 8px;
    padding: 11px 12px;
    font: inherit;
    font-size: 13px;
    color: #182230;
    background: white;
}

.form-group input:focus,
.form-group select:focus,
.form-group textarea:focus {
    outline: 2px solid #e0deff;
    border-color: #756cf0;
}

.full-width {
    grid-column: 1 / -1;
}

.form-actions {
    display: flex;
    justify-content: flex-end;
    gap: 10px;
    margin-top: 23px;
}

.list-heading {
    margin-bottom: 16px;
}

.list-heading p {
    margin-top: 6px;
}

.skills-grid {
    display: grid;
    grid-template-columns: repeat(3, minmax(0, 1fr));
    gap: 18px;
}

.skill-card {
    min-width: 0;
}

.skill-card-top {
    display: flex;
    align-items: center;
    justify-content: space-between;
}

.skill-avatar {
    display: grid;
    place-items: center;
    width: 43px;
    height: 43px;
    border-radius: 11px;
    background: #eeedff;
    color: #4f46e5;
    font-size: 19px;
    font-weight: 700;
}

.skill-actions {
    display: flex;
    gap: 6px;
}

.icon-button {
    display: grid;
    place-items: center;
    width: 31px;
    height: 31px;
    border: 1px solid #e8edf4;
    background: white;
    color: #667085;
    border-radius: 7px;
    font-size: 17px;
    cursor: pointer;
}

.icon-button:disabled {
    opacity: 0.5;
    cursor: not-allowed;
}

.delete-button {
    color: #d92d20;
}

.skill-card h3 {
    margin: 18px 0;
    font-size: 16px;
    overflow-wrap: anywhere;
}

.skill-level {
    display: flex;
    justify-content: space-between;
    gap: 10px;
    color: #7b8494;
    font-size: 12px;
}

.skill-level strong {
    color: #344054;
}

.level-track {
    height: 6px;
    margin-top: 10px;
    overflow: hidden;
    border-radius: 8px;
    background: #edf0f5;
}

.level-value {
    height: 100%;
    background: #5b50e8;
    border-radius: inherit;
}

.skill-years {
    margin: 18px 0 0;
    color: #7b8494;
    font-size: 12px;
}

.skill-years strong {
    color: #344054;
}

.skill-description {
    margin: 13px 0 0;
    color: #667085;
    font-size: 12px;
    line-height: 1.7;
    white-space: pre-wrap;
    overflow-wrap: anywhere;
}

.empty-state {
    padding: 50px 20px;
    text-align: center;
}

.empty-icon {
    margin-bottom: 15px;
    font-size: 35px;
}

.empty-state h3 {
    margin: 0 0 10px;
    font-size: 16px;
}

.empty-state p {
    margin: 0 0 22px;
    color: #7b8494;
    font-size: 13px;
}

.loading {
    color: #7b8494;
}

.alert {
    padding: 13px 15px;
    border-radius: 8px;
    margin-bottom: 18px;
    font-size: 13px;
}

.error {
    color: #b42318;
    background: #fef3f2;
}

.success {
    color: #067647;
    background: #ecfdf3;
}

@media (max-width: 1050px) {
    .skills-grid {
        grid-template-columns: repeat(2, minmax(0, 1fr));
    }
}

@media (max-width: 650px) {
    .page-heading {
        align-items: flex-start;
        flex-direction: column;
    }

    .form-grid,
    .skills-grid {
        grid-template-columns: 1fr;
    }

    .panel {
        padding: 17px;
    }

    .full-width {
        grid-column: auto;
    }
}
</style>