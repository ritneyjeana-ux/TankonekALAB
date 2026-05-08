<template>
  <div class="landing-wrapper">
    <!-- Header -->
    <header class="header">
      <div class="header-logo">
        <img :src="logo" alt="Tankonek Logo" class="logo-img" />
        <span class="logo-text">Tankonek</span>
      </div>
      <button class="btn-header" @click="goToLogin">Login</button>
    </header>

    <!-- Hero Section -->
    <section class="hero">
      <h5 class="hero-subtitle">Fast, Safe & Convenient Gas Delivery</h5>
      <p class="hero-description">
        Connect with nearby gas retailers for efficient cooking gas delivery.
        Order online, track in-real time, and support your local community.
      </p>
      <div class="hero-buttons">
      </div>
    </section>

    <!-- Why Choose -->
    <section class="why-section">
      <h3>Why Choose Tankonek?</h3>
      <div class="why-grid">
        <div class="why-card" v-for="(item, index) in whyItems" :key="index">
          <div class="icon">{{ item.icon }}</div>
          <h4>{{ item.title }}</h4>
          <p>{{ item.text }}</p>
        </div>
      </div>
    </section>

    <!-- Join Section -->
    <section class="join-section">
      <h3>Join tankonek Today</h3>
      <div class="join-grid">
        <div class="join-card" v-for="(join, i) in joinOptions" :key="i">
          <h4>{{ join.title }}</h4>
          <p>{{ join.text }}</p>
          <button class="btn-primary" @click="openAuthModal(join.type)">
            {{ join.button }}
          </button>
        </div>
      </div>
    </section>

    <!-- Footer -->
    <footer class="footer">
      <h4 class="footer-logo">tankonek</h4>
      <p>Making cooking gas delivery fast, safe, and convenient for everyone.</p>
    </footer>

    <!-- Auth Modal -->
    <div v-if="showAuthModal" class="modal-backdrop">
      <div class="modal-box">
        <button class="close-btn" @click="showAuthModal = false">×</button>

        <div class="tab-switch">
          <button :class="{ active: isLogin }" @click="isLogin = true">Login</button>
          <button :class="{ active: !isLogin }" @click="isLogin = false">Sign Up</button>
        </div>

        <div class="modal-body">
          <component 
            :is="isLogin ? Login : Signup" 
            @switch-to-login="handleSwitchToLogin"
            @switch-to-signup="handleSwitchToSignup"
          />
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref } from "vue";

// ✅ Import unified Login and Signup components
import Login from "./Login.vue";
import Signup from "./Signup.vue";

// ✅ Use public path for logo
const logo = '/images/logo.png';

const showAuthModal = ref(false);
const isLogin = ref(true);

// Default account type when clicking login button
const defaultAccountType = ref('customer');

const goToLogin = () => {
  isLogin.value = true;
  defaultAccountType.value = 'customer';
  showAuthModal.value = true;
};

const openAuthModal = (accountType = 'customer') => {
  isLogin.value = false; // Default to Signup when clicking "Join as" buttons
  defaultAccountType.value = accountType;
  showAuthModal.value = true;
};

const handleSwitchToLogin = (accountType) => {
  isLogin.value = true;
  defaultAccountType.value = accountType || 'customer';
};

const handleSwitchToSignup = (accountType) => {
  isLogin.value = false;
  defaultAccountType.value = accountType || 'customer';
};

const whyItems = [
  { icon: "", title: "Find Nearby Suppliers", text: "Discover and order from gas retailers in your area with real-time availability." },
  { icon: "", title: "Fast Delivery", text: "Community delivery riders ensure quick and reliable gas delivery to your doorstep." },
  { icon: "", title: "Real-time Updates", text: "Get instant notifications for order status and delivery tracking." },
  { icon: "", title: "Inventory Management", text: "Retailers can manage stock levels with automatic low-stock alerts." },
  { icon: "", title: "Safe & Secure", text: "Verified retailers and partners ensure quality and safety standards." },
  { icon: "", title: "Support Local Business", text: "Empower local retailers and create income opportunities for your community." },
];

const joinOptions = [
  { 
    type: 'customer',
    title: "For Customers", 
    text: "Order cooking gas online, track delivery, and enjoy convenient home delivery.", 
    button: "Start Ordering" 
  },
  { 
    type: 'retailer',
    title: "For Retailers", 
    text: "Manage orders, track inventory, and grow your gas distribution business.", 
    button: "Join as Retailer" 
  },
  { 
    type: 'rider',
    title: "For Delivery Partners", 
    text: "Earn extra income by delivering gas to customers in your community.", 
    button: "Become a Rider" 
  },
];
</script>

