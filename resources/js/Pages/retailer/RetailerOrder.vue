<template>
  <div class="retailer-dashboard">
    <!-- Header -->
    <header class="dashboard-header">
      <div class="logo-section">
        <img :src="logo" alt="Tankonek Logo" class="logo" />
        <div class="retailer-info">
          <h2>tankonek - <span class="highlight">Retailer Portal</span></h2>
          <p class="retailer-name">{{ retailerName }}</p>
          <!-- Rating display -->
          <div class="retailer-rating" v-if="retailerRating.averageRating > 0">
            <div class="stars">
              <span 
                v-for="star in 5" 
                :key="star" 
                class="star"
                :class="{ filled: star <= Math.round(retailerRating.averageRating) }"
              >
                ★
              </span>
            </div>
            <span class="rating-text">
              {{ retailerRating.averageRating.toFixed(1) }} ({{ retailerRating.totalRatings }} review{{ retailerRating.totalRatings !== 1 ? 's' : '' }})
            </span>
          </div>
          <div class="retailer-rating" v-else>
            <span class="no-rating">No ratings yet</span>
          </div>
        </div>
      </div>
     
      <div class="header-actions">
        <!-- Notification Component -->
        <Notification />
        
                <button class="wallet-icon-btn" @click="showWalletModal = true" title="Retailer Wallet">
          <svg width="16" height="16" viewBox="0 0 24 24" fill="none">
            <rect x="2" y="5" width="20" height="14" rx="2" stroke="currentColor" stroke-width="2"/>
            <path d="M16 12h4M6 9h6" stroke="currentColor" stroke-width="2"/>
          </svg>
          Wallet
        </button>

