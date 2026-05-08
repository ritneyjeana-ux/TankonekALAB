<template>
  <div class="admin-dashboard">
    <!-- Header -->
    <header class="dashboard-header">
      <div class="logo-section">
        <img :src="logo" alt="Tankonek Logo" class="logo" />
        <div class="admin-info">
          <h2>tankonek - <span class="highlight">Admin Portal</span></h2>
          <p class="admin-name">Welcome_jihub</p>
        </div>
      </div>
     
      <div class="header-actions">
        <button class="settings-btn" @click="showSettingsModal = true">
          <svg width="16" height="16" viewBox="0 0 24 24" fill="none">
            <path d="M12 15a3 3 0 100-6 3 3 0 000 6z" stroke="currentColor" stroke-width="2"/>
            <path d="M19.4 15a1.65 1.65 0 00.33 1.82l.06.06a2 2 0 010 2.83 2 2 0 01-2.83 0l-.06-.06a1.65 1.65 0 00-1.82-.33 1.65 1.65 0 00-1 1.51V21a2 2 0 01-2 2 2 2 0 01-2-2v-.09A1.65 1.65 0 009 19.4a1.65 1.65 0 00-1.82.33l-.06.06a2 2 0 01-2.83 0 2 2 0 010-2.83l.06-.06a1.65 1.65 0 00.33-1.82 1.65 1.65 0 00-1.51-1H3a2 2 0 01-2-2 2 2 0 012-2h.09A1.65 1.65 0 004.6 9a1.65 1.65 0 00-.33-1.82l-.06-.06a2 2 0 010-2.83 2 2 0 012.83 0l.06.06a1.65 1.65 0 001.82.33H9a1.65 1.65 0 001-1.51V3a2 2 0 012-2 2 2 0 012 2v.09a1.65 1.65 0 001 1.51 1.65 1.65 0 001.82-.33l.06-.06a2 2 0 012.83 0 2 2 0 010 2.83l-.06.06a1.65 1.65 0 00-.33 1.82V9a1.65 1.65 0 001.51 1H21a2 2 0 012 2 2 2 0 01-2 2h-.09a1.65 1.65 0 00-1.51 1z" stroke="currentColor" stroke-width="2"/>
          </svg>
          Settings
        </button>
        <button class="logout-btn" @click="logout">
          <svg width="16" height="16" viewBox="0 0 24 24" fill="none">
            <path d="M9 21H5a2 2 0 01-2-2V5a2 2 0 012-2h4" stroke="currentColor" stroke-width="2"/>
            <path d="M16 17l5-5-5-5" stroke="currentColor" stroke-width="2"/>
            <path d="M21 12H9" stroke="currentColor" stroke-width="2"/>
          </svg>
          Logout
        </button>
      </div>
    </header>

    <!-- Tabs -->
    <div class="tabs">
      <button 
        class="tab" 
        :class="{ active: activeTab === 'overview' }"
        @click="switchTab('overview')"
      >
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none">
          <path d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" stroke="currentColor" stroke-width="2"/>
          <path d="M9 22V12h6v10" stroke="currentColor" stroke-width="2"/>
        </svg>
        Overview
      </button>

      <button 
        class="tab" 
        :class="{ active: activeTab === 'users' }"
        @click="switchTab('users')"
      >
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none">
          <path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2" stroke="currentColor" stroke-width="2"/>
          <circle cx="9" cy="7" r="4" stroke="currentColor" stroke-width="2"/>
          <path d="M23 21v-2a4 4 0 00-3-3.87m-4-12a4 4 0 010 7.75" stroke="currentColor" stroke-width="2"/>
        </svg>
        Users
      </button>

      <button 
        class="tab" 
        :class="{ active: activeTab === 'orders' }"
        @click="switchTab('orders')"
      >
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none">
          <path d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" stroke="currentColor" stroke-width="2"/>
        </svg>
        Orders
      </button>

      <button 
        class="tab" 
        :class="{ active: activeTab === 'analytics' }"
        @click="switchTab('analytics')"
      >
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none">
          <path d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" stroke="currentColor" stroke-width="2"/>
        </svg>
        Analytics
      </button>

      <button 
        class="tab" 
        :class="{ active: activeTab === 'wallet' }"
        @click="switchTab('wallet')"
      >
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none">
          <rect x="2" y="5" width="20" height="14" rx="2" stroke="currentColor" stroke-width="2"/>
          <path d="M16 12h4M6 9h6" stroke="currentColor" stroke-width="2"/>
        </svg>
        Wallet
      </button>

    </div>

    <!-- Content Area - Dynamic Component Rendering -->
    <section class="content-section">
      <!-- Overview Content (Built-in sa parent) -->
      <div v-if="activeTab === 'overview'" class="tab-content">
        <div class="content-header">
          <h2>Gas Retailers Map - Victoria Oriental Mindoro</h2>
          <p>Real-time locations of all available gas retailers</p>
        </div>

        <!-- Loading State -->
        <div v-if="loading" class="loading-state">
          <svg width="48" height="48" viewBox="0 0 24 24" fill="none" class="loading-spinner">
            <path d="M12 2v4m0 12v4M4.93 4.93l2.83 2.83m8.48 8.48l2.83 2.83M2 12h4m12 0h4M4.93 19.07l2.83-2.83m8.48-8.48l2.83-2.83" stroke="#FF7828" stroke-width="2" stroke-linecap="round"/>
          </svg>
          <p>Loading map and retailer data...</p>
        </div>

        <!-- Leaflet Map Container -->
        <div v-else class="map-container">
          <div id="retailers-map" class="retailers-map"></div>
          <div class="map-legend">
            <h4>Retailer Ratings</h4>
            <div class="legend-item">
              <div class="legend-color excellent"></div>
              <span>Excellent (4.5-5.0)</span>
            </div>
            <div class="legend-item">
              <div class="legend-color good"></div>
              <span>Good (4.0-4.4)</span>
            </div>
            <div class="legend-item">
              <div class="legend-color average"></div>
              <span>Average (3.0-3.9)</span>
            </div>
            <div class="legend-item">
              <div class="legend-color poor"></div>
              <span>Poor (&lt;3.0)</span>
            </div>
          </div>
        </div>
      </div>

      <!-- Admin Wallet Content -->
      <div v-else-if="activeTab === 'wallet'" class="tab-content">
        <div class="content-header">
          <h2>Admin Platform Wallet</h2>
          <p>Temporary holding wallet for online payments before automatic distribution</p>
        </div>

        <div class="wallet-grid">
          <div class="wallet-card main-wallet">
            <span>Escrow / Holding Balance</span>
            <h2>₱{{ adminWallet.escrowBalance.toLocaleString() }}</h2>
            <p>Paid orders waiting for successful delivery</p>
          </div>

          <div class="wallet-card">
            <span>Admin Revenue</span>
            <h3>₱{{ adminWallet.adminRevenue.toLocaleString() }}</h3>
            <p>Platform share from delivery and commissions</p>
          </div>

          <div class="wallet-card">
            <span>Total Distributed</span>
            <h3>₱{{ adminWallet.totalDistributed.toLocaleString() }}</h3>
            <p>Released to retailer and rider wallets</p>
          </div>

          <div class="wallet-card">
            <span>Pending Distributions</span>
            <h3>{{ pendingDistributionCount }}</h3>
            <p>Paid orders not yet delivered</p>
          </div>
        </div>

        <div class="wallet-policy-card">
          <h3>Distribution Logic</h3>
          <div class="wallet-policy-grid">
            <div>
              <strong>Customer Online Payment</strong>
              <p>Payment goes first to the Admin Platform Wallet as escrow using the configured PayPal Sandbox merchant account.</p>
            </div>
            <div>
              <strong>Rider Share</strong>
              <p>Rider receives 67% of the delivery fee.</p>
            </div>
            <div>
              <strong>Admin Share</strong>
              <p>Admin keeps 33% of the delivery fee plus the ₱10 platform commission per order.</p>
            </div>
            <div>
              <strong>Retailer Share</strong>
              <p>Retailer receives the LPG product subtotal minus the ₱10 platform commission per order.</p>
            </div>
          </div>
        </div>

        <div class="wallet-history">
          <div class="wallet-history-header">
            <h3>Admin Wallet Transactions</h3>
            <p>Escrow entries and automatic distribution records</p>
          </div>

          <table class="wallet-table">
            <thead>
              <tr>
                <th>Date</th>
                <th>Order ID</th>
                <th>Type</th>
                <th>Amount</th>
                <th>Status</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="transaction in adminWalletTransactions" :key="transaction.id">
                <td>{{ formatWalletDate(transaction.createdAt) }}</td>
                <td>{{ transaction.orderNumber || transaction.orderId || 'N/A' }}</td>
                <td>{{ transaction.type }}</td>
                <td>₱{{ Number(transaction.amount || 0).toLocaleString() }}</td>
                <td>
                  <span class="wallet-status" :class="transaction.status">
                    {{ transaction.status }}
                  </span>
                </td>
              </tr>
              <tr v-if="adminWalletTransactions.length === 0">
                <td colspan="5" class="wallet-empty">No wallet transactions yet</td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>

      <!-- Child Components for Other Tabs -->
      <component 
        v-else
        :is="activeComponent" 
        :dashboard-data="dashboardData"
        :loading="loading"
        @update-data="handleDataUpdate"
      />
    </section>

    <!-- Settings Modal -->
    <div v-if="showSettingsModal" class="modal-overlay">
      <div class="modal">
        <div class="modal-icon">
          <svg width="48" height="48" viewBox="0 0 24 24" fill="none">
            <path d="M12 15a3 3 0 100-6 3 3 0 000 6z" stroke="#FF7828" stroke-width="2"/>
            <path d="M19.4 15a1.65 1.65 0 00.33 1.82l.06.06a2 2 0 010 2.83 2 2 0 01-2.83 0l-.06-.06a1.65 1.65 0 00-1.82-.33 1.65 1.65 0 00-1 1.51V21a2 2 0 01-2 2 2 2 0 01-2-2v-.09A1.65 1.65 0 009 19.4a1.65 1.65 0 00-1.82.33l-.06.06a2 2 0 01-2.83 0 2 2 0 010-2.83l.06-.06a1.65 1.65 0 00.33-1.82 1.65 1.65 0 00-1.51-1H3a2 2 0 01-2-2 2 2 0 012-2h.09A1.65 1.65 0 004.6 9a1.65 1.65 0 00-.33-1.82l-.06-.06a2 2 0 010-2.83 2 2 0 012.83 0l.06.06a1.65 1.65 0 001.82.33H9a1.65 1.65 0 001-1.51V3a2 2 0 012-2 2 2 0 012 2v.09a1.65 1.65 0 001 1.51 1.65 1.65 0 001.82-.33l.06-.06a2 2 0 012.83 0 2 2 0 010 2.83l-.06.06a1.65 1.65 0 00-.33 1.82V9a1.65 1.65 0 001.51 1H21a2 2 0 012 2 2 2 0 01-2 2h-.09a1.65 1.65 0 00-1.51 1z" stroke="#FF7828" stroke-width="2"/>
          </svg>
        </div>
        <div class="modal-buttons">
          <button class="modal-btn-confirm" @click="showSettingsModal = false">Close</button>
        </div>
      </div>
    </div>

    <!-- Logout Confirmation Modal -->
    <div v-if="showLogoutModal" class="modal-overlay">
      <div class="modal">
        <div class="modal-icon">
          <svg width="48" height="48" viewBox="0 0 24 24" fill="none">
            <path d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.35 16.5c-.77.833.192 2.5 1.732 2.5z" stroke="#FF7828" stroke-width="2"/>
          </svg>
        </div>
        <h3>Confirm Logout</h3>
        <p>Are you sure you want to logout?</p>
        <div class="modal-buttons">
          <button class="modal-btn-cancel" @click="cancelLogout">Cancel</button>
          <button class="modal-btn-confirm" @click="confirmLogout">Yes, Logout</button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted, provide, nextTick } from "vue";
