<template>
  <div class="inventory-dashboard">
    <!-- Inventory Section -->
    <section class="inventory-section">
      <div class="section-header">
        <div class="header-content">
          <h2>Inventory Management</h2>
          <p>Monitor and manage your stock levels</p>
        </div>
        <button class="add-btn" @click="openAddForm">
          <svg width="16" height="16" viewBox="0 0 24 24" fill="none">
            <path d="M12 5v14m-7-7h14" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
          </svg>
          Add Item
        </button>
      </div>

      <div class="table-container">
        <table class="inventory-table">
          <thead>
            <tr>
              <th>Product Type</th>
              <th>Tank Size</th>
              <th>Current Stock</th>
              <th>Stock Level</th>
              <th>Price</th>
              <th>Last Restocked</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="item in inventory" :key="item.id" :class="{ 'low-stock-row': item.isLow }">
              <td class="product-type">
                <span>{{ item.type }}</span>
                <span v-if="item.isLow" class="low-stock-indicator">Low Stock</span>
              </td>
              <td>{{ item.tankSize }}</td>
              <td>
                <div class="stock-count">
                  {{ item.currentStock }} units
                </div>
              </td>
              <td>
                <div class="stock-level">
                  <div class="bar-container">
                    <div
                      class="bar"
                      :class="{ 
                        'low': item.isLow, 
                        'critical': item.currentStock <= item.minStock * 0.5 
                      }"
                      :style="{ width: Math.min(item.stockLevel, 100) + '%' }"
                    ></div>
                  </div>
                  <small>Min: {{ item.minStock }}</small>
                </div>
              </td>
              <td class="price">₱{{ item.price.toLocaleString() }}</td>
              <td>{{ formatDate(item.lastRestocked) }}</td>
              <td class="action-btns">
                <button class="edit-btn" @click="openEditForm(item)">
                  <svg width="14" height="14" viewBox="0 0 24 24" fill="none">
                    <path d="M11 4H4a2 2 0 00-2 2v14a2 2 0 002 2h14a2 2 0 002-2v-7" stroke="currentColor" stroke-width="2"/>
                    <path d="M18.5 2.5a2.121 2.121 0 013 3L12 15l-4 1 1-4 9.5-9.5z" stroke="currentColor" stroke-width="2"/>
                  </svg>
                </button>
                <button class="restock-btn" @click="restockItem(item)">
                  <svg width="14" height="14" viewBox="0 0 24 24" fill="none">
                    <path d="M23 4v6h-6M1 20v-6h6" stroke="currentColor" stroke-width="2"/>
                    <path d="M3.51 9a9 9 0 0114.85-3.36L23 10M1 14l4.64 4.36A9 9 0 0020.49 15" stroke="currentColor" stroke-width="2"/>
                  </svg>
                </button>
                <button class="delete-btn" @click="deleteItem(item.id)">
                  <svg width="14" height="14" viewBox="0 0 24 24" fill="none">
                    <path d="M3 6h18m-2 0v14a2 2 0 01-2 2H7a2 2 0 01-2-2V6m3 0V4a2 2 0 012-2h4a2 2 0 012 2v2" stroke="currentColor" stroke-width="2"/>
                  </svg>
                </button>
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <div v-if="inventory.length === 0" class="empty-state">
        <svg width="48" height="48" viewBox="0 0 24 24" fill="none" class="empty-icon">
          <path d="M3 7v14a2 2 0 002 2h14a2 2 0 002-2V7m-4-4H7a2 2 0 00-2 2v14a2 2 0 002 2h10a2 2 0 002-2V5a2 2 0 00-2-2z" stroke="#9CA3AF" stroke-width="2"/>
        </svg>
        <h3>No inventory items</h3>
        <p>Get started by adding your first product</p>
        <button class="add-btn primary" @click="openAddForm">
          <svg width="16" height="16" viewBox="0 0 24 24" fill="none">
            <path d="M12 5v14m-7-7h14" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
          </svg>
          Add First Item
        </button>
      </div>
    </section>

    <!-- Add/Edit Modal -->
    <div v-if="showForm" class="modal-overlay">
      <div class="modal">
        <div class="modal-header">
          <h3>{{ editMode ? "Edit Item" : "Add New Item" }}</h3>
          <button class="close-btn" @click="closeForm">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none">
              <path d="M18 6L6 18" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
              <path d="M6 6l12 12" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
            </svg>
          </button>
        </div>

        <form @submit.prevent="saveItem" class="modal-form">
          <div class="form-group">
            <label>Product Type</label>
            <input v-model="form.type" required placeholder="e.g., LPG Gas" />
          </div>

          <div class="form-group">
            <label>Tank Size</label>
            <input v-model="form.tankSize" required placeholder="e.g., 11kg" />
          </div>

          <div class="form-row">
            <div class="form-group">
              <label>Current Stock</label>
              <input v-model.number="form.currentStock" type="number" required min="0" />
            </div>
            <div class="form-group">
              <label>Minimum Stock</label>
              <input v-model.number="form.minStock" type="number" required min="0" />
            </div>
          </div>

          <div class="form-group">
            <label>Price (₱)</label>
            <input v-model.number="form.price" type="number" required min="0" step="0.01" />
          </div>

          <div class="form-group">
            <label>Last Restocked</label>
            <input v-model="form.lastRestocked" type="date" required />
          </div>

          <div class="form-buttons">
            <button type="button" class="cancel-btn" @click="closeForm">Cancel</button>
            <button type="submit" class="save-btn">
              {{ editMode ? "Update Item" : "Add Item" }}
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from "vue";
import { usePage } from "@inertiajs/vue3"; // CHANGED: Import usePage instead of router
import {
  collection,
  getDocs,
  addDoc,
  updateDoc,
  deleteDoc,
  doc,
  onSnapshot,
  query,
  where
} from "firebase/firestore";
import { db } from "@/firebase";

