<template>
  <!-- Content Area -->
  <section class="content-section">
    <div v-if="activeTab === 'analytics'" class="tab-content">
      <div class="content-header">
        <h2>System Analytics & Revenue Report</h2>
        <p>Performance metrics and admin revenue tracking</p>
        
        <!-- Date Filter Controls -->
        <div class="filter-controls">
          <div class="filter-group">
            <label>Date Range:</label>
            <div class="date-inputs">
              <input 
                type="date" 
                v-model="startDate" 
                class="date-input"
                @change="applyFilters"
              >
              <span class="date-separator">to</span>
              <input 
                type="date" 
                v-model="endDate" 
                class="date-input"
                @change="applyFilters"
              >
            </div>
          </div>
          
          <div class="filter-group">
            <label>Quick Filters:</label>
            <div class="quick-filters">
              <button 
                v-for="filter in quickFilters" 
                :key="filter.value"
                :class="['filter-btn', { active: selectedQuickFilter === filter.value }]"
                @click="setQuickFilter(filter.value)"
              >
                {{ filter.label }}
              </button>
            </div>
          </div>
          
          <div class="action-buttons">
            <button class="print-btn" @click="generatePrintableReport">
              <svg width="16" height="16" viewBox="0 0 24 24" fill="none">
                <path d="M6 9V2h12v7M6 18H4a2 2 0 01-2-2v-5a2 2 0 012-2h16a2 2 0 012 2v5a2 2 0 01-2 2h-2" stroke="currentColor" stroke-width="2"/>
                <path d="M6 14h12v8H6z" stroke="currentColor" stroke-width="2"/>
              </svg>
              Print Report
            </button>
          </div>
        </div>
      </div>

      <!-- Loading State -->
      <div v-if="loading" class="loading-state">
        <svg width="48" height="48" viewBox="0 0 24 24" fill="none" class="loading-spinner">
          <path d="M12 2v4m0 12v4M4.93 4.93l2.83 2.83m8.48 8.48l2.83 2.83M2 12h4m12 0h4M4.93 19.07l2.83-2.83m8.48-8.48l2.83-2.83" stroke="#FF7828" stroke-width="2" stroke-linecap="round"/>
        </svg>
        <p>Loading analytics data...</p>
      </div>

      <div v-else class="analytics-grid">
        <!-- Summary Cards -->
        <div class="summary-cards">
          <div class="summary-card">
            <div class="card-icon total-orders">
              <svg width="24" height="24" viewBox="0 0 24 24" fill="none">
                <path d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" stroke="currentColor" stroke-width="2"/>
              </svg>
            </div>
            <h3>Total {{ reportType === 'deliveries' ? 'Deliveries' : 'Orders' }}</h3>
            <p class="count">{{ filteredData.totalCount }}</p>
            <p class="card-subtitle">Selected period</p>
          </div>
          
          <div class="summary-card">
            <div class="card-icon total-revenue">
              <svg width="24" height="24" viewBox="0 0 24 24" fill="none">
                <path d="M12 1v22M17 5H9.5a3.5 3.5 0 100 7h5a3.5 3.5 0 110 7H6" stroke="currentColor" stroke-width="2"/>
              </svg>
            </div>
            <h3>Total Revenue</h3>
            <p class="count">₱{{ formatNumber(filteredData.totalRevenue) }}</p>
            <p class="card-subtitle">Selected period</p>
          </div>
          
          <div class="summary-card">
            <div class="card-icon system-commission">
              <svg width="24" height="24" viewBox="0 0 24 24" fill="none">
                <path d="M12 2a10 10 0 110 20 10 10 0 010-20z" stroke="currentColor" stroke-width="2"/>
                <path d="M12 6v12M15 9.5A3.5 3.5 0 1112 13" stroke="currentColor" stroke-width="2"/>
              </svg>
            </div>
            <h3>Admin Revenue</h3>
            <p class="count">₱{{ formatNumber(filteredData.systemCommission) }}</p>
            <p class="card-subtitle">₱10 per order + 33% delivery fee</p>
          </div>
          
          <div class="summary-card">
            <div class="card-icon average-value">
              <svg width="24" height="24" viewBox="0 0 24 24" fill="none">
                <path d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" stroke="currentColor" stroke-width="2"/>
              </svg>
            </div>
            <h3>Average Admin Revenue</h3>
            <p class="count">₱{{ formatNumber(filteredData.averageCommission) }}</p>
            <p class="card-subtitle">Per {{ reportType === 'deliveries' ? 'delivery' : 'order' }}</p>
          </div>
        </div>

        <!-- Admin Revenue Breakdown -->
        <div class="analytics-full-width">
          <div class="analytics-card">
            <div class="card-header">
              <h3>Admin Revenue Breakdown</h3>
              <p>Detailed view of admin earnings from ₱10 fixed commission and 33% delivery share</p>
            </div>
            <div class="commission-breakdown">
              <div class="breakdown-grid">
                <div class="breakdown-item">
                  <div class="breakdown-label">Total Transaction Value</div>
                  <div class="breakdown-value">₱{{ formatNumber(filteredData.totalRevenue) }}</div>
                  <div class="breakdown-bar">
                    <div class="bar-fill total" :style="{ width: '100%' }"></div>
                  </div>
                </div>
                <div class="breakdown-item">
                  <div class="breakdown-label">Admin Revenue</div>
                  <div class="breakdown-value commission">₱{{ formatNumber(filteredData.systemCommission) }}</div>
                  <div class="breakdown-bar">
                    <div class="bar-fill commission" :style="{ width: filteredData.totalRevenue > 0 ? ((filteredData.systemCommission / filteredData.totalRevenue) * 100) + '%' : '0%' }"></div>
                  </div>
                </div>
                <div class="breakdown-item">
                  <div class="breakdown-label">Net Amount After Admin Revenue</div>
                  <div class="breakdown-value net">₱{{ formatNumber(filteredData.netAmount) }}</div>
                  <div class="breakdown-bar">
                    <div class="bar-fill net" :style="{ width: filteredData.totalRevenue > 0 ? ((filteredData.netAmount / filteredData.totalRevenue) * 100) + '%' : '0%' }"></div>
                  </div>
                </div>
              </div>
              
              <div class="commission-stats">
                <div class="stat-item">
                  <span class="stat-label">Revenue Model:</span>
                  <span class="stat-value">₱10/order + 33% delivery fee</span>
                </div>
                <div class="stat-item">
                  <span class="stat-label">Average Admin Revenue per Transaction:</span>
                  <span class="stat-value">₱{{ formatNumber(filteredData.averageCommission) }}</span>
                </div>
                <div class="stat-item">
                  <span class="stat-label">Transactions with Admin Revenue:</span>
                  <span class="stat-value">{{ filteredData.commissionTransactions }}</span>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Left Column -->
        <div class="analytics-column">
          <!-- Admin Revenue Trends -->
          <div class="analytics-card">
            <div class="card-header">
              <h3>Admin Revenue Trends</h3>
              <p>Daily admin revenue earnings</p>
            </div>
            <div class="commission-trends">
              <div class="trends-chart">
                <div class="chart-column" v-for="day in filteredData.dailyCommissionTrends" :key="day.date">
                  <div class="column-bar" :style="{ height: day.commissionPercentage + '%' }">
                    <span class="column-value">₱{{ formatNumber(day.commission) }}</span>
                  </div>
                  <span class="column-label">{{ day.label }}</span>
                </div>
              </div>
            </div>
          </div>

          <!-- Top Revenue Sources -->
          <div class="analytics-card">
            <div class="card-header">
              <h3>Top Revenue Sources</h3>
              <p>Highest contributing {{ reportType === 'deliveries' ? 'retailers' : 'customers' }}</p>
            </div>
            <div class="top-commission-list">
              <div v-for="(item, index) in filteredData.topCommissionSources" :key="item.id" class="commission-item">
                <div class="item-rank">{{ index + 1 }}</div>
                <div class="item-info">
                  <p class="item-name">{{ item.name }}</p>
                  <p class="item-details">{{ item.count }} transactions • ₱{{ formatNumber(item.totalAmount) }}</p>
                </div>
                <div class="commission-amount">
                  <span class="commission-label">Admin Revenue:</span>
                  <span class="commission-value">₱{{ formatNumber(item.commission) }}</span>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Right Column -->
        <div class="analytics-column">
          <!-- Revenue Distribution -->
          <div class="analytics-card">
            <div class="card-header">
              <h3>Revenue Distribution</h3>
              <p>How admin revenue is distributed across transaction types</p>
            </div>
            <div class="commission-distribution">
              <div class="distribution-chart">
                <div class="chart-slice" 
                     v-for="type in filteredData.commissionDistribution" 
                     :key="type.type"
                     :style="{ '--percentage': type.percentage, '--color': type.color }">
                  <div class="slice-label">
                    <span class="type-dot" :style="{ backgroundColor: type.color }"></span>
                    {{ type.type }} ({{ type.percentage }}%)
                  </div>
                </div>
              </div>
              <div class="distribution-legend">
                <div v-for="type in filteredData.commissionDistribution" :key="type.type" class="legend-item">
                  <span class="legend-color" :style="{ backgroundColor: type.color }"></span>
                  <span class="legend-label">{{ type.type }}</span>
                  <span class="legend-commission">₱{{ formatNumber(type.commission) }}</span>
                </div>
              </div>
            </div>
          </div>

          <!-- Monthly Commission Projection -->
          <div class="analytics-card">
            <div class="card-header">
              <h3>Monthly Projection</h3>
              <p>Based on current admin revenue model</p>
            </div>
            <div class="projection-section">
              <div class="projection-item">
                <span class="projection-label">Projected Monthly Admin Revenue:</span>
                <span class="projection-value">₱{{ formatNumber(filteredData.monthlyProjection) }}</span>
              </div>
              <div class="projection-item">
                <span class="projection-label">Average Daily Admin Revenue:</span>
                <span class="projection-value">₱{{ formatNumber(filteredData.averageDailyCommission) }}</span>
              </div>
              <div class="projection-note">
                * Projection based on current 30-day average
              </div>
            </div>
          </div>
        </div>

        <!-- Data Table with Commission -->
        <div class="analytics-full-width">
          <div class="analytics-card">
            <div class="card-header">
              <h3>{{ reportType === 'deliveries' ? 'Deliveries' : 'Orders' }} Details with Admin Revenue</h3>
              <p>Complete transaction data including admin revenue calculation</p>
            </div>
            <div class="data-table-container">
              <table class="data-table">
                <thead>
                  <tr>
                    <th>ID</th>
                    <th>Customer</th>
                    <th>{{ reportType === 'deliveries' ? 'Retailer' : 'Type' }}</th>
                    <th>Amount</th>
                    <th>Delivery Fee</th>
                    <th>Admin Revenue</th>
                    <th>Net Amount</th>
                    <th>Status</th>
                    <th>Date</th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-for="item in filteredData.items" :key="item.id">
                    <td class="item-id">{{ item.orderId || item.id }}</td>
                    <td class="item-customer">{{ item.customerName }}</td>
                    <td class="item-retailer">{{ reportType === 'deliveries' ? item.retailerName : item.productType }}</td>
                    <td class="item-amount">₱{{ formatNumber(item.totalAmount) }}</td>
                    <td class="item-amount">₱{{ formatNumber(getDeliveryFee(item)) }}</td>
                    <td class="item-commission">
                      <span class="commission-badge">₱{{ formatNumber(calculateCommission(item.totalAmount, item)) }}</span>
                    </td>
                    <td class="item-net">₱{{ formatNumber(calculateNetAmount(item.totalAmount, item)) }}</td>
                    <td class="item-status">
                      <span :class="['status-badge', item.status]">{{ formatStatus(item.status) }}</span>
                    </td>
                    <td class="item-date">{{ formatDate(item.createdAt) }}</td>
                  </tr>
                </tbody>
                <tfoot>
                  <tr class="table-total">
                    <td colspan="3"><strong>Totals:</strong></td>
                    <td><strong>₱{{ formatNumber(filteredData.totalRevenue) }}</strong></td>
                    <td><strong>₱{{ formatNumber(filteredData.totalDeliveryFee) }}</strong></td>
                    <td><strong class="commission-total">₱{{ formatNumber(filteredData.systemCommission) }}</strong></td>
                    <td><strong class="net-total">₱{{ formatNumber(filteredData.netAmount) }}</strong></td>
                    <td colspan="2"></td>
                  </tr>
                </tfoot>
              </table>
              <div v-if="filteredData.items.length === 0" class="no-data">
                <p>No data available for selected period</p>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- Printable Report (Hidden until printing) -->
  <div v-if="showPrintableReport" class="printable-report">
    <div class="report-header">
      <div class="company-info">
        <h1>LPG Delivery System</h1>
        <p>Official Admin Revenue Report</p>
      </div>
      <div class="report-info">
        <h2>{{ printableReport.title }}</h2>
        <p class="report-period">{{ printableReport.period }}</p>
        <p class="report-date">Generated on: {{ printableReport.generatedDate }}</p>
      </div>
    </div>

    <!-- Admin Revenue Summary Section -->
    <div class="report-summary">
      <h3>Admin Revenue Summary</h3>
      <div class="summary-grid">
        <div class="summary-item">
          <span class="label">Total Transactions:</span>
          <span class="value">{{ printableReport.summary.totalCount }}</span>
        </div>
        <div class="summary-item">
          <span class="label">Total Revenue:</span>
          <span class="value">₱{{ formatNumber(printableReport.summary.totalRevenue) }}</span>
        </div>
        <div class="summary-item highlight">
          <span class="label">Admin Revenue:</span>
          <span class="value">₱{{ formatNumber(printableReport.summary.systemCommission) }}</span>
        </div>
        <div class="summary-item">
          <span class="label">Net Amount After Admin Revenue:</span>
          <span class="value">₱{{ formatNumber(printableReport.summary.netAmount) }}</span>
        </div>
        <div class="summary-item">
          <span class="label">Average Admin Revenue per Transaction:</span>
          <span class="value">₱{{ formatNumber(printableReport.summary.averageCommission) }}</span>
        </div>
        <div class="summary-item">
          <span class="label">Revenue Model:</span>
          <span class="value">₱10/order + 33% delivery fee</span>
        </div>
      </div>
    </div>

    <!-- Admin Revenue Breakdown -->
    <div class="commission-breakdown-section">
      <h3>Admin Revenue Breakdown</h3>
      <div class="breakdown-visual">
        <div class="breakdown-bar">
          <div class="bar-section total" style="width: 100%">
            <span class="bar-label">Total Revenue: ₱{{ formatNumber(printableReport.summary.totalRevenue) }}</span>
          </div>
        </div>
        <div class="breakdown-bar">
          <div class="bar-section commission" style="width: 10%">
            <span class="bar-label">Admin Revenue: ₱{{ formatNumber(printableReport.summary.systemCommission) }}</span>
          </div>
          <div class="bar-section net" style="width: 90%">
            <span class="bar-label">Net Amount: ₱{{ formatNumber(printableReport.summary.netAmount) }}</span>
          </div>
        </div>
      </div>
    </div>

    <!-- Detailed Data Table -->
    <div class="report-details">
      <h3>Detailed Transaction Data with Admin Revenue</h3>
      <table class="report-table">
        <thead>
          <tr>
            <th>Order ID</th>
            <th>Customer Name</th>
            <th>{{ reportType === 'deliveries' ? 'Retailer' : 'Product Type' }}</th>
            <th>Product</th>
            <th>Quantity</th>
            <th>Amount</th>
            <th>Delivery Fee</th>
            <th>Admin Revenue</th>
            <th>Net Amount</th>
            <th>Status</th>
            <th>Date & Time</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="item in printableReport.items" :key="item.id">
            <td class="monospace">{{ item.orderId || item.id }}</td>
            <td>{{ item.customerName }}</td>
            <td>{{ reportType === 'deliveries' ? item.retailerName : item.productType }}</td>
            <td>{{ getProductDescription(item) }}</td>
            <td class="text-center">{{ getItemQuantity(item) }}</td>
            <td class="text-right">₱{{ formatNumber(item.totalAmount) }}</td>
            <td class="text-right">₱{{ formatNumber(getDeliveryFee(item)) }}</td>
            <td class="text-right commission-cell">₱{{ formatNumber(calculateCommission(item.totalAmount, item)) }}</td>
            <td class="text-right net-cell">₱{{ formatNumber(calculateNetAmount(item.totalAmount, item)) }}</td>
            <td class="text-center">
              <span :class="['status-badge', item.status]">{{ formatStatus(item.status) }}</span>
            </td>
            <td class="monospace">{{ formatDateTime(item.createdAt) }}</td>
          </tr>
        </tbody>
        <tfoot>
          <tr class="total-row">
            <td colspan="5" class="text-right"><strong>Grand Totals:</strong></td>
            <td class="text-right total-amount">
              <strong>₱{{ formatNumber(printableReport.summary.totalRevenue) }}</strong>
            </td>
            <td class="text-right total-amount">
              <strong>₱{{ formatNumber(printableReport.summary.totalDeliveryFee) }}</strong>
            </td>
            <td class="text-right commission-total">
              <strong>₱{{ formatNumber(printableReport.summary.systemCommission) }}</strong>
            </td>
            <td class="text-right net-total">
              <strong>₱{{ formatNumber(printableReport.summary.netAmount) }}</strong>
            </td>
            <td colspan="2"></td>
          </tr>
        </tfoot>
      </table>
    </div>

    <!-- Footer -->
    <div class="report-footer">
      <div class="footer-signature">
        <p>_________________________</p>
        <p>Authorized Signature</p>
      </div>
      <div class="footer-info">
        <p>Generated by: LPG Delivery System Admin</p>
        <p>Revenue Model: ₱10/order + 33% delivery fee on all transactions</p>
        <p>Page 1 of 1</p>
      </div>
    </div>
  </div>

  <!-- Print Modal -->
  <div v-if="showPrintPreview" class="modal-overlay print-preview">
    <div class="modal print-modal">
      <div class="modal-header">
        <h3>Print Preview</h3>
        <button class="close-btn" @click="showPrintPreview = false">×</button>
      </div>
      <div class="modal-content">
        <p>Your commission report is ready for printing.</p>
        <div class="print-options">
          <label>
            <input type="checkbox" v-model="printWithHeader" />
            Include company header
          </label>
          <label>
            <input type="checkbox" v-model="printWithSummary" checked />
            Include commission summary
          </label>
          <label>
            <input type="checkbox" v-model="printLandscape" />
            Landscape orientation
          </label>
        </div>
      </div>
      <div class="modal-buttons">
        <button class="modal-btn-cancel" @click="showPrintPreview = false">Cancel</button>
        <button class="modal-btn-confirm" @click="printReport">Print</button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted } from "vue";
