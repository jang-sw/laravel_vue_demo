<script setup>
import { computed, onMounted, ref } from "vue";
import { callApi } from "./apiRequest";

const user = ref(null);
const posts = ref([]);

const authMode = ref("login");

const authForm = ref({
    name: "",
    email: "",
    password: "",
    password_confirmation: "",
});

const postForm = ref({
    title: "",
    body: "",
});

const editingId = ref(null);
const error = ref("");
const loading = ref(false);

const isLoggedIn = computed(() => Boolean(user.value));
const isEditing = computed(() => Boolean(editingId.value));

onMounted(async () => {
    await loadMe();
});

async function loadMe() {
    try {
        const data = await callApi("/api/me");
        user.value = data.user;
        await loadPosts();
    } catch {
        user.value = null;
        posts.value = [];
    }
}

async function submitAuth() {
    error.value = "";
    loading.value = true;

    try {
        const endpoint =
            authMode.value === "login" ? "/api/login" : "/api/register";

        const data = await callApi(endpoint, {
            method: "POST",
            body: authForm.value,
        });

        user.value = data.user;

        authForm.value = {
            name: "",
            email: "",
            password: "",
            password_confirmation: "",
        };

        await loadPosts();
    } catch (e) {
        error.value = e.message;
    } finally {
        loading.value = false;
    }
}

async function logout() {
    error.value = "";

    try {
        await callApi("/api/logout", {
            method: "POST",
        });

        user.value = null;
        posts.value = [];
    } catch (e) {
        error.value = e.message;
    }
}

async function loadPosts() {
    error.value = "";

    try {
        const data = await callApi("/api/posts");
        posts.value = data.data;
    } catch (e) {
        error.value = e.message;
    }
}

async function savePost() {
    error.value = "";
    loading.value = true;

    try {
        if (isEditing.value) {
            await callApi(`/api/posts/${editingId.value}`, {
                method: "PUT",
                body: postForm.value,
            });
        } else {
            await callApi("/api/posts", {
                method: "POST",
                body: postForm.value,
            });
        }

        resetPostForm();
        await loadPosts();
    } catch (e) {
        error.value = e.message;
    } finally {
        loading.value = false;
    }
}

function editPost(post) {
    editingId.value = post.id;

    postForm.value = {
        title: post.title,
        body: post.body,
    };
}

function resetPostForm() {
    editingId.value = null;

    postForm.value = {
        title: "",
        body: "",
    };
}

async function deletePost(post) {
    if (!confirm(`"${post.title}" 글을 삭제할까요?`)) {
        return;
    }

    error.value = "";

    try {
        await callApi(`/api/posts/${post.id}`, {
            method: "DELETE",
        });

        await loadPosts();
    } catch (e) {
        error.value = e.message;
    }
}
</script>

<template>
    <main class="container">
        <h1>Laravel + Vue + PostgreSQL CRUD</h1>

        <p class="sub-title">Example site</p>

        <div v-if="error" class="error">
            {{ error }}
        </div>

        <section v-if="!isLoggedIn" class="card">
            <div class="tabs">
                <button
                    :class="{ active: authMode === 'login' }"
                    @click="authMode = 'login'"
                >
                    Login
                </button>

                <button
                    :class="{ active: authMode === 'register' }"
                    @click="authMode = 'register'"
                >
                    Join
                </button>
            </div>

            <form @submit.prevent="submitAuth" class="form">
                <template v-if="authMode === 'register'">
                    <label>
                        name
                        <input v-model="authForm.name" type="text" required />
                    </label>
                </template>

                <label>
                    email
                    <input v-model="authForm.email" type="email" required />
                </label>

                <label>
                    password
                    <input
                        v-model="authForm.password"
                        type="password"
                        required
                    />
                </label>

                <template v-if="authMode === 'register'">
                    <label>
                        password confirmation
                        <input
                            v-model="authForm.password_confirmation"
                            type="password"
                            required
                        />
                    </label>
                </template>

                <button type="submit" :disabled="loading">
                    {{ authMode === "login" ? "Login" : "Join" }}
                </button>
            </form>
        </section>

        <section v-else>
            <div class="top-bar">
                <div>
                    Welcome <strong>{{ user.name }}</strong>
                    <span class="email">({{ user.email }})</span>
                </div>

                <button @click="logout">Logout</button>
            </div>

            <section class="card">
                <h2>{{ isEditing ? "Edit Post" : "Create Post" }}</h2>

                <form @submit.prevent="savePost" class="form">
                    <label>
                        Title
                        <input v-model="postForm.title" type="text" required />
                    </label>

                    <label>
                        Content
                        <textarea v-model="postForm.body" rows="5" required />
                    </label>

                    <div class="actions">
                        <button type="submit" :disabled="loading">
                            {{ isEditing ? "Edit" : "Create" }}
                        </button>

                        <button
                            v-if="isEditing"
                            type="button"
                            class="secondary"
                            @click="resetPostForm"
                        >
                            Cancel
                        </button>
                    </div>
                </form>
            </section>

            <section class="posts">
                <h2>Post List</h2>

                <article v-for="post in posts" :key="post.id" class="post">
                    <div class="post-header">
                        <h3>{{ post.title }}</h3>

                        <small>
                            {{ post.user.name }} · {{ post.created_at }}
                        </small>
                    </div>

                    <p>{{ post.body }}</p>

                    <div class="actions">
                        <button
                            v-if="post.can_update"
                            class="secondary"
                            @click="editPost(post)"
                        >
                            Edit
                        </button>

                        <button
                            v-if="post.can_delete"
                            class="danger"
                            @click="deletePost(post)"
                        >
                            Delete
                        </button>
                    </div>
                </article>

                <p v-if="posts.length === 0" class="empty">No Data.</p>
            </section>
        </section>
    </main>
</template>
