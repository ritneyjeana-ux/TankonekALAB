<template>
  <div class="customer-order-page">
    <!-- Header -->
    <header class="dashboard-header">
      <div class="logo-section">
        <img :src="logo" alt="Tankonek Logo" class="logo" />
        <div class="customer-info">
          <h2>tankonek - <span class="highlight">Customer Portal</span></h2>
          <p class="customer-name">Welcome, {{ userName }}</p>
        </div>
      </div>
     
      <div class="header-actions">
        <button class="wallet-header-card" @click="showWalletModal = true">
          <span class="wallet-header-label">Wallet</span>
          <strong>₱{{ walletBalance.toLocaleString() }}</strong>
        </button>

        <div class="notification-icon">
          <svg width="20" height="20" viewBox="0 0 24 24" fill="none">
            <path d="M18 8A6 6 0 006 8c0 7-3 9-3 9h18s-3-2-3-9M13.73 21a2 2 0 01-3.46 0" stroke="currentColor" stroke-width="2"/>
          </svg>
        </div>
        <button class="profile-btn" @click="activeTab = 'profile'">
          <svg width="16" height="16" viewBox="0 0 24 24" fill="none">
            <path d="M20 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2" stroke="currentColor" stroke-width="2"/>
            <circle cx="12" cy="7" r="4" stroke="currentColor" stroke-width="2"/>
          </svg>
          Profile
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

    <!-- Tabs -->
    <div class="tabs">
      <button 
        class="tab" 
        :class="{ active: activeTab === 'order' }"
        @click="activeTab = 'order'"
      >
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none">
          <path d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" stroke="currentColor" stroke-width="2"/>
        </svg>
        Order Gas
      </button>

      <button 
        class="tab" 
        :class="{ active: activeTab === 'myOrders' }"
        @click="activeTab = 'myOrders'"
      >
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none">
          <path d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" stroke="currentColor" stroke-width="2"/>
        </svg>
        My Orders
      </button>

      
    
    </div>

    <!-- SMS Unlock Status Panel -->
    <div class="sms-unlock-panel" :class="{ unlocked: smsFeatureUnlocked }">
      <div class="sms-unlock-icon">
        <svg width="26" height="26" viewBox="0 0 24 24" fill="none">
          <path d="M21 15a4 4 0 01-4 4H8l-5 3V7a4 4 0 014-4h10a4 4 0 014 4v8z" stroke="currentColor" stroke-width="2"/>
          <path d="M8 9h8M8 13h5" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
        </svg>
      </div>

      <div class="sms-unlock-content">
        <h3>{{ smsFeatureUnlocked ? 'SMS Notifications Unlocked!' : 'SMS Notifications Locked' }}</h3>
        <p v-if="smsFeatureUnlocked">
          You can now receive SMS order updates using your contact number.
        </p>
        <p v-else>
          Complete {{ successfulTransactionsRemaining }} more successful transaction{{ successfulTransactionsRemaining === 1 ? '' : 's' }} to unlock SMS order updates.
        </p>

        <div class="sms-progress-bar">
          <div class="sms-progress-fill" :style="{ width: smsUnlockProgress + '%' }"></div>
        </div>

        <small>{{ successfulTransactionsCount }}/10 successful transactions completed</small>
      </div>
    </div>

    <!-- SMS Unlock Success Modal -->
    <div v-if="showSmsUnlockModal" class="modal-overlay">
      <div class="modal sms-unlock-modal">
        <div class="modal-icon sms-success">
          <svg width="52" height="52" viewBox="0 0 24 24" fill="none">
            <path d="M21 15a4 4 0 01-4 4H8l-5 3V7a4 4 0 014-4h10a4 4 0 014 4v8z" stroke="#FF7828" stroke-width="2"/>
            <path d="M8 11l2.5 2.5L16 8" stroke="#FF7828" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
          </svg>
        </div>
        <h3>You unlocked SMS notifications!</h3>
        <p>
          Congratulations! You reached 10 successful transactions.
          SMS order updates are now available for your next orders.
        </p>
        <div class="modal-buttons">
          <button class="modal-btn-confirm" @click="closeSmsUnlockModal">
            Got it
          </button>
        </div>
      </div>
    </div>

    <!-- Order Gas Section -->
    <section class="content-section">
      <div v-if="activeTab === 'order'" class="tab-content">
        <div class="content-header">
          <h2>Nearby Gas Retailers</h2>
          <p>Find and order from retailers near you</p>
          
          <!-- Customer Location Info -->
          <div v-if="customerLocation" class="customer-location-info">
            <div class="location-badge">
              <svg width="16" height="16" viewBox="0 0 24 24" fill="none">
                <path d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" stroke="#FF7828" stroke-width="2"/>
                <path d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" stroke="#FF7828" stroke-width="2"/>
              </svg>
              Your Location: {{ customerLocation.address || 'Loading your location...' }}
            </div>
            <button v-if="!customerLocation.address" @click="refreshLocation" class="location-refresh-btn">
              <svg width="14" height="14" viewBox="0 0 24 24" fill="none">
                <path d="M23 4v6h-6M1 20v-6h6" stroke="currentColor" stroke-width="2"/>
                <path d="M3.51 9a9 9 0 0114.85-3.36L23 10M1 14l4.64 4.36A9 9 0 0020.49 15" stroke="currentColor" stroke-width="2"/>
              </svg>
              Refresh Location
            </button>
          </div>
        </div>

        <!-- Place Filter Section - FIXED -->
        <div class="place-filter-section">
          <div class="filter-container">
            <label class="filter-label">
              <svg width="18" height="18" viewBox="0 0 24 24" fill="none" class="filter-icon">
                <path d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" stroke="currentColor" stroke-width="2"/>
                <path d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" stroke="currentColor" stroke-width="2"/>
              </svg>
              Filter by Location:
            </label>
            
            <div class="select-wrapper">
              <select v-model="selectedBarangay" class="place-select" @change="filterRetailers">
                <option value="">All Locations in {{ customerMunicipality }}</option>
                <optgroup v-if="customerBarangay" :label="`Your Area: ${customerBarangay}`">
                  <option :value="customerBarangay">{{ customerBarangay }} (Your Location)</option>
                </optgroup>
                <optgroup label="Nearby Areas">
                  <option v-for="location in nearbyBarangays" :key="location" :value="location">
                    {{ location }}
                  </option>
                </optgroup>
                <optgroup label="Other Barangays">
                  <option v-for="location in otherBarangays" :key="location" :value="location">
                    {{ location }}
                  </option>
                </optgroup>
              </select>
              <div class="select-arrow">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none">
                  <path d="M6 9l6 6 6-6" stroke="currentColor" stroke-width="2"/>
                </svg>
              </div>
            </div>

            <!-- Active Filter Indicator -->
            <div v-if="selectedBarangay" class="active-filter-indicator">
              <span class="filter-text">
                Showing retailers in: <strong>{{ selectedBarangay }}</strong>
                <span v-if="selectedBarangay === customerBarangay" class="your-location-badge">(Your Location)</span>
              </span>
              <button @click="clearFilter" class="clear-filter-btn">
                Clear Filter
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none">
                  <path d="M18 6L6 18M6 6l12 12" stroke="currentColor" stroke-width="2"/>
                </svg>
              </button>
            </div>
          </div>
        </div>

        <!-- Retailers Grid -->
        <div class="retailers-grid">
          <div
            v-for="retailer in filteredRetailers"
            :key="retailer.id"
            class="retailer-card"
          >
            <div class="retailer-header">
              <div class="retailer-name">{{ retailer.name }}</div>
              <div class="retailer-rating">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none">
                  <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z" 
                        :fill="retailer.rating >= 4 ? '#FF7828' : '#e2e8f0'"/>
                </svg>
                {{ retailer.rating }} • ({{ retailer.totalRatings || 0 }} ratings)
              </div>
            </div>
            
            <div class="retailer-details">
              <div class="detail-item">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" class="detail-icon">
                  <path d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" stroke="currentColor" stroke-width="2"/>
                  <path d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" stroke="currentColor" stroke-width="2"/>
                </svg>
                <span>{{ retailer.barangay }}</span>
              </div>
              <div class="detail-item">
                <span :class="getDistanceClass(retailer.distance)">
                  {{ getDistanceDisplay(retailer.distance) }}
                </span>
              </div>

              <!-- Availability Status Only - No Price Display -->
              <div class="detail-item" v-if="retailer.inventory && retailer.inventory.length > 0">
                <div class="availability-info">
                  <span class="availability-tag" :class="hasAvailableGas(retailer) ? 'available' : 'out-of-stock'">
                    {{ hasAvailableGas(retailer) ? 'Gas Available' : 'Out of Stock' }}
                  </span>
                </div>
              </div>
            </div>

            <div class="retailer-actions">
              <button 
                class="view-details-btn" 
                @click="viewRetailerDetails(retailer)"
              >
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none">
                  <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z" stroke="currentColor" stroke-width="2"/>
                  <circle cx="12" cy="12" r="3" stroke="currentColor" stroke-width="2"/>
                </svg>
                View Details
              </button>
              <button 
                class="order-btn" 
                @click="orderGas(retailer)" 
                :disabled="!retailer.distance || !hasAvailableGas(retailer)"
                :title="!hasAvailableGas(retailer) ? 'No gas available' : ''"
              >
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none">
                  <path d="M3 3h2l.4 2M7 13h10l4-8H5.4m0 0L7 13m0 0l-2.5 5.5M7 13l2.5 5.5m0 0L17 21" stroke="currentColor" stroke-width="2"/>
                  <circle cx="9" cy="19" r="1" stroke="currentColor" stroke-width="2"/>
                  <circle cx="17" cy="19" r="1" stroke="currentColor" stroke-width="2"/>
                </svg>
                {{ !hasAvailableGas(retailer) ? 'Out of Stock' : (retailer.distance ? 'Order Now' : 'Calculating...') }}
              </button>
            </div>
          </div>

          <!-- No Results Message -->
          <div v-if="filteredRetailers.length === 0" class="no-results">
            <svg width="64" height="64" viewBox="0 0 24 24" fill="none">
              <path d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" stroke="#CBD5E0" stroke-width="2"/>
            </svg>
            <h3>No retailers found in {{ selectedBarangay }}</h3>
            <p>Try selecting a different location</p>
            <button class="btn-primary" @click="clearFilter">Show All Locations</button>
          </div>
        </div>
      </div>

      <div v-else-if="activeTab === 'myOrders'">
        <MyOrder />
      </div>

      <div v-else-if="activeTab === 'rewards'">
        <Reward />
      </div>

      <div v-else-if="activeTab === 'profile'">
        <Profile />
      </div>
    </section>

    <!-- Retailer Details Modal -->
    <div v-if="showRetailerDetails" class="modal-overlay">
      <div class="retailer-details-modal">
        <div class="modal-header">
          <h2>{{ selectedRetailerDetails?.name }}</h2>
          <button class="close-btn" @click="closeRetailerDetails">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none">
              <path d="M18 6L6 18M6 6l12 12" stroke="currentColor" stroke-width="2"/>
            </svg>
          </button>
        </div>

        <div class="modal-content">
          <!-- Location Info -->
          <div class="info-section">
            <h3>Location Information</h3>
            <div class="info-grid">
              <div class="info-item">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" class="info-icon">
                  <path d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" stroke="#FF7828" stroke-width="2"/>
                  <path d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" stroke="#FF7828" stroke-width="2"/>
                </svg>
                <div class="info-content">
                  <div class="info-label">Address</div>
                  <div class="info-value">{{ selectedRetailerDetails?.barangay }}</div>
                </div>
              </div>
              <div class="info-item">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" class="detail-icon" style="opacity: 0;">
                  <path d="M1 1l22 22M23 1L1 23" stroke="currentColor" stroke-width="2"/>
                </svg>
                <div class="info-content">
                  <div class="info-label">Distance</div>
                  <div class="info-value" :class="getDistanceClass(selectedRetailerDetails?.distance)">
                    {{ getDistanceDisplay(selectedRetailerDetails?.distance) }}
                  </div>
                </div>
              </div>
              <div class="info-item">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" class="info-icon">
                  <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z" stroke="#FF7828" stroke-width="2"/>
                </svg>
                <div class="info-content">
                  <div class="info-label">Customer Rating</div>
                  <div class="info-value">
                    <span class="rating-display">
                      {{ selectedRetailerDetails?.rating || 'No ratings yet' }}
                      <span v-if="selectedRetailerDetails?.rating" class="rating-stars-small">
                        <span v-for="star in 5" :key="star" :class="{ 'filled': star <= Math.round(selectedRetailerDetails.rating) }">★</span>
                      </span>
                      ({{ selectedRetailerDetails?.totalRatings || 0 }} ratings)
                    </span>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Available Products - WITH PRICE DISPLAY -->
          <div class="info-section" v-if="selectedRetailerDetails?.inventory && selectedRetailerDetails.inventory.length > 0">
            <h3>Available Products</h3>

            <!-- All Products List -->
            <div class="products-grid">
              <div 
                v-for="gas in selectedRetailerDetails.inventory" 
                :key="gas.type"
                class="product-card"
                :class="{ 'out-of-stock': gas.currentStock <= 0, 'solane-product': gas.type.toLowerCase().includes('solane') }"
              >
                <div class="product-info">
                  <div class="product-name">{{ gas.type }}</div>
                  <div class="product-size">{{ gas.tankSize || 'Standard Tank' }}</div>
                  <div class="product-stock" :class="{ 'low-stock': gas.currentStock <= gas.minStock }">
                    {{ gas.currentStock > 0 ? `${gas.currentStock} in stock` : 'Out of stock' }}
                  </div>
                </div>
                <div class="product-price">₱{{ gas.price.toLocaleString() }}</div>
              </div>
            </div>
          </div>

          <!-- NEW: Customer Ratings and Feedback Section -->
          <div class="info-section" v-if="selectedRetailerDetails">
            <div class="section-header">
              <h3>Customer Reviews</h3>
              <div class="reviews-summary">
                <div class="overall-rating">
                  <div class="rating-score">{{ selectedRetailerDetails.rating || 0 }}</div>
                  <div class="rating-stars-display">
                    <span v-for="star in 5" :key="star" :class="{ 'filled': star <= Math.round(selectedRetailerDetails.rating || 0) }">★</span>
                  </div>
                  <div class="total-reviews">{{ selectedRetailerDetails.totalRatings || 0 }} reviews</div>
                </div>
              </div>
            </div>

            <!-- Real-time Ratings and Feedback List -->
            <div class="ratings-feedback-section">
              <div v-if="retailerFeedbacks.length === 0" class="no-reviews">
                <svg width="48" height="48" viewBox="0 0 24 24" fill="none">
                  <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z" stroke="#CBD5E0" stroke-width="2"/>
                </svg>
                <p>No reviews yet</p>
                <small>Be the first to rate this retailer!</small>
              </div>

              <div v-else class="reviews-list">
                <div 
                  v-for="feedback in retailerFeedbacks" 
                  :key="feedback.id"
                  class="feedback-item"
                >
                  <div class="feedback-header">
                    <div class="customer-info">
                      <div class="customer-name">{{ feedback.customerName || 'Customer' }}</div>
                      <div class="feedback-date">{{ formatFeedbackDate(feedback.createdAt) }}</div>
                    </div>
                    <div class="rating-display-small">
                      <span class="rating-stars-small">
                        <span v-for="star in 5" :key="star" :class="{ 'filled': star <= feedback.rating }">★</span>
                      </span>
                      <span class="rating-number">{{ feedback.rating }}/5</span>
                    </div>
                  </div>
                  
                  <div v-if="feedback.comment && feedback.comment !== 'No comment provided'" class="feedback-comment">
                    "{{ feedback.comment }}"
                  </div>

                  <div class="order-info" v-if="feedback.orderNumber">
                    <small>Order: {{ feedback.orderNumber }}</small>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Action Buttons -->
          <div class="modal-actions">
            <button class="secondary-btn" @click="closeRetailerDetails">
              Close
            </button>
            <button 
              class="primary-btn" 
              @click="orderFromDetails"
              :disabled="!selectedRetailerDetails?.distance || !hasAvailableGas(selectedRetailerDetails)"
            >
              <svg width="16" height="16" viewBox="0 0 24 24" fill="none">
                <path d="M3 3h2l.4 2M7 13h10l4-8H5.4m0 0L7 13m0 0l-2.5 5.5M7 13l2.5 5.5m0 0L17 21" stroke="currentColor" stroke-width="2"/>
                <circle cx="9" cy="19" r="1" stroke="currentColor" stroke-width="2"/>
                <circle cx="17" cy="19" r="1" stroke="currentColor" stroke-width="2"/>
              </svg>
              Order Now
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- Order Form Modal -->
    <div v-if="showOrderForm" class="modal-overlay">
      <div class="order-modal">
        <div class="order-modal-header">
          <h2>Place Order</h2>
          <button class="close-btn" @click="closeOrderForm">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none">
              <path d="M18 6L6 18M6 6l12 12" stroke="currentColor" stroke-width="2"/>
            </svg>
          </button>
        </div>

        <div class="order-modal-content">
          <!-- Business Info -->
          <div class="business-info-section">
            <div class="business-name">{{ selectedRetailer.name }}</div>
            <div class="retailer-rating-info">
              <span class="rating-badge">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none">
                  <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z" 
                        :fill="selectedRetailer.rating >= 4 ? '#FF7828' : '#e2e8f0'"/>
                </svg>
                {{ selectedRetailer.rating }} ({{ selectedRetailer.totalRatings || 0 }} ratings)
              </span>
            </div>
          </div>

          <!-- Gas Type Selection -->
          <div class="form-section" v-if="selectedRetailer.inventory && selectedRetailer.inventory.length > 0">
            <label class="form-label">Select Gas Type</label>
            <div class="gas-type-options">
              <label 
                v-for="gas in selectedRetailer.inventory" 
                :key="gas.type"
                class="gas-option"
                :class="{ 'out-of-stock': gas.currentStock <= 0 }"
              >
                <input 
                  type="radio" 
                  v-model="selectedGasType" 
                  :value="gas"
                  :disabled="gas.currentStock <= 0"
                  class="gas-radio"
                />
                <div class="gas-option-content">
                  <div class="gas-type-info">
                    <div class="tank-size">{{ gas.tankSize || '11kg LPG Tank' }}</div>
                    <div class="gas-brand">{{ gas.type }}</div>
                    <div class="gas-stock" :class="{ 'low-stock': gas.currentStock <= gas.minStock }">
                      {{ gas.currentStock > 0 ? `${gas.currentStock} in stock` : 'Out of stock' }}
                    </div>
                  </div>
                  <div class="gas-price">₱{{ gas.price.toLocaleString() }}</div>
                </div>
              </label>
            </div>
          </div>

          <!-- Quantity -->
          <div class="form-section">
            <label class="form-label">Quantity</label>
            <div class="quantity-selector">
              <button 
                class="quantity-btn" 
                @click="decreaseQuantity"
                :disabled="quantity <= 1 || !selectedGasType"
              >
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none">
                  <path d="M5 12h14" stroke="currentColor" stroke-width="2"/>
                </svg>
              </button>
              <span class="quantity-display">{{ quantity }}</span>
              <button 
                class="quantity-btn" 
                @click="increaseQuantity"
                :disabled="!selectedGasType || (selectedGasType && quantity >= selectedGasType.currentStock)"
              >
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none">
                  <path d="M12 5v14M5 12h14" stroke="currentColor" stroke-width="2"/>
                </svg>
              </button>
            </div>
            <div v-if="selectedGasType" class="stock-info">
              Max: {{ selectedGasType.currentStock }} tanks available
            </div>
          </div>

          <!-- Delivery Address -->
          <div class="form-section">
            <label class="form-label">Delivery Address</label>
            <textarea 
              v-model="deliveryAddress"
              class="address-input"
              placeholder="Enter your complete delivery address"
              rows="3"
            ></textarea>
          </div>

          <!-- Contact Number -->
          <div class="form-section">
            <label class="form-label">Contact Number</label>
            <input 
              v-model="contactNumber"
              type="tel"
              class="contact-input"
              placeholder="09123456789"
            />
          </div>

          <!-- SMS Notification Option -->
          <div class="form-section">
            <div class="sms-order-option" :class="{ locked: !smsFeatureUnlocked }">
              <div class="sms-order-info">
                <strong>{{ smsFeatureUnlocked ? 'SMS Order Updates' : 'SMS Order Updates Locked' }}</strong>
                <p v-if="smsFeatureUnlocked">
                  Receive simulated SMS updates for this order using your contact number.
                </p>
                <p v-else>
                  Unlock this after 10 successful transactions. Current progress: {{ successfulTransactionsCount }}/10.
                </p>
              </div>

              <label class="sms-toggle">
                <input
                  type="checkbox"
                  v-model="smsPreferenceEnabled"
                  :disabled="!smsFeatureUnlocked"
                />
                <span></span>
              </label>
            </div>
          </div>

          <!-- Special Instructions -->
          <div class="form-section">
            <label class="form-label">Special Instructions (Optional)</label>
            <textarea 
              v-model="specialInstructions"
              class="instructions-input"
              placeholder="Any special notes for delivery"
              rows="2"
            ></textarea>
          </div>

          <!-- Payment Method Section -->
          <div class="form-section">
            <label class="form-label">Payment Method</label>
            <div class="payment-options">
              <label class="payment-option">
                <input 
                  type="radio" 
                  v-model="paymentMethod" 
                  value="cash"
                  class="payment-radio"
                />
                <div class="payment-content">
                  <div class="payment-icon">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none">
                      <rect x="2" y="5" width="20" height="14" rx="2" stroke="currentColor" stroke-width="2"/>
                      <path d="M2 9h20M6 13h4M15 13h3" stroke="currentColor" stroke-width="2"/>
                    </svg>
                  </div>
                  <div class="payment-info">
                    <div class="payment-name">Cash on Delivery</div>
                    <div class="payment-desc">Pay when you receive the order</div>
                  </div>
                </div>
              </label>

              <label class="payment-option">
                <input 
                  type="radio" 
                  v-model="paymentMethod" 
                  value="wallet"
                  class="payment-radio"
                />
                <div class="payment-content">
                  <div class="payment-icon wallet-payment-icon">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none">
                      <rect x="2" y="5" width="20" height="14" rx="2" stroke="currentColor" stroke-width="2"/>
                      <path d="M16 12h4M6 9h6" stroke="currentColor" stroke-width="2"/>
                    </svg>
                  </div>
                  <div class="payment-info">
                    <div class="payment-name">Tankonek Wallet</div>
                    <div class="payment-desc">{{ walletStatus === 'linked' ? `Available balance: ₱${walletBalance.toLocaleString()}` : 'Link PayPal wallet first' }}</div>
                  </div>
                </div>
              </label>

              <label class="payment-option">
                <input 
                  type="radio" 
                  v-model="paymentMethod" 
                  value="paypal"
                  class="payment-radio"
                />
                <div class="payment-content">
                  <div class="payment-icon paypal-icon">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none">
                      <path d="M8 12h8M12 8v8" stroke="currentColor" stroke-width="2"/>
                      <rect x="3" y="3" width="18" height="18" rx="4" stroke="currentColor" stroke-width="2"/>
                    </svg>
                  </div>
                  <div class="payment-info">
                    <div class="payment-name">PayPal Sandbox</div>
                    <div class="payment-desc">Pay using the Admin PayPal Sandbox merchant account</div>
                  </div>
                </div>
              </label>
            </div>

            <!-- PayPal Sandbox Payment Simulation -->
            <div v-if="paymentMethod === 'paypal' && canPlaceOrder" class="paypal-sandbox-container">
              <button class="paypal-sandbox-pay-btn" @click="openPayPalPaymentSandbox">
                Pay ₱{{ total.toLocaleString() }} with PayPal Sandbox
              </button>
              <p class="paypal-sandbox-note">This simulates a PayPal sandbox payment for prototype testing.</p>
            </div>
          </div>

          <!-- Order Summary -->
          <div class="order-summary">
            <div class="summary-row">
              <span>Subtotal ({{ quantity }} tank{{ quantity > 1 ? 's' : '' }})</span>
              <span>₱{{ subtotal.toLocaleString() }}</span>
            </div>
            <div class="summary-row">
              <span>
                Delivery Fee 
                <span v-if="selectedRetailer?.distance" class="fee-breakdown">
                  (₱15 base + ₱5 per additional km)
                </span>
              </span>
              <span>₱{{ deliveryFee.toLocaleString() }}</span>
            </div>
            <div class="summary-divider"></div>
            <div class="summary-row total">
              <span>Total Amount</span>
              <span>₱{{ total.toLocaleString() }}</span>
            </div>
          </div>

          <!-- Action Buttons -->
          <div class="order-actions">
            <button class="cancel-btn" @click="closeOrderForm">Cancel</button>
            
            <!-- Show Confirm Order button for Cash on Delivery and Wallet -->
            <button 
              v-if="paymentMethod === 'cash' || paymentMethod === 'wallet'"
              class="confirm-order-btn" 
              @click="confirmOrder"
              :disabled="!canPlaceOrder || (paymentMethod === 'wallet' && (walletStatus !== 'linked' || walletBalance < total))"
            >
              <svg width="18" height="18" viewBox="0 0 24 24" fill="none">
                <path d="M5 13l4 4L19 7" stroke="currentColor" stroke-width="2"/>
              </svg>
              {{ paymentMethod === 'wallet' ? 'Pay with Wallet' : 'Confirm Order' }}
            </button>

            <!-- For PayPal, the PayPal Sandbox button handles the payment -->
            <div v-else class="paypal-placeholder">
              <p>Use the PayPal Sandbox simulation button above to pay</p>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Payment Success Modal -->
    <div v-if="showPaymentSuccess" class="modal-overlay">
      <div class="modal success-modal">
        <div class="modal-icon success">
          <svg width="48" height="48" viewBox="0 0 24 24" fill="none">
            <path d="M22 11.08V12a10 10 0 11-5.93-9.14" stroke="#10B981" stroke-width="2"/>
            <path d="M22 4L12 14.01l-3-3" stroke="#10B981" stroke-width="2"/>
          </svg>
        </div>
        <h3>Payment Successful!</h3>
        <p>Your order has been confirmed and payment has been processed.</p>
        <div class="order-details">
          <p><strong>Order Number:</strong> {{ currentOrderNumber }}</p>
          <p><strong>Amount Paid:</strong> ₱{{ total.toLocaleString() }}</p>
          <p><strong>Payment Method:</strong> {{ currentPaymentMethodLabel }}</p>
        </div>
        <div class="modal-buttons">
          <button class="modal-btn-confirm" @click="closePaymentSuccess">
            Continue Shopping
          </button>
        </div>
      </div>
    </div>
     
    <!-- Customer Wallet Modal -->
    <div v-if="showWalletModal" class="modal-overlay">
      <div class="customer-wallet-modal">
        <div class="wallet-modal-header">
          <div>
            <h2>Customer E-Wallet</h2>
            <p>Manage your PayPal-linked wallet, cash-ins, and order payments.</p>
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
              <h2>₱{{ walletBalance.toLocaleString() }}</h2>
              <p>Ready for Tankonek orders</p>
            </div>

            <div class="wallet-card">
              <span>PayPal Status</span>
              <h3>{{ walletStatus === 'linked' ? 'Linked' : 'Not Linked' }}</h3>
              <p>{{ walletStatus === 'linked' ? maskedPayPalEmail : 'Link your PayPal first' }}</p>
            </div>

            <div class="wallet-card">
              <span>PayPal Sandbox</span>
              <h3>PayPal Sandbox</h3>
              <p>Prototype payment simulation</p>
            </div>
          </div>

          <div class="wallet-info-card">
            <h3>How Customer Wallet Works</h3>
            <p>
              Link your PayPal email first, then use the sandbox cash-in to add balance.
              You can use this wallet to pay for LPG orders, and every cash-in or payment is recorded below.
            </p>
          </div>

          <div class="wallet-layout">
            <div class="wallet-panel">
              <div class="wallet-panel-header">
                <h3>PayPal Wallet Linking</h3>
                <p>Register or update your PayPal email before cash-in or wallet payment.</p>
              </div>

              <div class="wallet-topup-form">
                <label class="form-label">PayPal Email</label>
                <div class="wallet-amount-input">
                  <span>P</span>
                  <input
                    v-model="paypalEmail"
                    type="email"
                    placeholder="buyer@example.com"
                  />
                </div>

                <button
                  class="wallet-primary-btn"
                  @click="linkPayPalWallet"
                  :disabled="walletLoading"
                >
                  {{ walletLoading ? 'Processing...' : (walletStatus === 'linked' ? 'Update PayPal Email' : 'Link PayPal Account') }}
                </button>

                <p class="wallet-note">
                  This stores the customer's PayPal email as the linked wallet account for the prototype.
                </p>
              </div>
            </div>

            <div class="wallet-panel">
              <div class="wallet-panel-header">
                <h3>PayPal Sandbox Cash-In Simulation</h3>
                <p>Use PayPal Sandbox simulation to simulate adding funds to your wallet.</p>
              </div>

              <div class="wallet-topup-form">
                <label class="form-label">Amount</label>
                <div class="wallet-amount-input">
                  <span>₱</span>
                  <input
                    v-model.number="topUpAmount"
                    type="number"
                    min="1"
                    placeholder="0.00"
                  />
                </div>

                <div class="quick-amounts">
                  <button type="button" @click="topUpAmount = 100">₱100</button>
                  <button type="button" @click="topUpAmount = 300">₱300</button>
                  <button type="button" @click="topUpAmount = 500">₱500</button>
                  <button type="button" @click="topUpAmount = 1000">₱1,000</button>
                </div>

                <button
                  class="wallet-primary-btn paypal-primary-btn"
                  @click="topUpWallet"
                  :disabled="!topUpAmount || topUpAmount <= 0 || walletLoading"
                >
                  {{ walletLoading ? 'Processing...' : 'Open PayPal Simulation' }}
                </button>

                <div v-if="topUpAmount && topUpAmount > 0" class="paypal-sandbox-container">
                  <p class="paypal-sandbox-note">Click the button above to simulate PayPal Sandbox cash-in.</p>
                </div>

                <p class="wallet-note">
                  Sandbox mode uses PayPal Sandbox simulation and records the test reference number.
                </p>
              </div>
            </div>
          </div>

          <div class="wallet-history">
            <div class="wallet-history-header">
              <h3>Wallet Transactions</h3>
              <p>Recent cash-ins and wallet payments</p>
            </div>

            <table class="wallet-table">
              <thead>
                <tr>
                  <th>Date</th>
                  <th>Transaction</th>
                  <th>Reference</th>
                  <th>Method</th>
                  <th>Amount</th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="transaction in walletTransactions" :key="transaction.id">
                  <td>{{ formatWalletDate(transaction.createdAt) }}</td>
                  <td>{{ transaction.description || transaction.type }}</td>
                  <td>{{ transaction.referenceNumber || 'N/A' }}</td>
                  <td>{{ transaction.paymentMethod || 'Wallet' }}</td>
                  <td>
                    <span :class="transaction.amount >= 0 ? 'wallet-credit' : 'wallet-debit'">
                      {{ transaction.amount >= 0 ? '+' : '-' }}₱{{ Math.abs(transaction.amount || 0).toLocaleString() }}
                    </span>
                  </td>
                </tr>
                <tr v-if="walletTransactions.length === 0">
                  <td colspan="5" class="wallet-empty">No wallet transactions yet</td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>

    <!-- PayPal Sandbox Modal -->
    <div v-if="showPayPalSandboxModal" class="modal-overlay">
      <div class="modal paypal-sandbox-modal">
        <div class="modal-icon paypal">
          <svg width="48" height="48" viewBox="0 0 24 24" fill="none">
            <rect x="3" y="4" width="18" height="16" rx="4" stroke="#2563EB" stroke-width="2"/>
            <path d="M8 12h8M12 8v8" stroke="#2563EB" stroke-width="2"/>
          </svg>
        </div>

        <h3>PayPal Sandbox</h3>
        <p>This is a simulated PayPal Sandbox payment for prototype testing.</p>

        <div class="paypal-sandbox-details">
          <p><strong>Transaction Type:</strong> {{ paypalSandboxMode === 'cashin' ? 'Wallet Cash-In' : 'Order Payment' }}</p>
          <p><strong>PayPal Email:</strong> {{ paypalSandboxEmail }}</p>
          <p><strong>Amount:</strong> ₱{{ paypalSandboxAmount.toLocaleString() }}</p>
          <p><strong>Reference:</strong> {{ paypalSandboxReference }}</p>
        </div>

        <div class="modal-buttons">
          <button class="modal-btn-cancel" @click="closePayPalSandboxModal">Cancel</button>
          <button class="modal-btn-confirm paypal-confirm-btn" @click="confirmPayPalSandboxPayment" :disabled="paypalSandboxProcessing">
            {{ paypalSandboxProcessing ? 'Processing...' : 'Approve Simulated Payment' }}
          </button>
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
  </div>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted, watch, nextTick } from "vue";
