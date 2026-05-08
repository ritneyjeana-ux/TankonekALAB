<template>
    <!-- Content Area -->
    <section class="content-section">
      <div class="tab-content">
        <div class="content-header">
          <h2>Order Management</h2>
          <p>Monitor all orders in the system</p>
        </div>

        <!-- Loading State -->
        <div v-if="loading" class="loading-state">
          <svg width="48" height="48" viewBox="0 0 24 24" fill="none" class="loading-spinner">
            <path d="M12 2v4m0 12v4M4.93 4.93l2.83 2.83m8.48 8.48l2.83 2.83M2 12h4m12 0h4M4.93 19.07l2.83-2.83m8.48-8.48l2.83-2.83" stroke="#FF7828" stroke-width="2" stroke-linecap="round"/>
          </svg>
          <p>Loading orders data...</p>
        </div>

        <div v-else class="orders-management">
          <!-- Order Stats Cards -->
          <div class="order-stats-cards">
            <div class="order-stat-card">
              <h3>Total Orders</h3>
              <p class="count">{{ filteredOrders.length }}</p>
              <div class="order-breakdown">
                <small>Active: {{ activeOrders }}</small>
                <small>Completed: {{ completedOrders }}</small>
                <small>Cancelled: {{ cancelledOrders }}</small>
              </div>
            </div>
            
            <div class="order-stat-card">
              <h3>Total Revenue</h3>
              <p class="count">₱ {{ totalRevenue.toLocaleString() }}</p>
              <small class="revenue-subtitle">All time revenue</small>
            </div>

            <div class="order-stat-card">
              <h3>Orders Today</h3>
              <p class="count">{{ ordersToday }}</p>
              <small class="revenue-subtitle">New orders today</small>
            </div>
          </div>

          <!-- Filters Section -->
          <div class="filters-section">
            <!-- Calendar Filter -->
            <div class="filter-container">
              <div class="filter-header">
                <h3>Filter by Date Range</h3>
              </div>
              <div class="filter-controls">
                <div class="date-input-group">
                  <label for="startDate">From Date:</label>
                  <input 
                    type="date" 
                    id="startDate"
                    v-model="startDate" 
                    class="date-input"
                    @change="applyFilters"
                  >
                </div>
                <div class="date-input-group">
                  <label for="endDate">To Date:</label>
                  <input 
                    type="date" 
                    id="endDate"
                    v-model="endDate" 
                    class="date-input"
                    @change="applyFilters"
                  >
                </div>
              </div>
            </div>

            <!-- Retailer Filter -->
            <div class="filter-container">
              <div class="filter-header">
                <h3>Filter by Gas Retailer</h3>
              </div>
              <div class="filter-controls">
                <div class="retailer-input-group">
                  <label for="retailerFilter">Select Retailer:</label>
                  <select 
                    id="retailerFilter"
                    v-model="selectedRetailer" 
                    class="retailer-select"
                    @change="applyFilters"
                  >
                    <option value="">All Retailers</option>
                    <option 
                      v-for="retailer in retailersData" 
                      :key="retailer.id"
                      :value="retailer.businessName"
                    >
                      {{ retailer.businessName }}
                    </option>
                  </select>
                </div>
              </div>
            </div>

            <!-- Clear Filters -->
            <div class="filter-actions">
              <button class="clear-filters-btn" @click="clearAllFilters">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none">
                  <path d="M6 18L18 6M6 6l12 12" stroke="currentColor" stroke-width="2"/>
                </svg>
                Clear All Filters
              </button>
            </div>
          </div>

          <!-- Active Filters Info -->
          <div class="active-filters-info" v-if="isAnyFilterActive">
            <span class="filters-active-badge">
              <svg width="16" height="16" viewBox="0 0 24 24" fill="none" style="margin-right: 8px;">
                <path d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" stroke="currentColor" stroke-width="2"/>
              </svg>
              Active Filters: 
              <span v-if="isDateFilterActive" class="filter-tag">
                Date: {{ formatDisplayDate(startDate) }} to {{ formatDisplayDate(endDate) }}
              </span>
              <span v-if="isRetailerFilterActive" class="filter-tag">
                Retailer: {{ selectedRetailer }}
              </span>
              ({{ filteredOrders.length }} orders)
            </span>
          </div>

          <!-- Orders Table -->
          <div class="orders-table-container">
            <div class="table-header">
              <h3>All Orders</h3>
              <div class="table-actions">
                <button class="refresh-btn" @click="refreshData" title="Refresh Data">
                  <svg width="16" height="16" viewBox="0 0 24 24" fill="none">
                    <path d="M23 4v6h-6M1 20v-6h6M3.51 9a9 9 0 0114.85-3.36L23 10M1 14l4.64 4.36A9 9 0 0020.49 15" stroke="currentColor" stroke-width="2"/>
                  </svg>
                  Refresh
                </button>
              </div>
            </div>
            <table class="orders-table">
              <thead>
                <tr>
                  <th>Order ID</th>
                  <th>Customer</th>
                  <th>Retailer</th>
                  <th>Item</th>
                  <th>Amount</th>
                  <th>Status</th>
                  <th>Date</th>
                  <th>Actions</th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="order in filteredOrders" :key="order.id">
                  <td class="order-id">{{ order.orderId || order.orderNumber }}</td>
                  <td class="customer-name">{{ order.customerName }}</td>
                  <td class="retailer-name">{{ order.retailerName || order.retailerBusinessName }}</td>
                  <td class="order-item">
                    <span v-if="order.items && order.items.length > 0">
                      {{ order.items[0].quantity }}x {{ order.items[0].tankSize }} {{ order.items[0].productType }}
                    </span>
                    <span v-else>
                      {{ order.quantity }}x {{ order.gasType }} {{ order.gasBrand }}
                    </span>
                  </td>
                  <td class="order-amount">₱{{ order.totalAmount || order.total }}</td>
                  <td class="order-status">
                    <span :class="`status-badge ${order.orderStatus || order.status}`">
                      {{ formatStatus(order.orderStatus || order.status) }}
                    </span>
                  </td>
                  <td class="order-date">{{ formatDate(order.createdAt || order.orderDate) }}</td>
                  <td class="order-actions">
                    <button class="action-btn view-btn" @click="viewOrder(order)" title="View Order">
                      <svg width="16" height="16" viewBox="0 0 24 24" fill="none">
                        <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z" stroke="currentColor" stroke-width="2"/>
                        <circle cx="12" cy="12" r="3" stroke="currentColor" stroke-width="2"/>
                      </svg>
                    </button>
                  </td>
                </tr>
                <tr v-if="filteredOrders.length === 0">
                  <td colspan="8" class="no-orders">
                    <div class="no-data">
                      <svg width="64" height="64" viewBox="0 0 24 24" fill="none">
                        <path d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" stroke="#CBD5E0" stroke-width="2"/>
                      </svg>
                      <h3>No Orders Found</h3>
                      <p v-if="isAnyFilterActive">No orders found for the selected filters. Try adjusting your filter criteria.</p>
                      <p v-else>There are no orders in the system yet.</p>
                    </div>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </section>

    <!-- Order Details Modal -->
    <div v-if="showOrderModal" class="modal-overlay">
      <div class="modal large-modal">
        <div class="modal-header">
          <h3>Order Details</h3>
          <button class="close-btn" @click="showOrderModal = false">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none">
              <path d="M18 6L6 18M6 6l12 12" stroke="currentColor" stroke-width="2"/>
            </svg>
          </button>
        </div>
        <div class="modal-content" v-if="selectedOrder">
          <div class="order-details-grid">
            <div class="detail-section">
              <h4>Order Information</h4>
              <div class="detail-item">
                <span class="detail-label">Order ID:</span>
                <span class="detail-value">{{ selectedOrder.orderId || selectedOrder.orderNumber }}</span>
              </div>
              <div class="detail-item">
                <span class="detail-label">Status:</span>
                <span class="detail-value">
                  <span :class="`status-badge ${selectedOrder.orderStatus || selectedOrder.status}`">
                    {{ formatStatus(selectedOrder.orderStatus || selectedOrder.status) }}
                  </span>
                </span>
              </div>
              <div class="detail-item">
                <span class="detail-label">Order Date:</span>
                <span class="detail-value">{{ formatDate(selectedOrder.createdAt || selectedOrder.orderDate) }}</span>
              </div>
              <div class="detail-item">
                <span class="detail-label">Total Amount:</span>
                <span class="detail-value">₱{{ selectedOrder.totalAmount || selectedOrder.total }}</span>
              </div>
            </div>

            <div class="detail-section">
              <h4>Customer Information</h4>
              <div class="detail-item">
                <span class="detail-label">Name:</span>
                <span class="detail-value">{{ selectedOrder.customerName }}</span>
              </div>
              <div class="detail-item">
                <span class="detail-label">Phone:</span>
                <span class="detail-value">{{ selectedOrder.customerPhone }}</span>
              </div>
              <div class="detail-item">
                <span class="detail-label">Delivery Address:</span>
                <span class="detail-value">{{ selectedOrder.deliveryAddress || selectedOrder.customerAddress }}</span>
              </div>
            </div>

            <div class="detail-section">
              <h4>Retailer Information</h4>
              <div class="detail-item">
                <span class="detail-label">Business Name:</span>
                <span class="detail-value">{{ selectedOrder.retailerName || selectedOrder.retailerBusinessName }}</span>
              </div>
            </div>

            <div class="detail-section">
              <h4>Order Items</h4>
              <div class="order-items">
                <div v-if="selectedOrder.items && selectedOrder.items.length > 0" class="item-list">
                  <div v-for="(item, index) in selectedOrder.items" :key="index" class="item">
                    <span class="item-name">{{ item.quantity }}x {{ item.tankSize }} {{ item.productType }}</span>
                    <span class="item-price">₱{{ item.price }}</span>
                  </div>
                </div>
                <div v-else class="item-list">
                  <div class="item">
                    <span class="item-name">{{ selectedOrder.quantity }}x {{ selectedOrder.gasType }} {{ selectedOrder.gasBrand }}</span>
                    <span class="item-price">₱{{ selectedOrder.pricePerTank }}</span>
                  </div>
                </div>
                <div class="delivery-fee" v-if="selectedOrder.deliveryFee">
                  <span class="fee-label">Delivery Fee:</span>
                  <span class="fee-amount">₱{{ selectedOrder.deliveryFee }}</span>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="modal-buttons">
          <button class="modal-btn-confirm" @click="showOrderModal = false">Close</button>
        </div>
      </div>
    </div>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted, inject } from "vue";
