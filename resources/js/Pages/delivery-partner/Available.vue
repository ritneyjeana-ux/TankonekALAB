<template>
  <div class="delivery-dashboard">
    <!-- Header -->
    <header class="dashboard-header">
      <div class="logo-section">
        <img :src="logo" alt="Tankonek Logo" class="logo" />
        <div class="portal-info">
          <h2>tankonek - <span class="highlight">Delivery Partner</span></h2>
          <p class="partner-name">Welcome, {{ partnerName }}</p>
        </div>
      </div>
      
      <div class="header-actions">
        <button class="wallet-header-btn" @click="showWalletModal = true" title="Rider Wallet">
          <svg width="16" height="16" viewBox="0 0 24 24" fill="none">
            <rect x="2" y="5" width="20" height="14" rx="2" stroke="currentColor" stroke-width="2"/>
            <path d="M16 12h4M6 9h6" stroke="currentColor" stroke-width="2"/>
          </svg>
          Wallet
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

    <!-- Overview Cards -->
    <section class="overview-section">
      <div class="overview-card">
        <div class="card-icon active-deliveries">
          <svg width="24" height="24" viewBox="0 0 24 24" fill="none">
            <path d="M9 17a2 2 0 11-4 0 2 2 0 014 0zM19 17a2 2 0 11-4 0 2 2 0 014 0z" stroke="currentColor" stroke-width="2"/>
            <path d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h1M5 17a2 2 0 104 0m-4 0a2 2 0 114 0m6 0a2 2 0 104 0m-4 0a2 2 0 114 0" stroke="currentColor" stroke-width="2"/>
          </svg>
        </div>
        <h3>Active Deliveries</h3>
        <p class="value">{{ activeDeliveries }}</p>
        <span class="subtext">In progress</span>
      </div>
      <div class="overview-card">
        <div class="card-icon available-orders">
          <svg width="24" height="24" viewBox="0 0 24 24" fill="none">
            <path d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" stroke="currentColor" stroke-width="2"/>
          </svg>
        </div>
        <h3>Available Orders</h3>
        <p class="value">{{ availableOrders }}</p>
        <span class="subtext">Ready to accept</span>
      </div>
      <div class="overview-card">
        <div class="card-icon todays-earnings">
          <svg width="24" height="24" viewBox="0 0 24 24" fill="none">
            <path d="M12 1v22M17 5H9.5a3.5 3.5 0 100 7h5a3.5 3.5 0 110 7H6" stroke="currentColor" stroke-width="2"/>
          </svg>
        </div>
        <h3>Today's Earnings</h3>
        <p class="value">₱{{ todaysEarnings }}</p>
        <span class="subtext">{{ todaysDeliveries }} deliveries</span>
      </div>
      <div class="overview-card">
        <div class="card-icon total-earnings">
          <svg width="24" height="24" viewBox="0 0 24 24" fill="none">
            <path d="M12 2a10 10 0 110 20 10 10 0 010-20z" stroke="currentColor" stroke-width="2"/>
            <path d="M12 6v12M15 9.5A3.5 3.5 0 1112 13" stroke="currentColor" stroke-width="2"/>
          </svg>
        </div>
        <h3>Total Earnings</h3>
        <p class="value">₱{{ totalEarnings }}</p>
        <span class="subtext">All time</span>
      </div>
    </section>

    <!-- Tabs - DITO NAGBAGO: ADDED REVENUE TAB -->
    <nav class="tab-navigation">
      <button
        class="tab-btn"
        :class="{ active: activeTab === 'available' }"
        @click="activeTab = 'available'"
      >
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none">
          <path d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" stroke="currentColor" stroke-width="2"/>
        </svg>
        Available ({{ deliveries.length }})
      </button>
      <button
        class="tab-btn"
        :class="{ active: activeTab === 'active' }"
        @click="activeTab = 'active'"
      >
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none">
          <path d="M9 17a2 2 0 11-4 0 2 2 0 014 0zM19 17a2 2 0 11-4 0 2 2 0 014 0z" stroke="currentColor" stroke-width="2"/>
          <path d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h1M5 17a2 2 0 104 0m-4 0a2 2 0 114 0m6 0a2 2 0 104 0m-4 0a2 2 0 114 0" stroke="currentColor" stroke-width="2"/>
        </svg>
        Active ({{ activeDeliveries }})
      </button>
      <button
        class="tab-btn"
        :class="{ active: activeTab === 'history' }"
        @click="activeTab = 'history'"
      >
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none">
          <path d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" stroke="currentColor" stroke-width="2"/>
        </svg>
        History
      </button>
      <!-- BAGONG TAB: WALLET -->
      


      <!-- BAGONG TAB: REVENUE -->
      <button
        class="tab-btn"
        :class="{ active: activeTab === 'revenue' }"
        @click="activeTab = 'revenue'"
      >
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none">
          <path d="M12 1v22M17 5H9.5a3.5 3.5 0 100 7h5a3.5 3.5 0 110 7H6" stroke="currentColor" stroke-width="2"/>
        </svg>
        Revenue
      </button>
    </nav>

    <!-- Available Deliveries Tab -->
    <section v-if="activeTab === 'available'" class="content-section">
      <div class="tab-content">
        <div class="content-header">
          <h2>Available Delivery Requests</h2>
          <p>Accept deliveries to start earning</p>
        </div>

        <!-- Loading State -->
        <div v-if="loading" class="loading-state">
          <svg width="48" height="48" viewBox="0 0 24 24" fill="none" class="loading-spinner">
            <path d="M12 2v4m0 12v4M4.93 4.93l2.83 2.83m8.48 8.48l2.83 2.83M2 12h4m12 0h4M4.93 19.07l2.83-2.83m8.48-8.48l2.83-2.83" stroke="#FF7828" stroke-width="2" stroke-linecap="round"/>
          </svg>
          <p>Loading available deliveries...</p>
        </div>

        <!-- No Deliveries State -->
        <div v-else-if="deliveries.length === 0" class="no-deliveries">
          <svg width="64" height="64" viewBox="0 0 24 24" fill="none">
            <path d="M9 17a2 2 0 11-4 0 2 2 0 014 0zM19 17a2 2 0 11-4 0 2 2 0 014 0z" stroke="#CBD5E0" stroke-width="2"/>
            <path d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h1M5 17a2 2 0 104 0m-4 0a2 2 0 114 0m6 0a2 2 0 104 0m-4 0a2 2 0 114 0" stroke="#CBD5E0" stroke-width="2"/>
          </svg>
          <h3>No available deliveries</h3>
          <p>New delivery requests will appear here when retailers accept orders</p>
        </div>

        <!-- Deliveries Grid -->
        <div v-else class="deliveries-grid">
          <div v-for="delivery in deliveries" :key="delivery.id" class="delivery-card">
            <div class="delivery-main">
              <div class="delivery-header">
                <div class="order-info">
                  <strong class="order-id">{{ delivery.displayId }}</strong>
                  <span class="payment-badge" :class="delivery.paymentMethod">
                    {{ delivery.paymentMethod === 'cash' ? 'COD' : delivery.paymentMethod }}
                  </span>
                </div>
                <span class="earning-badge">₱{{ delivery.fee }}</span>
              </div>

              <div class="delivery-info">
                <p class="distance">
                  <i class="fas fa-map-marker-alt"></i> {{ delivery.distance }} km away
                </p>
                <p class="item"><strong>{{ delivery.item }}</strong></p>
                <div class="location-details">
                  <div class="location">
                    <strong>From:</strong> {{ delivery.from }}
                  </div>
                  <div class="location">
                    <strong>To:</strong> {{ delivery.to }}
                  </div>
                </div>
                <p class="address">{{ delivery.address }}</p>
                
                <div class="delivery-meta">
                  <span class="meta-item">
                    <strong>Total:</strong> ₱{{ delivery.totalAmount }}
                  </span>
                  <span class="meta-item">
                    <strong>Prep Time:</strong> {{ delivery.estimatedPrepTime }}min
                  </span>
                </div>
                
                <p v-if="delivery.specialInstructions" class="instructions">
                  <strong>Instructions:</strong> {{ delivery.specialInstructions }}
                </p>
              </div>
            </div>

            <div class="delivery-actions">
              <button 
                class="accept-btn" 
                @click="acceptDelivery(delivery.id, $event)"
                :disabled="acceptingDeliveryId === delivery.id"
              >
                <span v-if="acceptingDeliveryId === delivery.id">Accepting...</span>
                <span v-else>Accept Delivery</span>
              </button>
              <button class="view-btn" @click="viewDetails(delivery)">
                View Details
              </button>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- Active Deliveries Tab -->
    <Active v-else-if="activeTab === 'active'" />

    <!-- History Tab -->
    <History v-else-if="activeTab === 'history'" />

    <!-- Revenue Tab - BAGONG COMPONENT -->
    <Revenue v-else-if="activeTab === 'revenue'" />

    <!-- Rider Wallet Modal -->
    <div v-if="showWalletModal" class="modal-overlay">
      <div class="rider-wallet-modal">
        <div class="wallet-modal-header">
          <div>
            <h2>Rider E-Wallet</h2>
            <p>Your ₱10 delivery earnings will appear here after completed deliveries.</p>
          </div>
          <button class="close-btn" @click="showWalletModal = false">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none">
              <path d="M18 6L6 18M6 6l12 12" stroke="currentColor" stroke-width="2"/>
            </svg>
          </button>
        </div>

        <div class="wallet-modal-content">
          <div v-if="walletLoading" class="loading-state">
            <svg width="48" height="48" viewBox="0 0 24 24" fill="none" class="loading-spinner">
              <path d="M12 2v4m0 12v4M4.93 4.93l2.83 2.83m8.48 8.48l2.83 2.83M2 12h4m12 0h4M4.93 19.07l2.83-2.83m8.48-8.48l2.83-2.83" stroke="#FF7828" stroke-width="2" stroke-linecap="round"/>
            </svg>
            <p>Loading wallet data...</p>
          </div>

          <div v-else>
            <div class="wallet-grid">
              <div class="wallet-card main-wallet">
                <span>Available Balance</span>
                <h2>₱{{ walletBalance.toLocaleString() }}</h2>
                <p>Ready for GCash withdrawal</p>
              </div>

              <div class="wallet-card">
                <span>Pending Balance</span>
                <h3>₱{{ pendingBalance.toLocaleString() }}</h3>
                <p>Processing or unconfirmed earnings</p>
              </div>

              <div class="wallet-card">
                <span>Total Withdrawn</span>
                <h3>₱{{ totalWithdrawn.toLocaleString() }}</h3>
                <p>Successful GCash withdrawals</p>
              </div>
            </div>

            <div class="wallet-info-card">
              <h3>How Rider Wallet Works</h3>
              <p>
                Once a delivery is completed and confirmed, your rider share is credited here.
                In this system, the rider receives ₱10 from the delivery fee, while withdrawal requests are recorded through GCash.
              </p>
            </div>

            <div class="gcash-card">
              <div class="gcash-header">
                <div>
                  <h3>Withdraw via GCash</h3>
                  <p>Enter your GCash number and withdrawal amount.</p>
                </div>
                <span class="gcash-badge">GCash</span>
              </div>

              <div class="gcash-form">
                <div class="gcash-input-group">
                  <label>GCash Number</label>
                  <input
                    v-model="gcashNumber"
                    type="text"
                    maxlength="11"
                    placeholder="09XXXXXXXXX"
                  />
                </div>

                <div class="gcash-input-group">
                  <label>Amount</label>
                  <input
                    v-model="withdrawAmount"
                    type="number"
                    min="1"
                    placeholder="Enter amount"
                  />
                </div>

                <button
                  class="withdraw-btn"
                  @click="withdrawToGcash"
                  :disabled="walletProcessing"
                >
                  {{ walletProcessing ? 'Processing...' : 'Withdraw to GCash' }}
                </button>
              </div>
            </div>

            <div class="wallet-history">
              <div class="wallet-history-header">
                <h3>Wallet Transactions</h3>
                <p>Recent delivery credits and withdrawal requests</p>
              </div>

              <table class="wallet-table">
                <thead>
                  <tr>
                    <th>Date</th>
                    <th>Type</th>
                    <th>Amount</th>
                    <th>Status</th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-for="transaction in walletTransactions" :key="transaction.id">
                    <td>{{ formatWalletDate(transaction.createdAt) }}</td>
                    <td>{{ transaction.type }}</td>
                    <td>₱{{ Number(transaction.amount || 0).toLocaleString() }}</td>
                    <td>
                      <span class="wallet-status" :class="transaction.status">
                        {{ transaction.status }}
                      </span>
                    </td>
                  </tr>
                  <tr v-if="walletTransactions.length === 0">
                    <td colspan="4" class="wallet-empty">No wallet transactions yet</td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- View Details Modal -->
    <div v-if="showDetailsModal" class="modal-overlay">
      <div class="modal details-modal">
        <div class="modal-header">
          <h3>Delivery Details - {{ selectedDelivery?.displayId }}</h3>
          <button class="close-btn" @click="closeDetailsModal">×</button>
        </div>
        
        <div class="modal-content">
          <!-- Real-time Location Updates -->
          <div class="location-update-section">
            <div class="location-update-header">
              <h4>Real-time Location Tracking</h4>
              <button 
                class="refresh-btn" 
                @click="refreshDistances"
                :disabled="refreshingDistances"
              >
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none">
                  <path d="M23 4v6h-6M1 20v-6h6" stroke="currentColor" stroke-width="2"/>
                  <path d="M3.51 9a9 9 0 0114.85-3.36L23 10M1 14l4.64 4.36A9 9 0 0020.49 15" stroke="currentColor" stroke-width="2"/>
                </svg>
                {{ refreshingDistances ? 'Updating...' : 'Refresh Distances' }}
              </button>
            </div>
            <p class="location-update-info">
              Last updated: {{ lastLocationUpdate }}
            </p>
          </div>

          <!-- Map Section -->
          <div class="map-section">
            <h4>Shop to Customer Distance</h4>
            <div class="map-container">
              <div class="distance-cards">
                <div class="distance-card">
                  <div class="distance-icon retailer-to-customer">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none">
                      <path d="M12 2a8 8 0 00-8 8c0 6 8 12 8 12s8-6 8-12a8 8 0 00-8-8z" stroke="currentColor" stroke-width="2"/>
                      <circle cx="12" cy="10" r="3" stroke="currentColor" stroke-width="2"/>
                    </svg>
                  </div>
                  <div class="distance-info">
                    <strong>Shop to Customer</strong>
                    <p>{{ retailerToCustomerDistance }} km</p>
                  </div>
                </div>
                
              </div>
              
              <!-- Simplified Map Visualization -->
              <div class="route-visualization">
                <div class="route-points">
                  <div class="point retailer-point">
                    <div class="point-icon">
                      <svg width="20" height="20" viewBox="0 0 24 24" fill="none">
                        <path d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" stroke="currentColor" stroke-width="2"/>
                        <path d="M9 22V12h6v10" stroke="currentColor" stroke-width="2"/>
                      </svg>
                    </div>
                    <div class="point-label">{{ selectedDelivery?.from }}</div>
                    <div class="point-coordinates">
                      {{ retailerLocation.latitude.toFixed(6) }}, {{ retailerLocation.longitude.toFixed(6) }}
                    </div>
                  </div>
                  <div class="route-line"></div>
                  <div class="point customer-point">
                    <div class="point-icon">
                      <svg width="20" height="20" viewBox="0 0 24 24" fill="none">
                        <path d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" stroke="currentColor" stroke-width="2"/>
                      </svg>
                    </div>
                    <div class="point-label">{{ selectedDelivery?.to }}</div>
                    <div class="point-coordinates">
                      {{ customerLocation.latitude.toFixed(6) }}, {{ customerLocation.longitude.toFixed(6) }}
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Delivery Fee Calculation -->
          <div class="fee-calculation">
            <h4>Delivery Fee Breakdown</h4>
            <div class="fee-breakdown">
              <div class="fee-item">
                <span>Base Fee:</span>
                <span>₱{{ baseFee }}</span>
              </div>
              <div class="fee-item">
                <span>Distance Charge - Shop to Customer ({{ totalDistance }} km × ₱{{ distanceRate }}/km):</span>
                <span>₱{{ distanceCharge }}</span>
              </div>
              <div class="fee-item total-fee">
                <strong>Total Delivery Fee:</strong>
                <strong>₱{{ calculatedDeliveryFee }}</strong>
              </div>
            </div>
          </div>

          <!-- Order Details -->
          <div class="order-details-section">
            <h4>Order Information</h4>
            <div class="details-grid">
              <div class="detail-item">
                <strong>Product:</strong>
                <span>{{ selectedDelivery?.item }}</span>
              </div>
              <div class="detail-item">
                <strong>Quantity:</strong>
                <span>{{ selectedDelivery?.quantity || 1 }}</span>
              </div>
              <div class="detail-item">
                <strong>Total Amount:</strong>
                <span>₱{{ selectedDelivery?.totalAmount }}</span>
              </div>
              <div class="detail-item">
                <strong>Payment Method:</strong>
                <span>{{ selectedDelivery?.paymentMethod === 'cash' ? 'COD' : selectedDelivery?.paymentMethod }}</span>
              </div>
            </div>
          </div>

          <!-- Location Details -->
          <div class="location-details-section">
            <h4>Location Details</h4>
            <div class="location-cards">
              <div class="location-card">
                <h5>Pickup Location</h5>
                <p><strong>{{ selectedDelivery?.from }}</strong></p>
                <p>{{ selectedDelivery?.pickupAddress || 'Store Location' }}</p>
              </div>
              <div class="location-card">
                <h5>Delivery Location</h5>
                <p><strong>{{ selectedDelivery?.to }}</strong></p>
                <p>{{ selectedDelivery?.address }}</p>
              </div>
            </div>
          </div>

          <!-- Contact Information -->
          <div class="contact-section" v-if="selectedDelivery?.customerPhone">
            <h4>Contact Information</h4>
            <div class="contact-info">
              <p><strong>Customer:</strong> {{ selectedDelivery?.customerName || selectedDelivery?.to }}</p>
              <p><strong>Phone:</strong> {{ selectedDelivery?.customerPhone }}</p>
              <p v-if="selectedDelivery?.customerEmail"><strong>Email:</strong> {{ selectedDelivery?.customerEmail }}</p>
            </div>
          </div>

          <!-- Special Instructions -->
          <div class="instructions-section" v-if="selectedDelivery?.specialInstructions">
            <h4>Special Instructions</h4>
            <p>{{ selectedDelivery?.specialInstructions }}</p>
          </div>
        </div>

        <div class="modal-actions">
          <button class="modal-btn-cancel" @click="closeDetailsModal">Close</button>
          <button 
            class="modal-btn-confirm" 
            @click="acceptDelivery(selectedDelivery?.id, $event)"
            :disabled="acceptingDeliveryId === selectedDelivery?.id"
          >
            <span v-if="acceptingDeliveryId === selectedDelivery?.id">Accepting...</span>
            <span v-else>Accept Delivery</span>
          </button>
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
import { ref, onMounted, onUnmounted, computed } from "vue";
import { router } from "@inertiajs/vue3";
import Active from "./Active.vue";
import History from "./History.vue";
import Revenue from "./Revenue.vue"; // BAGONG IMPORT: Revenue component

