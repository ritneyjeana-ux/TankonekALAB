<template>
  <div class="modal-overlay" @click="handleClose">
    <div class="profile-container" @click.stop>
      <div class="profile-header">
        <div class="header-content">
          <svg width="24" height="24" viewBox="0 0 24 24" fill="none" class="header-icon">
            <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2" stroke="currentColor" stroke-width="2"/>
            <circle cx="12" cy="7" r="4" stroke="currentColor" stroke-width="2"/>
          </svg>
          <h2>Customer Profile Settings</h2>
        </div>
         <button class="close-btn" @click="handleClose" @mousedown.stop>
          <svg width="20" height="20" viewBox="0 0 24 24" fill="none">
            <path d="M18 6L6 18" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
            <path d="M6 6l12 12" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
          </svg>
        </button>
      </div>

      <div class="profile-content">
        <!-- Loading State -->
        <div v-if="loading" class="loading-state">
          <p>Loading your profile...</p>
        </div>

        <div v-else class="content-sections">
          <div class="form-section">
            <div class="section-header">
              <h3>Personal Information</h3>
              <p>Manage your personal details and preferences</p>
            </div>
            
            <div class="form-grid">
              <div class="form-group">
                <label class="form-label">
                  <svg width="16" height="16" viewBox="0 0 24 24" fill="none" class="label-icon">
                    <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2" stroke="currentColor" stroke-width="2"/>
                    <circle cx="12" cy="7" r="4" stroke="currentColor" stroke-width="2"/>
                  </svg>
                  Full Name
                </label>
                <input 
                  v-model="profile.name" 
                  type="text" 
                  class="form-input"
                  placeholder="Enter your full name"
                />
              </div>

              <div class="form-group">
                <label class="form-label">
                  <svg width="16" height="16" viewBox="0 0 24 24" fill="none" class="label-icon">
                    <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z" stroke="currentColor" stroke-width="2"/>
                    <path d="M22 6l-10 7L2 6" stroke="currentColor" stroke-width="2"/>
                  </svg>
                  Email Address
                </label>
                <input 
                  v-model="profile.email" 
                  type="email" 
                  class="form-input"
                  placeholder="your@email.com"
                />
              </div>

              <div class="form-group">
                <label class="form-label">
                  <svg width="16" height="16" viewBox="0 0 24 24" fill="none" class="label-icon">
                    <path d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" stroke="currentColor" stroke-width="2"/>
                  </svg>
                  New Password
                </label>
                <input 
                  v-model="newPassword" 
                  type="password" 
                  class="form-input"
                  placeholder="Enter new password"
                />
                <small class="form-help">Leave blank to keep current password</small>
              </div>

              <div class="form-group">
                <label class="form-label">
                  <svg width="16" height="16" viewBox="0 0 24 24" fill="none" class="label-icon">
                    <path d="M22 16.92v3a2 2 0 01-2.18 2 19.79 19.79 0 01-8.63-3.07 19.5 19.5 0 01-6-6 19.79 19.79 0 01-3.07-8.67A2 2 0 014.11 2h3a2 2 0 012 1.72 12.84 12.84 0 00.7 2.81 2 2 0 01-.45 2.11L8.09 9.91a16 16 0 006 6l1.27-1.27a2 2 0 012.11-.45 12.84 12.84 0 002.81.7A2 2 0 0122 16.92z" stroke="currentColor" stroke-width="2"/>
                  </svg>
                  Contact Number
                </label>
                <input 
                  v-model="profile.phone" 
                  type="text" 
                  class="form-input"
                  placeholder="0912 345 6789"
                />
              </div>

              <div class="form-group full-width">
                <label class="form-label">
                  <svg width="16" height="16" viewBox="0 0 24 24" fill="none" class="label-icon">
                    <path d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" stroke="currentColor" stroke-width="2"/>
                    <path d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" stroke="currentColor" stroke-width="2"/>
                  </svg>
                  Delivery Address
                </label>
                <input 
                  v-model="profile.address" 
                  type="text" 
                  class="form-input"
                  placeholder="Enter your complete delivery address"
                  @change="updateMapFromAddress"
                />
                <small class="form-help">This address will be used for gas delivery</small>
              </div>
            </div>
          </div>

          <div class="map-section">
            <div class="section-header">
              <h3>Delivery Location</h3>
              <p>Your current location for gas delivery</p>
            </div>
            
            <div class="location-info">
              <div class="location-card">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" class="location-icon">
                  <path d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" stroke="#FF7828" stroke-width="2"/>
                  <path d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" stroke="#FF7828" stroke-width="2"/>
                </svg>
                <div class="location-details">
                  <strong>Current Delivery Address</strong>
                  <p>{{ profile.address || 'No address set' }}</p>
                </div>
              </div>
            </div>

            <div class="map-container">
              <div id="map" class="map"></div>
            </div>
          </div>
        </div>
      </div>

      <div class="profile-footer">
        <button class="cancel-btn" @click="handleClose" @mousedown.stop>
          <svg width="16" height="16" viewBox="0 0 24 24" fill="none">
            <path d="M6 18L18 6M6 6l12 12" stroke="currentColor" stroke-width="2"/>
          </svg>
          Cancel
        </button>
        <button class="save-btn" @click="saveProfile" :disabled="loading">
          <svg width="16" height="16" viewBox="0 0 24 24" fill="none">
            <path d="M19 21H5a2 2 0 01-2-2V5a2 2 0 012-2h11l5 5v11a2 2 0 01-2 2z" stroke="currentColor" stroke-width="2"/>
            <path d="M17 21v-8H7v8M7 3v5h8" stroke="currentColor" stroke-width="2"/>
          </svg>
          {{ loading ? 'Saving...' : 'Save Changes' }}
        </button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, defineEmits, onUnmounted } from "vue";