<button class="settings-btn" @click="showSettingsModal = true">
          <svg width="16" height="16" viewBox="0 0 24 24" fill="none">
            <path d="M12 15a3 3 0 100-6 3 3 0 000 6z" stroke="currentColor" stroke-width="2"/>
            <path d="M19.4 15a1.65 1.65 0 00.33 1.82l.06.06a2 2 0 010 2.83 2 2 0 01-2.83 0l-.06-.06a1.65 1.65 0 00-1.82-.33 1.65 1.65 0 00-1 1.51V21a2 2 0 01-2 2 2 2 0 01-2-2v-.09A1.65 1.65 0 009 19.4a1.65 1.65 0 00-1.82.33l-.06.06a2 2 0 01-2.83 0 2 2 0 010-2.83l.06-.06a1.65 1.65 0 00.33-1.82 1.65 1.65 0 00-1.51-1H3a2 2 0 01-2-2 2 2 0 012-2h.09A1.65 1.65 0 004.6 9a1.65 1.65 0 00-.33-1.82l-.06-.06a2 2 0 010-2.83 2 2 0 012.83 0l.06.06a1.65 1.65 0 001.82.33H9a1.65 1.65 0 001-1.51V3a2 2 0 012-2 2 2 0 012 2v.09a1.65 1.65 0 001 1.51 1.65 1.65 0 001.82-.33l.06-.06a2 2 0 012.83 0 2 2 0 010 2.83l-.06.06a1.65 1.65 0 00-.33 1.82V9a1.65 1.65 0 001.51 1H21a2 2 0 012 2 2 2 0 01-2 2h-.09a1.65 1.65 0 00-1.51 1z" stroke="currentColor" stroke-width="2"/>
          </svg>
          Settings
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

    <!-- Summary Cards -->
    <div class="summary-cards">
      <div class="card">
        <div class="card-icon today-orders">
          <svg width="24" height="24" viewBox="0 0 24 24" fill="none">
            <path d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" stroke="currentColor" stroke-width="2"/>
          </svg>
        </div>
        <h3>Today's Orders</h3>
        <p class="count">{{ todaysOrdersCount }}</p>
        <small class="card-trend positive">+{{ diffYesterday }} from yesterday</small>
      </div>

      <div class="card">
        <div class="card-icon pending">
          <svg width="24" height="24" viewBox="0 0 24 24" fill="none">
            <path d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" stroke="currentColor" stroke-width="2"/>
          </svg>
        </div>
        <h3>Pending Orders</h3>
        <p class="count">{{ pendingOrdersCount }}</p>
        <small class="card-alert">Needs attention</small>
      </div>

      <div class="card">
        <div class="card-icon revenue">
          <svg width="24" height="24" viewBox="0 0 24 24" fill="none">
            <path d="M12 1v22M17 5H9.5a3.5 3.5 0 100 7h5a3.5 3.5 0 110 7H6" stroke="currentColor" stroke-width="2"/>
          </svg>
        </div>
        <h3>Total Revenue</h3>
        <p class="count">₱{{ totalRevenue.toLocaleString() }}</p>
        <small class="card-subtitle">This month</small>
      </div>

      <div class="card">
        <div class="card-icon low-stock">
          <svg width="24" height="24" viewBox="0 0 24 24" fill="none">
            <path d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.35 16.5c-.77.833.192 2.5 1.732 2.5z" stroke="currentColor" stroke-width="2"/>
          </svg>
        </div>
        <h3>Low Stock Items</h3>
        <p class="count">{{ lowStock }}</p>
        <small class="card-alert">Needs restocking</small>
      </div>
    </div>

    <!-- Tabs -->
    <div class="tabs">
      <button 
        class="tab" 
        :class="{ active: activeTab === 'orders' }"
        @click="activeTab = 'orders'"
      >
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none">
          <path d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" stroke="currentColor" stroke-width="2"/>
        </svg>
        Orders
      </button>

      <button 
        class="tab" 
        :class="{ active: activeTab === 'inventory' }"
        @click="activeTab = 'inventory'"
      >
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none">
          <path d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" stroke="currentColor" stroke-width="2"/>
        </svg>
        Inventory
      </button>

      <button 
        class="tab" 
        :class="{ active: activeTab === 'deliveries' }"
        @click="activeTab = 'deliveries'"
      >
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none">
          <path d="M9 17a2 2 0 11-4 0 2 2 0 014 0zM19 17a2 2 0 11-4 0 2 2 0 014 0z" stroke="currentColor" stroke-width="2"/>
          <path d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h1M5 17a2 2 0 104 0m-4 0a2 2 0 114 0m6 0a2 2 0 104 0m-4 0a2 2 0 114 0" stroke="currentColor" stroke-width="2"/>
        </svg>
        Deliveries
      </button>

      <!-- Revenue Tab -->
      <button 
        class="tab" 
        :class="{ active: activeTab === 'revenue' }"
        @click="activeTab = 'revenue'"
      >
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none">
          <path d="M12 1v22M17 5H9.5a3.5 3.5 0 100 7h5a3.5 3.5 0 110 7H6" stroke="currentColor" stroke-width="2"/>
        </svg>
        Revenue
      </button>

      <!-- BAGO: Ratings & Feedback Tab -->
      <button 
        class="tab" 
        :class="{ active: activeTab === 'ratings' }"
        @click="activeTab = 'ratings'"
      >
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none">
          <path d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z" stroke="currentColor" stroke-width="2"/>
        </svg>
        Ratings & Feedback
      </button>
    </div>

    <!-- Content Area -->
    <section class="content-section">
      <div v-if="activeTab === 'orders'" class="tab-content">
        <div class="content-header">
          <h2>Order Management</h2>
          <p>View and manage customer orders</p>
        </div>

        <!-- Loading State -->
        <div v-if="loading" class="loading-state">
          <svg width="48" height="48" viewBox="0 0 24 24" fill="none" class="loading-spinner">
            <path d="M12 2v4m0 12v4M4.93 4.93l2.83 2.83m8.48 8.48l2.83 2.83M2 12h4m12 0h4M4.93 19.07l2.83-2.83m8.48-8.48l2.83-2.83" stroke="#FF7828" stroke-width="2" stroke-linecap="round"/>
          </svg>
          <p>Loading orders...</p>
        </div>

        <!-- No Orders State -->
        <div v-else-if="orders.length === 0" class="no-orders">
          <svg width="64" height="64" viewBox="0 0 24 24" fill="none">
            <path d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" stroke="#CBD5E0" stroke-width="2"/>
          </svg>
          <h3>No orders yet</h3>
          <p>Customer orders will appear here once they place orders</p>
        </div>

        <!-- Orders with Ongoing and History Sections -->
        <div v-else class="orders-container">
          <!-- Ongoing Transactions Section -->
          <div class="orders-section">
            <h3 class="section-title">
              <svg width="20" height="20" viewBox="0 0 24 24" fill="none">
                <path d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" stroke="#FF7828" stroke-width="2"/>
              </svg>
              Ongoing Transactions
            </h3>
            <p class="section-subtitle">Orders that need your attention</p>
            
            <div class="orders-grid">
              <div
                v-for="order in ongoingOrders"
                :key="order.id"
                class="order-card"
              >
                <div class="order-main">
                  <div class="order-header">
                    <strong class="order-id">{{ order.orderNumber || order.orderId }}</strong>
                    <span
                      class="status"
                      :class="getStatusClass(order.orderStatus)"
                    >
                      {{ formatStatus(order.orderStatus) }}
                    </span>
                  </div>
                  <div class="order-details">
                    <p class="customer"><strong>Customer:</strong> {{ order.customerName }}</p>
                    <p class="date"><strong>Order Date:</strong> {{ formatDate(order.orderDate) }}</p>
                    <p class="item"><strong>Item:</strong> {{ order.gasBrand }} - {{ order.gasType }} × {{ order.quantity }}</p>
                    <p class="contact"><strong>Contact:</strong> {{ order.customerPhone }}</p>
                    <p class="address"><strong>Address:</strong> {{ order.deliveryAddress }}</p>
                    <p class="payment"><strong>Payment:</strong> {{ formatPaymentMethod(order.paymentMethod) }}</p>
                    <p class="payment-status"><strong>Payment Status:</strong> 
                      <span :class="getPaymentStatusClass(order.paymentStatus)">
                        {{ formatPaymentStatus(order.paymentStatus) }}
                      </span>
                    </p>
                    <p v-if="order.specialInstructions" class="instructions"><strong>Instructions:</strong> {{ order.specialInstructions }}</p>
                  </div>

                  <!-- Action Buttons - TANGGAL ANG REJECT BUTTON -->
                  <div class="order-actions" v-if="order.orderStatus === 'pending' || order.orderStatus === 'paid_pending_approval'">
                    <button class="accept-btn" @click="acceptOrder(order)">
                      <svg width="16" height="16" viewBox="0 0 24 24" fill="none">
                        <path d="M5 13l4 4L19 7" stroke="currentColor" stroke-width="2"/>
                      </svg>
                      {{ order.orderStatus === 'paid_pending_approval' ? 'Accept Payment & Order' : 'Accept Order' }}
                    </button>
                    <!-- REJECT BUTTON REMOVED -->
                  </div>

                  <!-- Status Info for Other Order Statuses -->
                  <div v-else class="status-info" :class="getStatusInfoClass(order.orderStatus)">
                    <p class="status-note">{{ getStatusNote(order.orderStatus) }}</p>
                    <p v-if="order.deliveryPerson" class="driver-info">Driver: {{ order.deliveryPerson }}</p>
                    <p v-if="order.deliveredAt" class="delivery-time">Delivered: {{ formatDate(order.deliveredAt) }}</p>
                    <p v-if="order.cancellationReason" class="cancellation-reason">Reason: {{ order.cancellationReason }}</p>
                  </div>
                </div>
                <div class="order-price">
                  ₱{{ order.total?.toLocaleString() }}
                  <div v-if="order.orderStatus === 'paid_pending_approval'" class="payment-badge paid">
                    Payment Received
                  </div>
                </div>
              </div>
            </div>

            <!-- No Ongoing Orders Message -->
            <div v-if="ongoingOrders.length === 0" class="no-ongoing-orders">
              <svg width="48" height="48" viewBox="0 0 24 24" fill="none">
                <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" stroke="#CBD5E0" stroke-width="2"/>
              </svg>
              <h4>No ongoing transactions</h4>
              <p>All orders are completed or cancelled</p>
            </div>
          </div>

          <!-- Order History Section -->
          <div class="orders-section">
            <h3 class="section-title">
              <svg width="20" height="20" viewBox="0 0 24 24" fill="none">
                <path d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" stroke="#718096" stroke-width="2"/>
              </svg>
              Order History
            </h3>
            <p class="section-subtitle">Completed and cancelled orders</p>
            
            <div class="orders-grid">
              <div
                v-for="order in orderHistory"
                :key="order.id"
                class="order-card history-card"
              >
                <div class="order-main">
                  <div class="order-header">
                    <strong class="order-id">{{ order.orderNumber || order.orderId }}</strong>
                    <span
                      class="status"
                      :class="getStatusClass(order.orderStatus)"
                    >
                      {{ formatStatus(order.orderStatus) }}
                    </span>
                  </div>
                  <div class="order-details">
                    <p class="customer"><strong>Customer:</strong> {{ order.customerName }}</p>
                    <p class="date"><strong>Order Date:</strong> {{ formatDate(order.orderDate) }}</p>
                    <p class="item"><strong>Item:</strong> {{ order.gasBrand }} - {{ order.gasType }} × {{ order.quantity }}</p>
                    <p class="contact"><strong>Contact:</strong> {{ order.customerPhone }}</p>
                    <p class="address"><strong>Address:</strong> {{ order.deliveryAddress }}</p>
                    <p class="payment"><strong>Payment:</strong> {{ formatPaymentMethod(order.paymentMethod) }}</p>
                    <p class="payment-status"><strong>Payment Status:</strong> 
                      <span :class="getPaymentStatusClass(order.paymentStatus)">
                        {{ formatPaymentStatus(order.paymentStatus) }}
                      </span>
                    </p>
                    <p v-if="order.specialInstructions" class="instructions"><strong>Instructions:</strong> {{ order.specialInstructions }}</p>
                  </div>

                  <!-- Status Info for History Orders -->
                  <div class="status-info" :class="getStatusInfoClass(order.orderStatus)">
                    <p class="status-note">{{ getStatusNote(order.orderStatus) }}</p>
                    <p v-if="order.deliveryPerson" class="driver-info">Driver: {{ order.deliveryPerson }}</p>
                    <p v-if="order.deliveredAt" class="delivery-time">Delivered: {{ formatDate(order.deliveredAt) }}</p>
                    <p v-if="order.cancellationReason" class="cancellation-reason">Reason: {{ order.cancellationReason }}</p>
                  </div>
                </div>
                <div class="order-price">
                  ₱{{ order.total?.toLocaleString() }}
                  <div v-if="order.orderStatus === 'delivered'" class="delivery-badge">
                    Completed
                  </div>
                </div>
              </div>
            </div>

            <!-- No History Message -->
            <div v-if="orderHistory.length === 0" class="no-history">
              <svg width="48" height="48" viewBox="0 0 24 24" fill="none">
                <path d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" stroke="#CBD5E0" stroke-width="2"/>
              </svg>
              <h4>No order history</h4>
              <p>Completed and cancelled orders will appear here</p>
            </div>
          </div>
        </div>
      </div>

      <div v-else-if="activeTab === 'inventory'">
        <Inventory />
      </div>

      <div v-else-if="activeTab === 'deliveries'">
        <Deliveries />
      </div>

      <!-- Revenue Tab Content -->
      <div v-else-if="activeTab === 'revenue'">
        <Revenue />
      </div>

      <!-- BAGO: Ratings & Feedback Tab Content -->
      <div v-else-if="activeTab === 'ratings'" class="tab-content">
        <div class="content-header">
          <h2>Customer Ratings & Feedback</h2>
          <p>View customer reviews and ratings for your service</p>
        </div>

        <!-- Loading State -->
        <div v-if="loadingRatings" class="loading-state">
          <svg width="48" height="48" viewBox="0 0 24 24" fill="none" class="loading-spinner">
            <path d="M12 2v4m0 12v4M4.93 4.93l2.83 2.83m8.48 8.48l2.83 2.83M2 12h4m12 0h4M4.93 19.07l2.83-2.83m8.48-8.48l2.83-2.83" stroke="#FF7828" stroke-width="2" stroke-linecap="round"/>
          </svg>
          <p>Loading ratings...</p>
        </div>

        <!-- No Ratings State -->
        <div v-else-if="customerFeedbacks.length === 0" class="no-ratings">
          <svg width="64" height="64" viewBox="0 0 24 24" fill="none">
            <path d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z" stroke="#CBD5E0" stroke-width="2"/>
          </svg>
          <h3>No ratings yet</h3>
          <p>Customer ratings and feedback will appear here once they review your service</p>
        </div>

        <!-- Ratings Summary -->
        <div v-else class="ratings-container">
          <!-- Overall Rating Card -->
          <div class="overall-rating-card">
            <div class="rating-summary">
              <div class="average-rating">
                <div class="rating-number">{{ retailerRating.averageRating.toFixed(1) }}</div>
                <div class="rating-stars">
                  <span 
                    v-for="star in 5" 
                    :key="star" 
                    class="star large"
                    :class="{ filled: star <= Math.round(retailerRating.averageRating) }"
                  >
                    ★
                  </span>
                </div>
                <div class="rating-count">{{ retailerRating.totalRatings }} review{{ retailerRating.totalRatings !== 1 ? 's' : '' }}</div>
              </div>
              <div class="rating-breakdown">
                <div v-for="rating in 5" :key="rating" class="rating-bar">
                  <span class="rating-label">{{ rating }} star</span>
                  <div class="bar-container">
                    <div 
                      class="bar-fill" 
                      :style="{ width: getRatingPercentage(rating) + '%' }"
                    ></div>
                  </div>
                  <span class="rating-percentage">{{ getRatingPercentage(rating) }}%</span>
                </div>
              </div>
            </div>
          </div>

          <!-- Customer Feedback List -->
          <div class="feedback-section">
            <h3 class="section-title">
              <svg width="20" height="20" viewBox="0 0 24 24" fill="none">
                <path d="M21 15a2 2 0 01-2 2H7l-4 4V5a2 2 0 012-2h14a2 2 0 012 2z" stroke="#FF7828" stroke-width="2"/>
              </svg>
              Customer Feedback
            </h3>
            
            <div class="feedback-list">
              <div
                v-for="feedback in customerFeedbacks"
                :key="feedback.id"
                class="feedback-card"
              >
                <div class="feedback-header">
                  <div class="customer-info">
                    <div class="customer-avatar">
                      {{ getInitials(feedback.customerName || 'Customer') }}
                    </div>
                    <div class="customer-details">
                      <strong class="customer-name">{{ feedback.customerName || 'Customer' }}</strong>
                      <span class="feedback-date">{{ formatDate(feedback.createdAt) }}</span>
                    </div>
                  </div>
                  <div class="feedback-rating">
                    <div class="stars">
                      <span 
                        v-for="star in 5" 
                        :key="star" 
                        class="star small"
                        :class="{ filled: star <= feedback.rating }"
                      >
                        ★
                      </span>
                    </div>
                    <span class="rating-value">{{ feedback.rating }}.0</span>
                  </div>
                </div>
                
                <div class="feedback-content">
                  <p class="feedback-comment">{{ feedback.comment }}</p>
                  
                  <div v-if="feedback.orderInfo" class="order-info">
                    <p class="order-reference">
                      <strong>Order:</strong> {{ feedback.orderInfo.orderNumber }}
                    </p>
                    <p class="order-items">
                      <strong>Items:</strong> {{ feedback.orderInfo.items }}
                    </p>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- Retailer Wallet Modal -->
    <div v-if="showWalletModal" class="modal-overlay">
      <div class="retailer-wallet-modal">
        <div class="wallet-modal-header">
          <div>
            <h2>Retailer E-Wallet</h2>
            <p>Your product sales revenue will appear here after accepting paid orders.</p>
          </div>
          <button class="close-btn" @click="showWalletModal = false">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none">
              <path d="M18 6L6 18M6 6l12 12" stroke="currentColor" stroke-width="2"/>
            </svg>
          </button>
        </div>

        <div class="wallet-modal-content">
          <div class="wallet-grid">
            <div class="wallet-card main-wallet">
              <span>Available Balance</span>
              <h2>₱{{ retailerWallet.availableBalance.toLocaleString() }}</h2>
              <p>Ready for withdrawal</p>
            </div>

            <div class="wallet-card">
              <span>Total Earnings</span>
              <h3>₱{{ retailerWallet.totalEarnings.toLocaleString() }}</h3>
              <p>All credited product sales</p>
            </div>

            <div class="wallet-card">
              <span>Pending Orders</span>
              <h3>{{ pendingOrdersCount }}</h3>
              <p>Waiting for acceptance</p>
            </div>
          </div>

          <div class="wallet-info-card">
            <h3>How Retailer Wallet Works</h3>
            <p>
              For online paid orders, the money is first held by the platform wallet.
              Once you accept the paid order, the product subtotal is credited to your retailer wallet
              and recorded in your revenue.
            </p>
          </div>

          <div class="wallet-history">
            <div class="wallet-history-header">
              <h3>Wallet Transactions</h3>
              <p>Product sale credits and wallet activity</p>
            </div>

            <table class="wallet-table">
              <thead>
                <tr>
                  <th>Date</th>
                  <th>Order ID</th>
                  <th>Type</th>
                  <th>Amount</th>
                  <th>Status</th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="transaction in retailerWalletTransactions" :key="transaction.id">
                  <td>{{ formatWalletDate(transaction.createdAt) }}</td>
                  <td>{{ transaction.orderNumber || 'N/A' }}</td>
                  <td>{{ transaction.type }}</td>
                  <td>₱{{ Number(transaction.amount || 0).toLocaleString() }}</td>
                  <td>
                    <span class="wallet-status" :class="transaction.status">
                      {{ transaction.status }}
                    </span>
                  </td>
                </tr>
                <tr v-if="retailerWalletTransactions.length === 0">
                  <td colspan="5" class="wallet-empty">No wallet transactions yet</td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>

    <!-- Settings Modal -->
    <Settings v-if="showSettingsModal" @close="showSettingsModal = false" />

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
import { ref, computed, onMounted } from "vue";
import { router } from '@inertiajs/vue3';
import Inventory from "./Inventory.vue";
import Deliveries from "./Deliveries.vue";
import Settings from "./Settings.vue";
import Notification from "./Notification.vue";
import Revenue from "./Revenue.vue";

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
  getDoc,
  addDoc,
  getDocs,
  serverTimestamp,
  runTransaction,
  increment
} from "firebase/firestore";
import { onAuthStateChanged } from "firebase/auth";