import { router } from '@inertiajs/vue3';

// FIREBASE
import { db, auth } from "@/firebase";
import { 
  collection, 
  query, 
  where, 
  onSnapshot,
  getCountFromServer,
  orderBy,
  limit,
  getDocs,
  Timestamp,
  doc,
  getDoc,
  addDoc,
  updateDoc,
  serverTimestamp,
  runTransaction,
  increment
} from "firebase/firestore";
import { onAuthStateChanged } from "firebase/auth";

// Import child components
import Users from './Users.vue';
import Orders from './Orders.vue';
import Analytics from './Analytics.vue';

// Import the logo
const logo = '/images/logo.png';

// PayPal Sandbox Client ID for Admin/Platform merchant account
const ADMIN_PAYPAL_CLIENT_ID = "Ae7Fm9pT9iCbCktH2NiPp8C5r9t2G2ZtuByxGLW1BGcgjOrSF7lENCFuh3WVEwvc0mCzajtwfrpiszIE";

const showSettingsModal = ref(false);
const activeTab = ref("overview");
const loading = ref(true);
const mapInitialized = ref(false);

// Dashboard data - real-time from Firestore
const totalUsers = ref(0);
const customerCount = ref(0);
const retailerCount = ref(0);
const deliveryCount = ref(0);
const activeRetailers = ref(0);
const totalOrders = ref(0);
const activeOrders = ref(0);
const ordersToday = ref(0);
const ordersYesterday = ref(0);
const activeDeliveryPersonnel = ref(0);

