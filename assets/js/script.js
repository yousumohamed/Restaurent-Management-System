/**
 * Restaurant Management System
 * Main JavaScript File
 */

// ============================================
// Image Upload Preview
// ============================================
function previewImage(input) {
    const preview = document.getElementById('imagePreview');
    const previewContainer = document.getElementById('imagePreviewContainer');
    
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        
        reader.onload = function(e) {
            preview.src = e.target.result;
            previewContainer.style.display = 'block';
        }
        
        reader.readAsDataURL(input.files[0]);
    } else {
        previewContainer.style.display = 'none';
    }
}

// ============================================
// Form Validation
// ============================================
function validateOrderForm() {
    const foodName = document.getElementById('food_name').value.trim();
    const quantity = document.getElementById('quantity').value;
    const price = document.getElementById('price').value;
    const orderDate = document.getElementById('order_date').value;
    
    if (foodName === '') {
        alert('Please enter food name');
        return false;
    }
    
    if (quantity <= 0) {
        alert('Quantity must be greater than 0');
        return false;
    }
    
    if (price <= 0) {
        alert('Price must be greater than 0');
        return false;
    }
    
    if (orderDate === '') {
        alert('Please select order date');
        return false;
    }
    
    return true;
}

function validateExpenseForm() {
    const category = document.getElementById('category').value;
    const amount = document.getElementById('amount').value;
    const expenseDate = document.getElementById('expense_date').value;
    
    if (category === '') {
        alert('Please select expense category');
        return false;
    }
    
    if (amount <= 0) {
        alert('Amount must be greater than 0');
        return false;
    }
    
    if (expenseDate === '') {
        alert('Please select expense date');
        return false;
    }
    
    return true;
}

// ============================================
// Delete Confirmation
// ============================================
function confirmDelete(type, id) {
    const message = `Are you sure you want to delete this ${type}?`;
    return confirm(message);
}

// ============================================
// Calculate Total Amount
// ============================================
function calculateTotal() {
    const quantity = parseFloat(document.getElementById('quantity').value) || 0;
    const price = parseFloat(document.getElementById('price').value) || 0;
    const total = quantity * price;
    
    const totalDisplay = document.getElementById('totalDisplay');
    if (totalDisplay) {
        totalDisplay.textContent = 'Total: KSh ' + total.toFixed(2);
    }
}

// ============================================
// Search Functionality
// ============================================
function searchTable() {
    const input = document.getElementById('searchInput');
    const filter = input.value.toUpperCase();
    const table = document.getElementById('dataTable');
    const tr = table.getElementsByTagName('tr');
    
    for (let i = 1; i < tr.length; i++) {
        let found = false;
        const td = tr[i].getElementsByTagName('td');
        
        for (let j = 0; j < td.length; j++) {
            if (td[j]) {
                const txtValue = td[j].textContent || td[j].innerText;
                if (txtValue.toUpperCase().indexOf(filter) > -1) {
                    found = true;
                    break;
                }
            }
        }
        
        tr[i].style.display = found ? '' : 'none';
    }
}

// ============================================
// Date Filter
// ============================================
function filterByDate() {
    const startDate = document.getElementById('startDate').value;
    const endDate = document.getElementById('endDate').value;
    
    if (startDate && endDate) {
        window.location.href = `?start_date=${startDate}&end_date=${endDate}`;
    }
}

// ============================================
// Active Navigation Highlight
// ============================================
function setActiveNav() {
    const currentPage = window.location.pathname.split('/').pop();
    const navLinks = document.querySelectorAll('.nav-menu a');
    
    navLinks.forEach(link => {
        const href = link.getAttribute('href');
        if (href && href.includes(currentPage)) {
            link.classList.add('active');
        }
    });
}

// ============================================
// Auto-hide Alerts
// ============================================
function autoHideAlerts() {
    const alerts = document.querySelectorAll('.alert');
    alerts.forEach(alert => {
        setTimeout(() => {
            alert.style.opacity = '0';
            setTimeout(() => {
                alert.style.display = 'none';
            }, 300);
        }, 5000);
    });
}

// ============================================
// Print Report
// ============================================
function printReport() {
    window.print();
}

// ============================================
// Export to CSV
// ============================================
function exportToCSV(tableId, filename) {
    const table = document.getElementById(tableId);
    let csv = [];
    const rows = table.querySelectorAll('tr');
    
    for (let i = 0; i < rows.length; i++) {
        const row = [];
        const cols = rows[i].querySelectorAll('td, th');
        
        for (let j = 0; j < cols.length; j++) {
            row.push(cols[j].innerText);
        }
        
        csv.push(row.join(','));
    }
    
    downloadCSV(csv.join('\n'), filename);
}

function downloadCSV(csv, filename) {
    const csvFile = new Blob([csv], { type: 'text/csv' });
    const downloadLink = document.createElement('a');
    downloadLink.download = filename;
    downloadLink.href = window.URL.createObjectURL(csvFile);
    downloadLink.style.display = 'none';
    document.body.appendChild(downloadLink);
    downloadLink.click();
    document.body.removeChild(downloadLink);
}

// ============================================
// Initialize on Page Load
// ============================================
document.addEventListener('DOMContentLoaded', function() {
    // Set active navigation
    setActiveNav();
    
    // Auto-hide alerts
    autoHideAlerts();
    
    // Add event listeners for quantity and price changes
    const quantityInput = document.getElementById('quantity');
    const priceInput = document.getElementById('price');
    
    if (quantityInput && priceInput) {
        quantityInput.addEventListener('input', calculateTotal);
        priceInput.addEventListener('input', calculateTotal);
        calculateTotal(); // Calculate on page load
    }
    
    // Image upload preview
    const imageInput = document.getElementById('order_image');
    if (imageInput) {
        imageInput.addEventListener('change', function() {
            previewImage(this);
        });
    }
});

// ============================================
// Smooth Scroll
// ============================================
document.querySelectorAll('a[href^="#"]').forEach(anchor => {
    anchor.addEventListener('click', function (e) {
        e.preventDefault();
        const target = document.querySelector(this.getAttribute('href'));
        if (target) {
            target.scrollIntoView({
                behavior: 'smooth'
            });
        }
    });
});
