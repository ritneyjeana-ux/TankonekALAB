<template>
  <div class="my-orders-page">
    <!-- Order History -->
    <section class="order-history">
      <h2>Order History</h2>
      <p class="subtitle">Track and view your past orders</p>

      <!-- Loading State -->
      <div v-if="loading" class="loading-state">
        <svg width="48" height="48" viewBox="0 0 24 24" fill="none" class="loading-spinner">
          <path d="M12 2v4m0 12v4M4.93 4.93l2.83 2.83m8.48 8.48l2.83 2.83M2 12h4m12 0h4M4.93 19.07l2.83-2.83m8.48-8.48l2.83-2.83" stroke="#FF7828" stroke-width="2" stroke-linecap="round"/>
        </svg>
        <p>Loading your orders...</p>
      </div>

      <!-- No Orders State -->
      <div v-else-if="filteredOrders.length === 0" class="no-orders">
        <svg width="64" height="64" viewBox="0 0 24 24" fill="none">
          <path d="M3 3h2l.4 2M7 13h10l4-8H5.4m0 0L7 13m0 0l-2.5 5.5M7 13l2.5 5.5m0 0L17 21" stroke="#CBD5E0" stroke-width="2"/>
          <circle cx="9" cy="19" r="1" stroke="#CBD5E0" stroke-width="2"/>
          <circle cx="17" cy="19" r="1" stroke="#CBD5E0" stroke-width="2"/>
        </svg>
        <h3>No orders yet</h3>
        <p>Your orders will appear here once you place them</p>
        <button class="btn-primary" @click="$emit('switchToOrder')">Place Your First Order</button>
      </div>

      <!-- Orders List -->
      <div v-else>

        <!-- REALTIME ACTIVITY -->
        <div class="realtime-activity-card">
          <div class="realtime-header">
            <h3>Real-Time Transaction Activity</h3>
            <span class="live-badge">LIVE</span>
          </div>

          <div class="realtime-grid">
            <div class="realtime-box successful-box">
              <h4>Successful Transactions</h4>
              <p class="realtime-count">
                {{ orders.filter(order => order.orderStatus === 'delivered').length }}
              </p>
            </div>

            <div class="realtime-box cancelled-box">
              <h4>Cancelled Orders</h4>
              <p class="realtime-count">
                {{ orders.filter(order => order.orderStatus === 'cancelled').length }}
              </p>
            </div>

            <div class="realtime-box pending-box">
              <h4>Active Orders</h4>
              <p class="realtime-count">
                {{ orders.filter(order => ['pending','accepted','assigned','ready-for-pickup','in-transit'].includes(order.orderStatus)).length }}
              </p>
            </div>
          </div>

          <div class="realtime-feed">
            <div
              v-for="activity in orders.slice(0, 10)"
              :key="activity.id"
              class="activity-item"
            >
              <div class="activity-status-dot" :class="getStatusClass(activity.orderStatus)"></div>

              <div class="activity-content">
                <strong>
                  {{ activity.gasBrand }} - {{ activity.gasType }}
                </strong>

                <p>
                  {{ formatStatus(activity.orderStatus) }}
                  • ₱{{ Number(activity.total || activity.totalAmount || 0).toLocaleString() }}
                </p>

                <small>
                  {{ formatDateTime(getOrderRealtimeTimestamp(activity)) }}
                </small>
              </div>
            </div>
          </div>
        </div>

        <div
          v-for="order in filteredOrders"
          :key="order.id"
          class="order-card"
          :data-order-id="order.id"
        >
          <div class="order-top">
            <div class="order-left">
              <div class="order-id">{{ order.orderNumber || order.orderId }}</div>
              <span
                class="status"
                :class="getStatusClass(order.orderStatus)"
              >
                <i v-if="order.orderStatus === 'delivered'" class="fa fa-check-circle"></i>
                <i v-else-if="order.orderStatus === 'in-transit'" class="fa fa-truck"></i>
                <i v-else-if="order.orderStatus === 'ready-for-pickup'" class="fa fa-box"></i>
                <i v-else-if="order.orderStatus === 'assigned'" class="fa fa-user"></i>
                <i v-else-if="order.orderStatus === 'accepted'" class="fa fa-check"></i>
                <i v-else-if="order.orderStatus === 'pending'" class="fa fa-clock"></i>
                <i v-else-if="order.orderStatus === 'cancelled'" class="fa fa-times-circle"></i>
                <i v-else class="fa fa-clock"></i>
                {{ formatStatus(order.orderStatus) }}
              </span>
            </div>

            <div class="order-price">₱{{ Number(order.total || order.totalAmount || 0).toLocaleString() }}</div>
          </div>

          <div class="order-details">
            <div class="store-name">
              {{ order.retailerName }} • {{ formatDate(order.orderDate) }}
            </div>

            <div class="info">
              <p><strong>Item:</strong> {{ order.gasBrand }} - {{ order.gasType }} × {{ order.quantity }}</p>
              <p><strong>Delivery Address:</strong> {{ order.deliveryAddress }}</p>
              <p><strong>Contact Number:</strong> {{ order.customerPhone }}</p>
              <p v-if="order.deliveryPerson"><strong>Delivery Person:</strong> {{ order.deliveryPerson }}</p>
              <p v-else-if="order.orderStatus === 'assigned' || order.orderStatus === 'ready-for-pickup' || order.orderStatus === 'in-transit'"><strong>Delivery Person:</strong> {{ getDeliveryPersonName(order) }}</p>
              <p v-else><strong>Delivery Person:</strong> Not assigned yet</p>
              <p><strong>Payment Method:</strong> {{ formatPaymentMethod(order.paymentMethod) }}</p>
              <p v-if="getOrderRealtimeTimestamp(order)"><strong>Last Status Update:</strong> {{ formatDateTime(getOrderRealtimeTimestamp(order)) }}</p>
              <p v-if="Number(order.distance) > 0"><strong>Distance:</strong> {{ Number(order.distance).toFixed(1) }} km</p>
              
              <!-- Status Timeline Info -->
              <div class="status-timeline">
                <!-- Pending Status -->
                <div v-if="order.orderStatus === 'pending'" class="status-info pending">
                  <p class="status-note">Waiting for retailer to accept your order...</p>
                </div>
                
                <!-- Accepted Status -->
                <div v-else-if="order.orderStatus === 'accepted'" class="status-info accepted">
                  <p class="status-note">Order accepted by retailer - Preparing your items</p>
                </div>
                
                <!-- Assigned Status -->
                <div v-else-if="order.orderStatus === 'assigned'" class="status-info assigned">
                  <p class="status-note"> Driver assigned - Waiting for pickup</p>
                  <p v-if="getDeliveryPersonName(order)" class="driver-details">Driver: {{ getDeliveryPersonName(order) }}</p>
                </div>
                
                <!-- Ready for Pickup Status -->
                <div v-else-if="order.orderStatus === 'ready-for-pickup'" class="status-info ready">
                  <p class="status-note">Ready for driver pickup</p>
                  <p v-if="getDeliveryPersonName(order)" class="driver-details">Driver: {{ getDeliveryPersonName(order) }} will pick up soon</p>
                </div>
                
                <!-- In Transit Status -->
                <div v-else-if="order.orderStatus === 'in-transit'" class="status-info transit">
                  <p class="status-note">Order is out for delivery</p>
                  <p v-if="getDeliveryPersonName(order)" class="driver-details">Driver: {{ getDeliveryPersonName(order) }} is on the way</p>
                  <p v-if="getOrderRealtimeTimestamp(order)" class="eta">Updated: {{ formatDateTime(getOrderRealtimeTimestamp(order)) }}</p>
                </div>
                
                <!-- Delivered Status -->
                <div v-else-if="order.orderStatus === 'delivered'" class="status-info delivered">
                  <p class="status-note">Order delivered successfully</p>
                  <p v-if="order.deliveredAt || order.statusUpdatedAt" class="delivery-time">Delivered at: {{ formatDateTime(order.deliveredAt || order.statusUpdatedAt) }}</p>
                  
                  <!-- Auto-remove countdown -->
                  <div v-if="!order.hasRated" class="auto-remove-notice">
                    <p class="countdown-text">
                      ⏱️ This order will be automatically removed in: 
                      <strong>{{ getRemainingTime(order.deliveredAt) }}</strong>
                    </p>
                  </div>
                  
                  <!-- Rating Section - Only show if not yet rated -->
                  <div v-if="!order.hasRated" class="rating-section">
                    <p class="rating-prompt">Rate your experience with this order:</p>
                    <div class="rating-stars">
                      <span 
                        v-for="star in 5" 
                        :key="star"
                        class="star"
                        :class="{ 'filled': star <= currentRating[order.id] }"
                        @click="setRating(order.id, star)"
                      >
                        ★
                      </span>
                    </div>
                    
                    <!-- Feedback/Comment Section -->
                    <div v-if="currentRating[order.id]" class="feedback-section">
                      <label class="feedback-label">Share your feedback (optional):</label>
                      <textarea 
                        v-model="currentFeedback[order.id]"
                        class="feedback-textarea"
                        placeholder="Tell us about your experience with this order..."
                        maxlength="500"
                        rows="3"
                      ></textarea>
                      <div class="feedback-counter">
                        {{ currentFeedback[order.id]?.length || 0 }}/500 characters
                      </div>
                    </div>
                    
                    <button 
                      v-if="currentRating[order.id]"
                      class="submit-rating-btn"
                      @click="submitRating(order)"
                    >
                      Submit Rating & Feedback
                    </button>
                  </div>
                  
                  <!-- Show existing rating and feedback -->
                  <div v-else-if="order.rating" class="existing-rating">
                    <p class="rating-display">
                      Your rating: 
                      <span class="rating-stars-display">
                        <span v-for="star in 5" :key="star" :class="{ 'filled': star <= order.rating }">★</span>
                      </span>
                      ({{ order.rating }}/5)
                    </p>
                    <div v-if="order.feedback" class="existing-feedback">
                      <p class="feedback-display">
                        <strong>Your feedback:</strong> "{{ order.feedback }}"
                      </p>
                    </div>
                    <p class="rated-notice">✅ Thank you for your feedback! This order will be removed shortly.</p>
                  </div>
                </div>
                
                <!-- Cancelled Status -->
                <div v-else-if="order.orderStatus === 'cancelled'" class="status-info cancelled">
                  <p class="status-note"> Order cancelled</p>
                  <p v-if="order.cancellationReason" class="cancellation-reason">Reason: {{ order.cancellationReason }}</p>
                </div>
              </div>
            </div>

            <!-- Action Buttons -->
            <div class="order-actions">
              <!-- Cancel Button - ONLY SHOWS FOR PENDING ORDERS -->
              <button 
                v-if="canCancelOrder(order.orderStatus)" 
                class="cancel-btn" 
                @click="openCancelModal(order)"
              >
                Cancel Order
              </button>
              
              <!-- Contact Retailer Button -->
              <button 
                v-if="order.orderStatus === 'pending' || order.orderStatus === 'accepted'" 
                class="contact-btn"
                @click="contactRetailer(order)"
              >
                Contact Retailer
              </button>
              
              <!-- Track Button for Active Orders -->
              <button 
                v-if="canTrackOrder(order.orderStatus)" 
                class="track-btn"
                @click="trackOrder(order)"
              >
                Track Order
              </button>
              
              <!-- Contact Driver Button -->
              <button 
                v-if="getDeliveryPersonName(order) && (order.orderStatus === 'assigned' || order.orderStatus === 'ready-for-pickup' || order.orderStatus === 'in-transit')" 
                class="contact-driver-btn"
                @click="contactDriver(order)"
              >
                Contact Driver
              </button>
              
              <!-- Delivered Orders - Show Rate Now and Order Again -->
              <button 
                v-if="order.orderStatus === 'delivered' && !order.hasRated" 
                class="rate-now-btn"
                @click="scrollToRating(order.id)"
              >
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none">
                  <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z" stroke="currentColor" stroke-width="2"/>
                </svg>
                Rate Now
              </button>
              
              <!-- Reorder Button for Delivered/Cancelled Orders -->
              <button 
                v-if="order.orderStatus === 'delivered' || order.orderStatus === 'cancelled'" 
                class="reorder-btn"
                @click="reorder(order)"
              >
                Order Again
              </button>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- Cancel Order Confirmation Modal -->
    <div v-if="showCancelModal" class="modal-overlay">
      <div class="modal">
        <div class="modal-icon">
          <svg width="48" height="48" viewBox="0 0 24 24" fill="none">
            <path d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.35 16.5c-.77.833.192 2.5 1.732 2.5z" stroke="#FF7828" stroke-width="2"/>
          </svg>
        </div>
        <h3>Cancel Order</h3>
        <p>Are you sure you want to cancel order <strong>{{ selectedOrder?.orderNumber }}</strong>?</p>
        <div class="modal-buttons">
          <button class="modal-btn-cancel" @click="closeCancelModal">No, Keep Order</button>
          <button class="modal-btn-confirm" @click="confirmCancel">Yes, Cancel Order</button>
        </div>
      </div>
    </div>

    <!-- Logout Confirmation Modal -->
    <div v-if="showLogoutModal" class="modal-overlay">
      <div class="modal">
        <p>⚠️ Are you sure you want to logout?</p>
        <div class="modal-buttons">
          <button @click="confirmLogout">Yes</button>
          <button @click="cancelLogout">No</button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted } from "vue";