// Real-time data collections - UPDATED FOR SEPARATE COLLECTIONS
const allOrders = ref([]);
const allDeliveries = ref([]);
const allInventory = ref([]);
const allCustomers = ref([]);
const allRetailers = ref([]);
const allRiders = ref([]);

// Admin wallet data
const adminWallet = ref({
  escrowBalance: 0,
  adminRevenue: 0,
  totalDistributed: 0
});
const adminWalletTransactions = ref([]);
const processedWalletOrders = ref(new Set());
const processingWalletOrders = ref(new Set());

const pendingDistributionCount = computed(() => {
  return allOrders.value.filter(order => {
    const isPaid = order.paymentStatus === 'paid';
    const isDistributed = order.distributionStatus === 'distributed';
    const isDelivered = order.status === 'delivered' || order.orderStatus === 'delivered';
    return isPaid && !isDistributed && !isDelivered;
  }).length;
});


// Map variables
let map = null;
let markers = ref([]);

// System alerts data
const systemAlerts = ref([]);

// Unsubscribe functions for real-time listeners
const unsubscribeFunctions = ref([]);

// Dashboard data object for sharing with child components
const dashboardData = ref({
  totalUsers: 0,
  customerCount: 0,
  retailerCount: 0,
  deliveryCount: 0,
  activeRetailers: 0,
  totalOrders: 0,
  activeOrders: 0,
  ordersToday: 0,
  ordersYesterday: 0,
  ordersYesterdayDiff: 0,
  activeDeliveryPersonnel: 0,
  recentOrders: [],
  systemAlerts: []
});

// Computed property for orders yesterday difference
const ordersYesterdayDiff = computed(() => {
  return Math.max(0, ordersToday.value - ordersYesterday.value);
});

// Computed property for active component
const activeComponent = computed(() => {
  const components = {
    users: Users,
    orders: Orders,
    analytics: Analytics
  };
  return components[activeTab.value] || null;
});

// Tab switching function
const switchTab = (tab) => {
  activeTab.value = tab;
  loading.value = true;
  
  setTimeout(() => {
    loading.value = false;
  }, 500);
};

// Handle data updates from child components
const handleDataUpdate = (newData) => {
  dashboardData.value = { ...dashboardData.value, ...newData };
};

// Provide data to child components
provide('dashboardData', dashboardData);
provide('loading', loading);
provide('adminPayPalClientId', ADMIN_PAYPAL_CLIENT_ID);

// Initialize Leaflet Map
const initializeMap = () => {
  nextTick(() => {
    const mapContainer = document.getElementById('retailers-map');
    if (!mapContainer) {
      console.error('Map container not found, retrying...');
      setTimeout(initializeMap, 100);
      return;
    }

    if (typeof L === 'undefined') {
      console.error('Leaflet library not loaded');
      return;
    }

    if (map) {
      map.remove();
      map = null;
    }

    try {
      map = L.map('retailers-map').setView([13.1775, 121.2775], 12);

      L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors',
        maxZoom: 19
      }).addTo(map);

      addRetailersToMap();
      
      mapInitialized.value = true;
      console.log('✅ Map initialized successfully');
    } catch (error) {
      console.error('❌ Error initializing map:', error);
    }
  });
};

// ✅ PINAGANDA KO ITO - Add retailers to the map with beautiful markers and names
const addRetailersToMap = () => {
  if (!map) {
    console.warn('Map not available for adding retailers');
    return;
  }

  markers.value.forEach(marker => {
    if (map && marker) {
      map.removeLayer(marker);
    }
  });
  markers.value = [];

  const retailers = allRetailers.value;
  
  if (retailers.length === 0) {
    console.log('No retailers to display on map');
    return;
  }

  const validMarkers = [];
  
  retailers.forEach(retailer => {
    const lat = parseFloat(retailer.latitude);
    const lng = parseFloat(retailer.longitude);
    
    if (isNaN(lat) || isNaN(lng)) {
      console.warn(`Invalid coordinates for retailer: ${retailer.businessName}`);
      return;
    }

    const avgRating = calculateAverageRating(retailer);
    const markerColor = getMarkerColor(avgRating);
    
    // ✅ PINAGANDA ANG MARKER DESIGN - may pangalan sa baba
    const customIcon = L.divIcon({
      html: `
        <div class="custom-marker-group">
          <div class="custom-marker" style="background-color: ${markerColor}">
            <div class="marker-rating">${avgRating > 0 ? avgRating.toFixed(1) : '★'}</div>
          </div>
          <div class="marker-label">${retailer.businessName || 'Gas Station'}</div>
        </div>
      `,
      className: 'custom-marker-container',
      iconSize: [80, 50],
      iconAnchor: [40, 50]
    });

    // ✅ PINAGANDA ANG POPUP CONTENT - mas maganda at malinis ang design
    const popupContent = `
      <div class="retailer-popup">
        <div class="popup-header" style="background: linear-gradient(135deg, #FF7828, #e6691f); color: white; padding: 12px; margin: -12px -12px 12px -12px; border-radius: 8px 8px 0 0;">
          <h3 style="margin: 0; font-size: 1.2rem;">${retailer.businessName || 'Unknown Business'}</h3>
        </div>
        
        <div class="popup-content" style="padding: 0 8px;">
          <div class="popup-row" style="display: flex; margin-bottom: 8px; align-items: center;">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#FF7828" style="margin-right: 8px;">
              <path d="M20 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2" stroke="currentColor" stroke-width="2"/>
              <circle cx="12" cy="7" r="4" stroke="currentColor" stroke-width="2"/>
            </svg>
            <span><strong>Owner:</strong> ${retailer.fullName || 'N/A'}</span>
          </div>
          
          <div class="popup-row" style="display: flex; margin-bottom: 8px; align-items: center;">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#FF7828" style="margin-right: 8px;">
              <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0118 0z" stroke="currentColor" stroke-width="2"/>
              <circle cx="12" cy="10" r="3" stroke="currentColor" stroke-width="2"/>
            </svg>
            <span><strong>Address:</strong> ${retailer.address || 'N/A'}</span>
          </div>
          
          <div class="popup-row" style="display: flex; margin-bottom: 8px; align-items: center;">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#FF7828" style="margin-right: 8px;">
              <path d="M22 16.92v3a2 2 0 01-2.18 2 19.79 19.79 0 01-8.63-3.07 19.5 19.5 0 01-6-6 19.79 19.79 0 01-3.07-8.67A2 2 0 014.11 2h3a2 2 0 012 1.72c.127.96.362 1.903.7 2.81a2 2 0 01-.45 2.11L8 10a16 16 0 006 6l1.27-1.27a2 2 0 012.11-.45c.907.338 1.85.573 2.81.7A2 2 0 0122 16.92z" stroke="currentColor" stroke-width="2"/>
            </svg>
            <span><strong>Phone:</strong> ${retailer.phone || 'N/A'}</span>
          </div>
          
          <div class="popup-row" style="display: flex; margin-bottom: 8px; align-items: center;">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#FF7828" style="margin-right: 8px;">
              <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z" stroke="currentColor" stroke-width="2"/>
              <path d="M22 6l-10 7L2 6" stroke="currentColor" stroke-width="2"/>
            </svg>
            <span><strong>Email:</strong> ${retailer.email || 'N/A'}</span>
          </div>
          
          <div class="popup-divider" style="height: 1px; background: #e2e8f0; margin: 12px 0;"></div>
          
          <div class="popup-row" style="display: flex; justify-content: space-between; align-items: center;">
            <div class="rating-display" style="display: flex; align-items: center; gap: 8px;">
              <strong>Rating:</strong> 
              <span class="rating-stars" style="color: #f59e0b;">${generateStarRating(avgRating)}</span>
            </div>
            <span class="rating-value" style="background: #f1f5f9; padding: 4px 8px; border-radius: 20px; font-weight: bold; color: #1a202c;">
              ${avgRating.toFixed(1)} (${retailer.totalRatings || 0})
            </span>
          </div>
        </div>
      </div>
    `;

    try {
      const marker = L.marker([lat, lng], { icon: customIcon })
        .addTo(map)
        .bindPopup(popupContent, {
          maxWidth: 300,
          className: 'custom-popup'
        });
      
      validMarkers.push(marker);
    } catch (error) {
      console.error('Error adding marker to map:', error);
    }
  });

  markers.value = validMarkers;

  if (validMarkers.length > 0) {
    try {
      const group = new L.featureGroup(validMarkers);
      map.fitBounds(group.getBounds().pad(0.1));
    } catch (error) {
      console.error('Error fitting map bounds:', error);
    }
  } else {
    map.setView([13.1775, 121.2775], 12);
  }
};

