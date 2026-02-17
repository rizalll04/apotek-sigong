/**
 * Shopping Cart Functionality
 *
 * Handles:
 * - Update quantity (+/- buttons)
 * - Auto-update subtotal and total
 * - Currency formatting (Rupiah)
 * - User notifications
 * - AJAX requests with CSRF token
 */

/**
 * Update quantity of cart item via AJAX
 *
 * @param {number} cartId - ID keranjang (id_keranjang)
 * @param {number} change - Change amount (+1 or -1)
 *
 * Process:
 * 1. Get current quantity from input field
 * 2. Calculate new quantity
 * 3. Validate (minimum 1)
 * 4. Send AJAX request to backend
 * 5. Update UI with new totals
 * 6. Show success/error notification
 */
function updateQty(cartId, change) {
    // Get quantity input element
    const qtyInput = document.getElementById(`qty${cartId}`);
    if (!qtyInput) {
        console.error(`Input field qty${cartId} not found`);
        return;
    }

    // Get current quantity
    const currentQty = parseInt(qtyInput.value);
    const newQty = currentQty + change;

    // Validation: minimum quantity is 1
    if (newQty < 1) {
        showAlert('Jumlah tidak boleh kurang dari 1', 'warning');
        return;
    }

    // Optional: Check stock limit (if max-stock attribute exists)
    const maxStock = parseInt(qtyInput.getAttribute('max') || qtyInput.dataset.maxStock || 999999);
    if (newQty > maxStock) {
        showAlert(`Stok tidak cukup. Stok tersedia: ${maxStock}`, 'warning');
        return;
    }

    // Disable input during request
    qtyInput.disabled = true;
    const originalValue = qtyInput.value;

    // Get CSRF token from meta tag
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;
    if (!csrfToken) {
        console.error('CSRF token not found in meta tag');
        showAlert('Terjadi kesalahan keamanan. Silahkan refresh halaman.', 'error');
        qtyInput.disabled = false;
        return;
    }

    // Send AJAX request to update quantity
    fetch(`/keranjang/ubah-jumlah/${cartId}`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': csrfToken
        },
        body: JSON.stringify({
            jumlah: newQty
        })
    })
    .then(response => {
        // Parse JSON first, regardless of status
        return response.json().then(data => ({
            ok: response.ok,
            status: response.status,
            data: data
        }));
    })
    .then(result => {
        if (result.ok || (result.data && result.data.success)) {
            // Success response
            const data = result.data;
            
            // Update quantity input
            qtyInput.value = newQty;

            // Update subtotal for this item (if element exists)
            const subtotalElement = document.getElementById(`subtotal${cartId}`);
            if (subtotalElement && data.item_total !== undefined) {
                const formattedAmount = formatRupiah(data.item_total);
                subtotalElement.textContent = formattedAmount;
            }

            // Update cart grand totals
            if (data.grandtotal) {
                const subtotalView = document.getElementById('subtotalView');
                const totalView = document.getElementById('totalView');

                if (subtotalView && data.grandtotal.subtotal !== undefined) {
                    const formattedSubtotal = formatRupiah(data.grandtotal.subtotal);
                    subtotalView.textContent = formattedSubtotal;
                }
                if (totalView && data.grandtotal.total !== undefined) {
                    const formattedTotal = formatRupiah(data.grandtotal.total);
                    totalView.textContent = formattedTotal;
                }

                // Update hidden input for payment form (if exists)
                const totalBelanja = document.getElementById('totalBelanja');
                if (totalBelanja) {
                    totalBelanja.value = parseInt(data.grandtotal.total);
                }
            }

            showAlert('Quantity berhasil diupdate', 'success', 2000);
        } else {
            // Server returned error
            const data = result.data;
            const errorMsg = data.message || 'Terjadi kesalahan saat update quantity';
            console.error('Server error:', data);
            showAlert(errorMsg, 'error');
            qtyInput.value = originalValue; // Revert to original
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showAlert('Terjadi kesalahan saat update quantity. Silahkan coba lagi.', 'error');
        qtyInput.value = originalValue;
    })
    .finally(() => {
        qtyInput.disabled = false;
    });
}