import { router } from '@inertiajs/vue3';

// FIREBASE
import { db, auth } from "@/firebase";
import { 
  collection, 
  query, 
  where, 
  onSnapshot,
  orderBy,
  Timestamp
} from "firebase/firestore";
import { onAuthStateChanged } from "firebase/auth";

const activeTab = ref("analytics");
const loading = ref(true);

// Filter controls
const startDate = ref('');
const endDate = ref('');
const selectedQuickFilter = ref('today');
const reportType = ref('deliveries');

// Print controls
const showPrintableReport = ref(false);
const showPrintPreview = ref(false);
const printWithHeader = ref(true);
const printWithSummary = ref(true);
const printLandscape = ref(false);

// Printable report data
const printableReport = ref({
  title: '',
  period: '',
  generatedDate: '',
  summary: {},
  items: [],
  commissionDistribution: []
});

// Quick filters
const quickFilters = [
  { label: 'Today', value: 'today' },
  { label: 'This Week', value: 'week' },
  { label: 'This Month', value: 'month' },
  { label: 'This Year', value: 'year' },
  { label: 'Quarterly', value: 'quarterly' },
];

// Real-time data
const deliveriesData = ref([]);
const ordersData = ref([]);

// Firestore unsubscribe functions
let deliveriesUnsubscribe = null;
let ordersUnsubscribe = null;