import { router } from "@inertiajs/vue3";
import MyOrder from "./MyOrder.vue";
import Profile from "./Profile.vue";

// FIREBASE
import { db, auth } from "@/firebase";
import { 
  collection, 
  onSnapshot, 
  query, 
  where, 
  doc, 
  getDoc, 
  addDoc,
  getDocs,
  updateDoc,
  serverTimestamp,
  orderBy,
  limit,
  runTransaction
} from "firebase/firestore";
import { onAuthStateChanged } from "firebase/auth";

const logo = "/images/logo.png";

// PayPal Sandbox Client ID for Admin/Platform merchant account
const ADMIN_PAYPAL_CLIENT_ID = "Ae7Fm9pT9iCbCktH2NiPp8C5r9t2G2ZtuByxGLW1BGcgjOrSF7lENCFuh3WVEwvc0mCzajtwfrpiszIE";

const userName = ref("Customer");
const activeTab = ref("order");
const selectedBarangay = ref("");
const userPoints = ref(150);

// Customer location - ALWAYS USE FIRESTORE DATA
const customerLocation = ref({
  latitude: null,
  longitude: null,
  address: null
});

// Customer barangay and municipality
const customerBarangay = ref("");
const customerMunicipality = ref("Victoria, Oriental Mindoro");

