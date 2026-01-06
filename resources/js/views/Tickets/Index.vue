<template>
  <div class="tickets-page">
    <div class="page-header">
      <div>
        <h2>Чат и тикеты</h2>
        <p>Общение с техподдержкой, админом и другими филиалами.</p>
      </div>
      <el-button type="primary" @click="openCreate">Создать тикет</el-button>
    </div>

    <div class="tickets-layout">
      <div class="tickets-list">
        <div class="list-header">
          <span>Тикеты</span>
          <el-button size="small" @click="loadTickets" :loading="loading">Обновить</el-button>
        </div>

        <el-skeleton v-if="loading" :rows="6" animated />
        <el-empty v-else-if="tickets.length === 0" description="Нет тикетов" />

        <div v-else class="list-items">
          <button
            v-for="ticket in tickets"
            :key="ticket.id"
            class="ticket-item"
            :class="{ active: selectedTicket?.id === ticket.id }"
            @click="selectTicket(ticket)"
          >
            <div class="ticket-subject">{{ ticket.subject }}</div>
            <div class="ticket-meta">
              <el-tag size="small" :type="statusTagType(ticket.status)">
                {{ statusLabel(ticket.status) }}
              </el-tag>
              <span>{{ formatDate(ticket.last_message_at || ticket.created_at) }}</span>
            </div>
          </button>
        </div>
      </div>

      <div class="tickets-chat">
        <el-card v-if="showCreate" class="create-card">
          <template #header>
            <div class="card-header">
              <span>Новый тикет</span>
              <el-button text @click="showCreate = false">Закрыть</el-button>
            </div>
          </template>

          <el-form label-position="top" @submit.prevent>
            <el-form-item label="Тема">
              <el-input v-model="createForm.subject" placeholder="Введите тему" />
            </el-form-item>

            <el-form-item label="Категория">
              <el-select v-model="createForm.category" placeholder="Выберите категорию">
                <el-option label="Техподдержка" value="support" />
                <el-option label="Админ" value="admin" />
                <el-option label="Филиал" value="branch" />
                <el-option label="Общий" value="general" />
              </el-select>
            </el-form-item>

            <el-form-item label="Приоритет">
              <el-select v-model="createForm.priority" placeholder="Выберите приоритет">
                <el-option label="Низкий" value="low" />
                <el-option label="Обычный" value="normal" />
                <el-option label="Высокий" value="high" />
                <el-option label="Срочный" value="urgent" />
              </el-select>
            </el-form-item>

            <el-form-item v-if="createForm.category === 'branch'" label="Филиал">
              <el-select v-model="createForm.target_branch" placeholder="Выберите филиал">
                <el-option
                  v-for="branch in branches"
                  :key="branch"
                  :label="branch"
                  :value="branch"
                />
              </el-select>
            </el-form-item>

            <el-form-item label="Сообщение">
              <el-input
                v-model="createForm.message"
                type="textarea"
                :rows="5"
                placeholder="Опишите вопрос"
              />
            </el-form-item>

            <el-button type="primary" :loading="createLoading" @click="createTicket">
              Создать
            </el-button>
          </el-form>
        </el-card>

        <el-card v-else class="chat-card">
          <template #header>
            <div class="card-header">
              <div>
                <div class="chat-title">{{ selectedTicket?.subject || 'Выберите тикет' }}</div>
                <div v-if="selectedTicket" class="chat-meta">
                  <el-tag size="small" :type="statusTagType(selectedTicket.status)">
                    {{ statusLabel(selectedTicket.status) }}
                  </el-tag>
                  <el-tag size="small" type="info">{{ priorityLabel(selectedTicket.priority) }}</el-tag>
                  <span v-if="selectedTicket.target_branch">Филиал: {{ selectedTicket.target_branch }}</span>
                </div>
              </div>
              <div class="chat-actions" v-if="selectedTicket">
                <el-button
                  size="small"
                  type="danger"
                  plain
                  :disabled="!canClose"
                  @click="closeTicket"
                >
                  Закрыть
                </el-button>
              </div>
            </div>
          </template>

          <el-empty v-if="!selectedTicket" description="Выберите тикет из списка" />

          <div v-else class="chat-body">
            <div class="messages">
              <div
                v-for="message in messages"
                :key="message.id"
                class="message"
                :class="{ mine: message.user?.id === authStore.user?.id }"
              >
                <div class="message-header">
                  <span class="message-author">{{ message.user?.full_name || 'Пользователь' }}</span>
                  <span class="message-time">{{ formatDate(message.created_at, true) }}</span>
                </div>
                <div class="message-body">{{ message.body }}</div>
              </div>
            </div>

            <div class="message-input">
              <el-input
                v-model="newMessage"
                type="textarea"
                :rows="3"
                placeholder="Введите сообщение"
              />
              <el-button type="primary" :loading="messageLoading" @click="sendMessage">
                Отправить
              </el-button>
            </div>
          </div>
        </el-card>
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed, onBeforeUnmount, onMounted, ref } from 'vue';
import api from '@/services/api';
import { useAuthStore } from '@/stores/auth';

