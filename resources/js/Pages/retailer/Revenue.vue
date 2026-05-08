<template>
  <div class="revenue-dashboard">
    <!-- Content Section -->
    <section class="content-section">
      <div class="tab-content">
        <div class="content-header">
          <h2>Revenue Report</h2>
          <p>Detailed analysis of your earnings from product sales</p>
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

        <!-- Revenue Cards -->
        <div v-else class="revenue-grid">
          <!-- Retailer Earnings Card -->
          <div class="revenue-card retailer-earnings-card">
            <div class="revenue-card-header">
              <h3>💰 Retailer Earnings</h3>
              <span class="revenue-percentage">Product Sales - ₱10/order</span>
            </div>
            <div class="revenue-stats">
              <div class="revenue-stat">
                <span class="stat-label">Today's Earnings</span>
                <span class="stat-value">₱{{ todaysRetailerEarnings }}</span>
              </div>
              <div class="revenue-stat">
                <span class="stat-label">Orders Completed</span>
                <span class="stat-value">{{ filteredOrders.length }}</span>
              </div>
              <div class="revenue-stat">
                <span class="stat-label">Average Earnings per Order</span>
                <span class="stat-value">₱{{ averageRetailerEarnings }}</span>
              </div>
            </div>
          </div>

          <!-- Product Revenue Card -->
          <div class="revenue-card product-revenue-card">
            <div class="revenue-card-header">
              <h3>⛽ Product Revenue</h3>
              <span class="revenue-percentage">100% Gross</span>
            </div>
            <div class="revenue-stats">
              <div class="revenue-stat">
                <span class="stat-label">Total Product Sales</span>
                <span class="stat-value">₱{{ totalProductRevenue }}</span>
              </div>
              <div class="revenue-stat">
                <span class="stat-label">Platform Commission</span>
                <span class="stat-value">₱{{ platformShare }}</span>
              </div>
              <div class="revenue-stat total">
                <span class="stat-label">Your Total Earnings</span>
                <span class="stat-value highlight">₱{{ totalRetailerEarnings }}</span>
              </div>
            </div>
          </div>
        </div>

        <!-- Revenue Report Table -->
        <div class="revenue-report-table" v-if="!loading">
          <div class="table-header">
            <h3>Revenue Report</h3>
            <p class="filter-info" v-if="isFiltered">
              Showing data from {{ formatDisplayDate(startDate) }} to {{ formatDisplayDate(endDate) }}
            </p>
            <p class="realtime-indicator" v-if="!isFiltered">
              🔄 Real-time Updates Active
            </p>
          </div>
          
          <table class="revenue-table">
            <thead>
              <tr>
                <th>Date</th>
                <th>Order ID</th>
                <th>Product</th>
                <th>Quantity</th>
                <th>Product Revenue</th>
                <th>Retailer Earnings</th>
                <th>Status</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="transaction in filteredTransactions" :key="transaction.id">
                <td>{{ formatDate(transaction.date) }}</td>
                <td class="order-id">{{ transaction.orderId }}</td>
                <td>{{ transaction.product }}</td>
                <td>{{ transaction.quantity }}</td>
                <td class="revenue-amount">₱{{ transaction.productRevenue }}</td>
                <td class="retailer-earnings">₱{{ transaction.retailerEarnings }}</td>
                <td>
                  <span class="status-badge delivered">Delivered</span>
                </td>
              </tr>
              <tr v-if="filteredTransactions.length === 0">
                <td colspan="7" class="no-data">No transactions available for selected date range</td>
              </tr>
            </tbody>
            <tfoot>
              <tr class="summary-row">
                <td colspan="4" class="total-label">Total Product Revenue</td>
                <td class="total-amount">₱{{ totalProductRevenue }}</td>
                <td class="total-earnings">₱{{ totalRetailerEarnings }}</td>
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
  doc,
  getDoc
} from "firebase/firestore";
import { onAuthStateChanged } from "firebase/auth";

const loading = ref(true);

// Date Filter
const startDate = ref('');
const endDate = ref('');
const isFiltered = ref(false);

// Revenue Data
const allTransactions = ref([]);
const filteredTransactions = ref([]);

// Current retailer ID
const currentRetailerId = ref('');

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

// Filtered orders based on date range
const filteredOrders = computed(() => {
  return filteredTransactions.value;
});

// Today's retailer earnings
const todaysRetailerEarnings = computed(() => {
  const today = new Date().toDateString();
  const todayTransactions = filteredTransactions.value.filter(transaction => 
    new Date(transaction.date).toDateString() === today
  );
  
  const todayEarnings = todayTransactions.reduce((sum, transaction) => {
    return sum + parseFloat(transaction.retailerEarnings || 0);
  }, 0);
  
  return todayEarnings.toFixed(2);
});