// FIREBASE
import { db, auth } from "@/firebase";
import { 
  collection, 
  query, 
  where, 
  onSnapshot,
  orderBy,
  updateDoc,
  doc,
  getDocs,
  getDoc,
  serverTimestamp,
  writeBatch,
  runTransaction,
  addDoc,
  increment
} from "firebase/firestore";
import { onAuthStateChanged } from "firebase/auth";

const logo = '/images/logo.png';

const activeTab = ref("available");
const partnerName = ref("");
const activeDeliveries = ref(0);
const availableOrders = ref(0);
const todaysEarnings = ref(0);
const totalEarnings = ref(0);
const todaysDeliveries = ref(0);

// Deliveries data from Firebase
const deliveries = ref([]);
const loading = ref(true);

// View Details Modal
const showDetailsModal = ref(false);
const selectedDelivery = ref(null);

// Real-time location tracking
const currentRiderLocation = ref({ latitude: 0, longitude: 0 });
const customerLocation = ref({ latitude: 0, longitude: 0 });
const retailerLocation = ref({ latitude: 0, longitude: 0 });
const lastLocationUpdate = ref('Just now');
const refreshingDistances = ref(false);

// Distance calculations
const riderToRetailerDistance = ref(0); // kept to avoid breaking existing refs, but not displayed/computed
const retailerToCustomerDistance = ref(0);
const totalDistance = ref(0);

