<template>
    <!-- Content Area -->
    <section class="content-section">
      <div v-if="activeTab === 'overview'" class="tab-content">
        <div class="content-header">
          <h2>Admin Dashboard</h2>
          <p>Welcome, jihuh</p>
        </div>

        <!-- Loading State -->
        <div v-if="loading" class="loading-state">
          <svg width="48" height="48" viewBox="0 0 24 24" fill="none" class="loading-spinner">
            <path d="M12 2v4m0 12v4M4.93 4.93l2.83 2.83m8.48 8.48l2.83 2.83M2 12h4m12 0h4M4.93 19.07l2.83-2.83m8.48-8.48l2.83-2.83" stroke="#FF7828" stroke-width="2" stroke-linecap="round"/>
          </svg>
          <p>Loading dashboard data...</p>
        </div>

        <div v-else class="dashboard-grid">
          <!-- Recent Orders Section -->
          <div class="dashboard-card">
            <div class="card-header">
              <h3>Recent Orders</h3>
              <p>Latest order activities</p>
            </div>
            <div class="recent-orders">
              <div v-for="order in recentOrders" :key="order.id" class="order-item">
                <div class="order-id">{{ order.orderNumber || order.orderId }}</div>
                <div class="customer-name">{{ order.customerName }}</div>
                <div class="order-status" :class="getStatusClass(order.orderStatus)">
                  {{ formatStatus(order.orderStatus) }}
                </div>
              </div>
              <div v-if="recentOrders.length === 0" class="no-data">
                <p>No recent orders</p>
              </div>
            </div>
          </div>

          <!-- Quick Statistics Section -->
          <div class="dashboard-card">
            <div class="card-header">
              <h3>Quick Statistics</h3>
            </div>
            <div class="quick-stats">
              <div class="stat-item">
                <div class="stat-icon">
                  <svg width="20" height="20" viewBox="0 0 24 24" fill="none">
                    <path d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 8v-4m0 4h4" stroke="currentColor" stroke-width="2"/>
                  </svg>
                </div>
                <div class="stat-info">
                  <p class="stat-value">{{ activeRetailers }}</p>
                  <p class="stat-label">Active Retailers</p>
                </div>
              </div>
              <div class="stat-item">
                <div class="stat-icon">
                  <svg width="20" height="20" viewBox="0 0 24 24" fill="none">
                    <path d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" stroke="currentColor" stroke-width="2"/>
                  </svg>
                </div>
                <div class="stat-info">
                  <p class="stat-value">{{ totalOrders }}</p>
                  <p class="stat-label">Total Orders</p>
                </div>
              </div>
              <div class="stat-item">
                <div class="stat-icon">
                  <svg width="20" height="20" viewBox="0 0 24 24" fill="none">
                    <path d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" stroke="currentColor" stroke-width="2"/>
                  </svg>
                </div>
                <div class="stat-info">
                  <p class="stat-value">{{ activeOrders }}</p>
                  <p class="stat-label">Active Orders</p>
                </div>
              </div>
              <div class="stat-item">
                <div class="stat-icon">
                  <svg width="20" height="20" viewBox="0 0 24 24" fill="none">
                    <path d="M16 8v8m-4-5v5m-4-2v2m-2 4h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" stroke="currentColor" stroke-width="2"/>
                  </svg>
                </div>
                <div class="stat-info">
                  <p class="stat-value">{{ ordersToday }}</p>
                  <p class="stat-label">Orders Today</p>
                </div>
              </div>
            </div>
          </div>

          <!-- System Alerts Section -->
          <div class="dashboard-card">
            <div class="card-header">
              <h3>System Alerts</h3>
              <p>Important notifications</p>
            </div>
            <div class="system-alerts">
              <div v-for="alert in systemAlerts" :key="alert.id" class="alert-item">
                <div class="alert-content">
                  <p class="alert-message">{{ alert.message }}</p>
                  <p class="alert-date">{{ formatDate(alert.date) }}</p>
                </div>
                <div class="alert-icon" :class="alert.type">
                  <svg width="16" height="16" viewBox="0 0 24 24" fill="none">
                    <path d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.35 16.5c-.77.833.192 2.5 1.732 2.5z" stroke="currentColor" stroke-width="2"/>
                  </svg>
                </div>
              </div>
              <div v-if="systemAlerts.length === 0" class="no-data">
                <p>No system alerts</p>
              </div>
            </div>
          </div>

          <!-- Active Delivery Personnel Section -->
          <div class="dashboard-card">
            <div class="card-header">
              <h3>Active Delivery Personnel</h3>
            </div>
            <div class="delivery-personnel">
              <div class="personnel-count">
                <p class="count">{{ activeDeliveryPersonnel }}</p>
                <p class="label">Currently active</p>
              </div>
            </div>
          </div>
        </div>
      </div>

      <div v-else-if="activeTab === 'users'" class="tab-content">
        <div class="content-header">
          <h2>User Management</h2>
          <p>Manage all system users</p>
        </div>

        <!-- Loading State -->
        <div v-if="loading" class="loading-state">
          <svg width="48" height="48" viewBox="0 0 24 24" fill="none" class="loading-spinner">
            <path d="M12 2v4m0 12v4M4.93 4.93l2.83 2.83m8.48 8.48l2.83 2.83M2 12h4m12 0h4M4.93 19.07l2.83-2.83m8.48-8.48l2.83-2.83" stroke="#FF7828" stroke-width="2" stroke-linecap="round"/>
          </svg>
          <p>Loading user data...</p>
        </div>

        <div v-else class="users-management">
          <!-- User Stats Cards - HIWA-HIWALAY NA CARD -->
          <div class="user-stats-cards">
            <!-- Customer Card -->
            <div class="user-stat-card customer-card">
              <h3>Customers</h3>
              <p class="count">{{ customerCount }}</p>
              <div class="card-trend">
                <span>Total registered customers</span>
              </div>
            </div>

            <!-- Retailer Card -->
            <div class="user-stat-card retailer-card">
              <h3>Retailers</h3>
              <p class="count">{{ retailerCount }}</p>
              <div class="card-trend">
                <span>Gas retailer partners</span>
              </div>
            </div>

            <!-- Delivery Riders Card -->
            <div class="user-stat-card rider-card">
              <h3>Delivery Riders</h3>
              <p class="count">{{ deliveryCount }}</p>
              <div class="card-trend">
                <span>Active delivery personnel</span>
              </div>
            </div>

            <!-- Pending Retailers Card -->
            <div class="user-stat-card pending-card">
              <h3>Pending Retailers</h3>
              <p class="count">{{ pendingRetailersCount }}</p>
              <div class="card-trend">
                <span>Awaiting approval</span>
              </div>
            </div>
          </div>

          <!-- Separate Tables for Each User Type -->
          <div class="user-tables-container">
            <!-- Customers Table -->
            <div class="user-table-section">
              <div class="table-header">
                <h3>Customers ({{ customerCount }})</h3>
                <p>Manage all customer accounts</p>
              </div>
              <div class="users-table-container">
                <table class="users-table">
                  <thead>
                    <tr>
                      <th>Name</th>
                      <th>Email</th>
                      <th>Phone</th>
                      <th>Status</th>
                      <th>Joined</th>
                      <th>Actions</th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr v-for="user in customers" :key="user.id">
                      <td class="user-name">{{ user.fullName }}</td>
                      <td class="user-email">{{ user.email }}</td>
                      <td class="user-phone">{{ user.phone || 'N/A' }}</td>
                      <td class="user-status">
                        <span :class="`status-badge ${user.status}`">{{ formatStatus(user.status) }}</span>
                      </td>
                      <td class="user-joined">{{ formatDate(user.createdAt) }}</td>
                      <td class="user-actions">
                        <!-- WALANG EDIT BUTTON - DELETE LANG -->
                        <button class="action-btn delete" @click="deleteUser(user, 'customer')" title="Delete Customer">
                          <svg width="16" height="16" viewBox="0 0 24 24" fill="none">
                            <path d="M3 6h18m-2 0v14a2 2 0 01-2 2H7a2 2 0 01-2-2V6m3 0V4a2 2 0 012-2h4a2 2 0 012 2v2" stroke="currentColor" stroke-width="2"/>
                          </svg>
                        </button>
                      </td>
                    </tr>
                    <tr v-if="customers.length === 0">
                      <td colspan="6" class="no-data-cell">
                        <div class="no-data">
                          <p>No customers found</p>
                        </div>
                      </td>
                    </tr>
                  </tbody>
                </table>
              </div>
            </div>

            <!-- ✅ FIXED: Retailers Table with approvalStatus -->
            <div class="user-table-section">
              <div class="table-header">
                <h3>Retailers ({{ retailerCount }})</h3>
                <p>Manage all gas retailer accounts</p>
              </div>
              <div class="users-table-container">
                <table class="users-table">
                  <thead>
                    <tr>
                      <th>Business Name</th>
                      <th>Owner</th>
                      <th>Email</th>
                      <th>Phone</th>
                      <th>Address</th>
                      <th>Status</th>
                      <th>Approval</th>
                      <th>Joined</th>
                      <th>Actions</th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr v-for="user in retailers" :key="user.id">
                      <td class="user-name">{{ user.businessName || 'N/A' }}</td>
                      <td class="user-name">{{ user.fullName }}</td>
                      <td class="user-email">{{ user.email }}</td>
                      <td class="user-phone">{{ user.phone || 'N/A' }}</td>
                      <td class="user-address">{{ user.address || 'N/A' }}</td>
                      <td class="user-status">
                        <span :class="`status-badge ${user.status}`">{{ formatStatus(user.status) }}</span>
                      </td>
                      <td class="user-status">
                        <span :class="`status-badge ${user.approvalStatus || 'pending'}`">
                          {{ formatApprovalStatus(user.approvalStatus) }}
                        </span>
                      </td>
                      <td class="user-joined">{{ formatDate(user.createdAt) }}</td>
                      <td class="user-actions">
                        <!-- ✅ FIXED: Show approve/reject for pending approvalStatus -->
                        <button v-if="user.approvalStatus === 'pending'" class="action-btn approve" @click="approveRetailer(user)" title="Approve Retailer">
                          <svg width="16" height="16" viewBox="0 0 24 24" fill="none">
                            <path d="M20 6L9 17l-5-5" stroke="currentColor" stroke-width="2"/>
                          </svg>
                        </button>
                        <button v-if="user.approvalStatus === 'pending'" class="action-btn reject" @click="rejectRetailer(user)" title="Reject Retailer">
                          <svg width="16" height="16" viewBox="0 0 24 24" fill="none">
                            <path d="M6 18L18 6M6 6l12 12" stroke="currentColor" stroke-width="2"/>
                          </svg>
                        </button>
                        <!-- Show delete for non-pending -->
                        <button v-if="user.approvalStatus !== 'pending'" class="action-btn delete" @click="deleteUser(user, 'retailer')" title="Delete Retailer">
                          <svg width="16" height="16" viewBox="0 0 24 24" fill="none">
                            <path d="M3 6h18m-2 0v14a2 2 0 01-2 2H7a2 2 0 01-2-2V6m3 0V4a2 2 0 012-2h4a2 2 0 012 2v2" stroke="currentColor" stroke-width="2"/>
                          </svg>
                        </button>
                      </td>
                    </tr>
                    <tr v-if="retailers.length === 0">
                      <td colspan="9" class="no-data-cell">
                        <div class="no-data">
                          <p>No retailers found</p>
                        </div>
                      </td>
                    </tr>
                  </tbody>
                </table>
              </div>
            </div>

            <!-- Delivery Riders Table -->
            <div class="user-table-section">
              <div class="table-header">
                <h3>Delivery Riders ({{ deliveryCount }})</h3>
                <p>Manage all delivery personnel accounts</p>
              </div>
              <div class="users-table-container">
                <table class="users-table">
                  <thead>
                    <tr>
                      <th>Name</th>
                      <th>Email</th>
                      <th>Phone</th>
                      <th>Vehicle Type</th>
                      <th>Status</th>
                      <th>Joined</th>
                      <th>Actions</th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr v-for="user in deliveryRiders" :key="user.id">
                      <td class="user-name">{{ user.fullName }}</td>
                      <td class="user-email">{{ user.email }}</td>
                      <td class="user-phone">{{ user.phone || 'N/A' }}</td>
                      <td class="user-vehicle">{{ user.vehicleType || 'N/A' }}</td>
                      <td class="user-status">
                        <span :class="`status-badge ${user.status}`">{{ formatStatus(user.status) }}</span>
                      </td>
                      <td class="user-joined">{{ formatDate(user.createdAt) }}</td>
                      <td class="user-actions">
                        <!-- WALANG EDIT BUTTON - DELETE LANG -->
                        <button class="action-btn delete" @click="deleteUser(user, 'rider')" title="Delete Rider">
                          <svg width="16" height="16" viewBox="0 0 24 24" fill="none">
                            <path d="M3 6h18m-2 0v14a2 2 0 01-2 2H7a2 2 0 01-2-2V6m3 0V4a2 2 0 012-2h4a2 2 0 012 2v2" stroke="currentColor" stroke-width="2"/>
                          </svg>
                        </button>
                      </td>
                    </tr>
                    <tr v-if="deliveryRiders.length === 0">
                      <td colspan="7" class="no-data-cell">
                        <div class="no-data">
                          <p>No delivery riders found</p>
                        </div>
                      </td>
                    </tr>
                  </tbody>
                </table>
              </div>
            </div>
          </div>
        </div>
      </div>

      <div v-else-if="activeTab === 'orders'">
        <div class="tab-content">
          <div class="content-header">
            <h2>Orders Management</h2>
            <p>View and manage all orders</p>
          </div>
          <div class="coming-soon">
            <svg width="64" height="64" viewBox="0 0 24 24" fill="none">
              <path d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" stroke="#CBD5E0" stroke-width="2"/>
            </svg>
            <h3>Coming Soon</h3>
            <p>Orders management feature is under development</p>
          </div>
        </div>
      </div>

      <div v-else-if="activeTab === 'analytics'">
        <div class="tab-content">
          <div class="content-header">
            <h2>Analytics</h2>
            <p>View system analytics and reports</p>
          </div>
          <div class="coming-soon">
            <svg width="64" height="64" viewBox="0 0 24 24" fill="none">
              <path d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" stroke="#CBD5E0" stroke-width="2"/>
            </svg>
            <h3>Coming Soon</h3>
            <p>Analytics feature is under development</p>
          </div>
        </div>
      </div>
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
        <h3>Settings</h3>
        <p>Admin settings panel</p>
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

    <!-- Approval Confirmation Modal -->
    <div v-if="showApprovalModal" class="modal-overlay">
      <div class="modal">
        <div class="modal-icon">
          <svg width="48" height="48" viewBox="0 0 24 24" fill="none">
            <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" stroke="#FF7828" stroke-width="2"/>
          </svg>
        </div>
        <h3>{{ approvalAction === 'approve' ? 'Approve Retailer' : 'Reject Retailer' }}</h3>
        <p>Are you sure you want to {{ approvalAction }} <strong>{{ selectedRetailer?.businessName }}</strong>?</p>
        <div class="modal-buttons">
          <button class="modal-btn-cancel" @click="cancelApproval">Cancel</button>
          <button :class="['modal-btn-confirm', approvalAction === 'reject' ? 'reject-btn' : '']" @click="confirmApproval">
            {{ approvalAction === 'approve' ? 'Yes, Approve' : 'Yes, Reject' }}
          </button>
        </div>
      </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div v-if="showDeleteModal" class="modal-overlay">
      <div class="modal">
        <div class="modal-icon">
          <svg width="48" height="48" viewBox="0 0 24 24" fill="none">
            <path d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.35 16.5c-.77.833.192 2.5 1.732 2.5z" stroke="#FF7828" stroke-width="2"/>
          </svg>
        </div>
        <h3>Confirm Delete</h3>
        <p>Are you sure you want to delete <strong>{{ selectedUser?.fullName || selectedUser?.businessName }}</strong>?</p>
        <p class="warning-text">This action cannot be undone!</p>
        <div class="modal-buttons">
          <button class="modal-btn-cancel" @click="cancelDelete">Cancel</button>
          <button class="modal-btn-confirm delete-btn" @click="confirmDelete">
            Yes, Delete
          </button>
        </div>
      </div>
    </div>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted } from "vue";
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
  updateDoc,
  doc,
  deleteDoc
} from "firebase/firestore";
import { onAuthStateChanged } from "firebase/auth";

