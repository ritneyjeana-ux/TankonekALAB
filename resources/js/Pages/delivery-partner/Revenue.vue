<template>
  <div class="revenue-dashboard">
    <!-- Content Section -->
    <section class="content-section">
      <div class="tab-content">
        <div class="content-header">
          <h2>Rider Revenue Report</h2>
          <p>Detailed analysis of your 67% rider share from delivery fees</p>
          <div class="report-actions">
            <!-- Date Filter -->
            <div class="date-filter">
              <div class="filter-group">
                <label>From Date:</label>
                <input type="date" v-model="startDate" @change="filterByDate" class="date-input">
              </div>
              <div class="filter-group">
                <label>To Date:</label>
                <input type="date" v-model="endDate" @change="filterByDate" class="date-input">
              </div>
              <button class="reset-filter" @click="resetDateFilter">Reset</button>
            </div>
            <button class="print-btn" @click="printReport">
              <svg width="16" height="16" viewBox="0 0 24 24" fill="none">
                <path d="M6 9V2h12v7M6 18H4a2 2 0 01-2-2v-5a2 2 0 012-2h16a2 2 0 012 2v5a2 2 0 01-2 2h-2" stroke="currentColor" stroke-width="2"/>
                <path d="M6 14h12v8H6z" stroke="currentColor" stroke-width="2"/>
              </svg>
              Print Report
            </button>
          </div>
        </div>

        <!-- Loading State -->
        <div v-if="loading" class="loading-state">
          <svg width="48" height="48" viewBox="0 0 24 24" fill="none" class="loading-spinner">
            <path d="M12 2v4m0 12v4M4.93 4.93l2.83 2.83m8.48 8.48l2.83 2.83M2 12h4m12 0h4M4.93 19.07l2.83-2.83m8.48-8.48l2.83-2.83" stroke="#FF7828" stroke-width="2" stroke-linecap="round"/>
          </svg>
          <p>Loading revenue data...</p>
        </div>

        <!-- Debug Info -->
        <div v-else class="debug-info">
          <p><strong>Debug Info:</strong> {{ allDeliveries.length }} total deliveries | {{ filteredDeliveries.length }} filtered | Rider ID: {{ currentRiderId }}</p>
          <p><strong>Real-time Status:</strong> {{ realTimeStatus }}</p>
          <p><strong>Last Update:</strong> {{ lastUpdateTime }}</p>
        </div>

        <!-- Revenue Cards -->
        <div v-if="!loading" class="revenue-grid">
          <!-- Today's Earnings Card -->
          <div class="revenue-card todays-earnings-card">
            <div class="revenue-card-header">
              <h3>💰 Today's Earnings</h3>
              <span class="revenue-percentage">Live Updates</span>
            </div>
            <div class="revenue-stats">
              <div class="revenue-stat">
                <span class="stat-label">Rider Earnings</span>
                <span class="stat-value">₱{{ todaysEarnings }}</span>
              </div>
              <div class="revenue-stat">
                <span class="stat-label">Completed Deliveries</span>
                <span class="stat-value">{{ todaysCompletedDeliveries }}</span>
              </div>
              <div class="revenue-stat">
                <span class="stat-label">Average per Delivery</span>
                <span class="stat-value">₱{{ averageTodayEarning }}</span>
              </div>
            </div>
          </div>

          <!-- Total Revenue Card -->
          <div class="revenue-card total-revenue-card">
            <div class="revenue-card-header">
              <h3>📊 Total Revenue</h3>
              <span class="revenue-percentage">All Time</span>
            </div>
            <div class="revenue-stats">
              <div class="revenue-stat">
                <span class="stat-label">Total Rider Earnings</span>
                <span class="stat-value">₱{{ totalEarnings }}</span>
              </div>
              <div class="revenue-stat">
                <span class="stat-label">Total Deliveries</span>
                <span class="stat-value">{{ totalCompletedDeliveries }}</span>
              </div>
              <div class="revenue-stat total">
                <span class="stat-label">Average per Delivery</span>
                <span class="stat-value highlight">₱{{ averageEarning }}</span>
              </div>
            </div>
          </div>
        </div>

        <!-- Revenue Report Table -->
        <div class="revenue-report-table" v-if="!loading">
          <div class="table-header">
            <h3>Rider Revenue Report</h3>
            <p class="filter-info" v-if="isFiltered">
              Showing data from {{ formatDisplayDate(startDate) }} to {{ formatDisplayDate(endDate) }}
            </p>
            <p class="realtime-indicator" v-if="!isFiltered">
              🔄 Real-time Updates Active (Last update: {{ lastUpdateTime }})
            </p>
          </div>
          
          <table class="revenue-table">
            <thead>
              <tr>
                <th>Date</th>
                <th>Order ID</th>
                <th>Customer</th>
                <th>Gas Type</th>
                <th>Delivery Address</th>
                <th>Distance</th>
                <th>Delivery Fee</th>
                <th>Rider Earnings</th>
                <th>Status</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="delivery in filteredDeliveries" :key="delivery.id">
                <td>{{ formatDate(delivery.date) }}</td>
                <td class="order-id">{{ delivery.orderId }}</td>
                <td>{{ delivery.customerName }}</td>
                <td>{{ delivery.gasType }}</td>
                <td>{{ delivery.deliveryAddress }}</td>
                <td class="distance">{{ delivery.distance }} km</td>
                <td class="delivery-fee">₱{{ delivery.deliveryFee }}</td>
                <td class="delivery-fee">₱{{ delivery.riderShare }}</td>
                <td>
                  <span class="status-badge" :class="delivery.status">{{ delivery.status }}</span>
                </td>
              </tr>
              <tr v-if="filteredDeliveries.length === 0">
                <td colspan="9" class="no-data">No delivery records available for selected date range</td>
              </tr>
            </tbody>
            <tfoot>
              <tr class="summary-row">
                <td colspan="7" class="total-label">Total Rider Earnings</td>
                <td class="grand-total">₱{{ totalEarnings }}</td>
                <td></td>
              </tr>
            </tfoot>
          </table>
        </div>

        <!-- Print-only content (hidden on screen) -->
        <div class="print-only-content">
          <!-- This will be used for printing with custom styling -->
        </div>
      </div>
    </section>
  </div>