// Calculate average rating from ratings array or rating field
const calculateAverageRating = (retailer) => {
  if (retailer.ratings && retailer.ratings.length > 0) {
    const sum = retailer.ratings.reduce((acc, rating) => acc + rating, 0);
    return sum / retailer.ratings.length;
  } else if (retailer.rating) {
    return retailer.rating;
  } else {
    return 0;
  }
};

// Get marker color based on rating
const getMarkerColor = (rating) => {
  if (rating >= 4.5) return '#22c55e'; // Excellent - Green
  if (rating >= 4.0) return '#3b82f6'; // Good - Blue
  if (rating >= 3.0) return '#f59e0b'; // Average - Yellow
  return '#ef4444'; // Poor - Red
};

// Generate star rating HTML
const generateStarRating = (rating) => {
  const fullStars = Math.floor(rating);
  const halfStar = rating % 1 >= 0.5;
  const emptyStars = 5 - fullStars - (halfStar ? 1 : 0);
  
  let stars = '';
  
  for (let i = 0; i < fullStars; i++) {
    stars += '★';
  }
  
  if (halfStar) {
    stars += '½';
  }
  
  for (let i = 0; i < emptyStars; i++) {
    stars += '☆';
  }
  
  return stars;
};

// Format date for display
const formatDate = (date) => {
  if (!date) return 'Unknown date';
  const d = date.toDate ? date.toDate() : new Date(date);
  return d.toLocaleString('en-US', {
    year: 'numeric',
    month: 'short',
    day: 'numeric',
    hour: '2-digit',
    minute: '2-digit'
  });
};

// Format wallet date
const formatWalletDate = (date) => {
  if (!date) return 'N/A';

  try {
    const walletDate = date?.toDate ? date.toDate() : new Date(date);
    return walletDate.toLocaleDateString('en-PH', {
      month: 'short',
      day: 'numeric',
      year: 'numeric',
      hour: '2-digit',
      minute: '2-digit'
    });
  } catch (error) {
    return 'Invalid Date';
  }
};

// Load admin wallet summary and transaction history
const setupAdminWalletListener = () => {
  const walletRef = doc(db, "platform_wallets", "admin");

  const walletUnsubscribe = onSnapshot(walletRef, (snapshot) => {
    if (snapshot.exists()) {
      const data = snapshot.data();
      adminWallet.value = {
        escrowBalance: Number(data.escrowBalance || 0),
        adminRevenue: Number(data.adminRevenue || 0),
        totalDistributed: Number(data.totalDistributed || 0)
      };
    }
  });

  const transactionsQuery = query(
    collection(db, "admin_wallet_transactions"),
    orderBy("createdAt", "desc"),
    limit(25)
  );

  const transactionsUnsubscribe = onSnapshot(transactionsQuery, (snapshot) => {
    adminWalletTransactions.value = snapshot.docs.map((transactionDoc) => ({
      id: transactionDoc.id,
      ...transactionDoc.data()
    }));
  });

  unsubscribeFunctions.value.push(walletUnsubscribe, transactionsUnsubscribe);
};

// Create escrow record when customer online payment becomes paid
const recordPaymentToAdminWallet = async (order) => {
  const orderNumber = order.orderNumber || order.orderId || order.displayId || order.id;

  if (!orderNumber || processingWalletOrders.value.has(orderNumber)) return;
  if (order.paymentStatus !== 'paid') return;
  if (order.escrowStatus === 'held' || order.escrowRecorded === true) return;

  const paymentMethod = order.paymentMethod || '';
  if (paymentMethod === 'cash' || paymentMethod === 'cash_on_delivery') return;

  processingWalletOrders.value.add(orderNumber);

  try {
    const orderRef = doc(db, "orders", order.id);
    const walletRef = doc(db, "platform_wallets", "admin");
    const amount = Number(order.totalAmount || order.total || 0);

    await runTransaction(db, async (transaction) => {
      const latestOrder = await transaction.get(orderRef);

      if (!latestOrder.exists()) {
        throw new Error("ORDER_NOT_FOUND");
      }

      const latestOrderData = latestOrder.data();

      if (latestOrderData.escrowStatus === 'held' || latestOrderData.escrowRecorded === true) {
        return;
      }

      transaction.set(walletRef, {
        escrowBalance: increment(amount),
        updatedAt: serverTimestamp()
      }, { merge: true });

      transaction.update(orderRef, {
        escrowStatus: 'held',
        escrowRecorded: true,
        escrowAmount: amount,
        escrowRecordedAt: serverTimestamp()
      });
    });

    await addDoc(collection(db, "admin_wallet_transactions"), {
      orderId: order.id,
      orderNumber,
      type: "Escrow In",
      amount,
      status: "held",
      paymentMethod: order.paymentMethod || "online",
      paypalClientId: ADMIN_PAYPAL_CLIENT_ID,
      paymentGateway: order.paymentMethod === "paypal" ? "PayPal Sandbox" : (order.paymentGateway || "online"),
      createdAt: serverTimestamp()
    });
  } catch (error) {
    console.error("Error recording payment to admin wallet:", error);
  } finally {
    processingWalletOrders.value.delete(orderNumber);
  }
};

