
<template>
    <div class="app-layout">
        <aside class="sidebar">
            <div class="brand">
                <span class="brand-icon">AI</span>
                <div>
                    <strong>Freelance Agent</strong>
                    <small>Assistant intelligent</small>
                </div>
            </div>

            <nav class="navigation">
                <p class="nav-label">MENU PRINCIPAL</p>

                <RouterLink to="/" class="nav-link">
                    <span>▦</span>
                    Tableau de bord
                </RouterLink>

                <RouterLink to="/profile" class="nav-link">
                    <span>♙</span>
                    Mon profil
                </RouterLink>

                <RouterLink to="/skills" class="nav-link">
                    <span>⚙</span>
                    Mes compétences
                </RouterLink>

                <router-link to="/experiences" class="nav-link">
                    <span>🔧</span>
                    Expériences
                </router-link>

                <a href="#" class="nav-link disabled" @click.prevent>
                    <span>⌕</span>
                    Offres freelance
                    <small>Bientôt</small>
                </a>

                <a href="#" class="nav-link disabled" @click.prevent>
                    <span>◈</span>
                    Analyses IA
                    <small>Bientôt</small>
                </a>
            </nav>

            <div class="sidebar-footer">
                <div class="user-avatar">
                    {{ userInitial }}
                </div>

                <div class="user-info">
                    <strong>{{ user?.name || 'Utilisateur' }}</strong>
                    <small>{{ user?.email || '' }}</small>
                </div>

                <button
                    class="logout-button"
                    title="Se déconnecter"
                    :disabled="loggingOut"
                    @click="logout"
                >
                    ↪
                </button>
            </div>
        </aside>

        <main class="main-area">
            <header class="topbar">
                <div>
                    <span class="topbar-label">ESPACE PERSONNEL</span>
                    <h2>{{ pageTitle }}</h2>
                </div>

                <span class="status">
                    <span class="status-dot"></span>
                    Agent prêt
                </span>
            </header>

            <section class="page-content">
                <div v-if="loadingUser" class="loading">
                    Chargement de votre compte...
                </div>

                <div v-else>
                    <div v-if="userError" class="error-message">
                        {{ userError }}
                    </div>

                    <RouterView v-else />
                </div>
            </section>
        </main>
    </div>
</template>

<script setup>
import { computed, onMounted, ref } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import auth from '../services/auth';

const route = useRoute();
const router = useRouter();

const user = ref(null);
const loadingUser = ref(true);
const loggingOut = ref(false);
const userError = ref('');

const pageTitle = computed(() => {
    if (route.path === '/profile') return 'Mon profil';
    if (route.path === '/skills') return 'Mes compétences';

    return 'Tableau de bord';
});

const userInitial = computed(() => {
    return user.value?.name?.charAt(0)?.toUpperCase() || 'U';
});

onMounted(async () => {
    try {
        user.value = await auth.me();

        if (!user.value) {
            await router.replace('/login');
        }
    } catch (error) {
        console.error(error);
        userError.value = 'Impossible de charger votre compte.';
    } finally {
        loadingUser.value = false;
    }
});

const logout = async () => {
    loggingOut.value = true;

    try {
        await auth.logout();
        await router.replace('/login');
    } catch (error) {
        console.error(error);
        userError.value = 'La déconnexion a rencontré un problème.';
    } finally {
        loggingOut.value = false;
    }
};
</script>

<style scoped>
.app-layout {
    min-height: 100vh;
    display: flex;
    background: #f5f7fb;
    color: #182230;
    font-family: Inter, Arial, sans-serif;
}

.sidebar {
    width: 255px;
    flex-shrink: 0;
    background: #fff;
    border-right: 1px solid #e8edf4;
    display: flex;
    flex-direction: column;
}

.brand {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 25px 20px;
    border-bottom: 1px solid #edf0f5;
}

.brand-icon {
    display: grid;
    place-items: center;
    width: 42px;
    height: 42px;
    background: #4f46e5;
    color: white;
    font-weight: 800;
    border-radius: 12px;
}

.brand strong,
.user-info strong {
    display: block;
    font-size: 13px;
}

.brand small,
.user-info small {
    display: block;
    color: #8a94a6;
    font-size: 11px;
    margin-top: 5px;
}

.navigation {
    padding: 25px 13px;
    flex: 1;
}

.nav-label {
    padding: 0 10px;
    color: #9aa3b2;
    font-size: 10px;
    font-weight: 700;
    letter-spacing: 1px;
}

.nav-link {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 13px 12px;
    margin: 6px 0;
    border-radius: 9px;
    text-decoration: none;
    color: #697386;
    font-size: 13px;
}

.nav-link > span {
    width: 20px;
    text-align: center;
}

.nav-link.router-link-active {
    background: #eeedff;
    color: #4f46e5;
    font-weight: 600;
}

.nav-link.disabled {
    opacity: 0.6;
    cursor: not-allowed;
}

.nav-link small {
    margin-left: auto;
    font-size: 9px;
}

.sidebar-footer {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 18px 14px;
    border-top: 1px solid #edf0f5;
}

.user-avatar {
    width: 35px;
    height: 35px;
    display: grid;
    place-items: center;
    background: #e0e7ff;
    color: #4338ca;
    border-radius: 50%;
    font-weight: 700;
}

.user-info {
    flex: 1;
    min-width: 0;
}

.user-info small {
    overflow-wrap: anywhere;
}

.logout-button {
    border: none;
    background: transparent;
    font-size: 22px;
    color: #7b8494;
    cursor: pointer;
}

.main-area {
    flex: 1;
    min-width: 0;
}

.topbar {
    background: white;
    border-bottom: 1px solid #e8edf4;
    padding: 22px 32px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 15px;
}

.topbar-label {
    color: #8a94a6;
    font-size: 10px;
    letter-spacing: 1px;
}

.topbar h2 {
    margin: 7px 0 0;
    font-size: 21px;
}

.status {
    display: flex;
    align-items: center;
    gap: 7px;
    color: #16835d;
    background: #e9f8f0;
    padding: 8px 12px;
    border-radius: 20px;
    font-size: 11px;
}

.status-dot {
    width: 7px;
    height: 7px;
    border-radius: 50%;
    background: #20b779;
}

.page-content {
    padding: 30px;
}

.loading {
    color: #697386;
    padding: 25px 0;
}

.error-message {
    color: #b42318;
    background: #fef3f2;
    padding: 14px;
    border-radius: 8px;
}

@media (max-width: 760px) {
    .sidebar {
        width: 76px;
    }

    .brand {
        justify-content: center;
        padding: 20px 8px;
    }

    .brand > div,
    .nav-label,
    .nav-link:not(.router-link-active),
    .nav-link.router-link-active {
        font-size: 0;
    }

    .navigation {
        padding: 20px 8px;
    }

    .nav-link {
        justify-content: center;
        padding: 14px 5px;
    }

    .nav-link > span {
        font-size: 19px;
    }

    .sidebar-footer {
        justify-content: center;
        flex-wrap: wrap;
        padding: 12px 5px;
    }

    .user-info {
        display: none;
    }

    .topbar {
        padding: 18px;
    }

    .page-content {
        padding: 18px;
    }
}
</style>