// Total product revenue (filtered)
const totalProductRevenue = computed(() => {
  const total = filteredTransactions.value.reduce((sum, transaction) => {
    return sum + parseFloat(transaction.productRevenue || 0);
  }, 0);
  return total.toFixed(2);
});

// Platform commission: fixed ₱10 per successful order
const platformShare = computed(() => {
  return (filteredTransactions.value.length * 10).toFixed(2);
});

// Total retailer earnings: product revenue minus ₱10 platform commission per order
const totalRetailerEarnings = computed(() => {
  return (
    parseFloat(totalProductRevenue.value) -
    parseFloat(platformShare.value)
  ).toFixed(2);
});

// Average retailer earnings per order
const averageRetailerEarnings = computed(() => {
  if (filteredTransactions.value.length === 0) return "0.00";
  const average = parseFloat(totalRetailerEarnings.value) / filteredTransactions.value.length;
  return average.toFixed(2);
});

// Fetch retailer data and revenue
const fetchRetailerData = () => {
  const user = auth.currentUser;
  if (!user) {
    console.log("No retailer logged in");
    loading.value = false;
    return;
  }

  currentRetailerId.value = user.uid;
  console.log("Fetching revenue data for retailer:", user.uid);

  // Get retailer data first
  const getRetailerData = async () => {
    try {
      const retailerDoc = doc(db, "retailers", user.uid);
      const retailerSnapshot = await getDoc(retailerDoc);
      const retailerData = retailerSnapshot.data();
      
      if (retailerData) {
        console.log("📊 Retailer found:", retailerData.businessName);
        // Fetch initial transactions from both collections
        await fetchInitialTransactions(user.uid);
        
        // Set up real-time listeners for both collections
        setupRealTimeListeners(user.uid);
      } else {
        console.error("Retailer data not found");
        loading.value = false;
      }
    } catch (error) {
      console.error("Error fetching retailer data:", error);
      loading.value = false;
    }
  };

  getRetailerData();
};

// Fetch initial transactions from both orders and deliveries collections
const fetchInitialTransactions = async (retailerId) => {
  try {
    console.log("🔄 Fetching initial transactions for retailer:", retailerId);
    
    // Fetch from orders collection - ONLY for this retailer
    const ordersQuery = query(
      collection(db, "orders"),
      where("retailerId", "==", retailerId),
      where("orderStatus", "==", "delivered"),
      orderBy("orderDate", "desc")
    );

    // Fetch from deliveries collection - ONLY for this retailer
    const deliveriesQuery = query(
      collection(db, "deliveries"),
      where("retailerId", "==", retailerId),
      where("status", "in", ["delivered", "completed"]),
      orderBy("createdAt", "desc")
    );

    const [ordersSnapshot, deliveriesSnapshot] = await Promise.all([
      getDocs(ordersQuery),
      getDocs(deliveriesQuery)
    ]);

    const transactions = [];

    console.log(`📦 Orders found: ${ordersSnapshot.size}`);
    console.log(`🚚 Deliveries found: ${deliveriesSnapshot.size}`);

    // Process orders collection
    ordersSnapshot.forEach((doc) => {
      const order = doc.data();
      // Double-check retailer ID to ensure it's for the current retailer
      if (order.retailerId === retailerId) {
        const transaction = createTransactionFromOrder(order, doc.id);
        if (transaction) {
          transactions.push(transaction);
        }
      }
    });

    // Process deliveries collection
    deliveriesSnapshot.forEach((doc) => {
      const delivery = doc.data();
      // Double-check retailer ID to ensure it's for the current retailer
      if (delivery.retailerId === retailerId) {
        const transaction = createTransactionFromDelivery(delivery, doc.id);
        if (transaction && !transactions.find(t => t.orderId === transaction.orderId)) {
          transactions.push(transaction);
        }
      }
    });

    // Sort by date (newest first)
    transactions.sort((a, b) => new Date(b.date) - new Date(a.date));

    allTransactions.value = transactions;
    filteredTransactions.value = transactions;
    loading.value = false;
    
    console.log("✅ Initial transactions loaded for current retailer:", transactions.length);
    
  } catch (error) {
    console.error("Error fetching initial transactions:", error);
    loading.value = false;
  }
};

