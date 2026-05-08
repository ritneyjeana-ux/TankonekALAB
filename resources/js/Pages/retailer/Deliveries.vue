<template>
  <div class="deliveries-dashboard">
    <!-- Deliveries Section -->
    <section class="deliveries-section">
      <div class="section-header">
        <div class="header-content">
          <h2>Active Deliveries</h2>
          <p>Track ongoing deliveries and monitor delivery progress</p>
        </div>
        <div class="delivery-stats">
          <div class="stat-item">
            <div class="stat-icon active">
              <svg width="20" height="20" viewBox="0 0 24 24" fill="none">
                <path d="M9 17a2 2 0 11-4 0 2 2 0 014 0zM19 17a2 2 0 11-4 0 2 2 0 014 0z" stroke="currentColor" stroke-width="2"/>
                <path d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h1M5 17a2 2 0 104 0m-4 0a2 2 0 114 0m6 0a2 2 0 104 0m-4 0a2 2 0 114 0" stroke="currentColor" stroke-width="2"/>
              </svg>
            </div>
            <div class="stat-info">
              <span class="stat-value">{{ activeDeliveries }}</span>
              <span class="stat-label">Active</span>
            </div>
          </div>
          <div class="stat-item">
            <div class="stat-icon completed">
              <svg width="20" height="20" viewBox="0 0 24 24" fill="none">
                <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" stroke="currentColor" stroke-width="2"/>
              </svg>
            </div>
            <div class="stat-info">
              <span class="stat-value">{{ completedToday }}</span>
              <span class="stat-label">Completed Today</span>
            </div>
          </div>
        </div>
      </div>

      <!-- Loading State -->
      <div v-if="loading" class="loading-state">
        <svg width="48" height="48" viewBox="0 0 24 24" fill="none" class="loading-spinner">
          <path d="M12 2v4m0 12v4M4.93 4.93l2.83 2.83m8.48 8.48l2.83 2.83M2 12h4m12 0h4M4.93 19.07l2.83-2.83m8.48-8.48l2.83-2.83" stroke="#FF7828" stroke-width="2" stroke-linecap="round"/>
        </svg>
        <p>Loading deliveries...</p>
      </div>

      <!-- Deliveries Grid -->
      <div v-else class="deliveries-grid">
        <div
          v-for="delivery in deliveries"
          :key="delivery.id"
          class="delivery-card"
          :class="delivery.status"
        >
          <div class="delivery-header">
            <div class="delivery-info">
              <span class="order-id">{{ delivery.displayId || delivery.orderId }}</span>
              <span class="customer-name">{{ delivery.customerName }}</span>
            </div>
            <div class="delivery-status">
              <span class="status-badge" :class="delivery.status">
                <span class="status-dot"></span>
                {{ formatStatus(delivery.status) }}
              </span>
              <span class="total-price">₱{{ delivery.totalAmount?.toLocaleString() }}</span>
            </div>
          </div>

          <div class="delivery-body">
            <div class="delivery-details">
              <div class="detail-item">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" class="detail-icon">
                  <path d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" stroke="currentColor" stroke-width="2"/>
                </svg>
                <span>{{ delivery.item || `${delivery.gasBrand} ${delivery.gasType} × ${delivery.quantity}` }}</span>
              </div>
              <div class="detail-item">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" class="detail-icon">
                  <path d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" stroke="currentColor" stroke-width="2"/>
                </svg>
                <span>{{ delivery.riderName || delivery.deliveryPerson || 'Driver not assigned yet' }}</span>
              </div>
              <div class="detail-item">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" class="detail-icon">
                  <path d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" stroke="currentColor" stroke-width="2"/>
                  <path d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" stroke="currentColor" stroke-width="2"/>
                </svg>
                <span class="address">{{ delivery.deliveryAddress }}</span>
              </div>
              <div v-if="delivery.customerPhone" class="detail-item">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" class="detail-icon">
                  <path d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" stroke="currentColor" stroke-width="2"/>
                </svg>
                <span>{{ delivery.customerPhone }}</span>
              </div>
            </div>

            <div class="delivery-actions">
              <button v-if="delivery.riderName" class="action-btn contact" @click="contactDriver(delivery)">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none">
                  <path d="M21 11.5a8.38 8.38 0 01-.9 3.8 8.5 8.5 0 01-7.6 4.7 8.38 8.38 0 01-3.8-.9L3 21l1.9-5.7a8.38 8.38 0 01-.9-3.8 8.5 8.5 0 014.7-7.6 8.38 8.38 0 013.8-.9h.5a8.48 8.48 0 018 8v.5z" stroke="currentColor" stroke-width="2"/>
                </svg>
                Contact Driver
              </button>
              <button class="action-btn track" @click="trackDelivery(delivery)">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none">
                  <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z" stroke="currentColor" stroke-width="2"/>
                  <circle cx="12" cy="12" r="3" stroke="currentColor" stroke-width="2"/>
                </svg>
                Track
              </button>
              <button v-if="delivery.status === 'accepted' || delivery.status === 'assigned'" 
                      class="action-btn ready" @click="markAsReadyForPickup(delivery)">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none">
                  <path d="M5 13l4 4L19 7" stroke="currentColor" stroke-width="2"/>
                </svg>
                Ready for Pickup
              </button>
            </div>
          </div>

          <div class="delivery-footer">
            <div class="delivery-time">
              <svg width="14" height="14" viewBox="0 0 24 24" fill="none">
                <path d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" stroke="currentColor" stroke-width="2"/>
              </svg>
              Estimated Prep: {{ delivery.estimatedPrepTime || 15 }} mins
            </div>
            <div class="delivery-update">
              Created: {{ formatDate(delivery.createdAt) }}
            </div>
          </div>
        </div>
      </div>

      <div v-if="!loading && deliveries.length === 0" class="empty-state">
        <svg width="64" height="64" viewBox="0 0 24 24" fill="none" class="empty-icon">
          <path d="M9 17a2 2 0 11-4 0 2 2 0 014 0zM19 17a2 2 0 11-4 0 2 2 0 014 0z" stroke="#9CA3AF" stroke-width="2"/>
          <path d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h1M5 17a2 2 0 104 0m-4 0a2 2 0 114 0m6 0a2 2 0 104 0m-4 0a2 2 0 114 0" stroke="#9CA3AF" stroke-width="2"/>
        </svg>
        <h3>No Active Deliveries</h3>
        <p>All deliveries have been completed or there are no ongoing deliveries at the moment.</p>
      </div>
    </section>
  </div>