// Admin revenue calculation functions
const FIXED_PLATFORM_COMMISSION = 10;
const DELIVERY_ADMIN_SHARE_RATE = 0.33;

const getDeliveryFee = (item) => {
  return Number(item?.deliveryFee || item?.fee || 0);
};

const calculateCommission = (amount, item = null) => {
  const deliveryFee = item ? getDeliveryFee(item) : 0;
  return FIXED_PLATFORM_COMMISSION + Number((deliveryFee * DELIVERY_ADMIN_SHARE_RATE).toFixed(2));
};

const calculateNetAmount = (amount, item = null) => {
  return Math.max(Number(amount || 0) - calculateCommission(amount, item), 0);
};

// Initialize date filters
const initializeDates = () => {
  const today = new Date();
  startDate.value = today.toISOString().split('T')[0];
  endDate.value = today.toISOString().split('T')[0];
};

// Set quick filter
const setQuickFilter = (filter) => {
  selectedQuickFilter.value = filter;
  const today = new Date();
  
  switch (filter) {
    case 'today':
      startDate.value = today.toISOString().split('T')[0];
      endDate.value = today.toISOString().split('T')[0];
      break;
    case 'week':
      const startOfWeek = new Date(today);
      startOfWeek.setDate(today.getDate() - today.getDay());
      startDate.value = startOfWeek.toISOString().split('T')[0];
      endDate.value = today.toISOString().split('T')[0];
      break;
    case 'month':
      const startOfMonth = new Date(today.getFullYear(), today.getMonth(), 1);
      startDate.value = startOfMonth.toISOString().split('T')[0];
      endDate.value = today.toISOString().split('T')[0];
      break;
    case 'year':
      const startOfYear = new Date(today.getFullYear(), 0, 1);
      startDate.value = startOfYear.toISOString().split('T')[0];
      endDate.value = today.toISOString().split('T')[0];
      break;
    case 'quarterly':
      const quarter = Math.floor(today.getMonth() / 3);
      const startOfQuarter = new Date(today.getFullYear(), quarter * 3, 1);
      startDate.value = startOfQuarter.toISOString().split('T')[0];
      endDate.value = today.toISOString().split('T')[0];
      break;
    case 'custom':
      // Keep current custom dates
      break;
  }
  
  applyFilters();
};

// Apply filters
const applyFilters = () => {
  selectedQuickFilter.value = 'custom';
};

// Generate printable report
const generatePrintableReport = () => {
  const data = filteredData.value;
  const today = new Date();
  
  // Determine report title based on filter
  let reportTitle = '';
  let reportPeriod = '';
  
  const start = new Date(startDate.value);
  const end = new Date(endDate.value);
  
  if (selectedQuickFilter.value === 'today') {
    reportTitle = `Daily Admin Revenue Report`;
    reportPeriod = `For ${start.toLocaleDateString('en-US', { month: 'long', day: 'numeric', year: 'numeric' })}`;
  } else if (selectedQuickFilter.value === 'week') {
    reportTitle = `Weekly Admin Revenue Report`;
    reportPeriod = `Week of ${start.toLocaleDateString('en-US', { month: 'long', day: 'numeric', year: 'numeric' })} to ${end.toLocaleDateString('en-US', { month: 'long', day: 'numeric', year: 'numeric' })}`;
  } else if (selectedQuickFilter.value === 'month') {
    reportTitle = `Monthly Admin Revenue Report`;
    reportPeriod = `For ${start.toLocaleDateString('en-US', { month: 'long', year: 'numeric' })}`;
  } else if (selectedQuickFilter.value === 'year') {
    reportTitle = `Annual Admin Revenue Report`;
    reportPeriod = `For Year ${start.getFullYear()}`;
  } else if (selectedQuickFilter.value === 'quarterly') {
    const quarter = Math.floor(start.getMonth() / 3) + 1;
    reportTitle = `Quarterly Admin Revenue Report`;
    reportPeriod = `Q${quarter} ${start.getFullYear()} (${start.toLocaleDateString('en-US', { month: 'long', day: 'numeric' })} - ${end.toLocaleDateString('en-US', { month: 'long', day: 'numeric', year: 'numeric' })})`;
  } else {
    reportTitle = `Custom Admin Revenue Report`;
    reportPeriod = `From ${start.toLocaleDateString('en-US', { month: 'long', day: 'numeric', year: 'numeric' })} to ${end.toLocaleDateString('en-US', { month: 'long', day: 'numeric', year: 'numeric' })}`;
  }
  
  printableReport.value = {
    title: reportTitle,
    period: reportPeriod,
    generatedDate: today.toLocaleDateString('en-US', { 
      year: 'numeric', 
      month: 'long', 
      day: 'numeric',
      hour: '2-digit',
      minute: '2-digit'
    }),
    summary: {
      summary: {
  totalCount: data.totalCount,
  totalRevenue: data.totalRevenue,
  totalDeliveryFee: data.totalDeliveryFee,
  systemCommission: data.systemCommission,
  netAmount: data.netAmount,
  averageCommission: data.averageCommission
},
    },
    items: data.items,
    commissionDistribution: data.commissionDistribution
  };
  
  showPrintPreview.value = true;
};