import { doc, getDoc, updateDoc, onSnapshot } from "firebase/firestore";
import { db, auth } from "../../firebase";
import { updateEmail, updatePassword } from "firebase/auth";
import L from "leaflet";
import "leaflet/dist/leaflet.css";

// Define emits
const emit = defineEmits(['close']);

// Fix for default markers in Leaflet with Webpack
delete L.Icon.Default.prototype._getIconUrl;
L.Icon.Default.mergeOptions({
  iconRetinaUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.7.1/images/marker-icon-2x.png',
  iconUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.7.1/images/marker-icon.png',
  shadowUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.7.1/images/marker-shadow.png',
});

const profile = ref({
  name: "",
  email: "",
  phone: "",
  address: "",
  lat: 13.152945,
  lng: 121.271435
});

const newPassword = ref("");
const loading = ref(true);
let unsubscribeCustomer = null;
let map, marker;

// ✅ BAGO: Real-time listener para sa customer data
function setupRealtimeListener() {
  const user = auth.currentUser;
  if (!user) {
    console.log('❌ No user logged in');
    loading.value = false;
    return;
  }

  console.log('🔄 Setting up REAL-TIME listener for customer profile');
  
  const customerDoc = doc(db, "customers", user.uid);
  
  unsubscribeCustomer = onSnapshot(customerDoc, 
    (docSnap) => {
      if (docSnap.exists()) {
        const data = docSnap.data();
        console.log('🔥 REAL-TIME PROFILE UPDATE:', data);
        
        // Update profile data
        profile.value.name = data.fullName || "";
        profile.value.email = data.email || "";
        profile.value.phone = data.phone || "";
        profile.value.address = data.address || "";
        profile.value.lat = parseFloat(data.latitude) || 13.152945;
        profile.value.lng = parseFloat(data.longitude) || 121.271435;
        
        // Update map if it exists
        if (map && marker) {
          updateMapLocation();
        }
        
        console.log('✅ Profile updated from real-time data');
      } else {
        console.log('❌ No customer document found');
      }
      loading.value = false;
    },
    (error) => {
      console.error('❌ Real-time listener error:', error);
      loading.value = false;
    }
  );
}