import { db } from "@/firebase";
import { 
  collection, 
  query, 
  orderBy,
  onSnapshot,
  where,
  Timestamp
} from "firebase/firestore";

const loading = ref(true);
const ordersData = ref([]);
const deliveriesData = ref([]);
const retailersData = ref([]);
const showOrderModal = ref(false);
const selectedOrder = ref(null);
const startDate = ref('');
const endDate = ref('');
const selectedRetailer = ref('');

// Inject data from parent if needed
const dashboardData = inject('dashboardData');

// Computed properties
const isDateFilterActive = computed(() => {
  return startDate.value !== '' && endDate.value !== '';
});

const isRetailerFilterActive = computed(() => {
  return selectedRetailer.value !== '';
});

const isAnyFilterActive = computed(() => {
  return isDateFilterActive.value || isRetailerFilterActive.value;
});

const filteredOrders = computed(() => {
  let filtered = combinedOrders.value;

  // Apply date filter
  if (isDateFilterActive.value) {
    const start = new Date(startDate.value);
    const end = new Date(endDate.value);
    end.setHours(23, 59, 59, 999);

    filtered = filtered.filter(order => {
      const orderDate = order.createdAt?.toDate ? order.createdAt.toDate() : new Date(order.createdAt || order.orderDate);
      return orderDate >= start && orderDate <= end;
    });
  }

  // Apply retailer filter
  if (isRetailerFilterActive.value) {
    filtered = filtered.filter(order => {
      const retailerName = order.retailerName || order.retailerBusinessName;
      return retailerName === selectedRetailer.value;
    });
  }

  return filtered;
});

