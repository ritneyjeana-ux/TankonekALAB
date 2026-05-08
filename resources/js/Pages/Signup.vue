<template>
  <div class="auth-modal-content">
    <h3 class="modal-title">Create Account</h3>
    <p class="modal-subtitle">Choose your account type to get started</p>

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

    <!-- Dynamic Form Container -->
    <div class="form-container">
      <!-- Customer Signup Form -->
      <div v-if="activeForm === 'customer'" class="form-wrapper">
        <h4 class="form-title">Customer Sign Up</h4>
        <div v-if="alert.show" :class="['alert', alert.type === 'success' ? 'alert-success' : 'alert-error']">
          {{ alert.message }}
        </div>

        <form @submit.prevent="registerCustomer" class="auth-form">
          <div class="form-group">
            <label>Full Name *</label>
            <input v-model="customerForm.fullName" type="text" placeholder="Juan Dela Cruz" required />
          </div>

          <div class="form-group">
            <label>Email *</label>
            <input v-model="customerForm.email" type="email" placeholder="your@email.com" required />
          </div>

          <div class="form-group">
            <label>Phone Number *</label>
            <input v-model="customerForm.phone" type="text" placeholder="09123456789" required />
          </div>

          <div class="form-group">
            <label>Delivery Address *</label>
            <div class="address-input-group">
              <input 
                v-model="customerForm.address" 
                type="text" 
                placeholder="Street, Barangay, Victoria, Oriental Mindoro" 
                @blur="geocodeCustomerAddress"
                @input="handleCustomerAddressInput"
                :disabled="loading.geocoding"
                required
              />
              <div v-if="loading.geocoding" class="loading-indicator">
                <div class="spinner"></div>
              </div>
            </div>
            <p class="address-helper-text">
              💡 Please include: <strong>Street, Barangay, Victoria, Oriental Mindoro</strong>
            </p>
          </div>

          <!-- Location Coordinates -->
          <div class="coordinates-section" v-if="customerForm.address">
            <div class="coordinates-display">
              <div class="coordinate-item">
                <label>Latitude</label>
                <input v-model="customerForm.latitude" type="text" readonly placeholder="Auto-filled from address" />
              </div>
              <div class="coordinate-item">
                <label>Longitude</label>
                <input v-model="customerForm.longitude" type="text" readonly placeholder="Auto-filled from address" />
              </div>
            </div>
            
            <div v-if="customerGeocodingStatus" class="geocoding-status" :class="customerGeocodingStatus.type">
              <svg width="16" height="16" viewBox="0 0 24 24" fill="none">
                <path v-if="customerGeocodingStatus.type === 'success'" d="M20 6L9 17l-5-5" stroke="currentColor" stroke-width="2"/>
                <path v-else d="M6 18L18 6M6 6l12 12" stroke="currentColor" stroke-width="2"/>
              </svg>
              {{ customerGeocodingStatus.message }}
            </div>

            <button v-if="!customerForm.latitude || !customerForm.longitude" class="btn-secondary" @click="getCustomerCurrentLocation" :disabled="loading.location">
              📍 {{ loading.location ? 'Getting GPS Location...' : 'Use My Current Location Instead' }}
            </button>
          </div>

          <div class="form-group">
            <label>Password *</label>
            <input v-model="customerForm.password" type="password" placeholder="At least 6 characters" required />
          </div>

          <div class="form-group">
            <label>Confirm Password *</label>
            <input v-model="customerForm.confirmPassword" type="password" placeholder="Re-enter your password" required />
          </div>

          <button type="submit" class="btn-primary w-full mt-2" :disabled="loading.register">
            {{ loading.register ? 'Creating Account...' : 'Create Customer Account' }}
          </button>
        </form>
      </div>

      <!-- Retailer Signup Form -->
      <div v-if="activeForm === 'retailer'" class="form-wrapper">
        <h4 class="form-title">Retailer Sign Up</h4>
        <div v-if="alert.show" :class="['alert', alert.type === 'success' ? 'alert-success' : 'alert-error']">
          {{ alert.message }}
        </div>

        <form @submit.prevent="registerRetailer" class="auth-form">
          <div class="form-group">
            <label>Full Name *</label>
            <input v-model="retailerForm.fullName" type="text" placeholder="Juan Dela Cruz" required />
          </div>

          <div class="form-group">
            <label>Business Name *</label>
            <input v-model="retailerForm.businessName" type="text" placeholder="Juan's Gas Station" required />
          </div>

          <div class="form-group">
            <label>Email *</label>
            <input v-model="retailerForm.email" type="email" placeholder="your@email.com" required />
          </div>

          <div class="form-group">
            <label>Phone Number *</label>
            <input v-model="retailerForm.phone" type="text" placeholder="09123456789" required />
          </div>

          <div class="form-group">
            <label>Shop Address *</label>
            <div class="address-input-group">
              <input 
                v-model="retailerForm.address" 
                type="text" 
                placeholder="Street, Barangay, Victoria, Oriental Mindoro" 
                @blur="geocodeRetailerAddress"
                @input="handleRetailerAddressInput"
                :disabled="loading.geocoding"
                required
              />
              <div v-if="loading.geocoding" class="loading-indicator">
                <div class="spinner"></div>
              </div>
            </div>
            <p class="address-helper-text">
              💡 Please include: <strong>Street, Barangay, Victoria, Oriental Mindoro</strong>
            </p>
          </div>

          <!-- Location Coordinates -->
          <div class="coordinates-section" v-if="retailerForm.address">
            <div class="coordinates-display">
              <div class="coordinate-item">
                <label>Latitude</label>
                <input v-model="retailerForm.latitude" type="text" readonly placeholder="Auto-filled from address" />
              </div>
              <div class="coordinate-item">
                <label>Longitude</label>
                <input v-model="retailerForm.longitude" type="text" readonly placeholder="Auto-filled from address" />
              </div>
            </div>
            
            <div v-if="retailerGeocodingStatus" class="geocoding-status" :class="retailerGeocodingStatus.type">
              <svg width="16" height="16" viewBox="0 0 24 24" fill="none">
                <path v-if="retailerGeocodingStatus.type === 'success'" d="M20 6L9 17l-5-5" stroke="currentColor" stroke-width="2"/>
                <path v-else d="M6 18L18 6M6 6l12 12" stroke="currentColor" stroke-width="2"/>
              </svg>
              {{ retailerGeocodingStatus.message }}
            </div>

            <button v-if="!retailerForm.latitude || !retailerForm.longitude" class="btn-secondary" @click="getRetailerCurrentLocation" :disabled="loading.location">
              📍 {{ loading.location ? 'Getting GPS Location...' : 'Use My Current Location Instead' }}
            </button>
          </div>

          <div class="form-group">
            <label>Password *</label>
            <input v-model="retailerForm.password" type="password" placeholder="At least 6 characters" required />
          </div>

          <div class="form-group">
            <label>Confirm Password *</label>
            <input v-model="retailerForm.confirmPassword" type="password" placeholder="Re-enter your password" required />
          </div>

          <div class="approval-notice">
            <p>📋 <strong>Note:</strong> Your retailer account will be pending admin approval. You will be notified once approved.</p>
          </div>

          <button type="submit" class="btn-primary w-full mt-2" :disabled="loading.register">
            {{ loading.register ? 'Creating Account...' : 'Create Retailer Account' }}
          </button>
        </form>
      </div>

      <!-- Delivery Partner Signup Form -->
      <div v-if="activeForm === 'rider'" class="form-wrapper">
        <h4 class="form-title">Delivery Partner Sign Up</h4>
        <div v-if="alert.show" :class="['alert', alert.type === 'success' ? 'alert-success' : 'alert-error']">
          {{ alert.message }}
        </div>

        <form @submit.prevent="registerRider" class="auth-form">
          <div class="form-group">
            <label>Full Name *</label>
            <input v-model="riderForm.fullName" type="text" placeholder="Juan Dela Cruz" required />
          </div>

          <div class="form-group">
            <label>Email *</label>
            <input v-model="riderForm.email" type="email" placeholder="your@email.com" required />
          </div>

          <div class="form-group">
            <label>Phone Number *</label>
            <input v-model="riderForm.phone" type="text" placeholder="09123456789" required />
          </div>

          <div class="form-group">
            <label>Vehicle Type *</label>
            <select v-model="riderForm.vehicleType" required>
              <option disabled value="">Select vehicle</option>
              <option>Motorcycle</option>
              <option>Tricycle</option>
              <option>Van</option>
            </select>
          </div>

          <div class="form-group">
            <label>Address *</label>
            <div class="address-input-group">
              <input 
                v-model="riderForm.address" 
                type="text" 
                placeholder="Street, Barangay, Victoria, Oriental Mindoro" 
                @blur="geocodeRiderAddress"
                @input="handleRiderAddressInput"
                :disabled="loading.geocoding"
                required
              />
              <div v-if="loading.geocoding" class="loading-indicator">
                <div class="spinner"></div>
              </div>
            </div>
            <p class="address-helper-text">
              💡 Please include: <strong>Street, Barangay, Victoria, Oriental Mindoro</strong>
            </p>
          </div>

          <!-- Location Coordinates -->
          <div class="coordinates-section" v-if="riderForm.address">
            <div class="coordinates-display">
              <div class="coordinate-item">
                <label>Latitude</label>
                <input v-model="riderForm.latitude" type="text" readonly placeholder="Auto-filled from address" />
              </div>
              <div class="coordinate-item">
                <label>Longitude</label>
                <input v-model="riderForm.longitude" type="text" readonly placeholder="Auto-filled from address" />
              </div>
            </div>
            
            <div v-if="riderGeocodingStatus" class="geocoding-status" :class="riderGeocodingStatus.type">
              <svg width="16" height="16" viewBox="0 0 24 24" fill="none">
                <path v-if="riderGeocodingStatus.type === 'success'" d="M20 6L9 17l-5-5" stroke="currentColor" stroke-width="2"/>
                <path v-else d="M6 18L18 6M6 6l12 12" stroke="currentColor" stroke-width="2"/>
              </svg>
              {{ riderGeocodingStatus.message }}
            </div>

            <button v-if="!riderForm.latitude || !riderForm.longitude" class="btn-secondary" @click="getRiderCurrentLocation" :disabled="loading.location">
              📍 {{ loading.location ? 'Getting GPS Location...' : 'Use My Current Location Instead' }}
            </button>
          </div>

          <div class="form-group">
            <label>Password *</label>
            <input v-model="riderForm.password" type="password" placeholder="At least 6 characters" required />
          </div>

          <div class="form-group">
            <label>Confirm Password *</label>
            <input v-model="riderForm.confirmPassword" type="password" placeholder="Re-enter your password" required />
          </div>

          <button type="submit" class="btn-primary w-full mt-2" :disabled="loading.register">
            {{ loading.register ? 'Creating Account...' : 'Create Delivery Partner Account' }}
          </button>
        </form>
      </div>
    </div>

    <!-- Switch to Login -->
    <div class="auth-footer">
      <p>Already have an account? 
        <a href="#" @click="switchToLogin">
          {{ activeForm === 'customer' ? 'Login as Customer' : activeForm === 'retailer' ? 'Login as Retailer' : 'Login as Delivery Partner' }}
        </a>
      </p>
    </div>
  </div>
