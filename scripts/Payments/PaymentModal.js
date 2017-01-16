import React from 'react';
import Modal from 'react-modal';
import { CardForm } from 'react-payment';
import Dialog from 'material-ui/Dialog';
import TextField from 'material-ui/TextField';
import MuiThemeProvider from 'material-ui/styles/MuiThemeProvider';

export default class PaymentModal extends React.Component {

	constructor(props) {
		super(props);

		this.state = {
			modalIsOpen: false
		}
	}

	componentDidMount() {
		document.getElementById('pay-button').addEventListener( 'click', this.openModal.bind(this) );
	}

	openModal(e) {
		this.setState({ modalIsOpen: true });
		return false;
	}

	closeModal() {
		this.setState({ modalIsOpen: false });
	}

	onSubmit(card) {
		console.log(card);
	}

	render() {
		const styles = {
			content: {
				top: '30%',
				left: '40%',
				right: 'auto',
				bottom: 'auto',
				width: '280px',
				transform: 'translate(0%0%)'
			}
		};
		return(
			<div>

					<MuiThemeProvider>
						<Dialog
							title="Pay Now"
							modal={false}
							open={this.state.modalIsOpen}
							onRequestClose={this.closeModal}
						>
							<TextField
								hintText="Name on Card"
								floatingLabelText="Name on Card"
							/><br />
							<TextField
								hintText="Card Number"
								floatingLabelText="Card Number"
							/><br />
							<TextField
								hintText="Expiration Date"
								floatingLabelText="Expiration Date"
							/><br />
						</Dialog>
					</MuiThemeProvider>
			</div>
			);
	}
}
//4vP48mMJ
