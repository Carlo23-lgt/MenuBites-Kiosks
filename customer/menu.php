<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Self-Service Kiosk</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            position: relative;
            background: url('../assets/woodplate.png') no-repeat center center/cover;
            background-size: 100%;
            background-color: rgba(255, 255, 255, 0.67); /* 67% transparent */
            margin: 0;
            padding: 0;
            overflow-x: hidden;
        }
        .d-flex {
            position: relative;
            z-index: 10;
        }
        .overlay {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(255, 255, 255, 0.75);
            z-index: 1;
        }
        .content-wrapper {
            position: relative;
            z-index: 2;
        }
        .sidebar {
            width: 140px;
            background: rgba(255, 255, 255, 0.9);
            padding: 15px 10px;
            height: 100vh;
            position: fixed;
            left: 0;
            top: 0;
            z-index: 100;
            box-shadow: 2px 0px 10px rgba(0, 0, 0, 0.2);
            overflow-y: auto;
            scrollbar-width: thin;
            scrollbar-color: rgba(0, 0, 0, 0.2) transparent;
            scroll-behavior: smooth;
            border-right: 1px solid rgba(0, 0, 0, 0.1);
        }
        .sidebar::-webkit-scrollbar {
            width: 6px;
        }
        .sidebar::-webkit-scrollbar-track {
            background: transparent;
        }
        .sidebar::-webkit-scrollbar-thumb {
            background-color: rgba(0, 0, 0, 0.2);
            border-radius: 10px;
        }
        .content {
            margin-left: 160px;
            padding-right: 320px;
            height: 100vh;
            overflow-y: auto;
            scroll-behavior: smooth;
        }
        .food-card {
            border-radius: 10px;
            overflow: hidden;
            text-align: center;
            padding: 15px;
            cursor: pointer;
            background-color: white;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
            height: 280px;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        .food-card:hover {
            transform: scale(1.05);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        }
        .food-card img {
            width: 180px;
            height: 180px;
            object-fit: contain;
            border-radius: 5px;
            margin: 0 auto 15px;
        }
        .modal-content {
            border-radius: 10px;
        }
        .order-summary {
            position: fixed;
            top: 0;
            right: 0;
            width: 300px;
            background:rgba(225, 225, 225, 0.75);
            padding: 15px;
            border-left: 2px solid #ddd;
            height: 100vh;
            overflow-y: auto;
        }
        .category-btn {
            width: 120px;
            margin-bottom: 10px;
            padding: 10px 5px;
            border-radius: 10px;
            font-weight: 500;
            transition: all 0.3s ease;
            text-align: center;
            background-color: #0d6efd;
            color: white;
            border: 2px solid #074bba;
            font-size: 15px;
        }
        .category-btn:hover {
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            transform: translateY(-2px);
            background-color: #0b5ed7;
        }
        .category-btn.active {
            background-color: #074bba;
            color: white;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            font-weight: 600;
        }
        .btn-secondary {
            background-color: #6c757d;
            border-color: #6c757d;
        }
        .btn-primary {
            background-color: #0d6efd;
            border-color: #0d6efd;
        }
        .ingredient-checkbox:checked + label {
            color: #28a745;
            font-weight: 500;
        }
        .btn:hover {
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        .btn:active {
            transform: translateY(0);
        }
        .menu-item img {
            max-height: 100px;
            object-fit: cover;
        }
    </style>
</head>
<body>
    <div class="d-flex">
        <!-- Sidebar -->
        <div class="sidebar" id="category-sidebar">
            <a href="../home.php" class="btn btn-success category-btn mb-3 w-100">
                <i class="fas fa-home"></i> Home
            </a>
            <button class="btn btn-primary category-btn" data-category-id="all">All</button>
        </div>
        
        <div class="container content">
            <h3 style="color: black;">Our Menu</h3>
            <div class="row" id="menu-items-container"></div>
        </div>
        
      <!-- Order Summary -->
       <div class="order-summary shadow">
       <h5 class="border-bottom pb-2 mb-3">Order Summary</h5>
       <div id="order-summary-list" class="mb-3">
           <p class="text-center text-muted">No items in cart.</p>
       </div>
       <div class="border-top pt-3">
           <div class="d-flex justify-content-between mb-2">
               <span>Subtotal:</span>
               <span>₱<span id="subtotal-amount">0.00</span></span>
           </div>
           <div class="d-flex justify-content-between">
               <h5 class="fw-bold">Total:</h5>
               <h5 class="fw-bold text-primary">₱<span id="total-amount">0.00</span></h5>
           </div>
       </div>
       <div class="mt-3">
           <button class="btn btn-primary w-100 mb-2" id="view-order">
               <i class="fas fa-eye me-2"></i>View Order
           </button>
           <button class="btn btn-warning w-100 mb-2">
               <i class="fas fa-pause me-2"></i>Hold Order
           </button>
           <button class="btn btn-success w-100" id="proceed-order">
               <i class="fas fa-check me-2"></i>Proceed to Payment
           </button>
       </div>
</div>
    </div>

    
    
    <!-- Customization Modal -->
    <div class="modal fade" id="customizationModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content p-3">
                <h5 class="modal-title">Customize Your Order</h5>
                <div class="modal-body">
                    <p id="selected-item-name" class="mb-1"></p>
                    <p id="selected-item-description" class="text-muted small mb-3"></p>
                    <div class="mb-3">
                        <label>Total Price:</label>
                        <div id="selected-item-price" class="h5">₱0.00</div>
                    </div>
                    <label>Quantity:</label>
                    <input type="number" id="quantity" class="form-control" min="1" value="1">
                    
                    <div class="mt-3">
                        <label>Adds On:</label>
                        <div id="available-ingredients" class="border p-2 mb-2 rounded">
                            <!-- Ingredients checkboxes will be populated here -->
                            <div class="text-center py-2">Loading ingredients...</div>
                        </div>
                    </div>
                    
                    <div class="mt-2">
                        <label>Request:</label>
                        <input type="text" id="custom-instructions" class="form-control" placeholder="">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" id="add-to-cart-modal">Add to Cart</button>
                </div>
            </div>
        </div>
    </div>


    <div class="modal fade" id="orderTypeModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content p-3">
            <h5 class="modal-title">Select Order Type</h5>
            <div class="modal-body">
                <button class="btn btn-primary w-100 mb-2" id="dine-in">Dine-In</button>
                <button class="btn btn-secondary w-100" id="take-out">Take-Out</button>
            </div>
        </div>
    </div>
</div>

<!-- Table Selection Modal -->
<div class="modal fade" id="tableSelectionModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content p-3">
            <h5 class="modal-title">Select Table Number</h5>
            <div class="modal-body">
                <div id="tables-container" class="d-flex flex-wrap justify-content-center gap-2">
                    <!-- Tables will be dynamically generated here -->
                </div>
            </div>
        </div>
    </div>
</div>

<!-- View Order Modal -->
<div class="modal fade" id="viewOrderModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content p-3">
            <div class="modal-header">
                <h5 class="modal-title">Your Order</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div id="view-order-list"></div>
                <div id="view-order-empty" class="text-center d-none">
                    <p>Your cart is empty.</p>
                </div>
                <div class="border-top pt-3 mt-3">
                    <div class="d-flex justify-content-between mb-2">
                        <span>Subtotal:</span>
                        <span>₱<span id="view-subtotal-amount">0.00</span></span>
                    </div>
                    <div class="d-flex justify-content-between">
                        <h5 class="fw-bold">Total:</h5>
                        <h5 class="fw-bold text-primary">₱<span id="view-total-amount">0.00</span></h5>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-success" id="update-and-proceed" data-bs-dismiss="modal">Proceed to Payment</button>
            </div>
        </div>
    </div>
</div>

<!-- Edit Item Modal -->
<div class="modal fade" id="editItemModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content p-3">
            <h5 class="modal-title">Edit Item</h5>
            <div class="modal-body">
                <input type="hidden" id="edit-item-index">
                <div class="mb-3">
                    <label for="edit-item-name" class="form-label">Item Name</label>
                    <input type="text" class="form-control" id="edit-item-name" readonly>
                </div>
                <div class="mb-3">
                    <label for="edit-quantity" class="form-label">Quantity</label>
                    <input type="number" class="form-control" id="edit-quantity" min="1" value="1">
                </div>
                <div class="mb-3">
                    <label>Adds On:</label>
                    <div id="edit-available-ingredients" class="border p-2 mb-2 rounded">
                        <!-- Ingredients checkboxes will be populated here -->
                        <div class="text-center py-2">Loading ingredients...</div>
                    </div>
                </div>
                <div class="mb-3">
                    <label for="edit-custom-instructions" class="form-label">Request</label>
                    <input type="text" class="form-control" id="edit-custom-instructions" placeholder="Special instructions">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" id="save-edit">Save Changes</button>
            </div>
        </div>
    </div>
</div>



    
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        let selectedItem = {};
        let cartItems = []; // Array to store cart items
        let selectedTable = null;
        let orderType = ""; // Store selected order type
        let lastOrderId = null; // Store the last order ID
        let refreshInterval = null; // Store the refresh interval

        document.addEventListener("DOMContentLoaded", function () {
            // Fetch both menu items and ingredients
            Promise.all([
                fetch("fetch_menu.php").then(response => response.json()),
                fetch("fetch_ingredients.php").then(response => response.json()).catch(error => {
                    console.error("Error fetching ingredients:", error);
                    return { ingredients: [] };
                })
            ])
            .then(([menuData, ingredientsData]) => {
                let categorySidebar = document.getElementById("category-sidebar");
                let menuContainer = document.getElementById("menu-items-container");
                let availableIngredients = Array.isArray(ingredientsData.ingredients) ? ingredientsData.ingredients : [];
                
                // Make sure "All" button has consistent styling and behavior
                let allButton = document.querySelector("[data-category-id='all']");
                allButton.classList.remove("btn-secondary");
                allButton.classList.add("btn-primary", "active");
                
                // Add categories
                menuData.categories.forEach((category, index) => {
                    let btn = document.createElement("button");
                    btn.classList.add("btn", "btn-primary", "category-btn");
                    btn.textContent = category.name;
                    btn.dataset.categoryId = category.id;
                    categorySidebar.appendChild(btn);
                });
                
                // Apply animations and event listeners to ALL category buttons (including "All")
                document.querySelectorAll(".category-btn").forEach((button, index) => {
                    // Remove the initial opacity:0 that was making buttons invisible
                    // button.style.opacity = "0";
                    button.style.animation = `fadeIn 0.3s ease forwards`;
                    
                    button.addEventListener("click", function() {
                        // Remove active class from all buttons
                        document.querySelectorAll(".category-btn").forEach(btn => {
                            btn.classList.remove("active");
                            btn.style.animation = "";
                        });
                        
                        // Add active class to clicked button
                        this.classList.add("active");
                        
                        // Add pulse animation to the active button
                        this.style.animation = "pulse 0.5s ease";
                        
                        // Ensure the active button is visible in the sidebar
                        const sidebarElement = document.querySelector(".sidebar");
                        const buttonRect = this.getBoundingClientRect();
                        const sidebarRect = sidebarElement.getBoundingClientRect();
                        
                        // Check if button is not fully visible in the sidebar
                        if (buttonRect.top < sidebarRect.top || buttonRect.bottom > sidebarRect.bottom) {
                            // Calculate scroll position to make button visible
                            const scrollTop = this.offsetTop - sidebarElement.clientHeight / 2 + this.clientHeight / 2;
                            sidebarElement.scrollTo({
                                top: scrollTop,
                                behavior: "smooth"
                            });
                        }
                        
                        // Display menu items for this category
                        displayMenuItems(this.dataset.categoryId);
                        
                        // Smooth scroll to the top of menu items
                        setTimeout(() => {
                            document.querySelector(".content").scrollTo({
                                top: 0,
                                behavior: "smooth"
                            });
                        }, 100);
                    });
                });
                
                // Add animation keyframes to head
                const styleSheet = document.createElement("style");
                styleSheet.textContent = `
                    @keyframes fadeIn {
                        from { opacity: 0; transform: translateY(10px); }
                        to { opacity: 1; transform: translateY(0); }
                    }
                    @keyframes pulse {
                        0% { transform: scale(1); }
                        50% { transform: scale(1.05); }
                        100% { transform: scale(1); }
                    }
                `;
                document.head.appendChild(styleSheet);
                
                function displayMenuItems(categoryId) {
                    menuContainer.innerHTML = "";
                    menuData.menu_items.forEach((item, index) => {
                        if (categoryId === "all" || item.category_id == categoryId) {
                            const safeItem = {
                                id: item.id,
                                name: item.name,
                                price: item.price,
                                image: item.image,
                                category_id: item.category_id,
                                description: item.description,
                                stock: item.stock
                            };
                            
                            let itemHtml = `
                                <div class="col-md-3 mb-3">
                                    <div class="food-card bg-white p-3 shadow ${item.stock <= 0 ? 'opacity-50' : ''}" 
                                        data-bs-toggle="modal" 
                                        data-bs-target="${item.stock <= 0 ? '' : '#customizationModal'}" 
                                        data-item='${JSON.stringify(safeItem)}'>
                                        <img src="../assets/images/${item.image}" class="img-fluid" alt="${item.name}">
                                        <p class="mb-1">${item.name}</p>
                                        <p class="mb-0">₱${parseFloat(item.price).toFixed(2)}</p>
                                        ${item.stock <= 0 ? '<div class="text-danger mt-2">Currently Unavailable</div>' : ''}
                                    </div>
                                </div>
                            `;
                            menuContainer.innerHTML += itemHtml;
                        }
                    });
                }
                
                document.getElementById("menu-items-container").addEventListener("click", function(event) {
                    let target = event.target.closest(".food-card");
                    if (target) {
                        try {
                            selectedItem = JSON.parse(target.dataset.item);
                            if (selectedItem.stock <= 0) {
                                alert("This item is currently unavailable.");
                                return;
                            }
                            
                            document.getElementById("selected-item-name").textContent = selectedItem.name;
                            document.getElementById("selected-item-description").textContent = selectedItem.description;
                            
                            // Populate available ingredients
                            const ingredientsContainer = document.getElementById("available-ingredients");
                            ingredientsContainer.innerHTML = "";
                            
                            if (availableIngredients.length === 0) {
                                ingredientsContainer.innerHTML = `<p class="text-center mb-0">No ingredients available for selection</p>`;
                            } else {
                                availableIngredients.forEach(ingredient => {
                                    if (ingredient.quantity > 0) {
                                        const checkbox = document.createElement("div");
                                        checkbox.className = "form-check d-flex justify-content-between align-items-center";
                                        checkbox.innerHTML = `
                                            <div>
                                                <input class="form-check-input ingredient-checkbox" type="checkbox" value="${ingredient.id}" id="ingredient-${ingredient.id}" data-price="${ingredient.price}">
                                                <label class="form-check-label" for="ingredient-${ingredient.id}">
                                                    ${ingredient.name} ${ingredient.quantity < 5 ? '(Low Stock)' : ''}
                                                </label>
                                            </div>
                                            <span class="text-muted">₱${parseFloat(ingredient.price).toFixed(2)}</span>
                                        `;
                                        ingredientsContainer.appendChild(checkbox);
                                    }
                                });
                                
                                if (ingredientsContainer.children.length === 0) {
                                    ingredientsContainer.innerHTML = `<p class="text-center mb-0">No ingredients currently available</p>`;
                                }
                            }
                            
                            // Reset form fields when opening modal
                            document.getElementById("quantity").value = 1;
                            document.getElementById("custom-instructions").value = "";

                            // Update price when opening modal
                            document.getElementById("selected-item-price").textContent = `₱${parseFloat(selectedItem.price).toFixed(2)}`;

                            // Add event listeners for ingredient checkboxes
                            document.querySelectorAll('.ingredient-checkbox').forEach(checkbox => {
                                checkbox.addEventListener('change', function() {
                                    updateCustomizationTotal();
                                });
                            });

                            // Add function to update total price with ingredients
                            function updateCustomizationTotal() {
                                let basePrice = parseFloat(selectedItem.price) || 0;
                                let quantity = parseInt(document.getElementById("quantity").value) || 1;
                                let ingredientTotal = 0;

                                document.querySelectorAll('.ingredient-checkbox:checked').forEach(checkbox => {
                                    ingredientTotal += parseFloat(checkbox.dataset.price) || 0;
                                });

                                let totalPrice = (basePrice + ingredientTotal) * quantity;
                                document.getElementById("selected-item-price").textContent = `₱${totalPrice.toFixed(2)}`;
                            }

                            // Update price when quantity changes
                            document.getElementById("quantity").addEventListener("change", updateCustomizationTotal);
                        } catch (error) {
                            console.error("Error parsing item data:", error);
                            alert("Error loading item details. Please try again.");
                        }
                    }
                });
                
                document.getElementById("add-to-cart-modal").addEventListener("click", function () {
                    // Check if selectedItem is valid
                    if (!selectedItem || !selectedItem.id) {
                        alert("Please select a valid item before adding to cart.");
                        return;
                    }
                    
                    // Check stock availability
                    if (selectedItem.stock <= 0) {
                        alert("This item is currently unavailable.");
                        return;
                    }
                    
                    // Get values from the form
                    let itemName = document.getElementById("selected-item-name").textContent;
                    let quantity = parseInt(document.getElementById("quantity").value) || 1;
                    let customInstructions = document.getElementById("custom-instructions").value || "";
                    let price = parseFloat(selectedItem.price) || 0;
                    
                    // Get selected ingredients
                    let selectedIngredients = [];
                    let ingredientPrices = [];
                    document.querySelectorAll('.ingredient-checkbox:checked').forEach(checkbox => {
                        const ingredientId = checkbox.value;
                        const ingredientName = checkbox.nextElementSibling.textContent.trim();
                        const ingredientPrice = parseFloat(checkbox.dataset.price) || 0;
                        selectedIngredients.push(ingredientName);
                        ingredientPrices.push({
                            name: ingredientName,
                            price: ingredientPrice
                        });
                    });
                    
                    // Combine selected ingredients and custom instructions
                    let ingredientsText = "";
                    if (selectedIngredients.length > 0) {
                        ingredientsText = "Adds on: " + selectedIngredients.join(", ");
                    }
                    
                    if (customInstructions) {
                        if (ingredientsText) {
                            ingredientsText += " | ";
                        }
                        ingredientsText += "Instructions: " + customInstructions;
                    }
                    
                    // Validate the information
                    if (!itemName || itemName === "" || itemName === "undefined") {
                        alert("Invalid item selected. Please try again.");
                        return;
                    }
                    
                    if (isNaN(price) || price <= 0) {
                        alert("Invalid price. Please try again.");
                        return;
                    }
                    
                    if (quantity <= 0 || isNaN(quantity)) {
                        alert("Please select a valid quantity.");
                        return;
                    }
                    
                    // Check if requested quantity exceeds available stock
                    if (quantity > selectedItem.stock) {
                        alert(`Sorry, only ${selectedItem.stock} items available in stock.`);
                        return;
                    }
                    
                    // Create cart item
                    let cartItem = {
                        id: selectedItem.id,
                        name: itemName,
                        quantity: quantity,
                        price: price,
                        ingredients: ingredientsText,
                        customInstructions: customInstructions,
                        ingredientPrices: ingredientPrices
                    };
                    
                    // Add to cart
                    cartItems.push(cartItem);
                    
                    // Save to session storage
                    try {
                        sessionStorage.setItem("cartItems", JSON.stringify(cartItems));
                    } catch (e) {
                        console.error("Error saving cart to session storage:", e);
                    }
                    
                    updateOrderSummary();
                    bootstrap.Modal.getInstance(document.getElementById("customizationModal")).hide();
                });
                
                displayMenuItems("all");
            })
            .catch(error => {
                console.error("Error loading data:", error);
                document.getElementById("menu-items-container").innerHTML = 
                    `<div class="alert alert-danger">Error loading menu. Please try again later.</div>`;
            });

            // Initialize cart items array or retrieve from session storage if exists
            if (sessionStorage.getItem("cartItems")) {
                try {
                    let storedItems = JSON.parse(sessionStorage.getItem("cartItems"));
                    // Validate stored items
                    if (Array.isArray(storedItems)) {
                        cartItems = storedItems.filter(item => 
                            item && item.name && item.name !== "undefined" && item.price && !isNaN(parseFloat(item.price))
                        );
                        updateOrderSummary();
                    } else {
                        console.error("Invalid stored cart items, initializing empty cart");
                        cartItems = [];
                    }
                } catch (e) {
                    console.error("Error parsing stored cart items:", e);
                    cartItems = [];
                }
            } else {
                cartItems = [];
            }
            
            // Initialize order type
            orderType = "";
            updateOrderSummary();
        });

        document.getElementById("view-order").addEventListener("click", function () {
            // Filter out any invalid items before displaying
            cartItems = cartItems.filter(item => 
                item && item.name && item.name !== "undefined" && item.price && !isNaN(parseFloat(item.price))
            );
            
            let viewOrderList = document.getElementById("view-order-list");
            let viewOrderEmpty = document.getElementById("view-order-empty");
            let viewTotalAmount = document.getElementById("view-total-amount");
            let viewSubtotalAmount = document.getElementById("view-subtotal-amount");
            
            viewOrderList.innerHTML = ""; // Clear previous content
            
            if (cartItems.length === 0) {
                viewOrderList.classList.add("d-none");
                viewOrderEmpty.classList.remove("d-none");
                viewTotalAmount.textContent = "0.00";
                viewSubtotalAmount.textContent = "0.00";
                let modal = new bootstrap.Modal(document.getElementById("viewOrderModal"));
                modal.show();
                return;
            }

            viewOrderList.classList.remove("d-none");
            viewOrderEmpty.classList.add("d-none");
            
            let total = 0;
            
            cartItems.forEach((item, index) => {
                // Ensure values are numbers and properties exist
                let itemName = item.name || "Unknown Item";
                let qty = parseInt(item.quantity) || 1;
                let basePrice = parseFloat(item.price) || 0;
                
                // Calculate ingredient prices
                let ingredientTotal = 0;
                let ingredientHtml = '';
                
                if (item.ingredientPrices && item.ingredientPrices.length > 0) {
                    ingredientHtml += '<ul class="list-unstyled ps-2 mb-0">';
                    
                    item.ingredientPrices.forEach(ing => {
                        const ingPrice = parseFloat(ing.price) || 0;
                        ingredientTotal += ingPrice;
                        
                        // Add each ingredient with its price
                        ingredientHtml += `<li class="d-flex justify-content-between">
                            <span>+ ${ing.name}</span>
                            <span>₱${ingPrice.toFixed(2)}</span>
                        </li>`;
                    });
                    
                    ingredientHtml += '</ul>';
                }
                
                // Get custom instructions if any
                let customInstructions = '';
                if (item.customInstructions) {
                    customInstructions = `<div class="fst-italic small">Note: ${item.customInstructions}</div>`;
                }
                
                // Calculate total price including ingredients
                let itemUnitPrice = basePrice + ingredientTotal;
                let itemTotal = itemUnitPrice * qty;
                total += itemTotal;
                
                // Create item card with edit and delete buttons
                const itemHtml = `
                    <div class="card mb-3" data-item-index="${index}">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-start">
                                <div class="fw-semibold">${itemName}</div>
                                <div>₱${basePrice.toFixed(2)}</div>
                            </div>
                            ${ingredientHtml}
                            ${customInstructions}
                            <div class="d-flex justify-content-between align-items-center mt-2">
                                <div class="input-group input-group-sm" style="max-width: 120px">
                                    <button class="btn btn-outline-secondary decrease-qty" type="button">-</button>
                                    <input type="number" class="form-control text-center item-qty" value="${qty}" min="1" max="99" data-unit-price="${itemUnitPrice.toFixed(2)}">
                                    <button class="btn btn-outline-secondary increase-qty" type="button">+</button>
                                </div>
                                <div class="text-end fw-semibold pt-1 item-total-price">
                                    ₱${itemTotal.toFixed(2)}
                                </div>
                            </div>
                            <div class="d-flex justify-content-end mt-2">
                                <button class="btn btn-sm btn-outline-danger delete-cart-item me-2">Remove</button>
                                <button class="btn btn-sm btn-primary edit-cart-item">Edit Details</button>
                            </div>
                        </div>
                    </div>
                `;
                
                viewOrderList.innerHTML += itemHtml;
            });
            
            updateOrderTotals();
            
            // Add event listeners for buttons after adding to DOM
            document.querySelectorAll('.edit-cart-item').forEach(button => {
                button.addEventListener('click', function() {
                    const card = this.closest('.card');
                    const index = parseInt(card.dataset.itemIndex);
                    editCartItem(index);
                });
            });

            document.querySelectorAll('.delete-cart-item').forEach(button => {
                button.addEventListener('click', function() {
                    const card = this.closest('.card');
                    const index = parseInt(card.dataset.itemIndex);
                    deleteCartItem(index);
                });
            });
            
            // Add quantity adjustment handlers
            document.querySelectorAll('.increase-qty').forEach(button => {
                button.addEventListener('click', function() {
                    const card = this.closest('.card');
                    const index = parseInt(card.dataset.itemIndex);
                    const qtyInput = card.querySelector('.item-qty');
                    let newQty = parseInt(qtyInput.value) + 1;
                    if (newQty > 99) newQty = 99;
                    qtyInput.value = newQty;
                    updateItemQuantity(index, newQty);
                    updateItemDisplay(card, newQty);
                });
            });
            
            document.querySelectorAll('.decrease-qty').forEach(button => {
                button.addEventListener('click', function() {
                    const card = this.closest('.card');
                    const index = parseInt(card.dataset.itemIndex);
                    const qtyInput = card.querySelector('.item-qty');
                    let newQty = parseInt(qtyInput.value) - 1;
                    if (newQty < 1) newQty = 1;
                    qtyInput.value = newQty;
                    updateItemQuantity(index, newQty);
                    updateItemDisplay(card, newQty);
                });
            });
            
            document.querySelectorAll('.item-qty').forEach(input => {
                input.addEventListener('change', function() {
                    const card = this.closest('.card');
                    const index = parseInt(card.dataset.itemIndex);
                    let newQty = parseInt(this.value);
                    if (isNaN(newQty) || newQty < 1) newQty = 1;
                    if (newQty > 99) newQty = 99;
                    this.value = newQty;
                    updateItemQuantity(index, newQty);
                    updateItemDisplay(card, newQty);
                });
            });

            // Proceed button handler
            document.getElementById("update-and-proceed").addEventListener('click', function() {
                // Close the modal and simulate click on proceed order
                document.getElementById("proceed-order").click();
            });

            let modal = new bootstrap.Modal(document.getElementById("viewOrderModal"));
            modal.show();
        });

        document.getElementById("save-edit").addEventListener("click", function() {
            let index = parseInt(document.getElementById("edit-item-index").value);
            let quantity = parseInt(document.getElementById("edit-quantity").value) || 1;
            let customInstructions = document.getElementById("edit-custom-instructions").value || "";
            
            if (index >= 0 && index < cartItems.length) {
                // Get selected ingredients
                let selectedIngredients = [];
                let ingredientPrices = [];
                document.querySelectorAll('#edit-available-ingredients .ingredient-checkbox:checked').forEach(checkbox => {
                    const ingredientName = checkbox.nextElementSibling.textContent.trim();
                    selectedIngredients.push(ingredientName);
                    ingredientPrices.push({
                        name: ingredientName,
                        price: parseFloat(checkbox.dataset.price) || 0
                    });
                });
                
                // Combine ingredients and custom instructions
                let ingredientsText = "";
                if (selectedIngredients.length > 0) {
                    ingredientsText = "Ingredients: " + selectedIngredients.join(", ");
                }
                
                if (customInstructions) {
                    if (ingredientsText) {
                        ingredientsText += " | ";
                    }
                    ingredientsText += "Instructions: " + customInstructions;
                }
                
                // Update the cart item
                cartItems[index].quantity = quantity;
                cartItems[index].ingredients = ingredientsText;
                cartItems[index].customInstructions = customInstructions;
                cartItems[index].ingredientPrices = ingredientPrices;
                
                // Save to session storage
                sessionStorage.setItem("cartItems", JSON.stringify(cartItems));
                
                updateOrderSummary();
                
                // Close edit modal and reopen view order modal
                bootstrap.Modal.getInstance(document.getElementById("editItemModal")).hide();
                
                // Refresh the view order modal
                document.getElementById("view-order").click();
            }
        });

        function editCartItem(index) {
            if (index >= 0 && index < cartItems.length) {
                let item = cartItems[index];
                
                document.getElementById("edit-item-index").value = index;
                document.getElementById("edit-item-name").value = item.name;
                document.getElementById("edit-quantity").value = item.quantity;
                document.getElementById("edit-custom-instructions").value = item.customInstructions || "";
                
                // Fetch and populate ingredients
                fetch("fetch_ingredients.php")
                    .then(response => response.json())
                    .then(data => {
                        const ingredientsContainer = document.getElementById("edit-available-ingredients");
                        ingredientsContainer.innerHTML = "";
                        
                        if (data.ingredients && data.ingredients.length > 0) {
                            data.ingredients.forEach(ingredient => {
                                if (ingredient.quantity > 0) {
                                    const checkbox = document.createElement("div");
                                    checkbox.className = "form-check d-flex justify-content-between align-items-center";
                                    
                                    // Check if this ingredient was previously selected
                                    const wasSelected = item.ingredients && item.ingredients.includes(ingredient.name);
                                    
                                    checkbox.innerHTML = `
                                        <div>
                                            <input class="form-check-input ingredient-checkbox" type="checkbox" 
                                                value="${ingredient.id}" 
                                                id="edit-ingredient-${ingredient.id}" 
                                                data-price="${ingredient.price}"
                                                ${wasSelected ? 'checked' : ''}>
                                            <label class="form-check-label" for="edit-ingredient-${ingredient.id}">
                                                ${ingredient.name} ${ingredient.quantity < 5 ? '(Low Stock)' : ''}
                                            </label>
                                        </div>
                                        <span class="text-muted">₱${parseFloat(ingredient.price).toFixed(2)}</span>
                                    `;
                                    ingredientsContainer.appendChild(checkbox);
                                }
                            });
                            
                            if (ingredientsContainer.children.length === 0) {
                                ingredientsContainer.innerHTML = `<p class="text-center mb-0">No ingredients currently available</p>`;
                            }
                        } else {
                            ingredientsContainer.innerHTML = `<p class="text-center mb-0">No ingredients available for selection</p>`;
                        }
                    })
                    .catch(error => {
                        console.error("Error fetching ingredients:", error);
                        document.getElementById("edit-available-ingredients").innerHTML = 
                            `<p class="text-center text-danger">Error loading ingredients</p>`;
                    });
                
                // Close view modal and open edit modal
                bootstrap.Modal.getInstance(document.getElementById("viewOrderModal")).hide();
                let editModal = new bootstrap.Modal(document.getElementById("editItemModal"));
                editModal.show();
            }
        }

        function deleteCartItem(index) {
            if (index >= 0 && index < cartItems.length) {
                if (confirm("Are you sure you want to remove this item?")) {
                    cartItems.splice(index, 1);
                    
                    // Save to session storage
                    sessionStorage.setItem("cartItems", JSON.stringify(cartItems));
                    
                    updateOrderSummary();
                    
                    // Refresh the view order modal
                    bootstrap.Modal.getInstance(document.getElementById("viewOrderModal")).hide();
                    document.getElementById("view-order").click();
                }
            }
        }

        function updateOrderSummary() {
            // Filter out invalid cart items
            cartItems = cartItems.filter(item => 
                item && item.name && item.name !== "undefined" && item.price && !isNaN(parseFloat(item.price))
            );
            
            let orderSummaryList = document.getElementById("order-summary-list");
            orderSummaryList.innerHTML = "";
            
            if (cartItems.length === 0) {
                orderSummaryList.innerHTML = "<p class='text-center text-muted'>No items in cart.</p>";
                document.getElementById("subtotal-amount").textContent = "0.00";
                document.getElementById("total-amount").textContent = "0.00";
                return;
            }

            let total = 0;
            
            cartItems.forEach(item => {
                // Ensure values are numbers and properties exist
                let itemName = item.name || "Unknown Item";
                let qty = parseInt(item.quantity) || 1;
                let basePrice = parseFloat(item.price) || 0;
                
                // Calculate ingredient prices
                let ingredientTotal = 0;
                let ingredientHtml = '';
                
                if (item.ingredientPrices && item.ingredientPrices.length > 0) {
                    ingredientHtml += '<ul class="list-unstyled ps-2 mb-0 small">';
                    
                    item.ingredientPrices.forEach(ing => {
                        const ingPrice = parseFloat(ing.price) || 0;
                        ingredientTotal += ingPrice;
                        
                        // Add each ingredient with its price
                        ingredientHtml += `<li class="d-flex justify-content-between">
                            <span>+ ${ing.name}</span>
                            <span class="text-muted">₱${ingPrice.toFixed(2)}</span>
                        </li>`;
                    });
                    
                    ingredientHtml += '</ul>';
                }
                
                // Get custom instructions if any
                let customInstructions = '';
                if (item.customInstructions) {
                    customInstructions = `<div class="fst-italic small text-muted">Note: ${item.customInstructions}</div>`;
                }
                
                // Calculate total price including ingredients
                let itemTotal = (basePrice + ingredientTotal) * qty;
                total += itemTotal;
                
                // Create item card for order summary
                const itemHtml = `
                    <div class="mb-3 pb-2 border-bottom">
                        <div class="d-flex justify-content-between align-items-start">
                            <div class="fw-semibold">${itemName} x${qty}</div>
                            <div class="text-muted">₱${basePrice.toFixed(2)}</div>
                        </div>
                        ${ingredientHtml}
                        ${customInstructions}
                        <div class="text-end fw-semibold pt-1">
                            Item Total: ₱${itemTotal.toFixed(2)}
                        </div>
                    </div>
                `;
                
                orderSummaryList.innerHTML += itemHtml;
            });
            
            document.getElementById("subtotal-amount").textContent = total.toFixed(2);
            document.getElementById("total-amount").textContent = total.toFixed(2);
        }

        document.querySelector(".btn-warning").addEventListener("click", function() {
            holdOrder();
        });

        function holdOrder() {
            // Filter out invalid items before holding
            cartItems = cartItems.filter(item => 
                item && item.name && item.name !== "undefined" && item.price && !isNaN(parseFloat(item.price))
            );
            
            if (cartItems.length === 0) {
                alert("No valid items in the cart to hold.");
                return;
            }
            
            const orderKey = "heldOrder_" + new Date().getTime(); // Create a unique key
            localStorage.setItem(orderKey, JSON.stringify({
                items: cartItems,
                timestamp: new Date().toLocaleString()
            }));
            
            alert("Order has been held successfully!");
            
            // Clear current cart
            cartItems = [];
            updateOrderSummary();
        }

        // Function to check order status
        function checkOrderStatus() {
            if (!lastOrderId) return;

            fetch(`check_order_status.php?order_id=${lastOrderId}`)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        if (data.status === 'Finished') {
                            // Order is finished, stop refreshing
                            clearInterval(refreshInterval);
                            alert('Your order is ready!');
                            // Optionally redirect to a thank you page or clear the cart
                            cartItems = [];
                            sessionStorage.removeItem("cartItems");
                            updateOrderSummary();
                        }
                    }
                })
                .catch(error => console.error('Error checking order status:', error));
        }

        // Function to start order status checking
        function startOrderStatusCheck(orderId) {
            lastOrderId = orderId;
            // Check status every 30 seconds
            refreshInterval = setInterval(checkOrderStatus, 30000);
        }

        // Function to stop order status checking
        function stopOrderStatusCheck() {
            if (refreshInterval) {
                clearInterval(refreshInterval);
                refreshInterval = null;
            }
            lastOrderId = null;
        }

        // Update the saveOrderToDatabase function
        function saveOrderToDatabase() {
            let totalAmount = document.getElementById("total-amount").textContent;
            let orderSummary = document.getElementById("order-summary-list").innerHTML;

            // Convert order summary to plain text
            let orderDetails = "";
            document.querySelectorAll("#order-summary-list p").forEach(p => {
                orderDetails += p.textContent + "\n";
            });

            // Check if cart is empty
            if (cartItems.length === 0) {
                alert("Your cart is empty. Please add items before proceeding.");
                return;
            }

            // Add table number for dine-in orders
            const tableData = orderType === "Dine-In" ? `&table_number=${selectedTable}` : "";
            
            // Convert cart items to JSON string to ensure proper formatting in cashier view
            const cartItemsJson = JSON.stringify(cartItems);

            // Send order details to PHP (save_order.php)
            fetch("save_order.php", {
                method: "POST",
                headers: { "Content-Type": "application/x-www-form-urlencoded" },
                body: `order_type=${orderType}&total_amount=${totalAmount}&order_details=${encodeURIComponent(orderDetails)}${tableData}&cart_items=${encodeURIComponent(cartItemsJson)}`
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert("Order saved successfully!");
                    generateReceipt(data.order_id);
                    
                    // Start checking order status
                    startOrderStatusCheck(data.order_id);
                    
                    // Clear the cart after successful order
                    cartItems = [];
                    sessionStorage.removeItem("cartItems");
                    updateOrderSummary();
                } else {
                    // Check if this is a table unavailability error
                    if (data.table_unavailable) {
                        alert("Error: " + data.error);
                        // Re-open table selection to let user pick another table
                        selectedTable = null;
                        showTableSelection();
                    } else {
                        alert("Error saving order: " + data.error);
                    }
                }
            })
            .catch(error => {
                console.error("Fetch Error:", error);
                alert("An error occurred while saving your order. Please try again.");
            });
        }

        // Add cleanup when leaving the page
        window.addEventListener('beforeunload', function() {
            stopOrderStatusCheck();
        });

        // Function to update displayed price when quantity changes
        function updateItemDisplay(card, quantity) {
            const unitPrice = parseFloat(card.querySelector('.item-qty').dataset.unitPrice);
            const totalPriceElement = card.querySelector('.item-total-price');
            const newTotal = unitPrice * quantity;
            totalPriceElement.textContent = `₱${newTotal.toFixed(2)}`;
            
            // Update overall totals
            updateOrderTotals();
        }

        // Function to update the total amounts in the view order modal
        function updateOrderTotals() {
            let total = 0;
            document.querySelectorAll('#view-order-list .card').forEach(card => {
                const qtyInput = card.querySelector('.item-qty');
                const qty = parseInt(qtyInput.value) || 1;
                const unitPrice = parseFloat(qtyInput.dataset.unitPrice) || 0;
                total += unitPrice * qty;
            });
            
            document.getElementById('view-subtotal-amount').textContent = total.toFixed(2);
            document.getElementById('view-total-amount').textContent = total.toFixed(2);
        }

        function updateItemQuantity(index, newQty) {
            if (index >= 0 && index < cartItems.length) {
                // Update quantity in cart items array
                cartItems[index].quantity = newQty;
                
                // Save to session storage
                sessionStorage.setItem("cartItems", JSON.stringify(cartItems));
                
                // Update the order summary in the sidebar
                updateOrderSummary();
            }
        }
    </script>