// Create transaction object from order data
const createTransactionFromOrder = (order, docId) => {
  try {
    // Verify this order belongs to the current retailer
    if (order.retailerId !== currentRetailerId.value) {
      console.warn("Order does not belong to current retailer:", order.retailerId);
      return null;
    }

    const deliveryFee = order.deliveryFee || 0;
    const productRevenue = (order.total || 0) - deliveryFee;
    const platformCommission = 10;
    const retailerEarnings = Math.max(productRevenue - platformCommission, 0);

    return {
      id: docId,
      orderId: order.orderNumber || order.orderId || `ORD-${docId.slice(-4)}`,
      date: order.orderDate?.toDate?.() || order.deliveredAt?.toDate?.() || new Date(),
      product: `${order.gasBrand || 'LPG'} ${order.gasType || 'Tank'}`,
      quantity: order.quantity || 1,
      productRevenue: productRevenue.toFixed(2),
      retailerEarnings: retailerEarnings.toFixed(2),
      status: 'delivered',
      source: 'orders',
      retailerId: order.retailerId // Store retailer ID for verification
    };
  } catch (error) {
    console.error("Error creating transaction from order:", error);
    return null;
  }
};

// Create transaction object from delivery data
const createTransactionFromDelivery = (delivery, docId) => {
  try {
    // Verify this delivery belongs to the current retailer
    if (delivery.retailerId !== currentRetailerId.value) {
      console.warn("Delivery does not belong to current retailer:", delivery.retailerId);
      return null;
    }

    const deliveryFee = delivery.deliveryFee || 0;
    const productRevenue = (delivery.totalAmount || 0) - deliveryFee;
    const platformCommission = 10;
    const retailerEarnings = Math.max(productRevenue - platformCommission, 0);

    return {
      id: docId,
      orderId: delivery.orderId || delivery.displayId || `ORD-${docId.slice(-4)}`,
      date: delivery.createdAt?.toDate?.() || delivery.updatedAt?.toDate?.() || new Date(),
      product: delivery.item || `${delivery.gasBrand || 'LPG'} ${delivery.gasType || 'Tank'}`,
      quantity: delivery.quantity || 1,
      productRevenue: productRevenue.toFixed(2),
      retailerEarnings: retailerEarnings.toFixed(2),
      status: delivery.status === 'completed' ? 'delivered' : delivery.status,
      source: 'deliveries',
      retailerId: delivery.retailerId // Store retailer ID for verification
    };
  } catch (error) {
    console.error("Error creating transaction from delivery:", error);
    return null;
  }
};

// Set up real-time listeners for both collections - ONLY for current retailer
const setupRealTimeListeners = (retailerId) => {
  console.log("🔊 Setting up real-time listeners for retailer:", retailerId);

  // Listener for orders collection - ONLY for this retailer
  const ordersQuery = query(
    collection(db, "orders"),
    where("retailerId", "==", retailerId),
    where("orderStatus", "==", "delivered")
  );

  const ordersUnsubscribe = onSnapshot(ordersQuery, 
    (snapshot) => {
      console.log("📦 Orders collection updated for current retailer");
      handleRealtimeUpdate(snapshot, 'orders');
    },
    (error) => {
      console.error("Error in orders real-time listener:", error);
    }
  );

  // Listener for deliveries collection - ONLY for this retailer
  const deliveriesQuery = query(
    collection(db, "deliveries"),
    where("retailerId", "==", retailerId),
    where("status", "in", ["delivered", "completed"])
  );

  const deliveriesUnsubscribe = onSnapshot(deliveriesQuery, 
    (snapshot) => {
      console.log("🚚 Deliveries collection updated for current retailer");
      handleRealtimeUpdate(snapshot, 'deliveries');
    },
    (error) => {
      console.error("Error in deliveries real-time listener:", error);
    }
  );

  // Store unsubscribe functions
  unsubscribeFunctions.value = [ordersUnsubscribe, deliveriesUnsubscribe];
};

// Handle real-time updates from both collections
const handleRealtimeUpdate = (snapshot, source) => {
  let updatesCount = 0;
  
  snapshot.docChanges().forEach((change) => {
    if (change.type === 'added' || change.type === 'modified') {
      const data = change.doc.data();
      
      // Verify the document belongs to the current retailer
      if (data.retailerId !== currentRetailerId.value) {
        console.warn("Skipping document not belonging to current retailer:", data.retailerId);
        return;
      }

      let transaction = null;

      if (source === 'orders') {
        transaction = createTransactionFromOrder(data, change.doc.id);
      } else if (source === 'deliveries') {
        transaction = createTransactionFromDelivery(data, change.doc.id);
      }

      if (transaction) {
        // Check if transaction already exists
        const existingIndex = allTransactions.value.findIndex(
          t => t.orderId === transaction.orderId && t.retailerId === currentRetailerId.value
        );

        if (existingIndex !== -1) {
          // Update existing transaction
          allTransactions.value.splice(existingIndex, 1, transaction);
          console.log("🔄 Updated transaction:", transaction.orderId);
        } else {
          // Add new transaction
          allTransactions.value.unshift(transaction); // Add to beginning for newest first
          console.log("✅ Added new transaction:", transaction.orderId);
        }
        updatesCount++;
      }
    } else if (change.type === 'removed') {
      // Remove transaction if needed
      const removedId = change.doc.id;
      allTransactions.value = allTransactions.value.filter(
        t => t.id !== removedId && t.retailerId === currentRetailerId.value
      );
      console.log("🗑️ Removed transaction:", removedId);
      updatesCount++;
    }
  });

  if (updatesCount > 0) {
    console.log(`🔄 Applied ${updatesCount} real-time updates`);
    // Re-apply date filter and sort
    applyDateFilterAndSort();
  }
};