// Print report
const printReport = () => {
  showPrintableReport.value = true;
  showPrintPreview.value = false;
  
  setTimeout(() => {
    const printContent = document.querySelector('.printable-report').innerHTML;
    const printWindow = window.open('', '_blank');
    
    const printStyles = `
      <style>
        @media print {
          @page {
            size: ${printLandscape.value ? 'landscape' : 'portrait'};
            margin: 0.5in;
          }
          body {
            margin: 0;
            padding: 0;
            font-family: 'Arial', sans-serif;
            font-size: 12px;
            line-height: 1.4;
            color: black;
            background: white;
          }
          .printable-report {
            display: block !important;
            width: 100%;
            max-width: none;
            margin: 0;
            padding: 0;
            background: white;
          }
          .report-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 1.5rem;
            padding-bottom: 1rem;
            border-bottom: 2px solid #333;
          }
          .company-info h1 {
            margin: 0 0 0.25rem 0;
            font-size: 24px;
            color: #1a202c;
          }
          .company-info p {
            margin: 0;
            color: #666;
            font-size: 14px;
          }
          .report-info h2 {
            margin: 0 0 0.5rem 0;
            font-size: 20px;
            color: #1a202c;
            text-align: right;
          }
          .report-period {
            margin: 0 0 0.25rem 0;
            font-size: 14px;
            color: #666;
            text-align: right;
          }
          .report-date {
            margin: 0;
            font-size: 12px;
            color: #999;
            text-align: right;
          }
          .report-summary {
            margin-bottom: 1.5rem;
            padding: 1rem;
            background: #f8fafc;
            border-radius: 4px;
            page-break-inside: avoid;
          }
          .report-summary h3 {
            margin: 0 0 1rem 0;
            font-size: 16px;
            color: #1a202c;
          }
          .summary-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 0.75rem;
          }
          .summary-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0.5rem;
            background: white;
            border-radius: 4px;
            border: 1px solid #e2e8f0;
            font-size: 12px;
          }
          .summary-item.highlight {
            background: #fff7ed;
            border-color: #fed7aa;
          }
          .summary-item .label {
            font-weight: 600;
            color: #4a5568;
          }
          .summary-item .value {
            font-weight: 700;
            color: #1a202c;
          }
          .commission-breakdown-section {
            margin-bottom: 1.5rem;
            page-break-inside: avoid;
          }
          .commission-breakdown-section h3 {
            margin: 0 0 1rem 0;
            font-size: 16px;
            color: #1a202c;
          }
          .breakdown-bar {
            display: flex;
            height: 40px;
            margin-bottom: 0.5rem;
            border-radius: 4px;
            overflow: hidden;
          }
          .bar-section {
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 600;
            font-size: 11px;
            text-align: center;
          }
          .bar-section.total {
            background: #3b82f6;
          }
          .bar-section.commission {
            background: #ef4444;
          }
          .bar-section.net {
            background: #10b981;
          }
          .bar-label {
            padding: 0 0.5rem;
          }
          .report-details {
            margin-bottom: 1.5rem;
            page-break-inside: avoid;
          }
          .report-details h3 {
            margin: 0 0 1rem 0;
            font-size: 16px;
            color: #1a202c;
          }
          .report-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 10px;
            page-break-inside: auto;
          }
          .report-table th {
            background: #1a202c !important;
            color: white !important;
            padding: 0.5rem;
            text-align: left;
            font-weight: 600;
            border: 1px solid #2d3748;
            -webkit-print-color-adjust: exact;
          }
          .report-table td {
            padding: 0.4rem;
            border: 1px solid #e2e8f0;
            page-break-inside: avoid;
            page-break-after: auto;
          }
          .report-table tfoot {
            background: #f7fafc;
            -webkit-print-color-adjust: exact;
          }
          .report-table tfoot td {
            font-weight: 600;
            border-top: 2px solid #1a202c;
          }
          .monospace {
            font-family: 'Courier New', monospace;
            font-size: 9px;
          }
          .text-center {
            text-align: center;
          }
          .text-right {
            text-align: right;
          }
          .commission-cell {
            color: #ef4444;
            font-weight: 600;
          }
          .net-cell {
            color: #10b981;
          }
          .total-amount {
            color: #3b82f6;
          }
          .commission-total {
            color: #ef4444;
            font-size: 11px;
          }
          .net-total {
            color: #10b981;
            font-size: 11px;
          }
          .total-row {
            background: #f1f5f9 !important;
          }
          .report-footer {
            display: flex;
            justify-content: space-between;
            align-items: flex-end;
            margin-top: 2rem;
            padding-top: 1rem;
            border-top: 1px solid #e2e8f0;
            page-break-inside: avoid;
          }
          .footer-signature {
            text-align: center;
          }
          .footer-signature p:first-child {
            margin: 0 0 0.5rem 0;
            color: #666;
          }
          .footer-signature p:last-child {
            margin: 0;
            font-size: 11px;
            color: #666;
          }
          .footer-info {
            text-align: right;
          }
          .footer-info p {
            margin: 0 0 0.25rem 0;
            font-size: 11px;
            color: #666;
          }
          .status-badge {
            padding: 0.2rem 0.4rem;
            border-radius: 8px;
            font-size: 8px;
            font-weight: 600;
            text-transform: capitalize;
            -webkit-print-color-adjust: exact;
          }
          .status-badge.accepted,
          .status-badge.delivered,
          .status-badge.completed {
            background: #d1fae5 !important;
            color: #065f46 !important;
          }
          .status-badge.pending,
          .status-badge.assigned {
            background: #fef3c7 !important;
            color: #92400e !important;
          }
          .status-badge.in-transit {
            background: #dbeafe !important;
            color: #1e40af !important;
          }
          .status-badge.cancelled {
            background: #fee2e2 !important;
            color: #991b1b !important;
          }
        }
        @media screen {
          .printable-report {
            display: none !important;
          }
        }
      </style>
    `;
    
    printWindow.document.write(`
      <!DOCTYPE html>
      <html>
        <head>
          <title>${printableReport.value.title}</title>
          ${printStyles}
        </head>
        <body>
          <div class="printable-report">
            ${printContent}
          </div>
        </body>
      </html>
    `);
    
    printWindow.document.close();
    printWindow.focus();
    
    setTimeout(() => {
      printWindow.print();
      printWindow.onafterprint = () => {
        printWindow.close();
        showPrintableReport.value = false;
      };
    }, 500);
  }, 100);
};

// Export to CSV
const exportToCSV = () => {
  const data = filteredData.value;
  const headers = ['Order ID', 'Customer Name', 'Retailer', 'Product Type', 'Quantity', 'Amount', 'Admin Revenue', 'Net Amount After Admin Revenue', 'Status', 'Date'];
  
  const csvContent = [
    headers,
    ...data.items.map(item => [
      item.orderId || item.id,
      `"${item.customerName}"`,
      `"${item.retailerName}"`,
      `"${getProductDescription(item)}"`,
      getItemQuantity(item),
      item.totalAmount,
      calculateCommission(item.totalAmount, item),
      calculateNetAmount(item.totalAmount, item),
      formatStatus(item.status),
      `"${formatDateTime(item.createdAt)}"`
    ])
  ].map(row => row.join(',')).join('\n');

  const blob = new Blob([csvContent], { type: 'text/csv' });
  const url = window.URL.createObjectURL(blob);
  const a = document.createElement('a');
  a.href = url;
  a.download = `admin-revenue-report-${new Date().toISOString().split('T')[0]}.csv`;
  a.click();
  window.URL.revokeObjectURL(url);
};