</template>

<script setup>
import { ref, onMounted, computed, onUnmounted } from "vue";

// FIREBASE
import { db, auth } from "@/firebase";
import { 
  collection, 
  query, 
  where, 
  onSnapshot,
  orderBy,
  getDocs,
  Timestamp
} from "firebase/firestore";
import { onAuthStateChanged } from "firebase/auth";

const loading = ref(true);
const realTimeStatus = ref("Setting up...");
const lastUpdateTime = ref("Never");

// Date Filter
const startDate = ref('');
const endDate = ref('');
const isFiltered = ref(false);

// Deliveries Data
const allDeliveries = ref([]);
const filteredDeliveries = ref([]);

// Current rider ID
const currentRiderId = ref('');

// Unsubscribe functions for real-time listeners
const unsubscribeFunctions = ref([]);

// Computed Properties
const currentMonth = computed(() => {
  return new Date().toLocaleString('default', { month: 'long', year: 'numeric' });
});

const currentDate = computed(() => {
  return new Date().toLocaleDateString('en-PH', {
    year: 'numeric',
    month: 'long',
    day: 'numeric',
    hour: '2-digit',
    minute: '2-digit'
  });
});

const RIDER_DELIVERY_SHARE_RATE = 0.67;

const calculateRiderShare = (deliveryFee) => {
  return parseFloat(deliveryFee || 0) * RIDER_DELIVERY_SHARE_RATE;
};

// Today's earnings (only completed deliveries)
const todaysEarnings = computed(() => {
  const today = new Date().toDateString();
  const todayDeliveries = filteredDeliveries.value.filter(delivery => {
    const deliveryDate = new Date(delivery.date).toDateString();
    return deliveryDate === today && delivery.status === 'delivered';
  });
  
  const todayEarnings = todayDeliveries.reduce((sum, delivery) => {
    return sum + calculateRiderShare(delivery.deliveryFee);
  }, 0);
  
  return todayEarnings.toFixed(2);
});

// Today's completed deliveries count
const todaysCompletedDeliveries = computed(() => {
  const today = new Date().toDateString();
  return filteredDeliveries.value.filter(delivery => {
    const deliveryDate = new Date(delivery.date).toDateString();
    return deliveryDate === today && delivery.status === 'delivered';
  }).length;
});

