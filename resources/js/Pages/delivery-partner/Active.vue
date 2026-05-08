<template>
  <div class="active-deliveries-page">
    <!-- Header -->
    <div class="content-header">
      <h2>Active Deliveries</h2>
      <p>Manage your ongoing deliveries</p>
    </div>

    <!-- Deliveries List -->
    <div class="deliveries-grid">
      <div
        v-for="delivery in activeDeliveries"
        :key="delivery.id"
        class="delivery-card"
      >
        <!-- Order Header -->
        <div class="delivery-header">
          <h3 class="order-id">{{ delivery.displayId || `ORD-${delivery.id.slice(-4)}` }}</h3>
          <span
            class="status-badge"
            :class="{
              'status-accepted': delivery.status === 'accepted',
              'status-ready': delivery.status === 'ready-for-pickup',
              'status-picked-up': delivery.status === 'picked-up',
              'status-transit': delivery.status === 'in-transit',
              'status-cancelled': delivery.status === 'cancelled'
            }"
          >
            {{ formatStatus(delivery.status) }}
          </span>
        </div>

        <!-- Fee -->
        <div class="delivery-fee">
          Delivery Fee: <span class="fee-amount">₱{{ delivery.fee || delivery.deliveryFee }}</span>
        </div>

        <!-- Delivery Details -->
        <div class="delivery-details">
          <div class="detail-item">
            <strong>Item:</strong> {{ delivery.item || `${delivery.gasBrand || 'LPG'} ${delivery.gasType || 'Tank'} × ${delivery.quantity || 1}` }}
          </div>
          <div class="detail-item">
            <strong>Customer:</strong> {{ delivery.customer || delivery.customerName || 'Customer' }}
          </div>
          <div class="detail-item">
            <strong>Contact:</strong> {{ delivery.contact || delivery.customerPhone || 'N/A' }}
          </div>
          <div class="detail-item">
            <strong>Pickup:</strong> {{ delivery.pickup || delivery.pickupAddress || 'Store Location' }}
          </div>
          <div class="detail-item">
            <strong>Deliver to:</strong> {{ delivery.deliverTo || delivery.deliveryAddress || 'Customer Address' }}
          </div>
          
          <!-- Special Instructions -->
          <div v-if="delivery.specialInstructions" class="detail-item instructions">
            <strong>Instructions:</strong> {{ delivery.specialInstructions }}
          </div>

          <!-- Ready for Pickup Indicator -->
          <div v-if="delivery.status === 'ready-for-pickup'" class="ready-indicator">
            <span class="ready-badge">Ready for Pickup</span>
            <p class="ready-text">The retailer has marked this order as ready for pickup</p>
          </div>

          <!-- Cancellation Reason (if cancelled) -->
          <div v-if="delivery.status === 'cancelled' && delivery.cancellationReason" class="cancellation-reason">
            <strong>Cancellation Reason:</strong> {{ delivery.cancellationReason }}
          </div>
        </div>

        <!-- Button Actions -->
        <div class="action-buttons">
          <button
            v-if="delivery.status === 'accepted' || delivery.status === 'ready-for-pickup'"
            class="action-btn pickup-btn"
            @click="markAsPickedUp(delivery)"
          >
            Mark as Picked Up
          </button>
          
          <!-- COMPLETE AND CANCEL BUTTONS SIDE BY SIDE -->
          <div v-if="delivery.status === 'picked-up' || delivery.status === 'in-transit'" class="dual-buttons">
            <button
              class="action-btn complete-btn"
              @click="completeDelivery(delivery)"
            >
              Complete Delivery
            </button>
            <button
              class="action-btn cancel-btn"
              @click="cancelDelivery(delivery)"
            >
              Cancel Delivery
            </button>
          </div>

          <!-- Cancelled Status Display -->
          <div v-if="delivery.status === 'cancelled'" class="cancelled-info">
            <p class="cancelled-note"> Delivery Cancelled</p>
            <p v-if="delivery.cancelledAt" class="cancelled-time">
              Cancelled: {{ formatTime(delivery.cancelledAt) }}
            </p>
          </div>
          
          <!-- Timeline -->
          <div class="delivery-timeline">
            <div class="timeline-item" :class="{ active: ['accepted', 'ready-for-pickup', 'picked-up', 'in-transit', 'delivered', 'cancelled'].includes(delivery.status) }">
              <div class="timeline-dot"></div>
              <span class="timeline-label">Order Accepted</span>
              <span class="timeline-time" v-if="delivery.acceptedAt">
                {{ formatTime(delivery.acceptedAt) }}
              </span>
            </div>
            <div class="timeline-item" :class="{ active: ['ready-for-pickup', 'picked-up', 'in-transit', 'delivered', 'cancelled'].includes(delivery.status) }">
              <div class="timeline-dot"></div>
              <span class="timeline-label">Ready for Pickup</span>
              <span class="timeline-time" v-if="delivery.readyAt">
                {{ formatTime(delivery.readyAt) }}
              </span>
            </div>
            <div class="timeline-item" :class="{ active: ['picked-up', 'in-transit', 'delivered', 'cancelled'].includes(delivery.status) }">
              <div class="timeline-dot"></div>
              <span class="timeline-label">Picked Up</span>
              <span class="timeline-time" v-if="delivery.pickedUpAt">
                {{ formatTime(delivery.pickedUpAt) }}
              </span>
            </div>
            <div class="timeline-item" :class="{ active: ['delivered'].includes(delivery.status) }">
              <div class="timeline-dot"></div>
              <span class="timeline-label">Delivered</span>
              <span class="timeline-time" v-if="delivery.deliveredAt">
                {{ formatTime(delivery.deliveredAt) }}
              </span>
            </div>
            <div class="timeline-item" :class="{ active: ['cancelled'].includes(delivery.status) }">
              <div class="timeline-dot"></div>
              <span class="timeline-label">Cancelled</span>
              <span class="timeline-time" v-if="delivery.cancelledAt">
                {{ formatTime(delivery.cancelledAt) }}
              </span>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- No Active Deliveries -->
    <div v-if="!activeDeliveries.length && !loading" class="empty-state">
      <svg width="64" height="64" viewBox="0 0 24 24" fill="none">
        <path d="M9 17a2 2 0 11-4 0 2 2 0 014 0zM19 17a2 2 0 11-4 0 2 2 0 014 0z" stroke="#CBD5E0" stroke-width="2"/>
        <path d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h1M5 17a2 2 0 104 0m-4 0a2 2 0 114 0m6 0a2 2 0 104 0m-4 0a2 2 0 114 0" stroke="#CBD5E0" stroke-width="2"/>
      </svg>
      <h3>No active deliveries</h3>
      <p>Accepted orders will appear here</p>
    </div>

    <!-- Loading State -->
    <div v-if="loading" class="loading-state">
      <svg width="48" height="48" viewBox="0 0 24 24" fill="none" class="loading-spinner">
        <path d="M12 2v4m0 12v4M4.93 4.93l2.83 2.83m8.48 8.48l2.83 2.83M2 12h4m12 0h4M4.93 19.07l2.83-2.83m8.48-8.48l2.83-2.83" stroke="#FF7828" stroke-width="2" stroke-linecap="round"/>
      </svg>
      <p>Loading active deliveries...</p>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, onUnmounted } from "vue";

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
  serverTimestamp,
  getDocs
} from "firebase/firestore";
import { onAuthStateChanged } from "firebase/auth";