</template>

<script setup>
import { ref, reactive } from "vue";
import { router } from "@inertiajs/vue3"; 
import { auth, db } from "@/firebase";
import { createUserWithEmailAndPassword } from "firebase/auth";
import { doc, setDoc } from "firebase/firestore";

const emit = defineEmits(['switch-to-login']);

// Account Types
const accountTypes = ref([
  {
    id: 'customer',
    title: 'Customer',
    description: 'Order gas products for delivery',
    icon: '👤'
  },
  {
    id: 'retailer',
    title: 'Retailer',
    description: 'Sell gas products from your store',
    icon: '🏪'
  },
  {
    id: 'rider',
    title: 'Delivery Partner',
    description: 'Deliver gas products to customers',
    icon: '🏍️'
  }
]);

// Active form state
const activeForm = ref('customer');

// Forms data
const customerForm = reactive({
  fullName: "",
  email: "",
  phone: "",
  address: "",
  password: "",
  confirmPassword: "",
  latitude: "",
  longitude: "",
});

const retailerForm = reactive({
  fullName: "",
  businessName: "",
  email: "",
  phone: "",
  address: "",
  password: "",
  confirmPassword: "",
  latitude: "",
  longitude: "",
});

const riderForm = reactive({
  fullName: "",
  email: "",
  phone: "",
  vehicleType: "",
  address: "",
  password: "",
  confirmPassword: "",
  latitude: "",
  longitude: "",
});