<style scoped>
.landing-wrapper {
  font-family: 'Inter', sans-serif;
  background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
  min-height: 100vh;
}

/* Header */
.header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 1.5rem 2rem;
  background: rgba(255, 255, 255, 0.95);
  backdrop-filter: blur(10px);
  border-bottom: 1px solid #e2e8f0;
  position: sticky;
  top: 0;
  z-index: 100;
}

.header-logo {
  display: flex;
  align-items: center;
  gap: 0.75rem;
}

.logo-img {
  width: 100px;
  height: 100px;
  border-radius: 10px;
  box-shadow: 0 4px 12px rgba(255, 120, 40, 0.3);
  border: 2px solid rgba(255, 120, 40, 0.2);
}

.logo-text {
  font-size: 1.5rem;
  font-weight: 100px;
  color: #1a202c;
  background: linear-gradient(135deg, #FF7828, #e6691f);
  -webkit-background-clip: text;
  -webkit-text-fill-color: transparent;
}

.btn-header {
  background: #0a0a0a;
  color: white;
  border: none;
  padding: 0.75rem 1.5rem;
  border-radius: 10px;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.3s ease;
  box-shadow: 0 4px 12px rgba(255, 120, 40, 0.3);
}

.btn-header:hover {
  background: #e6691f;
  transform: translateY(-2px);
  box-shadow: 0 6px 20px rgba(255, 120, 40, 0.4);
}

/* Hero Section */
.hero {
  text-align: center;
  padding: 4rem 2rem;
  max-width: 800px;
  margin: 0 auto;
}

.hero-subtitle {
  font-size: 1.1rem;
  color: #FF7828;
  font-weight: 600;
  margin-bottom: 1rem;
  text-transform: uppercase;
  letter-spacing: 1px;
}

.hero-description {
  font-size: 2.5rem;
  font-weight: 700;
  color: #1a202c;
  line-height: 1.2;
  margin-bottom: 2rem;
}

.hero-buttons {
  display: flex;
  gap: 1rem;
  justify-content: center;
}

.btn-secondary {
  background: transparent;
  color: #FF7828;
  border: 2px solid #FF7828;
  padding: 0.75rem 2rem;
  border-radius: 10px;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.3s ease;
}

.btn-secondary:hover {
  background: #FF7828;
  color: white;
  transform: translateY(-2px);
  box-shadow: 0 6px 20px rgba(255, 120, 40, 0.3);
}

/* Why Section */
.why-section {
  padding: 4rem 2rem;
  background: white;
}

.why-section h3 {
  text-align: center;
  font-size: 2rem;
  font-weight: 700;
  color: #1a202c;
  margin-bottom: 3rem;
}

.why-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
  gap: 2rem;
  max-width: 1200px;
  margin: 0 auto;
}

.why-card {
  background: #f8fafc;
  padding: 2rem;
  border-radius: 16px;
  text-align: center;
  border: 1px solid #e2e8f0;
  transition: all 0.3s ease;
  position: relative;
  overflow: hidden;
}

.why-card::before {
  content: '';
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  height: 4px;
  background: #FF7828;
}

.why-card:hover {
  transform: translateY(-4px);
  box-shadow: 0 8px 30px rgba(0, 0, 0, 0.1);
  border-color: #FF7828;
}

.why-card .icon {
  font-size: 2.5rem;
  margin-bottom: 1rem;
  color: #FF7828;
}

.why-card h4 {
  font-size: 1.25rem;
  font-weight: 600;
  color: #1a202c;
  margin-bottom: 1rem;
}

.why-card p {
  color: #718096;
  line-height: 1.6;
}

/* Join Section */
.join-section {
  padding: 4rem 2rem;
  background: #ffffff;
  color: white;
  position: relative;
  overflow: hidden;
}

.join-section::before {
  content: '';
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: radial-gradient(circle at 30% 70%, rgba(255, 120, 40, 0.1) 0%, transparent 50%),
              radial-gradient(circle at 70% 30%, rgba(0, 0, 0, 0.2) 0%, transparent 50%);
}