const activeDeliveries = ref([]);
const loading = ref(true);

// Format status for display
const formatStatus = (status) => {
  const statusMap = {
    'accepted': 'Accepted',
    'ready-for-pickup': 'Ready for Pickup',
    'picked-up': 'Picked Up',
    'in-transit': 'In Transit',
    'delivered': 'Delivered',
    'cancelled': 'Cancelled'
  };
  return statusMap[status] || status;
};

// Format time for display
const formatTime = (timestamp) => {
  if (!timestamp) return '';
  
  let date;
  if (timestamp instanceof Date) {
    date = timestamp;
  } else if (timestamp.toDate) {
    date = timestamp.toDate();
  } else {
    date = new Date(timestamp);
  }
  
  return date.toLocaleTimeString('en-PH', { 
    hour: '2-digit', 
    minute: '2-digit',
    hour12: true 
  });
};

// Fetch active deliveries from Firebase
const fetchActiveDeliveries = () => {
  const user = auth.currentUser;
  if (!user) {
    console.log("No rider logged in");
    loading.value = false;
    return;
  }

  console.log(" Fetching active deliveries for rider:", user.uid);

  // SIMPLIFIED QUERY - Remove complex ordering first to fix the index error
  const activeQuery = query(
    collection(db, "deliveries"),
    where("riderId", "==", user.uid),
    where("status", "in", ["accepted", "ready-for-pickup", "picked-up", "in-transit", "cancelled"])
    // Removed orderBy temporarily to avoid index issues
  );

  const unsubscribe = onSnapshot(activeQuery, 
    (snapshot) => {
      const deliveriesList = [];
      snapshot.forEach((doc) => {
        const deliveryData = doc.data();
        
        // Format the delivery data for display
        deliveriesList.push({
          id: doc.id,
          // Use displayId if available, otherwise generate one
          displayId: deliveryData.displayId || deliveryData.orderId || `ORD-${doc.id.slice(-4)}`,
          status: deliveryData.status,
          fee: deliveryData.deliveryFee || deliveryData.fee || 50,
          item: deliveryData.item || `${deliveryData.gasBrand || 'LPG'} ${deliveryData.gasType || 'Tank'} × ${deliveryData.quantity || 1}`,
          customer: deliveryData.customer || deliveryData.customerName,
          contact: deliveryData.contact || deliveryData.customerPhone,
          pickup: deliveryData.pickup || deliveryData.pickupAddress,
          deliverTo: deliveryData.deliverTo || deliveryData.deliveryAddress,
          // Additional details
          orderId: deliveryData.orderId,
          customerPhone: deliveryData.customerPhone,
          customerEmail: deliveryData.customerEmail,
          specialInstructions: deliveryData.specialInstructions,
          estimatedPrepTime: deliveryData.estimatedPrepTime,
          pickupAddress: deliveryData.pickupAddress,
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
          cancellationReason: deliveryData.cancellationReason,
          ...deliveryData
        });
      });
      
      // Manual sorting since we removed orderBy from query
      deliveriesList.sort((a, b) => {
        // Sort by status priority first
        const statusOrder = {
          'ready-for-pickup': 1,
          'in-transit': 2,
          'picked-up': 3,
          'accepted': 4,
          'cancelled': 5
        };
        
        const statusA = statusOrder[a.status] || 6;
        const statusB = statusOrder[b.status] || 6;
        
        if (statusA !== statusB) {
          return statusA - statusB;
        }
        
        // Then by creation date (newest first)
        return new Date(b.createdAt) - new Date(a.createdAt);
      });
      
      activeDeliveries.value = deliveriesList;
      loading.value = false;
      
      console.log("Active deliveries loaded:", deliveriesList.length);
    },
    (error) => {
      console.error("Error fetching active deliveries:", error);
      
      // Fallback: Try simpler query if complex one fails
      if (error.code === 'failed-precondition') {
        console.log(" Trying fallback query...");
        fetchActiveDeliveriesFallback(user.uid);
      } else {
        loading.value = false;
      }
    }
  );

  return unsubscribe;
};