// Average earning per delivery today
const averageTodayEarning = computed(() => {
  if (todaysCompletedDeliveries.value === 0) return "0.00";
  const average = parseFloat(todaysEarnings.value) / todaysCompletedDeliveries.value;
  return average.toFixed(2);
});

// Total earnings (only completed deliveries)
const totalEarnings = computed(() => {
  const total = filteredDeliveries.value
    .filter(delivery => delivery.status === 'delivered')
    .reduce((sum, delivery) => {
      return sum + calculateRiderShare(delivery.deliveryFee);
    }, 0);
  return total.toFixed(2);
});

// Total completed deliveries count
const totalCompletedDeliveries = computed(() => {
  return filteredDeliveries.value.filter(delivery => delivery.status === 'delivered').length;
});

// Average earning per delivery (all time)
const averageEarning = computed(() => {
  if (totalCompletedDeliveries.value === 0) return "0.00";
  const average = parseFloat(totalEarnings.value) / totalCompletedDeliveries.value;
  return average.toFixed(2);
});

// Update timestamp
const updateTimestamp = () => {
  lastUpdateTime.value = new Date().toLocaleTimeString('en-PH', {
    hour: '2-digit',
    minute: '2-digit',
    second: '2-digit'
  });
};

// Fetch rider data and deliveries
const fetchRiderData = () => {
  const user = auth.currentUser;
  if (!user) {
    console.log("No rider logged in");
    loading.value = false;
    return;
  }

  currentRiderId.value = user.uid;
  console.log("🏍️ Fetching revenue data for rider:", user.uid);

  // Fetch initial deliveries from both collections
  fetchInitialDeliveries(user.uid);
};

// Fetch initial deliveries from both orders and deliveries collections
const fetchInitialDeliveries = async (riderId) => {
  try {
    console.log("🔄 Fetching initial deliveries for rider:", riderId);
    realTimeStatus.value = "Fetching initial data...";
    
    const deliveries = [];

    // SIMPLIFIED QUERY: Only filter by riderId, no ordering to avoid index issues
    const ordersQuery = query(
      collection(db, "orders"),
      where("riderId", "==", riderId)
    );

    const ordersSnapshot = await getDocs(ordersQuery);
    console.log(`📦 Orders found: ${ordersSnapshot.size}`);

    // Process orders collection
    ordersSnapshot.forEach((doc) => {
      const order = doc.data();
      const deliveryData = createDeliveryData(order, doc.id, 'orders');
      if (deliveryData) {
        deliveries.push(deliveryData);
      }
    });

    // SIMPLIFIED QUERY: Only filter by riderId, no ordering to avoid index issues
    const deliveriesQuery = query(
      collection(db, "deliveries"),
      where("riderId", "==", riderId)
    );

    const deliveriesSnapshot = await getDocs(deliveriesQuery);
    console.log(`🚚 Deliveries found: ${deliveriesSnapshot.size}`);

    // Process deliveries collection
    deliveriesSnapshot.forEach((doc) => {
      const delivery = doc.data();
      const deliveryData = createDeliveryData(delivery, doc.id, 'deliveries');
      if (deliveryData) {
        deliveries.push(deliveryData);
      }
    });

    // Sort by date manually (newest first)
    deliveries.sort((a, b) => new Date(b.date) - new Date(a.date));

    allDeliveries.value = deliveries;
    filteredDeliveries.value = deliveries;
    loading.value = false;
    
    console.log("✅ Initial deliveries loaded for current rider:", deliveries.length);
    updateTimestamp();
    
    // Set up real-time listeners
    setupRealTimeListeners(riderId);
    
  } catch (error) {
    console.error("❌ Error fetching initial deliveries:", error);
    realTimeStatus.value = "Error: " + error.message;
    loading.value = false;
  }
};