// Delivery fee calculation
const baseFee = ref(40); // Base delivery fee
const distanceRate = ref(15); // Rate per km

// Accept delivery state
const acceptingDeliveryId = ref(null);

// Rider wallet state
const walletBalance = ref(0);
const pendingBalance = ref(0);
const totalWithdrawn = ref(0);
const gcashNumber = ref("");
const withdrawAmount = ref("");
const walletTransactions = ref([]);
const walletLoading = ref(false);
const walletProcessing = ref(false);
const creditedDeliveryIds = ref(new Set());
const showWalletModal = ref(false);


// Computed delivery fee based on distance
const calculatedDeliveryFee = computed(() => {
  return baseFee.value + (totalDistance.value * distanceRate.value);
});

const distanceCharge = computed(() => {
  return totalDistance.value * distanceRate.value;
});

// Calculate distance using Haversine formula
const calculateDistance = (lat1, lon1, lat2, lon2) => {
  const R = 6371; // Earth's radius in kilometers
  const dLat = (lat2 - lat1) * Math.PI / 180;
  const dLon = (lon2 - lon1) * Math.PI / 180;
  
  const a = 
    Math.sin(dLat/2) * Math.sin(dLat/2) +
    Math.cos(lat1 * Math.PI / 180) * Math.cos(lat2 * Math.PI / 180) * 
    Math.sin(dLon/2) * Math.sin(dLon/2);
  
  const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1-a));
  const distance = R * c;
  
  return Math.round(distance * 10) / 10; // Round to 1 decimal place
};

// Get rider's current location in real-time
const getRiderLocation = () => {
  return new Promise((resolve, reject) => {
    if (!navigator.geolocation) {
      reject(new Error('Geolocation is not supported'));
      return;
    }

    navigator.geolocation.getCurrentPosition(
      (position) => {
        const location = {
          latitude: position.coords.latitude,
          longitude: position.coords.longitude
        };
        currentRiderLocation.value = location;
        resolve(location);
      },
      (error) => {
        console.error('Error getting rider location:', error);
        // Fallback to a default location in Victoria, Oriental Mindoro
        const defaultLocation = {
          latitude: 13.1775,
          longitude: 121.2770
        };
        currentRiderLocation.value = defaultLocation;
        resolve(defaultLocation);
      },
      {
        enableHighAccuracy: true,
        timeout: 10000,
        maximumAge: 0 // Always get fresh location
      }
    );
  });
};

// Watch rider location in real-time
let locationWatchId = null;
const startLocationTracking = () => {
  if (!navigator.geolocation) return;

  locationWatchId = navigator.geolocation.watchPosition(
    (position) => {
      currentRiderLocation.value = {
        latitude: position.coords.latitude,
        longitude: position.coords.longitude
      };
      lastLocationUpdate.value = new Date().toLocaleTimeString();
      
      // Auto-update distances if modal is open
      if (showDetailsModal.value && selectedDelivery.value) {
        calculateAllDistances();
      }
    },
    (error) => {
      console.error('Error watching rider location:', error);
    },
    {
      enableHighAccuracy: true,
      timeout: 10000,
      maximumAge: 5000 // Update every 5 seconds
    }
  );
};

// Stop location tracking
const stopLocationTracking = () => {
  if (locationWatchId) {
    navigator.geolocation.clearWatch(locationWatchId);
    locationWatchId = null;
  }
};

// Calculate distance for shop/retailer to customer only
const calculateAllDistances = async () => {
  if (!selectedDelivery.value) return;

  try {
    // Only compute the distance from shop/retailer to customer
    retailerToCustomerDistance.value = calculateDistance(
      retailerLocation.value.latitude,
      retailerLocation.value.longitude,
      customerLocation.value.latitude,
      customerLocation.value.longitude
    );
    
    // Total distance is now the same as shop/retailer to customer distance
    totalDistance.value = retailerToCustomerDistance.value;
    riderToRetailerDistance.value = 0;
    
  } catch (error) {
    console.error('Error calculating distance:', error);
  }
};

// Refresh distances manually
const refreshDistances = async () => {
  refreshingDistances.value = true;
  
  try {
    await calculateAllDistances();
    lastLocationUpdate.value = new Date().toLocaleTimeString();
  } catch (error) {
    console.error('Error refreshing distances:', error);
  } finally {
    refreshingDistances.value = false;
  }
};

