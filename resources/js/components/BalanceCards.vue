<template>
  <div class="balance-cards">
    <div class="balance-card balance-card--cash-uzs">
      <div class="balance-card__icon">
        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
          <line x1="12" y1="1" x2="12" y2="23"></line>
          <path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"></path>
        </svg>
      </div>
      <div class="balance-card__content">
        <div class="balance-card__label">Наличные UZS</div>
        <div class="balance-card__value">{{ formatMoney(balances?.cash_uzs) }}</div>
      </div>
      <div class="balance-card__indicator" :class="{ 'balance-card__indicator--negative': (balances?.cash_uzs || 0) < 0 }"></div>
    </div>

    <div class="balance-card balance-card--cashless-uzs">
      <div class="balance-card__icon">
        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
          <rect x="1" y="4" width="22" height="16" rx="2" ry="2"></rect>
          <line x1="1" y1="10" x2="23" y2="10"></line>
        </svg>
      </div>
      <div class="balance-card__content">
        <div class="balance-card__label">Безналичные UZS</div>
        <div class="balance-card__value">{{ formatMoney(balances?.cashless_uzs) }}</div>
      </div>
      <div class="balance-card__indicator" :class="{ 'balance-card__indicator--negative': (balances?.cashless_uzs || 0) < 0 }"></div>
    </div>

    <div class="balance-card balance-card--card-uzs">
      <div class="balance-card__icon">
        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
          <rect x="1" y="4" width="22" height="16" rx="2" ry="2"></rect>
          <line x1="1" y1="10" x2="23" y2="10"></line>
        </svg>
      </div>
      <div class="balance-card__content">
        <div class="balance-card__label">Карты UZS</div>
        <div class="balance-card__value">{{ formatMoney(balances?.card_uzs) }}</div>
      </div>
      <div class="balance-card__indicator" :class="{ 'balance-card__indicator--negative': (balances?.card_uzs || 0) < 0 }"></div>
    </div>

    <div class="balance-card balance-card--p2p-uzs">
      <div class="balance-card__icon">
        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
          <path d="M17 18a5 5 0 0 0-10 0"></path>
          <line x1="12" y1="2" x2="12" y2="9"></line>
          <path d="M4.22 10.22a15 15 0 0 1 15.56 0"></path>
        </svg>
      </div>
      <div class="balance-card__content">
        <div class="balance-card__label">P2P UZS</div>
        <div class="balance-card__value">{{ formatMoney(balances?.p2p_uzs) }}</div>
      </div>
      <div class="balance-card__indicator" :class="{ 'balance-card__indicator--negative': (balances?.p2p_uzs || 0) < 0 }"></div>
    </div>

    <div class="balance-card balance-card--cash-usd">
      <div class="balance-card__icon">
        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
          <line x1="12" y1="1" x2="12" y2="23"></line>
          <path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"></path>
        </svg>
      </div>
      <div class="balance-card__content">
        <div class="balance-card__label">Наличные USD</div>
        <div class="balance-card__value">{{ formatMoney(balances?.cash_usd, 'USD') }}</div>
      </div>
      <div class="balance-card__indicator" :class="{ 'balance-card__indicator--negative': (balances?.cash_usd || 0) < 0 }"></div>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue';

const props = defineProps({
  balances: {
    type: Object,
    default: () => ({
      cash_uzs: 0,
      cashless_uzs: 0,
      card_uzs: 0,
      p2p_uzs: 0,
      cash_usd: 0,
    }),
  },
});

const formatMoney = (amount, currency = 'UZS') => {
  const formatted = new Intl.NumberFormat('ru-RU', {
    minimumFractionDigits: 2,
    maximumFractionDigits: 2,
  }).format(amount || 0);
  return `${formatted} ${currency}`;
};
</script>

<style scoped>
.balance-cards {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
  gap: 1rem;
  margin-bottom: 2rem;
}

.balance-card {
  position: relative;
  background: white;
  border-radius: 12px;
  padding: 1.5rem;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
  display: flex;
  align-items: flex-start;
  gap: 1rem;
  overflow: hidden;
  transition: transform 0.2s, box-shadow 0.2s;
}

.balance-card:hover {
  transform: translateY(-2px);
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
}

.balance-card__icon {
  flex-shrink: 0;
  width: 48px;
  height: 48px;
  border-radius: 10px;
  display: flex;
  align-items: center;
  justify-content: center;
}

.balance-card--cash-uzs .balance-card__icon {
  background: linear-gradient(135deg, #10b981 0%, #059669 100%);
  color: white;
}

.balance-card--cashless-uzs .balance-card__icon {
  background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
  color: white;
}

.balance-card--card-uzs .balance-card__icon {
  background: linear-gradient(135deg, #8b5cf6 0%, #7c3aed 100%);
  color: white;
}

.balance-card--p2p-uzs .balance-card__icon {
  background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
  color: white;
}

.balance-card--cash-usd .balance-card__icon {
  background: linear-gradient(135deg, #ec4899 0%, #db2777 100%);
  color: white;
}

.balance-card__content {
  flex: 1;
  min-width: 0;
}

.balance-card__label {
  font-size: 0.875rem;
  color: #6b7280;
  margin-bottom: 0.5rem;
  font-weight: 500;
}

.balance-card__value {
  font-size: 1.5rem;
  font-weight: 700;
  color: #111827;
  word-break: break-all;
}

.balance-card__indicator {
  position: absolute;
  right: 0;
  top: 0;
  bottom: 0;
  width: 4px;
}

.balance-card--cash-uzs .balance-card__indicator {
  background: #10b981;
}

.balance-card--cashless-uzs .balance-card__indicator {
  background: #3b82f6;
}

.balance-card--card-uzs .balance-card__indicator {
  background: #8b5cf6;
}

.balance-card--p2p-uzs .balance-card__indicator {
  background: #f59e0b;
}

.balance-card--cash-usd .balance-card__indicator {
  background: #ec4899;
}

.balance-card__indicator--negative {
  background: #ef4444 !important;
}

@media (max-width: 768px) {
  .balance-cards {
    grid-template-columns: 1fr;
  }

  .balance-card__value {
    font-size: 1.25rem;
  }
}
</style>