// Create delivery data object from document
const createDeliveryData = (data, docId, collectionType) => {
  try {
    console.log(`📄 Processing ${collectionType} document:`, docId, data);

    // Calculate amounts based on collection type
    let deliveryFee = 0;
    let distance = 0;

    if (collectionType === 'orders') {
      deliveryFee = parseFloat(data.deliveryFee || 0);
      distance = parseFloat(data.distance || 0);
    } else if (collectionType === 'deliveries') {
      deliveryFee = parseFloat(data.deliveryFee || 0);
      distance = parseFloat(data.distance || 0);
    }

    // Handle date conversion properly
    let deliveryDate = new Date();
    if (data.createdAt) {
      if (data.createdAt instanceof Timestamp) {
        deliveryDate = data.createdAt.toDate();
      } else if (data.createdAt.toDate) {
        deliveryDate = data.createdAt.toDate();
      }
    } else if (data.updatedAt) {
      if (data.updatedAt instanceof Timestamp) {
        deliveryDate = data.updatedAt.toDate();
      } else if (data.updatedAt.toDate) {
        deliveryDate = data.updatedAt.toDate();
      }
    } else if (data.acceptedAt) {
      if (data.acceptedAt instanceof Timestamp) {
        deliveryDate = data.acceptedAt.toDate();
      } else if (data.acceptedAt.toDate) {
        deliveryDate = data.acceptedAt.toDate();
      }
    } else if (data.deliveredAt) {
      if (data.deliveredAt instanceof Timestamp) {
        deliveryDate = data.deliveredAt.toDate();
      } else if (data.deliveredAt.toDate) {
        deliveryDate = data.deliveredAt.toDate();
      }
    }

    const deliveryData = {
      id: docId,
      orderId: data.orderId || data.displayId || data.orderNumber || `ORD-${docId.slice(-8)}`,
      date: deliveryDate,
      customerName: data.customerName || data.to || 'Customer',
      gasType: data.gasType || '50kg LPG',
      deliveryAddress: data.deliveryAddress || data.customerAddress || data.address || 'Address not specified',
      distance: distance.toFixed(2),
      deliveryFee: deliveryFee.toFixed(2),
      riderShare: calculateRiderShare(deliveryFee).toFixed(2),
      status: data.orderStatus || data.status || 'pending',
      riderId: data.riderId,
      collectionType: collectionType
    };

    console.log(`✅ Created delivery data:`, deliveryData);
    return deliveryData;

  } catch (error) {
    console.error("❌ Error creating delivery data:", error, data);
    return null;
  }
};

// Set up real-time listeners for both collections
const setupRealTimeListeners = (riderId) => {
  console.log("🔊 Setting up real-time listeners for rider:", riderId);
  realTimeStatus.value = "Setting up real-time listeners...";

  try {
    // SIMPLIFIED LISTENER: Only filter by riderId
    const ordersQuery = query(
      collection(db, "orders"),
      where("riderId", "==", riderId)
    );

    const ordersUnsubscribe = onSnapshot(ordersQuery, 
      (snapshot) => {
        console.log("📦 Orders collection real-time update");
        realTimeStatus.value = "Receiving orders update...";
        handleRealtimeUpdate(snapshot, 'orders');
      },
      (error) => {
        console.error("❌ Error in orders real-time listener:", error);
        realTimeStatus.value = "Orders listener error";
      }
    );

    // SIMPLIFIED LISTENER: Only filter by riderId
    const deliveriesQuery = query(
      collection(db, "deliveries"),
      where("riderId", "==", riderId)
    );

    const deliveriesUnsubscribe = onSnapshot(deliveriesQuery, 
      (snapshot) => {
        console.log("🚚 Deliveries collection real-time update");
        realTimeStatus.value = "Receiving deliveries update...";
        handleRealtimeUpdate(snapshot, 'deliveries');
      },
      (error) => {
        console.error("❌ Error in deliveries real-time listener:", error);
        realTimeStatus.value = "Deliveries listener error";
      }
    );

    // Store unsubscribe functions
    unsubscribeFunctions.value = [ordersUnsubscribe, deliveriesUnsubscribe];
    
    console.log("✅ Real-time listeners setup complete");
    realTimeStatus.value = "Real-time active - listening for changes";

  } catch (error) {
    console.error("❌ Error setting up real-time listeners:", error);
    realTimeStatus.value = "Setup error: " + error.message;
  }
};