// COMPLETELY NEW getBusinessName function - Comprehensive solution
const getBusinessName = () => {
  try {
    console.log("=== DEBUGGING BUSINESS NAME ===");
    
    // 1. Try Inertia props first
    const { props } = usePage();
    console.log("Inertia props:", props);
    
    if (props?.auth?.user?.businessName) {
      console.log("Found in props.auth.user.businessName:", props.auth.user.businessName);
      return props.auth.user.businessName;
    }
    if (props?.user?.businessName) {
      console.log("Found in props.user.businessName:", props.user.businessName);
      return props.user.businessName;
    }
    if (props?.businessName) {
      console.log("Found in props.businessName:", props.businessName);
      return props.businessName;
    }

    // 2. Try localStorage with multiple possible keys
    const possibleLocalStorageKeys = [
      'retailers', 'retailer', 'user', 'auth', 'business', 
      'currentUser', 'loggedInUser', 'userData'
    ];
    
    for (const key of possibleLocalStorageKeys) {
      const data = localStorage.getItem(key);
      if (data) {
        console.log(`Found data in localStorage.${key}:`, data);
        try {
          const parsed = JSON.parse(data);
          const businessName = parsed?.businessName || parsed?.companyName || parsed?.storeName || parsed?.name;
          if (businessName) {
            console.log(`Found business name in localStorage.${key}:`, businessName);
            return businessName;
          }
        } catch (e) {
          console.log(`Could not parse localStorage.${key}`);
        }
      }
    }

    // 3. Try sessionStorage
    for (const key of possibleLocalStorageKeys) {
      const data = sessionStorage.getItem(key);
      if (data) {
        console.log(`Found data in sessionStorage.${key}:`, data);
        try {
          const parsed = JSON.parse(data);
          const businessName = parsed?.businessName || parsed?.companyName || parsed?.storeName || parsed?.name;
          if (businessName) {
            console.log(`Found business name in sessionStorage.${key}:`, businessName);
            return businessName;
          }
        } catch (e) {
          console.log(`Could not parse sessionStorage.${key}`);
        }
      }
    }

    // 4. Manual fallback - ask user for business name
    console.log("No business name found in storage, using manual input");
    const manualBusinessName = "Ely Gas"; // You can change this to match your retailer
    console.log("Using manual business name:", manualBusinessName);
    return manualBusinessName;

  } catch (error) {
    console.error("Error in getBusinessName:", error);
    return "Ely Gas"; // Fallback manual business name
  }
};