// Fallback method with simpler query
const fetchActiveDeliveriesFallback = async (riderId) => {
  try {
    const activeQuery = query(
      collection(db, "deliveries"),
      where("riderId", "==", riderId)
    );
    
    const snapshot = await getDocs(activeQuery);
    const deliveriesList = [];
    
    snapshot.forEach((doc) => {
      const deliveryData = doc.data();
      // Only include active deliveries
      if (['accepted', 'ready-for-pickup', 'picked-up', 'in-transit', 'cancelled'].includes(deliveryData.status)) {
        deliveriesList.push({
          id: doc.id,
          displayId: deliveryData.displayId || deliveryData.orderId || `ORD-${doc.id.slice(-4)}`,
          status: deliveryData.status,
          fee: deliveryData.deliveryFee || deliveryData.fee || 50,
          item: deliveryData.item || `${deliveryData.gasBrand || 'LPG'} ${deliveryData.gasType || 'Tank'} × ${deliveryData.quantity || 1}`,
          customer: deliveryData.customer || deliveryData.customerName,
          contact: deliveryData.contact || deliveryData.customerPhone,
          pickup: deliveryData.pickup || deliveryData.pickupAddress,
          deliverTo: deliveryData.deliverTo || deliveryData.deliveryAddress,
          orderId: deliveryData.orderId,
          customerPhone: deliveryData.customerPhone,
          customerEmail: deliveryData.customerEmail,
          specialInstructions: deliveryData.specialInstructions,
          estimatedPrepTime: deliveryData.estimatedPrepTime,
          pickupAddress: deliveryData.pickupAddress,
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
          cancellationReason: deliveryData.cancellationReason,
          ...deliveryData
        });
      }
    });
    
    // Manual sorting
    deliveriesList.sort((a, b) => new Date(b.createdAt) - new Date(a.createdAt));
    
    activeDeliveries.value = deliveriesList;
    loading.value = false;
    
    console.log("Fallback active deliveries loaded:", deliveriesList.length);
  } catch (error) {
    console.error(" Error in fallback fetch:", error);
    loading.value = false;
  }
};