// Import the logo
const logo = '/images/logo.png';

const showSettingsModal = ref(false);
const showWalletModal = ref(false);
const retailerWallet = ref({
  availableBalance: 0,
  totalEarnings: 0,
  totalRevenue: 0
});
const retailerWalletTransactions = ref([]);
const activeTab = ref("orders");
const retailerName = ref("");
const loading = ref(true);
const loadingRatings = ref(true);

// Retailer rating data
const retailerRating = ref({
  averageRating: 0,
  totalRatings: 0,
  ratingDistribution: {5: 0, 4: 0, 3: 0, 2: 0, 1: 0}
});

// Customer feedbacks data
const customerFeedbacks = ref([]);

// Orders data from Firebase
const orders = ref([]);

// Summary data (computed)
const todaysOrdersCount = computed(() => {
  const today = new Date().toDateString();
  return orders.value.filter(order => 
    new Date(order.orderDate).toDateString() === today
  ).length;
});

const pendingOrdersCount = computed(() => {
  return orders.value.filter(order => 
    order.orderStatus === 'pending' || order.orderStatus === 'paid_pending_approval'
  ).length;
});

const totalRevenue = computed(() => {
  return orders.value
    .filter(order => order.retailerRevenueStatus === 'credited')
    .reduce((sum, order) => sum + Number(order.retailerEarnings || order.subtotal || 0), 0);
});