const showForm = ref(false);
const editMode = ref(false);
const form = ref({
  type: "",
  tankSize: "",
  currentStock: 0,
  minStock: 0,
  price: 0,
  lastRestocked: "",
});

const inventory = ref([]);

// Fetch inventory from Firestore on page load - FILTERED BY BUSINESS
const fetchInventory = async () => {
  const businessName = getBusinessName();
  
  if (!businessName) {
    console.error("No business name found");
    inventory.value = [];
    alert("Cannot load inventory: Business name not found. Please ensure you are logged in.");
    return;
  }

  console.log("Fetching inventory for business:", businessName);

  try {
    const querySnapshot = await getDocs(collection(db, "inventory"));
    const inventoryList = querySnapshot.docs
      .map((docItem) => {
        const data = docItem.data();
        return {
          id: docItem.id,
          ...data,
          isLow: data.currentStock < data.minStock,
          stockLevel: (data.currentStock / data.minStock) * 100,
        };
      })
      .filter(item => item.addedByBusiness === businessName);

    console.log("Filtered inventory:", inventoryList);
    inventory.value = inventoryList;
  } catch (error) {
    console.error("Error fetching inventory:", error);
    inventory.value = [];
  }
};

// Real-time listener para sa orders at automatic stock deduction
const setupOrderListener = () => {
  const businessName = getBusinessName();
  
  if (!businessName) {
    console.error("No business name found for order listener");
    return;
  }

  // Listen sa orders collection kung saan ang retailer ay match sa current business
  const ordersQuery = query(
    collection(db, "orders"),
    where("retailerName", "==", businessName),
    where("orderStatus", "in", ["accepted", "assigned", "ready-for-pickup", "in-transit"])
  );

  const unsubscribe = onSnapshot(ordersQuery, (snapshot) => {
    snapshot.docChanges().forEach(async (change) => {
      if (change.type === "added") {
        const order = change.doc.data();
        console.log("New order detected:", order);
        
        // I-deduct ang stock para sa bawat item sa order
        await processOrderStockDeduction(order);
      }
    });
  });

  return unsubscribe;
};

// Process order at i-deduct ang stock
const processOrderStockDeduction = async (order) => {
  try {
    const businessName = getBusinessName();
    
    // Gamitin ang order data structure na nakalista sa order
    const itemToDeduct = {
      productType: order.gasBrand,
      tankSize: order.gasType,
      quantity: order.quantity
    };

    // Hanapin ang inventory item base sa product type at tank size
    const inventoryQuery = query(
      collection(db, "inventory"),
      where("addedByBusiness", "==", businessName),
      where("type", "==", itemToDeduct.productType),
      where("tankSize", "==", itemToDeduct.tankSize)
    );

    const querySnapshot = await getDocs(inventoryQuery);
    
    if (!querySnapshot.empty) {
      const inventoryDoc = querySnapshot.docs[0];
      const inventoryData = inventoryDoc.data();
      const newStock = inventoryData.currentStock - itemToDeduct.quantity;

      // I-update ang stock sa Firestore
      await updateDoc(doc(db, "inventory", inventoryDoc.id), {
        currentStock: Math.max(0, newStock), // Siguraduhing hindi negative
      });

      console.log(`Stock updated for ${itemToDeduct.productType} ${itemToDeduct.tankSize}: ${inventoryData.currentStock} -> ${newStock}`);
      
      // I-refresh ang local inventory data
      await fetchInventory();
    } else {
      console.warn(`Inventory item not found for: ${itemToDeduct.productType} ${itemToDeduct.tankSize}`);
    }
  } catch (error) {
    console.error("Error processing order stock deduction:", error);
  }
};