const totalOrders = computed(() => {
  return combinedOrders.value.length;
});

const activeOrders = computed(() => {
  const activeStatuses = ['pending', 'accepted', 'assigned', 'ready-for-pickup', 'in-transit', 'processing'];
  return filteredOrders.value.filter(order => 
    activeStatuses.includes(order.orderStatus || order.status)
  ).length;
});

const completedOrders = computed(() => {
  return filteredOrders.value.filter(order => 
    (order.orderStatus || order.status) === 'delivered'
  ).length;
});

const cancelledOrders = computed(() => {
  return filteredOrders.value.filter(order => 
    (order.orderStatus || order.status) === 'cancelled'
  ).length;
});

const totalRevenue = computed(() => {
  return filteredOrders.value.reduce((total, order) => {
    const amount = parseFloat(order.totalAmount || order.total || 0);
    return total + (isNaN(amount) ? 0 : amount);
  }, 0);
});

const ordersToday = computed(() => {
  const today = new Date();
  today.setHours(0, 0, 0, 0);
  
  return combinedOrders.value.filter(order => {
    const orderDate = order.createdAt?.toDate ? order.createdAt.toDate() : new Date(order.createdAt || order.orderDate);
    return orderDate >= today;
  }).length;
});

const combinedOrders = computed(() => {
  // Combine orders from both collections and sort by date (newest first)
  const allOrders = [...ordersData.value, ...deliveriesData.value];
  
  return allOrders.sort((a, b) => {
    const dateA = a.createdAt?.toDate ? a.createdAt.toDate() : new Date(a.createdAt || a.orderDate);
    const dateB = b.createdAt?.toDate ? b.createdAt.toDate() : new Date(b.createdAt || b.orderDate);
    return dateB - dateA;
  });
});