const diffYesterday = ref(2);
const lowStock = ref(1);

// Computed properties for ongoing orders and history
const ongoingOrders = computed(() => {
  return orders.value.filter(order => 
    order.orderStatus === 'pending' || 
    order.orderStatus === 'paid_pending_approval' ||
    order.orderStatus === 'accepted' ||
    order.orderStatus === 'assigned' ||
    order.orderStatus === 'ready-for-pickup' ||
    order.orderStatus === 'in-transit'
  );
});

const orderHistory = computed(() => {
  return orders.value.filter(order => 
    order.orderStatus === 'delivered' || 
    order.orderStatus === 'cancelled'
  );
});

// BAGO: Function to fetch retailer feedbacks and ratings from retailers collection
const fetchRetailerFeedbacks = async (retailerId) => {
  try {
    console.log("⭐ Fetching feedbacks for retailer:", retailerId);
    
    const retailerDoc = doc(db, "retailers", retailerId);
    const unsubscribe = onSnapshot(retailerDoc, 
      (docSnapshot) => {
        if (docSnapshot.exists()) {
          const retailerData = docSnapshot.data();
          const feedbacks = retailerData.feedbacks || [];
          
          console.log("📝 Feedbacks found:", feedbacks.length);
          
          // Process feedbacks
          const processedFeedbacks = feedbacks.map((feedback, index) => ({
            id: `feedback-${index}`,
            customerName: feedback.customerName || 'Customer',
            customerId: feedback.customerId,
            comment: feedback.comment,
            rating: feedback.rating,
            createdAt: feedback.createdAt?.toDate ? feedback.createdAt.toDate() : new Date(feedback.createdAt),
            orderId: feedback.orderId,
            orderNumber: feedback.orderNumber,
            orderInfo: {
              orderNumber: feedback.orderNumber,
              items: `${feedback.gasBrand || 'LPG'} - ${feedback.gasType || 'Tank'}`
            }
          }));

          customerFeedbacks.value = processedFeedbacks;
          
          // Calculate ratings summary
          calculateRatingsSummary(processedFeedbacks);
          
          loadingRatings.value = false;
        } else {
          console.log("❌ Retailer document not found");
          customerFeedbacks.value = [];
          loadingRatings.value = false;
        }
      },
      (error) => {
        console.error("❌ Error fetching retailer feedbacks:", error);
        customerFeedbacks.value = [];
        loadingRatings.value = false;
      }
    );

    return unsubscribe;
  } catch (error) {
    console.error("Error in fetchRetailerFeedbacks:", error);
    customerFeedbacks.value = [];
    loadingRatings.value = false;
  }
};

// BAGO: Calculate ratings summary from feedbacks
const calculateRatingsSummary = (feedbacks) => {
  if (feedbacks.length === 0) {
    retailerRating.value = {
      averageRating: 0,
      totalRatings: 0,
      ratingDistribution: {5: 0, 4: 0, 3: 0, 2: 0, 1: 0}
    };
    return;
  }

  let totalRating = 0;
  const distribution = {5: 0, 4: 0, 3: 0, 2: 0, 1: 0};

  feedbacks.forEach(feedback => {
    const rating = feedback.rating;
    totalRating += rating;
    if (distribution[rating] !== undefined) {
      distribution[rating]++;
    }
  });

  retailerRating.value = {
    averageRating: totalRating / feedbacks.length,
    totalRatings: feedbacks.length,
    ratingDistribution: distribution
  };

  console.log("📊 Ratings calculated:", retailerRating.value);
};

// BAGO: Get rating percentage for bar chart
const getRatingPercentage = (rating) => {
  const distribution = retailerRating.value.ratingDistribution;
  const total = retailerRating.value.totalRatings;
  if (total === 0) return 0;
  return Math.round((distribution[rating] / total) * 100);
};

// BAGO: Get customer initials for avatar
const getInitials = (name) => {
  if (!name) return 'C';
  return name
    .split(' ')
    .map(part => part.charAt(0))
    .join('')
    .toUpperCase()
    .slice(0, 2);
};

// Fetch retailer wallet and transaction history
const fetchRetailerWallet = (retailerId) => {
  try {
    const walletRef = doc(db, "retailer_wallets", retailerId);

    onSnapshot(walletRef, (walletSnapshot) => {
      if (walletSnapshot.exists()) {
        const walletData = walletSnapshot.data();

        retailerWallet.value = {
          availableBalance: Number(walletData.availableBalance || 0),
          totalEarnings: Number(walletData.totalEarnings || 0),
          totalRevenue: Number(walletData.totalRevenue || 0)
        };
      } else {
        retailerWallet.value = {
          availableBalance: 0,
          totalEarnings: 0,
          totalRevenue: 0
        };
      }
    });

    const transactionsQuery = query(
      collection(db, "retailer_wallet_transactions"),
      where("retailerId", "==", retailerId)
    );

    onSnapshot(transactionsQuery, (snapshot) => {
      const transactions = [];

      snapshot.forEach((transactionDoc) => {
        transactions.push({
          id: transactionDoc.id,
          ...transactionDoc.data()
        });
      });

      transactions.sort((a, b) => {
        const dateA = a.createdAt?.toDate?.() || new Date(a.createdAt || 0);
        const dateB = b.createdAt?.toDate?.() || new Date(b.createdAt || 0);
        return dateB - dateA;
      });

      retailerWalletTransactions.value = transactions;
    });
  } catch (error) {
    console.error("Error fetching retailer wallet:", error);
  }
};

// Format wallet transaction date
const formatWalletDate = (date) => {
  if (!date) return "N/A";

  try {
    const walletDate = date?.toDate ? date.toDate() : new Date(date);
    return walletDate.toLocaleString("en-US", {
      year: "numeric",
      month: "short",
      day: "numeric",
      hour: "2-digit",
      minute: "2-digit"
    });
  } catch (error) {
    return "Invalid Date";
  }
};