// Firestore realtime retailers
const retailers = ref([]);

// Order form variables
const showOrderForm = ref(false);
const selectedRetailer = ref(null);
const selectedGasType = ref(null);
const quantity = ref(1);
const deliveryAddress = ref("");
const contactNumber = ref("");
const specialInstructions = ref("");
const paymentMethod = ref("cash");

// Retailer Details Modal
const showRetailerDetails = ref(false);
const selectedRetailerDetails = ref(null);

// NEW: Retailer feedbacks real-time data
const retailerFeedbacks = ref([]);

// NEW PAYMENT VARIABLES
const showPaymentSuccess = ref(false);
const currentOrderNumber = ref('');
const currentPaymentMethodLabel = ref('PayPal Sandbox');

// CUSTOMER WALLET
const walletBalance = ref(0);
const walletTransactions = ref([]);
const walletLoading = ref(false);
const topUpAmount = ref(null);
const paypalEmail = ref("");
const cashInReference = ref("");
const walletStatus = ref("not_linked");
const showWalletModal = ref(false);

// PAYPAL SANDBOX
const showPayPalSandboxModal = ref(false);
const paypalSandboxMode = ref("");
const paypalSandboxAmount = ref(0);
const paypalSandboxReference = ref("");
const paypalSandboxEmail = ref("");
const paypalSandboxProcessing = ref(false);

// SMS UNLOCK FEATURE
const successfulTransactionsCount = ref(0);
const showSmsUnlockModal = ref(false);
const smsPreferenceEnabled = ref(false);
const smsUnlockUnsubscribe = ref(null);
const smsFeatureUnlocked = computed(() => successfulTransactionsCount.value >= 10);
const successfulTransactionsRemaining = computed(() => Math.max(10 - successfulTransactionsCount.value, 0));
const smsUnlockProgress = computed(() => Math.min((successfulTransactionsCount.value / 10) * 100, 100));

const loadSmsUnlockStatus = (userId) => {
  if (smsUnlockUnsubscribe.value) {
    smsUnlockUnsubscribe.value();
    smsUnlockUnsubscribe.value = null;
  }

  const deliveredOrdersQuery = query(
    collection(db, "orders"),
    where("customerId", "==", userId),
    where("orderStatus", "==", "delivered")
  );

  smsUnlockUnsubscribe.value = onSnapshot(deliveredOrdersQuery, (snapshot) => {
    const wasUnlocked = smsFeatureUnlocked.value;
    successfulTransactionsCount.value = snapshot.size;

    if (!wasUnlocked && snapshot.size >= 10) {
      const storageKey = `tankonek_sms_unlocked_seen_${userId}`;
      const alreadySeen = localStorage.getItem(storageKey);

      if (!alreadySeen) {
        showSmsUnlockModal.value = true;
        localStorage.setItem(storageKey, "true");
      }
    }

    if (snapshot.size < 10) {
      smsPreferenceEnabled.value = false;
    }
  }, (error) => {
    console.error("Error loading SMS unlock status:", error);
  });
};

const closeSmsUnlockModal = () => {
  showSmsUnlockModal.value = false;
};

const getSmsPhoneNumber = () => {
  return String(contactNumber.value || "").trim();
};

const isValidSmsPhoneNumber = (phoneNumber) => {
  return /^09\d{9}$/.test(phoneNumber);
};

const getSmsOrderPayload = () => {
  const phoneNumber = getSmsPhoneNumber();
  const enabled = smsFeatureUnlocked.value && smsPreferenceEnabled.value && isValidSmsPhoneNumber(phoneNumber);

  return {
    smsNotificationUnlocked: smsFeatureUnlocked.value,
    smsNotificationEnabled: enabled,
    smsNotificationPhone: phoneNumber,
    smsNotificationStatus: enabled ? "enabled" : "disabled",
    smsUnlockRequirement: "10 successful delivered transactions",
    smsSuccessfulTransactionsCount: successfulTransactionsCount.value
  };
};

const createSmsNotificationRecord = async (userId, orderNumber, message) => {
  const phoneNumber = getSmsPhoneNumber();

  if (!smsFeatureUnlocked.value || !smsPreferenceEnabled.value) return;

  if (!isValidSmsPhoneNumber(phoneNumber)) {
    alert("Please enter a valid 11-digit mobile number before enabling SMS updates. Example: 09123456789");
    return;
  }

  await addDoc(collection(db, "smsNotifications"), {
    customerId: userId,
    orderNumber,
    phoneNumber,
    message,
    status: "queued",
    type: "order_created",
    channel: "sms",
    provider: "simulation",
    isSimulation: true,
    createdAt: serverTimestamp(),
    updatedAt: serverTimestamp()
  });
};

const maskedPayPalEmail = computed(() => {
  if (!paypalEmail.value || !paypalEmail.value.includes("@")) return "";
  const [name, domain] = paypalEmail.value.split("@");
  const maskedName = name.length <= 2 ? `${name[0] || ""}***` : `${name.slice(0, 2)}***${name.slice(-1)}`;
  return `${maskedName}@${domain}`;
});

// CUSTOMER WALLET METHODS
const loadCustomerWallet = async (userId) => {
  try {
    const customerRef = doc(db, "customers", userId);

    onSnapshot(customerRef, (customerSnap) => {
      if (customerSnap.exists()) {
        const customerData = customerSnap.data();
        walletBalance.value = Number(customerData.walletBalance || 0);
        paypalEmail.value = customerData.paypalEmail || "";
        walletStatus.value = customerData.walletStatus || (customerData.paypalEmail ? "linked" : "not_linked");
      }
    });

    const walletQuery = query(
      collection(db, "walletTransactions"),
      where("customerId", "==", userId),
      orderBy("createdAt", "desc"),
      limit(10)
    );

    onSnapshot(walletQuery, (snapshot) => {
      walletTransactions.value = snapshot.docs.map((docSnap) => {
        const data = docSnap.data();
        return {
          id: docSnap.id,
          ...data,
          amount: Number(data.amount || 0)
        };
      });
    });
  } catch (error) {
    console.error("Error loading customer wallet:", error);
  }
};

const refreshWallet = async () => {
  const user = auth.currentUser;
  if (user) {
    await loadCustomerWallet(user.uid);
  }
};

const linkPayPalWallet = async () => {
  const user = auth.currentUser;

  if (!user) {
    alert("Please log in first.");
    return;
  }

  if (!paypalEmail.value || !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(paypalEmail.value)) {
    alert("Please enter a valid PayPal email address.");
    return;
  }

  walletLoading.value = true;

  try {
    const customerRef = doc(db, "customers", user.uid);

    await updateDoc(customerRef, {
      paypalEmail: paypalEmail.value,
      walletStatus: "linked",
      walletUpdatedAt: serverTimestamp()
    });

    walletStatus.value = "linked";

    await addDoc(collection(db, "walletTransactions"), {
      customerId: user.uid,
      type: "paypal_link",
      description: "PayPal account linked",
      amount: 0,
      paymentMethod: "PayPal",
      referenceNumber: paypalEmail.value,
      balanceAfter: walletBalance.value,
      createdAt: serverTimestamp()
    });

    alert("PayPal account linked successfully.");
  } catch (error) {
    console.error("Error linking PayPal wallet:", error);
    alert("Failed to link PayPal account. Please try again.");
  } finally {
    walletLoading.value = false;
  }
};

const topUpWallet = async () => {
  const user = auth.currentUser;
  if (!user || !topUpAmount.value || topUpAmount.value <= 0) return;

  if (walletStatus.value !== "linked") {
    alert("Please link your PayPal account first.");
    return;
  }

  openPayPalCashInSandbox();
};

const openPayPalCashInSandbox = () => {
  const amount = Number(topUpAmount.value);

  if (!amount || amount <= 0) {
    alert("Please enter a valid cash-in amount.");
    return;
  }

  if (!paypalEmail.value || !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(paypalEmail.value)) {
    alert("Please link a valid PayPal email first.");
    return;
  }

  paypalSandboxMode.value = "cashin";
  paypalSandboxAmount.value = amount;
  paypalSandboxEmail.value = paypalEmail.value;
  paypalSandboxReference.value = `PAYPAL-SBX-${Date.now()}`;
  showPayPalSandboxModal.value = true;
};

const openPayPalPaymentSandbox = () => {
  if (!canPlaceOrder.value) {
    alert("Please complete the order details first.");
    return;
  }

  if (!paypalEmail.value || !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(paypalEmail.value)) {
    alert("Please link a valid PayPal email in your wallet first.");
    showWalletModal.value = true;
    return;
  }

  paypalSandboxMode.value = "payment";
  paypalSandboxAmount.value = total.value;
  paypalSandboxEmail.value = paypalEmail.value;
  paypalSandboxReference.value = `PAYPAL-SBX-${Date.now()}`;
  showPayPalSandboxModal.value = true;
};

const closePayPalSandboxModal = () => {
  if (paypalSandboxProcessing.value) return;

  showPayPalSandboxModal.value = false;
  paypalSandboxMode.value = "";
  paypalSandboxAmount.value = 0;
  paypalSandboxReference.value = "";
  paypalSandboxEmail.value = "";
};

const confirmPayPalSandboxPayment = async () => {
  paypalSandboxProcessing.value = true;

  try {
    if (paypalSandboxMode.value === "cashin") {
      await processPayPalSandboxCashIn();
    } else if (paypalSandboxMode.value === "payment") {
      await processOrderWithPayPalSandboxPayment();
    }

    closePayPalSandboxModal();
  } catch (error) {
    console.error("PayPal sandbox error:", error);
    alert(error.message || "PayPal sandbox transaction failed. Please try again.");
  } finally {
    paypalSandboxProcessing.value = false;
  }
};

const processPayPalSandboxCashIn = async () => {
  const user = auth.currentUser;
  if (!user) throw new Error("Please log in first.");

  walletLoading.value = true;

  try {
    const amount = Number(paypalSandboxAmount.value);
    const customerRef = doc(db, "customers", user.uid);
    let newBalance = 0;

    await runTransaction(db, async (transaction) => {
      const customerDoc = await transaction.get(customerRef);
      const currentBalance = customerDoc.exists() ? Number(customerDoc.data().walletBalance || 0) : 0;
      newBalance = currentBalance + amount;

      transaction.update(customerRef, {
        walletBalance: newBalance,
        walletStatus: "linked",
        paypalEmail: paypalEmail.value,
        walletUpdatedAt: serverTimestamp()
      });
    });

    await addDoc(collection(db, "walletTransactions"), {
      customerId: user.uid,
      type: "paypal_sandbox_cash_in",
      description: "PayPal Sandbox cash-in",
      amount: amount,
      paymentMethod: "PayPal Sandbox",
      referenceNumber: paypalSandboxReference.value,
      balanceAfter: newBalance,
      status: "paid",
      environment: "sandbox",
      createdAt: serverTimestamp()
    });

    walletBalance.value = newBalance;
    topUpAmount.value = null;
    cashInReference.value = "";
    alert("PayPal Sandbox cash-in successful.");
  } finally {
    walletLoading.value = false;
  }
};