const formatDate = (dateString) => {
  if (!dateString) return 'Never';
  const date = new Date(dateString);
  return date.toLocaleDateString('en-US', { 
    year: 'numeric', 
    month: 'short', 
    day: 'numeric' 
  });
};

onMounted(() => {
  fetchInventory();
  const unsubscribe = setupOrderListener();
  
  // Cleanup listener kapag na-unmount ang component
  return () => {
    if (unsubscribe) unsubscribe();
  };
});

const openAddForm = () => {
  editMode.value = false;
  showForm.value = true;
  form.value = {
    type: "",
    tankSize: "",
    currentStock: 0,
    minStock: 0,
    price: 0,
    lastRestocked: new Date().toISOString().split('T')[0],
  };
};

const openEditForm = (item) => {
  const businessName = getBusinessName();
  
  // Siguraduhin na ang retailer ay nagmamay-ari ng item na ito
  if (item.addedByBusiness !== businessName) {
    alert("You can only edit your own items.");
    return;
  }

  editMode.value = true;
  showForm.value = true;
  form.value = { 
    ...item,
    lastRestocked: item.lastRestocked || new Date().toISOString().split('T')[0]
  };
};

const closeForm = () => {
  showForm.value = false;
};

const restockItem = async (item) => {
  const businessName = getBusinessName();
  
  // Siguraduhin na ang retailer ay nagmamay-ari ng item na ito
  if (item.addedByBusiness !== businessName) {
    alert("You can only restock your own items.");
    return;
  }

  const amount = parseInt(prompt(`Enter restock amount for ${item.type}:`, 10));
  if (!amount || amount <= 0) return;

  // Update current stock
  const newStock = item.currentStock + amount;

  // Update Firestore
  const itemDoc = doc(db, "inventory", item.id);
  await updateDoc(itemDoc, {
    currentStock: newStock,
    lastRestocked: new Date().toISOString().split('T')[0],
  });

  // Refresh inventory
  await fetchInventory();
};

// Save item (add or edit) in Firestore
const saveItem = async () => {
  const businessName = getBusinessName();
  console.log("Business name to be saved:", businessName);
  
  if (!businessName) {
    alert("Cannot save item: Business name not found. Please login again.");
    return;
  }
  
  const itemData = {
    type: form.value.type,
    tankSize: form.value.tankSize,
    currentStock: Number(form.value.currentStock),
    minStock: Number(form.value.minStock),
    price: Number(form.value.price),
    lastRestocked: form.value.lastRestocked,
    addedByBusiness: businessName,
    isLow: Number(form.value.currentStock) < Number(form.value.minStock),
    stockLevel: (Number(form.value.currentStock) / Number(form.value.minStock)) * 100,
  };

  console.log("Item data to be saved:", itemData);

  try {
    if (editMode.value) {
      const itemDoc = doc(db, "inventory", form.value.id);
      await updateDoc(itemDoc, itemData);
      console.log("Item updated successfully");
    } else {
      await addDoc(collection(db, "inventory"), itemData);
      console.log("Item added successfully");
    }
    
    await fetchInventory();
    closeForm();
    alert(editMode.value ? "Item updated successfully!" : "Item added successfully!");
  } catch (error) {
    console.error("Error saving item:", error);
    alert("Error saving item. Please try again.");
  }
};