// View delivery details with shop-to-customer distance calculation
const viewDetails = async (delivery) => {
  selectedDelivery.value = delivery;
  
  try {
    // Get retailer location from retailers collection in real-time
    const retailersQuery = query(
      collection(db, "retailers"),
      where("businessName", "==", delivery.from)
    );
    
    const retailersSnapshot = await getDocs(retailersQuery);
    if (!retailersSnapshot.empty) {
      const retailerData = retailersSnapshot.docs[0].data();
      retailerLocation.value = {
        latitude: parseFloat(retailerData.latitude) || 13.164904,
        longitude: parseFloat(retailerData.longitude) || 121.280513
      };
    } else {
      // Default retailer location (Ely Gas)
      retailerLocation.value = {
        latitude: 13.164904,
        longitude: 121.280513
      };
    }
    
    // Get customer location from customers collection in real-time
    const customersQuery = query(
      collection(db, "customers"),
      where("fullName", "==", delivery.to)
    );
    
    const customersSnapshot = await getDocs(customersQuery);
    if (!customersSnapshot.empty) {
      const customerData = customersSnapshot.docs[0].data();
      customerLocation.value = {
        latitude: parseFloat(customerData.latitude) || 13.152945,
        longitude: parseFloat(customerData.longitude) || 121.271435
      };
    } else {
      // Default customer location
      customerLocation.value = {
        latitude: 13.152945,
        longitude: 121.271435
      };
    }
    
    // Calculate initial distances
    await calculateAllDistances();
    
    // Show the modal
    showDetailsModal.value = true;
    
  } catch (error) {
    console.error('Error setting up delivery details:', error);
    
    // Fallback distance: shop/retailer to customer only
    riderToRetailerDistance.value = 0;
    retailerToCustomerDistance.value = 2.3;
    totalDistance.value = 2.3;
    
    showDetailsModal.value = true;
  }
};

// Close details modal
const closeDetailsModal = () => {
  showDetailsModal.value = false;
  selectedDelivery.value = null;
  stopLocationTracking();
};

// Fetch available deliveries from Firebase
const fetchAvailableDeliveries = () => {
  const user = auth.currentUser;
  if (!user) {
    console.log("No rider logged in");
    loading.value = false;
    return;
  }

  console.log("Fetching available deliveries for rider:", user.uid);

  // Get rider data first
  const getRiderData = async () => {
    try {
      const riderDoc = doc(db, "riders", user.uid);
      const riderSnapshot = await getDoc(riderDoc);
      const riderData = riderSnapshot.data();
      
      if (riderData) {
        partnerName.value = riderData.fullName || "Delivery Partner";
        
        const deliveriesQuery = query(
          collection(db, "deliveries"),
          where("status", "==", "available")
        );

        const unsubscribe = onSnapshot(deliveriesQuery, 
          (snapshot) => {
            const deliveriesList = [];
            snapshot.forEach((doc) => {
              const deliveryData = doc.data();
              
              deliveriesList.push({
                id: doc.id,
                displayId: deliveryData.displayId || deliveryData.orderId || `ORD-${doc.id.slice(-4)}`,
                fee: deliveryData.deliveryFee || 50,
                distance: deliveryData.distance || 1.2,
                item: deliveryData.item || `${deliveryData.gasBrand || 'LPG'} ${deliveryData.gasType || 'Tank'} × ${deliveryData.quantity || 1}`,
                from: deliveryData.from || deliveryData.retailerName || "Store",
                to: deliveryData.to || deliveryData.customerName || "Customer",
                address: deliveryData.deliveryAddress || deliveryData.customerAddress || "Address not specified",
                orderId: deliveryData.orderId,
                customerPhone: deliveryData.customerPhone,
                customerEmail: deliveryData.customerEmail,
                customerName: deliveryData.customerName,
                specialInstructions: deliveryData.specialInstructions,
                estimatedPrepTime: deliveryData.estimatedPrepTime || 15,
                pickupAddress: deliveryData.pickupAddress,
                gasBrand: deliveryData.gasBrand,
                gasType: deliveryData.gasType,
                quantity: deliveryData.quantity,
                totalAmount: deliveryData.totalAmount,
                paymentMethod: deliveryData.paymentMethod,
                createdAt: deliveryData.createdAt?.toDate?.() || new Date(),
                ...deliveryData
              });
            });
            
            deliveriesList.sort((a, b) => new Date(b.createdAt) - new Date(a.createdAt));
            
            deliveries.value = deliveriesList;
            availableOrders.value = deliveriesList.length;
            loading.value = false;
            
            console.log("Available deliveries loaded:", deliveriesList.length);
          },
          (error) => {
            console.error("Error fetching available deliveries:", error);
            loading.value = false;
          }
        );

        return unsubscribe;
      }
    } catch (error) {
      console.error("Error fetching rider data:", error);
      loading.value = false;
    }
  };

  getRiderData();
};

// Also fetch active deliveries count
const fetchActiveDeliveriesCount = () => {
  const user = auth.currentUser;
  if (!user) return;

  const activeQuery = query(
    collection(db, "deliveries"),
    where("riderId", "==", user.uid),
    where("status", "==", "accepted")
  );

  const inTransitQuery = query(
    collection(db, "deliveries"),
    where("riderId", "==", user.uid),
    where("status", "==", "in-transit")
  );

  const unsubscribe1 = onSnapshot(activeQuery, 
    (snapshot) => {
      const acceptedCount = snapshot.size;
      
      const unsubscribe2 = onSnapshot(inTransitQuery, 
        (transitSnapshot) => {
          const transitCount = transitSnapshot.size;
          activeDeliveries.value = acceptedCount + transitCount;
          
          calculateTodaysEarnings(user.uid);
        },
        (error) => {
          console.error("Error fetching in-transit deliveries:", error);
          activeDeliveries.value = acceptedCount;
          calculateTodaysEarnings(user.uid);
        }
      );
      
      return unsubscribe2;
    },
    (error) => {
      console.error("Error fetching accepted deliveries:", error);
    }
  );

  return unsubscribe1;
};

// Calculate today's earnings
const calculateTodaysEarnings = async (riderId) => {
  try {
    const today = new Date();
    today.setHours(0, 0, 0, 0);
    
    const earningsQuery = query(
      collection(db, "deliveries"),
      where("riderId", "==", riderId),
      where("status", "==", "delivered")
    );

    const snapshot = await getDocs(earningsQuery);
    let todayTotal = 0;
    let todayCount = 0;

    snapshot.forEach((doc) => {
      const delivery = doc.data();
      const deliveredAt = delivery.deliveredAt?.toDate?.() || new Date();
      
      if (deliveredAt >= today) {
        todayTotal += delivery.deliveryFee || 0;
        todayCount++;
      }
    });

    todaysEarnings.value = todayTotal;
    todaysDeliveries.value = todayCount;
    
    calculateTotalEarnings(riderId);
    
  } catch (error) {
    console.error("Error calculating earnings:", error);
  }
};

// Calculate total earnings
const calculateTotalEarnings = async (riderId) => {
  try {
    const earningsQuery = query(
      collection(db, "deliveries"),
      where("riderId", "==", riderId),
      where("status", "==", "delivered")
    );

    const snapshot = await getDocs(earningsQuery);
    let total = 0;

    snapshot.forEach((doc) => {
      const delivery = doc.data();
      total += delivery.deliveryFee || 0;
    });

    totalEarnings.value = total;
  } catch (error) {
    console.error("Error calculating total earnings:", error);
  }
};

