<template>
  <div class="notification-container">
    <!-- Notification Icon with Badge -->
    <div class="notification-icon-wrapper" @click="toggleNotifications">
      <svg width="20" height="20" viewBox="0 0 24 24" fill="none">
        <path d="M18 8A6 6 0 006 8c0 7-3 9-3 9h18s-3-2-3-9M13.73 21a2 2 0 01-3.46 0" stroke="currentColor" stroke-width="2"/>
      </svg>
      <span v-if="unreadCount > 0" class="notification-badge">{{ unreadCount }}</span>
    </div>

    <!-- Notifications Dropdown -->
    <div v-if="showNotifications" class="notifications-dropdown">
      <div class="notifications-header">
        <h3>Notifications</h3>
        <button v-if="unreadCount > 0" class="mark-all-read" @click="markAllAsRead">
          Mark all as read
        </button>
      </div>

      <div class="notifications-list">
        <div v-if="notifications.length === 0" class="no-notifications">
          <svg width="48" height="48" viewBox="0 0 24 24" fill="none">
            <path d="M18 8A6 6 0 006 8c0 7-3 9-3 9h18s-3-2-3-9M13.73 21a2 2 0 01-3.46 0" stroke="#CBD5E0" stroke-width="2"/>
          </svg>
          <p>No notifications yet</p>
        </div>

        <div
          v-for="notification in sortedNotifications"
          :key="notification.id"
          class="notification-item"
          :class="{ unread: !notification.read }"
          @click="handleNotificationClick(notification)"
        >
          <div class="notification-icon" :class="getNotificationType(notification.type)">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none">
              <path v-if="notification.type === 'payment'" d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z" stroke="currentColor" stroke-width="2"/>
              <path v-else-if="notification.type === 'order'" d="M3 3h2l.4 2M7 13h10l4-8H5.4m0 0L7 13m0 0l-2.5 5.5M7 13l2.5 5.5m0 0L17 21" stroke="currentColor" stroke-width="2"/>
              <path v-else d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" stroke="currentColor" stroke-width="2"/>
            </svg>
          </div>
          
          <div class="notification-content">
            <p class="notification-message">{{ notification.message }}</p>
            <span class="notification-time">{{ formatTime(notification.timestamp) }}</span>
          </div>

          <div v-if="!notification.read" class="unread-indicator"></div>
        </div>
      </div>

      <div class="notifications-footer">
        <button class="view-all-btn" @click="viewAllNotifications">
          View All Notifications
        </button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted } from "vue";
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
  serverTimestamp
} from "firebase/firestore";
import { onAuthStateChanged } from "firebase/auth";

const showNotifications = ref(false);
const notifications = ref([]);
const unsubscribeNotifications = ref(null);

// Computed properties
const unreadCount = computed(() => {
  return notifications.value.filter(notification => !notification.read).length;
});

const sortedNotifications = computed(() => {
  return [...notifications.value].sort((a, b) => {
    return new Date(b.timestamp) - new Date(a.timestamp);
  }).slice(0, 5); // Show only latest 5 in dropdown
});

// Toggle notifications dropdown
const toggleNotifications = () => {
  showNotifications.value = !showNotifications.value;
};

// Close notifications when clicking outside
const handleClickOutside = (event) => {
  const notificationContainer = document.querySelector('.notification-container');
  if (notificationContainer && !notificationContainer.contains(event.target)) {
    showNotifications.value = false;
  }
};

// Initialize real-time notifications listener
const initializeNotifications = () => {
  const user = auth.currentUser;
  if (!user) return;

  console.log("🔔 Initializing notifications for retailer:", user.uid);

  const notificationsQuery = query(
    collection(db, "notifications"),
    where("retailerId", "==", user.uid),
    orderBy("timestamp", "desc")
  );

  unsubscribeNotifications.value = onSnapshot(notificationsQuery, 
    (snapshot) => {
      const notificationsList = [];
      snapshot.forEach((doc) => {
        const notificationData = doc.data();
        notificationsList.push({
          id: doc.id,
          ...notificationData,
          timestamp: notificationData.timestamp?.toDate ? notificationData.timestamp.toDate() : new Date(notificationData.timestamp)
        });
      });
      
      notifications.value = notificationsList;
      console.log("📢 Notifications updated:", notificationsList.length, "notifications");
      
      // Show browser notification for new unread notifications
      const newUnread = notificationsList.filter(n => !n.read && isRecent(n.timestamp));
      if (newUnread.length > 0 && Notification.permission === "granted") {
        showBrowserNotification(newUnread[0]);
      }
    },
    (error) => {
      console.error("❌ Error fetching notifications:", error);
    }
  );
};