// Loading states
const loading = reactive({
  register: false,
  location: false,
  geocoding: false
});

// Alert
const alert = reactive({
  show: false,
  message: "",
  type: ""
});

// Geocoding status
const customerGeocodingStatus = ref(null);
const retailerGeocodingStatus = ref(null);
const riderGeocodingStatus = ref(null);

// Timeouts for debouncing
let customerAddressTimeout = null;
let retailerAddressTimeout = null;
let riderAddressTimeout = null;

// Switch between forms
const switchForm = (formType) => {
  activeForm.value = formType;
  alert.show = false;
};

// Switch to login
const switchToLogin = () => {
  emit('switch-to-login', activeForm.value);
};

// Show alert
const showAlert = (message, type = 'error') => {
  alert.show = true;
  alert.message = message;
  alert.type = type;
  
  setTimeout(() => {
    alert.show = false;
  }, 5000);
};

// ================== CUSTOMER FUNCTIONS ==================
const handleCustomerAddressInput = () => {
  if (customerAddressTimeout) clearTimeout(customerAddressTimeout);
  customerAddressTimeout = setTimeout(() => {
    if (customerForm.address && customerForm.address.length > 5) {
      geocodeCustomerAddress();
    }
  }, 1500);
};

const geocodeCustomerAddress = async () => {
  if (!customerForm.address || customerForm.address.trim().length < 5) {
    customerGeocodingStatus.value = {
      type: 'error',
      message: '❌ Please enter a complete address'
    };
    return;
  }

  if (customerForm.latitude && customerForm.longitude) return;

  loading.geocoding = true;
  customerGeocodingStatus.value = {
    type: 'success',
    message: '🔄 Detecting location...'
  };

  try {
    const fullAddress = `${customerForm.address}, Oriental Mindoro, Philippines`;
    const response = await fetch(
      `https://nominatim.openstreetmap.org/search?format=json&q=${encodeURIComponent(fullAddress)}&limit=1&addressdetails=1`
    );
    
    if (!response.ok) throw new Error('Geocoding service unavailable');
    
    const data = await response.json();
    
    if (data && data.length > 0) {
      customerForm.latitude = parseFloat(data[0].lat).toFixed(6);
      customerForm.longitude = parseFloat(data[0].lon).toFixed(6);
      customerGeocodingStatus.value = {
        type: 'success',
        message: '✅ Location coordinates automatically detected!'
      };
    } else {
      throw new Error('Address not found');
    }
  } catch (error) {
    console.error('Geocoding error:', error);
    customerGeocodingStatus.value = {
      type: 'error',
      message: '❌ Could not detect coordinates'
    };
    customerForm.latitude = "";
    customerForm.longitude = "";
  } finally {
    loading.geocoding = false;
  }
};

