<template>
  <div class="landing-page" :class="{ 'lang-uz': currentLang === 'uz' }">
    <!-- Header -->
    <header class="header">
      <div class="container">
        <div class="logo">
          <span class="logo-icon">💳</span>
          <span class="logo-text">Paynes</span>
        </div>
        <nav class="nav">
          <a href="#features">{{ t.nav.features }}</a>
          <a href="#pricing">{{ t.nav.pricing }}</a>
          <a href="#faq">{{ t.nav.faq }}</a>
        </nav>
        <div class="header-actions">
          <div class="lang-switcher">
            <button v-for="lang in languages" :key="lang.code" 
                    :class="{ active: currentLang === lang.code }"
                    @click="setLang(lang.code)">
              {{ lang.flag }}
            </button>
          </div>
          <router-link to="/register" class="btn btn-primary">{{ t.nav.register }}</router-link>
          <router-link to="/login" class="btn btn-outline">{{ t.nav.login }}</router-link>
        </div>
      </div>
    </header>

    <!-- Hero Section -->
    <section class="hero">
      <div class="hero-bg"></div>
      <div class="container">
        <div class="hero-content">
          <h1 class="hero-title">{{ t.hero.title }}</h1>
          <p class="hero-subtitle">{{ t.hero.subtitle }}</p>
          <div class="hero-buttons">
            <router-link to="/register" class="btn btn-primary btn-lg">{{ t.hero.cta }}</router-link>
            <a href="#features" class="btn btn-ghost btn-lg">{{ t.hero.learn }}</a>
          </div>
          <div class="hero-stats">
            <div class="stat"><span class="stat-value">500+</span><span class="stat-label">{{ t.hero.stats.agents }}</span></div>
            <div class="stat"><span class="stat-value">1M+</span><span class="stat-label">{{ t.hero.stats.transactions }}</span></div>
            <div class="stat"><span class="stat-value">99.9%</span><span class="stat-label">{{ t.hero.stats.uptime }}</span></div>
          </div>
        </div>
      </div>
    </section>

    <!-- Demo Screenshot Section -->
    <section class="demo-section">
      <div class="container">
        <div class="demo-wrapper demo-placeholder">
          <div class="demo-content">
            <div class="demo-sidebar">
              <div class="demo-logo">💳 Paynes</div>
              <div class="demo-nav-item active">📊 Dashboard</div>
              <div class="demo-nav-item">💳 Платежи</div>
              <div class="demo-nav-item">💱 Обмен</div>
              <div class="demo-nav-item">📈 Отчёты</div>
            </div>
            <div class="demo-main">
              <div class="demo-kpi-row">
                <div class="demo-kpi"><span class="kpi-value">245</span><span class="kpi-label">Платежей</span></div>
                <div class="demo-kpi"><span class="kpi-value">$1.2M</span><span class="kpi-label">Сумма</span></div>
                <div class="demo-kpi"><span class="kpi-value">18</span><span class="kpi-label">Кассиров</span></div>
                <div class="demo-kpi"><span class="kpi-value">12</span><span class="kpi-label">Ожидание</span></div>
              </div>
              <div class="demo-table">
                <div class="demo-table-header">Последние транзакции</div>
                <div class="demo-table-row" v-for="i in 4" :key="i">
                  <span>09.01.2026</span><span>Кассир {{ i }}</span><span>Paynet</span><span>150 000 UZS</span><span class="status-badge">✓</span>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- Features Section -->
    <section id="features" class="features">
      <div class="container">
        <h2 class="section-title">{{ t.features.title }}</h2>
        <p class="section-subtitle">{{ t.features.subtitle }}</p>
        <div class="features-grid">
          <div class="feature-card" v-for="(feature, i) in t.features.items" :key="i">
            <div class="feature-icon">{{ feature.icon }}</div>
            <h3>{{ feature.title }}</h3>
            <p>{{ feature.desc }}</p>
          </div>
        </div>
      </div>
    </section>

    <!-- How It Works -->
    <section class="how-it-works">
      <div class="container">
        <h2 class="section-title">{{ t.howItWorks.title }}</h2>
        <div class="steps">
          <div class="step" v-for="(step, i) in t.howItWorks.steps" :key="i">
            <div class="step-number">{{ i + 1 }}</div>
            <h3>{{ step.title }}</h3>
            <p>{{ step.desc }}</p>
          </div>
        </div>
      </div>
    </section>

    <!-- Pricing Section -->
    <section id="pricing" class="pricing">
      <div class="container">
        <h2 class="section-title">{{ t.pricing.title }}</h2>
        <p class="section-subtitle">{{ t.pricing.subtitle }}</p>
        <div class="pricing-grid">
          <div class="pricing-card" v-for="(plan, i) in t.pricing.plans" :key="i" :class="{ popular: plan.popular }">
            <div class="popular-badge" v-if="plan.popular">{{ t.pricing.popular }}</div>
            <h3>{{ plan.name }}</h3>
            <div class="price">{{ plan.price }}<span>/{{ t.pricing.month }}</span></div>
            <ul class="features-list">
              <li v-for="(f, j) in plan.features" :key="j">✓ {{ f }}</li>
            </ul>
            <router-link to="/register" class="btn" :class="plan.popular ? 'btn-primary' : 'btn-outline'">
              {{ t.pricing.start }}
            </router-link>
          </div>
        </div>
      </div>
    </section>

    <!-- Testimonials Section -->
    <section class="testimonials">
      <div class="container">
        <h2 class="section-title">{{ t.testimonials.title }}</h2>
        <p class="section-subtitle">{{ t.testimonials.subtitle }}</p>
        <div class="testimonials-grid">
          <div class="testimonial-card" v-for="(review, i) in t.testimonials.items" :key="i">
            <div class="testimonial-rating">★★★★★</div>
            <p class="testimonial-text">"{{ review.text }}"</p>
            <div class="testimonial-author">
              <div class="author-avatar">{{ review.name.charAt(0) }}</div>
              <div class="author-info">
                <div class="author-name">{{ review.name }}</div>
                <div class="author-role">{{ review.role }}</div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- FAQ Section -->
    <section id="faq" class="faq">
      <div class="container">
        <h2 class="section-title">{{ t.faq.title }}</h2>
        <div class="faq-list">
          <div class="faq-item" v-for="(item, i) in t.faq.items" :key="i" 
               :class="{ open: openFaq === i }" @click="openFaq = openFaq === i ? null : i">
            <div class="faq-question">{{ item.q }}<span class="faq-toggle">{{ openFaq === i ? '−' : '+' }}</span></div>
            <div class="faq-answer" v-show="openFaq === i">{{ item.a }}</div>
          </div>
        </div>
      </div>
    </section>

    <!-- CTA Section -->
    <section class="cta">
      <div class="container">
        <h2>{{ t.cta.title }}</h2>
        <p>{{ t.cta.subtitle }}</p>
        <router-link to="/register" class="btn btn-primary btn-lg">{{ t.cta.button }}</router-link>
      </div>
    </section>

    <!-- Footer -->
    <footer class="footer">
      <div class="container">
        <div class="footer-content">
          <div class="footer-logo">
            <span class="logo-icon">💳</span>
            <span class="logo-text">Paynes</span>
            <p>{{ t.footer.desc }}</p>
          </div>
          <div class="footer-links">
            <h4>{{ t.footer.product }}</h4>
            <a href="#features">{{ t.nav.features }}</a>
            <a href="#pricing">{{ t.nav.pricing }}</a>
            <a href="#faq">{{ t.nav.faq }}</a>
          </div>
          <div class="footer-contact">
            <h4>{{ t.footer.contact }}</h4>
            <p>📧 support@paynes.uz</p>
            <p>📱 +998 90 123 45 67</p>
            <p>💬 @paynes_support</p>
          </div>
        </div>
        <div class="footer-bottom">
          <p>© 2026 Paynes. {{ t.footer.rights }}</p>
        </div>
      </div>
    </footer>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue';