import { router } from "@inertiajs/vue3";

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
  arrayUnion,
  increment,
  deleteDoc,
  serverTimestamp
} from "firebase/firestore";
import { onAuthStateChanged } from "firebase/auth";

const userName = ref("Customer");

// Orders data from Firebase
const orders = ref([]);
const loading = ref(true);

// Store delivery persons data
const deliveryPersons = ref({});

// Cancel Order Modal
const showCancelModal = ref(false);
const selectedOrder = ref(null);

// Rating state
const currentRating = ref({});

// Feedback state
const currentFeedback = ref({});

// Timer for auto-remove countdown
const countdownTimer = ref(null);
const currentTime = ref(new Date());
const realtimeClockTimer = ref(null);
const ordersUnsubscribe = ref(null);
const deliveriesUnsubscribe = ref(null);
const latestDeliveryUpdates = ref([]);

const toDateSafe = (value, fallback = null) => {
  if (!value) return fallback;
  if (value?.toDate) return value.toDate();

  const parsed = new Date(value);
  return Number.isNaN(parsed.getTime()) ? fallback : parsed;
};

const getOrderRealtimeTimestamp = (order) => {
  const matchingDelivery = latestDeliveryUpdates.value?.length
    ? getFreshestMatchingDelivery(order, latestDeliveryUpdates.value)
    : null;

  return toDateSafe(matchingDelivery?.statusUpdatedAt, null) ||
    toDateSafe(matchingDelivery?.updatedAt, null) ||
    toDateSafe(matchingDelivery?.deliveredAt, null) ||
    toDateSafe(matchingDelivery?.cancelledAt, null) ||
    order.statusUpdatedAt ||
    order.updatedAt ||
    order.deliveredAt ||
    order.cancelledAt ||
    order.orderDate ||
    order.createdAt ||
    null;
};