<script>
    document.addEventListener("DOMContentLoaded", function () {
        document.getElementById("proceed-order").addEventListener("click", function () {
            let modal = new bootstrap.Modal(document.getElementById("orderTypeModal"));
            modal.show();
        });

        document.getElementById("dine-in").addEventListener("click", function () {
            orderType = "Dine-In";
            closeOrderTypeModal();
            showTableSelection();
        });

        document.getElementById("take-out").addEventListener("click", function () {
            orderType = "Take-Out";
            closeOrderTypeModal();
            saveOrderToDatabase();
        });

        function closeOrderTypeModal() {
            let modalElement = document.getElementById("orderTypeModal");
            let modalInstance = bootstrap.Modal.getInstance(modalElement);
            modalInstance.hide();
        }
        
        function showTableSelection() {
            // Show loading indicator
            const tablesContainer = document.getElementById("tables-container");
            tablesContainer.innerHTML = `<div class="text-center w-100"><div class="spinner-border text-primary" role="status"></div><p>Loading available tables...</p></div>`;
            
            let tableModal = new bootstrap.Modal(document.getElementById("tableSelectionModal"));
            tableModal.show();
            
            // Fetch available tables - add timestamp to prevent caching
            fetch("check_tables.php?_=" + new Date().getTime())
                .then(response => response.json())
                .then(data => {
                    tablesContainer.innerHTML = "";
                    
                    if (!data.success) {
                        tablesContainer.innerHTML = `<div class="alert alert-danger">Error: ${data.error || 'Could not load tables'}</div>`;
                        return;
                    }
                    
                    // Show message if no table is available
                    if (data.table_statuses && data.table_statuses.filter(t => t.is_available).length === 0) {
                        tablesContainer.innerHTML = `<div class="alert alert-warning w-100">Sorry, no tables are currently available. Please try take-out or return later.</div>`;
                        return;
                    }
                    
                    // Use new detailed table information if available
                    if (data.table_statuses) {
                        data.table_statuses.forEach(table => {
                            const tableButton = document.createElement("button");
                            
                            // Different color based on status
                            let statusClass = "btn-success"; // default available
                            let statusLabel = "";
                            let isDisabled = false;
                            
                            if (table.status === "occupied") {
                                statusClass = "btn-danger";
                                statusLabel = "<small>In Use</small>";
                                isDisabled = true;
                            } else if (table.status === "unavailable") {
                                statusClass = "btn-warning";
                                statusLabel = "<small>Unavailable</small>";
                                isDisabled = true;
                            }
                            
                            tableButton.className = `btn ${statusClass} m-1`;
                            tableButton.style.width = "90px";
                            tableButton.style.height = "90px";
                            tableButton.innerHTML = `Table ${table.number}<br>${statusLabel}`;
                            tableButton.disabled = isDisabled;
                            
                            if (!isDisabled) {
                                tableButton.addEventListener("click", function() {
                                    selectedTable = table.number;
                                    bootstrap.Modal.getInstance(document.getElementById("tableSelectionModal")).hide();
                                    saveOrderToDatabase();
                                });
                            }
                            
                            tablesContainer.appendChild(tableButton);
                        });
                    } else {
                        // Fallback to old style if table_statuses is not available
                        // Create 10 tables for selection
                        for (let i = 1; i <= 10; i++) {
                            const isUnavailable = data.occupied_tables && data.occupied_tables.includes(i);
                            const tableButton = document.createElement("button");
                            tableButton.className = `btn ${isUnavailable ? "btn-danger" : "btn-success"} m-1`;
                            tableButton.style.width = "80px";
                            tableButton.style.height = "80px";
                            tableButton.textContent = `Table ${i}`;
                            
                            if (isUnavailable) {
                                tableButton.disabled = true;
                                tableButton.innerHTML = `Table ${i}<br><small>Unavailable</small>`;
                            } else {
                                tableButton.addEventListener("click", function() {
                                    selectedTable = i;
                                    bootstrap.Modal.getInstance(document.getElementById("tableSelectionModal")).hide();
                                    saveOrderToDatabase();
                                });
                            }
                            
                            tablesContainer.appendChild(tableButton);
                        }
                    }
                })
                .catch(error => {
                    console.error("Error fetching tables:", error);
                    tablesContainer.innerHTML = `<div class="alert alert-danger">Error loading tables. Please try again.</div>`;
                });
        }
    });

    function generateReceipt(orderId) {
        let orderSummary = document.getElementById("order-summary-list").innerHTML;
        let totalAmount = document.getElementById("total-amount").textContent;
        let tableInfo = orderType === "Dine-In" ? `<p>Table Number: ${selectedTable}</p>` : "";

        let receiptWindow = window.open("", "_blank", "width=600,height=600");
        receiptWindow.document.write(`
            <html>
            <head>
                <title>D Breakers Restobar</title>
                <style>
                    body { 
                        font-family: Arial, sans-serif; 
                        text-align: center; 
                        padding: 20px;
                    }
                    h5 { margin-bottom: 10px; }
                    .receipt-container { 
                        padding: 20px; 
                        border: 1px solid #000;
                        max-width: 500px;
                        margin: 0 auto;
                    }
                    hr { border: 1px solid #000; }
                    .order-item {
                        margin-bottom: 15px;
                        text-align: left;
                    }
                    .order-item p {
                        margin: 5px 0;
                    }
                    .total-section {
                        margin-top: 20px;
                        font-weight: bold;
                    }
                </style>
            </head>
            <body>
                <div class="receipt-container">
                    <h1>D Breakers Restobar</h1>
                    <h5>Order ID: ${orderId}</h5>
                    <h5>Order Type: ${orderType}</h5>
                    ${tableInfo}
                    <hr>
                    <div class="order-items">
                        ${orderSummary}
                    </div>
                    <hr>
                    <div class="total-section">
                        <p>Total Amount: ₱${totalAmount}</p>
                    </div>
                    <hr>
                    <p><strong>Thank you for your order!</strong></p>
                </div>
                <script>
                    window.print();
                    window.onafterprint = function () { window.close(); };
                <\/script>
            </body>
            </html>
        `);
        receiptWindow.document.close();
    }
</script>

</body>
</html>