// Import the logo
const logo = '/images/logo.png';

const showSettingsModal = ref(false);
const activeTab = ref("users");
const loading = ref(true);

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

// Users data - real-time from Firestore
const customers = ref([]);
const retailers = ref([]);
const deliveryRiders = ref([]);

// Recent orders and system alerts
const recentOrders = ref([]);
const systemAlerts = ref([]);

// Approval modal
const showApprovalModal = ref(false);
const selectedRetailer = ref(null);
const approvalAction = ref('approve'); // 'approve' or 'reject'

// Delete modal
const showDeleteModal = ref(false);
const selectedUser = ref(null);
const selectedUserType = ref(''); // 'customer', 'retailer', 'rider'

// Firestore unsubscribe functions
let customersUnsubscribe = null;
let retailersUnsubscribe = null;
let ridersUnsubscribe = null;
let ordersUnsubscribe = null;

// ✅ FIXED: Computed property for pending retailers count (uses approvalStatus)
const pendingRetailersCount = computed(() => {
  return retailers.value.filter(retailer => 
    retailer.approvalStatus === 'pending' || 
    (retailer.status === 'pending' && retailer.approvalStatus !== 'approved')
  ).length;
});

// Computed property for orders yesterday difference
const ordersYesterdayDiff = computed(() => {
  return ordersToday.value - ordersYesterday.value;
});