.join-section h3 {
  text-align: center;
  font-size: 2rem;
  font-weight: 700;
  margin-bottom: 3rem;
  position: relative;
  z-index: 1;
}

.join-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
  gap: 2rem;
  max-width: 1200px;
  margin: 0 auto;
  position: relative;
  z-index: 1;
}

.join-card {
  background: rgba(255, 255, 255, 0.95);
  color: #1a202c;
  padding: 2.5rem 2rem;
  border-radius: 16px;
  text-align: center;
  border: 1px solid rgba(255, 255, 255, 0.3);
  transition: all 0.3s ease;
  position: relative;
  overflow: hidden;
  box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
}

.join-card::before {
  content: '';
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  height: 4px;
  background: linear-gradient(90deg, #FF7828, #000000);
}

.join-card:hover {
  transform: translateY(-8px);
  box-shadow: 0 16px 40px rgba(0, 0, 0, 0.2);
  border-color: #FF7828;
}

.join-card h4 {
  font-size: 1.25rem;
  font-weight: 700;
  margin-bottom: 1rem;
  color: #1a202c;
}

.join-card p {
  margin-bottom: 2rem;
  line-height: 1.6;
  color: #4a5568;
  font-size: 0.95rem;
}

.btn-primary {
  background: linear-gradient(135deg, #000000 0%, #1a202c 100%);
  color: white;
  border: 2px solid transparent;
  padding: 0.75rem 2rem;
  border-radius: 10px;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.3s ease;
  width: 100%;
  position: relative;
  overflow: hidden;
}

.btn-primary::before {
  content: '';
  position: absolute;
  top: 0;
  left: -100%;
  width: 100%;
  height: 100%;
  background: linear-gradient(135deg, #FF7828 0%, #e6691f 100%);
  transition: left 0.3s ease;
  z-index: -1;
}

.btn-primary:hover {
  color: white;
  border-color: #FF7828;
  transform: translateY(-2px);
  box-shadow: 0 6px 20px rgba(255, 120, 40, 0.3);
}

.btn-primary:hover::before {
  left: 0;
}

/* Footer */
.footer {
  background: #1a202c;
  color: white;
  text-align: center;
  padding: 3rem 2rem;
}

.footer-logo {
  font-size: 1.5rem;
  font-weight: 700;
  margin-bottom: 1rem;
  color: #FF7828;
}

.footer p {
  color: #a0aec0;
  max-width: 400px;
  margin: 0 auto;
  line-height: 1.6;
}

/* Modal Styles */
.modal-backdrop {
  position: fixed;
  inset: 0;
  background: rgba(0, 0, 0, 0.5);
  display: flex;
  justify-content: center;
  align-items: center;
  z-index: 1000;
  backdrop-filter: blur(4px);
}

.modal-box {
  background: white;
  border-radius: 20px;
  padding: 2rem;
  width: 450px;
  max-width: 90vw;
  max-height: 90vh;
  overflow-y: auto;
  position: relative;
  box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
}

.close-btn {
  position: absolute;
  top: 1rem;
  right: 1rem;
  background: none;
  border: none;
  font-size: 1.5rem;
  cursor: pointer;
  color: #718096;
  transition: color 0.3s ease;
  z-index: 10;
}

.close-btn:hover {
  color: #1a202c;
}

.tab-switch {
  display: flex;
  margin-bottom: 2rem;
  background: #f7fafc;
  border-radius: 10px;
  padding: 0.25rem;
}

.tab-switch button {
  flex: 1;
  padding: 0.75rem 1rem;
  border: none;
  background: transparent;
  cursor: pointer;
  border-radius: 8px;
  font-weight: 600;
  transition: all 0.3s ease;
}

.tab-switch button.active {
  background: #FF7828;
  color: white;
  box-shadow: 0 2px 8px rgba(255, 120, 40, 0.3);
}

.modal-body {
  min-height: 400px;
}

/* Responsive Design */
@media (max-width: 768px) {
  .header {
    padding: 1rem;
  }
  
  .hero {
    padding: 2rem 1rem;
  }
  
  .hero-description {
    font-size: 1.8rem;
  }
  
  .hero-buttons {
    flex-direction: column;
    align-items: center;
  }
  
  .why-section,
  .join-section {
    padding: 2rem 1rem;
  }
  
  .why-grid,
  .join-grid {
    grid-template-columns: 1fr;
  }
  
  .modal-box {
    width: 90vw;
    padding: 1.5rem;
  }
}
</style>