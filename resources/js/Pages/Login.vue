<template>
  <div class="auth-modal-content">
    <h3 class="modal-title">Login to Your Account</h3>
    <p class="modal-subtitle">Sign in to access your account</p>

    <!-- Account Type Selection -->
    <div class="account-type-selector">
      <button 
        v-for="type in accountTypes" 
        :key="type.id"
        :class="['type-btn', { 'active': activeForm === type.id }]"
        @click="switchForm(type.id)"
      >
        <div class="type-icon">{{ type.icon }}</div>
        <div class="type-info">
          <h4>{{ type.title }}</h4>
          <p>{{ type.description }}</p>
        </div>
      </button>
    </div>

    <!-- Dynamic Login Form -->
    <div class="form-container">
      <h4 class="form-title">{{ activeForm === 'customer' ? 'Customer Login' : activeForm === 'retailer' ? 'Retailer Login' : 'Delivery Partner Login' }}</h4>
      
      <!-- Alert Message -->
      <div v-if="alert.show" :class="['alert', alert.type === 'success' ? 'alert-success' : 'alert-error']">
        {{ alert.message }}
      </div>
      
      <form @submit.prevent="login" class="auth-form">
        <div class="form-group">
          <label>Email *</label>
          <input v-model="form.email" type="email" placeholder="your@email.com" required />
        </div>

        <div class="form-group">
          <label>Password *</label>
          <input v-model="form.password" type="password" placeholder="Enter your password" required />
        </div>

        <button type="submit" class="btn-primary w-full mt-2" :disabled="loading">
          {{ loading ? 'Logging in...' : `Login as ${activeForm === 'customer' ? 'Customer' : activeForm === 'retailer' ? 'Retailer' : 'Delivery Partner'}` }}
        </button>
      </form>
    </div>

    <div class="auth-footer">
      <p>Don't have an account? <a href="#" @click="switchToSignup">Sign up here</a></p>
    </div>
  </div>
</template>

<script setup>
import { ref, reactive } from "vue";
import { router } from "@inertiajs/vue3"; 
import { auth, db } from "../firebase";
import { signInWithEmailAndPassword } from "firebase/auth";
import { doc, getDoc, setDoc } from "firebase/firestore";

const emit = defineEmits(['switch-to-signup']);

// Account Types
const accountTypes = ref([
  {
    id: 'customer',
    title: 'Customer',
    description: 'Order gas products for delivery',
    icon: ''
  },
  {
    id: 'retailer',
    title: 'Retailer',
    description: 'Sell gas products from your store',
    icon: ''
  },
  {
    id: 'rider',
    title: 'Delivery Partner',
    description: 'Deliver gas products to customers',
    icon: ''
  }
]);

const activeForm = ref('customer');

const form = ref({ 
  email: "", 
  password: ""
});

const loading = ref(false);
const alert = reactive({
  show: false,
  message: "",
  type: ""
});

const switchForm = (formType) => {
  activeForm.value = formType;
  form.value.email = "";
  form.value.password = "";
  alert.show = false;
};

const switchToSignup = () => {
  emit('switch-to-signup', activeForm.value);
};

const showAlert = (message, type = 'error') => {
  alert.show = true;
  alert.message = message;
  alert.type = type;
  
  setTimeout(() => {
    alert.show = false;
  }, 5000);
};

const login = async () => {
  loading.value = true;
  alert.show = false;

  try {
    const userCredential = await signInWithEmailAndPassword(
      auth, 
      form.value.email, 
      form.value.password
    );
    
    // Determine which collection to check based on active form
    let collectionName = "";
    let redirectPath = "";
    let localStorageKey = "";
    
    switch (activeForm.value) {
      case 'customer':
        collectionName = "customers";
        redirectPath = "/customer/orders";
        localStorageKey = "customer";
        break;
      case 'retailer':
        collectionName = "retailers";
        redirectPath = "/retailer/orders";
        localStorageKey = "retailer";
        break;
      case 'rider':
        collectionName = "riders";
        redirectPath = "/delivery-partner/available";
        localStorageKey = "rider";
        break;
    }
    
    // Check if user exists in the appropriate collection
    const userDoc = await getDoc(doc(db, collectionName, userCredential.user.uid));
    
    if (!userDoc.exists()) {
      showAlert(`No ${activeForm.value} account found with this email. Please sign up as ${activeForm.value}.`);
      await auth.signOut();
      return;
    }

    const userData = userDoc.data();

    // Check approval status for retailers
    if (activeForm.value === 'retailer' && userData.approvalStatus !== 'approved') {
      showAlert("Your retailer account is pending approval. Please wait for admin approval.");
      await auth.signOut();
      return;
    }

    // Update last login
    await setDoc(doc(db, collectionName, userCredential.user.uid), {
      lastLogin: new Date().toISOString()
    }, { merge: true });

    localStorage.setItem(localStorageKey, JSON.stringify(userData));
    showAlert(`Logged in successfully as ${activeForm.value}!`, 'success');

    setTimeout(() => {
      router.visit(redirectPath);
    }, 1000);

  } catch (error) {
    console.error(error);
    
    let errorMessage = "Login failed. Please try again.";
    if (error.code === 'auth/invalid-email') {
      errorMessage = "Invalid email address.";
    } else if (error.code === 'auth/user-not-found') {
      errorMessage = `No ${activeForm.value} account found with this email.`;
    } else if (error.code === 'auth/wrong-password') {
      errorMessage = "Incorrect password.";
    }
    
    showAlert(errorMessage);
  } finally {
    loading.value = false;
  }
};
</script>

