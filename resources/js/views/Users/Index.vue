<template>
  <div class="users-page">
    <div class="page-header">
      <h1 class="page-title">Сотрудники</h1>
      <button @click="openCreateModal" class="btn btn-primary">
        + Добавить сотрудника
      </button>
    </div>

    <div v-if="loading" class="loading">Загрузка...</div>

    <div v-else-if="error" class="alert alert-error">{{ error }}</div>

    <div v-else class="table-card">
      <table class="data-table">
        <thead>
          <tr>
            <th>Логин</th>
            <th>ФИО</th>
            <th>Должность</th>
            <th>Филиал</th>
            <th>Статус</th>
            <th>Роли</th>
            <th>Действия</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="user in users" :key="user.id">
            <td>{{ user.login }}</td>
            <td>{{ user.full_name }}</td>
            <td>
              <span class="badge" :class="`badge-${user.position}`">
                {{ user.position === 'admin' ? 'Администратор' : 'Кассир' }}
              </span>
            </td>
            <td>{{ user.branch || '-' }}</td>
            <td>
              <span class="badge" :class="`badge-${user.status}`">
                {{ user.status === 'active' ? 'Активен' : 'Неактивен' }}
              </span>
            </td>
            <td>
              <span v-for="role in user.roles" :key="role" class="role-tag">
                {{ role }}
              </span>
            </td>
            <td>
              <div class="action-buttons">
                <button
                  @click="openEditModal(user)"
                  class="btn-action btn-edit"
                  title="Редактировать"
                >
                  ✏️
                </button>
                <button
                  @click="deleteUser(user.id)"
                  class="btn-action btn-delete"
                  title="Удалить"
                >
                  🗑️
                </button>
              </div>
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <!-- Create/Edit Modal -->
    <div v-if="showModal" class="modal-overlay" @click="closeModal">
      <div class="modal-content large" @click.stop>
        <div class="modal-header">
          <h2>{{ isEditMode ? 'Редактировать сотрудника' : 'Добавить сотрудника' }}</h2>
        </div>
        <div class="modal-body">
          <form @submit.prevent="saveUser">
            <div class="form-row">
              <div class="form-group">
                <label for="login">Логин *</label>
                <input
                  id="login"
                  v-model="form.login"
                  type="text"
                  class="form-input"
                  :class="{ 'error': formErrors.login }"
                  required
                />
                <span v-if="formErrors.login" class="error-message">{{ formErrors.login[0] }}</span>
              </div>

              <div class="form-group">
                <label for="full_name">ФИО *</label>
                <input
                  id="full_name"
                  v-model="form.full_name"
                  type="text"
                  class="form-input"
                  :class="{ 'error': formErrors.full_name }"
                  required
                />
                <span v-if="formErrors.full_name" class="error-message">{{ formErrors.full_name[0] }}</span>
              </div>
            </div>

            <div class="form-row">
              <div class="form-group">
                <label for="password">Пароль {{ isEditMode ? '(оставьте пустым, чтобы не менять)' : '*' }}</label>
                <input
                  id="password"
                  v-model="form.password"
                  type="password"
                  class="form-input"
                  :class="{ 'error': formErrors.password }"
                  :required="!isEditMode"
                />
                <span v-if="formErrors.password" class="error-message">{{ formErrors.password[0] }}</span>
              </div>

              <div class="form-group">
                <label for="position">Должность *</label>
                <select
                  id="position"
                  v-model="form.position"
                  class="form-select"
                  required
                >
                  <option value="admin">Администратор</option>
                  <option value="cashier">Кассир</option>
                </select>
              </div>
            </div>

            <div class="form-row">
              <div class="form-group">
                <label for="branch">Филиал</label>
                <input
                  id="branch"
                  v-model="form.branch"
                  type="text"
                  class="form-input"
                />
              </div>

              <div class="form-group">
                <label for="status">Статус *</label>
                <select
                  id="status"
                  v-model="form.status"
                  class="form-select"
                  required
                >
                  <option value="active">Активен</option>
                  <option value="inactive">Неактивен</option>
                </select>
              </div>
            </div>

            <div class="form-group">
              <label for="reward_percentage">Процент вознаграждения (%)</label>
              <input
                id="reward_percentage"
                v-model.number="form.reward_percentage"
                type="number"
                step="0.01"
                min="0"
                max="100"
                class="form-input"
                placeholder="0.00"
              />
            </div>

            <div class="form-group">
              <label>Роли</label>
              <div class="checkbox-group">
                <label v-for="role in availableRoles" :key="role.id" class="checkbox-label">
                  <input
                    type="checkbox"
                    :value="role.name"
                    v-model="form.roles"
                  />
                  {{ role.name }}
                </label>
              </div>
            </div>

            <div class="form-group">
              <label>Привилегии</label>
              <div class="checkbox-group">
                <label v-for="permission in availablePermissions" :key="permission.id" class="checkbox-label">
                  <input
                    type="checkbox"
                    :value="permission.name"
                    v-model="form.permissions"
                  />
                  {{ permission.name }}
                </label>
              </div>
            </div>

            <div class="modal-footer">
              <button type="submit" class="btn btn-primary" :disabled="saving">
                {{ saving ? 'Сохранение...' : 'Сохранить' }}
              </button>
              <button type="button" @click="closeModal" class="btn btn-secondary">
                Отмена
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import api from '@/services/api';