const normalizeOrderKey = (value) => {
  return value ? String(value).trim().toLowerCase() : "";
};

const isMatchingDeliveryOrder = (order, deliveryData) => {
  const orderKeys = [
    order.id,
    order.orderNumber,
    order.orderId,
    order.displayId
  ].map(normalizeOrderKey).filter(Boolean);

  const deliveryKeys = [
    deliveryData.orderDocId,
    deliveryData.orderId,
    deliveryData.displayId,
    deliveryData.orderNumber
  ].map(normalizeOrderKey).filter(Boolean);

  return orderKeys.some((key) => deliveryKeys.includes(key));
};

const getDeliveryFreshTime = (deliveryData) => {
  return toDateSafe(deliveryData?.statusUpdatedAt, null) ||
    toDateSafe(deliveryData?.updatedAt, null) ||
    toDateSafe(deliveryData?.deliveredAt, null) ||
    toDateSafe(deliveryData?.cancelledAt, null) ||
    toDateSafe(deliveryData?.createdAt, null) ||
    new Date(0);
};

const getFreshestMatchingDelivery = (order, deliveryUpdates) => {
  return deliveryUpdates
    .filter((deliveryData) => isMatchingDeliveryOrder(order, deliveryData))
    .sort((a, b) => getDeliveryFreshTime(b) - getDeliveryFreshTime(a))[0] || null;
};

// Computed property to filter orders - remove delivered orders after 1 day or if rated
const filteredOrders = computed(() => {
  const now = currentTime.value;
  const oneDayInMs = 24 * 60 * 60 * 1000; // 1 day in milliseconds

  return orders.value.filter(order => {
    // Keep all non-delivered orders
    if (order.orderStatus !== 'delivered') {
      return true;
    }
    
    // For delivered orders, check if they should be shown
    const deliveredTime = order.deliveredAt ? new Date(order.deliveredAt) : new Date(order.orderDate);
    const timeSinceDelivery = now - deliveredTime;
    
    // Remove if:
    // 1. User has already rated, OR
    // 2. More than 1 day has passed since delivery
    if (order.hasRated || timeSinceDelivery > oneDayInMs) {
      return false;
    }
    
    return true;
  });
});

// Fetch orders from Firebase
const fetchOrders = () => {
  const user = auth.currentUser;
  if (!user) {
    console.log("No user logged in");
    loading.value = false;
    return;
  }

  if (ordersUnsubscribe.value) {
    ordersUnsubscribe.value();
    ordersUnsubscribe.value = null;
  }

  console.log("Fetching orders for user:", user.uid);

  const ordersQuery = query(
    collection(db, "orders"),
    where("customerId", "==", user.uid),
    orderBy("orderDate", "desc")
  );

  const unsubscribe = onSnapshot(
    ordersQuery,
    { includeMetadataChanges: true },
    async (snapshot) => {
      const ordersList = [];

      snapshot.forEach((docSnap) => {
        const orderData = docSnap.data();

        let orderStatus = orderData.orderStatus || orderData.status;
        if (!orderStatus || orderStatus === "") {
          orderStatus = "pending";
        }

        const updatedAt = toDateSafe(orderData.updatedAt, null);
        const statusUpdatedAt = toDateSafe(orderData.statusUpdatedAt, updatedAt || new Date());

        ordersList.push({
          id: docSnap.id,
          ...orderData,
          orderStatus,
          status: orderStatus,
          orderDate: toDateSafe(orderData.orderDate, new Date()),
          estimatedDelivery: toDateSafe(orderData.estimatedDelivery, null),
          deliveredAt: toDateSafe(orderData.deliveredAt, null),
          cancelledAt: toDateSafe(orderData.cancelledAt, null),
          createdAt: toDateSafe(orderData.createdAt, new Date()),
          updatedAt: updatedAt || statusUpdatedAt,
          statusUpdatedAt,
          rating: orderData.rating || null,
          hasRated: orderData.hasRated || false,
          feedback: orderData.feedback || null
        });
      });

      orders.value = mergeOrdersWithDeliveryUpdates(ordersList, latestDeliveryUpdates.value);
      loading.value = false;
      console.log("Orders loaded:", orders.value.length, "orders");

      await fetchDeliveryPersons(orders.value);
      startAutoRemoveCheck();
    },
    (error) => {
      console.error("Error fetching orders:", error);
      loading.value = false;
      alert("Error loading orders. Please try again.");
    }
  );

  ordersUnsubscribe.value = unsubscribe;
  return unsubscribe;
};

