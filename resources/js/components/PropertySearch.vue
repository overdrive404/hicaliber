<template>
    <div class="page">
        <header class="hero">
            <div class="hero-content">
                <p class="hero-kicker">Property Search</p>
                <h1 class="hero-title">Find the right home in seconds.</h1>
                <p class="hero-subtitle">
                    Filter by name, bedrooms, bathrooms, storeys, garages, and price range.
                </p>
            </div>
        </header>

        <section class="card">
            <el-form :model="form" label-position="top" class="search-form" @submit.prevent>
                <el-row :gutter="16">
                    <el-col :xs="24" :md="12">
                        <el-form-item label="Name">
                            <el-input
                                v-model="form.name"
                                placeholder="e.g. Victoria"
                                clearable
                            />
                        </el-form-item>
                    </el-col>
                    <el-col :xs="24" :md="6">
                        <el-form-item label="Min Price">
                            <el-input-number
                                v-model="form.price_min"
                                :min="0"
                                :controls="false"
                                placeholder="No min"
                                class="full-width"
                            />
                        </el-form-item>
                    </el-col>
                    <el-col :xs="24" :md="6">
                        <el-form-item label="Max Price">
                            <el-input-number
                                v-model="form.price_max"
                                :min="0"
                                :controls="false"
                                placeholder="No max"
                                class="full-width"
                            />
                        </el-form-item>
                    </el-col>
                </el-row>

                <el-row :gutter="16">
                    <el-col :xs="12" :sm="6">
                        <el-form-item label="Bedrooms">
                            <el-input-number
                                v-model="form.bedrooms"
                                :min="0"
                                :controls="false"
                                class="full-width"
                            />
                        </el-form-item>
                    </el-col>
                    <el-col :xs="12" :sm="6">
                        <el-form-item label="Bathrooms">
                            <el-input-number
                                v-model="form.bathrooms"
                                :min="0"
                                :controls="false"
                                class="full-width"
                            />
                        </el-form-item>
                    </el-col>
                    <el-col :xs="12" :sm="6">
                        <el-form-item label="Storeys">
                            <el-input-number
                                v-model="form.storeys"
                                :min="0"
                                :controls="false"
                                class="full-width"
                            />
                        </el-form-item>
                    </el-col>
                    <el-col :xs="12" :sm="6">
                        <el-form-item label="Garages">
                            <el-input-number
                                v-model="form.garages"
                                :min="0"
                                :controls="false"
                                class="full-width"
                            />
                        </el-form-item>
                    </el-col>
                </el-row>

                <div class="form-actions">
                    <el-button type="primary" :loading="loading" @click="search">
                        <el-icon v-if="loading" class="is-loading">
                            <Loading />
                        </el-icon>
                        Search
                    </el-button>
                    <el-button @click="reset">Reset</el-button>
                    <span v-if="searched" class="results-count">
                        {{ results.length }} result{{ results.length === 1 ? '' : 's' }}
                    </span>
                </div>
            </el-form>
        </section>

        <section class="card results">
            <div class="results-header">
                <div>
                    <h2>Results</h2>
                    <p v-if="searched" class="results-subtitle">
                        {{ results.length === 0 ? 'No matches yet.' : 'Updated just now.' }}
                    </p>
                    <p v-else class="results-subtitle">Run a search to see matching properties.</p>
                </div>
                <el-tag v-if="loading" type="info" effect="light" class="status-tag">
                    <el-icon class="is-loading"><Loading /></el-icon>
                    Searching
                </el-tag>
            </div>

            <el-alert
                v-if="error"
                type="error"
                :closable="false"
                show-icon
                class="mb-4"
                :title="error"
            />

            <el-table
                v-loading="loading"
                :data="results"
                stripe
                class="results-table"
                :empty-text="searched ? '' : 'No search executed yet'"
            >
                <el-table-column prop="name" label="Name" min-width="200" />
                <el-table-column prop="bedrooms" label="Bedrooms" width="110" />
                <el-table-column prop="bathrooms" label="Bathrooms" width="110" />
                <el-table-column prop="storeys" label="Storeys" width="100" />
                <el-table-column prop="garages" label="Garages" width="100" />
                <el-table-column prop="price" label="Price" min-width="140">
                    <template #default="{ row }">
                        {{ formatPrice(row.price) }}
                    </template>
                </el-table-column>
            </el-table>

            <el-empty
                v-if="searched && !loading && results.length === 0"
                description="No results found"
                class="empty-state"
            />
        </section>
    </div>