// Mark as Picked Up
const markAsPickedUp = async (delivery) => {
  try {
    const deliveryRef = doc(db, "deliveries", delivery.id);
    
    // Update delivery status
    await updateDoc(deliveryRef, {
      status: 'in-transit',
      pickedUpAt: serverTimestamp(),
      updatedAt: serverTimestamp()
    });

    // Also update the order status in orders collection
    if (delivery.orderId) {
      try {
        // Find the order by orderNumber/orderId
        const ordersQuery = query(
          collection(db, "orders"),
          where("orderNumber", "==", delivery.orderId)
        );
        const ordersSnapshot = await getDocs(ordersQuery);
        
        if (!ordersSnapshot.empty) {
          const orderDoc = ordersSnapshot.docs[0];
          await updateDoc(doc(db, "orders", orderDoc.id), {
            orderStatus: 'in-transit',
            updatedAt: serverTimestamp()
          });
          console.log(" Order status updated to 'in-transit'");
        } else {
          console.log("Order not found with orderNumber:", delivery.orderId);
        }
      } catch (orderError) {
        console.error("Error updating order status:", orderError);
        // Continue even if order update fails
      }
    }

    console.log("Delivery marked as picked up:", delivery.id);
    
    // Show success message
    alert(`Delivery ${delivery.displayId} marked as picked up successfully!`);
    
  } catch (error) {
    console.error("Error marking delivery as picked up:", error);
    alert("Failed to mark delivery as picked up. Please try again.");
  }
};

// Complete Delivery
const completeDelivery = async (delivery) => {
  try {
    const deliveryRef = doc(db, "deliveries", delivery.id);
    
    // Update delivery status
    await updateDoc(deliveryRef, {
      status: 'delivered',
      deliveredAt: serverTimestamp(),
      updatedAt: serverTimestamp()
    });

    // Also update the order status in orders collection
    if (delivery.orderId) {
      try {
        // Find the order by orderNumber/orderId
        const ordersQuery = query(
          collection(db, "orders"),
          where("orderNumber", "==", delivery.orderId)
        );
        const ordersSnapshot = await getDocs(ordersQuery);
        
        if (!ordersSnapshot.empty) {
          const orderDoc = ordersSnapshot.docs[0];
          await updateDoc(doc(db, "orders", orderDoc.id), {
            orderStatus: 'delivered',
            updatedAt: serverTimestamp()
          });
          console.log("Order status updated to 'delivered'");
        } else {
          console.log("Order not found with orderNumber:", delivery.orderId);
        }
      } catch (orderError) {
        console.error("Error updating order status:", orderError);
        // Continue even if order update fails
      }
    }

    console.log("Delivery completed:", delivery.id);
    
    // Show success message
    alert(`Delivery ${delivery.displayId} completed successfully!`);
    
  } catch (error) {
    console.error("Error completing delivery:", error);
    alert("Failed to complete delivery. Please try again.");
  }
};