const mergeOrdersWithDeliveryUpdates = (ordersList, deliveryUpdates) => {
  if (!Array.isArray(ordersList)) return [];

  return ordersList.map((order) => {
    const matchingDelivery = Array.isArray(deliveryUpdates)
      ? getFreshestMatchingDelivery(order, deliveryUpdates)
      : null;

    if (!matchingDelivery) {
      return {
        ...order,
        orderStatus: order.orderStatus || order.status || "pending",
        status: order.status || order.orderStatus || "pending",
        total: Number(order.total || order.totalAmount || 0),
        totalAmount: Number(order.totalAmount || order.total || 0)
      };
    }

    const syncedStatus = matchingDelivery.status || matchingDelivery.orderStatus || order.orderStatus || order.status || "pending";
    const syncedUpdatedAt =
      toDateSafe(matchingDelivery.statusUpdatedAt, null) ||
      toDateSafe(matchingDelivery.updatedAt, null) ||
      toDateSafe(matchingDelivery.deliveredAt, null) ||
      toDateSafe(matchingDelivery.cancelledAt, null) ||
      toDateSafe(order.statusUpdatedAt, null) ||
      toDateSafe(order.updatedAt, null) ||
      new Date();

    const syncedDeliveredAt = toDateSafe(matchingDelivery.deliveredAt, null) || order.deliveredAt;
    const syncedCancelledAt = toDateSafe(matchingDelivery.cancelledAt, null) || order.cancelledAt;
    const syncedEstimatedDelivery = toDateSafe(matchingDelivery.estimatedDelivery, null) || order.estimatedDelivery;

    return {
      ...order,

      // Realtime status from Firebase deliveries collection
      orderStatus: syncedStatus,
      status: syncedStatus,

      // Realtime rider/delivery info from Firebase deliveries collection
      deliveryPersonId: matchingDelivery.riderId || matchingDelivery.deliveryPersonId || order.deliveryPersonId,
      deliveryPerson: matchingDelivery.riderName || matchingDelivery.deliveryPerson || order.deliveryPerson,
      riderId: matchingDelivery.riderId || order.riderId,
      riderName: matchingDelivery.riderName || order.riderName,

      // Realtime timestamps from Firebase
      deliveredAt: syncedDeliveredAt,
      cancelledAt: syncedCancelledAt,
      estimatedDelivery: syncedEstimatedDelivery,
      updatedAt: syncedUpdatedAt,
      statusUpdatedAt: syncedUpdatedAt,

      // Realtime displayed fields from Firebase deliveries, fallback to orders
      retailerStatus: matchingDelivery.retailerStatus || order.retailerStatus,
      paymentStatus: matchingDelivery.paymentStatus || order.paymentStatus,
      deliveryAddress: matchingDelivery.customerAddress || matchingDelivery.address || order.deliveryAddress,
      customerPhone: matchingDelivery.customerPhone || order.customerPhone,
      retailerName: matchingDelivery.retailerName || matchingDelivery.retailerBusinessName || order.retailerName,
      gasBrand: matchingDelivery.gasBrand || order.gasBrand,
      gasType: matchingDelivery.gasType || order.gasType,
      quantity: Number(matchingDelivery.quantity || order.quantity || 1),
      distance: Number(matchingDelivery.distance || order.distance || 0),
      total: Number(matchingDelivery.total || matchingDelivery.totalAmount || order.total || order.totalAmount || 0),
      totalAmount: Number(matchingDelivery.totalAmount || matchingDelivery.total || order.totalAmount || order.total || 0),
      deliveryFee: Number(matchingDelivery.deliveryFee || order.deliveryFee || 0),
      subtotal: Number(matchingDelivery.subtotal || order.subtotal || 0),
      paymentMethod: matchingDelivery.paymentMethod || order.paymentMethod
    };
  });
};

// Fetch matching delivery updates in real-time and sync status/time/rider data to customer orders
const fetchDeliveryStatusUpdates = (userId) => {
  if (deliveriesUnsubscribe.value) {
    deliveriesUnsubscribe.value();
    deliveriesUnsubscribe.value = null;
  }

  const deliveriesQuery = query(
    collection(db, "deliveries"),
    where("customerId", "==", userId)
  );

  const unsubscribe = onSnapshot(
    deliveriesQuery,
    { includeMetadataChanges: true },
    async (snapshot) => {
      const deliveryUpdates = [];

      snapshot.forEach((docSnap) => {
        deliveryUpdates.push({
          id: docSnap.id,
          ...docSnap.data()
        });
      });

      latestDeliveryUpdates.value = deliveryUpdates;
      orders.value = mergeOrdersWithDeliveryUpdates(orders.value, latestDeliveryUpdates.value);

      await fetchDeliveryPersons(orders.value);
    },
    (error) => {
      console.error("Error syncing delivery status updates:", error);
    }
  );

  deliveriesUnsubscribe.value = unsubscribe;
  return unsubscribe;
};

// Start auto-remove check for delivered orders
const startAutoRemoveCheck = () => {
  // Clear existing timer
  if (countdownTimer.value) {
    clearInterval(countdownTimer.value);
  }
  
  // Check every minute for orders that need to be removed
  countdownTimer.value = setInterval(() => {
    const now = currentTime.value;
    const oneDayInMs = 24 * 60 * 60 * 1000;
    
    orders.value.forEach(async (order) => {
      if (order.orderStatus === 'delivered') {
        const deliveredTime = order.deliveredAt ? new Date(order.deliveredAt) : new Date(order.orderDate);
        const timeSinceDelivery = now - deliveredTime;
        
        // Remove order if more than 1 day has passed
        if (timeSinceDelivery > oneDayInMs) {
          console.log(`Auto-removing order ${order.id} after 1 day`);
          await removeOrderFromView(order.id);
        }
      }
    });
  }, 60000); // Check every minute
};

// Remove order from local view (doesn't delete from Firebase)
const removeOrderFromView = (orderId) => {
  const index = orders.value.findIndex(order => order.id === orderId);
  if (index !== -1) {
    orders.value.splice(index, 1);
  }
};

// Calculate remaining time for auto-remove
const getRemainingTime = (deliveredAt) => {
  if (!deliveredAt) return "24 hours";
  
  const deliveredTime = new Date(deliveredAt);
  const now = currentTime.value;
  const oneDayInMs = 24 * 60 * 60 * 1000;
  const timePassed = now - deliveredTime;
  const timeRemaining = oneDayInMs - timePassed;
  
  if (timeRemaining <= 0) {
    return "Moving to rewards...";
  }
  
  const hours = Math.floor(timeRemaining / (60 * 60 * 1000));
  const minutes = Math.floor((timeRemaining % (60 * 60 * 1000)) / (60 * 1000));
  
  return `${hours}h ${minutes}m`;
};

// Keep displayed time/countdown updated in real time
const startRealtimeClock = () => {
  if (realtimeClockTimer.value) {
    clearInterval(realtimeClockTimer.value);
  }

  realtimeClockTimer.value = setInterval(() => {
    currentTime.value = new Date();
    orders.value = mergeOrdersWithDeliveryUpdates([...orders.value], latestDeliveryUpdates.value);
  }, 1000);
};