// Accept delivery function - ENHANCED with multiple layers of race condition protection
const acceptDelivery = async (deliveryId, event) => {
  // Prevent multiple clicks
  if (acceptingDeliveryId.value === deliveryId) {
    return;
  }

  try {
    const user = auth.currentUser;
    if (!user) {
      alert("Please login first");
      return;
    }

    // Set accepting state immediately
    acceptingDeliveryId.value = deliveryId;

    const deliveryRef = doc(db, "deliveries", deliveryId);
    
    // LAYER 1: Quick optimistic check
    const quickCheckDoc = await getDoc(deliveryRef);
    if (!quickCheckDoc.exists()) {
      alert("Delivery not found");
      acceptingDeliveryId.value = null;
      return;
    }

    const quickCheckData = quickCheckDoc.data();
    if (quickCheckData.status !== 'available') {
      // Dapat may lalabas din na alert message sa rider na hindi na yun available
      alert(`❌ This delivery is no longer available. It has already been accepted by ${quickCheckData.riderName || 'another rider'}.`);
      // Remove from local list
      deliveries.value = deliveries.value.filter(d => d.id !== deliveryId);
      availableOrders.value = deliveries.value.length;
      closeDetailsModal();
      acceptingDeliveryId.value = null;
      return;
    }

    // LAYER 2: Firestore Transaction for atomic operation
    const result = await runTransaction(db, async (transaction) => {
      // Get the latest delivery data within the transaction
      const deliveryDoc = await transaction.get(deliveryRef);
      if (!deliveryDoc.exists()) {
        throw new Error("Delivery not found");
      }

      const deliveryData = deliveryDoc.data();
      
      // Double-check if delivery is still available within the transaction
      if (deliveryData.status !== 'available') {
        throw new Error("ALREADY_ACCEPTED");
      }

      const riderDoc = doc(db, "riders", user.uid);
      const riderSnapshot = await transaction.get(riderDoc);
      const riderData = riderSnapshot.data();
      
      const riderFullName = riderData?.fullName || "Delivery Partner";

      // Update delivery status within the transaction
      transaction.update(deliveryRef, {
        status: 'accepted',
        riderId: user.uid,
        riderName: riderFullName,
        acceptedAt: serverTimestamp(),
        updatedAt: serverTimestamp()
      });

      // Also update the order status in orders collection if orderId exists
      if (deliveryData.orderId) {
        const ordersQuery = query(
          collection(db, "orders"),
          where("orderNumber", "==", deliveryData.orderId)
        );
        const ordersSnapshot = await getDocs(ordersQuery);
        
        if (!ordersSnapshot.empty) {
          const orderDoc = ordersSnapshot.docs[0];
          transaction.update(doc(db, "orders", orderDoc.id), {
            orderStatus: 'assigned',
            deliveryPerson: riderFullName,
            riderId: user.uid,
            updatedAt: serverTimestamp()
          });
        }
      }

      return { 
        success: true, 
        deliveryId, 
        displayId: deliveryData.displayId,
        riderName: riderFullName 
      };
    });

    // If transaction succeeds, show success message
    console.log("🎉 Delivery accepted:", deliveryId);
    
    // Remove from local deliveries list immediately
    deliveries.value = deliveries.value.filter(d => d.id !== deliveryId);
    availableOrders.value = deliveries.value.length;
    activeDeliveries.value += 1;
    
    closeDetailsModal();
    
    alert(`✅ Delivery ${result.displayId} accepted successfully!\n\nCustomer will now see you as their delivery person.`);
    
    activeTab.value = 'active';
    
  } catch (error) {
    console.error("❌ Error accepting delivery:", error);
    
    if (error.message === "ALREADY_ACCEPTED") {
      // Get the latest data to show who accepted it
      try {
        const deliveryDoc = await getDoc(doc(db, "deliveries", deliveryId));
        if (deliveryDoc.exists()) {
          const deliveryData = deliveryDoc.data();
          // Dapat may lalabas din na alert message sa rider na hindi na yun available
          alert(`❌ This delivery is no longer available. It has already been accepted by ${deliveryData.riderName || 'another rider'}.`);
        } else {
          alert("❌ This delivery is no longer available. It has already been accepted by another rider.");
        }
      } catch (e) {
        alert("❌ This delivery is no longer available. It has already been accepted by another rider.");
      }
      
      // Remove from local list
      deliveries.value = deliveries.value.filter(d => d.id !== deliveryId);
      availableOrders.value = deliveries.value.length;
      closeDetailsModal();
      
    } else if (error.code === 'failed-precondition') {
      alert("❌ This delivery is no longer available. It has already been accepted by another rider. Please refresh the page.");
    } else {
      alert("❌ Failed to accept delivery. Please try again.");
    }
    
    // Refresh the available deliveries list
    fetchAvailableDeliveries();
  } finally {
    // Always reset accepting state
    acceptingDeliveryId.value = null;
  }
};

// Credit rider wallet when a delivery is completed
const creditRiderWalletOnDelivered = async (delivery) => {
  const user = auth.currentUser;
  if (!user || !delivery?.id) return;

  if (creditedDeliveryIds.value.has(delivery.id) || delivery.riderWalletStatus === "credited") {
    return;
  }

  const deliveryStatus = delivery.status || delivery.orderStatus;
  const isDelivered = deliveryStatus === "delivered" || deliveryStatus === "completed";

  if (!isDelivered) {
    return;
  }

  const deliveryFee = Number(delivery.deliveryFee || delivery.fee || 0);

// 67% kay rider
const riderShare = Number((deliveryFee * 0.67).toFixed(2));

// 33% kay admin/platform
const platformShare = Number((deliveryFee * 0.33).toFixed(2));

  if (riderShare <= 0) {
    return;
  }

  const deliveryRef = doc(db, "deliveries", delivery.id);
  const riderWalletRef = doc(db, "rider_wallets", user.uid);
  const orderNumber = delivery.orderId || delivery.displayId || delivery.orderNumber || delivery.id;

  creditedDeliveryIds.value.add(delivery.id);

  try {
    await runTransaction(db, async (transaction) => {
      const latestDelivery = await transaction.get(deliveryRef);

      if (!latestDelivery.exists()) {
        throw new Error("DELIVERY_NOT_FOUND");
      }

      const latestDeliveryData = latestDelivery.data();

      if (latestDeliveryData.riderWalletStatus === "credited") {
        return;
      }

      transaction.set(riderWalletRef, {
        availableBalance: increment(riderShare),
        totalEarnings: increment(riderShare),
        updatedAt: serverTimestamp()
      }, { merge: true });

      transaction.update(deliveryRef, {
        riderWalletStatus: "credited",
        riderShare,
        platformShare,
        riderWalletCreditedAt: serverTimestamp()
      });
    });

    await addDoc(collection(db, "rider_wallet_transactions"), {
      riderId: user.uid,
      deliveryId: delivery.id,
      orderNumber,
      type: "Delivery Earnings",
      description: `Rider share credited from delivery fee for ${orderNumber}`,
      amount: riderShare,
      deliveryFee,
      platformShare,
      status: "credited",
      createdAt: serverTimestamp()
    });

    console.log("✅ Rider wallet credited:", riderShare, "from delivery fee:", deliveryFee);
  } catch (error) {
    console.error("Error crediting rider wallet:", error);
    creditedDeliveryIds.value.delete(delivery.id);
  }
};

// Listen for completed deliveries and credit rider earnings automatically
const watchRiderCompletedDeliveries = (riderId) => {
  const completedQuery = query(
    collection(db, "deliveries"),
    where("riderId", "==", riderId),
    where("status", "==", "delivered")
  );

  return onSnapshot(completedQuery, (snapshot) => {
    snapshot.forEach((deliveryDoc) => {
      creditRiderWalletOnDelivered({
        id: deliveryDoc.id,
        ...deliveryDoc.data()
      });
    });
  }, (error) => {
    console.error("Error watching completed deliveries:", error);
  });
};

// Fetch rider wallet data in real-time
let unsubscribeRiderWallet = null;
let unsubscribeRiderWalletTransactions = null;

const fetchRiderWallet = (riderId) => {
  walletLoading.value = true;

  try {
    if (unsubscribeRiderWallet) {
      unsubscribeRiderWallet();
    }

    if (unsubscribeRiderWalletTransactions) {
      unsubscribeRiderWalletTransactions();
    }

    const walletRef = doc(db, "rider_wallets", riderId);

    unsubscribeRiderWallet = onSnapshot(walletRef, (walletSnapshot) => {
      if (walletSnapshot.exists()) {
        const walletData = walletSnapshot.data();

        walletBalance.value = Number(walletData.availableBalance || 0);
        pendingBalance.value = Number(walletData.pendingBalance || 0);
        totalWithdrawn.value = Number(walletData.totalWithdrawn || 0);
        gcashNumber.value = walletData.gcashNumber || "";
      } else {
        walletBalance.value = 0;
        pendingBalance.value = 0;
        totalWithdrawn.value = 0;
        gcashNumber.value = "";
      }

      walletLoading.value = false;
    }, (error) => {
      console.error("Error fetching rider wallet:", error);
      walletLoading.value = false;
    });

    const transactionsQuery = query(
      collection(db, "rider_wallet_transactions"),
      where("riderId", "==", riderId)
    );

    unsubscribeRiderWalletTransactions = onSnapshot(transactionsQuery, (snapshot) => {
      const transactionsList = [];

      snapshot.forEach((transactionDoc) => {
        transactionsList.push({
          id: transactionDoc.id,
          ...transactionDoc.data()
        });
      });

      transactionsList.sort((a, b) => {
        const dateA = a.createdAt?.toDate?.() || new Date(a.createdAt || 0);
        const dateB = b.createdAt?.toDate?.() || new Date(b.createdAt || 0);
        return dateB - dateA;
      });

      walletTransactions.value = transactionsList;
    }, (error) => {
      console.error("Error fetching rider wallet transactions:", error);
    });
  } catch (error) {
    console.error("Error setting up rider wallet listeners:", error);
    walletLoading.value = false;
    alert("Failed to load wallet data.");
  }
};

