// import React from 'react';
// import ReactDOM from 'react-dom';
// import PaymentModal from './Payments/PaymentModal.js';

// const el = document.getElementById( 'adbutler-uc--modal' );

// //ReactDOM.render( <PaymentModal/>, el );

window.wp.adbutler_cc = {};

window.wp.adbutler_cc.toPaymentPage = function(e) {
	document.getElementById('adbutler_cc--payment-form').submit();
}