// Fetch delivery persons data from riders collection
const fetchDeliveryPersons = async (ordersList) => {
  try {
    // Get all rider IDs from orders that are assigned, ready-for-pickup, or in-transit
    const riderIds = new Set();
    
    ordersList.forEach(order => {
      if ((order.orderStatus === 'assigned' || order.orderStatus === 'ready-for-pickup' || order.orderStatus === 'in-transit') && order.deliveryPersonId) {
        riderIds.add(order.deliveryPersonId);
      }

      const matchingDelivery = latestDeliveryUpdates.value.find((deliveryData) =>
        isMatchingDeliveryOrder(order, deliveryData)
      );

      if (matchingDelivery?.riderId || matchingDelivery?.deliveryPersonId) {
        riderIds.add(matchingDelivery.riderId || matchingDelivery.deliveryPersonId);
      }
    });

    if (riderIds.size === 0) {
      console.log("No rider IDs found to fetch");
      return;
    }

    console.log("Fetching delivery persons for IDs:", Array.from(riderIds));

    // Fetch rider details from riders collection
    for (const riderId of riderIds) {
      try {
        const riderQuery = query(
          collection(db, "riders"),
          where("__name__", "==", riderId)
        );
        
        const riderSnapshot = await getDocs(riderQuery);
        
        if (!riderSnapshot.empty) {
          const riderData = riderSnapshot.docs[0].data();
          deliveryPersons.value[riderId] = {
            fullName: riderData.fullName || "Delivery Partner",
            phone: riderData.phone || "N/A",
            vehicleType: riderData.vehicleType || "Vehicle"
          };
          console.log(`✅ Loaded rider data for ${riderId}:`, deliveryPersons.value[riderId]);
        } else {
          console.log(`❌ No rider found with ID: ${riderId}`);
          deliveryPersons.value[riderId] = {
            fullName: "Delivery Partner",
            phone: "N/A",
            vehicleType: "Vehicle"
          };
        }
      } catch (error) {
        console.error(`Error fetching rider ${riderId}:`, error);
        deliveryPersons.value[riderId] = {
          fullName: "Delivery Partner",
          phone: "N/A",
          vehicleType: "Vehicle"
        };
      }
    }
    
    console.log("📦 Final delivery persons data:", deliveryPersons.value);
  } catch (error) {
    console.error("Error fetching delivery persons:", error);
  }
};

// Get delivery person name
const getDeliveryPersonName = (order) => {
  if (order.deliveryPerson && order.deliveryPerson !== "Delivery Partner") {
    return order.deliveryPerson;
  }

  const matchingDelivery = latestDeliveryUpdates.value.find((deliveryData) =>
    isMatchingDeliveryOrder(order, deliveryData)
  );

  if (matchingDelivery?.riderName || matchingDelivery?.deliveryPerson) {
    return matchingDelivery.riderName || matchingDelivery.deliveryPerson;
  }

  if (order.deliveryPersonId && deliveryPersons.value[order.deliveryPersonId]) {
    return deliveryPersons.value[order.deliveryPersonId].fullName;
  }

  if (matchingDelivery?.riderId && deliveryPersons.value[matchingDelivery.riderId]) {
    return deliveryPersons.value[matchingDelivery.riderId].fullName;
  }

  if (order.orderStatus === 'assigned' || order.orderStatus === 'ready-for-pickup' || order.orderStatus === 'in-transit') {
    return "Delivery Partner";
  }

  return "Not assigned yet";
};

// Format status for display
const formatStatus = (status) => {
  const statusMap = {
    'pending': 'Pending',
    'accepted': 'Accepted',
    'assigned': 'Driver Assigned',
    'ready-for-pickup': 'Ready for Pickup',
    'in-transit': 'In Transit',
    'delivered': 'Delivered',
    'cancelled': 'Cancelled'
  };
  return statusMap[status] || status;
};

// Get CSS class for status
const getStatusClass = (status) => {
  const classMap = {
    'pending': 'pending',
    'accepted': 'accepted',
    'assigned': 'assigned',
    'ready-for-pickup': 'ready',
    'in-transit': 'transit',
    'delivered': 'delivered',
    'cancelled': 'cancelled'
  };
  return classMap[status] || 'pending';
};

// Format date for display
const formatDate = (date) => {
  if (!date) return 'Unknown date';
  const d = new Date(date);
  return d.toLocaleDateString('en-US', {
    year: 'numeric',
    month: 'short',
    day: 'numeric'
  });
};

// Format date and time for display
const formatDateTime = (date) => {
  if (!date) return 'Unknown';

  const d = new Date(date);
  if (Number.isNaN(d.getTime())) return 'Unknown';

  return d.toLocaleString('en-US', {
    year: 'numeric',
    month: 'short',
    day: 'numeric',
    hour: '2-digit',
    minute: '2-digit',
    second: '2-digit',
    hour12: true
  });
};

// Format payment method
const formatPaymentMethod = (method) => {
  const methodMap = {
    'cash': 'Cash on Delivery',
    'gcash': 'GCash',
    'card': 'Credit/Debit Card',
    'paypal': 'PayPal Sandbox'
  };
  return methodMap[method] || method;
};

// Check if order can be cancelled - ONLY PENDING ORDERS
const canCancelOrder = (status) => {
  // Only show cancel button for pending orders (not yet accepted by retailer)
  return status === 'pending';
};

// Check if order can be tracked
const canTrackOrder = (status) => {
  return ['accepted', 'assigned', 'ready-for-pickup', 'in-transit'].includes(status);
};

// Rating functions
const setRating = (orderId, rating) => {
  currentRating.value[orderId] = rating;
};

const scrollToRating = (orderId) => {
  const orderElement = document.querySelector(`[data-order-id="${orderId}"]`);
  if (orderElement) {
    orderElement.scrollIntoView({ behavior: 'smooth' });
  }
};

const submitRating = async (order) => {
  const rating = currentRating.value[order.id];
  if (!rating || rating < 1 || rating > 5) {
    alert('Please select a rating between 1 and 5 stars.');
    return;
  }

  try {
    const orderRef = doc(db, "orders", order.id);
    
    // Prepare update data
    const updateData = {
      rating: rating,
      hasRated: true,
      ratedAt: serverTimestamp(),
      updatedAt: serverTimestamp()
    };

    // Add feedback if provided
    if (currentFeedback.value[order.id] && currentFeedback.value[order.id].trim()) {
      updateData.feedback = currentFeedback.value[order.id].trim();
    }
    
    // Update the order with rating and feedback
    await updateDoc(orderRef, updateData);

    // Update retailer's overall rating in retailers collection with customer information
    await updateRetailerRating(order, rating, currentFeedback.value[order.id]);

    // Clear current rating and feedback
    delete currentRating.value[order.id];
    delete currentFeedback.value[order.id];
    
    // Remove order from view immediately after rating
    removeOrderFromView(order.id);
    
    alert('Thank you for your rating and feedback! This order will be removed from your list.');
  } catch (error) {
    console.error("Error submitting rating:", error);
    alert("Failed to submit rating. Please try again.");
  }
};