// Handle real-time updates from both collections
const handleRealtimeUpdate = (snapshot, collectionType) => {
  let updatesCount = 0;
  
  console.log(`🔄 Processing ${collectionType} real-time update with ${snapshot.docChanges().length} changes`);
  
  snapshot.docChanges().forEach((change) => {
    const data = change.doc.data();
    console.log(`📝 ${change.type} document in ${collectionType}:`, change.doc.id, data);

    if (change.type === 'added' || change.type === 'modified') {
      const deliveryData = createDeliveryData(data, change.doc.id, collectionType);

      if (deliveryData) {
        // Check if delivery already exists using multiple identifiers
        const existingIndex = allDeliveries.value.findIndex(
          d => (d.id === change.doc.id) || 
               (d.orderId === deliveryData.orderId) ||
               (d.collectionType === collectionType && d.id === change.doc.id)
        );

        if (existingIndex !== -1) {
          // Update existing delivery
          allDeliveries.value.splice(existingIndex, 1, deliveryData);
          console.log("🔄 Updated delivery:", deliveryData.orderId);
        } else {
          // Add new delivery
          allDeliveries.value.unshift(deliveryData); // Add to beginning for newest first
          console.log("✅ Added new delivery:", deliveryData.orderId);
        }
        updatesCount++;
      }
    } else if (change.type === 'removed') {
      // Remove delivery
      const removedId = change.doc.id;
      allDeliveries.value = allDeliveries.value.filter(
        d => d.id !== removedId
      );
      console.log("🗑️ Removed delivery:", removedId);
      updatesCount++;
    }
  });

  if (updatesCount > 0) {
    console.log(`🔄 Applied ${updatesCount} real-time updates to allDeliveries`);
    // Re-apply date filter and sort
    applyDateFilterAndSort();
    updateTimestamp();
    realTimeStatus.value = `Updated ${updatesCount} records`;
  } else {
    console.log("ℹ️ No updates applied from real-time listener");
    realTimeStatus.value = "No changes detected";
  }
};

// Apply date filter and sort deliveries
const applyDateFilterAndSort = () => {
  console.log("📊 Applying date filter and sort to", allDeliveries.value.length, "deliveries");
  
  if (!startDate.value && !endDate.value) {
    filteredDeliveries.value = [...allDeliveries.value].sort(
      (a, b) => new Date(b.date) - new Date(a.date)
    );
    isFiltered.value = false;
    console.log("📊 Showing all deliveries:", filteredDeliveries.value.length);
  } else {
    filterByDate();
  }
};

// Filter deliveries by date
const filterByDate = () => {
  if (!startDate.value && !endDate.value) {
    filteredDeliveries.value = [...allDeliveries.value].sort(
      (a, b) => new Date(b.date) - new Date(a.date)
    );
    isFiltered.value = false;
    return;
  }

  const start = startDate.value ? new Date(startDate.value) : null;
  const end = endDate.value ? new Date(endDate.value) : null;

  if (end) {
    end.setHours(23, 59, 59, 999); // Include entire end date
  }

  filteredDeliveries.value = allDeliveries.value.filter(delivery => {
    const deliveryDate = new Date(delivery.date);
    
    if (start && end) {
      return deliveryDate >= start && deliveryDate <= end;
    } else if (start) {
      return deliveryDate >= start;
    } else if (end) {
      return deliveryDate <= end;
    }
    
    return true;
  });

  // Sort filtered results
  filteredDeliveries.value.sort((a, b) => new Date(b.date) - new Date(a.date));
  isFiltered.value = true;
  
  console.log("📊 Filtered deliveries:", filteredDeliveries.value.length);
};

// Reset date filter
const resetDateFilter = () => {
  startDate.value = '';
  endDate.value = '';
  applyDateFilterAndSort();
};

// Format date for display
const formatDate = (date) => {
  if (!date) return 'N/A';
  
  try {
    const d = date.toDate ? date.toDate() : new Date(date);
    return d.toLocaleDateString('en-PH', {
      month: 'short',
      day: 'numeric',
      year: 'numeric',
      hour: '2-digit',
      minute: '2-digit'
    });
  } catch (error) {
    console.error("Error formatting date:", error, date);
    return 'Invalid Date';
  }
};

// Format date for filter display
const formatDisplayDate = (dateString) => {
  if (!dateString) return 'N/A';
  return new Date(dateString).toLocaleDateString('en-PH');
};

