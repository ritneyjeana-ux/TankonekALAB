<template>
  <div class="history-page">
    <!-- Header -->
    <div class="content-header">
      <h2>Delivery History</h2>
      <p>View your completed and cancelled deliveries</p>
    </div>

    <!-- Filter Tabs -->
    <div class="filter-tabs">
      <button 
        class="tab-btn" 
        :class="{ active: activeTab === 'all' }"
        @click="activeTab = 'all'"
      >
        All
      </button>
      <button 
        class="tab-btn" 
        :class="{ active: activeTab === 'completed' }"
        @click="activeTab = 'completed'"
      >
        Completed
      </button>
      <button 
        class="tab-btn" 
        :class="{ active: activeTab === 'cancelled' }"
        @click="activeTab = 'cancelled'"
      >
        Cancelled
      </button>
    </div>

    <!-- Completed Deliveries List -->
    <div class="deliveries-grid">
      <div
        v-for="delivery in filteredDeliveries"
        :key="delivery.id"
        class="delivery-card"
        :class="{ cancelled: delivery.status === 'cancelled' }"
      >
        <div class="delivery-content">
          <!-- Left Side -->
          <div class="delivery-info">
            <div class="delivery-header">
              <h3 class="order-id">{{ delivery.displayId || `ORD-${delivery.id.slice(-4)}` }}</h3>
              <span 
                class="status-badge"
                :class="{
                  'status-completed': delivery.status === 'delivered',
                  'status-cancelled': delivery.status === 'cancelled'
                }"
              >
                {{ delivery.status === 'delivered' ? 'Completed' : 'Cancelled' }}
              </span>
            </div>

            <p class="customer-name">
              {{ delivery.customer || delivery.customerName || 'Customer' }}
            </p>

            <div class="delivery-details">
              <div class="detail-item">
                <strong>Item:</strong> {{ delivery.item || `${delivery.gasBrand || 'LPG'} ${delivery.gasType || 'Tank'} × ${delivery.quantity || 1}` }}
              </div>
              <div class="detail-item">
                <strong>Distance:</strong> {{ delivery.distance || 'N/A' }}
              </div>
              <div class="detail-item">
                <strong>Completed:</strong> {{ formatTime(delivery.completedAt || delivery.deliveredAt) }}
              </div>
              
              <!-- Cancellation Reason -->
              <div 
                v-if="delivery.status === 'cancelled' && delivery.cancellationReason" 
                class="cancellation-reason"
              >
                <strong>Reason:</strong> {{ delivery.cancellationReason }}
              </div>
            </div>
          </div>

          <!-- Right Side -->
          <div class="delivery-earnings">
            <p 
              class="earned-amount"
              :class="{ cancelled: delivery.status === 'cancelled' }"
            >
              {{ delivery.status === 'delivered' ? '+' : '' }}₱{{ delivery.earned || delivery.fee || delivery.deliveryFee || 0 }}
            </p>
            <p class="earned-label">
              {{ delivery.status === 'delivered' ? 'Earned' : 'Amount' }}
            </p>
          </div>
        </div>

        <!-- Timeline for History -->
        <div class="delivery-timeline">
          <div class="timeline-item" :class="{ active: true }">
            <div class="timeline-dot"></div>
            <span class="timeline-label">Order Accepted</span>
            <span class="timeline-time" v-if="delivery.acceptedAt">
              {{ formatTime(delivery.acceptedAt) }}
            </span>
          </div>
          <div class="timeline-item" :class="{ active: delivery.readyAt }">
            <div class="timeline-dot"></div>
            <span class="timeline-label">Ready for Pickup</span>
            <span class="timeline-time" v-if="delivery.readyAt">
              {{ formatTime(delivery.readyAt) }}
            </span>
          </div>
          <div class="timeline-item" :class="{ active: delivery.pickedUpAt }">
            <div class="timeline-dot"></div>
            <span class="timeline-label">Picked Up</span>
            <span class="timeline-time" v-if="delivery.pickedUpAt">
              {{ formatTime(delivery.pickedUpAt) }}
            </span>
          </div>
          <div class="timeline-item" :class="{ active: delivery.status === 'delivered' }">
            <div class="timeline-dot"></div>
            <span class="timeline-label">Delivered</span>
            <span class="timeline-time" v-if="delivery.deliveredAt">
              {{ formatTime(delivery.deliveredAt) }}
            </span>
          </div>
          <div class="timeline-item" :class="{ active: delivery.status === 'cancelled' }">
            <div class="timeline-dot"></div>
            <span class="timeline-label">Cancelled</span>
            <span class="timeline-time" v-if="delivery.cancelledAt">
              {{ formatTime(delivery.cancelledAt) }}
            </span>
          </div>
        </div>
      </div>
    </div>

    <!-- No Completed Deliveries -->
    <div v-if="!filteredDeliveries.length && !loading" class="empty-state">
      <svg width="64" height="64" viewBox="0 0 24 24" fill="none">
        <path d="M9 17a2 2 0 11-4 0 2 2 0 014 0zM19 17a2 2 0 11-4 0 2 2 0 014 0z" stroke="#CBD5E0" stroke-width="2"/>
        <path d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h1M5 17a2 2 0 104 0m-4 0a2 2 0 114 0m6 0a2 2 0 104 0m-4 0a2 2 0 114 0" stroke="#CBD5E0" stroke-width="2"/>
      </svg>
      <h3>No delivery history</h3>
      <p>{{ activeTab === 'all' ? 'Completed and cancelled deliveries will appear here' : 
          activeTab === 'completed' ? 'Completed deliveries will appear here' : 
          'Cancelled deliveries will appear here' }}</p>
    </div>

    <!-- Loading State -->
    <div v-if="loading" class="loading-state">
      <svg width="48" height="48" viewBox="0 0 24 24" fill="none" class="loading-spinner">
        <path d="M12 2v4m0 12v4M4.93 4.93l2.83 2.83m8.48 8.48l2.83 2.83M2 12h4m12 0h4M4.93 19.07l2.83-2.83m8.48-8.48l2.83-2.83" stroke="#FF7828" stroke-width="2" stroke-linecap="round"/>
      </svg>
      <p>Loading delivery history...</p>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, onUnmounted, computed } from "vue";