const getCustomerCurrentLocation = () => {
  if (!navigator.geolocation) {
    showAlert("Geolocation is not supported by your browser.");
    return;
  }

  loading.location = true;
  customerGeocodingStatus.value = {
    type: 'success',
    message: '🔄 Getting your current location...'
  };

  navigator.geolocation.getCurrentPosition(
    async (position) => {
      try {
        const { latitude, longitude } = position.coords;
        customerForm.latitude = latitude.toFixed(6);
        customerForm.longitude = longitude.toFixed(6);
        
        const response = await fetch(
          `https://nominatim.openstreetmap.org/reverse?format=json&lat=${latitude}&lon=${longitude}&zoom=18&addressdetails=1`
        );
        
        if (response.ok) {
          const data = await response.json();
          if (data.display_name) {
            customerForm.address = data.display_name;
          }
        }
        
        customerGeocodingStatus.value = {
          type: 'success',
          message: '✅ Current location detected successfully!'
        };
        showAlert('Current location detected successfully!', 'success');
      } catch (error) {
        console.error('Reverse geocoding error:', error);
        customerGeocodingStatus.value = {
          type: 'success',
          message: '✅ Location detected (address may need manual adjustment)'
        };
        showAlert('Location detected! Please verify your address.', 'success');
      } finally {
        loading.location = false;
      }
    },
    (error) => {
      console.error('Geolocation error:', error);
      let errorMessage = "Unable to get your current location.";
      
      switch (error.code) {
        case error.PERMISSION_DENIED:
          errorMessage = "Location access denied.";
          break;
        case error.POSITION_UNAVAILABLE:
          errorMessage = "Location information unavailable.";
          break;
        case error.TIMEOUT:
          errorMessage = "Location request timed out.";
          break;
      }
      
      showAlert(errorMessage);
      customerGeocodingStatus.value = {
        type: 'error',
        message: '❌ ' + errorMessage
      };
      loading.location = false;
    },
    {
      enableHighAccuracy: true,
      timeout: 10000,
      maximumAge: 60000
    }
  );
};

const validateCustomerForm = () => {
  if (!customerForm.fullName) {
    showAlert("Please enter your full name.");
    return false;
  }
  if (!customerForm.email) {
    showAlert("Please enter your email address.");
    return false;
  }
  if (!customerForm.phone) {
    showAlert("Please enter your phone number.");
    return false;
  }
  if (!customerForm.address) {
    showAlert("Please enter your delivery address.");
    return false;
  }
  if (!customerForm.password) {
    showAlert("Please enter a password.");
    return false;
  }
  if (customerForm.password.length < 6) {
    showAlert("Password must be at least 6 characters long.");
    return false;
  }
  if (customerForm.password !== customerForm.confirmPassword) {
    showAlert("Passwords do not match.");
    return false;
  }
  return true;
};

const registerCustomer = async () => {
  if (!validateCustomerForm()) return;

  if (!customerForm.latitude || !customerForm.longitude) {
    await geocodeCustomerAddress();
  }

  loading.register = true;
  alert.show = false;

  try {
    const userCredential = await createUserWithEmailAndPassword(
      auth,
      customerForm.email,
      customerForm.password
    );

    const userData = {
      role: "customer",
      fullName: customerForm.fullName,
      email: customerForm.email,
      phone: customerForm.phone,
      address: customerForm.address,
      latitude: customerForm.latitude || "",
      longitude: customerForm.longitude || "",
      locationSource: customerForm.latitude ? "geocoded" : "manual",
      locationUpdatedAt: new Date().toISOString(),
      createdAt: new Date().toISOString(),
      status: "active"
    };

    await setDoc(doc(db, "customers", userCredential.user.uid), userData);
    
    showAlert(`✅ Registered successfully as customer!`, 'success');

    setTimeout(() => {
      router.visit("/customer/orders");
    }, 1500);

  } catch (error) {
    console.error(error);
    
    let errorMessage = "Registration failed. Please try again.";
    if (error.code === 'auth/email-already-in-use') {
      errorMessage = "Email address is already in use.";
    } else if (error.code === 'auth/invalid-email') {
      errorMessage = "Invalid email address.";
    } else if (error.code === 'auth/weak-password') {
      errorMessage = "Password is too weak.";
    }
    
    showAlert(errorMessage);
  } finally {
    loading.register = false;
  }
};

// ================== RETAILER FUNCTIONS ==================
const handleRetailerAddressInput = () => {
  if (retailerAddressTimeout) clearTimeout(retailerAddressTimeout);
  retailerAddressTimeout = setTimeout(() => {
    if (retailerForm.address && retailerForm.address.length > 5) {
      geocodeRetailerAddress();
    }
  }, 1500);
};