// Filter data based on selected dates and report type
const filteredData = computed(() => {
  const data = reportType.value === 'deliveries' ? deliveriesData.value : ordersData.value;
  const start = new Date(startDate.value);
  const end = new Date(endDate.value);
  end.setHours(23, 59, 59, 999);

  const filtered = data.filter(item => {
    const itemDate = item.createdAt?.toDate ? item.createdAt.toDate() : new Date(item.createdAt);
    return itemDate >= start && itemDate <= end;
  });

  // Calculate statistics with admin revenue model
  const totalCount = filtered.length;
  const totalRevenue = filtered.reduce((sum, item) => sum + Number(item.totalAmount || item.total || 0), 0);
  const totalDeliveryFee = filtered.reduce((sum, item) => sum + getDeliveryFee(item), 0);
  const systemCommission = filtered.reduce((sum, item) => {
    return sum + calculateCommission(Number(item.totalAmount || item.total || 0), item);
  }, 0);
  const netAmount = filtered.reduce((sum, item) => {
    return sum + calculateNetAmount(Number(item.totalAmount || item.total || 0), item);
  }, 0);
  const averageCommission = totalCount > 0 ? systemCommission / totalCount : 0;
  
  const completedCount = filtered.filter(item => 
    ['delivered', 'completed', 'accepted'].includes(item.status)
  ).length;
  
  const commissionTransactions = filtered.filter(item => 
    item.totalAmount > 0
  ).length;

  // Commission distribution by type
  const typeCommission = {};
  filtered.forEach(item => {
    const type = reportType.value === 'deliveries' ? item.retailerName : item.productType;
    const commission = calculateCommission(Number(item.totalAmount || item.total || 0), item);
    
    if (!typeCommission[type]) {
      typeCommission[type] = { commission: 0, count: 0 };
    }
    typeCommission[type].commission += commission;
    typeCommission[type].count += 1;
  });

  const commissionDistribution = Object.entries(typeCommission)
    .sort(([,a], [,b]) => b.commission - a.commission)
    .slice(0, 5)
    .map(([type, data], index) => ({
      type: type || 'Unknown',
      commission: data.commission,
      count: data.count,
      percentage: systemCommission > 0 ? Math.round((data.commission / systemCommission) * 100) : 0,
      color: getCommissionColor(index)
    }));

  // Daily commission trends
  const dailyCommissionData = {};
  filtered.forEach(item => {
    const date = item.createdAt?.toDate ? item.createdAt.toDate() : new Date(item.createdAt);
    const dateKey = date.toISOString().split('T')[0];
    if (!dailyCommissionData[dateKey]) {
      dailyCommissionData[dateKey] = { commission: 0, count: 0 };
    }
    dailyCommissionData[dateKey].commission += calculateCommission(Number(item.totalAmount || item.total || 0), item);
    dailyCommissionData[dateKey].count += 1;
  });

  const dailyCommissionTrends = Object.entries(dailyCommissionData)
    .sort(([a], [b]) => new Date(a) - new Date(b))
    .map(([date, data]) => {
      const maxCommission = Math.max(...Object.values(dailyCommissionData).map(d => d.commission));
      return {
        date,
        label: new Date(date).toLocaleDateString('en-US', { month: 'short', day: 'numeric' }),
        commission: data.commission,
        count: data.count,
        commissionPercentage: maxCommission > 0 ? (data.commission / maxCommission) * 100 : 0
      };
    });

  // Top commission sources
  const sourceCommission = {};
  filtered.forEach(item => {
    const key = reportType.value === 'deliveries' ? item.retailerId : item.customerId;
    const name = reportType.value === 'deliveries' ? item.retailerName : item.customerName;
    const commission = calculateCommission(Number(item.totalAmount || item.total || 0), item);
    
    if (!sourceCommission[key]) {
      sourceCommission[key] = { id: key, name, count: 0, totalAmount: 0, commission: 0 };
    }
    sourceCommission[key].count += 1;
    sourceCommission[key].totalAmount += Number(item.totalAmount || item.total || 0);
    sourceCommission[key].commission += commission;
  });

  const topCommissionSources = Object.values(sourceCommission)
    .sort((a, b) => b.commission - a.commission)
    .slice(0, 5);

  // Monthly projection
  const daysInPeriod = Math.max(1, Math.ceil((end - start) / (1000 * 60 * 60 * 24)));
  const averageDailyCommission = systemCommission / daysInPeriod;
  const monthlyProjection = averageDailyCommission * 30;

  return {
    items: filtered,
    totalCount,
    totalRevenue,
    totalDeliveryFee,
    systemCommission,
    netAmount,
    averageCommission,
    completedCount,
    commissionTransactions,
    commissionDistribution,
    dailyCommissionTrends,
    topCommissionSources,
    monthlyProjection,
    averageDailyCommission
  };
});

// Helper functions
const getProductDescription = (item) => {
  if (item.items && item.items.length > 0) {
    return item.items[0].productType || 'LPG Tank';
  }
  return item.productType || 'LPG Tank';
};

const getItemQuantity = (item) => {
  if (item.items && item.items.length > 0) {
    return item.items[0].quantity || 1;
  }
  return item.quantity || 1;
};