// FIREBASE
import { db, auth } from "@/firebase";
import { 
  collection, 
  query, 
  where, 
  onSnapshot,
  orderBy
} from "firebase/firestore";
import { onAuthStateChanged } from "firebase/auth";

const completedDeliveries = ref([]);
const loading = ref(true);
const activeTab = ref('all');

// Computed property for filtered deliveries
const filteredDeliveries = computed(() => {
  if (activeTab.value === 'all') {
    return completedDeliveries.value;
  } else if (activeTab.value === 'completed') {
    return completedDeliveries.value.filter(delivery => delivery.status === 'delivered');
  } else if (activeTab.value === 'cancelled') {
    return completedDeliveries.value.filter(delivery => delivery.status === 'cancelled');
  }
  return completedDeliveries.value;
});

// Format time for display
const formatTime = (timestamp) => {
  if (!timestamp) return 'N/A';
  
  let date;
  if (timestamp instanceof Date) {
    date = timestamp;
  } else if (timestamp.toDate) {
    date = timestamp.toDate();
  } else {
    date = new Date(timestamp);
  }
  
  return date.toLocaleDateString('en-PH', { 
    year: 'numeric',
    month: 'short', 
    day: 'numeric',
    hour: '2-digit', 
    minute: '2-digit',
    hour12: true 
  });
};