// ✅ BAGO: Update map location based on current profile data
function updateMapLocation() {
  if (map && marker) {
    const lat = profile.value.lat;
    const lng = profile.value.lng;
    
    marker.setLatLng([lat, lng]);
    map.setView([lat, lng], 16);
    
    marker.bindPopup(`
      <div class="map-popup">
        <h4>Delivery Location</h4>
        <p><strong>${profile.value.name}</strong></p>
        <p>${profile.value.address}</p>
      </div>
    `).openPopup();
  }
}

// Function para makuha ang coordinates mula sa address (forward geocoding)
async function getCoordsFromAddress(address) {
  try {
    const response = await fetch(
      `https://nominatim.openstreetmap.org/search?format=json&q=${encodeURIComponent(address)}&limit=1`
    );
    const data = await response.json();
    
    if (data && data.length > 0) {
      return {
        lat: parseFloat(data[0].lat),
        lng: parseFloat(data[0].lon)
      };
    }
    return null;
  } catch (error) {
    console.error("Error getting coordinates:", error);
    return null;
  }
}

// Function para makuha ang address mula sa coordinates (reverse geocoding)
async function getAddressFromCoords(lat, lng) {
  try {
    const response = await fetch(
      `https://nominatim.openstreetmap.org/reverse?format=json&lat=${lat}&lon=${lng}&zoom=18&addressdetails=1`
    );
    const data = await response.json();
    
    if (data && data.display_name) {
      return data.display_name;
    }
    return "Address not found";
  } catch (error) {
    console.error("Error getting address:", error);
    return "Error fetching address";
  }
}

// Fetch customer data from Firestore (fallback kung walang real-time)
async function fetchProfile() {
  try {
    const user = auth.currentUser;
    if (!user) {
      console.log('No user logged in');
      loading.value = false;
      return;
    }

    const customerDoc = doc(db, "customers", user.uid);
    const docSnap = await getDoc(customerDoc);
    
    if (docSnap.exists()) {
      const data = docSnap.data();
      console.log('📋 Customer profile data:', data);
      
      profile.value.name = data.fullName || "";
      profile.value.email = data.email || "";
      profile.value.phone = data.phone || "";
      profile.value.lat = parseFloat(data.latitude) || 13.152945;
      profile.value.lng = parseFloat(data.longitude) || 121.271435;
      profile.value.address = data.address || "San Antonio, Victoria, Oriental Mindoro";
    } else {
      console.log('❌ No customer document found');
    }
  } catch (error) {
    console.error("Error fetching profile:", error);
  } finally {
    loading.value = false;
  }
}

function initMap() {
  // Center sa customer location
  map = L.map("map").setView([profile.value.lat, profile.value.lng], 16);

  L.tileLayer("https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png", {
    maxZoom: 19,
    attribution: '© OpenStreetMap contributors'
  }).addTo(map);

  // Gumamit ng default Leaflet marker icon
  marker = L.marker([profile.value.lat, profile.value.lng], { 
    draggable: true
  }).addTo(map);

  // Ipakita ang customer name at location sa popup
  marker.bindPopup(`
    <div class="map-popup">
      <h4>Delivery Location</h4>
      <p><strong>${profile.value.name}</strong></p>
      <p>${profile.value.address}</p>
    </div>
  `).openPopup();

  // Drag event para i-update location at ipakita popup
  marker.on("dragend", async () => {
    const pos = marker.getLatLng();
    let lat = pos.lat;
    let lng = pos.lng;

    // Limit sa Victoria, Oriental Mindoro area
    const minLat = 12.9, maxLat = 13.3;
    const minLng = 120.9, maxLng = 121.4;
    if (lat < minLat) lat = minLat;
    if (lat > maxLat) lat = maxLat;
    if (lng < minLng) lng = minLng;
    if (lng > maxLng) lng = maxLng;

    profile.value.lat = lat;
    profile.value.lng = lng;

    marker.setLatLng([lat, lng]);
    map.setView([lat, lng], 16);

    // KUNIN ANG BAGONG ADDRESS BASE SA BAGONG COORDINATES
    const newAddress = await getAddressFromCoords(lat, lng);
    profile.value.address = newAddress;
    
    // Ipakita ang bagong address sa popup
    marker.bindPopup(`
      <div class="map-popup">
        <h4>Updated Location</h4>
        <p><strong>${profile.value.name}</strong></p>
        <p>${profile.value.address}</p>
      </div>
    `).openPopup();
  });

  // Click sa map para ipakita popup
  map.on("click", () => {
    marker.openPopup();
  });
}