// Format role for display
const formatRole = (role) => {
  const roleMap = {
    'customer': 'Customer',
    'retailer': 'Retailer',
    'delivery': 'Delivery',
    'rider': 'Delivery Rider'
  };
  return roleMap[role] || role;
};

// Format status for display
const formatStatus = (status) => {
  const statusMap = {
    'active': 'Active',
    'requestee': 'Requestee',
    'inactive': 'Inactive',
    'pending': 'Pending',
    'rejected': 'Rejected'
  };
  return statusMap[status] || status;
};

// ✅ NEW: Format approval status for display
const formatApprovalStatus = (status) => {
  const statusMap = {
    'approved': 'Approved',
    'pending': 'Pending Approval',
    'rejected': 'Rejected'
  };
  return statusMap[status] || 'Pending';
};

// Get CSS class for status
const getStatusClass = (status) => {
  const classMap = {
    'delivered': 'delivered',
    'in-transit': 'in-transit',
    'ready-for-pickup': 'ready',
    'assigned': 'assigned',
    'accepted': 'accepted',
    'pending': 'pending',
    'cancelled': 'cancelled'
  };
  return classMap[status] || 'pending';
};

// Format date for display - UPDATED para sa string dates
const formatDate = (date) => {
  if (!date) return 'Unknown date';
  
  try {
    // Kung string ang date
    if (typeof date === 'string') {
      // Check if valid date string
      const parsedDate = new Date(date);
      if (!isNaN(parsedDate.getTime())) {
        return parsedDate.toLocaleString('en-US', {
          year: 'numeric',
          month: 'short',
          day: 'numeric'
        });
      }
      return 'Invalid date';
    }
    
    // Kung Firestore Timestamp (may toDate method)
    if (date.toDate && typeof date.toDate === 'function') {
      return date.toDate().toLocaleString('en-US', {
        year: 'numeric',
        month: 'short',
        day: 'numeric'
      });
    }
    
    // Kung Date object na
    if (date instanceof Date) {
      return date.toLocaleString('en-US', {
        year: 'numeric',
        month: 'short',
        day: 'numeric'
      });
    }
    
    return 'Unknown date';
  } catch (error) {
    console.error('Error formatting date:', error);
    return 'Invalid date';
  }
};