// Distribute money once order is successfully delivered
const distributeDeliveredOrderPayment = async (order) => {
  const orderNumber = order.orderNumber || order.orderId || order.displayId || order.id;

  if (!orderNumber || processingWalletOrders.value.has(`distribute-${orderNumber}`)) return;
  if (order.paymentStatus !== 'paid') return;
  if (order.distributionStatus === 'distributed') return;

  const isDelivered = order.status === 'delivered' || order.orderStatus === 'delivered';
  if (!isDelivered) return;

  const paymentMethod = order.paymentMethod || '';
  if (paymentMethod === 'cash' || paymentMethod === 'cash_on_delivery') return;

  processingWalletOrders.value.add(`distribute-${orderNumber}`);

  try {
    const orderRef = doc(db, "orders", order.id);
    const adminWalletRef = doc(db, "platform_wallets", "admin");

    const matchingDelivery = allDeliveries.value.find(delivery =>
      delivery.orderId === orderNumber ||
      delivery.displayId === orderNumber ||
      delivery.orderNumber === orderNumber
    );

    const deliveryId = matchingDelivery?.id || order.deliveryId || null;
    const deliveryRef = deliveryId ? doc(db, "deliveries", deliveryId) : null;

    const totalAmount = Number(order.totalAmount || order.total || 0);
    const productSubtotal = Number(order.subtotal || order.productSubtotal || 0);
    const deliveryFee = Number(order.deliveryFee || matchingDelivery?.deliveryFee || 0);

const riderShare = Number((deliveryFee * 0.67).toFixed(2));
const adminDeliveryShare = Number((deliveryFee * 0.33).toFixed(2));
    const commissionShare = 10;
    const adminShare = adminDeliveryShare + commissionShare;
    const retailerShare = Math.max(productSubtotal - commissionShare, 0);
    const totalDistributed = riderShare + retailerShare + adminShare;

    const riderId = order.riderId || matchingDelivery?.riderId || null;
    const retailerId = order.retailerId || matchingDelivery?.retailerId || null;

    await runTransaction(db, async (transaction) => {
      const latestOrder = await transaction.get(orderRef);

      if (!latestOrder.exists()) {
        throw new Error("ORDER_NOT_FOUND");
      }

      const latestOrderData = latestOrder.data();

      if (latestOrderData.distributionStatus === 'distributed') {
        return;
      }

      transaction.set(adminWalletRef, {
        escrowBalance: increment(-totalAmount),
        adminRevenue: increment(adminShare),
        totalDistributed: increment(totalDistributed),
        updatedAt: serverTimestamp()
      }, { merge: true });

      if (riderId && riderShare > 0) {
        const riderWalletRef = doc(db, "rider_wallets", riderId);
        transaction.set(riderWalletRef, {
          availableBalance: increment(riderShare),
          totalEarnings: increment(riderShare),
          updatedAt: serverTimestamp()
        }, { merge: true });
      }

      if (retailerId && retailerShare > 0) {
        const retailerWalletRef = doc(db, "retailer_wallets", retailerId);
        transaction.set(retailerWalletRef, {
          availableBalance: increment(retailerShare),
          totalEarnings: increment(retailerShare),
          updatedAt: serverTimestamp()
        }, { merge: true });
      }

      transaction.update(orderRef, {
        distributionStatus: 'distributed',
        distributedAt: serverTimestamp(),
        riderShare,
        retailerShare,
        adminShare,
        adminDeliveryShare,
        distributionBreakdown: {
          productSubtotal,
          deliveryFee,
          riderShare,
          adminDeliveryShare,
          commissionShare,
          retailerShare,
          adminShare
        }
      });

      if (deliveryRef) {
        transaction.update(deliveryRef, {
          distributionStatus: 'distributed',
          distributedAt: serverTimestamp(),
          riderShare,
          adminShare,
          adminDeliveryShare
        });
      }
    });

    await addDoc(collection(db, "admin_wallet_transactions"), {
      orderId: order.id,
      orderNumber,
      type: "Auto Distribution",
      amount: totalDistributed,
      status: "distributed",
      riderShare,
      retailerShare,
      adminShare,
      deliveryFee,
      createdAt: serverTimestamp()
    });

    if (riderId && riderShare > 0) {
      await addDoc(collection(db, "rider_wallet_transactions"), {
        riderId,
        orderNumber,
        type: "Delivery Earnings",
        amount: riderShare,
        status: "credited",
        description: `67% rider share from delivery fee for ${orderNumber}`,
        createdAt: serverTimestamp()
      });
    }

    if (retailerId && retailerShare > 0) {
      await addDoc(collection(db, "retailer_wallet_transactions"), {
        retailerId,
        orderNumber,
        type: "Product Sale",
        amount: retailerShare,
        status: "credited",
        description: `LPG product payment minus ₱10 platform commission for ${orderNumber}`,
        createdAt: serverTimestamp()
      });
    }
  } catch (error) {
    console.error("Error distributing delivered order payment:", error);
  } finally {
    processingWalletOrders.value.delete(`distribute-${orderNumber}`);
  }
};

// Runs wallet escrow and distribution checks from real-time order updates
const processWalletAutomation = () => {
  allOrders.value.forEach((order) => {
    recordPaymentToAdminWallet(order);
    distributeDeliveredOrderPayment(order);
  });
};