// Print report function - UPDATED with gas retailer design
const printReport = () => {
  // Create a new window for printing
  const printWindow = window.open('', '_blank');
  
  // Calculate totals - ONLY FOR COMPLETED DELIVERIES
  const completedDeliveries = filteredDeliveries.value.filter(
    delivery => delivery.status === 'delivered' || delivery.status === 'completed'
  );
  
  const totalEarnings = completedDeliveries.reduce((sum, delivery) => {
    return sum + calculateRiderShare(delivery.deliveryFee);
  }, 0).toFixed(2);
  
  const totalDeliveries = completedDeliveries.length;
  
  // Start building the HTML content - USING GAS RETAILER DESIGN
  let printContent = `
    <!DOCTYPE html>
    <html>
    <head>
      <title>Revenue Report</title>
      <style>
        body {
          font-family: Arial, sans-serif;
          margin: 20px;
          color: #000000;
          line-height: 1.4;
        }
        .print-header {
          text-align: center;
          margin-bottom: 20px;
        }
        .report-title {
          font-size: 24px;
          font-weight: bold;
          margin-bottom: 20px;
          color: #000000;
        }
        .revenue-table {
          width: 100%;
          border-collapse: collapse;
          margin-top: 10px;
          font-size: 12px;
          color: #000000;
        }
        .revenue-table th {
          background-color: #f0f0f0;
          border: 1px solid #000000;
          padding: 8px;
          text-align: left;
          font-weight: bold;
          color: #000000;
        }
        .revenue-table td {
          border: 1px solid #000000;
          padding: 8px;
          color: #000000;
        }
        .order-id {
          font-family: 'Courier New', monospace;
          font-weight: 600;
        }
        .distance {
          text-align: center;
        }
        .delivery-fee {
          color: #000000;
          font-weight: 600;
        }
        .status-badge {
          background: #f0f0f0;
          color: #000000;
          padding: 4px 8px;
          border-radius: 12px;
          font-size: 10px;
          font-weight: 600;
          border: 1px solid #000000;
        }
        .summary-row {
          font-weight: bold;
          background-color: #f0f0f0;
        }
        .total-label {
          text-align: right;
          padding-right: 15px;
        }
        .total-amount {
          color: #000000;
          font-weight: bold;
        }
        @media print {
          body {
            margin: 0;
            padding: 15px;
          }
        }
      </style>
    </head>
    <body>
      <div class="print-header">
        <div class="report-title">Revenue Report</div>
      </div>
      
      <table class="revenue-table">
        <thead>
          <tr>
            <th>Date</th>
            <th>Order ID</th>
            <th>Customer</th>
            <th>Gas Type</th>
            <th>Delivery Address</th>
            <th>Distance</th>
            <th>Delivery Fee</th>
            <th>Rider Earnings</th>
            <th>Status</th>
          </tr>
        </thead>
        <tbody>
  `;
  
  // Add delivery rows - ONLY COMPLETED DELIVERIES
  completedDeliveries.forEach(delivery => {
    const formattedDate = formatDate(delivery.date);
    printContent += `
          <tr>
            <td>${formattedDate}</td>
            <td class="order-id">${delivery.orderId}</td>
            <td>${delivery.customerName}</td>
            <td>${delivery.gasType}</td>
            <td>${delivery.deliveryAddress}</td>
            <td class="distance">${delivery.distance} km</td>
            <td class="delivery-fee">₱${delivery.deliveryFee}</td>
            <td class="delivery-fee">₱${delivery.riderShare}</td>
            <td><span class="status-badge">Delivered</span></td>
          </tr>
    `;
  });
  
  // Add footer with totals
  printContent += `
          <tr class="summary-row">
            <td colspan="7" class="total-label">Total Rider Earnings</td>
            <td class="total-amount">₱${totalEarnings}</td>
            <td></td>
          </tr>
        </tbody>
      </table>
    </body>
    </html>
  `;
  
  // Write the content to the new window and trigger print
  printWindow.document.write(printContent);
  printWindow.document.close();
  
  // Wait for content to load before printing
  printWindow.onload = function() {
    printWindow.focus();
    printWindow.print();
    printWindow.close();
  };
};

