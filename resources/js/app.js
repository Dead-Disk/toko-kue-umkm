// Titik masuk seluruh JavaScript aplikasi.
import { initCashierPos } from './modules/cashier-pos';
import { initConfirmActions } from './modules/confirm-action';
import { initMaxOrderLimits } from './modules/max-order-limit';
import { initNavbarSearch } from './modules/navbar-search';
import { initPaymentDetails } from './modules/payment-details';
import { initProductImageCropper } from './modules/product-image-cropper';
import { initStaffProductStock } from './modules/staff-product-stock';
import { initToasts } from './modules/toast';

initCashierPos();
initConfirmActions();
initMaxOrderLimits();
initNavbarSearch();
initPaymentDetails();
initProductImageCropper();
initStaffProductStock();
initToasts();