// Fetch real-time data from Firestore
const setupRealTimeListeners = () => {
  console.log("🔄 Setting up real-time listeners for admin dashboard");

  const ordersQuery = query(collection(db, "orders"), orderBy("createdAt", "desc"));
  const ordersUnsubscribe = onSnapshot(ordersQuery, (snapshot) => {
      const ordersList = [];
      snapshot.forEach((doc) => {
        const orderData = doc.data();
        ordersList.push({
          id: doc.id,
          ...orderData,
          orderDate: orderData.orderDate?.toDate ? orderData.orderDate.toDate() : new Date(orderData.orderDate),
          createdAt: orderData.createdAt?.toDate ? orderData.createdAt.toDate() : new Date(orderData.createdAt),
          updatedAt: orderData.updatedAt?.toDate ? orderData.updatedAt.toDate() : new Date(orderData.updatedAt)
        });
      });
      
      allOrders.value = ordersList;
      updateDashboardStats();
      processWalletAutomation();
      console.log("✅ Orders loaded:", ordersList.length);
    },
    (error) => {
      console.error("❌ Error listening to orders:", error);
    }
  );

  const deliveriesQuery = query(collection(db, "deliveries"));
  const deliveriesUnsubscribe = onSnapshot(deliveriesQuery, (snapshot) => {
      const deliveriesList = [];
      snapshot.forEach((doc) => {
        const deliveryData = doc.data();
        deliveriesList.push({
          id: doc.id,
          ...deliveryData,
          createdAt: deliveryData.createdAt?.toDate ? deliveryData.createdAt.toDate() : new Date(deliveryData.createdAt),
          updatedAt: deliveryData.updatedAt?.toDate ? deliveryData.updatedAt.toDate() : new Date(deliveryData.updatedAt)
        });
      });
      
      allDeliveries.value = deliveriesList;
      updateDashboardStats();
      processWalletAutomation();
      console.log("✅ Deliveries loaded:", deliveriesList.length);
    },
    (error) => {
      console.error("❌ Error listening to deliveries:", error);
    }
  );

  const inventoryQuery = query(collection(db, "inventory"));
  const inventoryUnsubscribe = onSnapshot(inventoryQuery, (snapshot) => {
      const inventoryList = [];
      snapshot.forEach((doc) => {
        const inventoryData = doc.data();
        inventoryList.push({
          id: doc.id,
          ...inventoryData,
          lastUpdated: inventoryData.lastUpdated?.toDate ? inventoryData.lastUpdated.toDate() : new Date(inventoryData.lastUpdated)
        });
      });
      
      allInventory.value = inventoryList;
      updateSystemAlerts();
      console.log("✅ Inventory loaded:", inventoryList.length);
    },
    (error) => {
      console.error("❌ Error listening to inventory:", error);
    }
  );

  const customersQuery = query(collection(db, "customers"));
  const customersUnsubscribe = onSnapshot(customersQuery, (snapshot) => {
      const customersList = [];
      snapshot.forEach((doc) => {
        customersList.push({
          id: doc.id,
          ...doc.data()
        });
      });
      
      allCustomers.value = customersList;
      updateDashboardStats();
      console.log("✅ Customers loaded:", customersList.length);
    },
    (error) => {
      console.error("❌ Error listening to customers:", error);
    }
  );

  const retailersQuery = query(collection(db, "retailers"));
  const retailersUnsubscribe = onSnapshot(retailersQuery, (snapshot) => {
      const retailersList = [];
      snapshot.forEach((doc) => {
        retailersList.push({
          id: doc.id,
          ...doc.data()
        });
      });
      
      allRetailers.value = retailersList;
      updateDashboardStats();
      
      if (mapInitialized.value && map) {
        addRetailersToMap();
      }
      
      console.log("✅ Retailers loaded:", retailersList.length);
    },
    (error) => {
      console.error("❌ Error listening to retailers:", error);
    }
  );

  const ridersQuery = query(collection(db, "riders"));
  const ridersUnsubscribe = onSnapshot(ridersQuery, (snapshot) => {
      const ridersList = [];
      snapshot.forEach((doc) => {
        ridersList.push({
          id: doc.id,
          ...doc.data()
        });
      });
      
      allRiders.value = ridersList;
      updateDashboardStats();
      console.log("✅ Riders loaded:", ridersList.length);
    },
    (error) => {
      console.error("❌ Error listening to riders:", error);
    }
  );

  unsubscribeFunctions.value = [
    ordersUnsubscribe,
    deliveriesUnsubscribe,
    inventoryUnsubscribe,
    customersUnsubscribe,
    retailersUnsubscribe,
    ridersUnsubscribe
  ];
};

// Update dashboard statistics based on real-time data
const updateDashboardStats = () => {
  customerCount.value = allCustomers.value.length;
  retailerCount.value = allRetailers.value.length;
  deliveryCount.value = allRiders.value.length;
  totalUsers.value = customerCount.value + retailerCount.value + deliveryCount.value;
  
  totalOrders.value = allOrders.value.length;
  
  const activeOrderStatuses = ['pending', 'accepted', 'assigned', 'ready-for-pickup', 'in-transit'];
  activeOrders.value = allOrders.value.filter(order => 
    activeOrderStatuses.includes(order.orderStatus)
  ).length;
  
  const today = new Date();
  today.setHours(0, 0, 0, 0);
  
  const yesterday = new Date(today);
  yesterday.setDate(yesterday.getDate() - 1);
  
  ordersToday.value = allOrders.value.filter(order => {
    const orderDate = order.orderDate instanceof Date ? order.orderDate : new Date(order.orderDate);
    return orderDate >= today;
  }).length;
  
  const tomorrow = new Date(today);
  tomorrow.setDate(tomorrow.getDate() + 1);
  
  ordersYesterday.value = allOrders.value.filter(order => {
    const orderDate = order.orderDate instanceof Date ? order.orderDate : new Date(order.orderDate);
    return orderDate >= yesterday && orderDate < today;
  }).length;
  
  activeRetailers.value = allRetailers.value.filter(retailer => 
    retailer.status === 'active' || retailer.status === 'approved' || !retailer.status
  ).length;
  
  const activeDeliveryStatuses = ['accepted', 'assigned', 'in-transit', 'ready-for-pickup'];
  const activeDeliveries = allDeliveries.value.filter(delivery => 
    activeDeliveryStatuses.includes(delivery.status)
  );
  
  const uniqueDeliveryPersonnel = new Set(
    activeDeliveries.map(delivery => delivery.riderId).filter(Boolean)
  );
  activeDeliveryPersonnel.value = uniqueDeliveryPersonnel.size;
  
  dashboardData.value = {
    totalUsers: totalUsers.value,
    customerCount: customerCount.value,
    retailerCount: retailerCount.value,
    deliveryCount: deliveryCount.value,
    activeRetailers: activeRetailers.value,
    totalOrders: totalOrders.value,
    activeOrders: activeOrders.value,
    ordersToday: ordersToday.value,
    ordersYesterday: ordersYesterday.value,
    ordersYesterdayDiff: ordersYesterdayDiff.value,
    activeDeliveryPersonnel: activeDeliveryPersonnel.value,
    recentOrders: allOrders.value.slice(0, 5),
    systemAlerts: systemAlerts.value
  };
  
  if (!mapInitialized.value && allRetailers.value.length > 0 && activeTab.value === 'overview') {
    setTimeout(() => {
      initializeMap();
    }, 500);
  }
  
  loading.value = false;
};

// Update system alerts based on inventory and other conditions
const updateSystemAlerts = () => {
  const alerts = [];
  
  const lowStockItems = allInventory.value.filter(item => item.isLow === true);
  
  lowStockItems.forEach(item => {
    alerts.push({
      id: `low-stock-${item.id}`,
      message: `Low stock alert for ${item.type} ${item.tankSize} at ${item.addedByBusiness}`,
      date: item.lastUpdated || new Date(),
      type: 'warning'
    });
  });
  
  const pendingOrdersCount = allOrders.value.filter(order => order.orderStatus === 'pending').length;
  if (pendingOrdersCount > 0) {
    alerts.push({
      id: 'pending-orders',
      message: `${pendingOrdersCount} pending orders need attention`,
      date: new Date(),
      type: 'info'
    });
  }
  
  systemAlerts.value = alerts;
};