const authStore = useAuthStore();
const tickets = ref([]);
const messages = ref([]);
const branches = ref([]);
const selectedTicket = ref(null);
const newMessage = ref('');
const loading = ref(false);
const messageLoading = ref(false);
const createLoading = ref(false);
const showCreate = ref(false);

const createForm = ref({
  subject: '',
  category: 'support',
  priority: 'normal',
  target_branch: '',
  message: ''
});
let poller = null;

const canClose = computed(() => {
  if (!selectedTicket.value) return false;
  return authStore.isAdmin || selectedTicket.value.created_by?.id === authStore.user?.id;
});

const statusLabel = (status) => {
  const labels = {
    open: 'Открыт',
    in_progress: 'В работе',
    resolved: 'Решен',
    closed: 'Закрыт'
  };
  return labels[status] || status;
};

const priorityLabel = (priority) => {
  const labels = {
    low: 'Низкий',
    normal: 'Обычный',
    high: 'Высокий',
    urgent: 'Срочный'
  };
  return labels[priority] || priority;
};

const statusTagType = (status) => {
  if (status === 'open') return 'info';
  if (status === 'in_progress') return 'warning';
  if (status === 'resolved') return 'success';
  if (status === 'closed') return 'danger';
  return 'info';
};

const formatDate = (value, withTime = false) => {
  if (!value) return '';
  const date = new Date(value.replace(' ', 'T'));
  if (Number.isNaN(date.getTime())) return value;
  return new Intl.DateTimeFormat('ru-RU', {
    dateStyle: 'short',
    timeStyle: withTime ? 'short' : undefined
  }).format(date);
};

const loadTickets = async () => {
  loading.value = true;
  try {
    const response = await api.get('/tickets');
    tickets.value = response.data?.data?.data ?? [];
  } catch (error) {
    ElMessage.error(error.response?.data?.message || 'Ошибка загрузки тикетов');
  } finally {
    loading.value = false;
  }
};

const loadTicket = async (ticketId) => {
  messageLoading.value = true;
  try {
    const response = await api.get(`/tickets/${ticketId}`);
    selectedTicket.value = response.data?.data?.ticket ?? null;
    messages.value = response.data?.data?.messages ?? [];
  } catch (error) {
    ElMessage.error(error.response?.data?.message || 'Ошибка загрузки тикета');
  } finally {
    messageLoading.value = false;
  }
};

const selectTicket = (ticket) => {
  showCreate.value = false;
  loadTicket(ticket.id);
};

const sendMessage = async () => {
  if (!newMessage.value.trim() || !selectedTicket.value) return;
  messageLoading.value = true;
  try {
    const response = await api.post(`/tickets/${selectedTicket.value.id}/messages`, {
      message: newMessage.value
    });
    const saved = response.data?.data;
    if (saved) {
      messages.value.push(saved);
    }
    newMessage.value = '';
    await loadTickets();
  } catch (error) {
    ElMessage.error(error.response?.data?.message || 'Ошибка отправки сообщения');
  } finally {
    messageLoading.value = false;
  }
};

const openCreate = async () => {
  showCreate.value = true;
  selectedTicket.value = null;
  messages.value = [];
  await loadBranches();
};

const loadBranches = async () => {
  try {
    const response = await api.get('/tickets/branches');
    branches.value = response.data?.data ?? [];
  } catch (error) {
    ElMessage.error(error.response?.data?.message || 'Ошибка загрузки филиалов');
  }
};