// User management functions - WALANG EDIT FUNCTION
// const editUser = (user) => {
//   console.log("Edit user:", user);
//   // REMOVED EDIT FUNCTIONALITY
// };

// DELETE USER FUNCTION - REAL-TIME
const deleteUser = (user, userType) => {
  selectedUser.value = user;
  selectedUserType.value = userType;
  showDeleteModal.value = true;
};

const cancelDelete = () => {
  showDeleteModal.value = false;
  selectedUser.value = null;
  selectedUserType.value = '';
};

const confirmDelete = async () => {
  if (!selectedUser.value || !selectedUserType.value) return;

  try {
    let collectionName = '';
    let userRef;
    
    // Determine which collection to delete from
    switch (selectedUserType.value) {
      case 'customer':
        collectionName = 'customers';
        userRef = doc(db, collectionName, selectedUser.value.id);
        await deleteDoc(userRef);
        break;
      case 'retailer':
        collectionName = 'retailers';
        userRef = doc(db, collectionName, selectedUser.value.id);
        await deleteDoc(userRef);
        break;
      case 'rider':
        collectionName = 'riders';
        userRef = doc(db, collectionName, selectedUser.value.id);
        await deleteDoc(userRef);
        break;
      default:
        console.error('Invalid user type');
        return;
    }
    
    console.log(`User ${selectedUser.value.fullName || selectedUser.value.businessName} deleted successfully from ${collectionName}`);
    
    // The real-time listener will automatically update the UI
    showDeleteModal.value = false;
    selectedUser.value = null;
    selectedUserType.value = '';
    
  } catch (error) {
    console.error('Error deleting user:', error);
    alert('Error deleting user. Please try again.');
  }
};