// Format status for display
const formatStatus = (status) => {
  const statusMap = {
    'pending': 'Pending',
    'accepted': 'Accepted',
    'assigned': 'Assigned',
    'ready-for-pickup': 'Ready for Pickup',
    'in-transit': 'In Transit',
    'delivered': 'Delivered',
    'cancelled': 'Cancelled',
    'processing': 'Processing'
  };
  return statusMap[status] || status;
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

// Format date for display in filter
const formatDisplayDate = (dateString) => {
  if (!dateString) return '';
  const date = new Date(dateString);
  return date.toLocaleDateString('en-US', {
    year: 'numeric',
    month: 'short',
    day: 'numeric'
  });
};

// Filter functions
const applyFilters = () => {
  if (startDate.value && endDate.value) {
    const start = new Date(startDate.value);
    const end = new Date(endDate.value);
    
    if (start > end) {
      alert('Start date cannot be after end date');
      startDate.value = '';
      endDate.value = '';
      return;
    }
  }
};

const clearAllFilters = () => {
  startDate.value = '';
  endDate.value = '';
  selectedRetailer.value = '';
};

// Real-time listeners
let ordersUnsubscribe = null;
let deliveriesUnsubscribe = null;
let retailersUnsubscribe = null;

const setupRealTimeListeners = () => {
  console.log("🔄 Setting up real-time listeners for orders...");

  // Listen to orders collection
  const ordersQuery = query(collection(db, "orders"), orderBy("createdAt", "desc"));
  ordersUnsubscribe = onSnapshot(ordersQuery, 
    (snapshot) => {
      const ordersList = [];
      snapshot.forEach((doc) => {
        const orderData = doc.data();
        ordersList.push({
          id: doc.id,
          ...orderData,
          collectionType: 'orders',
          createdAt: orderData.createdAt,
          orderDate: orderData.orderDate,
          updatedAt: orderData.updatedAt,
          estimatedDelivery: orderData.estimatedDelivery,
          acceptedAt: orderData.acceptedAt
        });
      });
      
      ordersData.value = ordersList;
      loading.value = false;
      console.log("✅ Orders loaded:", ordersList.length);
    },
    (error) => {
      console.error("❌ Error listening to orders:", error);
      loading.value = false;
    }
  );

  // Listen to deliveries collection
  const deliveriesQuery = query(collection(db, "deliveries"), orderBy("createdAt", "desc"));
  deliveriesUnsubscribe = onSnapshot(deliveriesQuery, 
    (snapshot) => {
      const deliveriesList = [];
      snapshot.forEach((doc) => {
        const deliveryData = doc.data();
        deliveriesList.push({
          id: doc.id,
          ...deliveryData,
          collectionType: 'deliveries',
          createdAt: deliveryData.createdAt,
          updatedAt: deliveryData.updatedAt
        });
      });
      
      deliveriesData.value = deliveriesList;
      console.log("✅ Deliveries loaded:", deliveriesList.length);
    },
    (error) => {
      console.error("❌ Error listening to deliveries:", error);
    }
  );

  // Listen to retailers collection - REALTIME
  const retailersQuery = query(collection(db, "retailers"), orderBy("businessName", "asc"));
  retailersUnsubscribe = onSnapshot(retailersQuery, 
    (snapshot) => {
      const retailersList = [];
      snapshot.forEach((doc) => {
        const retailerData = doc.data();
        retailersList.push({
          id: doc.id,
          ...retailerData
        });
      });
      
      retailersData.value = retailersList;
      console.log("✅ Retailers loaded:", retailersList.length);
    },
    (error) => {
      console.error("❌ Error listening to retailers:", error);
    }
  );
};

// Order management functions
const viewOrder = (order) => {
  selectedOrder.value = order;
  showOrderModal.value = true;
};

const editOrder = (order) => {
  console.log("Edit order:", order);
  // Implement edit order functionality
  // You can open a modal or navigate to edit page
  alert(`Edit order: ${order.orderId || order.orderNumber}`);
};

const refreshData = () => {
  loading.value = true;
  // The real-time listeners will automatically update the data
  setTimeout(() => {
    loading.value = false;
  }, 1000);
};

// Clean up listeners
const cleanupListeners = () => {
  if (ordersUnsubscribe) {
    ordersUnsubscribe();
  }
  if (deliveriesUnsubscribe) {
    deliveriesUnsubscribe();
  }
  if (retailersUnsubscribe) {
    retailersUnsubscribe();
  }
};

// Initialize on component mount
onMounted(() => {
  setupRealTimeListeners();
});

// Clean up on component unmount
onUnmounted(() => {
  cleanupListeners();
});
</script>

<style scoped>
.orders-management {
  display: flex;
  flex-direction: column;
  gap: 2rem;
}

.order-stats-cards {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
  gap: 1.5rem;
}

.order-stat-card {
  background: #f8fafc;
  border-radius: 12px;
  padding: 1.5rem;
  border: 1px solid #e2e8f0;
  transition: all 0.3s ease;
}

.order-stat-card:hover {
  transform: translateY(-2px);
  box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
}

.order-stat-card h3 {
  margin: 0 0 1rem 0;
  color: #1a202c;
  font-size: 1.1rem;
  font-weight: 600;
}

.order-stat-card .count {
  font-size: 2.5rem;
  margin-bottom: 1rem;
  color: #1a202c;
  font-weight: 700;
}

.order-breakdown {
  display: flex;
  flex-direction: column;
  gap: 0.25rem;
}

.order-breakdown small {
  color: #718096;
  font-size: 0.8rem;
}

.revenue-subtitle {
  color: #718096;
  font-size: 0.8rem;
}

/* Filters Section */
.filters-section {
  display: grid;
  grid-template-columns: 1fr 1fr auto;
  gap: 1.5rem;
  align-items: end;
  background: white;
  border-radius: 12px;
  border: 1px solid #e2e8f0;
  padding: 1.5rem;
}

.filter-container {
  display: flex;
  flex-direction: column;
  gap: 1rem;
}

.filter-header {
  margin-bottom: 0.5rem;
}

.filter-header h3 {
  margin: 0;
  color: #1a202c;
  font-size: 1rem;
  font-weight: 600;
}

.filter-controls {
  display: flex;
  gap: 1rem;
}

.date-input-group,
.retailer-input-group {
  display: flex;
  flex-direction: column;
  gap: 0.5rem;
  flex: 1;
}

.date-input-group label,
.retailer-input-group label {
  color: #4a5568;
  font-size: 0.9rem;
  font-weight: 500;
}

.date-input {
  padding: 0.75rem;
  border: 1px solid #e2e8f0;
  border-radius: 8px;
  font-size: 0.9rem;
  color: #1a202c;
  background: white;
  transition: all 0.3s ease;
}

.date-input:focus {
  outline: none;
  border-color: #FF7828;
  box-shadow: 0 0 0 3px rgba(255, 120, 40, 0.1);
}

.retailer-select {
  padding: 0.75rem;
  border: 1px solid #e2e8f0;
  border-radius: 8px;
  font-size: 0.9rem;
  color: #1a202c;
  background: white;
  transition: all 0.3s ease;
  cursor: pointer;
}

.retailer-select:focus {
  outline: none;
  border-color: #FF7828;
  box-shadow: 0 0 0 3px rgba(255, 120, 40, 0.1);
}

.filter-actions {
  display: flex;
  align-items: end;
  height: fit-content;
}

.clear-filters-btn {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  padding: 0.75rem 1rem;
  background: #718096;
  color: white;
  border: none;
  border-radius: 8px;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.3s ease;
  font-size: 0.9rem;
  white-space: nowrap;
}

.clear-filters-btn:hover {
  background: #4a5568;
  transform: translateY(-1px);
}

/* Active Filters Info */
.active-filters-info {
  margin-top: -1rem;
}

.filters-active-badge {
  display: inline-flex;
  align-items: center;
  padding: 0.75rem 1.5rem;
  background: #e6fffa;
  color: #065f46;
  border-radius: 12px;
  font-size: 0.9rem;
  font-weight: 500;
  border: 1px solid #81e6d9;
}

.filter-tag {
  background: #ff7828;
  color: white;
  padding: 0.25rem 0.75rem;
  border-radius: 16px;
  font-size: 0.8rem;
  margin: 0 0.5rem;
  font-weight: 600;
}

/* Orders Table */
.orders-table-container {
  background: white;
  border-radius: 12px;
  border: 1px solid #e2e8f0;
  overflow: hidden;
}
.table-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 1.5rem;
  border-bottom: 1px solid #e2e8f0;
  background: #f8fafc;
}