const currentLang = ref('ru');
const openFaq = ref(null);

const languages = [
  { code: 'ru', flag: '🇷🇺' },
  { code: 'uz', flag: '🇺🇿' },
  { code: 'en', flag: '🇬🇧' }
];

const setLang = (lang) => { currentLang.value = lang; };

const translations = {
  ru: {
    nav: { features: 'Возможности', pricing: 'Тарифы', faq: 'FAQ', login: 'Войти', register: 'Регистрация' },
    hero: {
      title: 'Современная система управления платежами',
      subtitle: 'Полный контроль бизнеса для агентов платежных систем. Платежи, обмен валют, кредиты и отчёты — всё в одном месте.',
      cta: 'Начать бесплатно',
      learn: 'Узнать больше',
      stats: { agents: 'Агентов', transactions: 'Транзакций', uptime: 'Аптайм' }
    },
    features: {
      title: 'Всё для вашего бизнеса',
      subtitle: 'Мощные инструменты для управления точкой приёма платежей',
      items: [
        { icon: '💳', title: 'Платежи', desc: 'Приём платежей с автоматическим расчётом комиссий' },
        { icon: '💱', title: 'Обмен валют', desc: 'Покупка и продажа USD с актуальными курсами' },
        { icon: '💰', title: 'Кредиты', desc: 'Выдача и отслеживание погашения кредитов' },
        { icon: '💵', title: 'Инкассация', desc: 'Полный учёт денежных операций и сдачи' },
        { icon: '👥', title: 'Кассиры', desc: 'Управление персоналом с ролями и правами' },
        { icon: '📊', title: 'Отчёты', desc: 'Детальная аналитика и экспорт в Excel/PDF' },
        { icon: '🔐', title: 'Безопасность', desc: 'Надёжная защита данных и транзакций' },
        { icon: '📱', title: 'Мобильность', desc: 'Работа с любого устройства через браузер' }
      ]
    },
    howItWorks: {
      title: 'Как это работает',
      steps: [
        { title: 'Регистрация', desc: 'Создайте аккаунт и настройте точку приёма платежей' },
        { title: 'Настройка', desc: 'Добавьте типы платежей, курсы валют и кассиров' },
        { title: 'Работа', desc: 'Принимайте платежи и контролируйте бизнес в реальном времени' }
      ]
    },
    pricing: {
      title: 'Выберите тариф',
      subtitle: 'Гибкие тарифы для бизнеса любого масштаба',
      popular: 'Популярный',
      month: 'мес',
      start: 'Начать',
      plans: [
        { name: 'Стартовый', price: '299 000 UZS', features: ['1 кассир', '1 филиал', 'Отчёты за 30 дней', 'Email поддержка'], popular: false },
        { name: 'Бизнес', price: '599 000 UZS', features: ['5 кассиров', '1 менеджер', '3 филиала', 'Неограниченные отчёты', 'Приоритетная поддержка'], popular: true },
        { name: 'Корпоративный', price: '1 199 000 UZS', features: ['15 кассиров', '3 менеджера', '10 филиалов', 'Брендинг', '24/7 поддержка'], popular: false }
      ]
    },
    faq: {
      title: 'Частые вопросы',
      items: [
        { q: 'Как начать работу с Paynes?', a: 'Зарегистрируйтесь, выберите тариф и начните принимать платежи. Настройка занимает 15 минут.' },
        { q: 'Есть ли пробный период?', a: 'Да, первые 14 дней бесплатно на любом тарифе.' },
        { q: 'Какие платёжные системы поддерживаются?', a: 'Paynet, Click, Payme, UzCard, Humo и другие популярные системы.' },
        { q: 'Как обеспечивается безопасность?', a: 'Шифрование данных, двухфакторная аутентификация, резервное копирование.' }
      ]
    },
    testimonials: {
      title: 'Отзывы клиентов',
      subtitle: 'Что говорят наши пользователи о Paynes',
      items: [
        { name: 'Алишер Каримов', role: 'Владелец точки, Ташкент', text: 'Раньше тратил часы на отчёты. Теперь всё автоматически. Экономлю 2 часа каждый день!' },
        { name: 'Мадина Рахимова', role: 'Менеджер сети, Самарканд', text: 'Контролирую 5 точек с телефона. Вижу все платежи и остатки в реальном времени.' },
        { name: 'Бахтиёр Усманов', role: 'Агент Paynet, Фергана', text: 'Система окупилась за первый месяц. Теперь нет ошибок в расчётах комиссий.' }
      ]
    },
    cta: { title: 'Готовы автоматизировать бизнес?', subtitle: 'Присоединяйтесь к 500+ агентам, которые уже используют Paynes', button: 'Начать бесплатно' },
    footer: { desc: 'Современная система для агентов платежных систем', product: 'Продукт', contact: 'Контакты', rights: 'Все права защищены' }
  },
  uz: {
    nav: { features: 'Imkoniyatlar', pricing: 'Tariflar', faq: 'FAQ', login: 'Kirish', register: 'Ro\'yxatdan o\'tish' },
    hero: {
      title: 'Zamonaviy to\'lov boshqaruv tizimi',
      subtitle: 'To\'lov tizimlari agentlari uchun biznesni to\'liq nazorat qilish. To\'lovlar, valyuta ayirboshlash, kreditlar va hisobotlar — barchasi bir joyda.',
      cta: 'Bepul boshlash',
      learn: 'Batafsil',
      stats: { agents: 'Agentlar', transactions: 'Tranzaksiyalar', uptime: 'Ish vaqti' }
    },
    features: {
      title: 'Biznesingiz uchun hamma narsa',
      subtitle: 'To\'lov qabul qilish nuqtasini boshqarish uchun kuchli vositalar',
      items: [
        { icon: '💳', title: 'To\'lovlar', desc: 'Komissiyalarni avtomatik hisoblash bilan to\'lovlarni qabul qilish' },
        { icon: '💱', title: 'Valyuta ayirboshlash', desc: 'Joriy kurslar bilan USD sotib olish va sotish' },
        { icon: '💰', title: 'Kreditlar', desc: 'Kreditlarni berish va qaytarishni kuzatish' },
        { icon: '💵', title: 'Inkassatsiya', desc: 'Pul operatsiyalari va topshirishni to\'liq hisobga olish' },
        { icon: '👥', title: 'Kassirlar', desc: 'Rollar va huquqlar bilan xodimlarni boshqarish' },
        { icon: '📊', title: 'Hisobotlar', desc: 'Batafsil tahlil va Excel/PDF ga eksport' },
        { icon: '🔐', title: 'Xavfsizlik', desc: 'Ma\'lumotlar va tranzaksiyalarni ishonchli himoya qilish' },
        { icon: '📱', title: 'Mobillik', desc: 'Brauzer orqali istalgan qurilmadan ishlash' }
      ]
    },
    howItWorks: {
      title: 'Bu qanday ishlaydi',
      steps: [
        { title: 'Ro\'yxatdan o\'tish', desc: 'Hisob yarating va to\'lov qabul qilish nuqtasini sozlang' },
        { title: 'Sozlash', desc: 'To\'lov turlarini, valyuta kurslarini va kassirlarni qo\'shing' },
        { title: 'Ishlash', desc: 'To\'lovlarni qabul qiling va biznesni real vaqtda nazorat qiling' }
      ]
    },
    pricing: {
      title: 'Tarifni tanlang',
      subtitle: 'Har qanday miqyosdagi biznes uchun moslashuvchan tariflar',
      popular: 'Mashhur',
      month: 'oy',
      start: 'Boshlash',
      plans: [
        { name: 'Boshlang\'ich', price: '299 000 UZS', features: ['1 kassir', '1 filial', '30 kunlik hisobotlar', 'Email yordam'], popular: false },
        { name: 'Biznes', price: '599 000 UZS', features: ['5 kassir', '1 menejer', '3 filial', 'Cheksiz hisobotlar', 'Ustuvor yordam'], popular: true },
        { name: 'Korporativ', price: '1 199 000 UZS', features: ['15 kassir', '3 menejer', '10 filial', 'Brending', '24/7 yordam'], popular: false }
      ]
    },
    faq: {
      title: 'Ko\'p beriladigan savollar',
      items: [
        { q: 'Paynes bilan qanday ishlashni boshlash mumkin?', a: 'Ro\'yxatdan o\'ting, tarifni tanlang va to\'lovlarni qabul qilishni boshlang. Sozlash 15 daqiqa davom etadi.' },
        { q: 'Sinov muddati bormi?', a: 'Ha, har qanday tarifda dastlabki 14 kun bepul.' },
        { q: 'Qaysi to\'lov tizimlari qo\'llab-quvvatlanadi?', a: 'Paynet, Click, Payme, UzCard, Humo va boshqa mashhur tizimlar.' },
        { q: 'Xavfsizlik qanday ta\'minlanadi?', a: 'Ma\'lumotlarni shifrlash, ikki faktorli autentifikatsiya, zaxira nusxa yaratish.' }
      ]
    },
    testimonials: {
      title: 'Mijozlar sharhlari',
      subtitle: 'Foydalanuvchilarimiz Paynes haqida nima deyishadi',
      items: [
        { name: 'Alisher Karimov', role: 'Nuqta egasi, Toshkent', text: 'Ilgari hisobotlarga soatlab vaqt sarflardim. Endi hammasi avtomatik. Har kuni 2 soat tejashyapman!' },
        { name: 'Madina Rahimova', role: 'Tarmoq menejeri, Samarqand', text: '5 ta nuqtani telefondan boshqaraman. Barcha to\'lovlar va qoldiqlarni real vaqtda ko\'raman.' },
        { name: 'Baxtiyor Usmanov', role: 'Paynet agenti, Farg\'ona', text: 'Tizim birinchi oyda o\'zini oqladi. Endi komissiyalarni hisoblashda xatolar yo\'q.' }
      ]
    },
    cta: { title: 'Biznesni avtomatlashtirish uchun tayyormisiz?', subtitle: 'Paynes dan foydalanayotgan 500+ agentlarga qo\'shiling', button: 'Bepul boshlash' },
    footer: { desc: 'To\'lov tizimlari agentlari uchun zamonaviy tizim', product: 'Mahsulot', contact: 'Aloqa', rights: 'Barcha huquqlar himoyalangan' }
  },
  en: {
    nav: { features: 'Features', pricing: 'Pricing', faq: 'FAQ', login: 'Login', register: 'Sign Up' },
    hero: {
      title: 'Modern Payment Management System',
      subtitle: 'Complete business control for payment system agents. Payments, currency exchange, credits and reports — all in one place.',
      cta: 'Start Free',
      learn: 'Learn More',
      stats: { agents: 'Agents', transactions: 'Transactions', uptime: 'Uptime' }
    },
    features: {
      title: 'Everything for your business',
      subtitle: 'Powerful tools for managing payment acceptance points',
      items: [
        { icon: '💳', title: 'Payments', desc: 'Accept payments with automatic commission calculation' },
        { icon: '💱', title: 'Currency Exchange', desc: 'Buy and sell USD with current rates' },
        { icon: '💰', title: 'Credits', desc: 'Issue and track credit repayments' },
        { icon: '💵', title: 'Collection', desc: 'Complete accounting of cash operations' },
        { icon: '👥', title: 'Cashiers', desc: 'Staff management with roles and permissions' },
        { icon: '📊', title: 'Reports', desc: 'Detailed analytics and Excel/PDF export' },
        { icon: '🔐', title: 'Security', desc: 'Reliable data and transaction protection' },
        { icon: '📱', title: 'Mobility', desc: 'Work from any device via browser' }
      ]
    },
    howItWorks: {
      title: 'How it works',
      steps: [
        { title: 'Registration', desc: 'Create an account and set up your payment point' },
        { title: 'Setup', desc: 'Add payment types, exchange rates and cashiers' },
        { title: 'Work', desc: 'Accept payments and control your business in real time' }
      ]
    },
    pricing: {
      title: 'Choose your plan',
      subtitle: 'Flexible plans for businesses of any scale',
      popular: 'Popular',
      month: 'mo',
      start: 'Get Started',
      plans: [
        { name: 'Starter', price: '299 000 UZS', features: ['1 cashier', '1 branch', '30-day reports', 'Email support'], popular: false },
        { name: 'Business', price: '599 000 UZS', features: ['5 cashiers', '1 manager', '3 branches', 'Unlimited reports', 'Priority support'], popular: true },
        { name: 'Enterprise', price: '1 199 000 UZS', features: ['15 cashiers', '3 managers', '10 branches', 'Branding', '24/7 support'], popular: false }
      ]
    },
    faq: {
      title: 'FAQ',
      items: [
        { q: 'How to start with Paynes?', a: 'Register, choose a plan and start accepting payments. Setup takes 15 minutes.' },
        { q: 'Is there a trial period?', a: 'Yes, first 14 days free on any plan.' },
        { q: 'Which payment systems are supported?', a: 'Paynet, Click, Payme, UzCard, Humo and other popular systems.' },
        { q: 'How is security ensured?', a: 'Data encryption, two-factor authentication, backup.' }
      ]
    },
    testimonials: {
      title: 'Customer Reviews',
      subtitle: 'What our users say about Paynes',
      items: [
        { name: 'Alisher Karimov', role: 'Shop Owner, Tashkent', text: 'Used to spend hours on reports. Now everything is automatic. Saving 2 hours every day!' },
        { name: 'Madina Rahimova', role: 'Network Manager, Samarkand', text: 'I control 5 locations from my phone. See all payments and balances in real time.' },
        { name: 'Bakhtiyor Usmanov', role: 'Paynet Agent, Fergana', text: 'The system paid for itself in the first month. No more errors in commission calculations.' }
      ]
    },
    cta: { title: 'Ready to automate your business?', subtitle: 'Join 500+ agents already using Paynes', button: 'Start Free' },
    footer: { desc: 'Modern system for payment system agents', product: 'Product', contact: 'Contact', rights: 'All rights reserved' }
  }
};