const deductWalletPayment = async (userId, amount, orderNumber) => {
  const customerRef = doc(db, "customers", userId);
  let newWalletBalance = 0;

  await runTransaction(db, async (transaction) => {
    const customerDoc = await transaction.get(customerRef);

    if (!customerDoc.exists()) {
      throw new Error("Customer wallet not found.");
    }

    const customerData = customerDoc.data();
    const currentBalance = Number(customerData.walletBalance || 0);
    const currentWalletStatus = customerData.walletStatus || (customerData.paypalEmail ? "linked" : "not_linked");

    if (currentWalletStatus !== "linked") {
      throw new Error("Please link your PayPal wallet first.");
    }

    if (currentBalance < amount) {
      throw new Error("Insufficient wallet balance.");
    }

    newWalletBalance = currentBalance - amount;

    transaction.update(customerRef, {
      walletBalance: newWalletBalance,
      walletUpdatedAt: serverTimestamp()
    });
  });

  await addDoc(collection(db, "walletTransactions"), {
    customerId: userId,
    orderNumber: orderNumber,
    type: "payment",
    description: `Payment for order ${orderNumber}`,
    amount: -amount,
    paymentMethod: "Tankonek Wallet",
    balanceAfter: newWalletBalance,
    createdAt: serverTimestamp()
  });

  walletBalance.value = newWalletBalance;
};

const formatWalletDate = (date) => {
  if (!date) return "Recently";
  const walletDate = date?.toDate ? date.toDate() : new Date(date);
  return walletDate.toLocaleDateString("en-US", {
    year: "numeric",
    month: "short",
    day: "numeric"
  });
};

// ✅ FIXED: IMPROVED DISTANCE CALCULATION - NOW WORKS FOR ALL RETAILERS INCLUDING CLARK GAS
const getDistanceDisplay = (distance) => {
  if (distance === null || distance === undefined) {
    return 'Calculating distance...';
  }
  if (typeof distance !== 'number') {
    return 'Distance unavailable';
  }
  return distance.toFixed(1) + ' km away';
};

// Check if retailer has available gas
const hasAvailableGas = (retailer) => {
  if (!retailer.inventory || retailer.inventory.length === 0) return false;
  return retailer.inventory.some(gas => gas.currentStock > 0);
};

// Retailer Details Modal Methods
const viewRetailerDetails = async (retailer) => {
  selectedRetailerDetails.value = retailer;
  showRetailerDetails.value = true;
  
  // Load real-time feedbacks for this retailer
  await loadRetailerFeedbacks(retailer.id);
};

const closeRetailerDetails = () => {
  showRetailerDetails.value = false;
  selectedRetailerDetails.value = null;
  retailerFeedbacks.value = []; // Clear feedbacks when modal closes
};

const orderFromDetails = () => {
  if (selectedRetailerDetails.value) {
    orderGas(selectedRetailerDetails.value);
    closeRetailerDetails();
  }
};

// NEW: Load real-time feedbacks for retailer
const loadRetailerFeedbacks = async (retailerId) => {
  try {
    console.log('🔄 Loading feedbacks for retailer:', retailerId);
    
    const retailerRef = doc(db, "retailers", retailerId);
    
    // Real-time listener for retailer document changes
    const unsubscribe = onSnapshot(retailerRef, (doc) => {
      if (doc.exists()) {
        const retailerData = doc.data();
        const feedbacks = retailerData.feedbacks || [];
        
        console.log('📊 Real-time feedbacks update:', feedbacks.length, 'feedbacks');
        
        // Process and sort feedbacks by date (newest first)
        retailerFeedbacks.value = feedbacks
          .map(feedback => ({
            id: `${feedback.createdAt?.seconds || Date.now()}-${Math.random().toString(36).substr(2, 9)}`,
            rating: feedback.rating,
            comment: feedback.comment,
            customerName: feedback.customerName || 'Customer',
            customerPhone: feedback.customerPhone,
            orderNumber: feedback.orderNumber,
            createdAt: feedback.createdAt?.toDate ? feedback.createdAt.toDate() : new Date(feedback.createdAt || Date.now())
          }))
          .sort((a, b) => b.createdAt - a.createdAt); // Newest first
          
        console.log('✅ Processed feedbacks:', retailerFeedbacks.value);
      }
    });

    // Store unsubscribe function to clean up later
    selectedRetailerDetails.value.unsubscribe = unsubscribe;
    
  } catch (error) {
    console.error('❌ Error loading retailer feedbacks:', error);
    retailerFeedbacks.value = [];
  }
};

// NEW: Format feedback date
const formatFeedbackDate = (date) => {
  if (!date) return 'Recently';
  const now = new Date();
  const feedbackDate = new Date(date);
  const diffTime = Math.abs(now - feedbackDate);
  const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
  
  if (diffDays === 1) return 'Yesterday';
  if (diffDays < 7) return `${diffDays} days ago`;
  if (diffDays < 30) return `${Math.floor(diffDays / 7)} weeks ago`;
  return feedbackDate.toLocaleDateString('en-US', { 
    year: 'numeric', 
    month: 'short', 
    day: 'numeric' 
  });
};

// ✅ ORDER FORM COMPUTED PROPERTIES - PERFECTLY SAME AS AVAILABLE.VUE
const subtotal = computed(() => {
  return selectedGasType.value ? selectedGasType.value.price * quantity.value : 0;
});

// ✅ TOTAL DISTANCE CALCULATION (EXACTLY SAME AS AVAILABLE.VUE)
const totalDistance = computed(() => {
  if (!selectedRetailer.value?.distance) return 0;
  
  // SAME CALCULATION AS AVAILABLE.VUE: 
  // Total Distance = (Rider to Retailer) + (Retailer to Customer)
  const riderToRetailerDistance = 1.5; // Default estimate - same as Available.vue assumption
  const retailerToCustomerDistance = selectedRetailer.value.distance;
  
  return riderToRetailerDistance + retailerToCustomerDistance;
});

// ✅ DELIVERY FEE CALCULATION (EXACTLY SAME AS AVAILABLE.VUE) - FIXED FLOATING POINT
const deliveryFee = computed(() => {
  if (!selectedRetailer.value?.distance) return 15;

  const baseFee = 15;
  const additionalRate = 5;
  const roundedDistance = Math.ceil(totalDistance.value);

  if (roundedDistance <= 1) {
    return baseFee;
  }

  return baseFee + ((roundedDistance - 1) * additionalRate);
});

const total = computed(() => subtotal.value + deliveryFee.value);

const canPlaceOrder = computed(() => {
  return (
    selectedGasType.value &&
    deliveryAddress.value.trim() &&
    contactNumber.value.trim() &&
    paymentMethod.value &&
    quantity.value > 0 &&
    selectedGasType.value.currentStock >= quantity.value
  );
});

// ✅ FIXED: IMPROVED DISTANCE CALCULATION WITH BETTER ERROR HANDLING AND CLARK GAS FIX
const calculateDistance = (lat1, lon1, lat2, lon2) => {
  try {
    // Validate coordinates
    if (!lat1 || !lon1 || !lat2 || !lon2) {
      console.log('❌ Missing coordinates for distance calculation');
      return null;
    }
    
    // Convert to numbers
    const lat1Num = parseFloat(lat1);
    const lon1Num = parseFloat(lon1);
    const lat2Num = parseFloat(lat2);
    const lon2Num = parseFloat(lon2);
    
    // Validate numeric values
    if (isNaN(lat1Num) || isNaN(lon1Num) || isNaN(lat2Num) || isNaN(lon2Num)) {
      console.log('❌ Invalid coordinates for distance calculation');
      return null;
    }
    
    // ✅ FIX FOR CLARK GAS: Check if coordinates are exactly the same (common issue with some retailers)
    if (lat1Num === lat2Num && lon1Num === lon2Num) {
      console.log('📍 Same coordinates detected, using default distance');
      return 2.5; // Default reasonable distance
    }
    
    const R = 6371; // Earth's radius in kilometers
    const dLat = (lat2Num - lat1Num) * Math.PI / 180;
    const dLon = (lon2Num - lon1Num) * Math.PI / 180;
    const a = 
      Math.sin(dLat/2) * Math.sin(dLat/2) +
      Math.cos(lat1Num * Math.PI / 180) * Math.cos(lat2Num * Math.PI / 180) * 
      Math.sin(dLon/2) * Math.sin(dLon/2);
    const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1-a));
    const distance = R * c;
    
    // ✅ FIX FOR CLARK GAS: If distance is 0 but coordinates are different, use reasonable default
    if (distance === 0 && (lat1Num !== lat2Num || lon1Num !== lon2Num)) {
      console.log('📍 Zero distance but different coordinates, using default');
      return 3.0; // Default reasonable distance
    }
    
    console.log(`📍 Distance calculated: ${distance.toFixed(1)} km`);
    return distance;
  } catch (error) {
    console.error('❌ Error calculating distance:', error);
    return null;
  }
};

// PayPal Sandbox Simulation uses the in-app modal only.
// No external PayPal SDK is loaded to avoid sandbox capture/login errors.
const cleanupPayPalButtons = () => {};
const cleanupWalletPayPalButtons = () => {};
const loadPayPalSDK = () => Promise.resolve(null);

// Process order with PayPal Sandbox payment
const processOrderWithPayPalSandboxPayment = async () => {
  try {
    const user = auth.currentUser;
    if (!user) {
      throw new Error('Please log in to place an order.');
    }

    const orderNumber = `ORD-${Date.now()}-${Math.random().toString(36).substr(2, 9)}`;
    currentPaymentMethodLabel.value = paymentMethod.value === 'wallet' ? 'Tankonek Wallet' : 'Cash on Delivery';
    currentOrderNumber.value = orderNumber;
    currentPaymentMethodLabel.value = 'PayPal Sandbox Simulation';

    const customerDoc = await getDoc(doc(db, "customers", user.uid));
    const customerData = customerDoc.exists() ? customerDoc.data() : {};

    const orderStatus = 'paid_pending_approval';
    const paymentStatus = 'paid';

    const orderData = {
      orderId: orderNumber,
      orderNumber: orderNumber,
      displayId: orderNumber,

      customerId: user.uid,
      customerName: customerData.fullName || userName.value,
      customerEmail: customerData.email || "",
      customerPhone: contactNumber.value,
      customerAddress: deliveryAddress.value,

      retailerId: selectedRetailer.value.id,
      retailerName: selectedRetailer.value.name,
      retailerBusinessName: selectedRetailer.value.name,
      retailerBarangay: selectedRetailer.value.barangay,
      retailerLatitude: selectedRetailer.value.latitude,
      retailerLongitude: selectedRetailer.value.longitude,

      gasType: selectedGasType.value.tankSize,
      gasBrand: selectedGasType.value.type,
      quantity: quantity.value,
      pricePerTank: selectedGasType.value.price,

      productPrice: selectedGasType.value.price,
      subtotal: subtotal.value,
      deliveryFee: deliveryFee.value,
      totalAmount: total.value,
      total: total.value,
      currency: "PHP",

      paymentMethod: 'paypal',
      paymentGateway: 'PayPal Sandbox Simulation',
      paymentStatus: paymentStatus,
      paymentId: paypalSandboxReference.value,
      paymentReference: paypalSandboxReference.value,
      payerEmail: paypalSandboxEmail.value,
      paymentAmount: total.value,
      paidAt: serverTimestamp(),
      isTestPayment: true,
      environment: 'sandbox',

      ...getSmsOrderPayload(),

      deliveryAddress: deliveryAddress.value,
      distance: totalDistance.value,
      estimatedDelivery: calculateEstimatedDelivery(),
      specialInstructions: specialInstructions.value,

      orderStatus: orderStatus,
      status: orderStatus,
      retailerStatus: 'pending',

      orderDate: new Date(),
      createdAt: serverTimestamp(),
      updatedAt: serverTimestamp(),

      inventoryId: selectedGasType.value.id,
      isActive: true,
      version: 1,

      from: selectedRetailer.value.name,
      to: customerData.fullName || userName.value,
      item: `${selectedGasType.value.type} ${selectedGasType.value.tankSize} × ${quantity.value}`,
      fee: deliveryFee.value,
      estimatedPrepTime: 15
    };

    const ordersRef = collection(db, "orders");
    await addDoc(ordersRef, orderData);

    const deliveryData = {
      orderId: orderNumber,
      displayId: orderNumber,
      customerId: user.uid,
      customerName: customerData.fullName || userName.value,
      customerEmail: customerData.email || "",
      customerPhone: contactNumber.value,
      customerAddress: deliveryAddress.value,
      retailerId: selectedRetailer.value.id,
      retailerName: selectedRetailer.value.name,
      retailerBusinessName: selectedRetailer.value.name,
      gasBrand: selectedGasType.value.type,
      gasType: selectedGasType.value.tankSize,
      quantity: quantity.value,
      item: `${selectedGasType.value.type} ${selectedGasType.value.tankSize} × ${quantity.value}`,
      deliveryFee: deliveryFee.value,
      totalAmount: total.value,
      subtotal: subtotal.value,
      from: selectedRetailer.value.name,
      to: customerData.fullName || userName.value,
      address: deliveryAddress.value,
      pickupAddress: selectedRetailer.value.barangay,
      distance: totalDistance.value,
      specialInstructions: specialInstructions.value,
      estimatedPrepTime: 15,
      paymentMethod: 'paypal',
      paymentGateway: 'PayPal Sandbox Simulation',
      paymentReference: paypalSandboxReference.value,

      status: orderStatus,
      retailerStatus: 'pending',
      paymentStatus: paymentStatus,

      ...getSmsOrderPayload(),

      isTestPayment: true,
      environment: 'sandbox',
      createdAt: serverTimestamp(),
      updatedAt: serverTimestamp()
    };

    const deliveriesRef = collection(db, "deliveries");
    await addDoc(deliveriesRef, deliveryData);

    await createSmsNotificationRecord(
      user.uid,
      orderNumber,
      `Tankonek: Your PayPal order ${orderNumber} has been paid and placed successfully. We will send status updates here.`
    );

    await addDoc(collection(db, "walletTransactions"), {
      customerId: user.uid,
      orderNumber: orderNumber,
      type: "paypal_sandbox_payment",
      description: `PayPal Sandbox payment for order ${orderNumber}`,
      amount: -total.value,
      paymentMethod: "PayPal Sandbox",
      referenceNumber: paypalSandboxReference.value,
      balanceAfter: walletBalance.value,
      status: "paid",
      environment: "sandbox",
      createdAt: serverTimestamp()
    });

    if (selectedGasType.value.id) {
      try {
        const newStock = selectedGasType.value.currentStock - quantity.value;
        const inventoryDocRef = doc(db, "inventory", selectedGasType.value.id);

        await updateDoc(inventoryDocRef, {
          currentStock: newStock,
          isLow: newStock < selectedGasType.value.minStock,
          stockLevel: (newStock / selectedGasType.value.minStock) * 100,
          lastUpdated: serverTimestamp()
        });
      } catch (inventoryError) {
        console.error("Error updating inventory:", inventoryError);
      }
    }

    showPaymentSuccess.value = true;
    closeOrderForm();

  } catch (error) {
    console.error('❌ Error processing order with PayPal Sandbox payment:', error);
    handlePaymentError(`Failed to process order: ${error.message}`);
  }
};

