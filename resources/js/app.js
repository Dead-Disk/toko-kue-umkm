// Titik masuk seluruh JavaScript aplikasi.
import { initCashierPos } from './modules/cashier-pos';
import { initConfirmActions } from './modules/confirm-action';
import { initMaxOrderLimits } from './modules/max-order-limit';
import { initPaymentDetails } from './modules/payment-details';
import { initToasts } from './modules/toast';

initCashierPos();
initConfirmActions();
initMaxOrderLimits();
initPaymentDetails();
initToasts();
