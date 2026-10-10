/**
 * High-Converting Medical Landing Page Script
 * - Dynamic Pricing & Delivery Area Calculation
 * - Phone Number Validation (BD format)
 * - AJAX Order Submission & Meta Pixel Events
 * - Lightbox for Customer Review Screenshots
 */

document.addEventListener('DOMContentLoaded', function () {
    // -------------------------------------------------------------
    // Elements
    // -------------------------------------------------------------
    const orderForm = document.getElementById('checkout-form');
    const packageCards = document.querySelectorAll('.package-card');
    const deliveryCards = document.querySelectorAll('.delivery-card');
    const packageInput = document.getElementById('selected-package');
    const deliveryInput = document.getElementById('selected-delivery');
    
    const qtyInput = document.getElementById('order-quantity');
    const qtyMinus = document.getElementById('qty-minus');
    const qtyPlus = document.getElementById('qty-plus');

    const summaryPackageTitle = document.getElementById('summary-package-title');
    const summaryPackagePrice = document.getElementById('summary-package-price');
    const summaryDeliveryTitle = document.getElementById('summary-delivery-title');
    const summaryDeliveryPrice = document.getElementById('summary-delivery-price');
    const summaryTotalAmount = document.getElementById('summary-total-amount');
    const submitBtn = document.getElementById('btn-submit-order');
    const submitBtnAmount = document.getElementById('btn-submit-amount');
    const errorAlert = document.getElementById('order-error-alert');

    // -------------------------------------------------------------
    // Pricing State
    // -------------------------------------------------------------
    let currentPackagePrice = 0;
    let currentDeliveryPrice = 0;
    let currentQuantity = 1;

    function updateCalculations() {
        // Read quantity
        if (qtyInput) {
            currentQuantity = parseInt(qtyInput.value, 10) || 1;
            if (currentQuantity < 1) currentQuantity = 1;
            qtyInput.value = currentQuantity;
        }

        // Find selected package
        const selectedPkg = document.querySelector('.package-card.selected');
        if (selectedPkg) {
            currentPackagePrice = parseFloat(selectedPkg.dataset.price) || 0;
            const title = selectedPkg.dataset.title;
            packageInput.value = selectedPkg.dataset.package;
            const subtotal = currentPackagePrice * currentQuantity;

            if (summaryPackageTitle) {
                summaryPackageTitle.textContent = title + (currentQuantity > 1 ? ` (${currentQuantity}x)` : '');
            }
            if (summaryPackagePrice) {
                if (currentQuantity > 1) {
                    summaryPackagePrice.textContent = `৳${currentPackagePrice.toLocaleString('en-US')} × ${currentQuantity} = ৳${subtotal.toLocaleString('en-US')}`;
                } else {
                    summaryPackagePrice.textContent = '৳' + currentPackagePrice.toLocaleString('en-US');
                }
            }
        }

        // Find selected delivery
        const selectedDel = document.querySelector('.delivery-card.selected');
        if (selectedDel) {
            currentDeliveryPrice = parseFloat(selectedDel.dataset.price) || 0;
            const title = selectedDel.dataset.title;
            deliveryInput.value = selectedDel.dataset.area;
            if (summaryDeliveryTitle) summaryDeliveryTitle.textContent = title;
            if (summaryDeliveryPrice) summaryDeliveryPrice.textContent = '৳' + currentDeliveryPrice.toLocaleString('en-US');
        }

        const total = (currentPackagePrice * currentQuantity) + currentDeliveryPrice;
        if (summaryTotalAmount) summaryTotalAmount.textContent = '৳' + total.toLocaleString('en-US');
        if (submitBtnAmount) submitBtnAmount.textContent = '৳' + total.toLocaleString('en-US');
    }

    // Quantity Stepper Controls
    if (qtyMinus && qtyInput) {
        qtyMinus.addEventListener('click', function () {
            let val = parseInt(qtyInput.value, 10) || 1;
            if (val > 1) {
                qtyInput.value = val - 1;
                updateCalculations();
            }
        });
    }

    if (qtyPlus && qtyInput) {
        qtyPlus.addEventListener('click', function () {
            let val = parseInt(qtyInput.value, 10) || 1;
            if (val < 25) {
                qtyInput.value = val + 1;
                updateCalculations();
            }
        });
    }

    if (qtyInput) {
        qtyInput.addEventListener('change', updateCalculations);
        qtyInput.addEventListener('input', updateCalculations);
    }

    // Package Selection Click
    packageCards.forEach(card => {
        card.addEventListener('click', function () {
            packageCards.forEach(c => c.classList.remove('selected'));
            this.classList.add('selected');
            updateCalculations();
        });
    });

    // Delivery Selection Click
    deliveryCards.forEach(card => {
        card.addEventListener('click', function () {
            deliveryCards.forEach(c => c.classList.remove('selected'));
            this.classList.add('selected');
            updateCalculations();
        });
    });

    // Initialize initial values
    updateCalculations();

    // -------------------------------------------------------------
    // Smooth Scroll to Order Section
    // -------------------------------------------------------------
    document.querySelectorAll('.scroll-to-order').forEach(btn => {
        btn.addEventListener('click', function (e) {
            e.preventDefault();
            const orderSec = document.getElementById('order-section');
            if (orderSec) {
                orderSec.scrollIntoView({ behavior: 'smooth', block: 'start' });
                // Track InitiateCheckout on button click if Meta Pixel active
                if (typeof fbq === 'function') {
                    fbq('track', 'InitiateCheckout');
                }
            }
        });
    });

    // -------------------------------------------------------------
    // Form Validation & AJAX Submission
    // -------------------------------------------------------------
    if (orderForm) {
        orderForm.addEventListener('submit', function (e) {
            e.preventDefault();
            if (errorAlert) {
                errorAlert.style.display = 'none';
                errorAlert.textContent = '';
            }

            const name = document.getElementById('customer_name').value.trim();
            const phone = document.getElementById('phone').value.trim();
            const address = document.getElementById('address').value.trim();

            // Client-side validations
            if (!name) {
                showError('অনুগ্রহ করে আপনার পুরো নাম লিখুন।');
                document.getElementById('customer_name').focus();
                return;
            }

            // BD Phone regex (11 digits, starts with 01)
            const cleanPhone = phone.replace(/[^0-9]/g, '');
            const bdPhoneRegex = /^(?:88)?01[3-9]\d{8}$/;
            if (!bdPhoneRegex.test(cleanPhone)) {
                showError('অনুগ্রহ করে সঠিক ১১ ডিজিটের মোবাইল নাম্বার দিন (যেমন: 017xxxxxxxx বা 018xxxxxxxx)।');
                document.getElementById('phone').focus();
                return;
            }

            if (!address || address.length < 5) {
                showError('অনুগ্রহ করে আপনার বিস্তারিত ডেলিভারি ঠিকানা (জেলা, থানা, বাসা নং ইত্যাদি) লিখুন।');
                document.getElementById('address').focus();
                return;
            }

            // Button loading state
            submitBtn.disabled = true;
            submitBtn.classList.add('loading');

            // Trigger Meta Pixel InitiateCheckout
            if (typeof fbq === 'function') {
                fbq('track', 'InitiateCheckout', {
                    content_name: 'South Moon Lipoma Removal Spray',
                    currency: 'BDT',
                    value: currentPackagePrice + currentDeliveryPrice
                });
            }

            const formData = new FormData(orderForm);

            fetch('order.php', {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    // Redirect to thank-you page with order ID
                    window.location.href = data.redirect || 'thank-you.php?id=' + data.order_id;
                } else {
                    showError(data.message || 'অর্ডার সম্পন্ন করতে সমস্যা হয়েছে। অনুগ্রহ করে আবার চেষ্টা করুন।');
                    submitBtn.disabled = false;
                    submitBtn.classList.remove('loading');
                }
            })
            .catch(err => {
                console.error(err);
                showError('সার্ভারে যোগাযোগ করতে ব্যর্থ হয়েছে। ইন্টারনেট সংযোগ চেক করে আবার চেষ্টা করুন।');
                submitBtn.disabled = false;
                submitBtn.classList.remove('loading');
            });
        });
    }

    function showError(msg) {
        if (errorAlert) {
            errorAlert.textContent = msg;
            errorAlert.style.display = 'block';
            errorAlert.scrollIntoView({ behavior: 'smooth', block: 'center' });
        } else {
            alert(msg);
        }
    }

    // -------------------------------------------------------------
    // Review Image Lightbox
    // -------------------------------------------------------------
    const lightboxModal = document.getElementById('lightbox-modal');
    const lightboxImg = document.getElementById('lightbox-img');
    const lightboxClose = document.getElementById('lightbox-close');

    document.querySelectorAll('.review-zoom-btn, .review-img-wrap').forEach(el => {
        el.addEventListener('click', function () {
            const img = this.querySelector('img') || this;
            if (img && lightboxModal && lightboxImg) {
                lightboxImg.src = img.src;
                lightboxModal.classList.add('active');
            }
        });
    });

    if (lightboxClose && lightboxModal) {
        lightboxClose.addEventListener('click', function () {
            lightboxModal.classList.remove('active');
        });
        lightboxModal.addEventListener('click', function (e) {
            if (e.target === lightboxModal) {
                lightboxModal.classList.remove('active');
            }
        });
    }
});