const geocodeRetailerAddress = async () => {
  if (!retailerForm.address || retailerForm.address.trim().length < 5) {
    retailerGeocodingStatus.value = {
      type: 'error',
      message: '❌ Please enter a complete address'
    };
    return;
  }

  if (retailerForm.latitude && retailerForm.longitude) return;

  loading.geocoding = true;
  retailerGeocodingStatus.value = {
    type: 'success',
    message: '🔄 Detecting location...'
  };

  try {
    const fullAddress = `${retailerForm.address}, Oriental Mindoro, Philippines`;
    const response = await fetch(
      `https://nominatim.openstreetmap.org/search?format=json&q=${encodeURIComponent(fullAddress)}&limit=1&addressdetails=1`
    );
    
    if (!response.ok) throw new Error('Geocoding service unavailable');
    
    const data = await response.json();
    
    if (data && data.length > 0) {
      retailerForm.latitude = parseFloat(data[0].lat).toFixed(6);
      retailerForm.longitude = parseFloat(data[0].lon).toFixed(6);
      retailerGeocodingStatus.value = {
        type: 'success',
        message: '✅ Location coordinates automatically detected!'
      };
    } else {
      throw new Error('Address not found');
    }
  } catch (error) {
    console.error('Geocoding error:', error);
    retailerGeocodingStatus.value = {
      type: 'error',
      message: '❌ Could not detect coordinates'
    };
    retailerForm.latitude = "";
    retailerForm.longitude = "";
  } finally {
    loading.geocoding = false;
  }
};

const getRetailerCurrentLocation = () => {
  if (!navigator.geolocation) {
    showAlert("Geolocation is not supported by your browser.");
    return;
  }

  loading.location = true;
  retailerGeocodingStatus.value = {
    type: 'success',
    message: '🔄 Getting your current location...'
  };

  navigator.geolocation.getCurrentPosition(
    async (position) => {
      try {
        const { latitude, longitude } = position.coords;
        retailerForm.latitude = latitude.toFixed(6);
        retailerForm.longitude = longitude.toFixed(6);
        
        const response = await fetch(
          `https://nominatim.openstreetmap.org/reverse?format=json&lat=${latitude}&lon=${longitude}&zoom=18&addressdetails=1`
        );
        
        if (response.ok) {
          const data = await response.json();
          if (data.display_name) {
            retailerForm.address = data.display_name;
          }
        }
        
        retailerGeocodingStatus.value = {
          type: 'success',
          message: '✅ Current location detected successfully!'
        };
        showAlert('Current location detected successfully!', 'success');
      } catch (error) {
        console.error('Reverse geocoding error:', error);
        retailerGeocodingStatus.value = {
          type: 'success',
          message: '✅ Location detected (address may need manual adjustment)'
        };
        showAlert('Location detected! Please verify your address.', 'success');
      } finally {
        loading.location = false;
      }
    },
    (error) => {
      console.error('Geolocation error:', error);
      let errorMessage = "Unable to get your current location.";
      
      switch (error.code) {
        case error.PERMISSION_DENIED:
          errorMessage = "Location access denied.";
          break;
        case error.POSITION_UNAVAILABLE:
          errorMessage = "Location information unavailable.";
          break;
        case error.TIMEOUT:
          errorMessage = "Location request timed out.";
          break;
      }
      
      showAlert(errorMessage);
      retailerGeocodingStatus.value = {
        type: 'error',
        message: '❌ ' + errorMessage
      };
      loading.location = false;
    },
    {
      enableHighAccuracy: true,
      timeout: 10000,
      maximumAge: 60000
    }
  );
};

const validateRetailerForm = () => {
  if (!retailerForm.fullName) {
    showAlert("Please enter your full name.");
    return false;
  }
  if (!retailerForm.businessName) {
    showAlert("Please enter your business name.");
    return false;
  }
  if (!retailerForm.email) {
    showAlert("Please enter your email address.");
    return false;
  }
  if (!retailerForm.phone) {
    showAlert("Please enter your phone number.");
    return false;
  }
  if (!retailerForm.address) {
    showAlert("Please enter your shop address.");
    return false;
  }
  if (!retailerForm.password) {
    showAlert("Please enter a password.");
    return false;
  }
  if (retailerForm.password.length < 6) {
    showAlert("Password must be at least 6 characters long.");
    return false;
  }
  if (retailerForm.password !== retailerForm.confirmPassword) {
    showAlert("Passwords do not match.");
    return false;
  }
  return true;
};

const registerRetailer = async () => {
  if (!validateRetailerForm()) return;

  if (!retailerForm.latitude || !retailerForm.longitude) {
    await geocodeRetailerAddress();
  }

  loading.register = true;
  alert.show = false;

  try {
    const userCredential = await createUserWithEmailAndPassword(
      auth,
      retailerForm.email,
      retailerForm.password
    );

    const retailerData = {
      role: "retailer",
      fullName: retailerForm.fullName,
      businessName: retailerForm.businessName,
      email: retailerForm.email,
      phone: retailerForm.phone,
      address: retailerForm.address,
      latitude: retailerForm.latitude || "",
      longitude: retailerForm.longitude || "",
      locationSource: retailerForm.latitude ? "geocoded" : "manual",
      locationUpdatedAt: new Date().toISOString(),
      createdAt: new Date().toISOString(),
      approvalStatus: "pending",
      status: "active",
      rating: 0,
      ratings: [],
      totalRatings: 0
    };

    await setDoc(doc(db, "retailers", userCredential.user.uid), retailerData);
    
    showAlert(`✅ Registered successfully as retailer! Your account is pending admin approval.`, 'success');

    setTimeout(() => {
      router.visit("/");
    }, 3000);

  } catch (error) {
    console.error(error);
    
    let errorMessage = "Registration failed. Please try again.";
    if (error.code === 'auth/email-already-in-use') {
      errorMessage = "Email address is already in use.";
    } else if (error.code === 'auth/invalid-email') {
      errorMessage = "Invalid email address.";
    } else if (error.code === 'auth/weak-password') {
      errorMessage = "Password is too weak.";
    }
    
    showAlert(errorMessage);
  } finally {
    loading.register = false;
  }
};