// Clean up real-time listeners
const cleanupListeners = () => {
  unsubscribeFunctions.value.forEach(unsubscribe => {
    if (unsubscribe) unsubscribe();
  });
  unsubscribeFunctions.value = [];
};

// Clean up map when component is destroyed
const cleanupMap = () => {
  if (map) {
    map.remove();
    map = null;
  }
  mapInitialized.value = false;
};

// Logout modal logic
const showLogoutModal = ref(false);

function logout() {
  showLogoutModal.value = true;
}

function confirmLogout() {
  localStorage.removeItem("admin");
  cleanupListeners();
  cleanupMap();
  router.visit("/");
  showLogoutModal.value = false;
}

function cancelLogout() {
  showLogoutModal.value = false;
}

// Initialize on component mount
onMounted(() => {
  onAuthStateChanged(auth, (user) => {
    if (user) {
      setupRealTimeListeners();
      setupAdminWalletListener();
    } else {
      loading.value = false;
      console.log("No admin logged in");
    }
  });
});

// Clean up on component unmount
onUnmounted(() => {
  cleanupListeners();
  cleanupMap();
});
</script>

<style scoped>
.admin-dashboard {
  font-family: 'Inter', 'Poppins', sans-serif;
  padding: 2rem;
  background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
  min-height: 100vh;
}

/* Header Styles */
.dashboard-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 2rem;
}

.logo-section {
  display: flex;
  align-items: center;
  gap: 16px;
}

.logo {
  width: 70px;
  height: 70px;
  border-radius: 14px;
  box-shadow: 0 8px 20px rgba(255, 120, 40, 0.4);
  border: 2px solid rgba(255, 120, 40, 0.15);
  object-fit: contain;
}

.admin-info h2 {
  font-size: 1.3rem;
  margin: 0;
  color: #1a202c;
  font-weight: 600;
}

.highlight {
  color: #FF7828;
  font-weight: 700;
}

.admin-name {
  margin: 0;
  color: #718096;
  font-size: 0.95rem;
  font-weight: 500;
}

.header-actions {
  display: flex;
  gap: 12px;
}

.settings-btn, .logout-btn {
  display: flex;
  align-items: center;
  gap: 8px;
  padding: 0.75rem 1.5rem;
  border: none;
  border-radius: 10px;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.3s ease;
  font-size: 0.9rem;
}

.settings-btn {
  background: white;
  color: #4a5568;
  border: 2px solid #e2e8f0;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
}

.settings-btn:hover {
  border-color: #FF7828;
  color: #FF7828;
  transform: translateY(-1px);
  box-shadow: 0 4px 12px rgba(255, 120, 40, 0.15);
}

.logout-btn {
  background: #FF7828;
  color: white;
  box-shadow: 0 4px 12px rgba(255, 120, 40, 0.3);
}

.logout-btn:hover {
  background: #e6691f;
  transform: translateY(-1px);
  box-shadow: 0 6px 16px rgba(255, 120, 40, 0.4);
}

/* Tabs */
.tabs {
  display: flex;
  gap: 1rem;
  margin-bottom: 2rem;
  background: white;
  padding: 0.5rem;
  border-radius: 14px;
  box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
  border: 1px solid #f1f5f9;
}

.tab {
  flex: 1;
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 8px;
  padding: 1rem 1.5rem;
  border-radius: 10px;
  background: transparent;
  border: none;
  cursor: pointer;
  font-weight: 600;
  color: #718096;
  transition: all 0.3s ease;
  font-size: 0.95rem;
}

.tab:hover {
  color: #FF7828;
  background: rgba(255, 120, 40, 0.05);
}

.tab.active {
  background: #FF7828;
  color: white;
  box-shadow: 0 4px 12px rgba(255, 120, 40, 0.3);
}

/* Content Section */
.content-section {
  background: white;
  border-radius: 16px;
  box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
  border: 1px solid #f1f5f9;
  overflow: hidden;
}

.tab-content {
  padding: 2rem;
}

.content-header {
  margin-bottom: 2rem;
}

.content-header h2 {
  margin: 0 0 0.5rem 0;
  color: #1a202c;
  font-size: 1.5rem;
  font-weight: 700;
}

.content-header p {
  margin: 0;
  color: #718096;
  font-size: 1rem;
}

/* Loading State */
.loading-state {
  text-align: center;
  padding: 3rem 2rem;
  color: #718096;
}

.loading-spinner {
  animation: spin 1s linear infinite;
  margin-bottom: 1rem;
}

@keyframes spin {
  from { transform: rotate(0deg); }
  to { transform: rotate(360deg); }
}

/* Map Container */
.map-container {
  position: relative;
  height: 600px;
  border-radius: 12px;
  overflow: hidden;
  box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
}

.retailers-map {
  height: 100%;
  width: 100%;
  background: #f8fafc;
}

.map-legend {
  position: absolute;
  top: 20px;
  right: 20px;
  background: white;
  padding: 1rem;
  border-radius: 8px;
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
  z-index: 1000;
  min-width: 200px;
}

.map-legend h4 {
  margin: 0 0 0.75rem 0;
  color: #1a202c;
  font-size: 0.9rem;
  font-weight: 600;
}

.legend-item {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  margin-bottom: 0.5rem;
}

.legend-color {
  width: 16px;
  height: 16px;
  border-radius: 50%;
  border: 2px solid white;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.2);
}

.legend-color.excellent {
  background-color: #22c55e;
}

.legend-color.good {
  background-color: #3b82f6;
}

.legend-color.average {
  background-color: #f59e0b;
}

.legend-color.poor {
  background-color: #ef4444;
}

.legend-item span {
  font-size: 0.8rem;
  color: #4a5568;
}

/* ✅ PINAGANDA ANG MARKER STYLES */
:deep(.custom-marker-container) {
  background: transparent;
  border: none;
}

:deep(.custom-marker-group) {
  display: flex;
  flex-direction: column;
  align-items: center;
  cursor: pointer;
}

:deep(.custom-marker) {
  width: 36px;
  height: 36px;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  color: white;
  font-weight: bold;
  font-size: 0.8rem;
  box-shadow: 0 4px 10px rgba(0, 0, 0, 0.3);
  border: 3px solid white;
  cursor: pointer;
  transition: all 0.2s ease;
  animation: markerPop 0.3s ease;
}

@keyframes markerPop {
  0% { transform: scale(0); }
  80% { transform: scale(1.1); }
  100% { transform: scale(1); }
}

:deep(.custom-marker:hover) {
  transform: scale(1.15);
  box-shadow: 0 6px 15px rgba(0, 0, 0, 0.4);
  z-index: 1000;
}

