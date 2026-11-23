// Titik masuk seluruh JavaScript aplikasi.
import { initCashierPos } from './modules/cashier-pos';
import { initConfirmActions } from './modules/confirm-action';
import { initMaxOrderLimits } from './modules/max-order-limit';
import { initNavbarSearch } from './modules/navbar-search';
import { initPaymentDetails } from './modules/payment-details';
import { initProductImageCropper } from './modules/product-image-cropper';
import { initStaffProductStock } from './modules/staff-product-stock';
import { initToasts, showToast } from './modules/toast';

if (typeof window !== 'undefined') {
    window.showToast = showToast;
}

initCashierPos();
initConfirmActions();
initMaxOrderLimits();
initNavbarSearch();
initPaymentDetails();
initProductImageCropper();
initStaffProductStock();
initToasts();