</template>

<script setup>
import { ref, onMounted, computed } from "vue";
import { db, auth } from "@/firebase";
import { 
  collection, 
  query, 
  where, 
  onSnapshot,
  orderBy,
  updateDoc,
  doc,
  serverTimestamp,
  getDocs
} from "firebase/firestore";
import { onAuthStateChanged } from "firebase/auth";

// Deliveries data from Firebase
const deliveries = ref([]);
const loading = ref(true);

// Fetch deliveries from Firebase for this specific retailer
const fetchDeliveries = () => {
  const user = auth.currentUser;
  if (!user) {
    console.log("No retailer logged in");
    loading.value = false;
    return;
  }

  console.log("Fetching deliveries for retailer:", user.uid);

  // SIMPLIFIED QUERY - Remove complex ordering first to fix the index error
  const deliveriesQuery = query(
    collection(db, "deliveries"),
    where("retailerId", "==", user.uid),
    where("status", "in", ["accepted", "assigned", "ready-for-pickup", "in-transit"])
    // Removed orderBy temporarily to avoid index issues
  );

  const unsubscribe = onSnapshot(deliveriesQuery, 
    (snapshot) => {
      const deliveriesList = [];
      snapshot.forEach((doc) => {
        const deliveryData = doc.data();
        deliveriesList.push({
          id: doc.id,
          ...deliveryData,
          createdAt: deliveryData.createdAt?.toDate?.() || new Date(),
          acceptedAt: deliveryData.acceptedAt?.toDate?.() || null,
          // Ensure we have display fields
          displayId: deliveryData.displayId || deliveryData.orderId || `ORD-${doc.id.slice(-6)}`,
          item: deliveryData.item || `${deliveryData.gasBrand || 'LPG'} ${deliveryData.gasType || 'Tank'} × ${deliveryData.quantity || 1}`,
          customerName: deliveryData.customerName || 'Customer',
          deliveryAddress: deliveryData.deliveryAddress || 'Address not provided',
          totalAmount: deliveryData.totalAmount || 0,
          riderName: deliveryData.riderName || deliveryData.deliveryPerson,
          estimatedPrepTime: deliveryData.estimatedPrepTime || 15
        });
      });
      
      // Manual sorting since we removed orderBy from query
      deliveriesList.sort((a, b) => {
        // Sort by status priority first
        const statusOrder = {
          'ready-for-pickup': 1,
          'in-transit': 2,
          'accepted': 3,
          'assigned': 4
        };
        
        const statusA = statusOrder[a.status] || 5;
        const statusB = statusOrder[b.status] || 5;
        
        if (statusA !== statusB) {
          return statusA - statusB;
        }
        
        // Then by creation date (newest first)
        return new Date(b.createdAt) - new Date(a.createdAt);
      });
      
      deliveries.value = deliveriesList;
      loading.value = false;
      
      console.log("Retailer deliveries loaded:", deliveriesList.length);
      console.log("Delivery status breakdown:", 
        deliveriesList.reduce((acc, delivery) => {
          acc[delivery.status] = (acc[delivery.status] || 0) + 1;
          return acc;
        }, {})
      );
    },
    (error) => {
      console.error(" Error fetching retailer deliveries:", error);
      
      // Fallback: Try simpler query if complex one fails
      if (error.code === 'failed-precondition') {
        console.log("Trying fallback query...");
        fetchDeliveriesFallback(user.uid);
      } else {
        loading.value = false;
      }
    }
  );

  return unsubscribe;
};