.table-header h3 {
  margin: 0;
  color: #1a202c;
  font-size: 1.25rem;
  font-weight: 600;
}

.refresh-btn {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  padding: 0.5rem 1rem;
  background: #FF7828;
  color: white;
  border: none;
  border-radius: 8px;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.3s ease;
  font-size: 0.9rem;
}

.refresh-btn:hover {
  background: #e6691f;
  transform: translateY(-1px);
}

.orders-table {
  width: 100%;
  border-collapse: collapse;
}

.orders-table th,
.orders-table td {
  padding: 1rem;
  text-align: left;
  border-bottom: 1px solid #e2e8f0;
}

.orders-table th {
  background: #f8fafc;
  color: #4a5568;
  font-weight: 600;
  font-size: 0.9rem;
  text-transform: uppercase;
  letter-spacing: 0.5px;
}

.orders-table td {
  color: #4a5568;
  font-size: 0.95rem;
}

.order-id {
  font-weight: 600;
  color: #1a202c;
  font-family: 'Monaco', 'Consolas', monospace;
  font-size: 0.85rem;
}

.customer-name, .retailer-name {
  font-weight: 500;
  color: #1a202c;
}

.order-item {
  color: #718096;
  font-size: 0.9rem;
}

.order-amount {
  font-weight: 600;
  color: #10b981;
}