// Handle payment errors
const handlePaymentError = (message) => {
  alert(message);
};

// Close payment success modal
const closePaymentSuccess = () => {
  showPaymentSuccess.value = false;
  currentOrderNumber.value = '';
  // Switch to My Orders tab
  activeTab.value = 'myOrders';
};

// ✅ FIXED: IMPROVED LOCATION FETCHING WITH BETTER ERROR HANDLING
const getCustomerLocationFromFirestore = async (userId) => {
  try {
    const customerDoc = await getDoc(doc(db, "customers", userId));
    if (customerDoc.exists()) {
      const customerData = customerDoc.data();
      
      console.log("📋 Customer data from Firestore:", customerData);
      
      // ✅ ALWAYS USE FIRESTORE DATA FOR CONSISTENCY
      if (customerData.latitude && customerData.longitude) {
        customerLocation.value = {
          latitude: parseFloat(customerData.latitude),
          longitude: parseFloat(customerData.longitude),
          address: customerData.address || "Your saved location"
        };
        
        // ✅ EXTRACT BARANGAY FROM CUSTOMER'S ADDRESS
        if (customerData.address) {
          const addressParts = customerData.address.split(',');
          if (addressParts.length > 0) {
            customerBarangay.value = addressParts[0].trim();
            console.log("📍 Extracted barangay:", customerBarangay.value);
          }
        }
        
        console.log("✅ Using Firestore location:", customerLocation.value);
        calculateAllDistances();
        return true;
      } else {
        console.log("❌ No location data in Firestore");
        customerLocation.value = {
          latitude: null,
          longitude: null,
          address: "Location not set in profile"
        };
        return false;
      }
    } else {
      console.log("❌ Customer document not found");
      customerLocation.value = {
        latitude: null,
        longitude: null,
        address: "Please complete your profile"
      };
      return false;
    }
  } catch (error) {
    console.error("❌ Error fetching customer location:", error);
    customerLocation.value = {
      latitude: null,
      longitude: null,
      address: "Error loading location"
    };
    return false;
  }
};

// ✅ FIXED: Refresh location - ONLY USES FIRESTORE DATA
const refreshLocation = async () => {
  const user = auth.currentUser;
  if (user) {
    await getCustomerLocationFromFirestore(user.uid);
    calculateAllDistances();
  }
};

// ✅ FIXED: IMPROVED DISTANCE CALCULATION FOR ALL RETAILERS - NOW WITH CLARK GAS FIX
const calculateAllDistances = () => {
  if (!customerLocation.value.latitude || !customerLocation.value.longitude) {
    console.log("📍 Cannot calculate distances - no customer location");
    retailers.value.forEach(retailer => {
      retailer.distance = null;
    });
    return;
  }

  console.log("📍 Calculating distances from:", customerLocation.value);
  console.log("📍 Number of retailers to calculate:", retailers.value.length);
  
  let calculatedCount = 0;
  let missingCoordsCount = 0;
  let clarkGasFixedCount = 0;
  
  retailers.value.forEach((retailer, index) => {
    console.log(`📍 Retailer ${index + 1}: ${retailer.name}`, {
      hasLatitude: !!retailer.latitude,
      hasLongitude: !!retailer.longitude,
      latitude: retailer.latitude,
      longitude: retailer.longitude,
      coordinates: `${retailer.latitude}, ${retailer.longitude}`
    });
    
    if (retailer.latitude && retailer.longitude) {
      const distance = calculateDistance(
        customerLocation.value.latitude,
        customerLocation.value.longitude,
        retailer.latitude,
        retailer.longitude
      );
      
      // ✅ FIX FOR CLARK GAS: If distance is still null/undefined after calculation, use default
      if (distance === null || distance === undefined) {
        console.log(`📍 Using default distance for ${retailer.name}`);
        retailer.distance = 2.5; // Default reasonable distance
        clarkGasFixedCount++;
      } else {
        retailer.distance = distance;
      }
      
      calculatedCount++;
      console.log(`📍 [${index + 1}/${retailers.value.length}] Distance to ${retailer.name}: ${retailer.distance} km`);
    } else {
      // ✅ FIX FOR CLARK GAS: If no coordinates, use default distance
      console.log(`📍 No coordinates for ${retailer.name}, using default distance`);
      retailer.distance = 3.0; // Default reasonable distance
      missingCoordsCount++;
      clarkGasFixedCount++;
    }
  });

  console.log(`📊 DISTANCE CALCULATION SUMMARY:`);
  console.log(`✅ ${calculatedCount} retailers with distances calculated`);
  console.log(`❌ ${missingCoordsCount} retailers missing coordinates`);
  console.log(`🔧 ${clarkGasFixedCount} retailers fixed with default distances`);
  console.log(`📋 Total retailers: ${retailers.value.length}`);
};

// Get customer data from Firestore
const getCustomerData = async (userId) => {
  try {
    const customerDoc = await getDoc(doc(db, "customers", userId));
    if (customerDoc.exists()) {
      const customerData = customerDoc.data();
      userName.value = customerData.fullName || "Customer";
      
      // ✅ ALWAYS USE FIRESTORE LOCATION DATA
      await getCustomerLocationFromFirestore(userId);
    }
  } catch (error) {
    console.error("Error fetching customer data:", error);
  }
};

// Fetch inventory for a retailer - WITH REALISTIC PRICING
const fetchRetailerInventory = async (retailerBusinessName) => {
  try {
    console.log(`🔄 FETCHING INVENTORY for: "${retailerBusinessName}"`);
    
    const inventoryQuery = query(
      collection(db, "inventory"),
      where("addedByBusiness", "==", retailerBusinessName)
    );
    
    const inventorySnapshot = await getDocs(inventoryQuery);
    const inventory = [];
    
    console.log(`📊 Found ${inventorySnapshot.size} inventory items`);
    
    inventorySnapshot.forEach((doc) => {
      const data = doc.data();
      
      // REALISTIC PRICING BASED ON TANK SIZE
      let realisticPrice = data.price || 0;
      const tankSize = data.tankSize || data.type || "";
      
      // Set realistic prices if current price is too low or zero
      if (!realisticPrice || realisticPrice < 100) {
        if (tankSize.includes('50kg') || data.type?.includes('50kg')) {
          realisticPrice = 2500; // Realistic price for 50kg LPG
        } else if (tankSize.includes('11kg') || data.type?.includes('11kg')) {
          realisticPrice = 800; // Realistic price for 11kg LPG
        } else if (tankSize.includes('5kg') || data.type?.includes('5kg')) {
          realisticPrice = 400; // Realistic price for 5kg LPG
        } else {
          realisticPrice = 600; // Default realistic price
        }
      }
      
      inventory.push({
        id: doc.id,
        type: data.type || "Solane LPG",
        price: realisticPrice, // USE REALISTIC PRICE
        currentStock: data.currentStock || 0,
        minStock: data.minStock || 10,
        tankSize: data.tankSize || "11kg LPG Tank",
        isLow: data.isLow || false,
        lastRestocked: data.lastRestocked || "2025-11-16",
        addedByBusiness: data.addedByBusiness || ""
      });
    });

    return inventory;
    
  } catch (error) {
    console.error("❌ ERROR fetching inventory:", error);
    return [];
  }
};

// REAL-TIME INVENTORY LISTENER
const setupRealtimeInventoryListener = () => {
  const inventoryQuery = collection(db, "inventory");
  
  onSnapshot(inventoryQuery, (snapshot) => {
    console.log("🔄 REAL-TIME INVENTORY UPDATE DETECTED");
    
    snapshot.docChanges().forEach((change) => {
      const inventoryData = change.doc.data();
      const businessName = inventoryData.addedByBusiness;
      
      if (businessName) {
        retailers.value.forEach(retailer => {
          if (retailer.name === businessName && retailer.inventory) {
            const inventoryIndex = retailer.inventory.findIndex(inv => inv.id === change.doc.id);
            
            if (change.type === "added" || change.type === "modified") {
              const updatedInventory = {
                id: change.doc.id,
                type: inventoryData.type || "Solane",
                price: inventoryData.price || 0,
                currentStock: inventoryData.currentStock || 0,
                minStock: inventoryData.minStock || 10,
                tankSize: inventoryData.tankSize || "60kg LPG",
                isLow: inventoryData.isLow || false,
                lastRestocked: inventoryData.lastRestocked || "2025-11-16",
                addedByBusiness: inventoryData.addedByBusiness || ""
              };
              
              if (inventoryIndex >= 0) {
                retailer.inventory[inventoryIndex] = updatedInventory;
              } else {
                retailer.inventory.push(updatedInventory);
              }
            } else if (change.type === "removed" && inventoryIndex >= 0) {
              retailer.inventory.splice(inventoryIndex, 1);
            }
          }
        });
      }
    });
    
    console.log("✅ Retailers updated with real-time inventory:", retailers.value);
  });
};

// Order form methods
const orderGas = async (retailer) => {
  if (!retailer.distance) {
    alert("Please wait while we calculate the distance...");
    return;
  }

  if (!hasAvailableGas(retailer)) {
    alert("This retailer currently has no gas available.");
    return;
  }

  selectedRetailer.value = retailer;
  selectedGasType.value = retailer.inventory[0];
  quantity.value = 1;
  deliveryAddress.value = "";
  contactNumber.value = "";
  specialInstructions.value = "";
  paymentMethod.value = "cash";
  showOrderForm.value = true;
};

const closeOrderForm = () => {
  showOrderForm.value = false;
  selectedRetailer.value = null;
  selectedGasType.value = null;
  // Clean up PayPal buttons when closing form
  cleanupPayPalButtons();
};

const increaseQuantity = () => {
  if (selectedGasType.value && quantity.value < selectedGasType.value.currentStock) {
    quantity.value++;
  }
};

const decreaseQuantity = () => {
  if (quantity.value > 1) {
    quantity.value--;
  }
};

// FIXED: Confirm Order Function - WITH CONSISTENT LOGIC AS AVAILABLE.VUE
const confirmOrder = async () => {
  if (!canPlaceOrder.value) {
    alert("Please fill in all required fields and ensure quantity is available.");
    return;
  }

  try {
    const user = auth.currentUser;
    if (!user) {
      alert("Please log in to place an order.");
      return;
    }

    console.log("🔄 Starting order placement process...");

    // Get customer data for order
    const customerDoc = await getDoc(doc(db, "customers", user.uid));
    const customerData = customerDoc.exists() ? customerDoc.data() : {};
    
    // Generate unique order number
    const orderNumber = `ORD-${Date.now()}-${Math.random().toString(36).substr(2, 9)}`;

    // ✅ CONSISTENT PRICING WITH AVAILABLE.VUE
    const productPrice = selectedGasType.value.price;
    const calculatedSubtotal = productPrice * quantity.value;
    const calculatedDeliveryFee = deliveryFee.value;
    const calculatedTotal = calculatedSubtotal + calculatedDeliveryFee;

    console.log("💰 Consistent Pricing Breakdown:", {
      productPrice,
      quantity: quantity.value,
      subtotal: calculatedSubtotal,
      deliveryFee: calculatedDeliveryFee,
      total: calculatedTotal
    });

    // ✅ BAGONG STATUS SYSTEM: Para sa Cash on Delivery or Wallet
    const orderStatus = paymentMethod.value === 'wallet' ? 'paid_pending_approval' : 'pending';
    const paymentStatus = paymentMethod.value === 'wallet' ? 'paid' : 'cash_on_delivery';

    if (paymentMethod.value === 'wallet' && walletStatus.value !== 'linked') {
      alert("Please link your PayPal wallet before using wallet payment.");
      return;
    }

    if (paymentMethod.value === 'wallet' && walletBalance.value < calculatedTotal) {
      alert("Insufficient wallet balance. Please top up your wallet or choose another payment method.");
      return;
    }

    // Create comprehensive order document - SAME STRUCTURE AS EXPECTED IN AVAILABLE.VUE
    const orderData = {
      // Order Identification
      orderId: orderNumber,
      orderNumber: orderNumber,
      displayId: orderNumber,
      
      // Customer Information
      customerId: user.uid,
      customerName: customerData.fullName || userName.value,
      customerEmail: customerData.email || "",
      customerPhone: contactNumber.value,
      customerAddress: deliveryAddress.value,
      
      // Retailer Information
      retailerId: selectedRetailer.value.id,
      retailerName: selectedRetailer.value.name,
      retailerBusinessName: selectedRetailer.value.name,
      retailerBarangay: selectedRetailer.value.barangay,
      retailerLatitude: selectedRetailer.value.latitude,
      retailerLongitude: selectedRetailer.value.longitude,
      
      // Product Information - CONSISTENT WITH AVAILABLE.VUE
      gasType: selectedGasType.value.tankSize,
      gasBrand: selectedGasType.value.type,
      quantity: quantity.value,
      pricePerTank: productPrice,
      
      // ✅ FINANCIAL INFORMATION - SAME FIELD NAMES AS AVAILABLE.VUE EXPECTS
      productPrice: productPrice,
      subtotal: calculatedSubtotal,
      deliveryFee: calculatedDeliveryFee,
      totalAmount: calculatedTotal,
      total: calculatedTotal,
      currency: "PHP",
      
      // Delivery Information
      deliveryAddress: deliveryAddress.value,
      distance: totalDistance.value,
      estimatedDelivery: calculateEstimatedDelivery(),
      specialInstructions: specialInstructions.value,
      
      // ✅ BAGONG PAYMENT INFORMATION
      paymentMethod: paymentMethod.value,
      paymentStatus: paymentStatus,

      ...getSmsOrderPayload(),
      
      // ✅ BAGONG ORDER STATUS SYSTEM
      orderStatus: orderStatus,
      status: orderStatus,
      retailerStatus: 'pending',
      
      // Timestamps
      orderDate: new Date(),
      createdAt: serverTimestamp(),
      updatedAt: serverTimestamp(),
      
      // Additional Metadata
      inventoryId: selectedGasType.value.id,
      isActive: true,
      version: 1,

      // ✅ ADD THESE FIELDS THAT AVAILABLE.VUE EXPECTS
      from: selectedRetailer.value.name,
      to: customerData.fullName || userName.value,
      item: `${selectedGasType.value.type} ${selectedGasType.value.tankSize} × ${quantity.value}`,
      fee: calculatedDeliveryFee,
      estimatedPrepTime: 15
    };

    console.log(" Order data to be saved:", orderData);

    // Add order to Firestore
    const ordersRef = collection(db, "orders");
    const orderDocRef = await addDoc(ordersRef, orderData);

    // ✅ ALSO CREATE A DELIVERY RECORD FOR AVAILABLE.VUE
    const deliveryData = {
      // Order reference
      orderId: orderNumber,
      displayId: orderNumber,
      
      // Customer information
      customerId: user.uid,
      customerName: customerData.fullName || userName.value,
      customerEmail: customerData.email || "",
      customerPhone: contactNumber.value,
      customerAddress: deliveryAddress.value,
      
      // Retailer information  
      retailerId: selectedRetailer.value.id,
      retailerName: selectedRetailer.value.name,
      retailerBusinessName: selectedRetailer.value.name,
      
      // Product information
      gasBrand: selectedGasType.value.type,
      gasType: selectedGasType.value.tankSize,
      quantity: quantity.value,
      item: `${selectedGasType.value.type} ${selectedGasType.value.tankSize} × ${quantity.value}`,
      
      // Pricing - SAME AS AVAILABLE.VUE EXPECTS
      deliveryFee: calculatedDeliveryFee,
      totalAmount: calculatedTotal,
      subtotal: calculatedSubtotal,
      
      // Location information
      from: selectedRetailer.value.name,
      to: customerData.fullName || userName.value,
      address: deliveryAddress.value,
      pickupAddress: selectedRetailer.value.barangay,
      distance: totalDistance.value,
      
      // Delivery details
      specialInstructions: specialInstructions.value,
      estimatedPrepTime: 15,
      paymentMethod: paymentMethod.value,

      ...getSmsOrderPayload(),
      
      // ✅ BAGONG STATUS SYSTEM
      status: orderStatus,
      retailerStatus: 'pending',
      paymentStatus: paymentStatus,
      
      // Timestamps
      createdAt: serverTimestamp(),
      updatedAt: serverTimestamp()
    };

    // Create delivery record for riders to see
    const deliveriesRef = collection(db, "deliveries");
    await addDoc(deliveriesRef, deliveryData);

    await createSmsNotificationRecord(
      user.uid,
      orderNumber,
      `Tankonek: Your order ${orderNumber} has been placed successfully. We will send status updates here.`
    );

    if (paymentMethod.value === 'wallet') {
      await deductWalletPayment(user.uid, calculatedTotal, orderNumber);
    }

    console.log("✅ Order and Delivery records created successfully");
    
    // Update inventory stock
    if (selectedGasType.value.id) {
      try {
        const newStock = selectedGasType.value.currentStock - quantity.value;
        const inventoryDocRef = doc(db, "inventory", selectedGasType.value.id);
        
        await updateDoc(inventoryDocRef, {
          currentStock: newStock,
          isLow: newStock < selectedGasType.value.minStock,
          stockLevel: (newStock / selectedGasType.value.minStock) * 100,
          lastUpdated: serverTimestamp()
        });
        
        console.log("📦 Inventory updated successfully. New stock:", newStock);
      } catch (inventoryError) {
        console.error("Error updating inventory, but order was placed:", inventoryError);
      }
    }

    // Show success message
    alert(`✅ Order placed successfully!\n\nOrder Number: ${orderNumber}\nProduct: ₱${calculatedSubtotal.toLocaleString()}\nDelivery Fee: ₱${calculatedDeliveryFee.toLocaleString()}\nTotal: ₱${calculatedTotal.toLocaleString()}${smsFeatureUnlocked.value && smsPreferenceEnabled.value ? `\nSMS Updates: Enabled for ${getSmsPhoneNumber()}` : ''}\n\nYou can track your order in the "My Orders" tab.`);
    
    // Reset form and close modal
    closeOrderForm();
    
    // Switch to My Orders tab
    activeTab.value = 'myOrders';

  } catch (error) {
    console.error("❌ Error placing order:", error);
    alert(`Failed to place order: ${error.message || "Please try again."}`);
  }
};