<!-- ADD THESE STYLES (nagkulang sa original) -->
<style scoped>
.auth-modal-content {
  padding: 0.5rem;
}

.modal-title {
  font-size: 1.5rem;
  font-weight: 700;
  color: #1a202c;
  text-align: center;
  margin-bottom: 0.5rem;
}

.modal-subtitle {
  color: #718096;
  text-align: center;
  margin-bottom: 1.5rem;
  font-size: 0.9rem;
}

/* Account Type Selector */
.account-type-selector {
  display: flex;
  flex-direction: column;
  gap: 0.5rem;
  margin-bottom: 1.5rem;
}

.type-btn {
  display: flex;
  align-items: center;
  gap: 0.75rem;
  padding: 0.75rem;
  border: 2px solid #e5e7eb;
  border-radius: 10px;
  background: white;
  cursor: pointer;
  transition: all 0.3s ease;
  text-align: left;
  width: 100%;
}

.type-btn:hover {
  border-color: #FF7828;
  transform: translateY(-1px);
}

.type-btn.active {
  border-color: #FF7828;
  background-color: #fff8f3;
}

.type-icon {
  font-size: 1.25rem;
  width: 40px;
  height: 40px;
  display: flex;
  align-items: center;
  justify-content: center;
  background: #f3f4f6;
  border-radius: 8px;
}

.type-btn.active .type-icon {
  background: #FF7828;
  color: white;
}

.type-info h4 {
  font-size: 0.9rem;
  font-weight: 600;
  color: #1f2937;
  margin-bottom: 0.1rem;
}

.type-info p {
  font-size: 0.75rem;
  color: #6b7280;
  margin: 0;
}

/* Form Container */
.form-container {
  background: white;
  border-radius: 10px;
  padding: 1rem;
  border: 1px solid #e5e7eb;
}

.form-title {
  font-size: 1.1rem;
  font-weight: 600;
  color: #1a202c;
  margin-bottom: 1rem;
  text-align: center;
}

/* Form Styles */
.auth-form {
  width: 100%;
}

.form-group {
  margin-bottom: 1rem;
}

.form-group label {
  display: block;
  margin-bottom: 0.25rem;
  font-weight: 500;
  color: #374151;
  font-size: 0.85rem;
}

.form-group input {
  width: 100%;
  padding: 0.65rem 0.85rem;
  border: 1px solid #d1d5db;
  border-radius: 8px;
  font-size: 0.9rem;
  transition: all 0.3s ease;
  box-sizing: border-box;
}

.form-group input:focus {
  outline: none;
  border-color: #FF7828;
  box-shadow: 0 0 0 3px rgba(255, 120, 40, 0.1);
}

/* Buttons */
.btn-primary {
  background: #FF7828;
  color: white;
  border: none;
  padding: 0.75rem 1.5rem;
  border-radius: 10px;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.3s ease;
  width: 100%;
  font-size: 0.9rem;
}

.btn-primary:hover:not(:disabled) {
  background: #e6691f;
  transform: translateY(-2px);
}

.btn-primary:disabled {
  opacity: 0.6;
  cursor: not-allowed;
}

/* Alert */
.alert {
  padding: 10px 12px;
  border-radius: 8px;
  margin-bottom: 1rem;
  font-weight: 500;
  font-size: 0.85rem;
}

.alert-success {
  background-color: #d1fae5;
  color: #065f46;
  border: 1px solid #a7f3d0;
}

.alert-error {
  background-color: #fee2e2;
  color: #991b1b;
  border: 1px solid #fecaca;
}

/* Footer */
.auth-footer {
  text-align: center;
  margin-top: 1rem;
  padding-top: 1rem;
  border-top: 1px solid #e5e7eb;
}

.auth-footer p {
  color: #6b7280;
  font-size: 0.85rem;
}

.auth-footer a {
  color: #FF7828;
  text-decoration: none;
  cursor: pointer;
  font-weight: 500;
}

.auth-footer a:hover {
  text-decoration: underline;
}

/* Utility Classes */
.w-full {
  width: 100%;
}

.mt-2 {
  margin-top: 0.5rem;
}

@media (max-width: 480px) {
  .auth-modal-content {
    padding: 0.25rem;
  }
  
  .modal-title {
    font-size: 1.25rem;
  }
  
  .modal-subtitle {
    font-size: 0.8rem;
  }
  
  .type-btn {
    padding: 0.6rem;
    gap: 0.5rem;
  }
  
  .type-icon {
    width: 36px;
    height: 36px;
    font-size: 1rem;
  }
  
  .form-container {
    padding: 0.75rem;
  }
  
  .form-group input {
    padding: 0.6rem 0.75rem;
  }
  
  .btn-primary {
    padding: 0.65rem 1rem;
  }
}
</style>