const users = ref([]);
const loading = ref(false);
const error = ref(null);
const showModal = ref(false);
const isEditMode = ref(false);
const editingId = ref(null);
const saving = ref(false);
const formErrors = ref({});
const availableRoles = ref([]);
const availablePermissions = ref([]);

const defaultForm = {
  login: '',
  full_name: '',
  password: '',
  position: 'cashier',
  status: 'active',
  branch: '',
  reward_percentage: 0,
  roles: [],
  permissions: []
};

const form = ref({ ...defaultForm });

onMounted(async () => {
  await Promise.all([
    loadUsers(),
    loadRoles(),
    loadPermissions()
  ]);
});

async function loadUsers() {
  loading.value = true;
  error.value = null;

  try {
    const response = await api.get('/users');

    if (response.data.success) {
      users.value = response.data.data;
    }
  } catch (err) {
    error.value = err.response?.data?.message || 'Ошибка загрузки сотрудников';
  } finally {
    loading.value = false;
  }
}

async function loadRoles() {
  try {
    const response = await api.get('/users/roles');
    if (response.data.success) {
      availableRoles.value = response.data.data;
    }
  } catch (err) {
    console.error('Error loading roles:', err);
  }
}

async function loadPermissions() {
  try {
    const response = await api.get('/users/permissions');
    if (response.data.success) {
      availablePermissions.value = response.data.data;
    }
  } catch (err) {
    console.error('Error loading permissions:', err);
  }
}

function openCreateModal() {
  isEditMode.value = false;
  editingId.value = null;
  form.value = { ...defaultForm, roles: [], permissions: [] };
  formErrors.value = {};
  showModal.value = true;
}

function openEditModal(user) {
  isEditMode.value = true;
  editingId.value = user.id;
  form.value = {
    login: user.login,
    full_name: user.full_name,
    password: '',
    position: user.position,
    status: user.status,
    branch: user.branch || '',
    reward_percentage: user.reward_percentage || 0,
    roles: user.roles || [],
    permissions: user.permissions || []
  };
  formErrors.value = {};
  showModal.value = true;
}

function closeModal() {
  showModal.value = false;
  form.value = { ...defaultForm };
  formErrors.value = {};
}

async function saveUser() {
  saving.value = true;
  formErrors.value = {};

  try {
    const payload = { ...form.value };
    if (isEditMode.value && !payload.password) {
      delete payload.password;
    }

    if (isEditMode.value) {
      await api.put(`/users/${editingId.value}`, payload);
    } else {
      await api.post('/users', payload);
    }

    await loadUsers();
    closeModal();
  } catch (err) {
    if (err.response?.data?.errors) {
      formErrors.value = err.response.data.errors;
    } else {
      alert(err.response?.data?.message || 'Ошибка при сохранении сотрудника');
    }
  } finally {
    saving.value = false;
  }
}

async function deleteUser(id) {
  if (!confirm('Вы уверены, что хотите удалить этого сотрудника?')) {
    return;
  }

  try {
    await api.delete(`/users/${id}`);
    await loadUsers();
  } catch (err) {
    alert(err.response?.data?.message || 'Ошибка при удалении сотрудника');
  }
}
</script>

<style scoped>
.users-page {
  max-width: 1600px;
  margin: 0 auto;
}

.page-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 24px;
}

.page-title {
  font-size: 32px;
  font-weight: 600;
  color: #2c3e50;
  margin: 0;
}

.btn {
  padding: 12px 24px;
  border: none;
  border-radius: 8px;
  font-size: 15px;
  font-weight: 500;
  cursor: pointer;
  transition: all 0.2s;
}

.btn-primary {
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  color: white;
}

.btn-primary:hover:not(:disabled) {
  transform: translateY(-2px);
  box-shadow: 0 4px 12px rgba(102, 126, 234, 0.4);
}