const updateRetailerRating = async (order, newRating, feedback) => {
  try {
    const retailerRef = doc(db, "retailers", order.retailerId);
    
    // Get current user information
    const user = auth.currentUser;
    let customerName = "Customer";
    let customerPhone = order.customerPhone || "N/A";
    
    // If we have user info, get the customer name
    if (user && user.displayName) {
      customerName = user.displayName;
    } else if (order.customerName) {
      customerName = order.customerName;
    }

    // Prepare feedback data with customer information
    const feedbackData = {
      rating: newRating,
      comment: feedback && feedback.trim() ? feedback.trim() : "No comment provided",
      createdAt: new Date(),
      customerId: order.customerId,
      customerName: customerName,
      customerPhone: customerPhone,
      orderId: order.id,
      orderNumber: order.orderNumber || order.orderId,
      orderDate: order.orderDate
    };

    // Update retailer document with the new feedback including customer info
    const updateData = {
      ratings: arrayUnion(newRating),
      totalRatings: increment(1),
      feedbacks: arrayUnion(feedbackData),
      updatedAt: new Date()
    };

    await updateDoc(retailerRef, updateData);

    // Recalculate average rating
    await recalculateAverageRating(order.retailerId);
    
    console.log("✅ Rating and feedback submitted to retailer with customer information");
  } catch (error) {
    console.error("Error updating retailer rating:", error);
  }
};

const recalculateAverageRating = async (retailerId) => {
  try {
    const retailerRef = doc(db, "retailers", retailerId);
    const retailerDoc = await getDoc(retailerRef);
    
    if (retailerDoc.exists()) {
      const retailerData = retailerDoc.data();
      const ratings = retailerData.ratings || [];
      
      if (ratings.length > 0) {
        const averageRating = ratings.reduce((sum, rating) => sum + rating, 0) / ratings.length;
        
        await updateDoc(retailerRef, {
          rating: parseFloat(averageRating.toFixed(1)),
          updatedAt: new Date()
        });
        
        console.log(`✅ Updated retailer ${retailerId} average rating to: ${averageRating.toFixed(1)}`);
      }
    }
  } catch (error) {
    console.error("Error recalculating average rating:", error);
  }
};

// Cancel Order Modal Functions
function openCancelModal(order) {
  selectedOrder.value = order;
  showCancelModal.value = true;
}

function closeCancelModal() {
  showCancelModal.value = false;
  selectedOrder.value = null;
}

async function confirmCancel() {
  if (selectedOrder.value) {
    try {
      // Update order status in Firebase to cancelled
      const orderRef = doc(db, "orders", selectedOrder.value.id);
      await updateDoc(orderRef, {
        orderStatus: 'cancelled',
        status: 'cancelled',
        updatedAt: serverTimestamp(),
        statusUpdatedAt: serverTimestamp(),
        cancelledAt: serverTimestamp(),
        cancelledBy: 'customer',
        cancellationReason: 'Cancelled by customer'
      });

      // Show success message
      alert(`Order ${selectedOrder.value.orderNumber} has been cancelled successfully.`);
      
      closeCancelModal();
    } catch (error) {
      console.error("Error cancelling order:", error);
      alert("Failed to cancel order. Please try again.");
    }
  }
}

// Contact retailer
function contactRetailer(order) {
  // This would typically open phone dialer or chat
  alert(`Contacting retailer: ${order.retailerName}\n\nYou can call them for order updates.`);
}

// Contact driver
function contactDriver(order) {
  const driverName = getDeliveryPersonName(order);
  if (driverName && driverName !== "Delivery Partner" && driverName !== "Not assigned yet") {
    alert(`Contacting driver: ${driverName}\n\nThey will assist you with delivery updates.`);
  } else {
    alert(`Driver contact information is not available yet.\n\nPlease wait for driver assignment.`);
  }
}

// Track order function
function trackOrder(order) {
  const statusInfo = {
    'accepted': 'Retailer is preparing your order',
    'assigned': 'Driver has been assigned',
    'ready-for-pickup': 'Order is ready for driver pickup',
    'in-transit': 'Order is on the way to you'
  };
  
  const additionalInfo = statusInfo[order.orderStatus] || '';
  const driverName = getDeliveryPersonName(order);
  
  alert(`Tracking order ${order.orderNumber}\n\nStatus: ${formatStatus(order.orderStatus)}\n${driverName && driverName !== "Delivery Partner" ? `Driver: ${driverName}\n` : ''}${additionalInfo}\n${getOrderRealtimeTimestamp(order) ? `Last Status Update: ${formatDateTime(getOrderRealtimeTimestamp(order))}` : ''}`);
}

// Reorder function
function reorder(order) {
  // Emit event to parent to switch to order tab and pre-fill data
  emit('reorder', order);
  alert(`Reorder functionality for ${order.gasBrand} - ${order.gasType} will be implemented soon!`);
}

// Logout modal
const showLogoutModal = ref(false);

function logout() {
  showLogoutModal.value = true;
}

function confirmLogout() {
  localStorage.removeItem("retailers");
  router.visit("/");
  showLogoutModal.value = false;
}

function cancelLogout() {
  showLogoutModal.value = false;
}

// Emit events to parent component
const emit = defineEmits(['switchToOrder', 'reorder']);

// Initialize on component mount
onMounted(() => {
  onAuthStateChanged(auth, (user) => {
    if (user) {
      latestDeliveryUpdates.value = [];
      fetchDeliveryStatusUpdates(user.uid);
      fetchOrders();
      startRealtimeClock();
    } else {
      loading.value = false;
      orders.value = [];
      latestDeliveryUpdates.value = [];
      console.log("No user logged in for orders");
    }
  });
});

// Clean up timer when component unmounts
onUnmounted(() => {
  if (countdownTimer.value) {
    clearInterval(countdownTimer.value);
  }

  if (realtimeClockTimer.value) {
    clearInterval(realtimeClockTimer.value);
  }

  if (ordersUnsubscribe.value) {
    ordersUnsubscribe.value();
  }

  if (deliveriesUnsubscribe.value) {
    deliveriesUnsubscribe.value();
  }

  latestDeliveryUpdates.value = [];
});
</script>

<style scoped>
/* Keep all existing CSS styles the same, and add new styles for feedback section */

.my-orders-page {
  font-family: "Inter", sans-serif;
  background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
  min-height: 100vh;
  padding: 2rem;
}

.order-history h2 {
  font-size: 1.8rem;
  margin-bottom: 0.5rem;
  color: #1a202c;
  font-weight: 700;
}