// Fallback method with simpler query
const fetchDeliveriesFallback = async (retailerId) => {
  try {
    const deliveriesQuery = query(
      collection(db, "deliveries"),
      where("retailerId", "==", retailerId)
    );
    
    const snapshot = await getDocs(deliveriesQuery);
    const deliveriesList = [];
    
    snapshot.forEach((doc) => {
      const deliveryData = doc.data();
      // Only include non-available, non-delivered, non-cancelled deliveries
      if (deliveryData.status !== 'available' && 
          deliveryData.status !== 'delivered' && 
          deliveryData.status !== 'cancelled') {
        deliveriesList.push({
          id: doc.id,
          ...deliveryData,
          createdAt: deliveryData.createdAt?.toDate?.() || new Date(),
          acceptedAt: deliveryData.acceptedAt?.toDate?.() || null,
          displayId: deliveryData.displayId || deliveryData.orderId || `ORD-${doc.id.slice(-6)}`,
          item: deliveryData.item || `${deliveryData.gasBrand || 'LPG'} ${deliveryData.gasType || 'Tank'} × ${deliveryData.quantity || 1}`,
          customerName: deliveryData.customerName || 'Customer',
          deliveryAddress: deliveryData.deliveryAddress || 'Address not provided',
          totalAmount: deliveryData.totalAmount || 0,
          riderName: deliveryData.riderName || deliveryData.deliveryPerson,
          estimatedPrepTime: deliveryData.estimatedPrepTime || 15
        });
      }
    });
    
    // Manual sorting
    deliveriesList.sort((a, b) => new Date(b.createdAt) - new Date(a.createdAt));
    
    deliveries.value = deliveriesList;
    loading.value = false;
    
    console.log("Fallback deliveries loaded:", deliveriesList.length);
  } catch (error) {
    console.error(" Error in fallback fetch:", error);
    loading.value = false;
  }
};

// Computed properties for stats
const activeDeliveries = computed(() => {
  return deliveries.value.filter(delivery => 
    delivery.status !== 'delivered' && delivery.status !== 'cancelled'
  ).length;
});

const completedToday = computed(() => {
  const today = new Date();
  today.setHours(0, 0, 0, 0);
  
  return deliveries.value.filter(delivery => 
    delivery.status === 'delivered' && 
    delivery.deliveredAt && 
    new Date(delivery.deliveredAt) >= today
  ).length;
});

const formatStatus = (status) => {
  const statusMap = {
    'available': 'Available',
    'accepted': 'Accepted by Rider',
    'assigned': 'Assigned',
    'ready-for-pickup': 'Ready for Pickup',
    'in-transit': 'In Transit',
    'delivered': 'Delivered',
    'cancelled': 'Cancelled'
  };
  return statusMap[status] || status;
};

const formatDate = (date) => {
  if (!date) return 'Unknown date';
  const d = new Date(date);
  return d.toLocaleString('en-US', {
    month: 'short',
    day: 'numeric',
    hour: '2-digit',
    minute: '2-digit'
  });
};

const contactDriver = (delivery) => {
  if (delivery.riderName) {
    alert(`Contacting ${delivery.riderName} for order ${delivery.displayId}...`);
    // Implement actual contact logic (SMS, call, etc.)
  } else {
    alert("No driver assigned yet for this delivery.");
  }
};

const trackDelivery = (delivery) => {
  alert(`Tracking delivery for order ${delivery.displayId}...`);
  // Implement actual tracking logic
};

const markAsReadyForPickup = async (delivery) => {
  try {
    const deliveryRef = doc(db, "deliveries", delivery.id);
    await updateDoc(deliveryRef, {
      status: 'ready-for-pickup',
      updatedAt: serverTimestamp(),
      readyAt: serverTimestamp()
    });

    console.log("📦 Delivery marked as ready for pickup:", delivery.displayId);
    alert(`Order ${delivery.displayId} is now ready for pickup!`);
  } catch (error) {
    console.error("Error marking delivery as ready for pickup:", error);
    alert("Failed to update delivery status. Please try again.");
  }
};

