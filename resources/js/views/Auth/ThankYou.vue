<template>
  <div class="thank-you-container">
    <div class="thank-you-card">
      <div class="success-icon">✅</div>
      <h1>Спасибо за регистрацию!</h1>
      <p class="subtitle">Ваша заявка успешно отправлена</p>
      
      <div class="info-box">
        <h3>Что дальше?</h3>
        <ul class="steps-list">
          <li>
            <span class="step-num">1</span>
            <span>Наш менеджер свяжется с вами в течение 24 часов</span>
          </li>
          <li>
            <span class="step-num">2</span>
            <span>Мы активируем ваш аккаунт после проверки данных</span>
          </li>
          <li>
            <span class="step-num">3</span>
            <span>Вы получите письмо с инструкциями на указанный email</span>
          </li>
        </ul>
      </div>

      <div class="contact-info">
        <p>Есть вопросы? Свяжитесь с нами:</p>
        <p>📧 support@paynes.uz</p>
        <p>📱 +998 90 123 45 67</p>
      </div>

      <div class="actions">
        <router-link to="/landing" class="btn btn-primary">На главную</router-link>
        <router-link to="/login" class="btn btn-outline">Войти в систему</router-link>
      </div>
    </div>
  </div>
</template>

<script setup>
</script>

<style scoped>
.thank-you-container {
  min-height: 100vh;
  display: flex;
  align-items: center;
  justify-content: center;
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  padding: 20px;
}

.thank-you-card {
  background: white;
  border-radius: 20px;
  box-shadow: 0 25px 50px rgba(0,0,0,0.25);
  max-width: 500px;
  width: 100%;
  padding: 50px 40px;
  text-align: center;
  animation: slideUp 0.5s ease-out;
}

@keyframes slideUp {
  from { opacity: 0; transform: translateY(30px); }
  to { opacity: 1; transform: translateY(0); }
}

.success-icon {
  font-size: 64px;
  margin-bottom: 20px;
}

h1 {
  color: #333;
  font-size: 28px;
  margin: 0 0 10px;
}

.subtitle {
  color: #666;
  font-size: 16px;
  margin: 0 0 30px;
}

.info-box {
  background: #f8f9fa;
  border-radius: 12px;
  padding: 25px;
  margin-bottom: 25px;
  text-align: left;
}

.info-box h3 {
  color: #333;
  font-size: 18px;
  margin: 0 0 15px;
}

.steps-list {
  list-style: none;
  padding: 0;
  margin: 0;
}

.steps-list li {
  display: flex;
  align-items: center;
  gap: 12px;
  padding: 10px 0;
  color: #555;
  font-size: 14px;
}

.step-num {
  width: 28px;
  height: 28px;
  background: linear-gradient(135deg, #667eea, #764ba2);
  color: white;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 13px;
  font-weight: 600;
  flex-shrink: 0;
}

.contact-info {
  margin-bottom: 25px;
  color: #666;
  font-size: 14px;
}

.contact-info p {
  margin: 5px 0;
}

.actions {
  display: flex;
  gap: 12px;
  justify-content: center;
  flex-wrap: wrap;
}

.btn {
  padding: 12px 28px;
  border-radius: 8px;
  text-decoration: none;
  font-weight: 600;
  font-size: 14px;
  transition: all 0.3s;
}

.btn-primary {
  background: linear-gradient(135deg, #667eea, #764ba2);
  color: white;
}

.btn-primary:hover {
  transform: translateY(-2px);
  box-shadow: 0 8px 20px rgba(102, 126, 234, 0.4);
}

.btn-outline {
  background: transparent;
  border: 2px solid #667eea;
  color: #667eea;
}

.btn-outline:hover {
  background: #667eea;
  color: white;
}
</style>