// Firestore integration for delete
const deleteItem = async (id) => {
  const businessName = getBusinessName();
  
  // Hanapin ang item para ma-verify ang ownership
  const itemToDelete = inventory.value.find(item => item.id === id);
  
  if (!itemToDelete) {
    alert("Item not found.");
    return;
  }
  
  // Siguraduhin na ang retailer ay nagmamay-ari ng item na ito
  if (itemToDelete.addedByBusiness !== businessName) {
    alert("You can only delete your own items.");
    return;
  }

  if (confirm("Are you sure you want to delete this item?")) {
    try {
      const itemDoc = doc(db, "inventory", id);
      await deleteDoc(itemDoc);
      inventory.value = inventory.value.filter(item => item.id !== id);
      alert("Item deleted successfully!");
    } catch (error) {
      console.error("Error deleting item:", error);
      alert("Error deleting item. Please try again.");
    }
  }
};
</script>

<style scoped>
/* YOUR EXISTING STYLES REMAIN EXACTLY THE SAME */
.inventory-dashboard {
  font-family: 'Inter', 'Poppins', sans-serif;
  padding: 0;
}

/* Inventory Section */
.inventory-section {
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

.add-btn {
  display: flex;
  align-items: center;
  gap: 8px;
  padding: 0.875rem 1.5rem;
  background: #FF7828;
  color: white;
  border: none;
  border-radius: 12px;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.3s ease;
  font-size: 0.95rem;
  font-family: 'Inter', sans-serif;
  box-shadow: 0 4px 12px rgba(255, 120, 40, 0.3);
}

.add-btn:hover {
  background: #e6691f;
  transform: translateY(-1px);
  box-shadow: 0 6px 16px rgba(255, 120, 40, 0.4);
}

.add-btn.primary {
  background: #FF7828;
}

/* Table Container */
.table-container {
  overflow-x: auto;
}

.inventory-table {
  width: 100%;
  border-collapse: collapse;
  font-family: 'Inter', sans-serif;
}

.inventory-table th {
  background: #f8fafc;
  padding: 1rem 1.5rem;
  text-align: left;
  font-weight: 600;
  color: #4a5568;
  font-size: 0.9rem;
  text-transform: uppercase;
  letter-spacing: 0.5px;
  border-bottom: 1px solid #e2e8f0;
}

.inventory-table td {
  padding: 1.25rem 1.5rem;
  border-bottom: 1px solid #f1f5f9;
  color: #4a5568;
  font-size: 0.95rem;
}

.inventory-table tr:last-child td {
  border-bottom: none;
}

.inventory-table tr:hover {
  background: #f8fafc;
}

.low-stock-row {
  background: #fef2f2 !important;
  border-left: 4px solid #ef4444;
}

.low-stock-row:hover {
  background: #fecaca !important;
}

/* Product Type Column */
.product-type {
  display: flex;
  flex-direction: column;
  gap: 4px;
}

.low-stock-indicator {
  background: #ef4444;
  color: white;
  padding: 0.25rem 0.5rem;
  border-radius: 12px;
  font-size: 0.75rem;
  font-weight: 600;
  align-self: flex-start;
}

/* Stock Count */
.stock-count {
  font-weight: 600;
  color: #1a202c;
}

/* Stock Level */
.stock-level {
  display: flex;
  flex-direction: column;
  gap: 6px;
}

.bar-container {
  background: #e2e8f0;
  border-radius: 8px;
  height: 8px;
  width: 120px;
  overflow: hidden;
}

.bar {
  height: 8px;
  background: #10b981;
  border-radius: 8px;
  transition: all 0.3s ease;
}

.bar.low {
  background: #f59e0b;
}

.bar.critical {
  background: #ef4444;
}

.stock-level small {
  color: #718096;
  font-size: 0.8rem;
}

/* Price */
.price {
  font-weight: 700;
  color: #1a202c;
  font-size: 1rem;
}

/* Action Buttons */
.action-btns {
  display: flex;
  gap: 8px;
  justify-content: flex-start;
}

.edit-btn, .restock-btn, .delete-btn {
  display: flex;
  align-items: center;
  justify-content: center;
  width: 36px;
  height: 36px;
  border: none;
  border-radius: 8px;
  cursor: pointer;
  transition: all 0.3s ease;
}

.edit-btn {
  background: #dbeafe;
  color: #1d4ed8;
}

.edit-btn:hover {
  background: #bfdbfe;
  transform: translateY(-1px);
}

.restock-btn {
  background: #dcfce7;
  color: #16a34a;
}

.restock-btn:hover {
  background: #bbf7d0;
  transform: translateY(-1px);
}

.delete-btn {
  background: #fee2e2;
  color: #dc2626;
}

.delete-btn:hover {
  background: #fecaca;
  transform: translateY(-1px);
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
}

/* Modal Styles */
.modal-overlay {
  position: fixed;
  inset: 0;
  background: rgba(0, 0, 0, 0.6);
  display: flex;
  justify-content: center;
  align-items: center;
  z-index: 1000;
  padding: 1rem;
  backdrop-filter: blur(4px);
}

.modal {
  background: white;
  border-radius: 20px;
  box-shadow: 0 25px 80px rgba(0, 0, 0, 0.25);
  width: 500px;
  max-width: 95vw;
  max-height: 90vh;
  overflow: hidden;
  border: 1px solid #f1f5f9;
}

.modal-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 1.5rem 2rem;
  background: linear-gradient(135deg, #FF7828 0%, #e6691f 100%);
  color: white;
}

.modal-header h3 {
  margin: 0;
  font-size: 1.3rem;
  font-weight: 700;
  font-family: 'Inter', sans-serif;
}

.modal-header .close-btn {
  background: rgba(255, 255, 255, 0.15);
  border: none;
  color: white;
  width: 36px;
  height: 36px;
  border-radius: 10px;
  display: flex;
  align-items: center;
  justify-content: center;
  cursor: pointer;
  transition: all 0.3s ease;
}

.modal-header .close-btn:hover {
  background: rgba(255, 255, 255, 0.25);
}

.modal-form {
  padding: 2rem;
}

.form-group {
  margin-bottom: 1.5rem;
}

.form-row {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 1rem;
}

.form-group label {
  display: block;
  margin-bottom: 0.5rem;
  font-weight: 600;
  color: #374151;
  font-size: 0.9rem;
  font-family: 'Inter', sans-serif;
}

.form-group input {
  width: 100%;
  padding: 0.875rem 1rem;
  border: 2px solid #e2e8f0;
  border-radius: 10px;
  font-size: 0.95rem;
  transition: all 0.3s ease;
  background: white;
  font-family: 'Inter', sans-serif;
  color: #1a202c;
}

.form-group input:focus {
  outline: none;
  border-color: #FF7828;
  box-shadow: 0 0 0 3px rgba(255, 120, 40, 0.1);
}

.form-group input::placeholder {
  color: #9ca3af;
}

.form-buttons {
  display: flex;
  gap: 1rem;
  justify-content: flex-end;
  margin-top: 2rem;
}

.cancel-btn, .save-btn {
  padding: 0.875rem 2rem;
  border: none;
  border-radius: 10px;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.3s ease;
  font-size: 0.95rem;
  font-family: 'Inter', sans-serif;
}

.cancel-btn {
  background: #f7fafc;
  color: #4a5568;
  border: 2px solid #e2e8f0;
}

.cancel-btn:hover {
  background: #edf2f7;
  border-color: #cbd5e0;
}

.save-btn {
  background: #FF7828;
  color: white;
  box-shadow: 0 4px 12px rgba(255, 120, 40, 0.3);
}

.save-btn:hover {
  background: #e6691f;
  transform: translateY(-1px);
  box-shadow: 0 6px 16px rgba(255, 120, 40, 0.4);
}

/* Responsive Design */
@media (max-width: 768px) {
  .section-header {
    flex-direction: column;
    gap: 1rem;
    align-items: stretch;
  }
  
  .add-btn {
    align-self: flex-start;
  }
  
  .form-row {
    grid-template-columns: 1fr;
  }
  
  .action-btns {
    flex-direction: column;
  }
  
  .inventory-table th,
  .inventory-table td {
    padding: 1rem;
  }
}
</style>