// ================== RIDER FUNCTIONS ==================
const handleRiderAddressInput = () => {
  if (riderAddressTimeout) clearTimeout(riderAddressTimeout);
  riderAddressTimeout = setTimeout(() => {
    if (riderForm.address && riderForm.address.length > 5) {
      geocodeRiderAddress();
    }
  }, 1500);
};

const geocodeRiderAddress = async () => {
  if (!riderForm.address || riderForm.address.trim().length < 5) {
    riderGeocodingStatus.value = {
      type: 'error',
      message: '❌ Please enter a complete address'
    };
    return;
  }

  if (riderForm.latitude && riderForm.longitude) return;

  loading.geocoding = true;
  riderGeocodingStatus.value = {
    type: 'success',
    message: '🔄 Detecting location...'
  };

  try {
    const fullAddress = `${riderForm.address}, Oriental Mindoro, Philippines`;
    const response = await fetch(
      `https://nominatim.openstreetmap.org/search?format=json&q=${encodeURIComponent(fullAddress)}&limit=1&addressdetails=1`
    );
    
    if (!response.ok) throw new Error('Geocoding service unavailable');
    
    const data = await response.json();
    
    if (data && data.length > 0) {
      riderForm.latitude = parseFloat(data[0].lat).toFixed(6);
      riderForm.longitude = parseFloat(data[0].lon).toFixed(6);
      riderGeocodingStatus.value = {
        type: 'success',
        message: '✅ Location coordinates automatically detected!'
      };
    } else {
      throw new Error('Address not found');
    }
  } catch (error) {
    console.error('Geocoding error:', error);
    riderGeocodingStatus.value = {
      type: 'error',
      message: '❌ Could not detect coordinates'
    };
    riderForm.latitude = "";
    riderForm.longitude = "";
  } finally {
    loading.geocoding = false;
  }
};

const getRiderCurrentLocation = () => {
  if (!navigator.geolocation) {
    showAlert("Geolocation is not supported by your browser.");
    return;
  }

  loading.location = true;
  riderGeocodingStatus.value = {
    type: 'success',
    message: '🔄 Getting your current location...'
  };

  navigator.geolocation.getCurrentPosition(
    async (position) => {
      try {
        const { latitude, longitude } = position.coords;
        riderForm.latitude = latitude.toFixed(6);
        riderForm.longitude = longitude.toFixed(6);
        
        const response = await fetch(
          `https://nominatim.openstreetmap.org/reverse?format=json&lat=${latitude}&lon=${longitude}&zoom=18&addressdetails=1`
        );
        
        if (response.ok) {
          const data = await response.json();
          if (data.display_name) {
            riderForm.address = data.display_name;
          }
        }
        
        riderGeocodingStatus.value = {
          type: 'success',
          message: '✅ Current location detected successfully!'
        };
        showAlert('Current location detected successfully!', 'success');
      } catch (error) {
        console.error('Reverse geocoding error:', error);
        riderGeocodingStatus.value = {
          type: 'success',
          message: '✅ Location detected (address may need manual adjustment)'
        };
        showAlert('Location detected! Please verify your address.', 'success');
      } finally {
        loading.location = false;
      }
    },
    (error) => {
      console.error('Geolocation error:', error);
      let errorMessage = "Unable to get your current location.";
      
      switch (error.code) {
        case error.PERMISSION_DENIED:
          errorMessage = "Location access denied.";
          break;
        case error.POSITION_UNAVAILABLE:
          errorMessage = "Location information unavailable.";
          break;
        case error.TIMEOUT:
          errorMessage = "Location request timed out.";
          break;
      }
      
      showAlert(errorMessage);
      riderGeocodingStatus.value = {
        type: 'error',
        message: '❌ ' + errorMessage
      };
      loading.location = false;
    },
    {
      enableHighAccuracy: true,
      timeout: 10000,
      maximumAge: 60000
    }
  );
};

const validateRiderForm = () => {
  if (!riderForm.fullName) {
    showAlert("Please enter your full name.");
    return false;
  }
  if (!riderForm.email) {
    showAlert("Please enter your email address.");
    return false;
  }
  if (!riderForm.phone) {
    showAlert("Please enter your phone number.");
    return false;
  }
  if (!riderForm.vehicleType) {
    showAlert("Please select your vehicle type.");
    return false;
  }
  if (!riderForm.address) {
    showAlert("Please enter your address.");
    return false;
  }
  if (!riderForm.password) {
    showAlert("Please enter a password.");
    return false;
  }
  if (riderForm.password.length < 6) {
    showAlert("Password must be at least 6 characters long.");
    return false;
  }
  if (riderForm.password !== riderForm.confirmPassword) {
    showAlert("Passwords do not match.");
    return false;
  }
  return true;
};

