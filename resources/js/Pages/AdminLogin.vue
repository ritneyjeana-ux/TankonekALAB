<template>
  <div class="admin-login-wrapper">
    <!-- Main Login Form -->
    <main class="login-main">
      <div class="login-container">
        <div class="login-card">
          <div class="login-header">
            <h1>Admin Portal</h1>
            <p>Sign in to access the management dashboard</p>
          </div>

          <div v-if="errorMessage" class="error-message">
            {{ errorMessage }}
          </div>

          <form @submit.prevent="handleLogin" class="login-form">
            <div class="form-group">
              <label>Email Address</label>
              <input 
                type="email" 
                v-model="email" 
                required
                placeholder="admin@tankonek.com"
              >
            </div>

            <div class="form-group">
              <label>Password</label>
              <input 
                type="password" 
                v-model="password" 
                required
                placeholder="Enter admin password"
              >
            </div>

            <button 
              type="submit" 
              class="login-btn"
              :disabled="loading"
            >
              {{ loading ? 'Signing in...' : 'Sign In' }}
            </button>
          </form>
        </div>
      </div>
    </main>

    <!-- Footer -->
    <footer class="admin-footer">
      <p>&copy; 2026 Tankonek. Admin Portal - Secure platform management system</p>
    </footer>
  </div>
</template>

<script setup>
import { ref } from "vue";
import { router } from '@inertiajs/vue3';
import { auth } from "@/firebase";
import { signInWithEmailAndPassword } from "firebase/auth";

const email = ref('admin@gmail.com');
const password = ref('admin123');
const loading = ref(false);
const errorMessage = ref('');

const handleLogin = async () => {
  loading.value = true;
  errorMessage.value = '';
  
  try {
    // Sign in with Firebase
    const userCredential = await signInWithEmailAndPassword(auth, email.value, password.value);
    console.log("✅ Logged in to Firebase:", userCredential.user.email);
    
    // Store admin info in localStorage
    localStorage.setItem("admin", JSON.stringify({
      email: userCredential.user.email,
      uid: userCredential.user.uid
    }));
    
    // Redirect to dashboard using Inertia
    router.visit('/admin/dashboard');
    
  } catch (error) {
    console.error("❌ Login error:", error);
    
    // Handle different error types
    switch (error.code) {
      case 'auth/user-not-found':
        errorMessage.value = 'No account found with this email';
        break;
      case 'auth/wrong-password':
        errorMessage.value = 'Invalid password';
        break;
      case 'auth/invalid-email':
        errorMessage.value = 'Invalid email format';
        break;
      case 'auth/too-many-requests':
        errorMessage.value = 'Too many failed attempts. Try again later.';
        break;
      default:
        errorMessage.value = 'An error occurred. Please try again.';
    }
  } finally {
    loading.value = false;
  }
};
</script>

<style scoped>
.admin-login-wrapper {
  font-family: 'Inter', sans-serif;
  background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
  min-height: 100vh;
  display: flex;
  flex-direction: column;
}

.login-main {
  flex: 1;
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 2rem;
}

.login-container {
  width: 100%;
  max-width: 400px;
}

.login-card {
  background: white;
  padding: 3rem 2rem;
  border-radius: 20px;
  box-shadow: 0 10px 40px rgba(0, 0, 0, 0.1);
  border: 1px solid #e2e8f0;
  text-align: center;
}

.login-header {
  margin-bottom: 2rem;
}

.login-header h1 {
  font-size: 2rem;
  font-weight: 700;
  color: #1a202c;
  margin-bottom: 0.5rem;
}

.login-header p {
  color: #718096;
  font-size: 0.95rem;
}

.error-message {
  background-color: #fee2e2;
  color: #dc2626;
  padding: 0.75rem 1rem;
  border-radius: 8px;
  margin-bottom: 1.5rem;
  font-size: 0.9rem;
  text-align: center;
  border: 1px solid #fecaca;
}

.login-form {
  text-align: left;
}

.form-group {
  margin-bottom: 1.5rem;
}

.form-group label {
  display: block;
  margin-bottom: 0.5rem;
  font-weight: 600;
  color: #1a202c;
}

.form-group input {
  width: 100%;
  padding: 0.75rem 1rem;
  border: 2px solid #e2e8f0;
  border-radius: 8px;
  font-size: 1rem;
  transition: all 0.3s ease;
  box-sizing: border-box;
}

.form-group input:focus {
  outline: none;
  border-color: #FF7828;
  box-shadow: 0 0 0 3px rgba(255, 120, 40, 0.1);
}

.login-btn {
  width: 100%;
  background: #FF7828;
  color: white;
  border: none;
  padding: 0.75rem 1.5rem;
  border-radius: 8px;
  font-size: 1rem;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.3s ease;
}

.login-btn:hover:not(:disabled) {
  background: #e6691f;
  transform: translateY(-2px);
  box-shadow: 0 4px 12px rgba(255, 120, 40, 0.3);
}

.login-btn:disabled {
  opacity: 0.6;
  cursor: not-allowed;
  transform: none;
}

.admin-footer {
  background: #1a202c;
  color: white;
  text-align: center;
  padding: 1.5rem 2rem;
  margin-top: auto;
}

.admin-footer p {
  color: #a0aec0;
  font-size: 0.9rem;
}
</style>