// Fetch orders from Firebase for this specific retailer
const fetchOrders = () => {
  const user = auth.currentUser;
  if (!user) {
    console.log("No retailer logged in");
    loading.value = false;
    return;
  }

  console.log("🔄 Fetching orders for retailer:", user.uid);

  // Get retailer business name first
  const getRetailerData = async () => {
    try {
      const retailerDoc = doc(db, "retailers", user.uid);
      const retailerSnapshot = await getDoc(retailerDoc);
      const retailerData = retailerSnapshot.data();
      
      if (retailerData) {
        retailerName.value = retailerData.businessName || "Retailer";
        
        // BAGO: Fetch retailer feedbacks and ratings
        await fetchRetailerFeedbacks(user.uid);
        
        // SIMPLIFIED QUERY - Remove orderBy first to avoid index error
        const ordersQuery = query(
          collection(db, "orders"),
          where("retailerId", "==", user.uid)
        );

        const unsubscribe = onSnapshot(ordersQuery, 
          (snapshot) => {
            const ordersList = [];
            snapshot.forEach((doc) => {
              const orderData = doc.data();
              ordersList.push({
                id: doc.id,
                ...orderData,
                // Ensure dates are properly formatted
                orderDate: orderData.orderDate?.toDate ? orderData.orderDate.toDate() : new Date(orderData.orderDate),
                estimatedDelivery: orderData.estimatedDelivery?.toDate ? orderData.estimatedDelivery.toDate() : 
                                 (orderData.estimatedDelivery ? new Date(orderData.estimatedDelivery) : null),
                deliveredAt: orderData.deliveredAt?.toDate ? orderData.deliveredAt.toDate() : 
                            (orderData.deliveredAt ? new Date(orderData.deliveredAt) : null),
                createdAt: orderData.createdAt?.toDate ? orderData.createdAt.toDate() : 
                          (orderData.createdAt ? new Date(orderData.createdAt) : new Date())
              });
            });
            
            // Manual sorting since we removed orderBy from query
            ordersList.sort((a, b) => new Date(b.orderDate) - new Date(a.orderDate));
            
            orders.value = ordersList;
            loading.value = false;
            console.log("✅ Retailer orders loaded:", ordersList.length, "orders");
          },
          (error) => {
            console.error("❌ Error fetching retailer orders:", error);
            
            // Fallback: Try simpler query if complex one fails
            if (error.code === 'failed-precondition') {
              console.log("🔄 Trying fallback query...");
              fetchOrdersFallback(user.uid);
            } else {
              loading.value = false;
            }
          }
        );

        return unsubscribe;
      }
    } catch (error) {
      console.error("Error fetching retailer data:", error);
      loading.value = false;
    }
  };

  getRetailerData();
};

// Fallback method with simpler query
const fetchOrdersFallback = async (retailerId) => {
  try {
    const ordersQuery = query(
      collection(db, "orders"),
      where("retailerId", "==", retailerId)
    );
    
    const snapshot = await getDocs(ordersQuery);
    const ordersList = [];
    
    snapshot.forEach((doc) => {
      const orderData = doc.data();
      ordersList.push({
        id: doc.id,
        ...orderData,
        orderDate: orderData.orderDate?.toDate ? orderData.orderDate.toDate() : new Date(orderData.orderDate),
        estimatedDelivery: orderData.estimatedDelivery?.toDate ? orderData.estimatedDelivery.toDate() : 
                         (orderData.estimatedDelivery ? new Date(orderData.estimatedDelivery) : null),
        deliveredAt: orderData.deliveredAt?.toDate ? orderData.deliveredAt.toDate() : 
                    (orderData.deliveredAt ? new Date(orderData.deliveredAt) : null),
        createdAt: orderData.createdAt?.toDate ? orderData.createdAt.toDate() : 
                  (orderData.createdAt ? new Date(orderData.createdAt) : new Date())
      });
    });
    
    // Manual sorting
    ordersList.sort((a, b) => new Date(b.orderDate) - new Date(a.orderDate));
    
    orders.value = ordersList;
    loading.value = false;
    
    console.log("✅ Fallback orders loaded:", ordersList.length, "orders");
  } catch (error) {
    console.error("❌ Error in fallback fetch:", error);
    loading.value = false;
  }
};

// Format status for display
const formatStatus = (status) => {
  const statusMap = {
    'pending': 'Pending - Waiting for acceptance',
    'paid_pending_approval': 'Paid - Waiting for acceptance',
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
    'delivered': 'delivered',
    'in-transit': 'in-transit',
    'ready-for-pickup': 'ready',
    'assigned': 'assigned',
    'accepted': 'accepted',
    'pending': 'pending',
    'paid_pending_approval': 'paid-pending',
    'cancelled': 'cancelled'
  };
  return classMap[status] || 'pending';
};

// Get status info class
const getStatusInfoClass = (status) => {
  const classMap = {
    'accepted': 'accepted-info',
    'assigned': 'assigned-info',
    'ready-for-pickup': 'ready-info',
    'in-transit': 'transit-info',
    'delivered': 'delivered-info',
    'cancelled': 'cancelled-info'
  };
  return classMap[status] || 'accepted-info';
};

// Get status note text
const getStatusNote = (status) => {
  const noteMap = {
    'accepted': 'Order accepted - Ready for delivery',
    'assigned': 'Driver assigned - Order in progress',
    'ready-for-pickup': 'Ready for driver pickup',
    'in-transit': 'Order is out for delivery',
    'delivered': 'Order delivered successfully',
    'cancelled': 'Order cancelled'
  };
  return noteMap[status] || 'Order in progress';
};

// Format date for display
const formatDate = (date) => {
  if (!date) return 'Unknown date';
  const d = new Date(date);
  return d.toLocaleString('en-US', {
    year: 'numeric',
    month: 'short',
    day: 'numeric',
    hour: '2-digit',
    minute: '2-digit'
  });
};

// Format payment method
const formatPaymentMethod = (method) => {
  const methodMap = {
    'cash': 'Cash on Delivery',
    'gcash': 'GCash',
    'card': 'Credit/Debit Card',
    'paypal': 'PayPal'
  };
  return methodMap[method] || method;
};

// Format payment status
const formatPaymentStatus = (status) => {
  const statusMap = {
    'pending': 'Pending',
    'paid': 'Paid',
    'cash_on_delivery': 'Cash on Delivery',
    'refunded': 'Refunded',
    'failed': 'Failed'
  };
  return statusMap[status] || status;
};

// Get payment status class
const getPaymentStatusClass = (status) => {
  const classMap = {
    'paid': 'payment-paid',
    'cash_on_delivery': 'payment-cod',
    'pending': 'payment-pending',
    'refunded': 'payment-refunded',
    'failed': 'payment-failed'
  };
  return classMap[status] || 'payment-pending';
};

// Send notification to customer
const sendCustomerNotification = async (order, status, message = null) => {
  try {
    const notificationData = {
      orderId: order.orderNumber || order.id,
      customerId: order.customerId,
      customerName: order.customerName,
      status: status,
      message: message || (status === 'accepted' 
        ? `Your order ${order.orderNumber} has been accepted and is being prepared for delivery.` 
        : `Your order ${order.orderNumber} status has been updated.`),
      timestamp: serverTimestamp(),
      read: false
    };

    await addDoc(collection(db, "notifications"), notificationData);
    console.log("📢 Notification sent to customer");
  } catch (error) {
    console.error("Error sending notification:", error);
  }
};