// Format wallet transaction date
const formatWalletDate = (date) => {
  if (!date) return "N/A";

  try {
    const formattedDate = date.toDate ? date.toDate() : new Date(date);
    return formattedDate.toLocaleDateString("en-PH", {
      month: "short",
      day: "numeric",
      year: "numeric",
      hour: "2-digit",
      minute: "2-digit"
    });
  } catch (error) {
    return "Invalid Date";
  }
};

// Withdraw rider earnings to GCash
const withdrawToGcash = async () => {
  const user = auth.currentUser;
  const amount = Number(withdrawAmount.value);

  if (!user) {
    alert("Please login first.");
    return;
  }

  if (!gcashNumber.value || !/^09\d{9}$/.test(gcashNumber.value)) {
    alert("Please enter a valid 11-digit GCash number starting with 09.");
    return;
  }

  if (!amount || amount <= 0) {
    alert("Please enter a valid withdrawal amount.");
    return;
  }

  if (amount > walletBalance.value) {
    alert("Insufficient wallet balance.");
    return;
  }

  walletProcessing.value = true;

  try {
    const walletRef = doc(db, "rider_wallets", user.uid);

    await runTransaction(db, async (transaction) => {
      const walletSnapshot = await transaction.get(walletRef);

      if (!walletSnapshot.exists()) {
        throw new Error("WALLET_NOT_FOUND");
      }

      const walletData = walletSnapshot.data();
      const currentBalance = Number(walletData.availableBalance || 0);

      if (amount > currentBalance) {
        throw new Error("INSUFFICIENT_BALANCE");
      }

      transaction.update(walletRef, {
        availableBalance: increment(-amount),
        totalWithdrawn: increment(amount),
        gcashNumber: gcashNumber.value,
        updatedAt: serverTimestamp()
      });
    });

    await addDoc(collection(db, "rider_wallet_transactions"), {
      riderId: user.uid,
      type: "GCash Withdrawal",
      amount,
      status: "processing",
      gcashNumber: gcashNumber.value,
      createdAt: serverTimestamp()
    });

    walletBalance.value -= amount;
    totalWithdrawn.value += amount;

    walletTransactions.value.unshift({
      id: Date.now().toString(),
      type: "GCash Withdrawal",
      amount,
      status: "processing",
      gcashNumber: gcashNumber.value,
      createdAt: new Date()
    });

    alert(`Withdrawal request of ₱${amount.toLocaleString()} to ${gcashNumber.value} submitted.`);
    withdrawAmount.value = "";
  } catch (error) {
    console.error("Error withdrawing to GCash:", error);

    if (error.message === "WALLET_NOT_FOUND") {
      alert("Wallet record not found. Please contact the admin.");
    } else if (error.message === "INSUFFICIENT_BALANCE") {
      alert("Insufficient wallet balance.");
    } else {
      alert("Failed to submit withdrawal request. Please try again.");
    }
  } finally {
    walletProcessing.value = false;
  }
};

// Logout modal logic
const showLogoutModal = ref(false);

function logout() {
  showLogoutModal.value = true;
}

function confirmLogout() {
  localStorage.removeItem("riders");
  router.visit("/");
  showLogoutModal.value = false;
}

function cancelLogout() {
  showLogoutModal.value = false;
}

let unsubscribeCompletedDeliveries = null;

// Initialize on component mount
onMounted(() => {
  onAuthStateChanged(auth, (user) => {
    if (user) {
      fetchAvailableDeliveries();
      fetchActiveDeliveriesCount();
      fetchRiderWallet(user.uid);

      if (unsubscribeCompletedDeliveries) {
        unsubscribeCompletedDeliveries();
      }

      unsubscribeCompletedDeliveries = watchRiderCompletedDeliveries(user.uid);
    } else {
      loading.value = false;
      console.log("No rider logged in");
    }
  });
});

// Cleanup on unmount
onUnmounted(() => {
  stopLocationTracking();

  if (unsubscribeRiderWallet) {
    unsubscribeRiderWallet();
  }

  if (unsubscribeRiderWalletTransactions) {
    unsubscribeRiderWalletTransactions();
  }
});
</script>

<style scoped>
/* Add styles for disabled buttons */
.accept-btn:disabled,
.modal-btn-confirm:disabled {
  background: #9ca3af;
  cursor: not-allowed;
  transform: none;
  box-shadow: none;
}

.accept-btn:disabled:hover,
.modal-btn-confirm:disabled:hover {
  background: #9ca3af;
  transform: none;
  box-shadow: none;
}

/* Rest of the CSS styles remain exactly the same */
</style>

<!-- Rest of the CSS remains exactly the same -->

<style scoped>
/* Previous CSS styles remain the same, adding new styles for real-time features */

.location-update-section {
  margin-bottom: 1.5rem;
  padding: 1rem;
  background: #f0f9ff;
  border-radius: 8px;
  border: 1px solid #e0f2fe;
}

.location-update-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 0.5rem;
}

.location-update-header h4 {
  margin: 0;
  color: #0369a1;
  font-size: 1rem;
  font-weight: 600;
}

.refresh-btn {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  padding: 0.5rem 1rem;
  background: #38bdf8;
  color: white;
  border: none;
  border-radius: 6px;
  font-size: 0.85rem;
  font-weight: 500;
  cursor: pointer;
  transition: all 0.3s ease;
}

.refresh-btn:hover:not(:disabled) {
  background: #0ea5e9;
  transform: translateY(-1px);
}

.refresh-btn:disabled {
  opacity: 0.6;
  cursor: not-allowed;
  transform: none;
}

.location-update-info {
  margin: 0;
  font-size: 0.8rem;
  color: #64748b;
}

.point-coordinates {
  font-size: 0.7rem;
  color: #64748b;
  margin-top: 0.25rem;
  font-family: monospace;
}

/* Rest of the CSS styles remain exactly the same as in your previous code */
/* ... (all the existing CSS styles) ... */
</style>

<style scoped>
.delivery-dashboard {
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
  gap: 12px;
}

.logo {
  width: 50px;
  height: 50px;
  border-radius: 10px;
  box-shadow: 0 4px 12px rgba(255, 120, 40, 0.2);
}

.portal-info h2 {
  font-size: 1.3rem;
  margin: 0;
  color: #1a202c;
  font-weight: 600;
}

.highlight {
  color: #FF7828;
  font-weight: 700;
}

.partner-name {
  margin: 0;
  color: #718096;
  font-size: 0.95rem;
  font-weight: 500;
}

.logout-btn {
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
  background: #FF7828;
  color: white;
  box-shadow: 0 4px 12px rgba(255, 120, 40, 0.3);
}

.logout-btn:hover {
  background: #e6691f;
  transform: translateY(-1px);
  box-shadow: 0 6px 16px rgba(255, 120, 40, 0.4);
}

.wallet-header-btn {
  display: flex;
  align-items: center;
  gap: 8px;
  padding: 0.75rem 1.5rem;
  border: 2px solid #e2e8f0;
  border-radius: 10px;
  font-weight: 700;
  cursor: pointer;
  transition: all 0.3s ease;
  font-size: 0.9rem;
  background: white;
  color: #FF7828;
  box-shadow: 0 4px 12px rgba(15, 23, 42, 0.08);
}

.wallet-header-btn:hover {
  border-color: #FF7828;
  background: rgba(255, 120, 40, 0.06);
  transform: translateY(-1px);
}


/* Overview Cards */
.overview-section {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
  gap: 1.5rem;
  margin-bottom: 2rem;
}

.overview-card {
  background: white;
  padding: 1.5rem;
  border-radius: 16px;
  box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
  border: 1px solid #f1f5f9;
  transition: all 0.3s ease;
  position: relative;
  overflow: hidden;
}

.overview-card::before {
  content: '';
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  height: 4px;
  background: #FF7828;
}

.overview-card:hover {
  transform: translateY(-4px);
  box-shadow: 0 8px 30px rgba(0, 0, 0, 0.12);
}

.card-icon {
  width: 48px;
  height: 48px;
  border-radius: 12px;
  display: flex;
  align-items: center;
  justify-content: center;
  margin-bottom: 1rem;
}

.card-icon.active-deliveries {
  background: rgba(255, 120, 40, 0.1);
  color: #FF7828;
}

.card-icon.available-orders {
  background: rgba(34, 197, 94, 0.1);
  color: #22c55e;
}

.card-icon.todays-earnings {
  background: rgba(139, 92, 246, 0.1);
  color: #8b5cf6;
}