const registerRider = async () => {
  if (!validateRiderForm()) return;

  if (!riderForm.latitude || !riderForm.longitude) {
    await geocodeRiderAddress();
  }

  loading.register = true;
  alert.show = false;

  try {
    const userCredential = await createUserWithEmailAndPassword(
      auth,
      riderForm.email,
      riderForm.password
    );

    const riderData = {
      role: "rider",
      fullName: riderForm.fullName,
      email: riderForm.email,
      phone: riderForm.phone,
      vehicleType: riderForm.vehicleType,
      address: riderForm.address,
      latitude: riderForm.latitude || "",
      longitude: riderForm.longitude || "",
      locationSource: riderForm.latitude ? "geocoded" : "manual",
      locationUpdatedAt: new Date().toISOString(),
      createdAt: new Date().toISOString(),
      status: "active",
      availability: "offline",
      currentOrder: null,
      totalDeliveries: 0,
      rating: 0
    };

    await setDoc(doc(db, "riders", userCredential.user.uid), riderData);
    
    showAlert(`✅ Registered successfully as delivery partner!`, 'success');

    setTimeout(() => {
      router.visit("/delivery-partner/available");
    }, 1500);

  } catch (error) {
    console.error(error);
    
    let errorMessage = "Registration failed. Please try again.";
    if (error.code === 'auth/email-already-in-use') {
      errorMessage = "Email address is already in use.";
    } else if (error.code === 'auth/invalid-email') {
      errorMessage = "Invalid email address.";
    } else if (error.code === 'auth/weak-password') {
      errorMessage = "Password is too weak.";
    }
    
    showAlert(errorMessage);
  } finally {
    loading.register = false;
  }
};
</script>

<style scoped>
.auth-modal-content {
  padding: 1rem;
  max-width: 100%;
  box-sizing: border-box;
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
  margin-bottom: 2rem;
  font-size: 0.9rem;
}

/* Account Type Selector */
.account-type-selector {
  display: flex;
  flex-direction: column;
  gap: 0.75rem;
  margin-bottom: 2rem;
}

.type-btn {
  display: flex;
  align-items: center;
  gap: 1rem;
  padding: 1rem;
  border: 2px solid #e5e7eb;
  border-radius: 12px;
  background: white;
  cursor: pointer;
  transition: all 0.3s ease;
  text-align: left;
  width: 100%;
}

.type-btn:hover {
  border-color: #FF7828;
  transform: translateY(-2px);
}

.type-btn.active {
  border-color: #FF7828;
  background-color: #fff8f3;
}

.type-icon {
  font-size: 1.5rem;
  width: 48px;
  height: 48px;
  display: flex;
  align-items: center;
  justify-content: center;
  background: #f3f4f6;
  border-radius: 10px;
}

.type-btn.active .type-icon {
  background: #FF7828;
  color: white;
}

.type-info h4 {
  font-size: 1rem;
  font-weight: 600;
  color: #1f2937;
  margin-bottom: 0.25rem;
}

.type-info p {
  font-size: 0.85rem;
  color: #6b7280;
  margin: 0;
}

/* Form Container */
.form-container {
  background: white;
  border-radius: 12px;
  padding: 1.5rem;
  border: 1px solid #e5e7eb;
}

.form-title {
  font-size: 1.25rem;
  font-weight: 600;
  color: #1a202c;
  margin-bottom: 1.5rem;
  text-align: center;
}

/* Form Styles */
.auth-form {
  width: 100%;
}

.form-group {
  margin-bottom: 1.5rem;
}

.form-group label {
  display: block;
  margin-bottom: 0.5rem;
  font-weight: 500;
  color: #374151;
  font-size: 0.9rem;
}

.form-group input,
.form-group select {
  width: 100%;
  padding: 0.75rem 1rem;
  border: 1px solid #d1d5db;
  border-radius: 8px;
  font-size: 1rem;
  transition: all 0.3s ease;
  box-sizing: border-box;
}

.form-group input:focus,
.form-group select:focus {
  outline: none;
  border-color: #FF7828;
  box-shadow: 0 0 0 3px rgba(255, 120, 40, 0.1);
}

/* Address Input Styles */
.address-input-group {
  position: relative;
}

.loading-indicator {
  position: absolute;
  right: 12px;
  top: 50%;
  transform: translateY(-50%);
}

.spinner {
  width: 16px;
  height: 16px;
  border: 2px solid #f3f3f3;
  border-top: 2px solid #FF7828;
  border-radius: 50%;
  animation: spin 1s linear infinite;
}

@keyframes spin {
  0% { transform: rotate(0deg); }
  100% { transform: rotate(360deg); }
}

.address-helper-text {
  font-size: 0.8rem;
  color: #6b7280;
  margin-top: 0.5rem;
  line-height: 1.4;
}

/* Coordinates Section */
.coordinates-section {
  margin-bottom: 1.5rem;
  padding: 1rem;
  background-color: #f8fafc;
  border-radius: 8px;
  border: 1px solid #e2e8f0;
}

.coordinates-display {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 0.75rem;
  margin-bottom: 1rem;
}