// Credit retailer wallet and revenue when a paid order is accepted
const creditRetailerRevenueOnAccept = async (order) => {
  const paymentIsPaid = order.paymentStatus === 'paid' || order.orderStatus === 'paid_pending_approval';

  if (!paymentIsPaid) {
    return;
  }

  if (order.retailerRevenueStatus === 'credited') {
    console.log("Retailer revenue already credited for:", order.orderNumber || order.orderId);
    return;
  }

  const retailerId = order.retailerId || auth.currentUser?.uid;
  const orderNumber = order.orderNumber || order.orderId || order.displayId || order.id;
  const productRevenue = Number(order.subtotal || order.productRevenue || 0);

  if (!retailerId || !orderNumber || productRevenue <= 0) {
    console.log("Skipping retailer revenue credit due to incomplete order data.");
    return;
  }

  const orderRef = doc(db, "orders", order.id);
  const retailerWalletRef = doc(db, "retailer_wallets", retailerId);

  await runTransaction(db, async (transaction) => {
    const latestOrder = await transaction.get(orderRef);

    if (!latestOrder.exists()) {
      throw new Error("Order not found during revenue credit.");
    }

    const latestOrderData = latestOrder.data();

    if (latestOrderData.retailerRevenueStatus === 'credited') {
      return;
    }

    transaction.set(retailerWalletRef, {
      availableBalance: increment(productRevenue),
      totalEarnings: increment(productRevenue),
      totalRevenue: increment(productRevenue),
      updatedAt: serverTimestamp()
    }, { merge: true });

    transaction.update(orderRef, {
      retailerRevenueStatus: 'credited',
      retailerEarnings: productRevenue,
      productRevenue: productRevenue,
      retailerRevenueCreditedAt: serverTimestamp()
    });
  });

  await addDoc(collection(db, "retailer_wallet_transactions"), {
    retailerId,
    orderId: order.id,
    orderNumber,
    type: "Product Sale",
    description: `Product revenue credited after accepting order ${orderNumber}`,
    amount: productRevenue,
    status: "credited",
    paymentMethod: order.paymentMethod || "online",
    balanceType: "available",
    createdAt: serverTimestamp()
  });

  console.log("✅ Retailer wallet credited:", productRevenue, "for", orderNumber);
};

// Create delivery record for riders
const createDeliveryRecord = async (order) => {
  try {
    console.log("🔄 Creating delivery record for order:", order.orderNumber);

    const deliveryData = {
      orderId: order.orderNumber || order.orderId || `ORD-${Date.now()}`,
      retailerId: order.retailerId,
      retailerName: order.retailerBusinessName || order.retailerName || "Retailer Store",
      retailerAddress: order.retailerBarangay || "Store Location",
      customerId: order.customerId,
      customerName: order.customerName || "Customer",
      customerPhone: order.customerPhone || "N/A",
      customerEmail: order.customerEmail || "",
      items: [{
        productType: order.gasBrand || "LPG",
        tankSize: order.gasType || "Tank",
        quantity: order.quantity || 1,
        price: order.pricePerTank || 0,
        itemDescription: `${order.gasBrand || 'LPG'} - ${order.gasType || 'Tank'} × ${order.quantity || 1}`
      }],
      totalAmount: order.total || 0,
      deliveryFee: order.deliveryFee || 50,
      pickupAddress: order.retailerBarangay || "Store Location",
      deliveryAddress: order.deliveryAddress || order.customerAddress || "Delivery Address",
      distance: order.distance || 1.2,
      status: 'available',
      paymentMethod: order.paymentMethod || 'cash',
      paymentStatus: order.paymentStatus || 'pending',
      specialInstructions: order.specialInstructions || '',
      estimatedPrepTime: 15,
      createdAt: serverTimestamp(),
      updatedAt: serverTimestamp(),
      displayId: order.orderNumber || order.orderId || `ORD-${Date.now()}`,
      item: `${order.gasBrand || 'LPG'} ${order.gasType || 'Tank'} × ${order.quantity || 1}`,
      from: order.retailerBusinessName || order.retailerName || "Store",
      to: order.customerName || "Customer",
      gasBrand: order.gasBrand,
      gasType: order.gasType,
      quantity: order.quantity,
      pricePerTank: order.pricePerTank,
      subtotal: order.subtotal
    };

    console.log("📦 Delivery data to create:", deliveryData);

    const deliveryRef = await addDoc(collection(db, "deliveries"), deliveryData);
    console.log("✅ Delivery record created successfully:", deliveryRef.id);
    
    await sendCustomerNotification(order, 'accepted');
    
    return deliveryRef.id;
    
  } catch (error) {
    console.error("❌ Error creating delivery record:", error);
    throw error;
  }
};

// Order Actions - ACCEPT ONLY (REJECT REMOVED)
const acceptOrder = async (order) => {
  try {
    console.log("🔄 Accepting order:", order.orderNumber);

    if (!order.id) {
      throw new Error("Order ID is missing");
    }

    const orderRef = doc(db, "orders", order.id);
    
    const newStatus = 'accepted';
    
    await updateDoc(orderRef, {
      orderStatus: newStatus,
      status: newStatus,
      updatedAt: serverTimestamp(),
      acceptedAt: serverTimestamp()
    });

    console.log(`✅ Order status updated to '${newStatus}'`);

    await creditRetailerRevenueOnAccept(order);
    await createDeliveryRecord(order);
    
    console.log("🎉 Order accepted successfully:", order.orderNumber);
    
    const message = order.orderStatus === 'paid_pending_approval' 
      ? `Order ${order.orderNumber} accepted! Retailer revenue has been credited to your wallet and delivery is now available for riders.`
      : `Order ${order.orderNumber} accepted successfully! Delivery is now available for riders.`;
    
    alert(message);
    
  } catch (error) {
    console.error("❌ Error accepting order:", error);
    
    let errorMessage = "Failed to accept order. Please try again.";
    
    if (error.code === 'permission-denied') {
      errorMessage = "Permission denied. Please check your authentication.";
    } else if (error.code === 'not-found') {
      errorMessage = "Order not found. Please refresh the page.";
    } else if (error.message.includes("Order ID is missing")) {
      errorMessage = "Order data is incomplete. Please refresh the page.";
    }
    
    alert(errorMessage);
  }
};

// Logout modal logic
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

// Initialize on component mount
onMounted(() => {
  onAuthStateChanged(auth, (user) => {
    if (user) {
      fetchRetailerWallet(user.uid);
      fetchOrders();
    } else {
      loading.value = false;
      loadingRatings.value = false;
      console.log("No retailer logged in for orders");
    }
  });
});
</script>

<style scoped>
/* Styles for rating display */
.retailer-rating {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  margin-top: 0.25rem;
}

.stars {
  display: flex;
  gap: 2px;
}

.star {
  color: #e2e8f0;
  font-size: 0.9rem;
}

.star.filled {
  color: #fbfaf9;
}

.star.large {
  font-size: 1.5rem;
}

.star.small {
  font-size: 0.8rem;
}

.rating-text {
  font-size: 0.85rem;
  color: #718096;
  font-weight: 500;
}

.no-rating {
  font-size: 0.85rem;
  color: #a0aec0;
  font-style: italic;
}

/* BAGO: Ratings & Feedback Styles */
.no-ratings {
  text-align: center;
  padding: 3rem 2rem;
  color: #718096;
}