// Cancel Delivery
const cancelDelivery = async (delivery) => {
  const reason = prompt('Please provide a reason for cancellation:');
  
  if (!reason) {
    alert('Cancellation reason is required.');
    return;
  }

  if (!confirm(`Are you sure you want to cancel delivery ${delivery.displayId}?`)) {
    return;
  }

  try {
    const deliveryRef = doc(db, "deliveries", delivery.id);
    
    // Update delivery status
    await updateDoc(deliveryRef, {
      status: 'cancelled',
      cancellationReason: reason,
      cancelledAt: serverTimestamp(),
      updatedAt: serverTimestamp()
    });

    // Also update the order status in orders collection
    if (delivery.orderId) {
      try {
        // Find the order by orderNumber/orderId
        const ordersQuery = query(
          collection(db, "orders"),
          where("orderNumber", "==", delivery.orderId)
        );
        const ordersSnapshot = await getDocs(ordersQuery);
        
        if (!ordersSnapshot.empty) {
          const orderDoc = ordersSnapshot.docs[0];
          await updateDoc(doc(db, "orders", orderDoc.id), {
            orderStatus: 'cancelled',
            cancellationReason: `Cancelled by rider: ${reason}`,
            updatedAt: serverTimestamp()
          });
          console.log("Order status updated to 'cancelled'");
        } else {
          console.log("Order not found with orderNumber:", delivery.orderId);
        }
      } catch (orderError) {
        console.error("Error updating order status:", orderError);
        // Continue even if order update fails
      }
    }

    console.log("Delivery cancelled:", delivery.id);
    
    // Show success message
    alert(`Delivery ${delivery.displayId} has been cancelled.`);
    
  } catch (error) {
    console.error("Error cancelling delivery:", error);
    alert("Failed to cancel delivery. Please try again.");
  }
};

// Initialize on component mount
let unsubscribe = null;

