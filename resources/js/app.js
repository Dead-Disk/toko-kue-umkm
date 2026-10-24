// Titik masuk seluruh JavaScript aplikasi.
import { initConfirmActions } from './modules/confirm-action';
import { initMaxOrderLimits } from './modules/max-order-limit';
import { initPaymentDetails } from './modules/payment-details';
import { initToasts } from './modules/toast';

initConfirmActions();
initMaxOrderLimits();
initPaymentDetails();
initToasts();
