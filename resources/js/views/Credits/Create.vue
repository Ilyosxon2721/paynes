<template>
  <div class="create-credit-page">
    <div class="page-header">
      <h1 class="page-title">Новый кредит</h1>
      <router-link to="/credits" class="btn btn-secondary">← Назад</router-link>
    </div>

    <div class="form-card">
      <form @submit.prevent="handleSubmit">
        <div class="form-group">
          <label for="recipient">Получатель *</label>
          <input id="recipient" v-model="form.recipient" type="text" class="form-input" placeholder="ФИО получателя" required />
        </div>

        <div class="form-row">
          <div class="form-group">
            <label for="branch">Филиал *</label>
            <input id="branch" v-model="form.branch" type="text" class="form-input" placeholder="Филиал" required />
          </div>

          <div class="form-group">
            <label for="amount">Сумма кредита *</label>
            <input id="amount" v-model.number="form.amount" type="number" step="0.01" class="form-input" placeholder="0.00" required />
          </div>
        </div>

        <div v-if="submitError" class="alert alert-error">{{ submitError }}</div>

        <div class="form-actions">
          <button type="submit" class="btn btn-primary" :disabled="loading">
            {{ loading ? 'Создание...' : 'Создать кредит' }}
          </button>
          <router-link to="/credits" class="btn btn-secondary">Отмена</router-link>
        </div>
      </form>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue';
import { useRouter } from 'vue-router';
import api from '@/services/api';

const router = useRouter();
const form = ref({ recipient: '', branch: '', amount: 0 });
const submitError = ref(null);
const loading = ref(false);

async function handleSubmit() {
  submitError.value = null;
  loading.value = true;
  try {
    const response = await api.post('/credits', form.value);
    if (response.data.success) {
      router.push({ name: 'Credits' });
    }
  } catch (err) {
    submitError.value = err.response?.data?.message || 'Ошибка при создании кредита';
  } finally {
    loading.value = false;
  }
}
</script>

<style scoped>
.create-credit-page { max-width: 900px; margin: 0 auto; }
.page-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px; }
.page-title { font-size: 32px; font-weight: 600; color: #2c3e50; margin: 0; }
.form-card { background: white; padding: 30px; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.08); }
.form-row { display: grid; grid-template-columns: repeat(2, 1fr); gap: 20px; }
.form-group { margin-bottom: 20px; }
.form-group label { display: block; margin-bottom: 8px; font-weight: 500; color: #333; font-size: 14px; }
.form-input { width: 100%; padding: 12px 16px; border: 2px solid #e0e0e0; border-radius: 8px; font-size: 15px; transition: all 0.2s; outline: none; }
.form-input:focus { border-color: #667eea; box-shadow: 0 0 0 3px rgba(102,126,234,0.1); }
.alert { padding: 16px; border-radius: 8px; margin-bottom: 20px; }
.alert-error { background: #fee; border: 1px solid #fcc; color: #c33; }
.form-actions { display: flex; gap: 12px; margin-top: 30px; }
.btn { padding: 12px 24px; border: none; border-radius: 8px; font-size: 15px; font-weight: 500; cursor: pointer; transition: all 0.2s; text-decoration: none; display: inline-block; }
.btn-primary { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; }
.btn-primary:hover:not(:disabled) { transform: translateY(-2px); box-shadow: 0 4px 12px rgba(102,126,234,0.4); }
.btn-primary:disabled { opacity: 0.6; cursor: not-allowed; }
.btn-secondary { background: #6c757d; color: white; }
.btn-secondary:hover { background: #5a6268; }
</style>