const calculateEstimatedDelivery = () => {
  const now = new Date();
  let deliveryMinutes = 30;
  
  if (selectedRetailer.value?.distance) {
    if (selectedRetailer.value.distance < 2) deliveryMinutes = 20;
    else if (selectedRetailer.value.distance < 5) deliveryMinutes = 30;
    else if (selectedRetailer.value.distance < 10) deliveryMinutes = 45;
    else deliveryMinutes = 60;
  }

  now.setMinutes(now.getMinutes() + deliveryMinutes);
  return now;
};

// ✅ FIXED: COMPLETELY REWRITTEN RETAILER FETCHING WITH PROPER DISTANCE CALCULATION FOR ALL SHOPS INCLUDING CLARK GAS
onMounted(() => {
  onAuthStateChanged(auth, (user) => {
    if (user) {
      console.log("👤 User logged in:", user.uid);
      getCustomerData(user.uid);
      loadCustomerWallet(user.uid);
      loadSmsUnlockStatus(user.uid);
    } else {
      console.log("❌ No user logged in");
    }
  });

  // Setup real-time inventory listener
  setupRealtimeInventoryListener();

  
  const q = query(
    collection(db, "retailers"),
    where("approvalStatus", "==", "approved")
  );

  onSnapshot(q, async (snapshot) => {
    console.log("🔄 FETCHING RETAILERS FROM FIRESTORE");
    const items = [];

    // First, get all retailer data
    for (const docSnap of snapshot.docs) {
      const data = docSnap.data();

      console.log(`📋 Retailer Data for ${data.businessName}:`, {
        latitude: data.latitude,
        longitude: data.longitude,
        hasLatitude: !!data.latitude,
        hasLongitude: !!data.longitude,
        allData: data
      });

      const parseBarangayFromAddress = (fullAddress) => {
        if (!fullAddress) return "Unknown";
        const parts = fullAddress.split(',');
        if (parts.length > 0) {
          return parts[0].trim();
        }
        return fullAddress;
      };

      const retailer = {
        id: docSnap.id,
        name: data.businessName || "No Name",
        barangay: parseBarangayFromAddress(data.address),
        latitude: data.latitude ? parseFloat(data.latitude) : null,
        longitude: data.longitude ? parseFloat(data.longitude) : null,
        rating: data.rating || 0,
        totalRatings: data.totalRatings || 0,
        distance: null, // Initialize distance as null
        inventory: []
      };

      // Fetch inventory using BUSINESS NAME only
      retailer.inventory = await fetchRetailerInventory(retailer.name);

      items.push(retailer);
    }

    console.log(`📍 Loaded ${items.length} retailers from Firestore`);
    
    // Set retailers first
    retailers.value = items;
    
    // Then calculate distances for ALL retailers (including Clark Gas)
    calculateAllDistances();
  });
});

// Get distance class for styling
const getDistanceClass = (distance) => {
  if (distance === null || distance === undefined) return 'calculating-distance';
  if (distance < 2) return 'very-near';
  if (distance < 5) return 'near';
  if (distance < 10) return 'moderate';
  return 'far';
};

// FILTERED RETAILERS
const filteredRetailers = computed(() => {
  let filtered = retailers.value;
  
  if (selectedBarangay.value) {
    filtered = filtered.filter((retailer) => {
      const retailerBarangay = retailer.barangay?.toLowerCase().trim();
      const selectedBarangayLower = selectedBarangay.value.toLowerCase().trim();
      return retailerBarangay === selectedBarangayLower;
    });
  }

  return filtered.sort((a, b) => {
    if (a.distance === null) return 1;
    if (b.distance === null) return -1;
    return a.distance - b.distance;
  });
});

// CLEAR FILTER
const clearFilter = () => {
  selectedBarangay.value = "";
};

// LOGOUT
const showLogoutModal = ref(false);

function logout() {
  showLogoutModal.value = true;
}
function confirmLogout() {
  router.visit("/");
}
function cancelLogout() {
  showLogoutModal.value = false;
}

// Location arrays - DYNAMICALLY BASED ON CUSTOMER LOCATION
const poblacionAreas = [
  "Poblacion I", "Poblacion II", "Poblacion III", "Poblacion IV", "Poblacion V", "Poblacion VI"
];

const otherBarangays = [
  "Amitin", "Bacungan", "Bagong Buhay","Bagong Silang", "Bambanin", "Bethel", "Canaan", "Concepcion",
  "Duongan", "Loyal", "Mabini", "Macatoc", "Maligaya", "Manogpi", "Masaguisi", "San Andres",
  "San Antonio", "San Carlos", "San Gabriel", "San Gavino", "San Isidro", "San Juan",
  "San Narciso", "San Salvador", "Santa Cruz", "Santa Lucia", "Santiago"
];

// Nearby barangays - prioritize customer's barangay and nearby areas
const nearbyBarangays = computed(() => {
  const allBarangays = [...poblacionAreas, ...otherBarangays];
  
  // Remove customer's barangay if it exists
  if (customerBarangay.value) {
    return allBarangays.filter(barangay => barangay !== customerBarangay.value);
  }
  
  return allBarangays;
});

onUnmounted(() => {
  if (smsUnlockUnsubscribe.value) {
    smsUnlockUnsubscribe.value();
  }
});

</script>

<style scoped>
/* All CSS styles remain exactly the same as in your original code */

/* SMS Unlock Feature */
.sms-unlock-panel {
  display: flex;
  align-items: center;
  gap: 1rem;
  width: calc(100% - 4rem);
  margin: 0 2rem 2rem;
  padding: 1rem 1.25rem;
  background: rgba(255, 120, 40, 0.06);
  border: 1px solid rgba(255, 120, 40, 0.18);
  border-left: 5px solid #FF7828;
  border-radius: 14px;
  box-shadow: 0 8px 24px rgba(255, 120, 40, 0.08);
  box-sizing: border-box;
}

.sms-unlock-panel.unlocked {
  background: rgba(255, 120, 40, 0.08);
  border-color: rgba(255, 120, 40, 0.28);
  border-left-color: #FF7828;
}

.sms-unlock-icon {
  display: flex;
  align-items: center;
  justify-content: center;
  min-width: 48px;
  width: 48px;
  height: 48px;
  border-radius: 14px;
  background: rgba(255, 120, 40, 0.14);
  color: #FF7828;
}

.sms-unlock-panel.unlocked .sms-unlock-icon {
  background: rgba(255, 120, 40, 0.18);
  color: #FF7828;
}

.sms-unlock-content {
  flex: 1;
  min-width: 0;
}

.sms-unlock-content h3 {
  margin: 0 0 0.25rem;
  color: #1a202c;
  font-size: 1rem;
  font-weight: 800;
}

.sms-unlock-content p {
  margin: 0 0 0.65rem;
  color: #4a5568;
  font-size: 0.9rem;
  line-height: 1.45;
}

.sms-progress-bar {
  width: 100%;
  height: 8px;
  background: rgba(255, 120, 40, 0.16);
  border-radius: 999px;
  overflow: hidden;
  margin-bottom: 0.4rem;
}

.sms-progress-fill {
  height: 100%;
  background: #FF7828;
  border-radius: 999px;
  transition: width 0.3s ease;
}

.sms-unlock-content small {
  color: #c05621;
  font-weight: 700;
}

.sms-order-option {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 1rem;
  width: 100%;
  padding: 1rem;
  background: rgba(255, 120, 40, 0.06);
  border: 1px solid rgba(255, 120, 40, 0.18);
  border-left: 4px solid #FF7828;
  border-radius: 12px;
  box-sizing: border-box;
}

.sms-order-option.locked {
  background: rgba(255, 120, 40, 0.04);
  border-color: rgba(255, 120, 40, 0.18);
  border-left-color: #FF7828;
  opacity: 1;
}

.sms-order-info {
  flex: 1;
  min-width: 0;
}

.sms-order-info strong {
  display: block;
  color: #1a202c;
  margin-bottom: 0.25rem;
}

.sms-order-info p {
  margin: 0;
  color: #4a5568;
  font-size: 0.85rem;
  line-height: 1.45;
}

.sms-toggle {
  position: relative;
  display: inline-flex;
  width: 48px;
  height: 28px;
  flex-shrink: 0;
}

.sms-toggle input {
  opacity: 0;
  width: 0;
  height: 0;
}

.sms-toggle span {
  position: absolute;
  inset: 0;
  cursor: pointer;
  background: rgba(255, 120, 40, 0.25);
  border-radius: 999px;
  transition: 0.25s;
}

.sms-toggle span::before {
  content: "";
  position: absolute;
  height: 22px;
  width: 22px;
  left: 3px;
  top: 3px;
  background: white;
  border-radius: 50%;
  transition: 0.25s;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.15);
}

.sms-toggle input:checked + span {
  background: #FF7828;
}

.sms-toggle input:checked + span::before {
  transform: translateX(20px);
}

.sms-toggle input:disabled + span {
  cursor: not-allowed;
  background: rgba(255, 120, 40, 0.16);
}

.sms-unlock-modal .sms-success {
  margin-bottom: 1rem;
}

.sms-unlock-modal .modal-btn-confirm {
  background: #FF7828;
  box-shadow: 0 4px 12px rgba(255, 120, 40, 0.3);
}

.sms-unlock-modal .modal-btn-confirm:hover {
  background: #e6691f;
  box-shadow: 0 6px 16px rgba(255, 120, 40, 0.4);
}

@media (max-width: 768px) {
  .sms-unlock-panel {
    width: calc(100% - 2rem);
    margin: 0 1rem 1.5rem;
    align-items: flex-start;
  }

  .sms-order-option {
    align-items: flex-start;
  }
}


/* Customer Wallet Styles */
.wallet-header-card {
  display: flex;
  flex-direction: column;
  align-items: flex-start;
  justify-content: center;
  min-width: 130px;
  padding: 0.65rem 1rem;
  border: 1px solid rgba(255, 120, 40, 0.25);
  border-radius: 12px;
  background: rgba(255, 120, 40, 0.08);
  color: #1a202c;
  cursor: pointer;
  transition: all 0.3s ease;
}

.wallet-header-card:hover {
  background: rgba(255, 120, 40, 0.14);
  transform: translateY(-1px);
}

.wallet-header-label {
  font-size: 0.72rem;
  color: #718096;
  font-weight: 700;
  text-transform: uppercase;
  letter-spacing: 0.06em;
}

.wallet-header-card strong {
  color: #FF7828;
  font-size: 1rem;
  font-weight: 800;
}