.card-icon.total-earnings {
  background: rgba(59, 130, 246, 0.1);
  color: #3b82f6;
}

.overview-card h3 {
  margin: 0 0 0.5rem 0;
  color: #4a5568;
  font-size: 0.9rem;
  font-weight: 600;
  text-transform: uppercase;
  letter-spacing: 0.5px;
}

.value {
  font-size: 2rem;
  font-weight: 700;
  color: #1a202c;
  margin: 0 0 0.5rem 0;
}

.subtext {
  font-size: 0.8rem;
  font-weight: 500;
  color: #718096;
  margin: 0;
}

/* Tabs */
.tab-navigation {
  display: flex;
  gap: 1rem;
  margin-bottom: 2rem;
  background: white;
  padding: 0.5rem;
  border-radius: 14px;
  box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
  border: 1px solid #f1f5f9;
}

.tab-btn {
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

.tab-btn:hover {
  color: #FF7828;
  background: rgba(255, 120, 40, 0.05);
}

.tab-btn.active {
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

/* Deliveries Grid */
.deliveries-grid {
  display: flex;
  flex-direction: column;
  gap: 1rem;
}

.delivery-card {
  display: flex;
  justify-content: space-between;
  align-items: flex-start;
  padding: 1.5rem;
  background: #f8fafc;
  border-radius: 12px;
  border: 1px solid #e2e8f0;
  transition: all 0.3s ease;
}

.delivery-card:hover {
  border-color: #FF7828;
  box-shadow: 0 4px 12px rgba(255, 120, 40, 0.1);
  transform: translateY(-1px);
}

.delivery-main {
  flex: 1;
}

.delivery-header {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 1rem;
  margin-bottom: 1rem;
}

.order-info {
  display: flex;
  align-items: center;
  gap: 0.5rem;
}

.order-id {
  color: #1a202c;
  font-size: 1.1rem;
  font-weight: 600;
}

.payment-badge {
  font-size: 0.7rem;
  padding: 0.25rem 0.5rem;
  border-radius: 12px;
  font-weight: 600;
  text-transform: uppercase;
}

.payment-badge.cash {
  background: rgba(34, 197, 94, 0.1);
  color: #16a34a;
}

.payment-badge.gcash {
  background: rgba(59, 130, 246, 0.1);
  color: #2563eb;
}

.payment-badge.card {
  background: rgba(139, 92, 246, 0.1);
  color: #7c3aed;
}

.earning-badge {
  background: rgba(34, 197, 94, 0.1);
  color: #22c55e;
  font-size: 0.9rem;
  font-weight: 600;
  padding: 0.5rem 1rem;
  border-radius: 20px;
  letter-spacing: 0.5px;
}

.delivery-info {
  display: flex;
  flex-direction: column;
  gap: 0.75rem;
}

.distance {
  margin: 0;
  color: #718096;
  font-size: 0.9rem;
  display: flex;
  align-items: center;
  gap: 0.5rem;
}

.item {
  margin: 0;
  color: #1a202c;
  font-weight: 600;
  font-size: 1rem;
}

.location-details {
  display: flex;
  flex-direction: column;
  gap: 0.5rem;
}

.location {
  color: #4a5568;
  font-size: 0.9rem;
  line-height: 1.4;
}

.address {
  margin: 0;
  color: #718096;
  font-size: 0.9rem;
  font-style: italic;
}

.delivery-meta {
  display: flex;
  gap: 1rem;
  margin: 0.75rem 0;
}

.meta-item {
  font-size: 0.85rem;
  color: #6b7280;
}

.meta-item strong {
  color: #4b5563;
}

.instructions {
  margin: 0;
  color: #6b7280;
  font-size: 0.9rem;
  font-style: italic;
  padding: 0.5rem;
  background: rgba(255, 120, 40, 0.05);
  border-radius: 6px;
  border-left: 3px solid #FF7828;
}

.delivery-actions {
  display: flex;
  flex-direction: column;
  gap: 0.5rem;
  min-width: 150px;
}

.accept-btn {
  background: #FF7828;
  color: white;
  border: none;
  padding: 0.75rem 1rem;
  border-radius: 8px;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.3s ease;
  box-shadow: 0 2px 8px rgba(255, 120, 40, 0.3);
}

.accept-btn:hover {
  background: #e6691f;
  transform: translateY(-1px);
  box-shadow: 0 4px 12px rgba(255, 120, 40, 0.4);
}

.view-btn {
  background: #f7fafc;
  color: #4a5568;
  border: 2px solid #e2e8f0;
  padding: 0.75rem 1rem;
  border-radius: 8px;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.3s ease;
}

.view-btn:hover {
  background: #edf2f7;
  border-color: #cbd5e0;
}

/* View Details Modal */
.details-modal {
  max-width: 800px;
  max-height: 90vh;
  overflow-y: auto;
}

.modal-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 1.5rem 2rem;
  border-bottom: 1px solid #e2e8f0;
}

.modal-header h3 {
  margin: 0;
  color: #1a202c;
  font-size: 1.25rem;
  font-weight: 700;
}

.close-btn {
  background: none;
  border: none;
  font-size: 1.5rem;
  cursor: pointer;
  color: #718096;
  padding: 0.25rem;
  border-radius: 4px;
}

.close-btn:hover {
  background: #f7fafc;
  color: #1a202c;
}

.modal-content {
  padding: 2rem;
}

/* Map Section */
.map-section {
  margin-bottom: 2rem;
}

.map-section h4 {
  margin: 0 0 1rem 0;
  color: #1a202c;
  font-size: 1.1rem;
  font-weight: 600;
}

.distance-cards {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
  gap: 1rem;
  margin-bottom: 1.5rem;
}

.distance-card {
  display: flex;
  align-items: center;
  gap: 1rem;
  padding: 1rem;
  background: #f8fafc;
  border-radius: 8px;
  border: 1px solid #e2e8f0;
}

.distance-icon {
  width: 48px;
  height: 48px;
  border-radius: 8px;
  display: flex;
  align-items: center;
  justify-content: center;
}

.distance-icon.rider-to-retailer {
  background: rgba(59, 130, 246, 0.1);
  color: #3b82f6;
}

.distance-icon.retailer-to-customer {
  background: rgba(139, 92, 246, 0.1);
  color: #8b5cf6;
}

.distance-icon.total-distance {
  background: rgba(34, 197, 94, 0.1);
  color: #22c55e;
}

.distance-info strong {
  display: block;
  font-size: 0.9rem;
  color: #4a5568;
  margin-bottom: 0.25rem;
}

.distance-info p {
  margin: 0;
  font-size: 1.25rem;
  font-weight: 700;
  color: #1a202c;
}

.route-visualization {
  background: #f8fafc;
  border-radius: 8px;
  padding: 1.5rem;
  border: 1px solid #e2e8f0;
}

.route-points {
  display: flex;
  align-items: center;
  justify-content: space-between;
  position: relative;
}

.point {
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 0.5rem;
  z-index: 2;
}

.point-icon {
  width: 48px;
  height: 48px;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 1.25rem;
  background: white;
  border: 2px solid;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
}

.rider-point .point-icon {
  border-color: #3b82f6;
  background: #3b82f6;
  color: white;
}

.retailer-point .point-icon {
  border-color: #8b5cf6;
  background: #8b5cf6;
  color: white;
}

.customer-point .point-icon {
  border-color: #22c55e;
  background: #22c55e;
  color: white;
}

.point-label {
  font-size: 0.8rem;
  color: #4a5568;
  font-weight: 500;
  text-align: center;
  max-width: 80px;
}

.route-line {
  flex: 1;
  height: 2px;
  background: linear-gradient(90deg, #3b82f6, #8b5cf6, #22c55e);
  margin: 0 0.5rem;
  position: relative;
}

.route-line::before {
  content: '➔';
  position: absolute;
  top: 50%;
  left: 50%;
  transform: translate(-50%, -50%);
  background: white;
  padding: 0 0.25rem;
  color: #718096;
  font-size: 0.8rem;
}

/* Fee Calculation */
.fee-calculation {
  margin-bottom: 2rem;
  padding: 1.5rem;
  background: #f0fdf4;
  border-radius: 8px;
  border: 1px solid #dcfce7;
}

.fee-calculation h4 {
  margin: 0 0 1rem 0;
  color: #16a34a;
  font-size: 1.1rem;
  font-weight: 600;
}

.fee-breakdown {
  display: flex;
  flex-direction: column;
  gap: 0.75rem;
}

.fee-item {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 0.5rem 0;
  border-bottom: 1px solid #dcfce7;
}

.fee-item:last-child {
  border-bottom: none;
}

.total-fee {
  padding-top: 0.75rem;
  border-top: 2px solid #16a34a;
  font-size: 1.1rem;
}

/* Order Details */
.order-details-section,
.location-details-section,
.contact-section,
.instructions-section {
  margin-bottom: 2rem;
}

.order-details-section h4,
.location-details-section h4,
.contact-section h4,
.instructions-section h4 {
  margin: 0 0 1rem 0;
  color: #1a202c;
  font-size: 1.1rem;
  font-weight: 600;
}

.details-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
  gap: 1rem;
}

.detail-item {
  display: flex;
  justify-content: space-between;
  padding: 0.75rem;
  background: #f8fafc;
  border-radius: 6px;
  border: 1px solid #e2e8f0;
}

.location-cards {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
  gap: 1rem;
}

.location-card {
  padding: 1rem;
  background: #f8fafc;
  border-radius: 8px;
  border: 1px solid #e2e8f0;
}

.location-card h5 {
  margin: 0 0 0.5rem 0;
  color: #1a202c;
  font-size: 1rem;
  font-weight: 600;
}

.contact-info p {
  margin: 0.5rem 0;
  color: #4a5568;
}

.instructions-section p {
  margin: 0;
  padding: 1rem;
  background: rgba(255, 120, 40, 0.05);
  border-radius: 6px;
  border-left: 3px solid #FF7828;
  color: #6b7280;
  font-style: italic;
}

.modal-actions {
  display: flex;
  gap: 1rem;
  justify-content: flex-end;
  padding: 1.5rem 2rem;
  border-top: 1px solid #e2e8f0;
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

.no-deliveries {
  text-align: center;
  padding: 3rem 2rem;
  color: #718096;
}

.no-deliveries h3 {
  margin: 1rem 0 0.5rem 0;
  color: #4a5568;
  font-size: 1.25rem;
}

.no-deliveries p {
  margin: 0;
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

/* Wallet Styles */
.header-actions {
  display: flex;
  align-items: center;
  gap: 1rem;
}

.rider-wallet-modal {
  background: white;
  border-radius: 20px;
  width: 92%;
  max-width: 900px;
  max-height: 90vh;
  overflow-y: auto;
  box-shadow: 0 20px 60px rgba(0, 0, 0, 0.2);
}

.wallet-modal-header {
  display: flex;
  justify-content: space-between;
  align-items: flex-start;
  gap: 1rem;
  padding: 1.5rem 2rem;
  border-bottom: 1px solid #e2e8f0;
}

.wallet-modal-header h2 {
  margin: 0 0 0.4rem 0;
  color: #1a202c;
  font-size: 1.5rem;
  font-weight: 800;
}

.wallet-modal-header p {
  margin: 0;
  color: #718096;
  font-size: 0.95rem;
}

.wallet-modal-content {
  padding: 2rem;
}

.wallet-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
  gap: 1rem;
  margin-bottom: 1.5rem;
}

.wallet-card,
.wallet-info-card,
.gcash-card,
.wallet-history {
  background: #f8fafc;
  border: 1px solid #e2e8f0;
  border-radius: 14px;
  padding: 1.5rem;
}

.wallet-card span,
.wallet-card-label {
  display: block;
  color: #4a5568;
  font-size: 0.8rem;
  font-weight: 800;
  text-transform: uppercase;
  letter-spacing: 0.06em;
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
  color: white;
  border-color: #FF7828;
}

.main-wallet span,
.main-wallet h2,
.main-wallet p {
  color: white;
}

.wallet-info-card {
  background: rgba(255, 120, 40, 0.06);
  border-color: rgba(255, 120, 40, 0.18);
  margin-bottom: 1.5rem;
}

.wallet-info-card h3 {
  margin: 0 0 0.5rem 0;
  color: #1a202c;
  font-size: 1.1rem;
}

.wallet-info-card p {
  margin: 0;
  color: #4a5568;
  line-height: 1.6;
}

.gcash-card {
  margin-bottom: 1.5rem;
  background: white;
  box-shadow: 0 10px 28px rgba(15, 23, 42, 0.06);
}

.gcash-header {
  display: flex;
  justify-content: space-between;
  gap: 1rem;
  align-items: flex-start;
  margin-bottom: 1.25rem;
}

.gcash-header h3,
.wallet-history-header h3 {
  margin: 0 0 0.25rem 0;
  color: #1a202c;
}

.gcash-header p,
.wallet-history-header p {
  margin: 0;
  color: #718096;
  font-size: 0.9rem;
}

.gcash-badge {
  background: #2563eb;
  color: white;
  padding: 0.35rem 0.75rem;
  border-radius: 999px;
  font-size: 0.8rem;
  font-weight: 800;
}

.gcash-form {
  display: grid;
  grid-template-columns: 1fr 1fr auto;
  gap: 1rem;
  align-items: end;
}

.gcash-input-group {
  display: flex;
  flex-direction: column;
  gap: 0.5rem;
}

.gcash-input-group label {
  font-size: 0.85rem;
  color: #4a5568;
  font-weight: 700;
}

.gcash-input-group input {
  width: 100%;
  padding: 0.75rem;
  border-radius: 8px;
  border: 1px solid #cbd5e0;
  font-size: 0.95rem;
}

.gcash-input-group input:focus {
  outline: none;
  border-color: #2563eb;
  box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
}

.withdraw-btn {
  padding: 0.85rem 1.25rem;
  border: none;
  border-radius: 10px;
  background: #2563eb;
  color: white;
  font-weight: 800;
  cursor: pointer;
  transition: all 0.3s ease;
  white-space: nowrap;
}

.withdraw-btn:hover:not(:disabled) {
  background: #1d4ed8;
  transform: translateY(-1px);
  box-shadow: 0 6px 16px rgba(37, 99, 235, 0.25);
}

.withdraw-btn:disabled {
  background: #9ca3af;
  cursor: not-allowed;
}

.wallet-history {
  overflow-x: auto;
}

.wallet-history-header {
  margin-bottom: 1rem;
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
  font-weight: 800;
}

.wallet-status {
  display: inline-block;
  padding: 0.25rem 0.65rem;
  border-radius: 999px;
  font-size: 0.75rem;
  font-weight: 800;
  text-transform: capitalize;
}

.wallet-status.processing {
  background: rgba(251, 191, 36, 0.15);
  color: #d97706;
}

.wallet-status.completed,
.wallet-status.credited {
  background: rgba(34, 197, 94, 0.15);
  color: #16a34a;
}

.wallet-status.failed {
  background: rgba(239, 68, 68, 0.15);
  color: #dc2626;
}

.wallet-empty {
  text-align: center;
  color: #718096;
  font-style: italic;
}

/* Responsive Design */
@media (max-width: 768px) {
  .delivery-dashboard {
    padding: 1rem;
  }
  
  .dashboard-header {
    flex-direction: column;
    gap: 1rem;
    align-items: flex-start;
  }
  
  .overview-section {
    grid-template-columns: 1fr;
  }
  
  .tab-navigation {
    flex-direction: column;
  }
  
  .delivery-card {
    flex-direction: column;
    gap: 1.5rem;
  }
  
  .delivery-actions {
    width: 100%;
    flex-direction: row;
  }
  
  .delivery-header {
    flex-direction: column;
    align-items: flex-start;
    gap: 0.5rem;
  }
  
  .delivery-meta {
    flex-direction: column;
    gap: 0.5rem;
  }
  
  .modal-buttons {
    flex-direction: column;
  }
  
  .details-modal {
    margin: 1rem;
    max-height: calc(100vh - 2rem);
  }
  
  .route-points {
    flex-direction: column;
    gap: 1rem;
  }
  
  .route-line {
    width: 2px;
    height: 40px;
    margin: 0;
  }
  
  .route-line::before {
    content: '↓';
    left: 50%;
    top: 50%;
    transform: translate(-50%, -50%);
  }
  
  .distance-cards {
    grid-template-columns: 1fr;
  }
  
  .location-cards {
    grid-template-columns: 1fr;
  }
  
  .details-grid {
    grid-template-columns: 1fr;
  }
  
  .modal-actions {
    flex-direction: column;
  }

  .gcash-form {
    grid-template-columns: 1fr;
  }

  .rider-wallet-modal {
    width: 95%;
    margin: 1rem;
  }

  .wallet-modal-header,
  .wallet-modal-content {
    padding-left: 1.25rem;
    padding-right: 1.25rem;
  }

  .header-actions {
    width: 100%;
    justify-content: flex-end;
  }
}
</style>