.btn-primary:disabled {
  opacity: 0.6;
  cursor: not-allowed;
}

.btn-secondary {
  background: #6c757d;
  color: white;
}

.btn-secondary:hover {
  background: #5a6268;
}

.loading {
  text-align: center;
  padding: 40px;
  font-size: 18px;
  color: #666;
}

.alert {
  padding: 16px;
  border-radius: 8px;
  margin-bottom: 20px;
}

.alert-error {
  background: #fee;
  border: 1px solid #fcc;
  color: #c33;
}

.table-card {
  background: white;
  border-radius: 12px;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
  overflow: hidden;
}

.data-table {
  width: 100%;
  border-collapse: collapse;
}

.data-table th {
  background: #f8f9fa;
  padding: 16px 12px;
  text-align: left;
  font-weight: 600;
  font-size: 13px;
  color: #2c3e50;
  border-bottom: 2px solid #e0e0e0;
}

.data-table td {
  padding: 14px 12px;
  border-bottom: 1px solid #f0f0f0;
  font-size: 14px;
  color: #333;
}

.data-table tr:hover {
  background: #f8f9fa;
}

.badge {
  display: inline-block;
  padding: 4px 12px;
  border-radius: 12px;
  font-size: 12px;
  font-weight: 600;
}

.badge-admin {
  background: #e3f2fd;
  color: #1976d2;
}

.badge-cashier {
  background: #f3e5f5;
  color: #7b1fa2;
}

.badge-active {
  background: #e8f5e9;
  color: #2e7d32;
}

.badge-inactive {
  background: #ffebee;
  color: #c62828;
}

.role-tag {
  display: inline-block;
  padding: 2px 8px;
  background: #e0e0e0;
  border-radius: 8px;
  font-size: 11px;
  margin-right: 4px;
}

.action-buttons {
  display: flex;
  gap: 8px;
}

.btn-action {
  padding: 6px 10px;
  border: none;
  border-radius: 6px;
  cursor: pointer;
  font-size: 16px;
  transition: all 0.2s;
  background: transparent;
}

.btn-action:hover {
  transform: scale(1.1);
}

.btn-edit:hover {
  background: #e3f2fd;
}

.btn-delete:hover {
  background: #ffebee;
}

/* Modal Styles */
.modal-overlay {
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: rgba(0, 0, 0, 0.6);
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 9999;
  animation: fadeIn 0.2s;
}

@keyframes fadeIn {
  from { opacity: 0; }
  to { opacity: 1; }
}

.modal-content {
  background: white;
  border-radius: 12px;
  box-shadow: 0 10px 40px rgba(0, 0, 0, 0.3);
  max-width: 700px;
  width: 90%;
  max-height: 90vh;
  overflow-y: auto;
  animation: slideUp 0.3s;
}

.modal-content.large {
  max-width: 900px;
}

@keyframes slideUp {
  from {
    opacity: 0;
    transform: translateY(30px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

.modal-header {
  padding: 20px 24px;
  border-bottom: 2px solid #e0e0e0;
}

.modal-header h2 {
  margin: 0;
  font-size: 20px;
  color: #2c3e50;
  font-weight: 600;
}

.modal-body {
  padding: 24px;
}

.modal-footer {
  display: flex;
  gap: 12px;
  justify-content: flex-end;
  padding-top: 20px;
  margin-top: 20px;
  border-top: 1px solid #e0e0e0;
}

.form-group {
  margin-bottom: 20px;
}

.form-group label {
  display: block;
  margin-bottom: 8px;
  font-weight: 500;
  color: #333;
  font-size: 14px;
}

.form-input,
.form-select {
  width: 100%;
  padding: 12px 16px;
  border: 2px solid #e0e0e0;
  border-radius: 8px;
  font-size: 15px;
  transition: all 0.2s;
  outline: none;
}

.form-input:focus,
.form-select:focus {
  border-color: #667eea;
  box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
}

.form-input.error {
  border-color: #e74c3c;
}

.error-message {
  display: block;
  margin-top: 6px;
  color: #e74c3c;
  font-size: 13px;
}

.form-row {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 20px;
}

.checkbox-group {
  display: flex;
  flex-wrap: wrap;
  gap: 12px;
}

.checkbox-label {
  display: flex;
  align-items: center;
  gap: 6px;
  padding: 8px 12px;
  background: #f8f9fa;
  border-radius: 6px;
  cursor: pointer;
  transition: all 0.2s;
}

.checkbox-label:hover {
  background: #e9ecef;
}

.checkbox-label input[type="checkbox"] {
  cursor: pointer;
}
</style>