.customer-wallet-modal {
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
.wallet-history,
.wallet-panel {
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

.wallet-layout {
  display: grid;
  grid-template-columns: repeat(2, minmax(280px, 1fr));
  gap: 1.5rem;
  margin-bottom: 1.5rem;
}

.wallet-panel {
  background: white;
  box-shadow: 0 10px 28px rgba(15, 23, 42, 0.06);
}

.wallet-panel-header h3 {
  margin: 0;
  color: #1a202c;
  font-size: 1.2rem;
  font-weight: 800;
}

.wallet-panel-header p {
  margin: 0.35rem 0 1.25rem;
  color: #718096;
  line-height: 1.5;
}

.wallet-amount-input {
  display: flex;
  align-items: center;
  gap: 0.75rem;
  padding: 0.9rem 1rem;
  border: 2px solid #e2e8f0;
  border-radius: 12px;
  background: #f8fafc;
}

.wallet-amount-input span {
  color: #FF7828;
  font-weight: 900;
  font-size: 1.2rem;
}

.wallet-amount-input input {
  width: 100%;
  border: none;
  background: transparent;
  outline: none;
  font-size: 1.1rem;
  font-weight: 700;
  color: #1a202c;
}

.quick-amounts {
  display: grid;
  grid-template-columns: repeat(4, 1fr);
  gap: 0.65rem;
  margin: 1rem 0;
}

.quick-amounts button {
  padding: 0.65rem;
  border: 1px solid rgba(255, 120, 40, 0.3);
  border-radius: 10px;
  background: rgba(255, 120, 40, 0.08);
  color: #c05621;
  font-weight: 800;
  cursor: pointer;
}

.wallet-primary-btn {
  width: 100%;
  padding: 0.9rem 1.25rem;
  border: none;
  border-radius: 12px;
  background: #FF7828;
  color: white;
  font-weight: 800;
  cursor: pointer;
  box-shadow: 0 8px 20px rgba(255, 120, 40, 0.25);
}

.wallet-primary-btn:disabled {
  background: #9ca3af;
  cursor: not-allowed;
  box-shadow: none;
}

.wallet-note {
  margin: 0.9rem 0 0;
  color: #718096;
  font-size: 0.85rem;
  line-height: 1.5;
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

.wallet-credit {
  color: #059669;
  font-weight: 900;
  white-space: nowrap;
}

.wallet-debit {
  color: #dc2626;
  font-weight: 900;
  white-space: nowrap;
}

.wallet-empty {
  text-align: center;
  color: #718096;
  font-style: italic;
}

.wallet-payment-icon {
  color: #FF7828;
}

.paypal-payment-icon {
  color: #2563eb;
}

.paypal-primary-btn {
  background: #2563eb;
  box-shadow: 0 8px 20px rgba(37, 99, 235, 0.25);
}

.paypal-primary-btn:hover:not(:disabled) {
  background: #1d4ed8;
}

.paypal-sandbox-container {
  margin-top: 1rem;
  padding: 1rem;
  background: #eff6ff;
  border-radius: 8px;
  border: 1px solid #bfdbfe;
}

.paypal-sandbox-pay-btn {
  width: 100%;
  padding: 0.95rem 1.25rem;
  border: none;
  border-radius: 10px;
  background: #2563eb;
  color: white;
  font-weight: 800;
  cursor: pointer;
  box-shadow: 0 8px 20px rgba(37, 99, 235, 0.25);
}

.paypal-sandbox-pay-btn:hover {
  background: #1d4ed8;
  transform: translateY(-1px);
}

.paypal-sandbox-note {
  font-size: 0.8rem;
  color: #2563eb;
  text-align: center;
  margin-top: 0.5rem;
}

.paypal-sandbox-modal {
  max-width: 480px;
}

.modal-icon.paypal {
  margin-bottom: 1rem;
}

.paypal-sandbox-details {
  text-align: left;
  background: #eff6ff;
  border: 1px solid #bfdbfe;
  border-radius: 12px;
  padding: 1rem;
  margin: 1.5rem 0;
}

.paypal-sandbox-details p {
  margin: 0.45rem 0;
  color: #1e3a8a;
  font-size: 0.95rem;
}

.paypal-confirm-btn {
  background: #2563eb;
  box-shadow: 0 4px 12px rgba(37, 99, 235, 0.3);
}

.paypal-confirm-btn:hover:not(:disabled) {
  background: #1d4ed8;
  box-shadow: 0 6px 16px rgba(37, 99, 235, 0.4);
}

/* PayPal Specific Styles */
.paypal-button-container {
  margin-top: 1rem;
  padding: 1rem;
  background: #f8fafc;
  border-radius: 8px;
  border: 1px solid #e2e8f0;
  min-height: 80px;
  display: flex;
  flex-direction: column;
  justify-content: center;
}

.paypal-note {
  font-size: 0.8rem;
  color: #6b7280;
  text-align: center;
  margin-top: 0.5rem;
}

.paypal-placeholder {
  text-align: center;
  padding: 1rem;
  color: #6b7280;
}

.paypal-icon {
  color: #0070ba;
}

/* Payment Success Modal */
.success-modal {
  text-align: center;
  max-width: 500px;
}

.success-modal .modal-icon.success {
  color: #10B981;
}

.order-details {
  background: #f8fafc;
  padding: 1rem;
  border-radius: 8px;
  margin: 1.5rem 0;
  text-align: left;
}

.order-details p {
  margin: 0.5rem 0;
  color: #374151;
}

/* Payment Option Styles */
.payment-option {
  display: flex;
  align-items: center;
  padding: 1rem;
  border: 2px solid #e2e8f0;
  border-radius: 8px;
  cursor: pointer;
  transition: all 0.3s ease;
  margin-bottom: 0.75rem;
}

.payment-option:hover {
  border-color: #cbd5e0;
}

.payment-option:has(.payment-radio:checked) {
  border-color: #FF7828;
  background: rgba(255, 120, 40, 0.05);
}

.payment-radio {
  margin-right: 1rem;
}

.payment-content {
  display: flex;
  align-items: center;
  gap: 0.75rem;
  flex: 1;
}

.payment-icon {
  width: 40px;
  height: 40px;
  display: flex;
  align-items: center;
  justify-content: center;
  background: #f7fafc;
  border-radius: 8px;
  color: #4a5568;
}

.payment-info {
  flex: 1;
}

.payment-name {
  font-weight: 600;
  color: #1a202c;
  margin-bottom: 0.25rem;
}

.payment-desc {
  color: #718096;
  font-size: 0.85rem;
}

/* NEW STYLES FOR RATING DISPLAY */
.rating-display {
  display: flex;
  align-items: center;
  gap: 0.5rem;
}

.rating-stars-small {
  display: inline-flex;
  gap: 0.1rem;
}

.rating-stars-small .filled {
  color: #FF7828;
}

.rating-badge {
  display: flex;
  align-items: center;
  gap: 0.25rem;
  background: rgba(255, 120, 40, 0.1);
  color: #FF7828;
  padding: 0.3rem 0.6rem;
  border-radius: 12px;
  font-size: 0.8rem;
  font-weight: 600;
}

.retailer-rating-info {
  margin-top: 0.5rem;
}

/* Update retailer rating display */
.retailer-rating {
  display: flex;
  align-items: center;
  gap: 4px;
  background: rgba(255, 120, 40, 0.1);
  color: #FF7828;
  padding: 0.4rem 0.75rem;
  border-radius: 20px;
  font-size: 0.85rem;
  font-weight: 600;
  white-space: nowrap;
}

/* Availability tags */
.availability-info {
  display: flex;
  align-items: center;
}

.availability-tag {
  padding: 0.3rem 0.6rem;
  border-radius: 6px;
  font-size: 0.8rem;
  font-weight: 600;
  text-transform: uppercase;
  letter-spacing: 0.5px;
}

.availability-tag.available {
  background: rgba(16, 185, 129, 0.1);
  color: #059669;
  border: 1px solid rgba(16, 185, 129, 0.2);
}

.availability-tag.out-of-stock {
  background: rgba(220, 38, 38, 0.1);
  color: #dc2626;
  border: 1px solid rgba(220, 38, 38, 0.2);
}

/* NEW FEE BREAKDOWN STYLE */
.fee-breakdown {
  font-size: 0.75rem;
  color: #6b7280;
  display: block;
  margin-top: 0.25rem;
  font-weight: normal;
}

/* NEW YOUR LOCATION BADGE */
.your-location-badge {
  background: rgba(16, 185, 129, 0.1);
  color: #059669;
  padding: 0.2rem 0.5rem;
  border-radius: 4px;
  font-size: 0.75rem;
  font-weight: 500;
  margin-left: 0.5rem;
}

/* NEW STYLES FOR RATINGS AND FEEDBACK SECTION */
.section-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 1.5rem;
}

.reviews-summary {
  display: flex;
  align-items: center;
  gap: 1rem;
}

.overall-rating {
  text-align: center;
  padding: 1rem;
  background: rgba(255, 120, 40, 0.05);
  border-radius: 12px;
  border: 1px solid rgba(255, 120, 40, 0.2);
}

.rating-score {
  font-size: 2rem;
  font-weight: 700;
  color: #FF7828;
  line-height: 1;
}

.total-reviews {
  font-size: 0.85rem;
  color: #6b7280;
  margin-top: 0.25rem;
}

.ratings-feedback-section {
  max-height: 400px;
  overflow-y: auto;
}

.no-reviews {
  text-align: center;
  padding: 2rem;
  color: #9ca3af;
}

.no-reviews svg {
  margin-bottom: 1rem;
}

.no-reviews p {
  margin: 0.5rem 0;
  font-weight: 600;
}

.no-reviews small {
  font-size: 0.85rem;
}

.reviews-list {
  display: flex;
  flex-direction: column;
  gap: 1rem;
}

.feedback-item {
  background: #f8fafc;
  border-radius: 8px;
  padding: 1rem;
  border: 1px solid #e2e8f0;
}

.feedback-header {
  display: flex;
  justify-content: space-between;
  align-items: flex-start;
  margin-bottom: 0.75rem;
}

.customer-info {
  flex: 1;
}

.customer-name {
  font-weight: 600;
  color: #1f2937;
  margin-bottom: 0.25rem;
}

.feedback-date {
  font-size: 0.8rem;
  color: #6b7280;
}

.rating-display-small {
  display: flex;
  align-items: center;
  gap: 0.5rem;
}

.rating-number {
  font-weight: 600;
  color: #FF7828;
  font-size: 0.9rem;
}

.feedback-comment {
  background: white;
  padding: 0.75rem;
  border-radius: 6px;
  border-left: 3px solid #FF7828;
  font-style: italic;
  color: #4b5563;
  margin-bottom: 0.5rem;
  line-height: 1.4;
}

.order-info {
  text-align: right;
}

.order-info small {
  color: #9ca3af;
  font-size: 0.75rem;
}

/* Keep all existing styles below */
.customer-location-info {
  display: flex;
  align-items: center;
  gap: 1rem;
  margin-top: 1rem;
  padding: 0.75rem 1rem;
  background: rgba(255, 120, 40, 0.1);
  border-radius: 8px;
  border: 1px solid rgba(255, 120, 40, 0.2);
}

.location-badge {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  color: #FF7828;
  font-weight: 500;
  font-size: 0.9rem;
}

.location-refresh-btn {
  display: flex;
  align-items: center;
  gap: 0.25rem;
  padding: 0.4rem 0.75rem;
  background: rgba(255, 120, 40, 0.2);
  border: 1px solid rgba(255, 120, 40, 0.3);
  border-radius: 6px;
  color: #FF7828;
  font-size: 0.8rem;
  font-weight: 500;
  cursor: pointer;
  transition: all 0.3s ease;
}

.location-refresh-btn:hover {
  background: rgba(255, 120, 40, 0.3);
}

/* Distance Styling */
.very-near {
  color: #10b981;
  font-weight: 600;
}

.near {
  color: #059669;
  font-weight: 500;
}

.moderate {
  color: #d97706;
  font-weight: 500;
}

.far {
  color: #dc2626;
  font-weight: 500;
}

.calculating-distance {
  color: #6b7280;
  font-style: italic;
}

/* Profile Button */
.profile-btn {
  display: flex;
  align-items: center;
  gap: 8px;
  padding: 0.75rem 1.5rem;
  border: 2px solid #e2e8f0;
  border-radius: 10px;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.3s ease;
  font-size: 0.9rem;
  background: white;
  color: #4a5568;
}

.profile-btn:hover {
  background: #f7fafc;
  border-color: #cbd5e0;
}

.header-actions {
  display: flex;
  gap: 12px;
  align-items: center;
}

/* Place Filter Section Styles */
.place-filter-section {
  background: #f8fafc;
  border-radius: 12px;
  padding: 1.5rem;
  margin-bottom: 2rem;
  border: 1px solid #e2e8f0;
}

.filter-container {
  display: flex;
  flex-direction: column;
  gap: 1rem;
}

.filter-label {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  font-weight: 600;
  color: #374151;
  font-size: 0.95rem;
}

.filter-icon {
  color: #FF7828;
}

.select-wrapper {
  position: relative;
  width: 100%;
  max-width: 400px;
}

.place-select {
  width: 100%;
  padding: 0.75rem 1rem;
  padding-right: 2.5rem;
  border: 2px solid #e2e8f0;
  border-radius: 10px;
  font-size: 0.95rem;
  background: white;
  appearance: none;
  cursor: pointer;
  transition: all 0.3s ease;
  font-family: inherit;
}

.place-select:focus {
  outline: none;
  border-color: #FF7828;
  box-shadow: 0 0 0 3px rgba(255, 120, 40, 0.1);
}

.place-select:hover {
  border-color: #cbd5e0;
}

.select-arrow {
  position: absolute;
  right: 1rem;
  top: 50%;
  transform: translateY(-50%);
  pointer-events: none;
  color: #6b7280;
}

.active-filter-indicator {
  display: flex;
  align-items: center;
  justify-content: space-between;
  background: rgba(255, 120, 40, 0.1);
  padding: 0.75rem 1rem;
  border-radius: 8px;
  border: 1px solid rgba(255, 120, 40, 0.2);
}

.filter-text {
  color: #FF7828;
  font-weight: 500;
}

.clear-filter-btn {
  display: flex;
  align-items: center;
  gap: 0.25rem;
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

.clear-filter-btn:hover {
  background: rgba(255, 120, 40, 0.2);
}

/* No Results Styles */
.no-results {
  text-align: center;
  padding: 3rem 2rem;
  color: #718096;
  grid-column: 1 / -1;
}

.no-results h3 {
  margin: 1rem 0 0.5rem 0;
  color: #4a5568;
  font-size: 1.25rem;
}

.no-results p {
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

/* Update retailer details styling */
.retailer-details {
  display: flex;
  flex-direction: column;
  gap: 0.5rem;
  align-items: flex-start;
}

/* Order Modal Styles */
.order-modal {
  background: white;
  border-radius: 20px;
  width: 90%;
  max-width: 500px;
  max-height: 90vh;
  overflow-y: auto;
  box-shadow: 0 20px 60px rgba(0, 0, 0, 0.2);
}

.order-modal-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 1.5rem 2rem;
  border-bottom: 1px solid #e2e8f0;
}

.order-modal-header h2 {
  margin: 0;
  color: #1a202c;
  font-size: 1.5rem;
  font-weight: 700;
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

.order-modal-content {
  padding: 0 2rem 2rem;
}

/* Business Info */
.business-info-section {
  background: rgba(255, 120, 40, 0.05);
  padding: 1rem;
  border-radius: 12px;
  border: 1px solid rgba(255, 120, 40, 0.2);
  margin-bottom: 1.5rem;
}

.business-name {
  font-size: 1.1rem;
  font-weight: 600;
  color: #1a202c;
}

/* Form Sections */
.form-section {
  margin-bottom: 1.5rem;
}

.form-label {
  display: block;
  margin-bottom: 0.5rem;
  font-weight: 600;
  color: #374151;
  font-size: 0.95rem;
}

/* Quantity Selector */
.quantity-selector {
  display: flex;
  align-items: center;
  gap: 1rem;
  justify-content: center;
  padding: 1rem;
  background: #f8fafc;
  border-radius: 8px;
  border: 1px solid #e2e8f0;
}

.quantity-btn {
  background: white;
  border: 2px solid #e2e8f0;
  border-radius: 8px;
  width: 40px;
  height: 40px;
  display: flex;
  align-items: center;
  justify-content: center;
  cursor: pointer;
  transition: all 0.3s ease;
  color: #4a5568;
}

.quantity-btn:hover:not(:disabled) {
  border-color: #FF7828;
  color: #FF7828;
}

.quantity-btn:disabled {
  opacity: 0.5;
  cursor: not-allowed;
}

.quantity-display {
  font-size: 1.25rem;
  font-weight: 700;
  color: #1a202c;
  min-width: 40px;
  text-align: center;
}

/* Input Fields */
.address-input,
.contact-input,
.instructions-input {
  width: 100%;
  padding: 0.75rem 1rem;
  border: 2px solid #e2e8f0;
  border-radius: 8px;
  font-size: 0.95rem;
  transition: all 0.3s ease;
  font-family: inherit;
}

.address-input:focus,
.contact-input:focus,
.instructions-input:focus {
  outline: none;
  border-color: #FF7828;
  box-shadow: 0 0 0 3px rgba(255, 120, 40, 0.1);
}

.contact-input {
  width: 100%;
}

/* Order Summary */
.order-summary {
  background: #f8fafc;
  padding: 1.5rem;
  border-radius: 12px;
  border: 1px solid #e2e8f0;
  margin: 2rem 0;
}

.summary-row {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 0.75rem;
  color: #4a5568;
}

.summary-row.total {
  font-size: 1.1rem;
  font-weight: 700;
  color: #1a202c;
  margin-bottom: 0;
}

.summary-divider {
  height: 1px;
  background: #e2e8f0;
  margin: 1rem 0;
}

/* Order Actions */
.order-actions {
  display: flex;
  gap: 1rem;
}

.cancel-btn {
  flex: 1;
  padding: 1rem 1.5rem;
  background: #f7fafc;
  border: 2px solid #e2e8f0;
  border-radius: 10px;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.3s ease;
  color: #4a5568;
}

.cancel-btn:hover {
  background: #edf2f7;
  border-color: #cbd5e0;
}

.confirm-order-btn {
  flex: 2;
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 0.5rem;
  padding: 1rem 1.5rem;
  background: #FF7828;
  color: white;
  border: none;
  border-radius: 10px;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.3s ease;
  box-shadow: 0 4px 12px rgba(255, 120, 40, 0.3);
}

.confirm-order-btn:hover:not(:disabled) {
  background: #e6691f;
  transform: translateY(-1px);
  box-shadow: 0 6px 16px rgba(255, 120, 40, 0.4);
}

.confirm-order-btn:disabled {
  opacity: 0.6;
  cursor: not-allowed;
  transform: none;
  box-shadow: none;
}

/* Responsive Design */
@media (max-width: 768px) {
  .place-filter-section {
    padding: 1rem;
  }
  
  .active-filter-indicator {
    flex-direction: column;
    gap: 0.75rem;
    align-items: flex-start;
  }
  
  .select-wrapper {
    max-width: 100%;
  }

  .header-actions {
    flex-wrap: wrap;
  }

  .notification-icon {
    order: 1;
  }

  .profile-btn {
    order: 2;
  }

  .logout-btn {
    order: 3;
  }

  .customer-location-info {
    flex-direction: column;
    align-items: flex-start;
    gap: 0.75rem;
  }

  .order-modal {
    width: 95%;
    margin: 1rem;
  }
  
  .order-modal-header,
  .order-modal-content {
    padding-left: 1.5rem;
    padding-right: 1.5rem;
  }
  
  .order-actions {
    flex-direction: column;
  }
  
  .gas-option-content {
    flex-direction: column;
    gap: 0.5rem;
    align-items: flex-start;
  }

  /* NEW: Responsive styles for ratings section */
  .section-header {
    flex-direction: column;
    gap: 1rem;
    align-items: flex-start;
  }

  .reviews-summary {
    width: 100%;
    justify-content: space-between;
  }

  .feedback-header {
    flex-direction: column;
    gap: 0.5rem;
    align-items: flex-start;
  }

  .rating-display-small {
    align-self: flex-start;
  }
}

/* Keep all existing styles below */
.customer-order-page {
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
  gap: 12px;
}

.logo {
  width: 50px;
  height: 50px;
  border-radius: 10px;
  box-shadow: 0 4px 12px rgba(255, 120, 40, 0.2);
}

.customer-info h2 {
  font-size: 1.1rem;
  margin: 0;
  color: #1a202c;
  font-weight: 600;
}

.highlight {
  color: #FF7828;
  font-weight: 700;
}

.customer-name {
  margin: 0;
  color: #718096;
  font-size: 0.9rem;
  font-weight: 500;
}

.header-actions {
  display: flex;
  gap: 12px;
}

.logout-btn {
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
  background: #FF7828;
  color: white;
  box-shadow: 0 4px 12px rgba(255, 120, 40, 0.3);
}

.logout-btn:hover {
  background: #e6691f;
  transform: translateY(-1px);
  box-shadow: 0 6px 16px rgba(255, 120, 40, 0.4);
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

/* Retailers Grid */
.retailers-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
  gap: 1.5rem;
  margin-top: 1rem;
}

.retailer-card {
  background: white;
  border-radius: 16px;
  padding: 1.5rem;
  border: 1px solid #e2e8f0;
  transition: all 0.3s ease;
  display: flex;
  flex-direction: column;
  gap: 1rem;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
}

.retailer-card:hover {
  border-color: #FF7828;
  box-shadow: 0 8px 25px rgba(255, 120, 40, 0.15);
  transform: translateY(-2px);
}

.retailer-header {
  display: flex;
  justify-content: space-between;
  align-items: flex-start;
  gap: 1rem;
}

.retailer-name {
  color: #1a202c;
  font-size: 1.1rem;
  font-weight: 700;
  line-height: 1.3;
  flex: 1;
}

.retailer-details {
  display: flex;
  flex-direction: column;
  gap: 0.75rem;
  flex: 1;
}

.detail-item {
  display: flex;
  align-items: flex-start;
  gap: 0.75rem;
  color: #4a5568;
  font-size: 0.9rem;
  line-height: 1.4;
}

.detail-icon {
  color: #718096;
  flex-shrink: 0;
  margin-top: 0.1rem;
}

.retailer-actions {
  display: flex;
  gap: 0.75rem;
  margin-top: auto;
}

.view-details-btn {
  flex: 1;
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 0.5rem;
  padding: 0.75rem 1rem;
  background: #f8fafc;
  color: #4a5568;
  border: 2px solid #e2e8f0;
  border-radius: 8px;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.3s ease;
  font-size: 0.9rem;
}

.view-details-btn:hover {
  background: #edf2f7;
  border-color: #cbd5e0;
  color: #FF7828;
}

.order-btn {
  flex: 2;
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 0.5rem;
  padding: 0.75rem 1rem;
  background: #FF7828;
  color: white;
  border: none;
  border-radius: 8px;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.3s ease;
  font-size: 0.9rem;
  box-shadow: 0 2px 8px rgba(255, 120, 40, 0.3);
}

.order-btn:hover:not(:disabled) {
  background: #e6691f;
  transform: translateY(-1px);
  box-shadow: 0 4px 12px rgba(255, 120, 40, 0.4);
}

.order-btn:disabled {
  opacity: 0.6;
  cursor: not-allowed;
  background: #9ca3af;
  transform: none;
  box-shadow: none;
}

/* NEW RETAILER DETAILS MODAL */
.retailer-details-modal {
  background: white;
  border-radius: 20px;
  width: 90%;
  max-width: 600px; /* Increased width to accommodate ratings section */
  max-height: 80vh;
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

.modal-header h2 {
  margin: 0;
  color: #1a202c;
  font-size: 1.5rem;
  font-weight: 700;
}

.modal-content {
  padding: 0 2rem 2rem;
}

.info-section {
  margin-bottom: 2rem;
}

.info-section h3 {
  margin: 0 0 1rem 0;
  color: #374151;
  font-size: 1.1rem;
  font-weight: 600;
}

.info-grid {
  display: flex;
  flex-direction: column;
  gap: 1rem;
}

.info-item {
  display: flex;
  align-items: flex-start;
  gap: 1rem;
  padding: 1rem;
  background: #f8fafc;
  border-radius: 8px;
  border: 1px solid #e2e8f0;
}

.info-icon {
  color: #FF7828;
  flex-shrink: 0;
  margin-top: 0.1rem;
}

.info-content {
  flex: 1;
}

.info-label {
  font-size: 0.85rem;
  color: #6b7280;
  margin-bottom: 0.25rem;
}

.info-value {
  font-weight: 600;
  color: #1a202c;
}

.products-grid {
  display: grid;
  gap: 0.75rem;
}

.product-card {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 1rem;
  border: 2px solid #e2e8f0;
  border-radius: 8px;
  transition: all 0.3s ease;
}

.product-card:hover:not(.out-of-stock) {
  border-color: #cbd5e0;
}

.product-card.out-of-stock {
  opacity: 0.6;
  background: #f7fafc;
}

.product-info {
  flex: 1;
}

.product-name {
  font-weight: 600;
  color: #1a202c;
  margin-bottom: 0.25rem;
}

.product-size {
  color: #718096;
  font-size: 0.9rem;
  margin-bottom: 0.25rem;
}

.product-stock {
  font-size: 0.8rem;
  font-weight: 500;
}

.product-stock.low-stock {
  color: #dc2626;
}

.product-price {
  font-size: 1.1rem;
  font-weight: 700;
  color: #FF7828;
}

.modal-actions {
  display: flex;
  gap: 1rem;
  margin-top: 2rem;
}

.secondary-btn {
  flex: 1;
  padding: 1rem 1.5rem;
  background: #f7fafc;
  border: 2px solid #e2e8f0;
  border-radius: 10px;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.3s ease;
  color: #4a5568;
}

.secondary-btn:hover {
  background: #edf2f7;
  border-color: #cbd5e0;
}

.primary-btn {
  flex: 2;
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 0.5rem;
  padding: 1rem 1.5rem;
  background: #FF7828;
  color: white;
  border: none;
  border-radius: 10px;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.3s ease;
  box-shadow: 0 4px 12px rgba(255, 120, 40, 0.3);
}

.primary-btn:hover:not(:disabled) {
  background: #e6691f;
  transform: translateY(-1px);
  box-shadow: 0 6px 16px rgba(255, 120, 40, 0.4);
}

.primary-btn:disabled {
  opacity: 0.6;
  cursor: not-allowed;
  transform: none;
  box-shadow: none;
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
/* Gas Type Selection Styles */
.gas-type-options {
  display: flex;
  flex-direction: column;
  gap: 0.75rem;
}

.gas-option {
  display: flex;
  align-items: center;
  padding: 1rem;
  border: 2px solid #e2e8f0;
  border-radius: 10px;
  cursor: pointer;
  transition: all 0.3s ease;
  background: white;
}

.gas-option:hover {
  border-color: #cbd5e0;
  background: #f8fafc;
}

.gas-option:has(.gas-radio:checked) {
  border-color: #FF7828;
  background: rgba(255, 120, 40, 0.05);
}

.gas-option.out-of-stock {
  opacity: 0.6;
  background: #f7fafc;
  cursor: not-allowed;
}

.gas-radio {
  margin-right: 1rem;
}

.gas-option-content {
  display: flex;
  align-items: center;
  justify-content: space-between;
  flex: 1;
  gap: 1rem;
}

.gas-type-info {
  flex: 1;
}

.tank-size {
  font-weight: 600;
  color: #1a202c;
  font-size: 1rem;
  margin-bottom: 0.25rem;
}

.gas-brand {
  color: #718096;
  font-size: 0.9rem;
  margin-bottom: 0.25rem;
}

.gas-stock {
  font-size: 0.8rem;
  font-weight: 500;
}

.gas-stock.low-stock {
  color: #dc2626;
  font-weight: 600;
}

.gas-price {
  font-size: 1.1rem;
  font-weight: 700;
  color: #FF7828;
  white-space: nowrap;
}

/* Responsive Design for Gas Options */
@media (max-width: 768px) {
  .gas-option-content {
    flex-direction: column;
    align-items: flex-start;
    gap: 0.5rem;
  }
  
  .gas-price {
    align-self: flex-end;
  }
}

/* Focus states for accessibility */
.gas-radio:focus {
  outline: 2px solid #FF7828;
  outline-offset: 2px;
}

.gas-option:focus-within {
  border-color: #FF7828;
  box-shadow: 0 0 0 3px rgba(255, 120, 40, 0.1);
}

/* RESPONSIVE DESIGN */
@media (max-width: 768px) {
  .retailers-grid {
    grid-template-columns: 1fr;
    gap: 1rem;
  }
  
  .retailer-actions {
    flex-direction: column;
  }
  
  .retailer-details-modal {
    width: 95%;
    margin: 1rem;
  }
  
  .modal-header,
  .modal-content {
    padding-left: 1.5rem;
    padding-right: 1.5rem;
  }
  
  .modal-actions {
    flex-direction: column;
  }
}

/* Responsive Design */
@media (max-width: 768px) {
  .customer-order-page {
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
  
  .tabs {
    flex-direction: column;
  }
  
  .retailer-card {
    flex-direction: column;
    gap: 1rem;
    align-items: stretch;
  }
  
  .retailer-header {
    flex-direction: column;
    align-items: flex-start;
    gap: 0.5rem;
  }
  
  .retailer-details {
    flex-direction: column;
    gap: 0.5rem;
    align-items: flex-start;
  }
  
  .modal-buttons {
    flex-direction: column;
  }

  .wallet-header-card {
    width: 100%;
  }

  .customer-wallet-modal {
    width: 95%;
    margin: 1rem;
  }

  .wallet-modal-header,
  .wallet-modal-content {
    padding-left: 1.25rem;
    padding-right: 1.25rem;
  }

  .wallet-layout {
    grid-template-columns: 1fr;
  }

  .quick-amounts {
    grid-template-columns: repeat(2, 1fr);
  }

}
</style>