// Clean up listeners when component is unmounted
onUnmounted(() => {
  unsubscribeFunctions.value.forEach(unsubscribe => {
    if (unsubscribe) unsubscribe();
  });
  console.log("🧹 Cleaned up real-time listeners");
});

// Initialize on component mount
onMounted(() => {
  onAuthStateChanged(auth, (user) => {
    if (user) {
      console.log("👤 Rider authenticated:", user.uid);
      fetchRiderData();
    } else {
      loading.value = false;
      console.log("❌ No rider logged in");
    }
  });
});
</script>

<style scoped>
/* Debug info styling */
.debug-info {
  background: #fef3c7;
  border: 1px solid #f59e0b;
  border-radius: 8px;
  padding: 0.75rem;
  margin-bottom: 1rem;
  font-size: 0.8rem;
  color: #92400e;
}

.debug-info p {
  margin: 0;
  line-height: 1.4;
}

/* Add real-time indicator style */
.realtime-indicator {
  margin: 0.5rem 0 0 0;
  color: #10b981;
  font-size: 0.8rem;
  font-weight: 600;
  background: rgba(16, 185, 129, 0.1);
  padding: 0.4rem 0.8rem;
  border-radius: 6px;
  display: inline-block;
}

/* Print-only content - hidden on screen */
.print-only-content {
  display: none;
}

/* Keep all existing styles below */
.revenue-dashboard {
  font-family: 'Inter', 'Poppins', sans-serif;
  padding: 2rem;
  background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
  min-height: 100vh;
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
  display: flex;
  justify-content: space-between;
  align-items: flex-start;
  flex-wrap: wrap;
  gap: 1rem;
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

.report-actions {
  display: flex;
  gap: 1rem;
  align-items: flex-end;
  flex-wrap: wrap;
}

/* Date Filter Styles */
.date-filter {
  display: flex;
  gap: 1rem;
  align-items: flex-end;
  flex-wrap: wrap;
}

.filter-group {
  display: flex;
  flex-direction: column;
  gap: 0.5rem;
}

.filter-group label {
  font-size: 0.8rem;
  font-weight: 600;
  color: #4a5568;
}

.date-input {
  padding: 0.5rem;
  border: 1px solid #e2e8f0;
  border-radius: 6px;
  font-size: 0.9rem;
  min-width: 140px;
}

.date-input:focus {
  outline: none;
  border-color: #FF7828;
  box-shadow: 0 0 0 3px rgba(255, 120, 40, 0.1);
}

.reset-filter {
  padding: 0.5rem 1rem;
  border: 1px solid #e2e8f0;
  border-radius: 6px;
  background: white;
  color: #718096;
  font-size: 0.8rem;
  font-weight: 500;
  cursor: pointer;
  transition: all 0.3s ease;
}

.reset-filter:hover {
  border-color: #FF7828;
  color: #FF7828;
}

.print-btn {
  display: flex;
  align-items: center;
  gap: 8px;
  padding: 0.75rem 1.5rem;
  border: 2px solid #FF7828;
  border-radius: 10px;
  background: white;
  color: #FF7828;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.3s ease;
  font-size: 0.9rem;
}

.print-btn:hover {
  background: #FF7828;
  color: white;
  transform: translateY(-1px);
  box-shadow: 0 4px 12px rgba(255, 120, 40, 0.3);
}

/* Revenue Grid */
.revenue-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
  gap: 1.5rem;
  margin-bottom: 2rem;
}

.revenue-card {
  background: #f8fafc;
  padding: 1.5rem;
  border-radius: 12px;
  border: 1px solid #e2e8f0;
  transition: all 0.3s ease;
}

.revenue-card:hover {
  border-color: #FF7828;
  box-shadow: 0 4px 12px rgba(255, 120, 40, 0.1);
  transform: translateY(-2px);
}

.revenue-card-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 1.5rem;
  padding-bottom: 1rem;
  border-bottom: 2px solid #e2e8f0;
}

.revenue-card-header h3 {
  margin: 0;
  color: #1a202c;
  font-size: 1.1rem;
  font-weight: 600;
}

.revenue-percentage {
  background: #FF7828;
  color: white;
  padding: 0.25rem 0.75rem;
  border-radius: 20px;
  font-size: 0.8rem;
  font-weight: 600;
}