:deep(.marker-label) {
  background: rgba(255, 255, 255, 0.95);
  padding: 4px 8px;
  border-radius: 20px;
  font-size: 0.7rem;
  font-weight: 600;
  color: #1a202c;
  margin-top: 4px;
  white-space: nowrap;
  box-shadow: 0 2px 6px rgba(0, 0, 0, 0.15);
  border: 1px solid #e2e8f0;
  max-width: 120px;
  overflow: hidden;
  text-overflow: ellipsis;
}

:deep(.marker-label:hover) {
  background: white;
  max-width: none;
  z-index: 1001;
}

/* ✅ PINAGANDA ANG POPUP STYLES */
:deep(.custom-popup .leaflet-popup-content-wrapper) {
  border-radius: 12px;
  padding: 0;
  overflow: hidden;
  box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
  border: 1px solid rgba(255, 120, 40, 0.2);
}

:deep(.custom-popup .leaflet-popup-content) {
  margin: 0;
  width: 300px !important;
  max-width: 300px;
}

:deep(.custom-popup .leaflet-popup-tip) {
  background: white;
  box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
}

:deep(.retailer-popup) {
  font-family: 'Inter', 'Poppins', sans-serif;
  padding: 12px;
}

:deep(.popup-header h3) {
  font-weight: 600;
  letter-spacing: -0.01em;
}

:deep(.popup-row) {
  font-size: 0.9rem;
  color: #4a5568;
}

:deep(.popup-row svg) {
  flex-shrink: 0;
}

:deep(.rating-stars) {
  font-size: 1.1rem;
  letter-spacing: 2px;
}

/* Modal Styles */
.modal-overlay {
  position: fixed;
  inset: 0;
  background: rgba(0, 0, 0, 0.5);
  display: flex;
  justify-content: center;
  align-items: center;
  z-index: 1000;
  backdrop-filter: blur(4px);
}

.modal {
  background: white;
  padding: 2.5rem;
  border-radius: 20px;
  text-align: center;
  width: 400px;
  max-width: 90vw;
  box-shadow: 0 20px 60px rgba(0, 0, 0, 0.2);
}

.modal-icon {
  margin-bottom: 1.5rem;
}

.modal h3 {
  margin: 0 0 1rem 0;
  color: #1a202c;
  font-size: 1.5rem;
  font-weight: 700;
}

.modal p {
  margin: 0 0 2rem 0;
  color: #718096;
  font-size: 1rem;
  line-height: 1.5;
}

.modal-buttons {
  display: flex;
  gap: 1rem;
  justify-content: center;
}

.modal-btn-cancel, .modal-btn-confirm {
  padding: 0.75rem 2rem;
  border: none;
  border-radius: 10px;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.3s ease;
  font-size: 0.95rem;
}

.modal-btn-cancel {
  background: #f7fafc;
  color: #4a5568;
  border: 2px solid #e2e8f0;
}

.modal-btn-cancel:hover {
  background: #edf2f7;
  border-color: #cbd5e0;
}

.modal-btn-confirm {
  background: #FF7828;
  color: white;
  box-shadow: 0 4px 12px rgba(255, 120, 40, 0.3);
}

.modal-btn-confirm:hover {
  background: #e6691f;
  transform: translateY(-1px);
  box-shadow: 0 6px 16px rgba(255, 120, 40, 0.4);
}


/* Admin Wallet Styles */
.wallet-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
  gap: 1rem;
  margin-bottom: 1.5rem;
}

.wallet-card,
.wallet-policy-card,
.wallet-history {
  background: #f8fafc;
  border: 1px solid #e2e8f0;
  border-radius: 14px;
  padding: 1.5rem;
}

.wallet-card span {
  display: block;
  color: #4a5568;
  font-size: 0.85rem;
  font-weight: 700;
  text-transform: uppercase;
  letter-spacing: 0.04em;
  margin-bottom: 0.5rem;
}

.wallet-card h2,
.wallet-card h3 {
  margin: 0 0 0.5rem 0;
  color: #1a202c;
}

.wallet-card p {
  margin: 0;
  color: #718096;
  font-size: 0.9rem;
}

.main-wallet {
  background: #FF7828;
  border-color: #FF7828;
  color: white;
}

.main-wallet span,
.main-wallet h2,
.main-wallet p {
  color: white;
}

.wallet-policy-card {
  margin-bottom: 1.5rem;
  background: white;
}

.wallet-policy-card h3,
.wallet-history-header h3 {
  margin: 0 0 1rem 0;
  color: #1a202c;
  font-weight: 800;
}

.wallet-policy-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
  gap: 1rem;
}

.wallet-policy-grid div {
  padding: 1rem;
  border-radius: 12px;
  background: #f8fafc;
  border: 1px solid #e2e8f0;
}

.wallet-policy-grid strong {
  display: block;
  margin-bottom: 0.4rem;
  color: #1a202c;
}

.wallet-policy-grid p {
  margin: 0;
  color: #718096;
  font-size: 0.9rem;
  line-height: 1.5;
}

.wallet-history {
  overflow-x: auto;
}

.wallet-history-header p {
  margin: -0.5rem 0 1rem 0;
  color: #718096;
}

.wallet-table {
  width: 100%;
  border-collapse: collapse;
  background: white;
  border-radius: 10px;
  overflow: hidden;
}

.wallet-table th,
.wallet-table td {
  padding: 0.9rem;
  border-bottom: 1px solid #e2e8f0;
  text-align: left;
  font-size: 0.9rem;
}

.wallet-table th {
  background: #f1f5f9;
  color: #4a5568;
  font-weight: 700;
}

.wallet-status {
  display: inline-block;
  padding: 0.25rem 0.65rem;
  border-radius: 999px;
  font-size: 0.75rem;
  font-weight: 700;
  text-transform: capitalize;
}

.wallet-status.held {
  background: rgba(251, 191, 36, 0.15);
  color: #d97706;
}

.wallet-status.distributed,
.wallet-status.credited {
  background: rgba(34, 197, 94, 0.15);
  color: #16a34a;
}

.wallet-empty {
  text-align: center;
  color: #718096;
  font-style: italic;
}

/* Responsive Design */
@media (max-width: 1024px) {
  .map-container {
    height: 500px;
  }
}

@media (max-width: 768px) {
  .admin-dashboard {
    padding: 1rem;
  }
  
  .dashboard-header {
    flex-direction: column;
    gap: 1rem;
    align-items: flex-start;
  }
  
  .header-actions {
    width: 100%;
    justify-content: flex-end;
  }
  
  .tabs {
    flex-direction: column;
  }
  
  .map-container {
    height: 400px;
  }
  
  .map-legend {
    position: relative;
    top: auto;
    right: auto;
    margin-top: 1rem;
  }
  
  .modal-buttons {
    flex-direction: column;
  }
}

@media (max-width: 480px) {
  .map-container {
    height: 350px;
  }
  
  .tab-content {
    padding: 1rem;
  }
}
</style>