// Check if notification is recent (within last 10 seconds)
const isRecent = (timestamp) => {
  const now = new Date();
  const notificationTime = new Date(timestamp);
  return (now - notificationTime) < 10000; // 10 seconds
};

// Show browser notification
const showBrowserNotification = (notification) => {
  if ("Notification" in window && Notification.permission === "granted") {
    new Notification("Tankonek - New Notification", {
      body: notification.message,
      icon: "/images/logo.png",
      tag: "tankonek-notification"
    });
  }
};

// Request browser notification permission
const requestNotificationPermission = () => {
  if ("Notification" in window && Notification.permission === "default") {
    Notification.requestPermission().then(permission => {
      if (permission === "granted") {
        console.log("✅ Browser notification permission granted");
      }
    });
  }
};

// Get notification type for styling
const getNotificationType = (type) => {
  const typeMap = {
    'payment': 'payment-type',
    'order': 'order-type',
    'system': 'system-type'
  };
  return typeMap[type] || 'system-type';
};

// Format time for display
const formatTime = (timestamp) => {
  if (!timestamp) return 'Just now';
  
  const now = new Date();
  const notificationTime = new Date(timestamp);
  const diffInMinutes = Math.floor((now - notificationTime) / (1000 * 60));
  const diffInHours = Math.floor(diffInMinutes / 60);
  const diffInDays = Math.floor(diffInHours / 24);

  if (diffInMinutes < 1) return 'Just now';
  if (diffInMinutes < 60) return `${diffInMinutes}m ago`;
  if (diffInHours < 24) return `${diffInHours}h ago`;
  if (diffInDays < 7) return `${diffInDays}d ago`;
  
  return notificationTime.toLocaleDateString();
};

// Handle notification click
const handleNotificationClick = async (notification) => {
  // Mark as read
  if (!notification.read) {
    await markAsRead(notification.id);
  }

  // Handle different notification types
  switch (notification.type) {
    case 'payment':
      // Navigate to orders tab or show order details
      console.log("💰 Payment notification clicked:", notification.orderId);
      // You can emit an event here to parent component
      break;
    case 'order':
      console.log("📦 Order notification clicked:", notification.orderId);
      break;
  }

  // Close dropdown
  showNotifications.value = false;
};

// Mark single notification as read
const markAsRead = async (notificationId) => {
  try {
    const notificationRef = doc(db, "notifications", notificationId);
    await updateDoc(notificationRef, {
      read: true,
      readAt: serverTimestamp()
    });
  } catch (error) {
    console.error("Error marking notification as read:", error);
  }
};

// Mark all notifications as read
const markAllAsRead = async () => {
  try {
    const unreadNotifications = notifications.value.filter(n => !n.read);
    const updatePromises = unreadNotifications.map(notification => 
      updateDoc(doc(db, "notifications", notification.id), {
        read: true,
        readAt: serverTimestamp()
      })
    );
    
    await Promise.all(updatePromises);
    console.log("✅ All notifications marked as read");
  } catch (error) {
    console.error("Error marking all notifications as read:", error);
  }
};

// View all notifications (navigate to notifications page)
const viewAllNotifications = () => {
  console.log("View all notifications clicked");
  // You can implement navigation to a full notifications page here
  showNotifications.value = false;
};

// Cleanup
const cleanup = () => {
  if (unsubscribeNotifications.value) {
    unsubscribeNotifications.value();
  }
  document.removeEventListener('click', handleClickOutside);
};

// Initialize
onMounted(() => {
  // Request notification permission
  requestNotificationPermission();
  
  // Set up click outside listener
  document.addEventListener('click', handleClickOutside);
  
  // Initialize notifications when user is authenticated
  onAuthStateChanged(auth, (user) => {
    if (user) {
      initializeNotifications();
    }
  });
});

onUnmounted(() => {
  cleanup();
});
</script>

<style scoped>
.notification-container {
  position: relative;
}

.notification-icon-wrapper {
  position: relative;
  padding: 0.5rem;
  border-radius: 8px;
  cursor: pointer;
  transition: all 0.3s ease;
  color: #4a5568;
}

.notification-icon-wrapper:hover {
  background: #f7fafc;
  color: #FF7828;
}

.notification-badge {
  position: absolute;
  top: -2px;
  right: -2px;
  background: #ef4444;
  color: white;
  border-radius: 50%;
  width: 18px;
  height: 18px;
  font-size: 0.7rem;
  font-weight: 600;
  display: flex;
  align-items: center;
  justify-content: center;
  border: 2px solid white;
  animation: pulse 2s infinite;
}