.subtitle {
  color: #718096;
  font-size: 1rem;
  margin-bottom: 2rem;
  font-weight: 500;
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

/* No Orders State */
.no-orders {
  text-align: center;
  padding: 3rem 2rem;
  color: #718096;
}

.no-orders h3 {
  margin: 1rem 0 0.5rem 0;
  color: #4a5568;
  font-size: 1.25rem;
}

.no-orders p {
  margin-bottom: 1.5rem;
}

.btn-primary {
  padding: 0.75rem 1.5rem;
  background: #FF7828;
  color: white;
  border: none;
  border-radius: 8px;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.3s ease;
}

.btn-primary:hover {
  background: #e6691f;
  transform: translateY(-1px);
}

.order-card {
  background: #ffffff;
  border-radius: 16px;
  padding: 1.5rem;
  margin-bottom: 1rem;
  box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
  border: 1px solid #f1f5f9;
  transition: all 0.3s ease;
  position: relative;
}

.order-card:hover {
  transform: translateY(-2px);
  box-shadow: 0 8px 30px rgba(0, 0, 0, 0.12);
  border-color: #FF7828;
}

.order-top {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 1rem;
  padding-bottom: 1rem;
  border-bottom: 1px solid #e2e8f0;
}

.order-id {
  font-weight: 700;
  font-size: 1.1rem;
  color: #1a202c;
}

.status {
  margin-left: 10px;
  padding: 0.5rem 1rem;
  border-radius: 20px;
  font-size: 0.8rem;
  text-transform: capitalize;
  font-weight: 600;
  display: inline-flex;
  align-items: center;
  gap: 5px;
  letter-spacing: 0.5px;
}

.status.delivered {
  background: rgba(45, 206, 137, 0.1);
  color: #2dce89;
}

.status.transit {
  background: rgba(255, 120, 40, 0.1);
  color: #FF7828;
}

.status.pending {
  background: rgba(59, 130, 246, 0.1);
  color: #3b82f6;
}

.status.cancelled {
  background: rgba(239, 68, 68, 0.1);
  color: #ef4444;
}

.status.accepted {
  background: rgba(16, 185, 129, 0.1);
  color: #10b981;
}

.status.assigned {
  background: rgba(139, 92, 246, 0.1);
  color: #8b5cf6;
}

.status.ready {
  background: rgba(245, 158, 11, 0.1);
  color: #f59e0b;
}

.order-price {
  font-weight: 700;
  font-size: 1.3rem;
  color: #FF7828;
}

.store-name {
  font-weight: 600;
  margin-bottom: 1rem;
  color: #1a202c;
  font-size: 1rem;
}

.info {
  font-size: 0.9rem;
  color: #4a5568;
  margin-bottom: 1rem;
}

.info p {
  margin: 0.5rem 0;
  line-height: 1.5;
}

.info strong {
  color: #2d3748;
}

/* Status Timeline Styles */
.status-timeline {
  margin: 1rem 0;
}

.status-info {
  padding: 0.75rem;
  border-radius: 8px;
  font-weight: 500;
  border-left: 4px solid;
}

.status-info.pending {
  background: rgba(59, 130, 246, 0.05);
  border-left-color: #3b82f6;
}

.status-info.accepted {
  background: rgba(16, 185, 129, 0.05);
  border-left-color: #10b981;
}

.status-info.assigned {
  background: rgba(139, 92, 246, 0.05);
  border-left-color: #8b5cf6;
}

.status-info.ready {
  background: rgba(245, 158, 11, 0.05);
  border-left-color: #f59e0b;
}

.status-info.transit {
  background: rgba(255, 120, 40, 0.05);
  border-left-color: #FF7828;
}

.status-info.delivered {
  background: rgba(34, 197, 94, 0.05);
  border-left-color: #22c55e;
}

.status-info.cancelled {
  background: rgba(239, 68, 68, 0.05);
  border-left-color: #ef4444;
}

.status-note {
  margin: 0 0 0.5rem 0;
  font-size: 0.9rem;
  font-weight: 600;
}

.driver-details, .eta, .delivery-time, .cancellation-reason {
  margin: 0.25rem 0 0 0;
  font-size: 0.85rem;
  color: #4a5568;
}

/* Auto Remove Notice */
.auto-remove-notice {
  margin: 0.75rem 0;
  padding: 0.5rem;
  background: rgba(255, 193, 7, 0.1);
  border-radius: 6px;
  border-left: 3px solid #ffc107;
}

.countdown-text {
  margin: 0;
  font-size: 0.85rem;
  color: #856404;
  font-weight: 500;
}

.rated-notice {
  margin: 0.5rem 0 0 0;
  font-size: 0.85rem;
  color: #155724;
  font-weight: 500;
  padding: 0.5rem;
  background: rgba(40, 167, 69, 0.1);
  border-radius: 4px;
}

/* Rating Section Styles */
.rating-section {
  margin-top: 1rem;
  padding: 1rem;
  background: rgba(255, 120, 40, 0.05);
  border-radius: 8px;
  border-left: 4px solid #FF7828;
}

.rating-prompt {
  margin: 0 0 0.75rem 0;
  font-weight: 600;
  color: #374151;
  font-size: 0.9rem;
}

.rating-stars {
  display: flex;
  gap: 0.25rem;
  margin-bottom: 1rem;
}

.star {
  font-size: 1.5rem;
  color: #e2e8f0;
  cursor: pointer;
  transition: all 0.2s ease;
}

.star.filled {
  color: #FF7828;
}

.star:hover {
  transform: scale(1.2);
}

/* Feedback Section Styles */
.feedback-section {
  margin: 1rem 0;
}

.feedback-label {
  display: block;
  margin-bottom: 0.5rem;
  font-weight: 600;
  color: #374151;
  font-size: 0.9rem;
}

.feedback-textarea {
  width: 100%;
  padding: 0.75rem;
  border: 2px solid #e2e8f0;
  border-radius: 8px;
  font-family: "Inter", sans-serif;
  font-size: 0.9rem;
  resize: vertical;
  transition: all 0.3s ease;
  background: white;
}

.feedback-textarea:focus {
  outline: none;
  border-color: #FF7828;
  box-shadow: 0 0 0 3px rgba(255, 120, 40, 0.1);
}

.feedback-textarea::placeholder {
  color: #a0aec0;
}

.feedback-counter {
  text-align: right;
  font-size: 0.75rem;
  color: #718096;
  margin-top: 0.25rem;
}

.submit-rating-btn {
  padding: 0.5rem 1rem;
  background: #FF7828;
  color: white;
  border: none;
  border-radius: 6px;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.3s ease;
  font-size: 0.85rem;
}

.submit-rating-btn:hover {
  background: #e6691f;
  transform: translateY(-1px);
}

.existing-rating {
  margin-top: 0.75rem;
}

.rating-display {
  margin: 0;
  font-size: 0.9rem;
  color: #4a5568;
}

.rating-stars-display {
  display: inline-flex;
  gap: 0.1rem;
}

.rating-stars-display .filled {
  color: #FF7828;
}

.existing-feedback {
  margin: 0.5rem 0;
  padding: 0.75rem;
  background: rgba(255, 120, 40, 0.05);
  border-radius: 6px;
  border-left: 3px solid #FF7828;
}

.feedback-display {
  margin: 0;
  font-size: 0.85rem;
  color: #4a5568;
  font-style: italic;
  line-height: 1.4;
}

/* Order Actions */
.order-actions {
  display: flex;
  gap: 0.75rem;
  justify-content: flex-end;
  padding-top: 1rem;
  border-top: 1px solid #e2e8f0;
}

.cancel-btn, .track-btn, .reorder-btn, .contact-btn, .contact-driver-btn, .rate-now-btn {
  padding: 0.75rem 1.5rem;
  border: none;
  border-radius: 8px;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.3s ease;
  font-size: 0.9rem;
}

.cancel-btn {
  background: #ef4444;
  color: white;
  box-shadow: 0 4px 12px rgba(239, 68, 68, 0.3);
}

.cancel-btn:hover {
  background: #dc2626;
  transform: translateY(-1px);
  box-shadow: 0 6px 16px rgba(239, 68, 68, 0.4);
}

.track-btn {
  background: #3b82f6;
  color: white;
  box-shadow: 0 4px 12px rgba(59, 130, 246, 0.3);
}

.track-btn:hover {
  background: #2563eb;
  transform: translateY(-1px);
  box-shadow: 0 6px 16px rgba(59, 130, 246, 0.4);
}

.reorder-btn {
  background: #10b981;
  color: white;
  box-shadow: 0 4px 12px rgba(16, 185, 129, 0.3);
}

.reorder-btn:hover {
  background: #059669;
  transform: translateY(-1px);
  box-shadow: 0 6px 16px rgba(16, 185, 129, 0.4);
}

.contact-btn {
  background: #3b82f6;
  color: white;
  box-shadow: 0 4px 12px rgba(59, 130, 246, 0.3);
}

.contact-btn:hover {
  background: #2563eb;
  transform: translateY(-1px);
  box-shadow: 0 6px 16px rgba(59, 130, 246, 0.4);
}

.contact-driver-btn {
  background: #8b5cf6;
  color: white;
  box-shadow: 0 4px 12px rgba(139, 92, 246, 0.3);
}

.contact-driver-btn:hover {
  background: #7c3aed;
  transform: translateY(-1px);
  box-shadow: 0 6px 16px rgba(139, 92, 246, 0.4);
}

/* New Rate Now Button */
.rate-now-btn {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  background: #8b5cf6;
  color: white;
  box-shadow: 0 4px 12px rgba(139, 92, 246, 0.3);
}

.rate-now-btn:hover {
  background: #7c3aed;
  transform: translateY(-1px);
  box-shadow: 0 6px 16px rgba(139, 92, 246, 0.4);
}

/* Modal Styles */
.modal-overlay {
  position: fixed;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background: rgba(0, 0, 0, 0.5);
  display: flex;
  justify-content: center;
  align-items: center;
  z-index: 1000;
  backdrop-filter: blur(4px);
}

.modal {
  background: white;
  padding: 2rem;
  border-radius: 16px;
  text-align: center;
  width: 400px;
  max-width: 90vw;
  box-shadow: 0 20px 60px rgba(0, 0, 0, 0.2);
}

.modal-icon {
  margin-bottom: 1rem;
}

.modal h3 {
  margin: 0 0 1rem 0;
  color: #1a202c;
  font-size: 1.5rem;
  font-weight: 700;
}

.modal p {
  margin-bottom: 2rem;
  color: #4a5568;
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
  font-size: 0.9rem;
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
  background: #ef4444;
  color: white;
  box-shadow: 0 4px 12px rgba(239, 68, 68, 0.3);
}

.modal-btn-confirm:hover {
  background: #dc2626;
  transform: translateY(-1px);
  box-shadow: 0 6px 16px rgba(239, 68, 68, 0.4);
}

/* Logout Modal Buttons */
.modal-buttons button:first-child {
  background: #FF7828;
  color: white;
  box-shadow: 0 4px 12px rgba(255, 120, 40, 0.3);
}

.modal-buttons button:first-child:hover {
  background: #e6691f;
  transform: translateY(-1px);
  box-shadow: 0 6px 16px rgba(255, 120, 40, 0.4);
}

.modal-buttons button:last-child {
  background: #f7fafc;
  color: #4a5568;
  border: 2px solid #e2e8f0;
}

.modal-buttons button:last-child:hover {
  background: #edf2f7;
  border-color: #cbd5e0;
}


/* REALTIME ACTIVITY */
.realtime-activity-card {
  background: #ffffff;
  border-radius: 18px;
  padding: 1.5rem;
  margin-bottom: 1.5rem;
  border: 1px solid #e2e8f0;
  box-shadow: 0 4px 20px rgba(0,0,0,0.06);
}

.realtime-header {
  display: flex;
  align-items: center;
  justify-content: space-between;
  margin-bottom: 1rem;
}

.realtime-header h3 {
  margin: 0;
  font-size: 1.2rem;
  color: #1a202c;
}

.live-badge {
  background: #ef4444;
  color: white;
  padding: 0.35rem 0.7rem;
  border-radius: 999px;
  font-size: 0.75rem;
  font-weight: 700;
  animation: pulse 1.5s infinite;
}

@keyframes pulse {
  0% { opacity: 1; }
  50% { opacity: .5; }
  100% { opacity: 1; }
}

.realtime-grid {
  display: grid;
  grid-template-columns: repeat(3,1fr);
  gap: 1rem;
  margin-bottom: 1.5rem;
}

.realtime-box {
  border-radius: 14px;
  padding: 1rem;
  color: white;
}

.successful-box {
  background: linear-gradient(135deg,#10b981,#059669);
}

.cancelled-box {
  background: linear-gradient(135deg,#ef4444,#dc2626);
}

.pending-box {
  background: linear-gradient(135deg,#3b82f6,#2563eb);
}

.realtime-box h4 {
  margin: 0 0 .5rem;
  font-size: .95rem;
}

.realtime-count {
  margin: 0;
  font-size: 2rem;
  font-weight: 800;
}

.realtime-feed {
  display: flex;
  flex-direction: column;
  gap: .8rem;
}

.activity-item {
  display: flex;
  gap: .8rem;
  align-items: flex-start;
  background: #f8fafc;
  padding: .9rem;
  border-radius: 12px;
}

.activity-status-dot {
  width: 12px;
  height: 12px;
  border-radius: 50%;
  margin-top: .4rem;
}

.activity-status-dot.pending { background: #3b82f6; }
.activity-status-dot.accepted { background: #10b981; }
.activity-status-dot.assigned { background: #8b5cf6; }
.activity-status-dot.ready { background: #f59e0b; }
.activity-status-dot.transit { background: #FF7828; }
.activity-status-dot.delivered { background: #22c55e; }
.activity-status-dot.cancelled { background: #ef4444; }

.activity-content strong {
  display: block;
  color: #111827;
}

.activity-content p {
  margin: .15rem 0;
  color: #4b5563;
}

.activity-content small {
  color: #6b7280;
}


/* Responsive Design */
@media (max-width: 768px) {
  .my-orders-page {
    padding: 1rem;
  }
  
  .order-top {
    flex-direction: column;
    align-items: flex-start;
    gap: 1rem;
  }
  
  .order-left {
    display: flex;
    flex-direction: column;
    gap: 0.5rem;
  }
  
  .status {
    margin-left: 0;
  }
  
  .modal-buttons {
    flex-direction: column;
  }
  
  .modal-buttons button {
    width: 100%;
  }
  
  .order-actions {
    flex-direction: column;
    justify-content: center;
  }
  
  .cancel-btn, .track-btn, .reorder-btn, .contact-btn, .contact-driver-btn, .rate-now-btn {
    width: 100%;
  }
  
  .rating-stars {
    justify-content: center;
  }
}
</style>