.status-badge {
  padding: 0.4rem 0.8rem;
  border-radius: 20px;
  font-size: 0.75rem;
  font-weight: 600;
  text-transform: capitalize;
  letter-spacing: 0.5px;
  display: inline-block;
  min-width: 80px;
  text-align: center;
}

.status-badge.pending {
  background: #fef3c7;
  color: #d97706;
}

.status-badge.accepted {
  background: #dbeafe;
  color: #1d4ed8;
}

.status-badge.assigned {
  background: #e9d5ff;
  color: #7e22ce;
}

.status-badge.ready-for-pickup {
  background: #fef3c7;
  color: #d97706;
}

.status-badge.in-transit {
  background: #e9d5ff;
  color: #7e22ce;
}

.status-badge.delivered {
  background: #d1fae5;
  color: #065f46;
}

.status-badge.cancelled {
  background: #fee2e2;
  color: #dc2626;
}

.status-badge.processing {
  background: #dbeafe;
  color: #1d4ed8;
}

.order-actions {
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
  transform: translateY(-1px);
}

.view-btn:hover {
  background: #3b82f6;
  color: white;
  border-color: #3b82f6;
}

.edit-btn:hover {
  background: #f59e0b;
  color: white;
  border-color: #f59e0b;
}

/* No Orders State */
.no-orders {
  text-align: center;
  padding: 3rem;
}