// Update map kapag binago ang address sa input field
async function updateMapFromAddress() {
  if (!profile.value.address.trim()) return;
  
  const coords = await getCoordsFromAddress(profile.value.address);
  if (coords) {
    // Limit sa Victoria, Oriental Mindoro area
    const minLat = 12.9, maxLat = 13.3;
    const minLng = 120.9, maxLng = 121.4;
    
    let lat = coords.lat;
    let lng = coords.lng;
    
    if (lat < minLat) lat = minLat;
    if (lat > maxLat) lat = maxLat;
    if (lng < minLng) lng = minLng;
    if (lng > maxLng) lng = maxLng;

    profile.value.lat = lat;
    profile.value.lng = lng;

    // Update ang mapa at marker
    if (map && marker) {
      marker.setLatLng([lat, lng]);
      map.setView([lat, lng], 16);
      marker.bindPopup(`
        <div class="map-popup">
          <h4>Location Updated</h4>
          <p><strong>${profile.value.name}</strong></p>
          <p>${profile.value.address}</p>
        </div>
      `).openPopup();
    }
  } else {
    alert("Address not found. Please enter a valid address in Victoria, Oriental Mindoro.");
  }
}

// Save changes to Firestore and Firebase Auth
async function saveProfile() {
  try {
    loading.value = true;
    const user = auth.currentUser;
    if (!user) {
      alert("No user logged in");
      return;
    }

    const customerDoc = doc(db, "customers", user.uid);
    await updateDoc(customerDoc, {
      fullName: profile.value.name,
      email: profile.value.email,
      phone: profile.value.phone,
      latitude: profile.value.lat.toString(),
      longitude: profile.value.lng.toString(),
      address: profile.value.address,
      lastLogin: new Date().toISOString()
    });

    if (auth.currentUser.email !== profile.value.email) {
      await updateEmail(auth.currentUser, profile.value.email);
    }

    if (newPassword.value) {
      await updatePassword(auth.currentUser, newPassword.value);
      newPassword.value = "";
    }

    alert("Profile updated successfully!\nYour delivery location has been saved.");
  } catch (error) {
    console.error("Error updating profile:", error);
    alert("Failed to update profile. See console for details.");
  } finally {
    loading.value = false;
  }
}

// ✅ AYOS: Handle close function - tiyaking maayos na nag-eemit
function handleClose() {
  console.log('🔴 Close button clicked - emitting close event');
  emit('close');
}

onMounted(async () => {
  console.log('👤 Profile component mounted');
  
  // Unahin ang real-time listener
  setupRealtimeListener();
  
  // Fallback kung walang real-time data agad
  setTimeout(() => {
    if (loading.value) {
      console.log('⏳ Falling back to initial fetch...');
      fetchProfile();
    }
  }, 2000);
  
  // Initialize map after a short delay to ensure DOM is ready
  setTimeout(() => {
    if (!loading.value) {
      initMap();
    }
  }, 500);
});

onUnmounted(() => {
  // Cleanup real-time listener
  if (unsubscribeCustomer) {
    console.log('🧹 Cleaning up real-time listener');
    unsubscribeCustomer();
  }
});
</script>