// Approval functions
const approveRetailer = (retailer) => {
  selectedRetailer.value = retailer;
  approvalAction.value = 'approve';
  showApprovalModal.value = true;
};

const rejectRetailer = (retailer) => {
  selectedRetailer.value = retailer;
  approvalAction.value = 'reject';
  showApprovalModal.value = true;
};

const cancelApproval = () => {
  showApprovalModal.value = false;
  selectedRetailer.value = null;
};

const confirmApproval = async () => {
  if (!selectedRetailer.value) return;

  try {
    const retailerRef = doc(db, "retailers", selectedRetailer.value.id);
    
    if (approvalAction.value === 'approve') {
      await updateDoc(retailerRef, {
        status: 'active',
        approvalStatus: 'approved',
        approvalDate: new Date().toISOString(),
        approvedBy: 'admin'
      });
      console.log(`Retailer ${selectedRetailer.value.businessName} approved successfully`);
    } else {
      await updateDoc(retailerRef, {
        status: 'rejected',
        approvalStatus: 'rejected',
        rejectionDate: new Date().toISOString(),
        rejectedBy: 'admin'
      });
      console.log(`Retailer ${selectedRetailer.value.businessName} rejected`);
    }
    
    showApprovalModal.value = false;
    selectedRetailer.value = null;
    
  } catch (error) {
    console.error('Error updating retailer status:', error);
  }
};