// Fetch completed and cancelled deliveries from Firebase
const fetchDeliveryHistory = () => {
  const user = auth.currentUser;
  if (!user) {
    console.log("No rider logged in");
    loading.value = false;
    return;
  }

  console.log("Fetching delivery history for rider:", user.uid);

  // Query for completed and cancelled deliveries
  const historyQuery = query(
    collection(db, "deliveries"),
    where("riderId", "==", user.uid),
    where("status", "in", ["delivered", "cancelled"]),
    orderBy("updatedAt", "desc")
  );

  const unsubscribe = onSnapshot(historyQuery, 
    (snapshot) => {
      const deliveriesList = [];
      snapshot.forEach((doc) => {
        const deliveryData = doc.data();
        
        // Format the delivery data for display
        deliveriesList.push({
          id: doc.id,
          displayId: deliveryData.displayId || deliveryData.orderId || `ORD-${doc.id.slice(-4)}`,
          status: deliveryData.status,
          earned: deliveryData.deliveryFee || deliveryData.fee || 50,
          fee: deliveryData.deliveryFee || deliveryData.fee || 50,
          item: deliveryData.item || `${deliveryData.gasBrand || 'LPG'} ${deliveryData.gasType || 'Tank'} × ${deliveryData.quantity || 1}`,
          customer: deliveryData.customer || deliveryData.customerName,
          contact: deliveryData.contact || deliveryData.customerPhone,
          pickup: deliveryData.pickup || deliveryData.pickupAddress,
          deliverTo: deliveryData.deliverTo || deliveryData.deliveryAddress,
          distance: deliveryData.distance || 'N/A',
          // Additional details
          orderId: deliveryData.orderId,
          customerPhone: deliveryData.customerPhone,
          customerEmail: deliveryData.customerEmail,
          specialInstructions: deliveryData.specialInstructions,
          // Product details
          gasBrand: deliveryData.gasBrand,
          gasType: deliveryData.gasType,
          quantity: deliveryData.quantity,
          totalAmount: deliveryData.totalAmount,
          paymentMethod: deliveryData.paymentMethod,
          // Timestamps
          acceptedAt: deliveryData.acceptedAt,
          readyAt: deliveryData.readyAt,
          pickedUpAt: deliveryData.pickedUpAt,
          deliveredAt: deliveryData.deliveredAt,
          cancelledAt: deliveryData.cancelledAt,
          completedAt: deliveryData.deliveredAt || deliveryData.cancelledAt,
          cancellationReason: deliveryData.cancellationReason,
          updatedAt: deliveryData.updatedAt,
          ...deliveryData
        });
      });
      
      completedDeliveries.value = deliveriesList;
      loading.value = false;
      
      console.log("Delivery history loaded:", deliveriesList.length);
      console.log("Deliveries:", deliveriesList);
    },
    (error) => {
      console.error("Error fetching delivery history:", error);
      loading.value = false;
      
      // Fallback: Try simpler query if complex one fails
      if (error.code === 'failed-precondition') {
        console.log("Trying fallback query for history...");
        fetchDeliveryHistoryFallback(user.uid);
      }
    }
  );

  return unsubscribe;
};