.no-data {
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 1rem;
  color: #718096;
}

.no-data h3 {
  margin: 0;
  color: #4a5568;
  font-size: 1.25rem;
}

.no-data p {
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

.large-modal {
  background: white;
  border-radius: 20px;
  width: 90%;
  max-width: 800px;
  max-height: 90vh;
  overflow-y: auto;
  box-shadow: 0 20px 60px rgba(0, 0, 0, 0.2);
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
  font-size: 1.5rem;
  font-weight: 700;
}

.close-btn {
  background: none;
  border: none;
  color: #718096;
  cursor: pointer;
  padding: 0.5rem;
  border-radius: 6px;
  transition: all 0.3s ease;
}

.close-btn:hover {
  background: #f7fafc;
  color: #1a202c;
}

.modal-content {
  padding: 2rem;
}

.order-details-grid {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 2rem;
}

.detail-section {
  display: flex;
  flex-direction: column;
  gap: 1rem;
}

.detail-section h4 {
  margin: 0 0 0.5rem 0;
  color: #1a202c;
  font-size: 1.1rem;
  font-weight: 600;
}

.detail-item {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 0.75rem 0;
  border-bottom: 1px solid #f1f5f9;
}

.detail-label {
  color: #718096;
  font-weight: 500;
}

.detail-value {
  color: #1a202c;
  font-weight: 600;
}

.order-items {
  display: flex;
  flex-direction: column;
  gap: 1rem;
}

.item-list {
  display: flex;
  flex-direction: column;
  gap: 0.5rem;
}

.item {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 0.75rem;
  background: #f8fafc;
  border-radius: 8px;
}

.item-name {
  color: #1a202c;
  font-weight: 500;
}

.item-price {
  color: #10b981;
  font-weight: 600;
}

.delivery-fee {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 0.75rem 0;
  border-top: 1px solid #e2e8f0;
  margin-top: 0.5rem;
}

.fee-label {
  color: #718096;
  font-weight: 500;
}

.fee-amount {
  color: #1a202c;
  font-weight: 600;
}

.modal-buttons {
  display: flex;
  gap: 1rem;
  justify-content: flex-end;
  padding: 1.5rem 2rem;
  border-top: 1px solid #e2e8f0;
  background: #f8fafc;
}

.modal-btn-confirm {
  padding: 0.75rem 2rem;
  border: none;
  border-radius: 10px;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.3s ease;
  font-size: 0.95rem;
  background: #FF7828;
  color: white;
  box-shadow: 0 4px 12px rgba(255, 120, 40, 0.3);
}

.modal-btn-confirm:hover {
  background: #e6691f;
  transform: translateY(-1px);
  box-shadow: 0 6px 16px rgba(255, 120, 40, 0.4);
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
@keyframes spin {
  from { transform: rotate(0deg); }
  to { transform: rotate(360deg); }
}

/* Responsive Design */
@media (max-width: 1024px) {
  .order-details-grid {
    grid-template-columns: 1fr;
  }
  
  .filters-section {
    grid-template-columns: 1fr;
    gap: 1rem;
  }
}

@media (max-width: 768px) {
  .order-stats-cards {
    grid-template-columns: 1fr;
  }
  
  .filter-controls {
    flex-direction: column;
  }
  
  .filters-section {
    grid-template-columns: 1fr;
  }
  
  .orders-table-container {
    overflow-x: auto;
  }
  
  .orders-table {
    min-width: 900px;
  }
  
  .table-header {
    flex-direction: column;
    gap: 1rem;
    align-items: flex-start;
  }
  
  .large-modal {
    width: 95%;
    margin: 1rem;
  }
  
  .modal-content {
    padding: 1rem;
  }
  
  .modal-buttons {
    flex-direction: column;
  }
  
  .filters-active-badge {
    flex-direction: column;
    align-items: flex-start;
    gap: 0.5rem;
  }
  
  .filter-tag {
    margin: 0.25rem 0;
  }
}
</style>