// Initialize real-time data listeners for separate collections - UPDATED
const initializeRealtimeListeners = () => {
  console.log("Initializing Firestore listeners...");
  
  // Listen to customers collection (ROOT LEVEL)
  const customersQuery = query(collection(db, "customers"));
  customersUnsubscribe = onSnapshot(customersQuery, (snapshot) => {
    console.log("Customers snapshot received:", snapshot.size, "documents");
    
    const customersList = [];
    snapshot.forEach((doc) => {
      const userData = {
        id: doc.id,
        ...doc.data()
      };
      customersList.push(userData);
    });
    
    customers.value = customersList;
    customerCount.value = customersList.length;
    console.log("Customers loaded:", customersList.length);
    updateTotalUsers();
  }, (error) => {
    console.error("Error in customers listener:", error);
  });

  // Listen to retailers collection (root level)
  const retailersQuery = query(collection(db, "retailers"));
  retailersUnsubscribe = onSnapshot(retailersQuery, (snapshot) => {
    console.log("Retailers snapshot received:", snapshot.size, "documents");
    
    const retailersList = [];
    let activeRetailersTemp = 0;
    
    snapshot.forEach((doc) => {
      const userData = {
        id: doc.id,
        ...doc.data()
      };
      retailersList.push(userData);
      
      // Count active retailers (based on approvalStatus)
      if (userData.approvalStatus === 'approved' || userData.status === 'active') {
        activeRetailersTemp++;
      }
    });
    
    retailers.value = retailersList;
    retailerCount.value = retailersList.length;
    activeRetailers.value = activeRetailersTemp;
    updateTotalUsers();
  });

  // Listen to riders collection (root level)
  const ridersQuery = query(collection(db, "riders"));
  ridersUnsubscribe = onSnapshot(ridersQuery, (snapshot) => {
    console.log("Riders snapshot received:", snapshot.size, "documents");
    
    const ridersList = [];
    let activeRiders = 0;
    
    snapshot.forEach((doc) => {
      const userData = {
        id: doc.id,
        ...doc.data()
      };
      ridersList.push(userData);
      
      if (userData.status === 'active') {
        activeRiders++;
      }
    });
    
    deliveryRiders.value = ridersList;
    deliveryCount.value = ridersList.length;
    activeDeliveryPersonnel.value = activeRiders;
    updateTotalUsers();
  });

  // Listen to orders collection for statistics
  const ordersQuery = query(collection(db, "orders"));
  ordersUnsubscribe = onSnapshot(ordersQuery, (snapshot) => {
    totalOrders.value = snapshot.size;
    
    // Calculate active orders (orders that are not delivered or cancelled)
    let activeCount = 0;
    let todayCount = 0;
    const today = new Date();
    today.setHours(0, 0, 0, 0);
    
    snapshot.forEach((doc) => {
      const orderData = doc.data();
      
      // Check if order is active
      if (orderData.orderStatus && 
          !['delivered', 'cancelled'].includes(orderData.orderStatus)) {
        activeCount++;
      }
      
      // Check if order was created today (handle string dates)
      if (orderData.createdAt) {
        let orderDate;
        
        if (typeof orderData.createdAt === 'string') {
          orderDate = new Date(orderData.createdAt);
        } else if (orderData.createdAt.toDate) {
          orderDate = orderData.createdAt.toDate();
        } else {
          orderDate = new Date(orderData.createdAt);
        }
        
        if (!isNaN(orderDate.getTime())) {
          orderDate.setHours(0, 0, 0, 0);
          if (orderDate.getTime() === today.getTime()) {
            todayCount++;
          }
        }
      }
    });
    
    activeOrders.value = activeCount;
    ordersToday.value = todayCount;
  });
};

// Update total users count
const updateTotalUsers = () => {
  totalUsers.value = customerCount.value + retailerCount.value + deliveryCount.value;
};

// Cleanup listeners
const cleanupListeners = () => {
  if (customersUnsubscribe) {
    customersUnsubscribe();
  }
  if (retailersUnsubscribe) {
    retailersUnsubscribe();
  }
  if (ridersUnsubscribe) {
    ridersUnsubscribe();
  }
  if (ordersUnsubscribe) {
    ordersUnsubscribe();
  }
};

// Logout modal logic
const showLogoutModal = ref(false);

function logout() {
  showLogoutModal.value = true;
}

function confirmLogout() {
  localStorage.removeItem("admin");
  router.visit("/");
  showLogoutModal.value = false;
}

function cancelLogout() {
  showLogoutModal.value = false;
}

// Initialize on component mount - I-replace ang existing onMounted
onMounted(() => {
  console.log("Users component mounted");
  
  // Check if there's an admin in localStorage (from Firebase login)
  const adminData = localStorage.getItem("admin");
  console.log("Admin data from localStorage:", adminData);
  
  if (adminData) {
    // May admin na naka-login
    console.log("✅ Admin found in localStorage");
    
    // Initialize real-time listeners
    initializeRealtimeListeners();
    
    setTimeout(() => {
      loading.value = false;
    }, 2000);
  } else {
    // Walang admin, check Firebase auth (fallback)
    onAuthStateChanged(auth, (user) => {
      if (user) {
        console.log("✅ Admin logged in via Firebase:", user.email);
        
        // Store sa localStorage for next time
        localStorage.setItem("admin", JSON.stringify({
          email: user.email,
          uid: user.uid
        }));
        
        initializeRealtimeListeners();
        
        setTimeout(() => {
          loading.value = false;
        }, 2000);
      } else {
        loading.value = false;
        console.log("❌ No admin logged in - please login first");
      }
    });
  }
});
// Cleanup on component unmount
onUnmounted(() => {
  cleanupListeners();
});
</script>