// Fallback method with simpler query
const fetchDeliveryHistoryFallback = async (riderId) => {
  try {
    const historyQuery = query(
      collection(db, "deliveries"),
      where("riderId", "==", riderId)
    );
    
    const unsubscribe = onSnapshot(historyQuery, 
      (snapshot) => {
        const deliveriesList = [];
        snapshot.forEach((doc) => {
          const deliveryData = doc.data();
          // Only include completed and cancelled deliveries
          if (['delivered', 'cancelled'].includes(deliveryData.status)) {
            deliveriesList.push({
              id: doc.id,
              displayId: deliveryData.displayId || deliveryData.orderId || `ORD-${doc.id.slice(-4)}`,
              status: deliveryData.status,
              earned: deliveryData.deliveryFee || deliveryData.fee || 50,
              fee: deliveryData.deliveryFee || deliveryData.fee || 50,
              item: deliveryData.item || `${deliveryData.gasBrand || 'LPG'} ${deliveryData.gasType || 'Tank'} × ${deliveryData.quantity || 1}`,
              customer: deliveryData.customer || deliveryData.customerName,
              contact: deliveryData.contact || deliveryData.customerPhone,
              pickup: deliveryData.pickup || deliveryData.pickupAddress,
              deliverTo: deliveryData.deliverTo || deliveryData.deliveryAddress,
              distance: deliveryData.distance || 'N/A',
              orderId: deliveryData.orderId,
              customerPhone: deliveryData.customerPhone,
              customerEmail: deliveryData.customerEmail,
              specialInstructions: deliveryData.specialInstructions,
              gasBrand: deliveryData.gasBrand,
              gasType: deliveryData.gasType,
              quantity: deliveryData.quantity,
              totalAmount: deliveryData.totalAmount,
              paymentMethod: deliveryData.paymentMethod,
              acceptedAt: deliveryData.acceptedAt,
              readyAt: deliveryData.readyAt,
              pickedUpAt: deliveryData.pickedUpAt,
              deliveredAt: deliveryData.deliveredAt,
              cancelledAt: deliveryData.cancelledAt,
              completedAt: deliveryData.deliveredAt || deliveryData.cancelledAt,
              cancellationReason: deliveryData.cancellationReason,
              updatedAt: deliveryData.updatedAt,
              ...deliveryData
            });
          }
        });
        
        // Manual sorting by updatedAt (newest first)
        deliveriesList.sort((a, b) => {
          const timeA = a.updatedAt ? (a.updatedAt.toDate ? a.updatedAt.toDate() : new Date(a.updatedAt)) : new Date(0);
          const timeB = b.updatedAt ? (b.updatedAt.toDate ? b.updatedAt.toDate() : new Date(b.updatedAt)) : new Date(0);
          return timeB - timeA;
        });
        
        completedDeliveries.value = deliveriesList;
        loading.value = false;
        
        console.log("Fallback delivery history loaded:", deliveriesList.length);
      },
      (error) => {
        console.error("Error in fallback history fetch:", error);
        loading.value = false;
      }
    );
    
    return unsubscribe;
  } catch (error) {
    console.error("Error in fallback history setup:", error);
    loading.value = false;
    return null;
  }
};

// Initialize on component mount
let unsubscribe = null;

onMounted(() => {
  onAuthStateChanged(auth, (user) => {
    if (user) {
      unsubscribe = fetchDeliveryHistory();
    } else {
      loading.value = false;
      console.log("No rider logged in");
    }
  });
});

// Cleanup on component unmount
onUnmounted(() => {
  if (unsubscribe) {
    unsubscribe();
  }
});
</script>