const formatDateTime = (date) => {
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

const formatNumber = (num) => {
  const value = Number(num || 0);
  return value.toLocaleString("en-PH", {
    minimumFractionDigits: value % 1 === 0 ? 0 : 2,
    maximumFractionDigits: 2
  });
};

const formatStatus = (status) => {
  const statusMap = {
    'accepted': 'Accepted',
    'delivered': 'Delivered',
    'completed': 'Completed',
    'pending': 'Pending',
    'in-transit': 'In Transit',
    'assigned': 'Assigned',
    'cancelled': 'Cancelled'
  };
  return statusMap[status] || status;
};

const getCommissionColor = (index) => {
  const colors = ['#FF7828', '#3b82f6', '#10b981', '#8b5cf6', '#ef4444'];
  return colors[index] || '#6b7280';
};

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

// Initialize real-time data listeners
const initializeRealtimeListeners = () => {
  // Listen to deliveries collection
  const deliveriesQuery = query(collection(db, "deliveries"), orderBy("createdAt", "desc"));
  deliveriesUnsubscribe = onSnapshot(deliveriesQuery, (snapshot) => {
    const deliveriesList = [];
    snapshot.forEach((doc) => {
      deliveriesList.push({
        id: doc.id,
        ...doc.data()
      });
    });
    deliveriesData.value = deliveriesList;
  });

  // Listen to orders collection
  const ordersQuery = query(collection(db, "orders"), orderBy("createdAt", "desc"));
  ordersUnsubscribe = onSnapshot(ordersQuery, (snapshot) => {
    const ordersList = [];
    snapshot.forEach((doc) => {
      ordersList.push({
        id: doc.id,
        ...doc.data()
      });
    });
    ordersData.value = ordersList;
  });
};

// Cleanup listeners
const cleanupListeners = () => {
  if (deliveriesUnsubscribe) {
    deliveriesUnsubscribe();
  }
  if (ordersUnsubscribe) {
    ordersUnsubscribe();
  }
};

// Initialize on component mount
onMounted(() => {
  initializeDates();
  setQuickFilter('today');
  
  onAuthStateChanged(auth, (user) => {
    if (user) {
      initializeRealtimeListeners();
      setTimeout(() => {
        loading.value = false;
      }, 1000);
    } else {
      loading.value = false;
      console.log("No admin logged in");
    }
  });
});

// Cleanup on component unmount
onUnmounted(() => {
  cleanupListeners();
});
</script>

<style scoped>
/* Previous CSS styles remain, adding new commission-specific styles */

.system-commission {
  background: rgba(239, 68, 68, 0.1);
  color: #ef4444;
}

.commission-badge {
  background: rgba(239, 68, 68, 0.1);
  color: #ef4444;
  padding: 0.25rem 0.5rem;
  border-radius: 6px;
  font-weight: 600;
  font-size: 0.8rem;
}

.commission-total {
  color: #ef4444;
}

.net-total {
  color: #10b981;
}

/* Admin Revenue Breakdown */
.commission-breakdown {
  padding: 1rem 0;
}

.breakdown-grid {
  display: flex;
  flex-direction: column;
  gap: 1rem;
  margin-bottom: 1.5rem;
}

.breakdown-item {
  display: flex;
  flex-direction: column;
  gap: 0.5rem;
}

.breakdown-label {
  font-weight: 600;
  color: #374151;
  font-size: 0.9rem;
}

.breakdown-value {
  font-weight: 700;
  font-size: 1.1rem;
}

.breakdown-value.commission {
  color: #ef4444;
}

.breakdown-value.net {
  color: #10b981;
}

.breakdown-bar {
  height: 8px;
  background: #e5e7eb;
  border-radius: 4px;
  overflow: hidden;
}

.bar-fill {
  height: 100%;
  border-radius: 4px;
  transition: width 0.3s ease;
}

.bar-fill.total {
  background: #3b82f6;
}

.bar-fill.commission {
  background: #ef4444;
}

.bar-fill.net {
  background: #10b981;
}

.commission-stats {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
  gap: 1rem;
  padding: 1rem;
  background: #f8fafc;
  border-radius: 8px;
  border: 1px solid #e2e8f0;
}

.stat-item {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 0.5rem 0;
}

.stat-label {
  color: #6b7280;
  font-size: 0.85rem;
}

.stat-value {
  font-weight: 600;
  color: #1f2937;
}

/* Admin Revenue Trends */
.commission-trends {
  padding: 1rem 0;
}

.trends-chart {
  display: flex;
  align-items: flex-end;
  justify-content: space-between;
  height: 150px;
  padding: 0 0.5rem;
}

.chart-column {
  display: flex;
  flex-direction: column;
  align-items: center;
  flex: 1;
  margin: 0 0.25rem;
}

.column-bar {
  width: 100%;
  background: #ef4444;
  border-radius: 4px 4px 0 0;
  position: relative;
  transition: all 0.3s ease;
  min-height: 20px;
}

.column-bar:hover {
  transform: scaleY(1.05);
  background: #dc2626;
}

.column-value {
  position: absolute;
  top: -25px;
  left: 50%;
  transform: translateX(-50%);
  font-size: 0.75rem;
  color: #1a202c;
  font-weight: 600;
  white-space: nowrap;
}

.column-label {
  margin-top: 0.5rem;
  font-size: 0.75rem;
  color: #718096;
}

/* Top Commission List */
.top-commission-list {
  display: flex;
  flex-direction: column;
  gap: 0.75rem;
}

.commission-item {
  display: flex;
  align-items: center;
  gap: 1rem;
  padding: 0.75rem;
  background: white;
  border-radius: 8px;
  border: 1px solid #e5e7eb;
}

.item-rank {
  width: 24px;
  height: 24px;
  background: #f3f4f6;
  border-radius: 6px;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 0.75rem;
  font-weight: 600;
  color: #6b7280;
}

.item-info {
  flex: 1;
}

.item-name {
  margin: 0 0 0.25rem 0;
  font-weight: 600;
  color: #1f2937;
  font-size: 0.875rem;
}

.item-details {
  margin: 0;
  color: #6b7280;
  font-size: 0.75rem;
}

.commission-amount {
  display: flex;
  flex-direction: column;
  align-items: flex-end;
  gap: 0.25rem;
}

.commission-label {
  font-size: 0.7rem;
  color: #6b7280;
}

.commission-value {
  font-weight: 600;
  color: #ef4444;
  font-size: 0.875rem;
}

/* Revenue Distribution */
.commission-distribution {
  display: flex;
  gap: 2rem;
  align-items: flex-start;
}

.distribution-chart {
  flex: 1;
  display: flex;
  flex-direction: column;
  gap: 0.5rem;
}

.chart-slice {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  padding: 0.5rem;
  background: linear-gradient(
    to right,
    var(--color) 0%,
    var(--color) var(--percentage),
    #e5e7eb var(--percentage),
    #e5e7eb 100%
  );
  border-radius: 4px;
  font-size: 0.875rem;
}

.slice-label {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  font-weight: 500;
}

.type-dot {
  width: 8px;
  height: 8px;
  border-radius: 50%;
}

.distribution-legend {
  display: flex;
  flex-direction: column;
  gap: 0.5rem;
  min-width: 200px;
}

.legend-item {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  font-size: 0.875rem;
}

.legend-color {
  width: 12px;
  height: 12px;
  border-radius: 2px;
}

.legend-label {
  flex: 1;
  color: #6b7280;
}

.legend-commission {
  font-weight: 600;
  color: #1f2937;
}

/* Projection Section */
.projection-section {
  display: flex;
  flex-direction: column;
  gap: 1rem;
}

.projection-item {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 0.75rem;
  background: #f0fdf4;
  border-radius: 6px;
  border: 1px solid #dcfce7;
}

.projection-label {
  color: #166534;
  font-size: 0.85rem;
  font-weight: 500;
}

.projection-value {
  font-weight: 700;
  color: #166534;
  font-size: 1rem;
}

.projection-note {
  font-size: 0.75rem;
  color: #6b7280;
  font-style: italic;
  text-align: center;
}

/* Table Styles */
.table-total {
  background: #f8fafc;
  font-weight: 600;
}

.item-commission {
  font-weight: 600;
  color: #ef4444;
}

.item-net {
  font-weight: 600;
  color: #10b981;
}

/* Rest of the existing CSS styles remain the same */
/* ... (all previous CSS styles) ... */
</style>

<style scoped>
.admin-dashboard {
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

.admin-info h2 {
  font-size: 1.3rem;
  margin: 0;
  color: #1a202c;
  font-weight: 600;
}

.highlight {
  color: #FF7828;
  font-weight: 700;
}

.admin-name {
  margin: 0;
  color: #718096;
  font-size: 0.95rem;
  font-weight: 500;
}

.header-actions {
  display: flex;
  gap: 12px;
}

.settings-btn, .logout-btn {
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

.settings-btn {
  background: white;
  color: #4a5568;
  border: 2px solid #e2e8f0;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
}

.settings-btn:hover {
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

.card-icon.total-users {
  background: rgba(139, 92, 246, 0.1);
  color: #8b5cf6;
}

.card-icon.total-orders {
  background: rgba(255, 120, 40, 0.1);
  color: #FF7828;
}

.card-icon.total-revenue {
  background: rgba(16, 185, 129, 0.1);
  color: #10b981;
}

.card-icon.active-users {
  background: rgba(59, 130, 246, 0.1);
  color: #3b82f6;
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

.user-breakdown {
  display: flex;
  flex-direction: column;
  gap: 0.25rem;
}

.user-breakdown small {
  color: #718096;
  font-size: 0.8rem;
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

/* Analytics Grid */
.analytics-grid {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 1.5rem;
}

.analytics-column {
  display: flex;
  flex-direction: column;
  gap: 1.5rem;
}

.analytics-card {
  background: #f8fafc;
  border-radius: 12px;
  padding: 1.5rem;
  border: 1px solid #e2e8f0;
}

.analytics-card .card-header {
  margin-bottom: 1.5rem;
}

.analytics-card .card-header h3 {
  margin: 0 0 0.25rem 0;
  color: #1a202c;
  font-size: 1.1rem;
  font-weight: 600;
}

.analytics-card .card-header p {
  margin: 0;
  color: #718096;
  font-size: 0.9rem;
}

/* Completion Rate */
.completion-rate {
  display: flex;
  align-items: center;
  gap: 2rem;
}

.completion-circle {
  position: relative;
  width: 120px;
  height: 120px;
}

.circle-progress {
  width: 100%;
  height: 100%;
  border-radius: 50%;
  background: conic-gradient(#FF7828 calc(var(--progress) * 3.6deg), #e2e8f0 0deg);
  display: flex;
  align-items: center;
  justify-content: center;
  position: relative;
}

.circle-progress::before {
  content: '';
  position: absolute;
  width: 80px;
  height: 80px;
  background: white;
  border-radius: 50%;
}

.percentage {
  position: relative;
  font-size: 1.5rem;
  font-weight: 700;
  color: #1a202c;
  z-index: 1;
}

.completion-stats {
  flex: 1;
}

.stats-text {
  margin: 0;
  color: #718096;
  font-size: 0.9rem;
  line-height: 1.5;
}

/* Average Order Value */
.average-value {
  text-align: center;
  padding: 1rem 0;
}

.value-display {
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 0.5rem;
  margin-bottom: 0.5rem;
}

.currency {
  font-size: 1.5rem;
  color: #10b981;
  font-weight: 600;
}

.amount {
  font-size: 2.5rem;
  font-weight: 700;
  color: #1a202c;
}

.value-subtitle {
  margin: 0;
  color: #718096;
  font-size: 0.9rem;
}

/* Active Users Stats */
.active-users-stats {
  display: flex;
  flex-direction: column;
  gap: 1rem;
}

.users-count {
  text-align: center;
}

.users-count .count {
  font-size: 2rem;
  margin-bottom: 0.25rem;
}

.users-count .total {
  color: #718096;
  font-size: 0.9rem;
}

.users-chart {
  display: flex;
  align-items: flex-end;
  justify-content: space-between;
  height: 120px;
  padding: 0 0.5rem;
}

.chart-bar {
  flex: 1;
  background: #e2e8f0;
  border-radius: 4px 4px 0 0;
  margin: 0 0.25rem;
  position: relative;
  transition: all 0.3s ease;
  min-height: 20px;
}

.chart-bar.active {
  background: #3b82f6;
}

.chart-bar:hover {
  transform: scaleY(1.05);
}

.bar-label {
  position: absolute;
  bottom: -25px;
  left: 50%;
  transform: translateX(-50%);
  font-size: 0.75rem;
  color: #718096;
  white-space: nowrap;
}

/* User Distribution */
.user-distribution {
  display: flex;
  flex-direction: column;
  gap: 1rem;
}

.distribution-item {
  display: flex;
  flex-direction: column;
  gap: 0.5rem;
}

.distribution-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.user-type {
  font-weight: 600;
  color: #1a202c;
  font-size: 0.9rem;
}

.user-count {
  color: #718096;
  font-size: 0.8rem;
}

.distribution-bar {
  height: 8px;
  background: #e2e8f0;
  border-radius: 4px;
  overflow: hidden;
}

.bar-fill {
  height: 100%;
  border-radius: 4px;
  transition: width 0.3s ease;
}

.bar-fill.customers {
  background: #8b5cf6;
}

.bar-fill.retailers {
  background: #10b981;
}

.bar-fill.delivery {
  background: #3b82f6;
}

.distribution-percentage {
  text-align: right;
  font-size: 0.8rem;
  color: #718096;
  font-weight: 600;
}

/* Revenue Trends */
.revenue-trends {
  padding: 1rem 0;
}

.trends-chart {
  display: flex;
  align-items: flex-end;
  justify-content: space-between;
  height: 150px;
  padding: 0 0.5rem;
}

.chart-column {
  display: flex;
  flex-direction: column;
  align-items: center;
  flex: 1;
  margin: 0 0.25rem;
}

.column-bar {
  width: 100%;
  background: #FF7828;
  border-radius: 4px 4px 0 0;
  position: relative;
  transition: all 0.3s ease;
  min-height: 20px;
}

.column-bar:hover {
  transform: scaleY(1.05);
  background: #e6691f;
}

.column-value {
  position: absolute;
  top: -25px;
  left: 50%;
  transform: translateX(-50%);
  font-size: 0.75rem;
  color: #1a202c;
  font-weight: 600;
  white-space: nowrap;
}

.column-label {
  margin-top: 0.5rem;
  font-size: 0.75rem;
  color: #718096;
}

/* Performance Metrics */
.performance-metrics {
  display: flex;
  flex-direction: column;
  gap: 1rem;
}

.metric-item {
  display: flex;
  align-items: center;
  gap: 1rem;
  padding: 1rem;
  background: white;
  border-radius: 8px;
  border: 1px solid #e2e8f0;
}

.metric-icon {
  width: 40px;
  height: 40px;
  border-radius: 8px;
  display: flex;
  align-items: center;
  justify-content: center;
}

.metric-icon.success {
  background: rgba(16, 185, 129, 0.1);
  color: #10b981;
}

.metric-icon.warning {
  background: rgba(245, 158, 11, 0.1);
  color: #f59e0b;
}

.metric-icon.error {
  background: rgba(239, 68, 68, 0.1);
  color: #ef4444;
}

.metric-value {
  margin: 0;
  font-size: 1.25rem;
  font-weight: 700;
  color: #1a202c;
}

.metric-label {
  margin: 0;
  color: #718096;
  font-size: 0.85rem;
}

/* Coming Soon Sections */
.coming-soon {
  text-align: center;
  padding: 4rem 2rem;
  color: #718096;
}

.coming-soon h3 {
  margin: 1rem 0 0.5rem 0;
  color: #4a5568;
  font-size: 1.25rem;
}

.coming-soon p {
  margin-bottom: 1.5rem;
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

/* Filter Controls */
.filter-controls {
  display: flex;
  gap: 2rem;
  align-items: end;
  margin-top: 1.5rem;
  padding: 1.5rem;
  background: #f8fafc;
  border-radius: 12px;
  border: 1px solid #e2e8f0;
}

.filter-group {
  display: flex;
  flex-direction: column;
  gap: 0.5rem;
}

.filter-group label {
  font-weight: 600;
  color: #374151;
  font-size: 0.875rem;
}

.date-inputs {
  display: flex;
  align-items: center;
  gap: 0.5rem;
}

.date-input {
  padding: 0.5rem;
  border: 1px solid #d1d5db;
  border-radius: 6px;
  font-size: 0.875rem;
}

.date-separator {
  color: #6b7280;
  font-size: 0.875rem;
}

.quick-filters {
  display: flex;
  gap: 0.5rem;
}

.filter-btn {
  padding: 0.5rem 1rem;
  border: 1px solid #d1d5db;
  border-radius: 6px;
  background: white;
  color: #374151;
  font-size: 0.875rem;
  cursor: pointer;
  transition: all 0.2s ease;
}

.filter-btn:hover {
  border-color: #FF7828;
  color: #FF7828;
}

.filter-btn.active {
  background: #FF7828;
  border-color: #FF7828;
  color: white;
}

.report-select {
  padding: 0.5rem;
  border: 1px solid #d1d5db;
  border-radius: 6px;
  background: white;
  color: #374151;
  font-size: 0.875rem;
}

.action-buttons {
  display: flex;
  gap: 0.5rem;
}

.export-btn {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  padding: 0.5rem 1rem;
  background: #10b981;
  color: white;
  border: none;
  border-radius: 6px;
  font-size: 0.875rem;
  cursor: pointer;
  transition: all 0.2s ease;
}

.export-btn:hover {
  background: #059669;
  transform: translateY(-1px);
}

.print-btn {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  padding: 0.5rem 1rem;
  background: #3b82f6;
  color: white;
  border: none;
  border-radius: 6px;
  font-size: 0.875rem;
  cursor: pointer;
  transition: all 0.2s ease;
}

.print-btn:hover {
  background: #2563eb;
  transform: translateY(-1px);
}

.summary-cards {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
  gap: 1.5rem;
  margin-bottom: 2rem;
}

.summary-card {
  background: white;
  padding: 1.5rem;
  border-radius: 12px;
  border: 1px solid #e2e8f0;
  text-align: center;
}

.summary-card .card-icon {
  width: 48px;
  height: 48px;
  border-radius: 10px;
  display: flex;
  align-items: center;
  justify-content: center;
  margin: 0 auto 1rem;
}

.summary-card .card-icon.total-orders {
  background: rgba(255, 120, 40, 0.1);
  color: #FF7828;
}

.summary-card .card-icon.total-revenue {
  background: rgba(16, 185, 129, 0.1);
  color: #10b981;
}

.summary-card .card-icon.success-rate {
  background: rgba(34, 197, 94, 0.1);
  color: #22c55e;
}

.summary-card .card-icon.average-value {
  background: rgba(139, 92, 246, 0.1);
  color: #8b5cf6;
}

.summary-card h3 {
  margin: 0 0 0.5rem 0;
  color: #6b7280;
  font-size: 0.875rem;
  font-weight: 600;
  text-transform: uppercase;
  letter-spacing: 0.5px;
}

.summary-card .count {
  font-size: 2rem;
  font-weight: 700;
  color: #1f2937;
  margin: 0 0 0.5rem 0;
}

.summary-card .card-subtitle {
  margin: 0;
  color: #9ca3af;
  font-size: 0.875rem;
}

.status-breakdown {
  display: flex;
  flex-direction: column;
  gap: 0.5rem;
  margin-top: 1rem;
}

.status-item {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  font-size: 0.875rem;
  color: #6b7280;
}

.status-dot {
  width: 8px;
  height: 8px;
  border-radius: 50%;
}

.status-item.completed .status-dot {
  background: #10b981;
}

.status-item.pending .status-dot {
  background: #f59e0b;
}

.status-item.cancelled .status-dot {
  background: #ef4444;
}

.status-distribution {
  display: flex;
  gap: 2rem;
  align-items: center;
}

.distribution-chart {
  flex: 1;
  display: flex;
  flex-direction: column;
  gap: 0.5rem;
}

.chart-slice {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  padding: 0.5rem;
  background: linear-gradient(
    to right,
    var(--color) 0%,
    var(--color) var(--percentage),
    #e5e7eb var(--percentage),
    #e5e7eb 100%
  );
  border-radius: 4px;
  font-size: 0.875rem;
}

.slice-label {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  font-weight: 500;
}

.distribution-legend {
  display: flex;
  flex-direction: column;
  gap: 0.5rem;
}

.legend-item {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  font-size: 0.875rem;
}

.legend-color {
  width: 12px;
  height: 12px;
  border-radius: 2px;
}

.legend-label {
  flex: 1;
  color: #6b7280;
}

.legend-count {
  font-weight: 600;
  color: #1f2937;
}

.top-list {
  display: flex;
  flex-direction: column;
  gap: 0.75rem;
}

.list-item {
  display: flex;
  align-items: center;
  gap: 1rem;
  padding: 0.75rem;
  background: white;
  border-radius: 8px;
  border: 1px solid #e5e7eb;
}

.item-rank {
  width: 24px;
  height: 24px;
  background: #f3f4f6;
  border-radius: 6px;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 0.75rem;
  font-weight: 600;
  color: #6b7280;
}

.item-info {
  flex: 1;
}

.item-name {
  margin: 0 0 0.25rem 0;
  font-weight: 600;
  color: #1f2937;
  font-size: 0.875rem;
}

.item-details {
  margin: 0;
  color: #6b7280;
  font-size: 0.75rem;
}

.item-value {
  font-weight: 600;
  color: #10b981;
  font-size: 0.875rem;
}

.analytics-full-width {
  grid-column: 1 / -1;
}

.data-table-container {
  overflow-x: auto;
}

.data-table {
  width: 100%;
  border-collapse: collapse;
}

.data-table th,
.data-table td {
  padding: 0.75rem;
  text-align: left;
  border-bottom: 1px solid #e5e7eb;
  font-size: 0.875rem;
}

.data-table th {
  background: #f9fafb;
  color: #6b7280;
  font-weight: 600;
  text-transform: uppercase;
  letter-spacing: 0.5px;
}

.item-id {
  font-family: monospace;
  color: #6b7280;
}

.item-customer,
.item-retailer {
  font-weight: 500;
  color: #1f2937;
}

.item-amount {
  font-weight: 600;
  color: #10b981;
}

.status-badge {
  padding: 0.25rem 0.5rem;
  border-radius: 12px;
  font-size: 0.75rem;
  font-weight: 600;
  text-transform: capitalize;
}

.status-badge.accepted,
.status-badge.delivered,
.status-badge.completed {
  background: #d1fae5;
  color: #065f46;
}

.status-badge.pending,
.status-badge.assigned {
  background: #fef3c7;
  color: #92400e;
}

.status-badge.in-transit {
  background: #dbeafe;
  color: #1e40af;
}

.status-badge.cancelled {
  background: #fee2e2;
  color: #991b1b;
}

.item-date {
  color: #6b7280;
  white-space: nowrap;
}

.no-data {
  text-align: center;
  padding: 2rem;
  color: #9ca3af;
}

/* Printable Report Styles */
.printable-report {
  display: none;
  font-family: 'Arial', sans-serif;
  max-width: 8.5in;
  margin: 0 auto;
  padding: 0.5in;
  background: white;
  color: black;
}

@media print {
  .printable-report {
    display: block !important;
  }
}

.report-header {
  display: flex;
  justify-content: space-between;
  align-items: flex-start;
  margin-bottom: 2rem;
  padding-bottom: 1rem;
  border-bottom: 2px solid #333;
}

.company-info h1 {
  margin: 0 0 0.25rem 0;
  font-size: 24px;
  color: #1a202c;
}

.company-info p {
  margin: 0;
  color: #666;
  font-size: 14px;
}

.report-info h2 {
  margin: 0 0 0.5rem 0;
  font-size: 20px;
  color: #1a202c;
  text-align: right;
}

.report-period {
  margin: 0 0 0.25rem 0;
  font-size: 14px;
  color: #666;
  text-align: right;
}

.report-date {
  margin: 0;
  font-size: 12px;
  color: #999;
  text-align: right;
}

.report-summary {
  margin-bottom: 2rem;
  padding: 1rem;
  background: #f8fafc;
  border-radius: 4px;
}

.report-summary h3 {
  margin: 0 0 1rem 0;
  font-size: 16px;
  color: #1a202c;
}

.summary-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
  gap: 1rem;
}

.summary-item {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 0.5rem;
  background: white;
  border-radius: 4px;
  border: 1px solid #e2e8f0;
}

.summary-item .label {
  font-weight: 600;
  color: #4a5568;
  font-size: 14px;
}

.summary-item .value {
  font-weight: 700;
  color: #1a202c;
  font-size: 14px;
}

.report-details {
  margin-bottom: 2rem;
}

.report-details h3 {
  margin: 0 0 1rem 0;
  font-size: 16px;
  color: #1a202c;
}

.report-table {
  width: 100%;
  border-collapse: collapse;
  font-size: 12px;
}

.report-table th {
  background: #1a202c;
  color: white;
  padding: 0.75rem;
  text-align: left;
  font-weight: 600;
  border: 1px solid #2d3748;
}

.report-table td {
  padding: 0.5rem;
  border: 1px solid #e2e8f0;
}

.report-table tfoot {
  background: #f7fafc;
}

.report-table tfoot td {
  font-weight: 600;
  border-top: 2px solid #1a202c;
}

.monospace {
  font-family: 'Courier New', monospace;
  font-size: 11px;
}

.text-center {
  text-align: center;
}

.text-right {
  text-align: right;
}

.total-amount {
  color: #059669;
  font-size: 14px;
}

.report-status-summary {
  margin-bottom: 2rem;
}

.report-status-summary h3 {
  margin: 0 0 1rem 0;
  font-size: 16px;
  color: #1a202c;
}

.status-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
  gap: 0.5rem;
}

.status-item {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 0.5rem;
  background: #f8fafc;
  border-radius: 4px;
  font-size: 14px;
}

.status-label {
  font-weight: 600;
  color: #4a5568;
}

.status-count {
  color: #1a202c;
}

.report-footer {
  display: flex;
  justify-content: space-between;
  align-items: flex-end;
  margin-top: 3rem;
  padding-top: 1rem;
  border-top: 1px solid #e2e8f0;
}

.footer-signature {
  text-align: center;
}

.footer-signature p:first-child {
  margin: 0 0 0.5rem 0;
  color: #666;
}

.footer-signature p:last-child {
  margin: 0;
  font-size: 12px;
  color: #666;
}

.footer-info {
  text-align: right;
}

.footer-info p {
  margin: 0 0 0.25rem 0;
  font-size: 12px;
  color: #666;
}

/* Print Preview Modal */
.print-preview .modal {
  max-width: 500px;
}

.print-modal .modal-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 1rem;
  padding-bottom: 1rem;
  border-bottom: 1px solid #e2e8f0;
}

.print-modal .modal-header h3 {
  margin: 0;
  color: #1a202c;
}

.close-btn {
  background: none;
  border: none;
  font-size: 24px;
  color: #666;
  cursor: pointer;
  padding: 0;
  width: 30px;
  height: 30px;
  display: flex;
  align-items: center;
  justify-content: center;
}

.close-btn:hover {
  color: #333;
}

.print-options {
  display: flex;
  flex-direction: column;
  gap: 0.75rem;
  margin: 1.5rem 0;
}

.print-options label {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  cursor: pointer;
  font-size: 14px;
  color: #4a5568;
}

.print-options input[type="checkbox"] {
  width: 16px;
  height: 16px;
}

/* Responsive Design */
@media (max-width: 1024px) {
  .analytics-grid {
    grid-template-columns: 1fr;
  }
  
  .filter-controls {
    flex-direction: column;
    align-items: stretch;
    gap: 1rem;
  }
  
  .date-inputs {
    justify-content: space-between;
  }
  
  .status-distribution {
    flex-direction: column;
    align-items: stretch;
  }
}

@media (max-width: 768px) {
  .admin-dashboard {
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
  
  .completion-rate {
    flex-direction: column;
    text-align: center;
    gap: 1rem;
  }
  
  .users-chart,
  .trends-chart {
    height: 100px;
  }
  
  .modal-buttons {
    flex-direction: column;
  }
  
  .quick-filters {
    flex-wrap: wrap;
  }
  
  .data-table {
    min-width: 800px;
  }
  
  .action-buttons {
    flex-direction: column;
  }
}
</style>

<!-- Add this style tag for print-specific styles -->
<style id="print-styles">
@media print {
  body {
    margin: 0;
    padding: 0;
    background: white;
  }
  
  .printable-report {
    font-family: 'Arial', sans-serif;
    font-size: 12px;
    line-height: 1.4;
    color: black;
  }
  
  .report-table {
    font-size: 10px;
  }
  
  .report-table th {
    background: #333 !important;
    color: white !important;
    -webkit-print-color-adjust: exact;
  }
  
  .status-badge {
    padding: 2px 6px;
    border-radius: 8px;
    font-size: 9px;
    font-weight: 600;
  }
  
  .status-badge.accepted,
  .status-badge.delivered,
  .status-badge.completed {
    background: #d1fae5 !important;
    color: #065f46 !important;
    -webkit-print-color-adjust: exact;
  }
  
  .status-badge.pending,
  .status-badge.assigned {
    background: #fef3c7 !important;
    color: #92400e !important;
    -webkit-print-color-adjust: exact;
  }
  
  .status-badge.in-transit {
    background: #dbeafe !important;
    color: #1e40af !important;
    -webkit-print-color-adjust: exact;
  }
  
  .status-badge.cancelled {
    background: #fee2e2 !important;
    color: #991b1b !important;
    -webkit-print-color-adjust: exact;
  }
  
  .no-print {
    display: none !important;
  }
  
  @page {
    margin: 0.5in;
  }
}
</style>