// Initialize on component mount
onMounted(() => {
  onAuthStateChanged(auth, (user) => {
    if (user) {
      fetchDeliveries();
    } else {
      loading.value = false;
      console.log("No retailer logged in for deliveries");
    }
  });
});
</script>

<style scoped>
/* Add loading state styles */
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

/* Update action buttons */
.action-btn.ready {
  background: #f59e0b;
  color: white;
  border-color: #f59e0b;
}

.action-btn.ready:hover {
  background: #d97706;
  border-color: #d97706;
  transform: translateY(-1px);
}

/* Keep all existing styles below */
.deliveries-dashboard {
  font-family: 'Inter', 'Poppins', sans-serif;
  padding: 0;
}

/* Deliveries Section */
.deliveries-section {
  background: white;
  border-radius: 16px;
  box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
  border: 1px solid #f1f5f9;
  overflow: hidden;
}

.section-header {
  display: flex;
  justify-content: space-between;
  align-items: flex-start;
  padding: 2rem 2rem 1.5rem 2rem;
  border-bottom: 1px solid #f1f5f9;
}

.header-content h2 {
  margin: 0 0 0.5rem 0;
  color: #1a202c;
  font-size: 1.5rem;
  font-weight: 700;
  font-family: 'Inter', sans-serif;
}

.header-content p {
  margin: 0;
  color: #718096;
  font-size: 1rem;
  font-family: 'Inter', sans-serif;
}

/* Delivery Stats */
.delivery-stats {
  display: flex;
  gap: 2rem;
}

.stat-item {
  display: flex;
  align-items: center;
  gap: 12px;
  padding: 1rem 1.5rem;
  background: #f8fafc;
  border-radius: 12px;
  border: 1px solid #e2e8f0;
}

.stat-icon {
  width: 48px;
  height: 48px;
  border-radius: 10px;
  display: flex;
  align-items: center;
  justify-content: center;
}

.stat-icon.active {
  background: rgba(255, 120, 40, 0.1);
  color: #FF7828;
}

.stat-icon.completed {
  background: rgba(34, 197, 94, 0.1);
  color: #22c55e;
}

.stat-info {
  display: flex;
  flex-direction: column;
}

.stat-value {
  font-size: 1.5rem;
  font-weight: 700;
  color: #1a202c;
  line-height: 1;
}

.stat-label {
  font-size: 0.8rem;
  color: #718096;
  font-weight: 500;
  margin-top: 4px;
}

/* Deliveries Grid */
.deliveries-grid {
  padding: 0 2rem 2rem 2rem;
  display: flex;
  flex-direction: column;
  gap: 1.25rem;
}

/* Delivery Card */
.delivery-card {
  background: white;
  border: 1px solid #f1f5f9;
  border-radius: 16px;
  padding: 1.5rem;
  transition: all 0.3s ease;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.04);
}

.delivery-card:hover {
  border-color: #FF7828;
  box-shadow: 0 8px 25px rgba(255, 120, 40, 0.1);
  transform: translateY(-2px);
}

.delivery-card.available {
  border-left: 4px solid #f59e0b;
}

.delivery-card.accepted {
  border-left: 4px solid #3b82f6;
}

.delivery-card.assigned {
  border-left: 4px solid #8b5cf6;
}

.delivery-card.ready-for-pickup {
  border-left: 4px solid #f59e0b;
}

.delivery-card.in-transit {
  border-left: 4px solid #8b5cf6;
}

.delivery-card.delivered {
  border-left: 4px solid #10b981;
}

.delivery-card.cancelled {
  border-left: 4px solid #ef4444;
}

/* Delivery Header */
.delivery-header {
  display: flex;
  justify-content: space-between;
  align-items: flex-start;
  margin-bottom: 1.25rem;
}

.delivery-info {
  display: flex;
  flex-direction: column;
  gap: 4px;
}

.order-id {
  font-size: 1.1rem;
  font-weight: 700;
  color: #1a202c;
  font-family: 'Inter', sans-serif;
}

.customer-name {
  font-size: 0.9rem;
  color: #4a5568;
  font-weight: 500;
}

.delivery-status {
  display: flex;
  flex-direction: column;
  align-items: flex-end;
  gap: 8px;
}

.status-badge {
  display: flex;
  align-items: center;
  gap: 6px;
  padding: 0.5rem 0.875rem;
  border-radius: 20px;
  font-size: 0.8rem;
  font-weight: 600;
  text-transform: capitalize;
  letter-spacing: 0.3px;
}