<style scoped>
.history-page {
  font-family: 'Inter', sans-serif;
  padding: 2rem;
  background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
  min-height: 100vh;
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

/* Filter Tabs */
.filter-tabs {
  display: flex;
  gap: 0.5rem;
  margin-bottom: 2rem;
  background: #ffffff;
  padding: 0.5rem;
  border-radius: 12px;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
}

.tab-btn {
  flex: 1;
  padding: 0.75rem 1rem;
  border: none;
  background: transparent;
  border-radius: 8px;
  font-weight: 500;
  color: #718096;
  cursor: pointer;
  transition: all 0.3s ease;
}

.tab-btn.active {
  background: #FF7828;
  color: white;
  box-shadow: 0 2px 8px rgba(255, 120, 40, 0.3);
}

.tab-btn:hover:not(.active) {
  background: #f8fafc;
  color: #4a5568;
}

.deliveries-grid {
  display: flex;
  flex-direction: column;
  gap: 1rem;
}

.delivery-card {
  background: #ffffff;
  border-radius: 16px;
  padding: 1.5rem;
  box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
  border: 1px solid #f1f5f9;
  transition: all 0.3s ease;
}

.delivery-card:hover {
  transform: translateY(-2px);
  box-shadow: 0 8px 30px rgba(0, 0, 0, 0.12);
}

.delivery-card:not(.cancelled):hover {
  border-color: #FF7828;
}

.delivery-card.cancelled {
  border-left: 4px solid #dc2626;
  background: linear-gradient(135deg, #ffffff 0%, #fef2f2 100%);
}

.delivery-card.cancelled:hover {
  border-color: #dc2626;
}

.delivery-content {
  display: flex;
  justify-content: space-between;
  align-items: flex-start;
  margin-bottom: 1rem;
}

.delivery-info {
  flex: 1;
}

.delivery-header {
  display: flex;
  align-items: center;
  gap: 1rem;
  margin-bottom: 0.5rem;
}

.order-id {
  color: #1a202c;
  font-size: 1.1rem;
  font-weight: 600;
  margin: 0;
}

.status-badge {
  font-size: 0.75rem;
  padding: 0.4rem 0.8rem;
  border-radius: 20px;
  text-transform: capitalize;
  font-weight: 600;
  letter-spacing: 0.5px;
}

.status-completed {
  background: rgba(34, 197, 94, 0.1);
  color: #22c55e;
}

.status-cancelled {
  background: rgba(239, 68, 68, 0.1);
  color: #dc2626;
}

.customer-name {
  color: #1a202c;
  font-size: 0.95rem;
  font-weight: 500;
  margin: 0 0 1rem 0;
}

.delivery-details {
  display: flex;
  flex-direction: column;
  gap: 0.5rem;
}

.detail-item {
  color: #4a5568;
  font-size: 0.9rem;
  line-height: 1.4;
}

.detail-item strong {
  color: #2d3748;
}

.cancellation-reason {
  padding: 0.5rem;
  background: rgba(239, 68, 68, 0.05);
  border-radius: 6px;
  border-left: 3px solid #dc2626;
  font-style: italic;
  margin-top: 0.5rem;
}

.delivery-earnings {
  text-align: right;
  min-width: 100px;
}

.earned-amount {
  color: #22c55e;
  font-size: 1.25rem;
  font-weight: 700;
  margin: 0 0 0.25rem 0;
}

.earned-amount.cancelled {
  color: #dc2626;
}

.earned-label {
  color: #718096;
  font-size: 0.8rem;
  font-weight: 500;
  margin: 0;
}

/* Timeline for History */
.delivery-timeline {
  display: flex;
  flex-direction: column;
  gap: 0.5rem;
  padding-top: 1rem;
  border-top: 1px solid #e2e8f0;
}

.timeline-item {
  display: flex;
  align-items: center;
  gap: 0.75rem;
  padding: 0.25rem 0.5rem;
  border-radius: 6px;
  transition: all 0.3s ease;
}

.timeline-item.active {
  background: rgba(34, 197, 94, 0.05);
}

.timeline-item.active .timeline-dot {
  background: #22c55e;
}

.timeline-item.active .timeline-label {
  color: #16a34a;
  font-weight: 600;
}

.timeline-dot {
  width: 6px;
  height: 6px;
  border-radius: 50%;
  background: #cbd5e0;
  flex-shrink: 0;
}

.timeline-label {
  font-size: 0.8rem;
  color: #718096;
  font-weight: 500;
  flex: 1;
}

.timeline-time {
  font-size: 0.75rem;
  color: #a0aec0;
  font-style: italic;
}

.empty-state {
  text-align: center;
  color: #718096;
  font-size: 1rem;
  padding: 3rem 1rem;
  background: #ffffff;
  border-radius: 16px;
  border: 1px solid #f1f5f9;
}

.empty-state h3 {
  margin: 1rem 0 0.5rem 0;
  color: #4a5568;
  font-size: 1.25rem;
}

.empty-state p {
  margin: 0;
}

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

/* Responsive Design */
@media (max-width: 768px) {
  .history-page {
    padding: 1rem;
  }
  
  .filter-tabs {
    flex-direction: column;
  }
  
  .delivery-content {
    flex-direction: column;
    gap: 1rem;
  }
  
  .delivery-earnings {
    text-align: left;
    width: 100%;
    display: flex;
    justify-content: space-between;
    align-items: center;
  }
  
  .delivery-header {
    flex-direction: column;
    align-items: flex-start;
    gap: 0.5rem;
  }
  
  .delivery-timeline {
    padding: 0.75rem 0;
  }
  
  .timeline-item {
    flex-direction: column;
    align-items: flex-start;
    gap: 0.25rem;
  }
  
  .timeline-time {
    align-self: flex-end;
    font-size: 0.7rem;
  }
}
</style>