// Apply date filter and sort transactions
const applyDateFilterAndSort = () => {
  // Ensure we only show transactions for current retailer
  const currentRetailerTransactions = allTransactions.value.filter(
    transaction => transaction.retailerId === currentRetailerId.value
  );

  if (!startDate.value && !endDate.value) {
    filteredTransactions.value = [...currentRetailerTransactions].sort(
      (a, b) => new Date(b.date) - new Date(a.date)
    );
    isFiltered.value = false;
  } else {
    filterByDate();
  }
};

// Filter transactions by date
const filterByDate = () => {
  // Ensure we only filter transactions for current retailer
  const currentRetailerTransactions = allTransactions.value.filter(
    transaction => transaction.retailerId === currentRetailerId.value
  );

  if (!startDate.value && !endDate.value) {
    filteredTransactions.value = [...currentRetailerTransactions].sort(
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

  filteredTransactions.value = currentRetailerTransactions.filter(transaction => {
    const transactionDate = new Date(transaction.date);
    
    if (start && end) {
      return transactionDate >= start && transactionDate <= end;
    } else if (start) {
      return transactionDate >= start;
    } else if (end) {
      return transactionDate <= end;
    }
    
    return true;
  });

  // Sort filtered results
  filteredTransactions.value.sort((a, b) => new Date(b.date) - new Date(a.date));
  isFiltered.value = true;
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
    return 'Invalid Date';
  }
};

// Format date for filter display
const formatDisplayDate = (dateString) => {
  if (!dateString) return 'N/A';
  return new Date(dateString).toLocaleDateString('en-PH');
};

// Print report function - UPDATED with "Revenue Report" only (no month/year)
const printReport = () => {
  // Create a new window for printing
  const printWindow = window.open('', '_blank');
  
  // Calculate totals
  const totalProductRevenue = filteredTransactions.value.reduce((sum, transaction) => {
    return sum + parseFloat(transaction.productRevenue || 0);
  }, 0).toFixed(2);
  
  const totalRetailerEarnings = filteredTransactions.value.reduce((sum, transaction) => {
    return sum + parseFloat(transaction.retailerEarnings || 0);
  }, 0).toFixed(2);
  
  // Start building the HTML content - SIMPLIFIED with only "Revenue Report" title
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
        .revenue-amount, .retailer-earnings {
          color: #000000;
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
        .total-amount, .total-earnings {
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
            <th>Product</th>
            <th>Quantity</th>
            <th>Product Revenue</th>
            <th>Retailer Earnings</th>
            <th>Status</th>
          </tr>
        </thead>
        <tbody>
  `;
  
  // Add transaction rows
  filteredTransactions.value.forEach(transaction => {
    const formattedDate = formatDate(transaction.date);
    printContent += `
          <tr>
            <td>${formattedDate}</td>
            <td class="order-id">${transaction.orderId}</td>
            <td>${transaction.product}</td>
            <td>${transaction.quantity}</td>
            <td class="revenue-amount">₱${transaction.productRevenue}</td>
            <td class="retailer-earnings">₱${transaction.retailerEarnings}</td>
            <td><span class="status-badge">Delivered</span></td>
          </tr>
    `;
  });
  
  // Add footer with totals
  printContent += `
          <tr class="summary-row">
            <td colspan="4" class="total-label">Total Product Revenue</td>
            <td class="total-amount">₱${totalProductRevenue}</td>
            <td class="total-earnings">₱${totalRetailerEarnings}</td>
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
      fetchRetailerData();
    } else {
      loading.value = false;
      console.log("No retailer logged in");
    }
  });
});
</script>

<style scoped>
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

.revenue-amount {
  font-weight: 600;
  color: #22c55e;
}

.retailer-earnings {
  font-weight: 600;
  color: #FF7828;
}

.status-badge {
  padding: 0.25rem 0.75rem;
  border-radius: 20px;
  font-size: 0.8rem;
  font-weight: 600;
}

.status-badge.delivered {
  background: rgba(34, 197, 94, 0.1);
  color: #16a34a;
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

.total-amount {
  color: #22c55e;
  font-size: 1.1rem;
  font-weight: 700;
}

.total-earnings {
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

/* Print Styles - UPDATED */
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
}
</style>