.status-dot {
  width: 6px;
  height: 6px;
  border-radius: 50%;
}

.status-badge.available {
  background: #fef3c7;
  color: #d97706;
}

.status-badge.available .status-dot {
  background: #d97706;
}

.status-badge.accepted {
  background: #dbeafe;
  color: #1d4ed8;
}

.status-badge.accepted .status-dot {
  background: #1d4ed8;
}

.status-badge.assigned {
  background: #e9d5ff;
  color: #7e22ce;
}

.status-badge.assigned .status-dot {
  background: #7e22ce;
}

.status-badge.ready-for-pickup {
  background: #fef3c7;
  color: #d97706;
}

.status-badge.ready-for-pickup .status-dot {
  background: #d97706;
}

.status-badge.in-transit {
  background: #e9d5ff;
  color: #7e22ce;
}

.status-badge.in-transit .status-dot {
  background: #7e22ce;
}

.status-badge.delivered {
  background: #dcfce7;
  color: #16a34a;
}

.status-badge.delivered .status-dot {
  background: #16a34a;
}

.status-badge.cancelled {
  background: #fee2e2;
  color: #dc2626;
}

.status-badge.cancelled .status-dot {
  background: #dc2626;
}

.total-price {
  font-size: 1.25rem;
  font-weight: 700;
  color: #FF7828;
  font-family: 'Inter', sans-serif;
}

/* Delivery Body */
.delivery-body {
  display: flex;
  justify-content: space-between;
  align-items: flex-start;
  margin-bottom: 1.25rem;
}

.delivery-details {
  display: flex;
  flex-direction: column;
  gap: 0.75rem;
  flex: 1;
}

.detail-item {
  display: flex;
  align-items: center;
  gap: 8px;
  color: #4a5568;
  font-size: 0.9rem;
}

.detail-icon {
  color: #718096;
  flex-shrink: 0;
}

.address {
  max-width: 300px;
  line-height: 1.4;
}

.delivery-actions {
  display: flex;
  gap: 0.75rem;
}

.action-btn {
  display: flex;
  align-items: center;
  gap: 6px;
  padding: 0.625rem 1rem;
  border: 1px solid #e2e8f0;
  border-radius: 8px;
  background: white;
  color: #4a5568;
  font-size: 0.85rem;
  font-weight: 500;
  cursor: pointer;
  transition: all 0.3s ease;
}

.action-btn.contact:hover {
  background: #f7fafc;
  border-color: #cbd5e0;
  color: #2d3748;
}

.action-btn.track {
  background: #FF7828;
  color: white;
  border-color: #FF7828;
}

.action-btn.track:hover {
  background: #e6691f;
  border-color: #e6691f;
  transform: translateY(-1px);
}

/* Delivery Footer */
.delivery-footer {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding-top: 1rem;
  border-top: 1px solid #f1f5f9;
  color: #718096;
  font-size: 0.8rem;
}

.delivery-time {
  display: flex;
  align-items: center;
  gap: 6px;
  font-weight: 500;
}

.delivery-update {
  color: #9ca3af;
}

/* Empty State */
.empty-state {
  padding: 4rem 2rem;
  text-align: center;
  color: #718096;
}

.empty-icon {
  margin-bottom: 1.5rem;
}

.empty-state h3 {
  margin: 0 0 0.5rem 0;
  color: #4a5568;
  font-size: 1.25rem;
  font-weight: 600;
}

.empty-state p {
  margin: 0 0 2rem 0;
  font-size: 1rem;
  max-width: 400px;
  margin-left: auto;
  margin-right: auto;
  line-height: 1.5;
}

/* Responsive Design */
@media (max-width: 768px) {
  .section-header {
    flex-direction: column;
    gap: 1.5rem;
    align-items: stretch;
  }
  
  .delivery-stats {
    justify-content: space-between;
  }
  
  .deliveries-grid {
    padding: 0 1rem 1.5rem 1rem;
  }
  
  .delivery-header {
    flex-direction: column;
    gap: 1rem;
    align-items: stretch;
  }
  
  .delivery-status {
    flex-direction: row;
    justify-content: space-between;
    align-items: center;
  }
  
  .delivery-body {
    flex-direction: column;
    gap: 1rem;
  }
  
  .delivery-actions {
    width: 100%;
    justify-content: stretch;
  }
  
  .action-btn {
    flex: 1;
    justify-content: center;
  }
  
  .delivery-footer {
    flex-direction: column;
    gap: 0.5rem;
    align-items: flex-start;
  }
}
</style>