const createTicket = async () => {
  if (!createForm.value.subject.trim() || !createForm.value.message.trim()) {
    ElMessage.error('Заполните тему и сообщение');
    return;
  }

  createLoading.value = true;
  try {
    const payload = { ...createForm.value };
    if (payload.category !== 'branch') {
      payload.target_branch = null;
    }
    const response = await api.post('/tickets', payload);
    ElMessage.success(response.data?.message || 'Тикет создан');
    showCreate.value = false;
    await loadTickets();
    if (response.data?.data?.id) {
      await loadTicket(response.data.data.id);
    }
    createForm.value = {
      subject: '',
      category: 'support',
      priority: 'normal',
      target_branch: '',
      message: ''
    };
  } catch (error) {
    ElMessage.error(error.response?.data?.message || 'Ошибка создания тикета');
  } finally {
    createLoading.value = false;
  }
};

const closeTicket = async () => {
  if (!selectedTicket.value) return;
  try {
    const response = await api.patch(`/tickets/${selectedTicket.value.id}`, { status: 'closed' });
    selectedTicket.value = response.data?.data ?? selectedTicket.value;
    await loadTickets();
  } catch (error) {
    ElMessage.error(error.response?.data?.message || 'Ошибка закрытия тикета');
  }
};

onMounted(async () => {
  await loadTickets();
  poller = setInterval(async () => {
    if (showCreate.value) return;
    await loadTickets();
    if (selectedTicket.value) {
      await loadTicket(selectedTicket.value.id);
    }
  }, 15000);
});

onBeforeUnmount(() => {
  if (poller) {
    clearInterval(poller);
  }
});
</script>

<style scoped>
.tickets-page {
  display: flex;
  flex-direction: column;
  gap: 20px;
}

.page-header {
  display: flex;
  align-items: center;
  justify-content: space-between;
}

.page-header h2 {
  margin: 0 0 4px 0;
}

.page-header p {
  margin: 0;
  color: #6b7280;
  font-size: 14px;
}

.tickets-layout {
  display: grid;
  grid-template-columns: 320px 1fr;
  gap: 20px;
}

.tickets-list {
  background: #fff;
  border-radius: 12px;
  padding: 16px;
  box-shadow: var(--paynes-shadow-md);
  display: flex;
  flex-direction: column;
  gap: 16px;
  max-height: calc(100vh - 220px);
  overflow: hidden;
}

.list-header {
  display: flex;
  align-items: center;
  justify-content: space-between;
  font-weight: 600;
}

.list-items {
  display: flex;
  flex-direction: column;
  gap: 12px;
  overflow-y: auto;
  padding-right: 6px;
}

.ticket-item {
  text-align: left;
  border: 1px solid #e5e7eb;
  padding: 12px;
  border-radius: 10px;
  background: #f9fafb;
  cursor: pointer;
  display: flex;
  flex-direction: column;
  gap: 8px;
  transition: all 0.2s ease;
}

.ticket-item:hover {
  border-color: #1a77c9;
  background: #f1f7ff;
}

.ticket-item.active {
  border-color: #1a77c9;
  background: #e9f3ff;
}

.ticket-subject {
  font-weight: 600;
  color: #111827;
}

.ticket-meta {
  display: flex;
  align-items: center;
  justify-content: space-between;
  font-size: 12px;
  color: #6b7280;
}

.tickets-chat {
  display: flex;
  flex-direction: column;
  gap: 16px;
}

.chat-card,
.create-card {
  border-radius: 14px;
  min-height: 480px;
}

.card-header {
  display: flex;
  align-items: center;
  justify-content: space-between;
}

.chat-title {
  font-weight: 600;
}

.chat-meta {
  display: flex;
  align-items: center;
  gap: 8px;
  margin-top: 6px;
  font-size: 12px;
  color: #6b7280;
}

.chat-body {
  display: flex;
  flex-direction: column;
  gap: 16px;
}

.messages {
  display: flex;
  flex-direction: column;
  gap: 12px;
  max-height: 360px;
  overflow-y: auto;
  padding-right: 8px;
}

.message {
  border-radius: 12px;
  padding: 10px 12px;
  background: #f3f4f6;
  max-width: 70%;
  align-self: flex-start;
}

.message.mine {
  background: #e3f2ff;
  align-self: flex-end;
}

.message-header {
  display: flex;
  justify-content: space-between;
  font-size: 12px;
  color: #6b7280;
  margin-bottom: 6px;
}

.message-body {
  white-space: pre-wrap;
  color: #111827;
}

.message-input {
  display: flex;
  flex-direction: column;
  gap: 10px;
}

@media (max-width: 1200px) {
  .tickets-layout {
    grid-template-columns: 1fr;
  }

  .tickets-list {
    max-height: none;
  }
}
</style>