onMounted(() => {
  onAuthStateChanged(auth, (user) => {
    if (user) {
      unsubscribe = fetchActiveDeliveries();
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
.active-deliveries-page {
  font-family: 'Inter', sans-serif;
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

.deliveries-grid {
  display: flex;
  flex-direction: column;
  gap: 1.5rem;
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
  border-color: #FF7828;
}

.delivery-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 1rem;
  padding-bottom: 1rem;
  border-bottom: 1px solid #e2e8f0;
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

.status-accepted {
  background: rgba(245, 158, 11, 0.1);
  color: #f59e0b;
}

.status-ready {
  background: rgba(34, 197, 94, 0.1);
  color: #16a34a;
  border: 1px solid rgba(34, 197, 94, 0.2);
}

.status-picked-up {
  background: rgba(139, 92, 246, 0.1);
  color: #8b5cf6;
}

.status-transit {
  background: rgba(59, 130, 246, 0.1);
  color: #3b82f6;
}

.status-cancelled {
  background: rgba(239, 68, 68, 0.1);
  color: #dc2626;
}

.delivery-fee {
  color: #4a5568;
  font-size: 0.95rem;
  margin-bottom: 1rem;
}

.fee-amount {
  color: #FF7828;
  font-weight: 600;
  font-size: 1.1rem;
}

.delivery-details {
  display: flex;
  flex-direction: column;
  gap: 0.5rem;
  margin-bottom: 1.5rem;
}

.detail-item {
  color: #4a5568;
  font-size: 0.9rem;
  line-height: 1.4;
}

.detail-item strong {
  color: #2d3748;
}

.detail-item.instructions {
  padding: 0.5rem;
  background: rgba(255, 120, 40, 0.05);
  border-radius: 6px;
  border-left: 3px solid #FF7828;
  font-style: italic;
}

.cancellation-reason {
  padding: 0.75rem;
  background: rgba(239, 68, 68, 0.05);
  border-radius: 8px;
  border-left: 3px solid #dc2626;
  color: #dc2626;
  font-size: 0.9rem;
  margin-top: 0.5rem;
}

.ready-indicator {
  padding: 1rem;
  background: rgba(34, 197, 94, 0.05);
  border-radius: 8px;
  border: 1px solid rgba(34, 197, 94, 0.2);
  margin-top: 0.5rem;
}

.ready-badge {
  color: #16a34a;
  font-weight: 600;
  font-size: 0.9rem;
  display: block;
  margin-bottom: 0.5rem;
}

.ready-text {
  color: #4a5568;
  font-size: 0.8rem;
  margin: 0;
  font-style: italic;
}

.action-buttons {
  display: flex;
  flex-direction: column;
  gap: 1rem;
}

.action-btn {
  width: 100%;
  background: #FF7828;
  color: white;
  border: none;
  padding: 0.75rem 1.5rem;
  border-radius: 10px;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.3s ease;
  font-size: 0.95rem;
  box-shadow: 0 4px 12px rgba(255, 120, 40, 0.3);
}

.action-btn:hover {
  background: #e6691f;
  transform: translateY(-1px);
  box-shadow: 0 6px 16px rgba(255, 120, 40, 0.4);
}

.pickup-btn {
  background: #f59e0b;
  box-shadow: 0 4px 12px rgba(245, 158, 11, 0.3);
}

.pickup-btn:hover {
  background: #d97706;
  box-shadow: 0 6px 16px rgba(245, 158, 11, 0.4);
}

.complete-btn {
  background: #22c55e;
  box-shadow: 0 4px 12px rgba(34, 197, 94, 0.3);
}

.complete-btn:hover {
  background: #16a34a;
  box-shadow: 0 6px 16px rgba(34, 197, 94, 0.4);
}

.cancel-btn {
  background: #ef4444;
  box-shadow: 0 4px 12px rgba(239, 68, 68, 0.3);
}

.cancel-btn:hover {
  background: #dc2626;
  box-shadow: 0 6px 16px rgba(239, 68, 68, 0.4);
}

/* DUAL BUTTONS LAYOUT */
.dual-buttons {
  display: flex;
  gap: 0.75rem;
}

.dual-buttons .action-btn {
  flex: 1;
  width: auto;
}

.cancelled-info {
  padding: 1rem;
  background: rgba(239, 68, 68, 0.05);
  border-radius: 8px;
  border: 1px solid rgba(239, 68, 68, 0.2);
  text-align: center;
}

.cancelled-note {
  color: #dc2626;
  font-weight: 600;
  font-size: 1rem;
  margin: 0 0 0.5rem 0;
}

.cancelled-time {
  color: #718096;
  font-size: 0.85rem;
  margin: 0;
}

.delivery-timeline {
  display: flex;
  flex-direction: column;
  gap: 0.75rem;
  margin-top: 1rem;
  padding: 1rem;
  background: #f8fafc;
  border-radius: 8px;
}

.timeline-item {
  display: flex;
  align-items: center;
  gap: 0.75rem;
  padding: 0.5rem;
  border-radius: 6px;
  transition: all 0.3s ease;
}

.timeline-item.active {
  background: rgba(34, 197, 94, 0.05);
  border-left: 3px solid #22c55e;
}

.timeline-item.active.cancelled {
  background: rgba(239, 68, 68, 0.05);
  border-left: 3px solid #dc2626;
}

.timeline-dot {
  width: 8px;
  height: 8px;
  border-radius: 50%;
  background: #cbd5e0;
  flex-shrink: 0;
}

.timeline-item.active .timeline-dot {
  background: #22c55e;
}

.timeline-item.active.cancelled .timeline-dot {
  background: #dc2626;
}

.timeline-label {
  font-size: 0.85rem;
  color: #4a5568;
  font-weight: 500;
  flex: 1;
}

.timeline-item.active .timeline-label {
  color: #16a34a;
  font-weight: 600;
}

.timeline-item.active.cancelled .timeline-label {
  color: #dc2626;
  font-weight: 600;
}

.timeline-time {
  font-size: 0.75rem;
  color: #718096;
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
  .active-deliveries-page {
    padding: 1rem;
  }
  
  .delivery-header {
    flex-direction: column;
    align-items: flex-start;
    gap: 0.5rem;
  }
  
  .status-badge {
    align-self: flex-start;
  }
  
  .action-buttons {
    gap: 0.75rem;
  }
  
  .dual-buttons {
    flex-direction: column;
    gap: 0.75rem;
  }
  
  .delivery-timeline {
    padding: 0.75rem;
  }
  
  .timeline-item {
    flex-direction: column;
    align-items: flex-start;
    gap: 0.5rem;
  }
  
  .timeline-time {
    align-self: flex-end;
  }
}
</style>