<style scoped>
/* Parehong styles na nasa previous code... */
/* Loading State Styles */
.loading-state {
  display: flex;
  justify-content: center;
  align-items: center;
  padding: 4rem 2rem;
  color: #718096;
  font-size: 1.1rem;
}

.content-sections {
  animation: fadeIn 0.3s ease-in;
}

@keyframes fadeIn {
  from { opacity: 0; }
  to { opacity: 1; }
}

.modal-overlay {
  position: fixed;
  inset: 0;
  background: rgba(0, 0, 0, 0.6);
  display: flex;
  justify-content: center;
  align-items: center;
  z-index: 1000;
  padding: 1rem;
  backdrop-filter: blur(4px);
}

.profile-container {
  background: white;
  border-radius: 20px;
  box-shadow: 0 25px 80px rgba(0, 0, 0, 0.25);
  width: 900px;
  max-width: 95vw;
  max-height: 90vh;
  overflow: hidden;
  display: flex;
  flex-direction: column;
  border: 1px solid #f1f5f9;
}

.profile-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 1.5rem 2rem;
  background: linear-gradient(135deg, #FF7828 0%, #e6691f 100%);
  color: white;
  position: relative;
}

.profile-header::before {
  content: '';
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  height: 4px;
  background: rgba(255, 255, 255, 0.3);
}

.header-content {
  display: flex;
  align-items: center;
  gap: 12px;
}

.header-icon {
  color: white;
}

.profile-header h2 {
  margin: 0;
  font-size: 1.4rem;
  font-weight: 700;
  font-family: 'Inter', sans-serif;
}

.close-btn {
  background: rgba(255, 255, 255, 0.15);
  border: none;
  color: white;
  width: 40px;
  height: 40px;
  border-radius: 12px;
  display: flex;
  align-items: center;
  justify-content: center;
  cursor: pointer;
  transition: all 0.3s ease;
  backdrop-filter: blur(10px);
  position: relative;
  z-index: 10;
}

.close-btn:hover {
  background: rgba(255, 255, 255, 0.25);
  transform: rotate(90deg);
}

.profile-content {
  flex: 1;
  overflow-y: auto;
  padding: 0;
  background: #f8fafc;
}

.form-section {
  padding: 2rem;
  background: white;
  margin: 0;
}

.section-header {
  margin-bottom: 2rem;
}

.section-header h3 {
  margin: 0 0 0.5rem 0;
  color: #1a202c;
  font-size: 1.3rem;
  font-weight: 700;
  font-family: 'Inter', sans-serif;
}

.section-header p {
  margin: 0;
  color: #718096;
  font-size: 0.95rem;
}

.form-grid {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 1.5rem;
}

.form-group {
  display: flex;
  flex-direction: column;
}

.form-group.full-width {
  grid-column: 1 / -1;
}

.form-label {
  display: flex;
  align-items: center;
  gap: 8px;
  font-weight: 600;
  color: #2d3748;
  margin-bottom: 0.75rem;
  font-size: 0.9rem;
  font-family: 'Inter', sans-serif;
}

.label-icon {
  color: #FF7828;
}

.form-input {
  padding: 1rem 1.25rem;
  border: 2px solid #e2e8f0;
  border-radius: 12px;
  font-size: 0.95rem;
  transition: all 0.3s ease;
  background: white;
  font-family: 'Inter', sans-serif;
  color: #1a202c;
}

.form-input:focus {
  outline: none;
  border-color: #FF7828;
  box-shadow: 0 0 0 4px rgba(255, 120, 40, 0.1);
  background: white;
}

.form-input:disabled {
  background: #f7fafc;
  color: #a0aec0;
  cursor: not-allowed;
  border-color: #e2e8f0;
}

.form-input::placeholder {
  color: #a0aec0;
}

.form-help {
  color: #718096;
  font-size: 0.8rem;
  margin-top: 0.5rem;
  font-family: 'Inter', sans-serif;
}