<style scoped>
/* LAHAT NG CSS NANDITO PA RIN, NAG-DAGDAG LANG NG DELETE BUTTON STYLES */

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

/* Summary Cards */
.summary-cards {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
  gap: 1.5rem;
  margin-bottom: 2rem;
}

.card {
  background: white;
  padding: 1.5rem;
  border-radius: 16px;
  box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
  border: 1px solid #f1f5f9;
  transition: all 0.3s ease;
  position: relative;
  overflow: hidden;
}

.card::before {
  content: '';
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  height: 4px;
  background: #FF7828;
}

.card:hover {
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

.card-icon.total-users {
  background: rgba(139, 92, 246, 0.1);
  color: #8b5cf6;
}

.card-icon.active-retailers {
  background: rgba(16, 185, 129, 0.1);
  color: #10b981;
}

.card-icon.total-orders {
  background: rgba(255, 120, 40, 0.1);
  color: #FF7828;
}

.card-icon.orders-today {
  background: rgba(59, 130, 246, 0.1);
  color: #3b82f6;
}

.card h3 {
  margin: 0 0 0.5rem 0;
  color: #4a5568;
  font-size: 0.9rem;
  font-weight: 600;
  text-transform: uppercase;
  letter-spacing: 0.5px;
}

.count {
  font-size: 2rem;
  font-weight: 700;
  color: #1a202c;
  margin: 0 0 0.5rem 0;
}

.user-breakdown {
  display: flex;
  flex-direction: column;
  gap: 0.25rem;
}

.user-breakdown small {
  color: #718096;
  font-size: 0.8rem;
}

.card-trend, .card-alert, .card-subtitle {
  font-size: 0.8rem;
  font-weight: 500;
  margin: 0;
}

.card-trend.positive {
  color: #22c55e;
}

.card-alert {
  color: #ef4444;
}

.card-subtitle {
  color: #718096;
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

/* Users Management */
.users-management {
  display: flex;
  flex-direction: column;
  gap: 2rem;
}

/* User Stats Cards - HIWA-HIWALAY NA DESIGN */
.user-stats-cards {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
  gap: 1.5rem;
  margin-bottom: 1rem;
}

.user-stat-card {
  background: white;
  padding: 1.5rem;
  border-radius: 16px;
  box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
  border: 1px solid #f1f5f9;
  transition: all 0.3s ease;
  position: relative;
  overflow: hidden;
  text-align: center;
}

.user-stat-card::before {
  content: '';
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  height: 4px;
}

.user-stat-card:hover {
  transform: translateY(-4px);
  box-shadow: 0 8px 30px rgba(0, 0, 0, 0.12);
}

/* Individual Card Colors */
.customer-card::before {
  background: #8b5cf6;
}

.retailer-card::before {
  background: #10b981;
}

.rider-card::before {
  background: #3b82f6;
}

.pending-card::before {
  background: #f59e0b;
}

.user-stat-card h3 {
  margin: 0 0 0.5rem 0;
  color: #4a5568;
  font-size: 1rem;
  font-weight: 600;
  text-transform: uppercase;
  letter-spacing: 0.5px;
}

.user-stat-card .count {
  font-size: 2.5rem;
  font-weight: 700;
  margin: 0 0 0.5rem 0;
}

.customer-card .count {
  color: #8b5cf6;
}

.retailer-card .count {
  color: #10b981;
}

.rider-card .count {
  color: #3b82f6;
}

.pending-card .count {
  color: #f59e0b;
}

.user-stat-card .card-trend {
  font-size: 0.85rem;
  color: #718096;
  font-weight: 500;
}

/* User Tables Container */
.user-tables-container {
  display: flex;
  flex-direction: column;
  gap: 2rem;
}

.user-table-section {
  background: white;
  border-radius: 12px;
  border: 1px solid #e2e8f0;
  overflow: hidden;
}

.table-header {
  padding: 1.5rem;
  background: #f8fafc;
  border-bottom: 1px solid #e2e8f0;
}

.table-header h3 {
  margin: 0 0 0.5rem 0;
  color: #1a202c;
  font-size: 1.2rem;
  font-weight: 600;
}

.table-header p {
  margin: 0;
  color: #718096;
  font-size: 0.9rem;
}

/* Users Table */
.users-table-container {
  overflow-x: auto;
}

.users-table {
  width: 100%;
  border-collapse: collapse;
}

.users-table th,
.users-table td {
  padding: 1rem;
  text-align: left;
  border-bottom: 1px solid #e2e8f0;
}

.users-table th {
  background: #f8fafc;
  color: #4a5568;
  font-weight: 600;
  font-size: 0.9rem;
  text-transform: uppercase;
  letter-spacing: 0.5px;
}

.users-table td {
  color: #4a5568;
  font-size: 0.95rem;
}

.user-name {
  font-weight: 600;
  color: #1a202c;
}

.user-email {
  color: #718096;
}

.user-phone {
  color: #4a5568;
  font-family: monospace;
}

.user-address {
  color: #4a5568;
  max-width: 200px;
  overflow: hidden;
  text-overflow: ellipsis;
  white-space: nowrap;
}

.user-vehicle {
  color: #4a5568;
  text-transform: capitalize;
}

.status-badge {
  padding: 0.4rem 0.8rem;
  border-radius: 20px;
  font-size: 0.75rem;
  font-weight: 600;
  text-transform: capitalize;
  letter-spacing: 0.5px;
}

.status-badge.active {
  background: #d1fae5;
  color: #065f46;
}

.status-badge.requestee {
  background: #fef3c7;
  color: #d97706;
}

.status-badge.inactive {
  background: #fee2e2;
  color: #dc2626;
}

.status-badge.pending {
  background: #fef3c7;
  color: #d97706;
}

.status-badge.rejected {
  background: #fee2e2;
  color: #dc2626;
}

/* Status badge for approval status */
.status-badge.approved {
  background: #d1fae5;
  color: #065f46;
}

.status-badge.pending-approval {
  background: #fef3c7;
  color: #d97706;
}

.user-actions {
  display: flex;
  gap: 0.5rem;
}

.action-btn {
  width: 32px;
  height: 32px;
  border-radius: 8px;
  display: flex;
  align-items: center;
  justify-content: center;
  background: #f7fafc;
  border: 1px solid #e2e8f0;
  color: #718096;
  cursor: pointer;
  transition: all 0.3s ease;
}

.action-btn:hover {
  background: #FF7828;
  color: white;
  border-color: #FF7828;
}

.action-btn.approve {
  background: #d1fae5;
  color: #065f46;
  border-color: #a7f3d0;
}

.action-btn.approve:hover {
  background: #065f46;
  color: white;
}

.action-btn.reject {
  background: #fee2e2;
  color: #991b1b;
  border-color: #fecaca;
}

.action-btn.reject:hover {
  background: #991b1b;
  color: white;
}

.action-btn.delete {
  background: #fee2e2;
  color: #dc2626;
  border-color: #fecaca;
}

.action-btn.delete:hover {
  background: #dc2626;
  color: white;
}

.no-data-cell {
  text-align: center;
}

.no-data {
  padding: 2rem;
  text-align: center;
  color: #718096;
}

.no-data p {
  margin: 0;
}

/* Coming Soon Sections */
.coming-soon {
  text-align: center;
  padding: 4rem 2rem;
  color: #718096;
}

.coming-soon h3 {
  margin: 1rem 0 0.5rem 0;
  color: #4a5568;
  font-size: 1.25rem;
}

.coming-soon p {
  margin-bottom: 1.5rem;
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
  margin: 0 0 1rem 0;
  color: #718096;
  font-size: 1rem;
  line-height: 1.5;
}

.warning-text {
  color: #dc2626 !important;
  font-weight: 600;
  font-size: 0.9rem !important;
}

.modal-buttons {
  display: flex;
  gap: 1rem;
  justify-content: center;
  margin-top: 1.5rem;
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

.reject-btn {
  background: #ef4444 !important;
}

.reject-btn:hover {
  background: #dc2626 !important;
}

.delete-btn {
  background: #dc2626 !important;
}

.delete-btn:hover {
  background: #b91c1c !important;
}

/* Responsive Design */
@media (max-width: 1024px) {
  .dashboard-grid {
    grid-template-columns: 1fr;
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
  
  .summary-cards {
    grid-template-columns: 1fr;
  }
  
  .user-stats-cards {
    grid-template-columns: repeat(2, 1fr);
  }
  
  .tabs {
    flex-direction: column;
  }
  
  .users-table-container {
    overflow-x: auto;
  }
  
  .users-table {
    min-width: 900px;
  }
  
  .modal-buttons {
    flex-direction: column;
  }
}

@media (max-width: 480px) {
  .user-stats-cards {
    grid-template-columns: 1fr;
  }
  
  .table-header {
    padding: 1rem;
  }
  
  .users-table th,
  .users-table td {
    padding: 0.75rem 0.5rem;
  }
}
</style>