.revenue-stats {
  display: flex;
  flex-direction: column;
  gap: 1rem;
}

.revenue-stat {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 0.75rem;
  background: white;
  border-radius: 8px;
  border: 1px solid #e2e8f0;
}

.revenue-stat.total {
  background: #f0fdf4;
  border-color: #dcfce7;
}

.stat-label {
  color: #4a5568;
  font-weight: 500;
}

.stat-value {
  color: #1a202c;
  font-weight: 600;
  font-size: 1.1rem;
}

.highlight {
  color: #FF7828;
}

/* Revenue Report Table */
.revenue-report-table {
  margin: 2rem 0;
  background: #f8fafc;
  border-radius: 12px;
  border: 1px solid #e2e8f0;
  overflow: hidden;
}

.table-header {
  padding: 1.5rem;
  background: white;
  border-bottom: 1px solid #e2e8f0;
}

.table-header h3 {
  margin: 0 0 0.5rem 0;
  color: #1a202c;
  font-size: 1.25rem;
  font-weight: 600;
}

.filter-info {
  margin: 0;
  color: #FF7828;
  font-size: 0.9rem;
  font-weight: 500;
  background: rgba(255, 120, 40, 0.1);
  padding: 0.5rem 1rem;
  border-radius: 6px;
  display: inline-block;
}

.revenue-table {
  width: 100%;
  border-collapse: collapse;
  background: white;
}

.revenue-table th,
.revenue-table td {
  padding: 1rem;
  text-align: left;
  border-bottom: 1px solid #e2e8f0;
}

.revenue-table th {
  background: #f1f5f9;
  color: #4a5568;
  font-weight: 600;
  font-size: 0.85rem;
  text-transform: uppercase;
  letter-spacing: 0.5px;
}

.revenue-table td {
  color: #4a5568;
  font-size: 0.9rem;
}

.order-id {
  font-family: 'Courier New', monospace;
  font-weight: 600;
  color: #1a202c;
}

.distance {
  text-align: center;
  font-weight: 600;
  color: #6b7280;
}

.delivery-fee {
  font-weight: 600;
  color: #22c55e;
}

.status-badge {
  padding: 0.25rem 0.75rem;
  border-radius: 20px;
  font-size: 0.8rem;
  font-weight: 600;
}

.status-badge.delivered,
.status-badge.completed {
  background: rgba(34, 197, 94, 0.1);
  color: #16a34a;
}

.status-badge.pending {
  background: rgba(251, 191, 36, 0.1);
  color: #d97706;
}

.status-badge.accepted {
  background: rgba(59, 130, 246, 0.1);
  color: #2563eb;
}

.status-badge.in-transit {
  background: rgba(168, 85, 247, 0.1);
  color: #9333ea;
}

.no-data {
  text-align: center;
  color: #718096;
  font-style: italic;
  padding: 2rem;
}

.revenue-table tfoot {
  background: #f1f5f9;
}

.summary-row {
  font-weight: 600;
}

.total-label {
  color: #1a202c;
  font-size: 1rem;
  text-align: right;
  padding-right: 1rem;
}

.grand-total {
  color: #FF7828;
  font-size: 1.1rem;
  font-weight: 700;
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

/* Print Styles */
@media print {
  /* Hide everything when printing from the main window */
  body * {
    visibility: hidden;
  }
  
  /* Only show specific elements if needed, but we're using new window */
  .revenue-dashboard,
  .content-section,
  .tab-content {
    display: none !important;
  }
}

/* Responsive Design */
@media (max-width: 768px) {
  .revenue-dashboard {
    padding: 1rem;
  }
  
  .content-header {
    flex-direction: column;
    gap: 1rem;
    align-items: flex-start;
  }
  
  .report-actions {
    width: 100%;
    justify-content: space-between;
  }
  
  .date-filter {
    width: 100%;
    justify-content: space-between;
  }
  
  .revenue-grid {
    grid-template-columns: 1fr;
  }
  
  .revenue-table {
    font-size: 0.8rem;
  }
  
  .revenue-table th,
  .revenue-table td {
    padding: 0.5rem;
  }
  
  .debug-info {
    font-size: 0.7rem;
    padding: 0.5rem;
  }
}
</style>