</template>

<script setup>
import { reactive, ref } from 'vue';
import axios from 'axios';

const form = reactive({
    name: '',
    bedrooms: null,
    bathrooms: null,
    storeys: null,
    garages: null,
    price_min: null,
    price_max: null,
});

const results = ref([]);
const loading = ref(false);
const searched = ref(false);
const error = ref('');

const buildParams = () => {
    const params = {};

    if (form.name && form.name.trim()) {
        params.name = form.name.trim();
    }

    for (const key of ['bedrooms', 'bathrooms', 'storeys', 'garages', 'price_min', 'price_max']) {
        if (form[key] !== null && form[key] !== '') {
            params[key] = form[key];
        }
    }

    return params;
};

const search = async () => {
    loading.value = true;
    error.value = '';

    try {
        const response = await axios.get('/api/properties', { params: buildParams() });
        results.value = response.data?.data ?? [];
    } catch (err) {
        results.value = [];
        error.value = 'Something went wrong while searching. Please try again.';
    } finally {
        loading.value = false;
        searched.value = true;
    }
};

const reset = () => {
    form.name = '';
    form.bedrooms = null;
    form.bathrooms = null;
    form.storeys = null;
    form.garages = null;
    form.price_min = null;
    form.price_max = null;
    results.value = [];
    searched.value = false;
    error.value = '';
};

const formatPrice = (value) => {
    if (typeof value !== 'number') {
        return value;
    }

    return new Intl.NumberFormat('en-US', {
        style: 'currency',
        currency: 'USD',
        maximumFractionDigits: 0,
    }).format(value);
};
</script>

<style scoped>
.page {
    min-height: 100vh;
    padding: 48px 20px 72px;
    background: radial-gradient(circle at top, rgba(66, 153, 225, 0.18), transparent 45%),
        radial-gradient(circle at right, rgba(16, 185, 129, 0.2), transparent 40%),
        linear-gradient(120deg, #f9fafb 0%, #eef2ff 50%, #f8fafc 100%);
    color: #0f172a;
}

.hero {
    max-width: 980px;
    margin: 0 auto 32px;
}

.hero-content {
    padding: 28px 32px;
    border-radius: 20px;
    background: rgba(255, 255, 255, 0.8);
    box-shadow: 0 20px 45px rgba(15, 23, 42, 0.12);
    backdrop-filter: blur(8px);
}

.hero-kicker {
    text-transform: uppercase;
    letter-spacing: 0.28em;
    font-size: 12px;
    color: #2563eb;
    font-weight: 600;
    margin-bottom: 12px;
}

.hero-title {
    font-size: 32px;
    font-weight: 700;
    margin-bottom: 12px;
}

.hero-subtitle {
    margin: 0;
    color: #475569;
    font-size: 16px;
}

.card {
    max-width: 980px;
    margin: 0 auto 24px;
    padding: 28px 32px;
    border-radius: 18px;
    background: #ffffff;
    box-shadow: 0 16px 30px rgba(15, 23, 42, 0.08);
}

.search-form :deep(.el-form-item__label) {
    font-weight: 600;
    color: #1f2937;
}

.form-actions {
    display: flex;
    align-items: center;
    gap: 12px;
    margin-top: 12px;
}

.results-count {
    margin-left: auto;
    font-size: 14px;
    color: #475569;
}

.results {
    padding-bottom: 20px;
}

.results-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 16px;
}

.results-header h2 {
    font-size: 20px;
    margin-bottom: 4px;
}

.results-subtitle {
    margin: 0;
    color: #64748b;
    font-size: 14px;
}

.results-table {
    border-radius: 12px;
    overflow: hidden;
}

.status-tag {
    display: inline-flex;
    align-items: center;
    gap: 6px;
}

.full-width {
    width: 100%;
}

.mb-4 {
    margin-bottom: 16px;
}

.empty-state {
    margin-top: 16px;
}

@media (max-width: 640px) {
    .hero-content,
    .card {
        padding: 20px;
    }

    .form-actions {
        flex-wrap: wrap;
    }

    .results-count {
        width: 100%;
        margin-left: 0;
    }
}
</style>