const t = computed(() => translations[currentLang.value]);
</script>

<style scoped>
* { box-sizing: border-box; margin: 0; padding: 0; }

.landing-page {
  font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
  background: #f8fafc;
  color: #1f2937;
  min-height: 100vh;
}

.container { max-width: 1200px; margin: 0 auto; padding: 0 20px; }

/* Header */
.header {
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  z-index: 100;
  background: rgba(255, 255, 255, 0.95);
  backdrop-filter: blur(10px);
  border-bottom: 1px solid #e5e7eb;
  box-shadow: 0 1px 3px rgba(0,0,0,0.05);
}

.header .container {
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 15px 20px;
}

.logo { display: flex; align-items: center; gap: 10px; }
.logo-icon { font-size: 28px; }
.logo-text { font-size: 24px; font-weight: 700; color: #1f6fd6; }

.nav { display: flex; gap: 30px; }
.nav a { color: #6b7280; text-decoration: none; transition: 0.3s; font-weight: 500; }
.nav a:hover { color: #1f6fd6; }

.header-actions { display: flex; align-items: center; gap: 15px; }
.lang-switcher { display: flex; gap: 5px; }
.lang-switcher button { background: transparent; border: none; font-size: 20px; cursor: pointer; opacity: 0.5; transition: 0.3s; padding: 5px; }
.lang-switcher button.active, .lang-switcher button:hover { opacity: 1; }

/* Buttons */
.btn {
  display: inline-block;
  padding: 12px 24px;
  border-radius: 8px;
  text-decoration: none;
  font-weight: 600;
  transition: all 0.3s;
  cursor: pointer;
  border: none;
}

.btn-primary { background: #1f6fd6; color: #fff; }
.btn-primary:hover { background: #1a5bb8; transform: translateY(-2px); box-shadow: 0 10px 30px rgba(31, 111, 214, 0.3); }
.btn-outline { background: transparent; border: 2px solid #1f6fd6; color: #1f6fd6; }
.btn-outline:hover { background: #1f6fd6; color: #fff; }
.btn-ghost { background: transparent; color: #6b7280; }
.btn-ghost:hover { color: #1f6fd6; }
.btn-lg { padding: 16px 32px; font-size: 18px; }

/* Hero */
.hero {
  min-height: 100vh;
  display: flex;
  align-items: center;
  padding-top: 80px;
  position: relative;
  overflow: hidden;
  background: linear-gradient(135deg, #f0f9ff 0%, #e0f2fe 50%, #f0f9ff 100%);
}

.hero-bg {
  position: absolute;
  inset: 0;
  background: radial-gradient(circle at 30% 50%, rgba(31, 111, 214, 0.1), transparent 50%),
              radial-gradient(circle at 70% 50%, rgba(14, 165, 233, 0.1), transparent 50%);
}

.hero-content { position: relative; text-align: center; max-width: 800px; margin: 0 auto; }
.hero-title { font-size: clamp(32px, 6vw, 56px); font-weight: 800; line-height: 1.2; margin-bottom: 20px; color: #0f172a; }
.hero-subtitle { font-size: clamp(16px, 2vw, 20px); color: #64748b; margin-bottom: 40px; line-height: 1.6; }
.hero-buttons { display: flex; gap: 15px; justify-content: center; flex-wrap: wrap; margin-bottom: 60px; }
.hero-stats { display: flex; gap: 50px; justify-content: center; flex-wrap: wrap; }
.stat { text-align: center; }
.stat-value { font-size: 36px; font-weight: 800; color: #1f6fd6; display: block; }
.stat-label { font-size: 14px; color: #64748b; }

/* Features */
.features { padding: 100px 0; background: #fff; }
.section-title { text-align: center; font-size: clamp(28px, 4vw, 40px); font-weight: 700; margin-bottom: 15px; color: #0f172a; }
.section-subtitle { text-align: center; color: #64748b; margin-bottom: 60px; font-size: 18px; }
.features-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 30px; }
.feature-card {
  background: #fff;
  border: 1px solid #e5e7eb;
  border-radius: 16px;
  padding: 30px;
  transition: all 0.3s;
  box-shadow: 0 1px 3px rgba(0,0,0,0.05);
}
.feature-card:hover { transform: translateY(-5px); box-shadow: 0 20px 40px rgba(0,0,0,0.1); border-color: #1f6fd6; }
.feature-icon { font-size: 40px; margin-bottom: 15px; }
.feature-title { font-size: 18px; font-weight: 600; margin-bottom: 10px; color: #0f172a; }
.feature-desc { color: #64748b; font-size: 14px; line-height: 1.6; }

/* How It Works */
.how-it-works { padding: 100px 0; background: #f8fafc; }
.steps { display: flex; gap: 40px; justify-content: center; flex-wrap: wrap; }
.step { text-align: center; max-width: 300px; }
.step-number { width: 60px; height: 60px; background: #1f6fd6; color: #fff; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 24px; font-weight: 700; margin: 0 auto 20px; }
.step-title { font-size: 18px; font-weight: 600; margin-bottom: 10px; color: #0f172a; }
.step-desc { color: #64748b; font-size: 14px; }

/* Pricing */
.pricing { padding: 100px 0; background: #fff; }
.pricing-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 30px; max-width: 1000px; margin: 0 auto; }
.pricing-card {
  background: #fff;
  border: 2px solid #e5e7eb;
  border-radius: 20px;
  padding: 40px 30px;
  text-align: center;
  transition: all 0.3s;
}
.pricing-card:hover { transform: translateY(-10px); box-shadow: 0 25px 50px rgba(0,0,0,0.1); }
.pricing-card.popular { border-color: #1f6fd6; position: relative; transform: scale(1.05); }
.pricing-card.popular:hover { transform: scale(1.05) translateY(-10px); }
.popular-badge { position: absolute; top: -12px; left: 50%; transform: translateX(-50%); background: #1f6fd6; color: #fff; padding: 5px 20px; border-radius: 20px; font-size: 12px; font-weight: 600; }
.pricing-name { font-size: 22px; font-weight: 700; margin-bottom: 10px; color: #0f172a; }
.pricing-price { font-size: 36px; font-weight: 800; color: #1f6fd6; margin-bottom: 5px; }
.pricing-period { color: #64748b; margin-bottom: 30px; font-size: 14px; }
.pricing-features { list-style: none; margin-bottom: 30px; }
.pricing-features li { padding: 10px 0; color: #64748b; font-size: 14px; border-bottom: 1px solid #f1f5f9; }
.pricing-features li:last-child { border-bottom: none; }

/* FAQ */
.faq { padding: 100px 0; background: #f8fafc; }
.faq-list { max-width: 700px; margin: 0 auto; }
.faq-item {
  background: #fff;
  border: 1px solid #e5e7eb;
  border-radius: 12px;
  margin-bottom: 15px;
  overflow: hidden;
  cursor: pointer;
  transition: all 0.3s;
}
.faq-item:hover { border-color: #1f6fd6; }
.faq-question { padding: 20px; display: flex; justify-content: space-between; align-items: center; font-weight: 600; color: #0f172a; }
.faq-toggle { font-size: 24px; color: #1f6fd6; }
.faq-answer { padding: 0 20px 20px; color: #64748b; line-height: 1.6; }

/* Demo Screenshot */
.demo-section {
  padding: 0 0 60px;
  margin-top: -60px;
  position: relative;
  z-index: 10;
}
.demo-wrapper {
  background: #fff;
  border: 1px solid #e5e7eb;
  border-radius: 20px;
  padding: 12px;
  box-shadow: 0 25px 50px rgba(0,0,0,0.1);
}

/* Inline Demo Dashboard */
.demo-placeholder {
  background: #f8fafc;
  border-radius: 16px;
  overflow: hidden;
}
.demo-content {
  display: flex;
  min-height: 400px;
}
.demo-sidebar {
  width: 180px;
  background: #1f2937;
  padding: 20px 15px;
  flex-shrink: 0;
}
.demo-logo {
  color: #1f6fd6;
  font-weight: 700;
  font-size: 16px;
  margin-bottom: 25px;
}
.demo-nav-item {
  color: #9ca3af;
  font-size: 13px;
  padding: 10px 12px;
  border-radius: 8px;
  margin-bottom: 5px;
}
.demo-nav-item.active {
  background: #1f6fd6;
  color: #fff;
}
.demo-main {
  flex: 1;
  padding: 25px;
  background: #f1f5f9;
}
.demo-kpi-row {
  display: grid;
  grid-template-columns: repeat(4, 1fr);
  gap: 15px;
  margin-bottom: 25px;
}
.demo-kpi {
  background: #fff;
  border-radius: 12px;
  padding: 18px;
  text-align: center;
  box-shadow: 0 1px 3px rgba(0,0,0,0.05);
}
.kpi-value {
  display: block;
  font-size: 24px;
  font-weight: 700;
  color: #1f2937;
}
.kpi-label {
  font-size: 12px;
  color: #6b7280;
}
.demo-table {
  background: #fff;
  border-radius: 12px;
  padding: 20px;
  box-shadow: 0 1px 3px rgba(0,0,0,0.05);
}
.demo-table-header {
  font-weight: 700;
  color: #1f2937;
  margin-bottom: 15px;
  font-size: 15px;
}
.demo-table-row {
  display: flex;
  justify-content: space-between;
  padding: 12px 0;
  border-bottom: 1px solid #e5e7eb;
  font-size: 13px;
  color: #374151;
}
.demo-table-row:last-child { border-bottom: none; }
.status-badge {
  background: #dcfce7;
  color: #16a34a;
  padding: 2px 8px;
  border-radius: 10px;
  font-size: 11px;
}

/* Testimonials */
.testimonials { padding: 100px 0; background: #fff; }
.testimonials-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 25px; }
.testimonial-card {
  background: #f8fafc;
  border: 1px solid #e5e7eb;
  border-radius: 16px;
  padding: 30px;
  transition: all 0.3s;
}
.testimonial-card:hover { transform: translateY(-5px); border-color: #1f6fd6; box-shadow: 0 15px 30px rgba(0,0,0,0.1); }
.testimonial-rating { color: #f59e0b; font-size: 16px; margin-bottom: 15px; letter-spacing: 2px; }
.testimonial-text { font-size: 15px; line-height: 1.7; color: #374151; margin-bottom: 20px; font-style: italic; }
.testimonial-author { display: flex; align-items: center; gap: 12px; }
.author-avatar {
  width: 44px;
  height: 44px;
  background: #1f6fd6;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  font-weight: 700;
  font-size: 18px;
  color: #fff;
}
.author-name { font-weight: 600; font-size: 15px; color: #0f172a; }
.author-role { font-size: 13px; color: #64748b; }

/* CTA */
.cta {
  padding: 100px 0;
  text-align: center;
  background: linear-gradient(135deg, #1f6fd6 0%, #0ea5e9 100%);
  color: #fff;
}
.cta h2 { font-size: clamp(28px, 4vw, 38px); margin-bottom: 15px; color: #fff; }
.cta p { color: rgba(255,255,255,0.9); margin-bottom: 30px; }
.cta .btn-primary { background: #fff; color: #1f6fd6; }
.cta .btn-primary:hover { background: #f8fafc; }

/* Footer */
.footer { padding: 60px 0 30px; background: #0f172a; color: #fff; }
.footer-content { display: grid; grid-template-columns: 2fr 1fr 1fr; gap: 40px; margin-bottom: 40px; }
.footer-logo .logo-text { font-size: 20px; color: #1f6fd6; }
.footer-logo p { color: #94a3b8; margin-top: 10px; font-size: 14px; }
.footer-links h4, .footer-contact h4 { margin-bottom: 15px; font-size: 16px; color: #fff; }
.footer-links a { display: block; color: #94a3b8; text-decoration: none; padding: 5px 0; transition: 0.3s; }
.footer-links a:hover { color: #fff; }
.footer-contact p { color: #94a3b8; padding: 5px 0; font-size: 14px; }
.footer-bottom { text-align: center; padding-top: 30px; border-top: 1px solid #1e293b; color: #64748b; font-size: 14px; }

/* Responsive */
@media (max-width: 768px) {
  .nav { display: none; }
  .footer-content { grid-template-columns: 1fr; text-align: center; }
  .features-list { text-align: center; }
  .hero-stats { gap: 30px; }
  .pricing-card.popular { transform: none; }
  .pricing-card.popular:hover { transform: translateY(-5px); }
  .demo-content { flex-direction: column; }
  .demo-sidebar { width: 100%; }
  .demo-kpi-row { grid-template-columns: repeat(2, 1fr); }
}
</style>