.no-ratings h3 {
  margin: 1rem 0 0.5rem 0;
  color: #4a5568;
  font-size: 1.25rem;
}

.no-ratings p {
  margin-bottom: 1.5rem;
}

.ratings-container {
  display: flex;
  flex-direction: column;
  gap: 2rem;
}

.overall-rating-card {
  background: #FF7828;
  color: white;
  padding: 2rem;
  border-radius: 16px;
  box-shadow: 0 8px 32px rgba(102, 126, 234, 0.3);
}

.rating-summary {
  display: flex;
  justify-content: space-between;
  align-items: center;
  gap: 2rem;
}

.average-rating {
  text-align: center;
}

.rating-number {
  font-size: 3rem;
  font-weight: 700;
  margin-bottom: 0.5rem;
}

.rating-count {
  font-size: 0.9rem;
  opacity: 0.9;
}

.rating-breakdown {
  flex: 1;
  max-width: 300px;
}

.rating-bar {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  margin-bottom: 0.5rem;
}

.rating-label {
  font-size: 0.8rem;
  width: 50px;
}

.bar-container {
  flex: 1;
  background: rgba(255, 255, 255, 0.3);
  height: 8px;
  border-radius: 4px;
  overflow: hidden;
}

.bar-fill {
  height: 100%;
  background: white;
  border-radius: 4px;
  transition: width 0.3s ease;
}

.rating-percentage {
  font-size: 0.8rem;
  width: 35px;
  text-align: right;
}

.feedback-section {
  background: #f8fafc;
  border-radius: 12px;
  padding: 1.5rem;
  border: 1px solid #e2e8f0;
}

.feedback-list {
  display: flex;
  flex-direction: column;
  gap: 1rem;
}

.feedback-card {
  background: white;
  padding: 1.5rem;
  border-radius: 12px;
  border: 1px solid #e2e8f0;
  transition: all 0.3s ease;
}

.feedback-card:hover {
  border-color: #FF7828;
  box-shadow: 0 4px 12px rgba(255, 120, 40, 0.1);
  transform: translateY(-1px);
}

.feedback-header {
  display: flex;
  justify-content: space-between;
  align-items: flex-start;
  margin-bottom: 1rem;
}

.customer-info {
  display: flex;
  align-items: center;
  gap: 0.75rem;
}

.customer-avatar {
  width: 40px;
  height: 40px;
  border-radius: 50%;
  background: #FF7828;
  color: white;
  display: flex;
  align-items: center;
  justify-content: center;
  font-weight: 600;
  font-size: 0.9rem;
}

.customer-details {
  display: flex;
  flex-direction: column;
}

.customer-name {
  color: #1a202c;
  font-size: 1rem;
}

.feedback-date {
  color: #718096;
  font-size: 0.8rem;
}

.feedback-rating {
  display: flex;
  align-items: center;
  gap: 0.5rem;
}

.rating-value {
  font-weight: 600;
  color: #1a202c;
  font-size: 0.9rem;
}

.feedback-content {
  color: #4a5568;
}

.feedback-comment {
  margin: 0 0 1rem 0;
  line-height: 1.5;
  font-size: 0.95rem;
}

.order-info {
  background: #f7fafc;
  padding: 0.75rem;
  border-radius: 8px;
  border-left: 3px solid #FF7828;
}

.order-reference, .order-items {
  margin: 0.25rem 0;
  font-size: 0.85rem;
  color: #718096;
}

.order-info strong {
  color: #4a5568;
}

/* Payment status styles */
.payment-status {
  margin: 0;
  line-height: 1.4;
}

.payment-paid {
  color: #10b981;
  font-weight: 600;
  padding: 0.2rem 0.5rem;
  background: rgba(16, 185, 129, 0.1);
  border-radius: 4px;
  font-size: 0.8rem;
}

.payment-cod {
  color: #f59e0b;
  font-weight: 600;
  padding: 0.2rem 0.5rem;
  background: rgba(245, 158, 11, 0.1);
  border-radius: 4px;
  font-size: 0.8rem;
}

.payment-pending {
  color: #6b7280;
  font-weight: 600;
  padding: 0.2rem 0.5rem;
  background: rgba(107, 114, 128, 0.1);
  border-radius: 4px;
  font-size: 0.8rem;
}

.payment-refunded {
  color: #ef4444;
  font-weight: 600;
  padding: 0.2rem 0.5rem;
  background: rgba(239, 68, 68, 0.1);
  border-radius: 4px;
  font-size: 0.8rem;
}

.payment-failed {
  color: #dc2626;
  font-weight: 600;
  padding: 0.2rem 0.5rem;
  background: rgba(220, 38, 38, 0.1);
  border-radius: 4px;
  font-size: 0.8rem;
}

/* Payment badge */
.payment-badge {
  margin-top: 0.5rem;
  padding: 0.4rem 0.75rem;
  border-radius: 20px;
  font-size: 0.75rem;
  font-weight: 600;
  text-align: center;
}

.payment-badge.paid {
  background: rgba(16, 185, 129, 0.1);
  color: #10b981;
  border: 1px solid rgba(16, 185, 129, 0.2);
}

/* Delivery badge for history */
.delivery-badge {
  margin-top: 0.5rem;
  padding: 0.4rem 0.75rem;
  border-radius: 20px;
  font-size: 0.75rem;
  font-weight: 600;
  text-align: center;
  background: rgba(34, 197, 94, 0.1);
  color: #16a34a;
  border: 1px solid rgba(34, 197, 94, 0.2);
}

/* Status for paid_pending_approval */
.status.paid-pending {
  background: rgba(139, 92, 246, 0.1);
  color: #7e22ce;
  border: 1px solid rgba(139, 92, 246, 0.2);
}

/* New styles for ongoing and history sections */
.orders-container {
  display: flex;
  flex-direction: column;
  gap: 2rem;
}

.orders-section {
  background: #f8fafc;
  border-radius: 12px;
  padding: 1.5rem;
  border: 1px solid #e2e8f0;
}

.section-title {
  display: flex;
  align-items: center;
  gap: 0.75rem;
  margin: 0 0 0.5rem 0;
  color: #1a202c;
  font-size: 1.25rem;
  font-weight: 600;
}

.section-subtitle {
  margin: 0 0 1.5rem 0;
  color: #718096;
  font-size: 0.9rem;
}

.no-ongoing-orders,
.no-history {
  text-align: center;
  padding: 3rem 2rem;
  color: #718096;
  background: white;
  border-radius: 8px;
  border: 2px dashed #e2e8f0;
}

.no-ongoing-orders h4,
.no-history h4 {
  margin: 1rem 0 0.5rem 0;
  color: #4a5568;
  font-size: 1.1rem;
}

.no-ongoing-orders p,
.no-history p {
  margin: 0;
  font-size: 0.9rem;
}

/* History card styling */
.history-card {
  background: white;
  opacity: 0.9;
  border-left: 4px solid #e2e8f0;
}

.history-card:hover {
  opacity: 1;
  border-left-color: #cbd5e0;
}

/* Add new styles for order actions and status */
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

.order-actions {
  display: flex;
  gap: 0.75rem;
  margin-top: 1rem;
  padding-top: 1rem;
  border-top: 1px solid #e2e8f0;
}

.accept-btn {
  padding: 0.75rem 1.5rem;
  border: none;
  border-radius: 8px;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.3s ease;
  font-size: 0.9rem;
  display: flex;
  align-items: center;
  gap: 6px;
  background: #10b981;
  color: white;
  box-shadow: 0 4px 12px rgba(16, 185, 129, 0.3);
}