/**
 * Format number to Indonesian Rupiah currency format
 *
 * @param {number} amount - Amount to format
 * @returns {string} Formatted string (e.g., "Rp 15.000")
 *
 * Examples:
 *   formatRupiah(1000)      → "Rp 1.000"
 *   formatRupiah(1500000)   → "Rp 1.500.000"
 *   formatRupiah(null)      → "Rp 0"
 */
function formatRupiah(amount) {
    if (amount === null || amount === undefined || isNaN(amount)) {
        return 'Rp 0';
    }

    // Convert to integer (remove decimal places)
    const intAmount = Math.round(Number(amount));

    // Return formatted with thousand separators
    return 'Rp ' + intAmount
        .toString()
        .replace(/\B(?=(\d{3})+(?!\d))/g, '.');
}

/**
 * Show toast/alert notification to user
 *
 * @param {string} message - Message to display
 * @param {string} type - Alert type: 'success', 'error', 'warning', 'info'
 * @param {number} duration - Duration in milliseconds (default: 3000)
 *
 * Behavior:
 * - Creates floating alert at top-right of screen
 * - Auto-dismisses after specified duration
 * - Can be manually closed with X button
 * - Uses Bootstrap 5 alert styling
 */
function showAlert(message, type = 'info', duration = 3000) {
    // Create alert container element
    const alertDiv = document.createElement('div');

    // Determine Bootstrap alert class
    const alertClass = mapAlertType(type);
    alertDiv.className = `alert alert-${alertClass} alert-dismissible fade show`;
    alertDiv.setAttribute('role', 'alert');

    // Style alert positioning (fixed top-right)
    alertDiv.style.position = 'fixed';
    alertDiv.style.top = '20px';
    alertDiv.style.right = '20px';
    alertDiv.style.zIndex = '9999';
    alertDiv.style.minWidth = '300px';
    alertDiv.style.maxWidth = '500px';
    alertDiv.style.animation = 'slideIn 0.3s ease-in-out';

    // Get icon for alert type
    const icon = getAlertIcon(type);

    // Build HTML content
    alertDiv.innerHTML = `
        <div style="display: flex; align-items: center;">
            ${icon}
            <span>${message}</span>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    `;

    // Append to document body
    document.body.appendChild(alertDiv);

    // Initialize Bootstrap dismiss behavior
    if (window.bootstrap && window.bootstrap.Alert) {
        new window.bootstrap.Alert(alertDiv);
    }

    // Auto-remove after duration
    const timeoutId = setTimeout(() => {
        alertDiv.remove();
    }, duration);

    // Allow manual dismissal to clear timeout
    alertDiv.addEventListener('closed.bs.alert', () => {
        clearTimeout(timeoutId);
    });
}

/**
 * Map alert type to Bootstrap alert color class
 *
 * @param {string} type - Alert type
 * @returns {string} Bootstrap class (success, danger, warning, info)
 */
function mapAlertType(type) {
    const mapping = {
        'success': 'success',
        'error': 'danger',
        'warning': 'warning',
        'info': 'info'
    };
    return mapping[type] || 'info';
}

/**
 * Get icon HTML for alert type
 *
 * @param {string} type - Alert type
 * @returns {string} HTML icon element with Bootstrap Icons
 */
function getAlertIcon(type) {
    const icons = {
        'success': '<i class="bi bi-check-circle me-2" style="color: var(--bs-success); font-size: 1.2em;"></i>',
        'error': '<i class="bi bi-exclamation-circle me-2" style="color: var(--bs-danger); font-size: 1.2em;"></i>',
        'warning': '<i class="bi bi-exclamation-triangle me-2" style="color: var(--bs-warning); font-size: 1.2em;"></i>',
        'info': '<i class="bi bi-info-circle me-2" style="color: var(--bs-info); font-size: 1.2em;"></i>'
    };
    return icons[type] || '';
}

/**
 * Add slide-in animation for alerts
 */
const style = document.createElement('style');
style.textContent = `
    @keyframes slideIn {
        from {
            transform: translateX(400px);
            opacity: 0;
        }
        to {
            transform: translateX(0);
            opacity: 1;
        }
    }
`;
document.head.appendChild(style);