.coordinate-item label {
  display: block;
  margin-bottom: 0.25rem;
  font-weight: 500;
  color: #374151;
  font-size: 0.8rem;
}

.coordinate-item input {
  width: 100%;
  padding: 0.5rem 0.75rem;
  border: 1px solid #d1d5db;
  border-radius: 6px;
  font-size: 0.85rem;
  background-color: #f9fafb;
  color: #6b7280;
  box-sizing: border-box;
}

/* Geocoding Status */
.geocoding-status {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  padding: 0.5rem 0.75rem;
  border-radius: 6px;
  font-size: 0.85rem;
  margin-bottom: 1rem;
}

.geocoding-status.success {
  background-color: #d1fae5;
  color: #065f46;
  border: 1px solid #a7f3d0;
}

.geocoding-status.error {
  background-color: #fee2e2;
  color: #991b1b;
  border: 1px solid #fecaca;
}

.geocoding-status.info {
  background-color: #dbeafe;
  color: #1e40af;
  border: 1px solid #93c5fd;
}

/* Approval Notice */
.approval-notice {
  background: #fff3cd;
  border: 1px solid #ffeaa7;
  border-radius: 8px;
  padding: 12px;
  margin: 1rem 0;
  font-size: 0.875rem;
  color: #856404;
}

.approval-notice p {
  margin: 0;
}

/* Buttons */
.btn-secondary {
  background: #6b7280;
  color: white;
  border: none;
  padding: 0.75rem 1rem;
  border-radius: 8px;
  font-weight: 500;
  cursor: pointer;
  transition: all 0.3s ease;
  width: 100%;
  font-size: 0.9rem;
}

.btn-secondary:hover:not(:disabled) {
  background: #4b5563;
}

.btn-secondary:disabled {
  opacity: 0.6;
  cursor: not-allowed;
}

.btn-primary {
  background: #FF7828;
  color: white;
  border: none;
  padding: 0.75rem 2rem;
  border-radius: 10px;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.3s ease;
  width: 100%;
  font-size: 1rem;
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
  padding: 12px 16px;
  border-radius: 8px;
  margin-bottom: 16px;
  font-weight: 500;
  font-size: 0.9rem;
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
}

.auth-footer p {
  color: #6b7280;
  font-size: 0.9rem;
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

/* Mobile Responsive Styles */
@media (max-width: 768px) {
  .auth-modal-content {
    padding: 0.75rem;
  }
  
  .modal-title {
    font-size: 1.25rem;
  }
  
  .modal-subtitle {
    font-size: 0.85rem;
    margin-bottom: 1.5rem;
  }
  
  .account-type-selector {
    gap: 0.5rem;
    margin-bottom: 1.5rem;
  }
  
  .type-btn {
    padding: 0.75rem;
    gap: 0.75rem;
  }
  
  .type-icon {
    width: 40px;
    height: 40px;
    font-size: 1.25rem;
  }
  
  .type-info h4 {
    font-size: 0.9rem;
  }
  
  .type-info p {
    font-size: 0.8rem;
  }
  
  .form-container {
    padding: 1rem;
  }
  
  .form-title {
    font-size: 1.1rem;
    margin-bottom: 1rem;
  }
  
  .form-group {
    margin-bottom: 1.25rem;
  }
  
  .form-group label {
    font-size: 0.85rem;
  }
  
  .form-group input,
  .form-group select {
    padding: 0.65rem 0.85rem;
    font-size: 0.9rem;
  }
  
  .coordinates-section {
    padding: 0.75rem;
    margin-bottom: 1.25rem;
  }
  
  .coordinates-display {
    grid-template-columns: 1fr;
    gap: 0.5rem;
  }
  
  .coordinate-item label {
    font-size: 0.75rem;
  }
  
  .coordinate-item input {
    padding: 0.4rem 0.6rem;
    font-size: 0.8rem;
  }
  
  .geocoding-status {
    font-size: 0.8rem;
    padding: 0.4rem 0.6rem;
  }
  
  .btn-secondary {
    padding: 0.65rem 0.85rem;
    font-size: 0.85rem;
  }
  
  .btn-primary {
    padding: 0.65rem 1.5rem;
    font-size: 0.9rem;
  }
  
  .auth-footer p {
    font-size: 0.85rem;
  }
  
  .alert {
    padding: 10px 14px;
    font-size: 0.85rem;
  }
  
  .address-helper-text {
    font-size: 0.75rem;
  }
  
  .approval-notice {
    padding: 10px;
    font-size: 0.8rem;
  }
}

@media (max-width: 480px) {
  .auth-modal-content {
    padding: 0.5rem;
  }
  
  .modal-title {
    font-size: 1.1rem;
  }
  
  .modal-subtitle {
    font-size: 0.8rem;
    margin-bottom: 1.25rem;
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
  
  .form-title {
    font-size: 1rem;
  }
  
  .form-group {
    margin-bottom: 1rem;
  }
  
  .form-group input,
  .form-group select {
    padding: 0.6rem 0.75rem;
    font-size: 0.85rem;
  }
  
  .btn-primary {
    padding: 0.6rem 1.25rem;
    font-size: 0.85rem;
  }
}
</style>