.accept-btn:hover {
  background: #059669;
  transform: translateY(-1px);
  box-shadow: 0 6px 16px rgba(16, 185, 129, 0.4);
}

/* Status Info Styles */
.status-info {
  margin-top: 1rem;
  padding: 0.75rem;
  border-radius: 8px;
  font-weight: 500;
}

.accepted-info {
  background: rgba(16, 185, 129, 0.1);
  border-left: 4px solid #10b981;
}

.assigned-info {
  background: rgba(139, 92, 246, 0.1);
  border-left: 4px solid #8b5cf6;
}

.ready-info {
  background: rgba(245, 158, 11, 0.1);
  border-left: 4px solid #f59e0b;
}

.transit-info {
  background: rgba(59, 130, 246, 0.1);
  border-left: 4px solid #3b82f6;
}

.delivered-info {
  background: rgba(34, 197, 94, 0.1);
  border-left: 4px solid #22c55e;
}

.cancelled-info {
  background: rgba(239, 68, 68, 0.1);
  border-left: 4px solid #ef4444;
}

.status-note {
  margin: 0 0 0.75rem 0;
  font-size: 0.9rem;
  font-weight: 600;
}

.accepted-info .status-note {
  color: #10b981;
}

.assigned-info .status-note {
  color: #8b5cf6;
}

.ready-info .status-note {
  color: #f59e0b;
}

.transit-info .status-note {
  color: #3b82f6;
}

.delivered-info .status-note {
  color: #22c55e;
}

.cancelled-info .status-note {
  color: #ef4444;
}

.driver-info, .delivery-time, .cancellation-reason {
  margin: 0.5rem 0 0 0;
  font-size: 0.85rem;
  color: #4a5568;
}

/* Update order details styling */
.order-details {
  display: flex;
  flex-direction: column;
  gap: 0.5rem;
}

.order-details p {
  margin: 0;
  line-height: 1.4;
}

.order-details strong {
  color: #374151;
}

/* Add new status styles */
.status.assigned {
  background: #e9d5ff;
  color: #7e22ce;
}

.status.ready {
  background: #fef3c7;
  color: #d97706;
}

/* Keep all existing styles below */
.retailer-dashboard {
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

.retailer-info h2 {
  font-size: 1.3rem;
  margin: 0;
  color: #1a202c;
  font-weight: 600;
}

.highlight {
  color: #FF7828;
  font-weight: 700;
}

.retailer-name {
  margin: 0;
  color: #718096;
  font-size: 0.95rem;
  font-weight: 500;
}

.header-actions {
  display: flex;
  gap: 12px;
  align-items: center;
}

.settings-btn, .logout-btn, .wallet-icon-btn {
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

.settings-btn, .wallet-icon-btn {
  background: white;
  color: #4a5568;
  border: 2px solid #e2e8f0;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
}

.settings-btn:hover, .wallet-icon-btn:hover {
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

.card-icon.today-orders {
  background: rgba(255, 120, 40, 0.1);
  color: #FF7828;
}

.card-icon.pending {
  background: rgba(245, 158, 11, 0.1);
  color: #f59e0b;
}

.card-icon.revenue {
  background: rgba(34, 197, 94, 0.1);
  color: #22c55e;
}

.card-icon.low-stock {
  background: rgba(239, 68, 68, 0.1);
  color: #ef4444;
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

/* Orders Grid */
.orders-grid {
  display: flex;
  flex-direction: column;
  gap: 1rem;
}

.order-card {
  display: flex;
  justify-content: space-between;
  align-items: flex-start;
  padding: 1.5rem;
  background: white;
  border-radius: 12px;
  border: 1px solid #e2e8f0;
  transition: all 0.3s ease;
}

.order-card:hover {
  border-color: #FF7828;
  box-shadow: 0 4px 12px rgba(255, 120, 40, 0.1);
  transform: translateY(-1px);
}

.order-main {
  flex: 1;
}

.order-header {
  display: flex;
  align-items: center;
  gap: 1rem;
  margin-bottom: 0.75rem;
}

.order-id {
  color: #1a202c;
  font-size: 1.1rem;
  font-weight: 600;
}

.status {
  font-size: 0.75rem;
  padding: 0.4rem 0.8rem;
  border-radius: 20px;
  text-transform: capitalize;
  font-weight: 600;
  letter-spacing: 0.5px;
}

.status.pending {
  background: #fef3c7;
  color: #d97706;
}

.status.paid-pending {
  background: rgba(139, 92, 246, 0.1);
  color: #7e22ce;
  border: 1px solid rgba(139, 92, 246, 0.2);
}

.status.accepted {
  background: #dbeafe;
  color: #1d4ed8;
}

.status.in-transit {
  background: #e9d5ff;
  color: #7e22ce;
}

.status.delivered {
  background: #d1fae5;
  color: #065f46;
}

.status.cancelled {
  background: #fee2e2;
  color: #dc2626;
}

.order-price {
  font-size: 1.25rem;
  font-weight: 700;
  color: #FF7828;
  min-width: 120px;
  text-align: right;
}

/* Retailer Wallet Styles */
.retailer-wallet-modal {
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

.close-btn {
  background: none;
  border: none;
  padding: 0.5rem;
  border-radius: 8px;
  cursor: pointer;
  color: #718096;
  transition: all 0.3s ease;
}

.close-btn:hover {
  background: #f7fafc;
  color: #4a5568;
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
.wallet-history {
  background: #f8fafc;
  border: 1px solid #e2e8f0;
  border-radius: 14px;
  padding: 1.5rem;
}

.wallet-card span {
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

.wallet-history {
  overflow-x: auto;
}

.wallet-history-header h3 {
  margin: 0 0 0.25rem 0;
  color: #1a202c;
  font-size: 1.1rem;
}

.wallet-history-header p {
  margin: 0 0 1rem 0;
  color: #718096;
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

.wallet-status.credited {
  background: rgba(34, 197, 94, 0.15);
  color: #16a34a;
}

.wallet-status.pending {
  background: rgba(245, 158, 11, 0.15);
  color: #d97706;
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

.wallet-icon-btn {
  color: #FF7828;
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

/* Responsive Design */
@media (max-width: 768px) {
  .retailer-dashboard {
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
  
  .tabs {
    flex-direction: column;
  }
  
  .order-card {
    flex-direction: column;
    gap: 1rem;
  }
  
  .order-price {
    text-align: left;
    min-width: auto;
  }
  
  .order-actions {
    flex-direction: column;
  }
  
  .modal-buttons {
    flex-direction: column;
  }

  /* Mobile styles for rating */
  .retailer-rating {
    flex-direction: column;
    align-items: flex-start;
    gap: 0.25rem;
  }

  /* Mobile styles for orders sections */
  .orders-section {
    padding: 1rem;
  }

  .section-title {
    font-size: 1.1rem;
  }

  /* BAGO: Mobile styles for ratings */
  .rating-summary {
    flex-direction: column;
    text-align: center;
    gap: 1.5rem;
  }

  .rating-breakdown {
    max-width: 100%;
  }

  .feedback-header {
    flex-direction: column;
    align-items: flex-start;
    gap: 1rem;
  }

  .feedback-rating {
    align-self: flex-start;
  }

  .retailer-wallet-modal {
    width: 95%;
    margin: 1rem;
  }

  .wallet-modal-header,
  .wallet-modal-content {
    padding-left: 1.25rem;
    padding-right: 1.25rem;
  }
}
</style>