@keyframes pulse {
  0% {
    transform: scale(1);
  }
  50% {
    transform: scale(1.1);
  }
  100% {
    transform: scale(1);
  }
}

.notifications-dropdown {
  position: absolute;
  top: 100%;
  right: 0;
  width: 380px;
  max-width: 90vw;
  background: white;
  border-radius: 12px;
  box-shadow: 0 10px 40px rgba(0, 0, 0, 0.15);
  border: 1px solid #e2e8f0;
  z-index: 1000;
  margin-top: 0.5rem;
  animation: slideDown 0.2s ease-out;
}

@keyframes slideDown {
  from {
    opacity: 0;
    transform: translateY(-10px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

.notifications-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 1rem 1.25rem;
  border-bottom: 1px solid #e2e8f0;
}

.notifications-header h3 {
  margin: 0;
  color: #1a202c;
  font-size: 1.1rem;
  font-weight: 600;
}

.mark-all-read {
  background: none;
  border: none;
  color: #FF7828;
  font-size: 0.85rem;
  font-weight: 500;
  cursor: pointer;
  padding: 0.25rem 0.5rem;
  border-radius: 4px;
  transition: all 0.2s ease;
}

.mark-all-read:hover {
  background: rgba(255, 120, 40, 0.1);
}

.notifications-list {
  max-height: 320px;
  overflow-y: auto;
}

.no-notifications {
  text-align: center;
  padding: 2rem 1rem;
  color: #718096;
}

.no-notifications p {
  margin: 1rem 0 0 0;
  font-size: 0.9rem;
}

.notification-item {
  display: flex;
  align-items: flex-start;
  gap: 0.75rem;
  padding: 1rem 1.25rem;
  border-bottom: 1px solid #f1f5f9;
  cursor: pointer;
  transition: all 0.2s ease;
  position: relative;
}

.notification-item:last-child {
  border-bottom: none;
}

.notification-item:hover {
  background: #f8fafc;
}

.notification-item.unread {
  background: rgba(255, 120, 40, 0.03);
}

.notification-icon {
  width: 32px;
  height: 32px;
  border-radius: 8px;
  display: flex;
  align-items: center;
  justify-content: center;
  flex-shrink: 0;
  margin-top: 0.25rem;
}

.notification-icon.payment-type {
  background: rgba(34, 197, 94, 0.1);
  color: #16a34a;
}

.notification-icon.order-type {
  background: rgba(59, 130, 246, 0.1);
  color: #2563eb;
}

.notification-icon.system-type {
  background: rgba(139, 92, 246, 0.1);
  color: #7c3aed;
}

.notification-content {
  flex: 1;
  min-width: 0;
}

.notification-message {
  margin: 0 0 0.25rem 0;
  color: #1a202c;
  font-size: 0.9rem;
  line-height: 1.4;
  word-wrap: break-word;
}

.notification-time {
  font-size: 0.75rem;
  color: #718096;
  font-weight: 500;
}

.unread-indicator {
  width: 8px;
  height: 8px;
  background: #FF7828;
  border-radius: 50%;
  flex-shrink: 0;
  margin-top: 0.5rem;
  animation: pulse 2s infinite;
}

.notifications-footer {
  padding: 1rem 1.25rem;
  border-top: 1px solid #e2e8f0;
  text-align: center;
}

.view-all-btn {
  background: none;
  border: 1px solid #e2e8f0;
  color: #4a5568;
  padding: 0.5rem 1rem;
  border-radius: 6px;
  font-size: 0.85rem;
  font-weight: 500;
  cursor: pointer;
  transition: all 0.2s ease;
  width: 100%;
}

.view-all-btn:hover {
  background: #f7fafc;
  border-color: #cbd5e0;
}

/* Scrollbar styling */
.notifications-list::-webkit-scrollbar {
  width: 4px;
}

.notifications-list::-webkit-scrollbar-track {
  background: #f1f5f9;
}

.notifications-list::-webkit-scrollbar-thumb {
  background: #cbd5e0;
  border-radius: 2px;
}

.notifications-list::-webkit-scrollbar-thumb:hover {
  background: #94a3b8;
}

/* Responsive Design */
@media (max-width: 768px) {
  .notifications-dropdown {
    width: 320px;
    right: -50px;
  }
}

@media (max-width: 480px) {
  .notifications-dropdown {
    width: 280px;
    right: -80px;
  }
  
  .notification-item {
    padding: 0.75rem 1rem;
  }
}
</style>