.map-section {
  padding: 2rem;
  background: white;
  border-top: 1px solid #f1f5f9;
}

.location-info {
  margin-bottom: 1.5rem;
}

.location-card {
  display: flex;
  align-items: flex-start;
  gap: 12px;
  padding: 1.25rem;
  background: #f8fafc;
  border-radius: 12px;
  border: 1px solid #e2e8f0;
}

.location-icon {
  flex-shrink: 0;
  margin-top: 2px;
}

.location-details {
  flex: 1;
}

.location-details strong {
  display: block;
  color: #2d3748;
  font-size: 0.9rem;
  font-weight: 600;
  margin-bottom: 0.25rem;
  font-family: 'Inter', sans-serif;
}

.location-details p {
  margin: 0;
  color: #4a5568;
  font-size: 0.9rem;
  line-height: 1.4;
  font-family: 'Inter', sans-serif;
}

.map-container {
  position: relative;
  border-radius: 16px;
  overflow: hidden;
  box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
  border: 1px solid #e2e8f0;
}

.map {
  height: 300px;
  width: 100%;
}

.profile-footer {
  display: flex;
  justify-content: flex-end;
  gap: 1rem;
  padding: 1.5rem 2rem;
  border-top: 1px solid #f1f5f9;
  background: white;
}

.cancel-btn, .save-btn {
  display: flex;
  align-items: center;
  gap: 8px;
  padding: 0.875rem 2rem;
  border: none;
  border-radius: 12px;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.3s ease;
  font-size: 0.95rem;
  font-family: 'Inter', sans-serif;
}

.cancel-btn {
  background: #f7fafc;
  color: #4a5568;
  border: 2px solid #e2e8f0;
}

.cancel-btn:hover {
  background: #edf2f7;
  border-color: #cbd5e0;
  transform: translateY(-1px);
}

.save-btn {
  background: #FF7828;
  color: white;
  box-shadow: 0 4px 12px rgba(255, 120, 40, 0.3);
}

.save-btn:hover:not(:disabled) {
  background: #e6691f;
  transform: translateY(-1px);
  box-shadow: 0 6px 16px rgba(255, 120, 40, 0.4);
}

.save-btn:disabled {
  background: #cbd5e0;
  cursor: not-allowed;
  transform: none;
  box-shadow: none;
}

/* Map popup styles */
:deep(.map-popup) {
  font-family: 'Inter', sans-serif;
}

:deep(.map-popup h4) {
  margin: 0 0 0.5rem 0;
  color: #1a202c;
  font-size: 0.9rem;
  font-weight: 700;
}

:deep(.map-popup p) {
  margin: 0 0 0.25rem 0;
  color: #4a5568;
  font-size: 0.8rem;
  line-height: 1.3;
}

:deep(.map-popup strong) {
  color: #FF7828;
}

/* Leaflet marker styles */
:deep(.leaflet-marker-icon) {
  margin-left: -12px !important;
  margin-top: -41px !important;
}

:deep(.leaflet-marker-shadow) {
  margin-left: -12px !important;
  margin-top: -41px !important;
}

/* Scrollbar styling */
.profile-content::-webkit-scrollbar {
  width: 8px;
}

.profile-content::-webkit-scrollbar-track {
  background: #f1f5f9;
}

.profile-content::-webkit-scrollbar-thumb {
  background: #cbd5e0;
  border-radius: 4px;
}

.profile-content::-webkit-scrollbar-thumb:hover {
  background: #a0aec0;
}

/* Responsive design */
@media (max-width: 768px) {
  .form-grid {
    grid-template-columns: 1fr;
  }
  
  .profile-header,
  .form-section,
  .map-section,
  .profile-footer {
    padding: 1.25rem;
  }
  
  .map {
    height: 250px;
  }
  
  .profile-footer {
    flex-direction: column;
  }
  
  .cancel-btn, .save-btn {
    justify-content: center;
  }
}
</style>